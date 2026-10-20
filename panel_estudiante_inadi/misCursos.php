
<?php
session_start();

/*
|--------------------------------------------------------------------------
| INADI - Portal del estudiante
| Archivo: misCursos.php
|--------------------------------------------------------------------------
| Muestra los cursos asociados al estudiante que inició sesión.
| Utiliza las tablas matricula y curso de la base de datos insoft.
|--------------------------------------------------------------------------
*/

// Evitar que un usuario sin sesión acceda a los cursos.
$nombreUsuario = trim($_SESSION['nombre_usuario'] ?? '');

if ($nombreUsuario === '') {
    header('Location: ../Inicio/inicioindex.php');
    exit;
}

// Nombre que se mostrará en la página.
$nombre = trim(
    ($_SESSION['nombre'] ?? '') . ' ' .
    ($_SESSION['apellido'] ?? '')
);

if ($nombre === '') {
    $nombre = $nombreUsuario;
}

// Inicializar variables.
$cursos = [];
$error = '';

$totalCursos = 0;
$totalActivos = 0;

// Conectar con la base de datos.
try {
    require_once __DIR__ . '/../database/consultas/conexion.php';

    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException(
            'No se pudo establecer la conexión con la base de datos.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Consultar los cursos del estudiante
    |--------------------------------------------------------------------------
    | La relación se realiza mediante:
    | matricula.id_curso = curso.id_curso
    |
    | El usuario se obtiene de la sesión, no de un formulario.
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            m.id_matricula,
            m.fecha AS fecha_matricula,
            m.estado AS estado_matricula,
            c.id_curso,
            c.nombre AS nombre_curso,
            c.modalidad,
            c.duracion,
            c.descripcion,
            c.costo,
            c.estado AS estado_curso,
            c.img
        FROM matricula AS m
        INNER JOIN curso AS c
            ON m.id_curso = c.id_curso
        WHERE m.nombre_usuario_estudiante = :usuario
        ORDER BY m.fecha DESC, c.nombre ASC
    ";

    $consulta = $pdo->prepare($sql);

    $consulta->execute([
        ':usuario' => $nombreUsuario
    ]);

    $cursos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Calcular cantidades para el resumen.
    $totalCursos = count($cursos);

    foreach ($cursos as $curso) {
        $estadoMatricula = mb_strtolower(
            trim($curso['estado_matricula'] ?? ''),
            'UTF-8'
        );

        if (in_array(
            $estadoMatricula,
            ['activa', 'activo', 'aprobada', 'aprobado', 'confirmada', 'confirmado'],
            true
        )) {
            $totalActivos++;
        }
    }

} catch (Throwable $e) {
    error_log('INADI misCursos.php: ' . $e->getMessage());

    $error = 'No fue posible cargar tus cursos en este momento.';
}

/*
|--------------------------------------------------------------------------
| Funciones de presentación
|--------------------------------------------------------------------------
*/

function escaparCurso($valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function claseEstadoCurso($estado): string
{
    $estado = mb_strtolower(trim((string) $estado), 'UTF-8');

    if (in_array(
        $estado,
        ['activa', 'activo', 'aprobada', 'aprobado', 'confirmada', 'confirmado'],
        true
    )) {
        return 'estado-activo';
    }

    if (in_array(
        $estado,
        ['en espera', 'pendiente', 'pendiente de pago'],
        true
    )) {
        return 'estado-pendiente';
    }

    if (in_array(
        $estado,
        ['finalizada', 'finalizado', 'completada', 'completado'],
        true
    )) {
        return 'estado-finalizado';
    }

    return 'estado-general';
}

function fechaCurso($fecha): string
{
    if (empty($fecha)) {
        return 'No disponible';
    }

    $fechaObjeto = DateTime::createFromFormat('!Y-m-d', $fecha);

    if (!$fechaObjeto || $fechaObjeto->format('Y-m-d') !== $fecha) {
        return 'No disponible';
    }

    return $fechaObjeto->format('d/m/Y');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#101521">

    <title>INADI | Mis cursos</title>

    <link
        rel="icon"
        href="../Elementos Gráficos/Logo Inadi sin Brillo.png"
    >

    <!-- Estilos generales del portal -->
    <link rel="stylesheet" href="CSS/stylesportal.css">

    <!-- Estilos específicos de Mis cursos -->
    <link rel="stylesheet" href="CSS/miscursos.css">

    <!-- Iconos -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
</head>

<body>

<div class="portal-shell">

    <!-- =========================================================
         MENÚ LATERAL
    ========================================================== -->

    <aside class="sidebar" id="sidebar">

        <div class="brand">

            <button
                class="icon-button sidebar-collapse"
                id="sidebarToggle"
                type="button"
                aria-label="Contraer menú"
            >
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <a
                class="brand-logo"
                href="portalEstudiante.php"
                aria-label="Inicio del portal"
            >
                <img
                    src="../Elementos Gráficos/Logo Inadi sin Brillo.png"
                    alt="Logo INADI"
                >
            </a>

            <div class="brand-name">INADI</div>

            <div class="brand-since">DESDE 1992</div>

            <div class="brand-address">
                Artigas 827 · Salto, Uruguay
            </div>

        </div>

        <nav class="side-nav" aria-label="Navegación principal">

            <a class="nav-link" href="portalEstudiante.php">
                <i class="fa-solid fa-house"></i>
                <span>Inicio del portal</span>
            </a>

            <a class="nav-link active" href="misCursos.php">
                <i class="fa-solid fa-book-open"></i>
                <span>Mis cursos</span>
            </a>

            <a class="nav-link" href="aulaVirtual.php">
                <i class="fa-solid fa-laptop"></i>
                <span>Aula virtual</span>
            </a>

            <a class="nav-link" href="calificaciones.php">
                <i class="fa-solid fa-chart-column"></i>
                <span>Calificaciones</span>
            </a>

            <a class="nav-link" href="calendario.php">
                <i class="fa-regular fa-calendar"></i>
                <span>Calendario</span>
            </a>

            <a class="nav-link" href="legajo.php">
                <i class="fa-regular fa-folder-open"></i>
                <span>Mi legajo</span>
            </a>

            <a class="nav-link" href="perfil.php">
                <i class="fa-regular fa-user"></i>
                <span>Mi perfil</span>
            </a>

            <a class="nav-link" href="misPagos.php">
                <i class="fa-solid fa-wallet"></i>
                <span>Mis pagos</span>
            </a>

        </nav>

        <a
            class="whatsapp-link"
            href="https://wa.me/59847331609"
            target="_blank"
            rel="noopener noreferrer"
        >
            <i class="fa-brands fa-whatsapp"></i>
            <span>¿Consultas? WhatsApp Salto</span>
        </a>

        <div class="sidebar-footer">
            INADI · Formación para el futuro
        </div>

    </aside>

    <!-- =========================================================
         CONTENIDO PRINCIPAL
    ========================================================== -->

    <main class="main-area">

        <header class="topbar">

            <div class="news-strip">
                <span class="news-label">
                    <i class="fa-solid fa-circle"></i>
                    CAMPUS DIGITAL
                </span>

                <div class="news-track">
                    <span>
                        <i class="fa-solid fa-star"></i>
                        Consultá tus cursos y matrículas
                    </span>
                </div>
            </div>

            <div class="top-actions">

                <a class="top-portal" href="portalEstudiante.php">
                    <i class="fa-solid fa-house"></i>
                    Volver al portal
                </a>

                <div class="user-menu">

                    <div class="user-avatar">
                        <?= escaparCurso(
                            mb_strtoupper(
                                mb_substr($nombre, 0, 1, 'UTF-8'),
                                'UTF-8'
                            )
                        ) ?>
                    </div>

                    <div class="user-data">
                        <strong><?= escaparCurso($nombre) ?></strong>
                        <small>
                            <i class="fa-solid fa-circle"></i>
                            Portal del estudiante
                        </small>
                    </div>

                </div>

            </div>

        </header>

        <section class="miscursos-content">

            <!-- Encabezado -->

            <div class="miscursos-hero">

                <div>
                    <span class="eyebrow">TU FORMACIÓN</span>

                    <h1>Mis cursos</h1>

                    <p>
                        Consultá los cursos asociados a tus matrículas,
                        sus modalidades, duración y estado.
                    </p>
                </div>

                <div class="miscursos-hero-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

            </div>

            <!-- Resumen -->

            <section class="miscursos-resumen">

                <article class="resumen-card">
                    <div class="resumen-icon azul">
                        <i class="fa-solid fa-book-open"></i>
                    </div>

                    <div>
                        <span>Cursos registrados</span>
                        <strong><?= $totalCursos ?></strong>
                    </div>
                </article>

                <article class="resumen-card">
                    <div class="resumen-icon verde">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div>
                        <span>Matrículas activas</span>
                        <strong><?= $totalActivos ?></strong>
                    </div>
                </article>

            </section>

            <!-- Listado de cursos -->

            <section class="miscursos-listado">

                <div class="miscursos-heading">
                    <div>
                        <span class="eyebrow">MI HISTORIAL ACADÉMICO</span>
                        <h2>Cursos y matrículas</h2>
                    </div>

                    <a
                        class="text-link"
                        href="../Cursos/cursosindex.php"
                    >
                        Explorar cursos
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <?php if ($error !== ''): ?>

                    <div class="miscursos-mensaje error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <?= escaparCurso($error) ?>
                    </div>

                <?php elseif (empty($cursos)): ?>

                    <div class="miscursos-vacio">

                        <div class="vacio-icon">
                            <i class="fa-solid fa-book-open"></i>
                        </div>

                        <h3>Todavía no tenés cursos registrados</h3>

                        <p>
                            Cuando exista una matrícula asociada a tu
                            usuario, el curso aparecerá en esta sección.
                        </p>

                        <a
                            class="button button-blue"
                            href="../Cursos/cursosindex.php"
                        >
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Explorar cursos
                        </a>

                    </div>

                <?php else: ?>

                    <div class="miscursos-grid">

                        <?php foreach ($cursos as $curso): ?>

                            <?php
                            $estadoMatricula = trim(
                                $curso['estado_matricula'] ?? ''
                            );

                            $estadoCurso = trim(
                                $curso['estado_curso'] ?? ''
                            );

                            $estadoVisual = $estadoMatricula !== ''
                                ? $estadoMatricula
                                : 'Sin estado';

                            $esActivo = in_array(
                                mb_strtolower($estadoMatricula, 'UTF-8'),
                                [
                                    'activa',
                                    'activo',
                                    'aprobada',
                                    'aprobado',
                                    'confirmada',
                                    'confirmado'
                                ],
                                true
                            );
                            ?>

                            <article class="miscursos-card">

                                <div class="miscursos-card-top">

                                    <div class="miscursos-card-icon">
                                        <i class="fa-solid fa-laptop-code"></i>
                                    </div>

                                    <span class="estado-etiqueta <?= escaparCurso(
                                        claseEstadoCurso($estadoMatricula)
                                    ) ?>">
                                        <?= escaparCurso($estadoVisual) ?>
                                    </span>

                                </div>

                                <div class="miscursos-card-body">

                                    <span class="curso-categoria">
                                        <?= escaparCurso(
                                            $curso['modalidad'] ?: 'Modalidad no especificada'
                                        ) ?>
                                    </span>

                                    <h3>
                                        <?= escaparCurso($curso['nombre_curso']) ?>
                                    </h3>

                                    <p class="curso-descripcion">
                                        <?= escaparCurso(
                                            $curso['descripcion'] ?: 'No hay descripción disponible.'
                                        ) ?>
                                    </p>

                                    <div class="curso-datos">

                                        <div>
                                            <i class="fa-regular fa-clock"></i>

                                            <span>
                                                <?= escaparCurso(
                                                    $curso['duracion'] ?: 'Duración no especificada'
                                                ) ?>
                                            </span>
                                        </div>

                                        <div>
                                            <i class="fa-regular fa-calendar"></i>

                                            <span>
                                                Matrícula:
                                                <?= escaparCurso(
                                                    fechaCurso($curso['fecha_matricula'])
                                                ) ?>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="curso-precio">
                                        <span>Costo registrado del curso</span>

                                        <strong>
                                            $ <?= number_format(
                                                (float) $curso['costo'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>
                                        </strong>
                                    </div>

                                    <div class="curso-estado">
                                        <span>Estado del curso</span>

                                        <strong class="<?= escaparCurso(
                                            claseEstadoCurso($estadoCurso)
                                        ) ?>">
                                            <?= escaparCurso(
                                                $estadoCurso ?: 'No especificado'
                                            ) ?>
                                        </strong>
                                    </div>

                                    <?php if ($esActivo): ?>

                                        <a
                                            class="curso-boton"
                                            href="aulaVirtual.php?id_curso=<?= (int) $curso['id_curso'] ?>"
                                        >
                                            Ingresar al aula
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>

                                    <?php else: ?>

                                        <div class="curso-no-disponible">
                                            <i class="fa-solid fa-circle-info"></i>
                                            Consultá el estado de tu matrícula
                                            con la administración.
                                        </div>

                                    <?php endif; ?>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>

            <footer class="page-footer">
                © <?= date('Y') ?> INADI · Instituto Nacional de Informática · Salto, Uruguay
            </footer>

        </section>

    </main>

</div>

<!-- JavaScript compartido del portal -->
<script src="JS/scriptportal.js"></script>

</body>
</html>
