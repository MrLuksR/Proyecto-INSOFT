<?php
require_once __DIR__ . '/portal_common.php';

/* Datos del portal estudiantil */
$matriculas = [];
$errorDatos = false;

/* Obtener el nombre sin asumir que el usuario es un objeto */
$nombreEstudiante = portalNombreActual();

/* Cargar matrículas si la conexión y la sesión son válidas */
if (
    isset($pdo)
    && $pdo instanceof PDO
    && !($errorConexion ?? true)
    && ($haySesionEstudiante ?? false)
) {
    try {
        $matriculas = portalObtenerMatriculas($pdo, $nombreUsuario);
    } catch (Throwable $e) {
        $matriculas = [];
        $errorDatos = true;
    }
}

/* Contar matrículas activas */
$activas = array_filter($matriculas, static function ($m) {
    return isset($m['estado'])
        && mb_strtolower(trim((string) $m['estado']), 'UTF-8') === 'activa';
});

/* ==========================================
   PRÓXIMA TAREA PENDIENTE DE ENTREGA
========================================== */

$proximaTarea = null;
$errorTareas = false;

if (
    $pdo instanceof PDO &&
    !$errorConexion &&
    $haySesionEstudiante
) {

    try {

        $sql = "
            SELECT
                t.id_tarea,
                t.titulo,
                t.fecha_limite,
                c.nombre AS nombre_curso,
                c.id_curso

            FROM tareas t

            INNER JOIN clase cl
                ON cl.id_clase = t.id_clase

            INNER JOIN curso c
                ON c.id_curso = cl.id_curso

            INNER JOIN matricula m
                ON m.id_curso = c.id_curso

            WHERE m.nombre_usuario_estudiante = :usuario

              AND LOWER(TRIM(m.estado)) = 'activa'

              AND t.fecha_limite IS NOT NULL

              AND t.fecha_limite >= NOW()

              AND NOT EXISTS (
                  SELECT 1
                  FROM tareas_estudiantes te
                  WHERE te.id_tarea = t.id_tarea
                    AND te.nombre_usuario_estudiante =
                        m.nombre_usuario_estudiante
              )

            ORDER BY t.fecha_limite ASC

            LIMIT 1
        ";

        $consulta = $pdo->prepare($sql);

        $consulta->execute([
            ':usuario' => $nombreUsuario
        ]);

        $proximaTarea = $consulta->fetch(PDO::FETCH_ASSOC);

    } catch (Throwable $e) {

        error_log(
            "[INADI] Error al consultar próxima tarea: "
            . $e->getMessage()
        );

        $errorTareas = true;
    }
}

portalLayoutInicio('Inicio', 'portal');
?>

<section class="welcome-panel">
    <div>
        <p class="eyebrow">TU ESPACIO ACADÉMICO</p>

        <h1>¡Hola, <?= e($nombreEstudiante) ?>!</h1>

        <p>
            Desde aquí podés consultar tus cursos, información académica
            y servicios estudiantiles.
        </p>

        <a class="btn btn-light" href="misCursos.php">
            Ver mis cursos
        </a>
    </div>

    <div class="welcome-art" aria-hidden="true">
        <i class="fa-solid fa-shield-halved"></i>
    </div>
</section>

<?php if ($errorDatos): ?>
    <div class="alert alert-warning">
        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
        <span>No se pudieron cargar las matrículas en este momento.</span>
    </div>
<?php endif; ?>

