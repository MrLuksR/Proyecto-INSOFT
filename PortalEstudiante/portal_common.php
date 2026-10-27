<?php
/**
 * INADI - Funciones compartidas del Portal del Estudiante.
 * Modo de desarrollo: permite visualizar el portal sin iniciar sesión.
 * Antes de publicar, activar la autenticación obligatoria en cada módulo.
 */
include("../database/Clases/Estudiante.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario'])){
    $haySesionEstudiante = (
        isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) &&
        $_SESSION['tipo_usuario'] === 'estudiante'
    );

    $usuario = $_SESSION['usuario'];

    $nombreUsuario = $haySesionEstudiante
        ? $_SESSION['usuario']->getNombreUsuario()
        : 'Estudiante de prueba';
    
}else{
    header('Location: ../Bienvenido/bienvenidoindex.php');
}

/* Conexión a la base de datos */
$pdo = $pdo ?? null;
$errorConexion = false;
$rutaConexion = __DIR__ . '/../database/consultas/conexion.php';

if (!($pdo instanceof PDO) && is_file($rutaConexion)) {
    try {
        require_once $rutaConexion;
    } catch (Throwable $e) {
        $errorConexion = true;
    }
}

if (!($pdo instanceof PDO)) {
    $errorConexion = true;
}

