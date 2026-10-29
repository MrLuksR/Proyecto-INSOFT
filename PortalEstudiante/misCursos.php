
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

        // GUARDAR EL ERROR EN EL SERVIDOR
        error_log(
            "[INADI] Error al consultar los cursos: "
            . $e->getMessage()
        );

        // MOSTRAR MENSAJE GENÉRICO
        $fallo = true;

    }

}

/* MATRÍCULAS PENDIENTES DE PAGO */

$matriculasPendientes = array_filter(
    $matriculas,
    function ($matricula) {

        $estado = mb_strtolower(
            trim((string) ($matricula['estado'] ?? '')),
            'UTF-8'
        );

        return $estado === 'pendiente de pago';
    }
);

$cantidadPendientes = count($matriculasPendientes);


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


    <!-- ACCIONES DE MATRÍCULAS -->

    <div class="acciones-matriculas">

        <?php if ($cantidadPendientes > 1): ?>

            <!-- PAGAR TODAS LAS MATRÍCULAS PENDIENTES -->

            <form action="irAPago.php" method="POST">

                <input
                    type="hidden"
                    name="tipo_pago"
                    value="multiple"
                >

                <button
                    type="submit"
                    class="btn btn-primary btn-pagar-todo"
                >
                    <i class="fa-solid fa-credit-card"></i>
                    Pagar todo junto

                    <span class="cantidad-pagos">
                        <?= $cantidadPendientes ?>
                    </span>
                </button>

            </form>

        <?php endif; ?>


        <!-- EXPLORAR CURSOS -->

        <a
            class="btn btn-primary"
            href="../Cursos/cursosindex.php"
        >
            <i class="fa-solid fa-magnifying-glass"></i>

            Ver cursos
        </a>

    </div>

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
                            Progreso
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


                            
                            <!-- PROGRESO DEL CURSO -->

                            <td>

                                <?php

                                $totalTareas = (int) $m['total_tareas'];

                                $tareasCompletadas = (int) $m['tareas_completadas'];

                                $porcentaje = $totalTareas > 0
                                    ? round(($tareasCompletadas / $totalTareas) * 100)
                                    : 0;

                                ?>

                                <div class="progreso-curso">

                                    <div class="progreso-info">

                                        <strong>
                                            <?= $porcentaje ?>%
                                        </strong>

                                        <small>
                                            <?= $tareasCompletadas ?>
                                            de
                                            <?= $totalTareas ?>
                                            tareas
                                        </small>

                                    </div>

                                    <div
                                        class="progreso-barra"
                                        role="progressbar"
                                        aria-valuenow="<?= $porcentaje ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-label="Progreso del curso"
                                    >

                                        <div
                                            class="progreso-relleno"
                                            style="width: <?= $porcentaje ?>%;"
                                        ></div>

                                    </div>

                                </div>

                            </td>



                            <!-- ACCESO AL AULA VIRTUAL O PAGO -->

                            <td>

                                <?php

                                // OBTENER ESTADO DE LA MATRÍCULA
                                $estadoMatricula = mb_strtolower(
                                    trim((string) ($m['estado'] ?? '')),
                                    'UTF-8'
                                );

                                ?>

                                <?php if ($estadoMatricula === 'pendiente de pago'): ?>

                                    <!-- BOTÓN PARA PAGAR MATRÍCULA -->

                                    <?php portalBotonPago((int) $m['id_matricula']); ?>

                                <?php else: ?>

                                    <!-- BOTÓN PARA ACCEDER DIRECTAMENTE AL CURSO -->

                                    <a
                                        class="btn btn-small btn-outline"
                                        href="cursoVirtual.php?id=<?= (int) $m['id_curso'] ?>"
                                    >
                                        <i class="fa-solid fa-chalkboard-user"></i>

                                        Ver aula
                                    </a>

                                <?php endif; ?>

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