<section class="stats-grid">
    <article class="stat-card">
        <span class="stat-icon purple">
            <i class="fa-solid fa-book-open"></i>
        </span>
        <div>
            <p>Mis matrículas</p>
            <strong><?= count($matriculas) ?></strong>
        </div>
    </article>

    <article class="stat-card">
        <span class="stat-icon orange">
            <i class="fa-solid fa-circle-check"></i>
        </span>
        <div>
            <p>Matrículas activas</p>
            <strong><?= count($activas) ?></strong>
        </div>
    </article>

    <!-- PRÓXIMA ENTREGA ACADÉMICA -->
    <article class="stat-card">

        <span class="stat-icon blue">
            <i class="fa-solid fa-calendar-days"></i>
        </span>

        <div>

            <p>Próxima entrega</p>

            <?php if ($errorTareas): ?>

                <strong>—</strong>

                <small>
                    No se pudieron consultar las tareas.
                </small>

            <?php elseif ($proximaTarea): ?>

                <!-- NOMBRE DE LA TAREA -->

                <strong class="proxima-tarea-titulo">
                    <?= e($proximaTarea['titulo']) ?>
                </strong>

                <!-- CURSO -->

                <small>
                    <i class="fa-solid fa-book-open"></i>

                    <?= e($proximaTarea['nombre_curso']) ?>
                </small>

                <!-- FECHA LÍMITE -->

                <small class="proxima-tarea-fecha">

                    <i class="fa-regular fa-clock"></i>

                    Vence:

                    <?= e(
                        date(
                            'd/m/Y H:i',
                            strtotime($proximaTarea['fecha_limite'])
                        )
                    ) ?>

                </small>

                <!-- ACCESO AL CURSO -->

                <a
                    href="cursoVirtual.php?id=<?= (int) $proximaTarea['id_curso'] ?>"
                    class="proxima-tarea-enlace"
                >
                    Ver tarea

                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            <?php else: ?>

                <strong>
                    <i class="fa-solid fa-circle-check"></i>
                </strong>

                <small>
                    No tenés entregas próximas pendientes.
                </small>

                <a
                    href="calendario.php"
                    class="proxima-tarea-enlace"
                >
                    Ver calendario

                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            <?php endif; ?>

        </div>

    </article>
</section>

<section class="section-block">
    <div class="section-heading">
        <div>
            <p class="eyebrow">ACCESOS RÁPIDOS</p>
            <h2>¿Qué necesitás consultar?</h2>
        </div>
    </div>

    <div class="module-grid">
        <a class="module-card" href="misCursos.php">
            <span class="module-icon">
                <i class="fa-solid fa-book-open"></i>
            </span>
            <h3>Mis cursos</h3>
            <p>Consultá las matrículas asociadas a tu usuario.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>

        <a class="module-card" href="aulaVirtual.php">
            <span class="module-icon">
                <i class="fa-solid fa-chalkboard-user"></i>
            </span>
            <h3>Aula virtual</h3>
            <p>Acceso a aulas y recursos disponibles.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>

        <a class="module-card" href="legajo.php">
            <span class="module-icon">
                <i class="fa-solid fa-folder-open"></i>
            </span>
            <h3>Mi legajo</h3>
            <p>Historial de matrículas registradas.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>

        <a class="module-card" href="certificaciones.php">
            <span class="module-icon">
                <i class="fa-solid fa-certificate"></i>
            </span>
            <h3>Certificaciones</h3>
            <p>Consultá tus certificaciones disponibles.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>

        <a class="module-card" href="perfil.php">
            <span class="module-icon">
                <i class="fa-solid fa-user"></i>
            </span>
            <h3>Mi perfil</h3>
            <p>Datos básicos vinculados a tu sesión.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>

        <a class="module-card" href="calendario.php">
            <span class="module-icon">
                <i class="fa-solid fa-calendar-days"></i>
            </span>
            <h3>Calendario</h3>
            <p>Espacio para eventos académicos.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>

        <a class="module-card" href="credencial.php">
            <span class="module-icon">
                <i class="fa-solid fa-id-card"></i>
            </span>
            <h3>Credencial</h3>
            <p>Vista imprimible con los datos disponibles.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>

        <a class="module-card" href="misPagos.php">
            <span class="module-icon">
                <i class="fa-solid fa-credit-card"></i>
            </span>
            <h3>Mis pagos</h3>
            <p>Consultá referencias asociadas a tus matrículas.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>

        <a class="module-card" href="notificaciones.php">
            <span class="module-icon">
                <i class="fa-solid fa-bell"></i>
            </span>
            <h3>Notificaciones</h3>
            <p>Consultá los avisos disponibles en el sistema.</p>
            <span class="module-link">
                Ingresar <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>
    </div>
</section>

<?php portalLayoutFin(); ?>
