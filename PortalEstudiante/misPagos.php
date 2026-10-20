
<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIALIZACIÓN DE VARIABLES */

$matriculas = [];
$fallo = false;


/* 3. CONSULTA DE MATRÍCULAS Y REFERENCIAS */

if (!$errorConexion) {

    try {

        $matriculas = portalObtenerMatriculas(
            $pdo,
            $nombreUsuario
        );

    } catch (Throwable $e) {

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
            Referencias vinculadas a tus matrículas.
        </p>

    </div>

</section>


<!-- 6. AVISO SOBRE LA VERIFICACIÓN DE PAGOS -->

<div class="alert alert-info">

    <i class="fa-solid fa-circle-info"></i>

    <span>
        Tener una matrícula o una referencia asociada
        no significa que un pago esté confirmado.
        Por eso no se presenta ningún pago como abonado
        sin un estado verificable.
    </span>

</div>


<!-- 7. CONTROL DE ESTADOS DE LA CONSULTA -->

<?php if ($fallo): ?>

    <!-- ERROR AL CONSULTAR LAS REFERENCIAS -->

    <div class="alert alert-warning">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <span>
            No se pudieron consultar las referencias de matrícula.
        </span>

    </div>


<?php elseif (!$matriculas): ?>

    <!-- NO HAY MATRÍCULAS ASOCIADAS -->

    <?php

    portalMostrarVacio(
        'Sin referencias para mostrar',

        'Cuando existan matrículas asociadas a tu usuario, se podrán listar aquí.',

        'fa-credit-card'
    );

    ?>


<?php else: ?>

    <!-- 8. TABLA DE REFERENCIAS DE PAGO -->

    <div class="table-card">

        <div class="table-responsive">

            <table>

                <!-- ENCABEZADOS -->

                <thead>

                    <tr>

                        <th>
                            Matrícula
                        </th>

                        <th>
                            Curso
                        </th>

                        <th>
                            Referencia de pago
                        </th>

                        <th>
                            Estado del pago
                        </th>

                    </tr>

                </thead>


                <!-- DATOS DE LAS MATRÍCULAS -->

                <tbody>

                    <?php foreach ($matriculas as $m): ?>

                        <tr>

                            <!-- IDENTIFICADOR DE MATRÍCULA -->

                            <td>

                                #<?= e(
                                    $m['id_matricula']
                                ) ?>

                            </td>


                            <!-- NOMBRE DEL CURSO -->

                            <td>

                                <?= e(
                                    $m['nombre_curso']
                                    ?: 'Curso no disponible'
                                ) ?>

                            </td>


                            <!-- REFERENCIA DE PAGO -->

                            <td>

                                <?php

                                if (
                                    isset($m['id_pago'])
                                    && $m['id_pago'] !== ''
                                ) {

                                    echo e($m['id_pago']);

                                } else {

                                    echo 'Sin referencia';

                                }

                                ?>

                            </td>


                            <!-- ESTADO DE VERIFICACIÓN -->

                            <td>

                                <span class="status-pill status-neutral">

                                    No verificado

                                </span>

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