/* Funciones generales */
if (!function_exists('e')) {
    function e($valor): string
    {
        return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('portalUrl')) {
    function portalUrl(string $archivo): string
    {
        return $archivo;
    }
}

if (!function_exists('portalNombreActual')) {
    function portalNombreActual(): string
    {
        return (string) ($_SESSION['usuario']->getNombreUsuario() ?? 'Estudiante de prueba');
    }
}

if (!function_exists('portalConsultar')) {
    function portalConsultar(PDO $pdo, string $sql, array $parametros = []): array
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
  
/* ==========================================
   OBTENER MATRÍCULAS DEL ESTUDIANTE
========================================== */

/* ==========================================
   OBTENER MATRÍCULAS DEL ESTUDIANTE
========================================== */

if (!function_exists('portalObtenerMatriculas')) {

    function portalObtenerMatriculas(PDO $pdo, string $usuario): array
    {
        // COMPROBAR QUE EXISTA UNA SESIÓN DE ESTUDIANTE
        if (
            !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
            $_SESSION['tipo_usuario'] !== 'estudiante'
        ) {
            return [];
        }

        // CONSULTAR MATRÍCULAS, CURSOS Y TAREAS
        $sql = "
            SELECT
                m.id_matricula,
                m.fecha,
                m.estado,
                m.id_curso,

                c.nombre AS nombre_curso,
                c.modalidad,
                c.duracion,
                c.descripcion,

                /* TOTAL DE TAREAS DEL CURSO */
                (
                    SELECT COUNT(*)

                    FROM tareas t

                    INNER JOIN clase cl
                        ON cl.id_clase = t.id_clase

                    WHERE cl.id_curso = m.id_curso

                ) AS total_tareas,

                /* TAREAS ENTREGADAS POR EL ESTUDIANTE */
                (
                    SELECT COUNT(DISTINCT t.id_tarea)

                    FROM tareas t

                    INNER JOIN clase cl
                        ON cl.id_clase = t.id_clase

                    INNER JOIN tareas_estudiantes te
                        ON te.id_tarea = t.id_tarea

                    WHERE cl.id_curso = m.id_curso

                      AND te.nombre_usuario_estudiante =
                          m.nombre_usuario_estudiante

                ) AS tareas_completadas

            FROM matricula m

            INNER JOIN curso c
                ON c.id_curso = m.id_curso

            WHERE m.nombre_usuario_estudiante = :usuario

            ORDER BY m.fecha DESC, m.id_matricula DESC
        ";

        // PREPARAR CONSULTA
        $consulta = $pdo->prepare($sql);

        // EJECUTAR CONSULTA
        $consulta->execute([
            ':usuario' => $usuario
        ]);

        // DEVOLVER MATRÍCULAS
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}

/* Inicio de la estructura visual */
if (!function_exists('portalLayoutInicio')) {
    function portalLayoutInicio(string $titulo, string $activo = 'portal'): void
    {
        global $errorConexion, $haySesionEstudiante;

        $nombre = portalNombreActual();
        $logo = '../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20sin%20Brillo.png';
        $inicial = mb_strtoupper(mb_substr(trim($nombre), 0, 1, 'UTF-8'), 'UTF-8');
        $userFt = $_SESSION['usuario']->getFoto();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= e($titulo) ?> | INADI</title>

            <link rel="stylesheet" href="CSS/stylesportal.css">
            <link rel="stylesheet" href="CSS/modulosportal.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        </head>
        <body>
        <div class="portal-shell">

            <aside class="portal-sidebar" id="portalSidebar">
                <a class="portal-brand" href="portalEstudiante.php">
                    <img src="<?= e($logo) ?>" alt="INADI" onerror="this.style.display='none'">
                    <span>
                        INADI
                        <small>Portal del estudiante</small>
                    </span>
                </a>

                <nav class="portal-nav" aria-label="Navegación principal">
                    <?php
                    $enlaces = [
                        'portal' => ['portalEstudiante.php', 'fa-house', 'Inicio'],
                        'cursos' => ['misCursos.php', 'fa-book-open', 'Mis cursos'],
                        'aula' => ['aulaVirtual.php', 'fa-chalkboard-user', 'Aula virtual'],
                        'legajo' => ['legajo.php', 'fa-folder-open', 'Mi legajo'],
                        'certificaciones' => ['certificaciones.php', 'fa-certificate', 'Certificaciones'],
                        'perfil' => ['perfil.php', 'fa-user', 'Perfil estudiante'],
                        'calendario' => ['calendario.php', 'fa-calendar-days', 'Calendario'],
                        'credencial' => ['credencial.php', 'fa-id-card', 'Credencial'],
                        'pagos' => ['misPagos.php', 'fa-credit-card', 'Mis pagos'],
                        'notificaciones' => ['notificaciones.php', 'fa-bell', 'Notificaciones'],
                    ];

                    foreach ($enlaces as $clave => $datos):
                    ?>
                        <a
                            class="portal-nav-link <?= $activo === $clave ? 'active' : '' ?>"
                            href="<?= e($datos[0]) ?>"
                            <?= $activo === $clave ? 'aria-current="page"' : '' ?>
                        >
                            <i class="fa-solid <?= e($datos[1]) ?>" aria-hidden="true"></i>
                            <span><?= e($datos[2]) ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="portal-sidebar-bottom">
                    <a class="portal-nav-link" href="../Inicio/inicioindex.php">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        <span>Volver al sitio</span>
                    </a>
                </div>
            </aside>

            <main class="portal-main">
                <header class="portal-topbar">
                    <button
                        class="portal-menu-toggle"
                        id="portalMenuToggle"
                        type="button"
                        aria-label="Abrir menú lateral"
                        aria-controls="portalSidebar"
                        aria-expanded="false"
                    >
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </button>

                    <div class="portal-topbar-title">
                        <span>Portal del estudiante</span>
                        <strong><?= e($titulo) ?></strong>
                    </div>

                    <!-- Menú desplegable del usuario -->
                    <div class="user-menu" id="userMenu">
                        <button
                            type="button"
                            class="user-menu-trigger"
                            id="userMenuTrigger"
                            aria-expanded="false"
                            aria-controls="userMenuDropdown"
                            aria-label="Abrir menú del estudiante"
                        >
                            <span class="user-avatar">
                            <?php
                                if ($userFt === NULL){
                                    echo '<span class="user-avatar">
                                        '. e($inicial) .'    
                                    </span>
                                    ';
                                }else
                                    echo '<img src="../database/consultas/estudiantes/fotosEst/'. $userFt .'" alt="" class="user-avatar" style="background-color: white;">';
                            ?></span>

                            <span class="user-info">
                                <strong><?= e($nombre); ?></strong>
                                <small>
                                    <i class="fa-solid fa-circle" aria-hidden="true"></i>
                                    <?= $haySesionEstudiante
                                        ? 'Alumno/a regular - INADI Salto'
                                        : 'Modo de prueba - INADI Salto' ?>
                                </small>
                            </span>

                            <i class="fa-solid fa-chevron-down user-chevron" aria-hidden="true"></i>
                        </button>

                        <div class="user-menu-dropdown" id="userMenuDropdown" hidden>
                            <a href="perfil.php" class="user-menu-item">
                                <i class="fa-regular fa-user" aria-hidden="true"></i>
                                <span>Mi perfil</span>
                            </a>

                            <?php if ($haySesionEstudiante): ?>
                                <a href="../login/logout.php" class="user-menu-item logout-item">
                                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                                    <span>Cerrar sesión</span>
                                </a>
                            <?php else: ?>
                                <a href="../Bienvenido/bienvenidoindex.php" class="user-menu-item logout-item">
                                    <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                                    <span>Ir al inicio de sesión</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </header>

                <div class="portal-content">
                    <?php if (!$haySesionEstudiante): ?>
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            <span>
                                Modo de desarrollo: estás viendo el portal sin iniciar sesión.
                                Los datos académicos reales no están disponibles en esta vista.
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorConexion): ?>
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                            <span>
                                No fue posible conectar con la base de datos.
                                Podés visualizar el diseño, pero las funciones que necesitan datos no estarán disponibles.
                            </span>
                        </div>
                    <?php endif; ?>
        <?php
    }
}

/* Cierre de la estructura visual */
if (!function_exists('portalLayoutFin')) {
    function portalLayoutFin(): void
    {
        ?>
                </div>
                <footer class="portal-footer">
                    INADI · Instituto de capacitación · Salto, Uruguay
                </footer>
            </main>
        </div>
        <script src="JS/scriptportal.js" defer></script>
        </body>
        </html>
        <?php
    }
}

/* Mensajes para módulos vacíos */
if (!function_exists('portalMostrarVacio')) {
    function portalMostrarVacio(
        string $titulo,
        string $mensaje,
        string $icono = 'fa-folder-open'
    ): void {
        ?>
        <div class="empty-state">
            <span class="empty-state-icon">
                <i class="fa-solid <?= e($icono) ?>" aria-hidden="true"></i>
            </span>
            <h3><?= e($titulo) ?></h3>
            <p><?= e($mensaje) ?></p>
        </div>
        <?php
    }
}

if (!function_exists('portalEtiquetaEstadoPago')) {
    function portalEtiquetaEstadoPago($estado): string
    {
        $estado = mb_strtolower(
            trim((string) ($estado ?? '')),
            'UTF-8'
        );

        $estados = [
            'pagado' => [
                'texto' => 'Pagado',
                'clase' => 'status-paid'
            ],
            'confirmado' => [
                'texto' => 'Confirmado',
                'clase' => 'status-paid'
            ],
            'pendiente' => [
                'texto' => 'Pendiente',
                'clase' => 'status-pending'
            ],
            'pendiente de pago' => [
                'texto' => 'Pendiente de pago',
                'clase' => 'status-pending'
            ],
            'rechazado' => [
                'texto' => 'Rechazado',
                'clase' => 'status-rejected'
            ]
        ];

        if ($estado === '') {
            $resultado = [
                'texto' => 'Sin verificar',
                'clase' => 'status-neutral'
            ];
        } else {
            $resultado = $estados[$estado] ?? [
                'texto' => 'Estado desconocido',
                'clase' => 'status-neutral'
            ];
        }

        return '<span class="status-pill '
            . e($resultado['clase'])
            . '">'
            . e($resultado['texto'])
            . '</span>';
    }
}

/* ==========================================
   BOTÓN PARA PAGAR MATRÍCULA
========================================== */

if (!function_exists('portalBotonPago')) {

    function portalBotonPago(int $idMatricula): void
    {
        ?>

        <form
            action="irAPago.php"
            method="POST"
            style="display:inline;"
        >

            <input
                type="hidden"
                name="id_matricula"
                value="<?= (int) $idMatricula ?>"
            >

            <button
                type="submit"
                class="btn btn-small btn-primary"
            >

                <i class="fa-solid fa-credit-card"></i>

                Pagar matrícula

            </button>

        </form>

        <?php
    }
}