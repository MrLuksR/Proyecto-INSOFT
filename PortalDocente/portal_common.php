
<?php

/* PORTAL DOCENTE INADI - ARCHIVO COMÚN */

/* INICIAR SESIÓN */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* CONEXIÓN A LA BASE DE DATOS */
$portalConnectionFile = dirname(__DIR__) . '/database/consultas/conexion.php';

if (is_file($portalConnectionFile)) {
    require_once $portalConnectionFile;
}

if (!isset($pdo)) {
    $pdo = null;
}

/* RUTAS ABSOLUTAS DEL PORTAL */
if (!function_exists('portal_asset')) {
    function portal_asset(string $file): string
    {
        return '/Proyecto-INSOFT/PortalDocente/' . ltrim($file, '/');
    }
}

/* ESCAPAR TEXTO HTML */
if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

/* INFORMACIÓN DE CADA PÁGINA */
if (!function_exists('portal_page_info')) {
    function portal_page_info(): array
    {
        $page = strtolower(basename($_SERVER['SCRIPT_NAME'] ?? ''));

        $pages = [
            'bienvenidoindex.php' => [
                'Inicio docente',
                'fa-solid fa-house',
                'Tu espacio de trabajo'
            ],
            'calendarioindex.php' => [
                'Calendario docente',
                'fa-solid fa-calendar-days',
                'Organización académica'
            ],
            'mensajesindex.php' => [
                'Mensajes',
                'fa-solid fa-envelope',
                'Comunicación docente'
            ],
            'recursosindex.php' => [
                'Recursos docentes',
                'fa-solid fa-folder-open',
                'Materiales y herramientas'
            ]
        ];

        return $pages[$page] ?? [
            'Portal Docente',
            'fa-solid fa-chalkboard-user',
            'Espacio de trabajo docente'
        ];
    }
}

