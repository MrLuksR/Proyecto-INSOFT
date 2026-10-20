
<?php

require_once __DIR__ . '/portal_common.php';

/* 1. INICIALIZACIÓN DE VARIABLES */

$matriculas = [];
$fallo = false;


/* 2. CONSULTA DE MATRÍCULAS DEL USUARIO */

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


/* 3. INICIO DE LA ESTRUCTURA VISUAL */

portalLayoutInicio(
    'Aula virtual',
    'aula'
);

?>

<!-- 4. ENCABEZADO DE LA PÁGINA -->

<section class="page-heading">

    <div>

        <p class="eyebrow">
            APRENDIZAJE
        </p>

        <h1>
            Aula virtual
        </h1>

        <p>
            Acceso a los cursos en los que figurás matriculado.
        </p>

    </div>

</section>


<!-- 5. AVISO INFORMATIVO -->

<div class="alert alert-info">

    <i class="fa-solid fa-circle-info"></i>

    <span>
        Los materiales y las clases se mostrarán cuando
        se confirme la estructura de sus tablas y sus
        relaciones con los cursos.
    </span>

</div>


<!-- 6. ESTADOS DE LA CONSULTA -->

<?php if ($fallo): ?>

    <!-- ERROR AL CONSULTAR LAS MATRÍCULAS -->

    <div class="alert alert-warning">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <span>
            No se pudo consultar tus cursos en este momento.
        </span>

    </div>


<?php elseif (!$matriculas): ?>

    <!-- NO HAY MATRÍCULAS DISPONIBLES -->

    <?php

    portalMostrarVacio(
        'No hay aulas disponibles',
        'No encontramos matrículas asociadas a tu usuario.',
        'fa-chalkboard-user'
    );

    ?>


<?php else: ?>

    <!-- 7. LISTADO DE CURSOS MATRICULADOS -->

    <div class="module-grid">

        <?php foreach ($matriculas as $m): ?>

            <article class="module-card module-card-static">

                <!-- ICONO DEL CURSO -->

                <span class="module-icon">

                    <i class="fa-solid fa-chalkboard-user"></i>

                </span>


                <!-- NOMBRE DEL CURSO -->

                <h3>

                    <?= e(
                        $m['nombre_curso'] ?: 'Curso no disponible'
                    ) ?>

                </h3>


                <!-- ESTADO DE LA MATRÍCULA -->

                <p>

                    Estado de matrícula:

                    <?= e(
                        $m['estado'] ?: 'Sin estado'
                    ) ?>

                </p>


                <!-- ESPACIO PARA FUTUROS MATERIALES -->

                <div class="resource-placeholder">

                    <i class="fa-solid fa-file-circle-question"></i>

                    <span>
                        Materiales y clases pendientes de vincular
                    </span>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

<?php endif; ?>


<!-- 8. CIERRE DE LA ESTRUCTURA VISUAL -->

<?php

portalLayoutFin();

?>
