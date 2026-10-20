
<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIALIZACIÓN DE VARIABLES */

$matriculas = [];
$fallo = false;


/* 3. CONSULTA DEL HISTORIAL ACADÉMICO */

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
    'Mi legajo',
    'legajo'
);

?>


<!-- 5. ENCABEZADO PRINCIPAL -->

<section class="page-heading">

    <div>

        <p class="eyebrow">
            REGISTRO ACADÉMICO
        </p>

        <h1>
            Mi legajo
        </h1>

        <p>
            Historial de matrículas registradas para tu usuario.
        </p>

    </div>

</section>


<!-- 6. ESTADOS DE LA CONSULTA -->

<?php if ($fallo): ?>

    <!-- ERROR AL CONSULTAR EL HISTORIAL -->

    <div class="alert alert-warning">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <span>
            No se pudo consultar el legajo.
            Revisá el esquema de la base de datos.
        </span>

    </div>


<?php elseif (!$matriculas): ?>

    <!-- HISTORIAL SIN MATRÍCULAS -->

    <?php

    portalMostrarVacio(
        'Tu legajo está vacío',
        'No hay matrículas registradas para mostrar.',
        'fa-folder-open'
    );

    ?>


<?php else: ?>

    <!-- 7. TABLA DEL HISTORIAL ACADÉMICO -->

    <div class="table-card">

        <div class="table-responsive">

            <table>

                <!-- ENCABEZADOS DE LA TABLA -->

                <thead>

                    <tr>

                        <th>
                            N.º matrícula
                        </th>

                        <th>
                            Curso
                        </th>

                        <th>
                            Fecha
                        </th>

                        <th>
                            Estado
                        </th>

                    </tr>

                </thead>


                <!-- DATOS DE LAS MATRÍCULAS -->

                <tbody>

                    <?php foreach ($matriculas as $m): ?>

                        <tr>

                            <!-- IDENTIFICADOR -->

                            <td>
                                #<?= e($m['id_matricula']) ?>
                            </td>


                            <!-- NOMBRE DEL CURSO -->

                            <td>

                                <?= e(
                                    $m['nombre_curso']
                                    ?: 'Curso no disponible'
                                ) ?>

                            </td>


                            <!-- FECHA DE MATRÍCULA -->

                            <td>

                                <?= e(
                                    $m['fecha']
                                    ?: '—'
                                ) ?>

                            </td>


                            <!-- ESTADO DE LA MATRÍCULA -->

                            <td>

                                <span class="status-pill">

                                    <?= e(
                                        $m['estado']
                                        ?: 'Sin estado'
                                    ) ?>

                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

<?php endif; ?>


<!-- 8. CIERRE DEL DISEÑO DE LA PÁGINA -->

<?php

portalLayoutFin();

?>
