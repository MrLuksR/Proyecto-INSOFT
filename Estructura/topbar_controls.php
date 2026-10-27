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
    $idioma = (string) ($_COOKIE['googtrans'] ?? '');
    $actual = 'es';
    if (preg_match('~/(es|en|pt)(?:/|$)~', $idioma, $m)) {
        $actual = $m[1];
    }
    $nombres = ['es' => 'Español', 'en' => 'English', 'pt' => 'Português'];
    $nombreActual = $nombres[$actual] ?? 'Español';
    echo '
        <div class="site-language" id="site-language-control">
            <details class="site-language-details">
                <summary class="site-language-button" aria-label="Elegir idioma" title="Idioma: ' . inadiTopbarEscape($nombreActual) . '">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M3 12h18 M12 3a14 14 0 0 1 0 18 M12 3a14 14 0 0 0 0 18"></path>
                    </svg>
                    <span>' . strtoupper($actual) . '</span>
                    <span class="site-language-chevron" aria-hidden="true">▾</span>
                </summary>
                <div class="site-language-menu" role="menu" aria-label="Seleccionar idioma">
                    <button type="button" role="menuitem" data-language="es">🇪🇸 Español</button>
                    <button type="button" role="menuitem" data-language="en">🇬🇧 English</button>
                    <button type="button" role="menuitem" data-language="pt">🇧🇷 Português</button>
                </div>
            </details>
        </div>
        <div id="google_translate_element" class="site-translate-engine" aria-hidden="true"></div>
        <style>
            .site-language{position:relative}.site-language-details{position:relative}
            .site-language-button{display:flex;align-items:center;justify-content:center;gap:6px;cursor:pointer;list-style:none}
            .site-language-button::-webkit-details-marker{display:none}
            .site-language-chevron{font-size:11px;margin-left:1px}
            .site-language-menu{position:absolute;right:0;top:calc(100% + 9px);min-width:160px;padding:6px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 12px 30px #17274420;z-index:1000}
            .site-language-menu button{display:block;width:100%;border:0;background:transparent;text-align:left;padding:10px 12px;border-radius:8px;color:#26364e;cursor:pointer;font:inherit;font-size:13px;white-space:nowrap}
            .site-language-menu button:hover,.site-language-menu button:focus{background:#f0f5ff;outline:none}
            .site-translate-engine{position:absolute!important;left:-10000px!important;top:auto!important;width:1px!important;height:1px!important;overflow:hidden!important}
            body{top:0!important}.goog-te-banner-frame,.skiptranslate iframe{display:none!important}
        </style>
        <script>
        (function(){
            if(window.__inadiLanguageControlLoaded)return;
            window.__inadiLanguageControlLoaded=true;
            window.googleTranslateElementInit=function(){
                if(window.google&&google.translate){new google.translate.TranslateElement({pageLanguage:"es",includedLanguages:"es,en,pt",autoDisplay:false},"google_translate_element");}
            };
            function setLanguage(lang){
                if(["es","en","pt"].indexOf(lang)===-1)return;
                var value=lang==="es"?"/es/es":"/es/"+lang;
                document.cookie="googtrans="+value+"; path=/";
                document.cookie="googtrans="+value+"; path=/Proyecto-INSOFT";
                var select=document.querySelector("#google_translate_element select");
                if(select){select.value=lang;select.dispatchEvent(new Event("change"));}
                else{window.location.reload();}
            }
            document.addEventListener("click",function(e){
                var button=e.target.closest("[data-language]");
                if(!button)return;
                e.preventDefault();setLanguage(button.getAttribute("data-language"));
            });
            var script=document.createElement("script");script.src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";script.async=true;document.head.appendChild(script);
        })();
        </script>
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
