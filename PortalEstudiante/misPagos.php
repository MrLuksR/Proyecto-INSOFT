<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIALIZACIÓN DE VARIABLES */

$pagos = [];
$fallo = false;


/* 3. CONSULTA DE PAGOS DEL ESTUDIANTE */

if (!$errorConexion && $haySesionEstudiante) {

    try {

        $pagos = portalObtenerPagos(
            $pdo,
            $nombreUsuario
        );

    } catch (Throwable $e) {

        // REGISTRAR EL ERROR EN EL SERVIDOR
        error_log(
            '[INSOFT] Error al consultar los pagos: '
            . $e->getMessage()
        );

        $fallo = true;

    }

}


/* 4. INICIO DEL DISEÑO DE LA PÁGINA */

portalLayoutInicio(
    'Mis pagos',
    'pagos'
);

?>


<!-- 5. ENCABEZADO PRINCIPAL -->

<section class="page-heading">

    <div>

        <p class="eyebrow">
            INFORMACIÓN FINANCIERA
        </p>

        <h1>
            Mis pagos
        </h1>

        <p>
            Consultá el historial de pagos asociados a tus matrículas.
        </p>

    </div>

</section>


<!-- 6. AVISO SOBRE LOS PAGOS -->

<div class="alert alert-info">

    <i class="fa-solid fa-circle-info"></i>

    <span>
        En esta sección podés consultar tus operaciones de pago.
        Los pagos confirmados corresponden a operaciones aprobadas
        y verificadas por el sistema.
        Las operaciones pendientes todavía no se consideran abonadas.
    </span>

</div>


<!-- 7. CONTROL DE ESTADOS DE LA CONSULTA -->

<?php if ($fallo || $errorConexion): ?>

    <!-- ERROR AL CONSULTAR LOS PAGOS -->

    <div class="alert alert-warning">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <span>
            No se pudieron consultar los pagos registrados.
            Intentá nuevamente más tarde.
        </span>

    </div>


<?php elseif (!$pagos): ?>

    <!-- NO HAY PAGOS REGISTRADOS -->

    <?php

    portalMostrarVacio(
        'Sin pagos registrados',

        'Todavía no existen operaciones de pago asociadas a tus matrículas.',

        'fa-credit-card'
    );

    ?>


<?php else: ?>

    <!-- 8. TABLA DE HISTORIAL DE PAGOS -->

    <div class="table-card">

        <div class="table-responsive">

            <table>

                <!-- ENCABEZADOS -->

                <thead>

                    <tr>

                        <th>
                            Pago
                        </th>

                        <th>
                            Curso
                        </th>

                        <th>
                            Fecha
                        </th>

                        <th>
                            Importe
                        </th>

                        <th>
                            Método
                        </th>

                        <th>
                            Referencia
                        </th>

                        <th>
                            Estado
                        </th>

                    </tr>

                </thead>


                <!-- DATOS DE LOS PAGOS -->

                <tbody>

                    <?php foreach ($pagos as $p): ?>

                        <tr>

                            <!-- IDENTIFICADOR DEL PAGO -->

                            <td>

                                #<?= e(
                                    $p['id_pago']
                                ) ?>

                            </td>


                            <!-- NOMBRE DEL CURSO -->

                            <td>

                                <?= e(
                                    $p['nombre_curso']
                                    ?: 'Curso no disponible'
                                ) ?>

                                <br>

                                <small>
                                    Matrícula #<?= e(
                                        $p['id_matricula']
                                    ) ?>
                                </small>

                            </td>


                            <!-- FECHA DEL PAGO -->

                            <td>

                                <?php

                                $fecha = $p['fecha'] ?? null;

                                if (!empty($fecha)) {

                                    $timestamp = strtotime($fecha);

                                    echo $timestamp !== false
                                        ? e(date('d/m/Y', $timestamp))
                                        : 'Sin fecha';

                                } else {

                                    echo 'Sin fecha';

                                }

                                ?>

                            </td>


                            <!-- IMPORTE -->

                            <td>

                                <strong>

                                    $<?= e(
                                        number_format(
                                            (float) ($p['monto'] ?? 0),
                                            2,
                                            ',',
                                            '.'
                                        )
                                    ) ?>

                                </strong>

                                <small>
                                    <?= e(
                                        $p['moneda'] ?? 'UYU'
                                    ) ?>
                                </small>

                            </td>


                            <!-- MÉTODO DE PAGO -->

                            <td>

                                <?= e(
                                    $p['metodo_pago']
                                    ?: 'No especificado'
                                ) ?>

                            </td>


                            <!-- REFERENCIA DE MERCADO PAGO -->

                            <td>

                                <?php

                                $referenciaMP = trim(
                                    (string) ($p['ref_pasarela'] ?? '')
                                );

                                if ($referenciaMP !== '') {

                                    echo e($referenciaMP);

                                } else {

                                    echo '<span class="text-muted">
                                            Sin referencia
                                          </span>';

                                }

                                ?>

                            </td>


                            <!-- ESTADO DEL PAGO -->

                            <td>

                                <?= portalEtiquetaEstadoPago(
                                    $p['estado'] ?? null
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

<?php endif; ?>


<!-- 9. CIERRE DEL DISEÑO DE LA PÁGINA -->

<?php

portalLayoutFin();

?>