<?php

/**
 * CONTROLES COMPARTIDOS DE LA BARRA SUPERIOR - INADI
 */

require_once __DIR__ . "/../database/Clases/Estudiante.php";
require_once __DIR__ . '/../database/Clases/Administrativo.php';

// INICIAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// PROTEGER CONTENIDO HTML
function inadiTopbarEscape($valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


// OBTENER IDENTIDAD DEL USUARIO
function inadiTopbarIdentity(): array{
    // RECUPERAR OBJETO DE SESIÓN
    $usuario = $_SESSION['usuario'] ?? null;

    // OBTENER TIPO DE USUARIO
    $tipo = strtolower(
        trim((string) ($_SESSION['tipo_usuario'] ?? ''))
    );

    // COMPROBAR ESTUDIANTE AUTENTICADO
    if (
        $usuario instanceof Estudiante &&
        $tipo === 'estudiante'
    ) {

        // OBTENER NOMBRE
        $nombre = trim((string) $usuario->getNombre());

        if ($nombre === '') {
            $nombre = $usuario->getNombreUsuario();
        }

        return [
            'nombre' => $nombre,
            'rol' => 'Estudiante',
            'usuario' => $usuario,

            'enlaces' => [
                [
                    'Panel de estudiante',
                    '../PortalEstudiante/portalEstudiante.php'
                ],
                [
                    'Mi perfil',
                    '../PortalEstudiante/perfil.php'
                ],
                [
                    'Cerrar sesión',
                    '../login/logout.php',
                    'logout'
                ]
            ]
        ];
    }

    // COMPROBAR ADMINISTRATIVO AUTENTICADO
    if (
        $usuario instanceof Administrativo &&
        $tipo === 'administrativo'
    ) {

        // OBTENER NOMBRE
        $nombre = trim((string) $usuario->getNombre());

        if ($nombre === '') {
            $nombre = $usuario->getNombreUsuario();
        }

        return [
            'nombre' => $nombre,
            'rol' => 'Administrativo',
            'usuario' => $usuario,

            'enlaces' => [
                [
                    'Panel administrativo',
                    '../Admin/index.php'
                ],
                [
                    'Cerrar sesión',
                    '../login/logout.php',
                    'logout'
                ]
            ]
        ];
    }

    // USUARIO INVITADO
    return [
        'nombre' => 'Acceso al portal',
        'rol' => 'Invitado',
        'usuario' => null,

        'enlaces' => [
            [
                'Iniciar sesión',
                '../login/loginn.php'
            ]
        ]
    ];
}


// CONTROL DE IDIOMA
function inadiLanguageControl(): void
{
    echo '
        <span
            class="site-language-button"
            aria-label="Idioma actual: Español"
            title="Idioma actual: Español"
        >
            <svg
                viewBox="0 0 24 24"
                aria-hidden="true"
                focusable="false"
            >
                <circle cx="12" cy="12" r="9"></circle>

                <path
                    d="M3 12h18
                       M12 3a14 14 0 0 1 0 18
                       M12 3a14 14 0 0 0 0 18">
                </path>
            </svg>

            <span>ES</span>
        </span>
    ';
}


// CONTROL DE USUARIO
function inadiUserControl(): void
{
    // OBTENER INFORMACIÓN DEL USUARIO
    $identidad = inadiTopbarIdentity();

    $nombre = inadiTopbarEscape($identidad['nombre']);
    $rol = inadiTopbarEscape($identidad['rol']);

    $usuario = $identidad['usuario'];

    // ÍCONO PREDETERMINADO
    $imagen = '
        <svg
            viewBox="0 0 24 24"
            aria-hidden="true"
            focusable="false"
        >
            <circle cx="12" cy="8" r="3.4"></circle>
            <path d="M5.4 20c.6-3.1 3.1-5 6.6-5s6 1.9 6.6 5"></path>
        </svg>
    ';

    // COMPROBAR SI EXISTE UN ESTUDIANTE
    if ($usuario instanceof Estudiante) {

        // OBTENER FOTOGRAFÍA
        $foto = (string) $usuario->getFoto();

        if ($foto !== '') {

            // PROTEGER NOMBRE DEL ARCHIVO
            $fotoSegura = basename($foto);

            // RUTA FÍSICA
            $rutaFisica = __DIR__
                . '/../database/consultas/estudiantes/fotosEst/'
                . $fotoSegura;

            // COMPROBAR QUE EXISTA
            if (
                $fotoSegura === $foto &&
                is_file($rutaFisica)
            ) {

                // RUTA WEB ABSOLUTA
                $rutaWeb = '../database/consultas/estudiantes/fotosEst/'
                    . rawurlencode($fotoSegura);

                // EVITAR CACHÉ DE FOTOGRAFÍA ANTERIOR
                $version = filemtime($rutaFisica);

                $rutaWeb .= '?v=' . $version;

                // MOSTRAR FOTOGRAFÍA
                $imagen = '
                    <img
                        src="' . inadiTopbarEscape($rutaWeb) . '"
                        alt="Foto de perfil"
                        class="site-account-photo"
                    >
                ';
            }
        }
    }

    // MOSTRAR MENÚ
    echo '
        <div class="site-account">

            <details>

                <summary
                    class="site-account-trigger"
                    aria-label="Abrir menú de usuario"
                    title="Menú de usuario"
                >
                    ' . $imagen . '
                </summary>

                <div class="site-account-menu">

                    <div class="site-account-heading">

                        <strong>' . $nombre . '</strong>

                        <span>' . $rol . '</span>

                    </div>
    ';

    // MOSTRAR ENLACES
    foreach ($identidad['enlaces'] as $enlace) {

        $clase = ($enlace[2] ?? '') === 'logout'
            ? 'site-account-link site-account-logout'
            : 'site-account-link';

        echo '
            <a
                class="' . $clase . '"
                href="' . inadiTopbarEscape($enlace[1]) . '"
            >
                ' . inadiTopbarEscape($enlace[0]) . '
            </a>
        ';
    }

    echo '
                </div>

            </details>

        </div>
    ';
}


// MOSTRAR CONTROLES DE LA BARRA SUPERIOR
function renderSiteTopbarControls(): void
{
    echo '<div class="site-header-controls">';

    inadiLanguageControl();

    inadiUserControl();

    echo '</div>';
}