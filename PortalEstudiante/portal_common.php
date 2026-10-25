<?php
/**
 * INADI - Funciones compartidas del Portal del Estudiante.
 */

// Iniciar la sesión antes de incluir dependencias que puedan emitir salida.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../database/Clases/Estudiante.php';
require_once __DIR__ . '/../Estructura/topbar_controls.php';


/* ======================================================
   1. SESIÓN DEL USUARIO
====================================================== */

$usuario = $_SESSION['usuario'] ?? null;

$haySesionEstudiante = (
    is_object($usuario) &&
    !($usuario instanceof __PHP_Incomplete_Class) &&
    isset($_SESSION['tipo_usuario']) &&
    strtolower(trim((string) $_SESSION['tipo_usuario'])) === 'estudiante'
);

$nombreUsuario = 'Estudiante de prueba';

if (
    $haySesionEstudiante &&
    method_exists($usuario, 'getNombreUsuario')
) {
    try {
        $nombreObtenido = $usuario->getNombreUsuario();

        if (is_scalar($nombreObtenido) && trim((string) $nombreObtenido) !== '') {
            $nombreUsuario = trim((string) $nombreObtenido);
        }
    } catch (Throwable $e) {
        error_log('No se pudo obtener el nombre del estudiante: ' . $e->getMessage());
    }
}

/*
 * Si querés que el portal sea obligatorio para estudiantes
 * autenticados, descomentá las siguientes líneas.
 *
 * if (!$haySesionEstudiante) {
 *     header('Location: ../Bienvenido/bienvenidoindex.php');
 *     exit;
 * }
 */

/* ======================================================
   2. CONEXIÓN A LA BASE DE DATOS
====================================================== */

$pdo = $pdo ?? null;
$errorConexion = false;

$rutaConexion = __DIR__ . '/../database/consultas/conexion.php';

if (!($pdo instanceof PDO) && is_file($rutaConexion)) {
    try {
        require_once $rutaConexion;
    } catch (Throwable $e) {
        error_log('Error de conexión del portal: ' . $e->getMessage());
        $errorConexion = true;
    }
}

if (!($pdo instanceof PDO)) {
    $errorConexion = true;
}

/* ======================================================
   3. FUNCIONES GENERALES
====================================================== */