/* IDENTIFICADOR DEL USUARIO */
if (!function_exists('portal_user_key')) {
    function portal_user_key(): string
    {
        $keys = [
            $_SESSION['usuario_id'] ?? null,
            $_SESSION['user_id'] ?? null,
            $_SESSION['id_usuario'] ?? null,
            $_SESSION['usuario']['id'] ?? null,
            $_SESSION['user']['id'] ?? null,
            $_SESSION['usuario']['email'] ?? null,
            $_SESSION['user']['email'] ?? null
        ];

        foreach ($keys as $value) {
            if (is_scalar($value) && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return session_id();
    }
}

/* NOMBRE DEL USUARIO */
if (!function_exists('portal_user_name')) {
    function portal_user_name(): string
    {
        $names = [
            $_SESSION['nombre'] ?? null,
            $_SESSION['nombre_usuario'] ?? null,
            $_SESSION['usuario_nombre'] ?? null,
            $_SESSION['usuario']['nombre'] ?? null,
            $_SESSION['user']['name'] ?? null,
            $_SESSION['user']['nombre'] ?? null
        ];

        foreach ($names as $value) {
            if (is_scalar($value) && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return 'Docente';
    }
}

/* TOKEN DE SEGURIDAD CSRF */
if (!function_exists('portal_csrf_token')) {
    function portal_csrf_token(): string
    {
        if (
            empty($_SESSION['portal_csrf_token']) ||
            !is_string($_SESSION['portal_csrf_token'])
        ) {
            $_SESSION['portal_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['portal_csrf_token'];
    }
}

/* VERIFICAR TOKEN CSRF */
if (!function_exists('portal_verify_csrf')) {
    function portal_verify_csrf(): bool
    {
        $submitted = $_POST['csrf_token'] ?? '';
        $stored = $_SESSION['portal_csrf_token'] ?? '';

        return is_string($submitted)
            && is_string($stored)
            && $submitted !== ''
            && $stored !== ''
            && hash_equals($stored, $submitted);
    }
}

/* VERIFICAR IDENTIFICADOR */
if (!function_exists('portal_require_user_key')) {
    function portal_require_user_key(): string
    {
        $key = portal_user_key();

        if ($key === '') {
            throw new RuntimeException(
                'No se pudo identificar al usuario.'
            );
        }

        return $key;
    }
}

/* MENSAJES TEMPORALES */
if (!function_exists('portal_flash')) {
    function portal_flash(string $type, string $message): void
    {
        $_SESSION['portal_flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

if (!function_exists('portal_show_flash')) {
    function portal_show_flash(): void
    {
        if (empty($_SESSION['portal_flash'])) {
            return;
        }

        $flash = $_SESSION['portal_flash'];
        unset($_SESSION['portal_flash']);

        $type = ($flash['type'] ?? '') === 'success'
            ? 'success'
            : 'error';

        echo '<div class="portal-alert portal-alert-' . h($type) . '" role="status">';
        echo h($flash['message'] ?? '');
        echo '</div>';
    }
}

/* =========================================================
   ENCABEZADO COMÚN Y CARGA DE LOS CSS
========================================================= */
if (!function_exists('portal_header')) {
    function portal_header(
        string $title,
        string $icon = 'fa-solid fa-chalkboard-user',
        string $kicker = 'Espacio de trabajo docente'
    ): void {

        $name = portal_user_name();
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }

        if ($initials === '') {
            $initials = 'D';
        }

        $currentPage = strtolower(
            basename($_SERVER['SCRIPT_NAME'] ?? '')
        );

        $menu = [
            'bienvenidoindex.php' => [
                'Inicio docente',
                'fa-solid fa-house',
                'bienvenidoindex.php'
            ],
            'calendarioindex.php' => [
                'Calendario',
                'fa-solid fa-calendar-days',
                'calendarioindex.php'
            ],
            'mensajesindex.php' => [
                'Mensajes',
                'fa-solid fa-envelope',
                'mensajesindex.php'
            ],
            'recursosindex.php' => [
                'Recursos docentes',
                'fa-solid fa-folder-open',
                'recursosindex.php'
            ]
        ];

        echo '<!DOCTYPE html>';
        echo '<html lang="es">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>' . h($title) . ' | INADI</title>';

        /* FUENTES E ICONOS */
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        echo '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">';
        echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">';

        /* LOS DOS CSS SE CARGAN EN TODAS LAS PÁGINAS */
        echo '<link rel="stylesheet" href="' . h(portal_asset('portalInterno.css')) . '?v=10">';
        echo '<link rel="stylesheet" href="' . h(portal_asset('portalDocente.css')) . '?v=10">';

        echo '</head>';
        echo '<body>';

        echo '<div class="portal-shell">';

        /* MENÚ LATERAL */
        echo '<aside class="sidebar" id="sidebar">';

        echo '<div class="sidebar-brand">';
        echo '<span class="brand-icon"><i class="fa-solid fa-graduation-cap"></i></span>';
        echo '<div class="brand-text">';
        echo '<strong>INADI</strong>';
        echo '<span>Portal Docente</span>';
        echo '</div>';
        echo '</div>';

        echo '<nav class="sidebar-nav" aria-label="Navegación principal">';

        foreach ($menu as $file => $data) {
            $active = ($currentPage === $file);

            echo '<a class="sidebar-link' .
                ($active ? ' active' : '') .
                '" href="' . h(portal_asset($data[2])) . '"';

            if ($active) {
                echo ' aria-current="page"';
            }

            echo '>';
            echo '<i class="' . h($data[1]) . '"></i>';
            echo '<span>' . h($data[0]) . '</span>';
            echo '</a>';
        }

        echo '</nav>';

        echo '<div class="sidebar-bottom">';
        echo '<a class="sidebar-link" href="/Proyecto-INSOFT/Inicio/inicioindex.php">';
        echo '<i class="fa-solid fa-arrow-up-right-from-square"></i>';
        echo '<span>Volver al inicio</span>';
        echo '</a>';
        echo '</div>';

        echo '<div class="sidebar-footer">';
        echo '<strong>Instituto de Informática</strong>';
        echo '<span>INADI · Salto</span>';
        echo '</div>';

        echo '</aside>';

        /* ÁREA PRINCIPAL */
        echo '<div class="main-area">';

        /* BARRA SUPERIOR */
        echo '<header class="topbar">';

        echo '<button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Mostrar u ocultar menú">';
        echo '<i class="fa-solid fa-bars"></i>';
        echo '</button>';

        echo '<div class="topbar-heading">';
        echo '<span class="topbar-title">PORTAL DOCENTE</span>';
        echo '<span class="topbar-subtitle">Organización académica</span>';
        echo '</div>';

        echo '<div class="topbar-actions">';

        echo '<a class="topbar-agenda" href="' .
            h(portal_asset('calendarioindex.php')) . '">';
        echo '<i class="fa-regular fa-calendar"></i>';
        echo '<span>Mi agenda</span>';
        echo '</a>';

        echo '<div class="profile-avatar">' . h($initials) . '</div>';

        echo '<div class="profile-info">';
        echo '<strong>' . h($name) . '</strong>';
        echo '<span>Portal Docente</span>';
        echo '</div>';

        echo '</div>';
        echo '</header>';

        /* ENCABEZADO DE LA PÁGINA */
        echo '<main class="portal-content">';

        echo '<div class="page-heading">';
        echo '<div class="page-heading-icon"><i class="' . h($icon) . '"></i></div>';
        echo '<div>';
        echo '<p class="page-kicker">' . h($kicker) . '</p>';
        echo '<h1>' . h($title) . '</h1>';
        echo '<p class="page-description">Portal Docente · Instituto de Informática INADI</p>';
        echo '</div>';
        echo '</div>';

        portal_show_flash();
    }
}

/* =========================================================
   PIE DE PÁGINA Y JAVASCRIPT
========================================================= */
if (!function_exists('portal_footer')) {
    function portal_footer(): void
    {
        echo '</main>';

        echo '<footer class="portal-footer">';
        echo '<div><strong>INADI</strong> · Portal Docente</div>';
        echo '<div>Instituto de Informática · Salto, Uruguay</div>';
        echo '<a href="/Proyecto-INSOFT/Inicio/inicioindex.php">Volver al sitio principal</a>';
        echo '</footer>';

        echo '</div>';
        echo '</div>';

        echo '<script src="' . h(portal_asset('portal-docente.js')) . '?v=10"></script>';

        echo '</body>';
        echo '</html>';
    }
}

/* MANEJO DE ERRORES DE BASE DE DATOS */
if (!function_exists('portal_db_error')) {
    function portal_db_error(Throwable $e, string $friendly): void
    {
        error_log('Portal Docente DB: ' . $e->getMessage());
        portal_flash('error', $friendly);
    }
}
