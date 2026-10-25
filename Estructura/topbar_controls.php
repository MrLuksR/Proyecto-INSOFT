<?php

/**
 * Controles compartidos de idioma y usuario para las barras superiores INADI.
 */

if (!function_exists('inadiTopbarEscape')) {
    function inadiTopbarEscape($value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('inadiTopbarIdentity')) {
    function inadiTopbarIdentity(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $usuario = $_SESSION['usuario'] ?? null;
        $tipo = strtolower(trim((string) ($_SESSION['tipo_usuario'] ?? '')));
        $esAdmin = !empty($_SESSION['admin_id']);

        $sesionDeUsuario = is_object($usuario)
            && !($usuario instanceof __PHP_Incomplete_Class);
        $autenticado = $esAdmin
            || $sesionDeUsuario
            || !empty($_SESSION['nombre_usuario'])
            || !empty($_SESSION['usuario_nombre']);

        $rutaActual = strtolower((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $esEstudiante = $tipo === 'estudiante';
        $esDocente = in_array($tipo, ['docente', 'profesor', 'profesora', 'teacher'], true);

        if ($autenticado && !$esAdmin && !$esEstudiante && !$esDocente) {
            $esEstudiante = strpos($rutaActual, '/portalestudiante/') !== false
                || strpos($rutaActual, '/inicio/inicioestudiante.php') !== false;
            $esDocente = strpos($rutaActual, '/portaldocente/') !== false;
        }

        $nombre = trim((string) (
            $_SESSION['admin_name']
            ?? $_SESSION['nombre']
            ?? $_SESSION['nombre_usuario']
            ?? $_SESSION['usuario_nombre']
            ?? ''
        ));

        if ($sesionDeUsuario) {
            foreach (['getNombre', 'getNombreUsuario'] as $metodo) {
                if (!method_exists($usuario, $metodo)) {
                    continue;
                }

                try {
                    $nombreObtenido = trim((string) $usuario->{$metodo}());
                    if ($nombreObtenido !== '') {
                        $nombre = $nombreObtenido;
                        break;
                    }
                } catch (Throwable $e) {
                    error_log('No se pudo obtener el nombre del usuario de la barra superior.');
                }
            }
        }

        if ($nombre === '') {
            $nombre = $autenticado ? 'Mi cuenta' : 'Acceso al portal';
        }

        if ($esAdmin) {
            $rol = 'Administración';
            $enlaces = [
                ['Panel administrativo', '/Proyecto-INSOFT/Admin/index.php?page=dashboard'],
                ['Mi perfil', '/Proyecto-INSOFT/Admin/index.php?page=perfil'],
                ['Cerrar sesión', '/Proyecto-INSOFT/Admin/logout.php', 'logout'],
            ];
        } elseif ($esDocente) {
            $rol = 'Docente';
            $enlaces = [
                ['Panel docente', '/Proyecto-INSOFT/PortalDocente/bienvenidoindex.php'],
                ['Sitio principal', '/Proyecto-INSOFT/Inicio/inicioindex.php'],
                ['Cerrar sesión', '/Proyecto-INSOFT/login/logout.php', 'logout'],
            ];
        } elseif ($esEstudiante) {
            $rol = 'Estudiante';
            $enlaces = [
                ['Panel de estudiante', '/Proyecto-INSOFT/PortalEstudiante/portalEstudiante.php'],
                ['Mi perfil', '/Proyecto-INSOFT/PortalEstudiante/perfil.php'],
                ['Cerrar sesión', '/Proyecto-INSOFT/login/logout.php', 'logout'],
            ];
        } elseif ($autenticado) {
            $rol = 'Usuario';
            $enlaces = [
                ['Mi cuenta', '/Proyecto-INSOFT/login/inicio.php'],
                ['Cerrar sesión', '/Proyecto-INSOFT/login/logout.php', 'logout'],
            ];
        } else {
            $rol = 'Invitado';
            $enlaces = [
                ['Iniciar sesión', '/Proyecto-INSOFT/login/loginn.php'],
            ];
        }

        return [
            'nombre' => $nombre,
            'rol' => $rol,
            'enlaces' => $enlaces,
        ];
    }
}

if (!function_exists('inadiLanguageControl')) {
    function inadiLanguageControl(): void
    {
        echo '
            <span class="site-language-button" aria-label="Idioma actual: Español" title="Idioma actual: Español">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"></path>
                </svg>
                <span>ES</span>
            </span>
        ';
    }
}

if (!function_exists('inadiUserControl')) {
    function inadiUserControl(): void
    {
        $identidad = inadiTopbarIdentity();
        $nombre = inadiTopbarEscape($identidad['nombre']);
        $rol = inadiTopbarEscape($identidad['rol']);

        echo '
            <div class="site-account">
                <details>
                    <summary class="site-account-trigger" aria-label="Abrir menú de usuario" title="Menú de usuario">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <circle cx="12" cy="8" r="3.4"></circle>
                            <path d="M5.4 20c.6-3.1 3.1-5 6.6-5s6 1.9 6.6 5"></path>
                        </svg>
                    </summary>
                    <div class="site-account-menu">
                        <div class="site-account-heading">
                            <strong>' . $nombre . '</strong>
                            <span>' . $rol . '</span>
                        </div>
        ';

        foreach ($identidad['enlaces'] as $enlace) {
            $clase = ($enlace[2] ?? '') === 'logout'
                ? 'site-account-link site-account-logout'
                : 'site-account-link';

            echo '<a class="' . $clase . '" href="' . inadiTopbarEscape($enlace[1]) . '">'
                . inadiTopbarEscape($enlace[0])
                . '</a>';
        }

        echo '
                    </div>
                </details>
            </div>
        ';
    }
}

if (!function_exists('renderSiteTopbarControls')) {
    function renderSiteTopbarControls(): void
    {
        echo '<div class="site-header-controls">';
        inadiLanguageControl();
        inadiUserControl();
        echo '</div>';
    }
}