if (!function_exists('e')) {
    function e($valor): string
    {
        return htmlspecialchars(
            (string) ($valor ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
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
        $usuario = $_SESSION['usuario'] ?? null;

        if (
            is_object($usuario) &&
            !($usuario instanceof __PHP_Incomplete_Class) &&
            method_exists($usuario, 'getNombre')
        ) {
            $nombre = trim((string) $usuario->getNombre());

            if ($nombre !== '') {
                return $nombre;
            }
        }

        if (
            is_object($usuario) &&
            !($usuario instanceof __PHP_Incomplete_Class) &&
            method_exists($usuario, 'getNombreUsuario')
        ) {
            $nombre = trim((string) $usuario->getNombreUsuario());

            if ($nombre !== '') {
                return $nombre;
            }
        }

        return 'Estudiante de prueba';
    }
}

if (!function_exists('portalConsultar')) {
    function portalConsultar(
        PDO $pdo,
        string $sql,
        array $parametros = []
    ): array {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/* ======================================================
   4. OBTENER MATRÍCULAS DEL ESTUDIANTE
====================================================== */

if (!function_exists('portalObtenerMatriculas')) {
    function portalObtenerMatriculas(
        PDO $pdo,
        string $usuario
    ): array {
        if (
            !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
            strtolower(trim((string) $_SESSION['tipo_usuario'])) !== 'estudiante'
        ) {
            return [];
        }

        $sql = "
            SELECT
                m.id_matricula,
                m.fecha,
                m.estado,
                m.id_curso,
                m.id_pago,

                c.nombre AS nombre_curso,
                c.modalidad,
                c.duracion,
                c.descripcion,

                p.estado AS estado_pago,

                (
                    SELECT COUNT(*)
                    FROM tareas t
                    INNER JOIN clase cl
                        ON cl.id_clase = t.id_clase
                    WHERE cl.id_curso = m.id_curso
                ) AS total_tareas,

                (
                    SELECT COUNT(*)
                    FROM tareas_estudiantes te
                    INNER JOIN tareas t
                        ON t.id_tarea = te.id_tarea
                    INNER JOIN clase cl
                        ON cl.id_clase = t.id_clase
                    WHERE cl.id_curso = m.id_curso
                    AND te.nombre_usuario_estudiante =
                        m.nombre_usuario_estudiante
                    AND te.completada = 1
                ) AS tareas_completadas

            FROM matricula m

            LEFT JOIN curso c
                ON c.id_curso = m.id_curso

            LEFT JOIN pagos p
                ON p.id_pago = m.id_pago

            WHERE m.nombre_usuario_estudiante = :usuario

            ORDER BY m.fecha DESC
        ";

        return portalConsultar(
            $pdo,
            $sql,
            [':usuario' => $usuario]
        );
    }
}

/* ======================================================
   5. INICIO DE LA ESTRUCTURA VISUAL
====================================================== */

if (!function_exists('portalLayoutInicio')) {
    function portalLayoutInicio(
        string $titulo,
        string $activo = 'portal'
    ): void {
        global $errorConexion, $haySesionEstudiante;

        $logo = '../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20sin%20Brillo.png';
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <title><?= e($titulo) ?> | INADI</title>

            <link rel="stylesheet" href="CSS/stylesportal.css">
            <link rel="stylesheet" href="CSS/modulosportal.css">
            <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/topbar-controls.css?v=1">
            <link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/paneles-theme.css?v=1">

            <link
                rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
            >
        </head>

        <body>
        <div class="portal-shell">

            <!-- MENÚ LATERAL -->
            <aside class="portal-sidebar" id="portalSidebar">

                <a class="portal-brand" href="portalEstudiante.php">
                    <img
                        src="<?= e($logo) ?>"
                        alt="INADI"
                        onerror="this.style.display='none'"
                    >

                    <span>
                        INADI
                        <small>Portal del estudiante</small>
                    </span>
                </a>

                <nav class="portal-nav" aria-label="Navegación principal">

                    <?php
                    $enlaces = [
                        'portal' => [
                            'portalEstudiante.php',
                            'fa-house',
                            'Inicio'
                        ],
                        'cursos' => [
                            'misCursos.php',
                            'fa-book-open',
                            'Mis cursos'
                        ],
                        'aula' => [
                            'aulaVirtual.php',
                            'fa-chalkboard-user',
                            'Aula virtual'
                        ],
                        'legajo' => [
                            'legajo.php',
                            'fa-folder-open',
                            'Mi legajo'
                        ],
                        'calificaciones' => [
                            'calificaciones.php',
                            'fa-graduation-cap',
                            'Calificaciones'
                        ],
                        'perfil' => [
                            'perfil.php',
                            'fa-user',
                            'Perfil estudiante'
                        ],
                        'calendario' => [
                            'calendario.php',
                            'fa-calendar-days',
                            'Calendario'
                        ],
                        'credencial' => [
                            'credencial.php',
                            'fa-id-card',
                            'Credencial'
                        ],
                        'pagos' => [
                            'misPagos.php',
                            'fa-credit-card',
                            'Mis pagos'
                        ],
                        'notificaciones' => [
                            'notificaciones.php',
                            'fa-bell',
                            'Notificaciones'
                        ],
                    ];

                    foreach ($enlaces as $clave => $datos):
                    ?>

                        <a
                            class="portal-nav-link <?= $activo === $clave ? 'active' : '' ?>"
                            href="<?= e($datos[0]) ?>"
                            <?= $activo === $clave ? 'aria-current="page"' : '' ?>
                        >
                            <i
                                class="fa-solid <?= e($datos[1]) ?>"
                                aria-hidden="true"
                            ></i>

                            <span><?= e($datos[2]) ?></span>
                        </a>

                    <?php endforeach; ?>

                </nav>

                <div class="portal-sidebar-bottom">

                    <a
                        class="portal-nav-link"
                        href="../Inicio/inicioindex.php"
                    >
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        <span>Volver al sitio</span>
                    </a>

                    <?php if ($haySesionEstudiante): ?>

                        <a
                            class="portal-nav-link portal-logout"
                            href="../login/logout.php"
                        >
                            <i
                                class="fa-solid fa-right-from-bracket"
                                aria-hidden="true"
                            ></i>
                            <span>Cerrar sesión</span>
                        </a>

                    <?php else: ?>

                        <a
                            class="portal-nav-link portal-logout"
                            href="../Bienvenido/bienvenidoindex.php"
                        >
                            <i
                                class="fa-solid fa-right-to-bracket"
                                aria-hidden="true"
                            ></i>
                            <span>Ir al inicio de sesión</span>
                        </a>

                    <?php endif; ?>

                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
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

                    <?php renderSiteTopbarControls(); ?>

                </header>

                <div class="portal-content">

                    <?php if (!$haySesionEstudiante): ?>

                        <div class="alert alert-warning">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>

                            <span>
                                Modo de desarrollo: estás viendo el portal
                                sin iniciar sesión. Los datos académicos
                                reales no están disponibles en esta vista.
                            </span>
                        </div>

                    <?php endif; ?>

                    <?php if ($errorConexion): ?>

                        <div class="alert alert-warning">
                            <i
                                class="fa-solid fa-circle-exclamation"
                                aria-hidden="true"
                            ></i>

                            <span>
                                No fue posible conectar con la base de datos.
                                Podés visualizar el diseño, pero las funciones
                                que necesitan datos no estarán disponibles.
                            </span>
                        </div>

                    <?php endif; ?>

        <?php
    }
}

/* ======================================================
   6. CIERRE DE LA ESTRUCTURA VISUAL
====================================================== */

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

/* ======================================================
   7. MENSAJES PARA MÓDULOS VACÍOS
====================================================== */

if (!function_exists('portalMostrarVacio')) {
    function portalMostrarVacio(
        string $titulo,
        string $mensaje,
        string $icono = 'fa-folder-open'
    ): void {
        ?>

        <div class="empty-state">

            <span class="empty-state-icon">
                <i
                    class="fa-solid <?= e($icono) ?>"
                    aria-hidden="true"
                ></i>
            </span>

            <h3><?= e($titulo) ?></h3>
            <p><?= e($mensaje) ?></p>

        </div>

        <?php
    }
}

/* ======================================================
   8. ETIQUETAS DE ESTADO DE PAGO
====================================================== */

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
            'abonado' => [
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
            'rechazado' => [
                'texto' => 'Rechazado',
                'clase' => 'status-rejected'
            ],
            'vencido' => [
                'texto' => 'Vencido',
                'clase' => 'status-rejected'
            ],
            'cancelado' => [
                'texto' => 'Cancelado',
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
