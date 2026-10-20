
<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIALIZACIÓN DE VARIABLES */

$matriculas = [];
$fallo = false;


/* 3. CONSULTA DE MATRÍCULAS */

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
    'Mis cursos',
    'cursos'
);

?>


<!-- 5. ENCABEZADO Y BOTÓN PARA EXPLORAR CURSOS -->

<section class="page-heading">

    <div>

        <p class="eyebrow">
            FORMACIÓN
        </p>

        <h1>
            Mis cursos
        </h1>

        <p>
            Listado de matrículas vinculadas a tu cuenta.
        </p>

    </div>


    <!-- ACCESO AL CATÁLOGO DE CURSOS -->

    <a
        class="btn btn-primary"
        href="../Cursos/cursosindex.php"
    >

        <i class="fa-solid fa-magnifying-glass"></i>

        Explorar cursos

    </a>

</section>


<!-- 6. CONTROL DE ESTADOS DE LA CONSULTA -->

<?php if ($fallo): ?>

    <!-- ERROR DE CONSULTA -->

    <div class="alert alert-warning">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <span>
            No se pudo consultar el listado.
            Verificá la estructura de las tablas de matrículas y cursos.
        </span>

    </div>


<?php elseif (!$matriculas): ?>

    <!-- NO HAY CURSOS MATRICULADOS -->

    <?php

    portalMostrarVacio(
        'Todavía no hay cursos para mostrar',

        'Cuando tengas matrículas asociadas a tu usuario, aparecerán en esta sección.',

        'fa-book-open'
    );

    ?>


<?php else: ?>

    <!-- 7. TABLA DE CURSOS MATRICULADOS -->

    <div class="table-card">

        <div class="table-responsive">

            <table>

                <!-- ENCABEZADOS -->

                <thead>

                    <tr>

                        <th>
                            Curso
                        </th>

                        <th>
                            Fecha de matrícula
                        </th>

                        <th>
                            Modalidad
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Acceso
                        </th>

                    </tr>

                </thead>


                <!-- CONTENIDO DE LA TABLA -->

                <tbody>

                    <?php foreach ($matriculas as $m): ?>

                        <tr>

                            <!-- NOMBRE E IDENTIFICADOR DEL CURSO -->

                            <td>

                                <strong>

                                    <?= e(
                                        $m['nombre_curso']
                                        ?: 'Curso no disponible'
                                    ) ?>

                                </strong>

                                <small class="table-subtext">

                                    Matrícula #

                                    <?= e(
                                        $m['id_matricula']
                                    ) ?>

                                </small>

                            </td>


                            <!-- FECHA DE MATRÍCULA -->

                            <td>

                                <?= e(
                                    $m['fecha']
                                    ?: '—'
                                ) ?>

                            </td>


                            <!-- MODALIDAD -->

                            <td>

                                <?= e(
                                    $m['modalidad']
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


                            <!-- ACCESO AL AULA VIRTUAL -->

                            <td>

                                <a
                                    class="btn btn-small btn-outline"
                                    href="aulaVirtual.php"
                                >

                                    <i class="fa-solid fa-chalkboard-user"></i>

                                    Ver aula

                                </a>

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
