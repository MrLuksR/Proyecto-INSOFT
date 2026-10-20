<?php

// 1. CONFIGURACIÓN INICIAL Y CONEXIÓN A LA BASE DE DATOS

// Iniciar sesión si todavía no está activa.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Ruta del archivo de conexión.
$portalConnectionFile = dirname(__DIR__) . '/database/consultas/conexion.php';

// Incluir la conexión si el archivo existe.
if (is_file($portalConnectionFile)) {
    require_once $portalConnectionFile;
}

// Verificar que exista una conexión PDO válida.
if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log(
        'Portal Docente: conexion.php no definió una conexión PDO en $pdo.'
    );

    http_response_code(500);

    exit(
        'No se pudo conectar con la base de datos. ' .
        'Verificá database/consultas/conexion.php y la variable $pdo.'
    );
}

// Configurar PDO.
$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);

$pdo->setAttribute(
    PDO::ATTR_DEFAULT_FETCH_MODE,
    PDO::FETCH_ASSOC
);


// 2. ESCAPAR DATOS PARA MOSTRARLOS EN HTML

function h($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


// 3. OBTENER EL NOMBRE DEL DOCENTE

function portal_user_name(): string
{
    $name = $_SESSION['nombre_usuario']
        ?? $_SESSION['nombre']
        ?? null;

    // Si hay nombre y apellido, formar el nombre completo.
    if (
        !$name &&
        !empty($_SESSION['nombre']) &&
        !empty($_SESSION['apellido'])
    ) {
        $name = trim(
            $_SESSION['nombre'] . ' ' . $_SESSION['apellido']
        );
    }

    return trim((string) ($name ?: 'Docente'));
}


// 4. OBTENER LA CLAVE ÚNICA DEL USUARIO

function portal_user_key(): ?string
{
    global $pdo;

    // Datos disponibles en la sesión.
    $sessionValues = [
        'cedula' => $_SESSION['cedula']
            ?? $_SESSION['ci']
            ?? null,

        'correo' => $_SESSION['correo']
            ?? $_SESSION['email']
            ?? null,

        'nombre_usuario' => $_SESSION['nombre_usuario']
            ?? null,
    ];

    // Resolver la clave canónica consultando la base de datos.
    if (isset($pdo) && $pdo instanceof PDO) {
        try {
            $where = [];
            $params = [];

            // Buscar por cédula.
            if (!empty($sessionValues['cedula'])) {
                $where[] = 'cedula = ?';

                $params[] = trim(
                    (string) $sessionValues['cedula']
                );
            }

            // Buscar por correo.
            if (!empty($sessionValues['correo'])) {
                $where[] = 'correo = ?';

                $params[] = trim(
                    (string) $sessionValues['correo']
                );
            }

            // Buscar por nombre de usuario.
            if (!empty($sessionValues['nombre_usuario'])) {
                $where[] = 'nombre_usuario = ?';

                $params[] = trim(
                    (string) $sessionValues['nombre_usuario']
                );
            }

            // Ejecutar la consulta si existen criterios.
            if ($where) {
                $sql = '
                    SELECT cedula, correo, nombre_usuario
                    FROM usuarios
                    WHERE ' . implode(' OR ', $where) . '
                    LIMIT 1
                ';

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    // Prioridad 1: cédula.
                    if (!empty($row['cedula'])) {
                        return 'cedula:' . mb_strtolower(
                            trim((string) $row['cedula']),
                            'UTF-8'
                        );
                    }

                    // Prioridad 2: correo.
                    if (!empty($row['correo'])) {
                        return 'correo:' . mb_strtolower(
                            trim((string) $row['correo']),
                            'UTF-8'
                        );
                    }

                    // Prioridad 3: nombre de usuario.
                    if (!empty($row['nombre_usuario'])) {
                        return 'usuario:' . mb_strtolower(
                            trim((string) $row['nombre_usuario']),
                            'UTF-8'
                        );
                    }
                }
            }
        } catch (Throwable $e) {
            error_log(
                'Portal Docente: no se pudo resolver la clave ' .
                'de sesión: ' . $e->getMessage()
            );
        }
    }

    // Alternativas si no se pudo consultar la base de datos.
    $fallback = [
        'cedula' => $sessionValues['cedula'],
        'correo' => $sessionValues['correo'],
        'usuario' => $sessionValues['nombre_usuario'],

        'id' => $_SESSION['id_usuario']
            ?? $_SESSION['usuario_id']
            ?? $_SESSION['id']
            ?? null,
    ];

    foreach ($fallback as $prefix => $value) {
        if (
            is_scalar($value) &&
            trim((string) $value) !== ''
        ) {
            return $prefix . ':' . mb_strtolower(
                trim((string) $value),
                'UTF-8'
            );
        }
    }

    return null;
}


// 5. OBTENER LAS INICIALES DEL DOCENTE

function portal_user_initials(): string
{
    $name = trim(portal_user_name());

    $parts = preg_split(
        '/\s+/',
        $name,
        -1,
        PREG_SPLIT_NO_EMPTY
    );

    $initials = '';

    foreach (array_slice($parts ?: [], 0, 2) as $part) {
        $initials .= mb_substr(
            $part,
            0,
            1,
            'UTF-8'
        );
    }

    return $initials ?: 'D';
}


// 6. PROTECCIÓN CSRF

// Generar o recuperar el token de seguridad.
function portal_csrf_token(): string
{
    if (empty($_SESSION['portal_csrf'])) {
        $_SESSION['portal_csrf'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['portal_csrf'];
}


// Verificar el token enviado por el formulario.
function portal_verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals(portal_csrf_token(), $token)
    ) {
        http_response_code(419);

        exit(
            'La sesión del formulario venció. ' .
            'Volvé atrás, recargá la página e intentá nuevamente.'
        );
    }
}


// 7. MENSAJES TEMPORALES DE SESIÓN

// Guardar un mensaje para mostrarlo posteriormente.
function portal_flash(string $type, string $message): void
{
    $_SESSION['portal_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}


// Mostrar el mensaje guardado.
function portal_show_flash(): void
{
    if (empty($_SESSION['portal_flash'])) {
        return;
    }

    $flash = $_SESSION['portal_flash'];

    unset($_SESSION['portal_flash']);

    $class = $flash['type'] === 'success'
        ? 'notice-success'
        : 'notice-error';

    echo '<div class="notice ' . $class . '" role="status">';
    echo h($flash['message']);
    echo '</div>';
}


// 8. VERIFICAR QUE EL USUARIO TENGA UNA CLAVE VÁLIDA

function portal_require_user_key(): string
{
    $key = portal_user_key();

    if ($key === null) {
        portal_flash(
            'error',
            'Para guardar datos personales, iniciá sesión ' .
            'en el portal con tu cuenta docente.'
        );

        header('Location: bienvenidoindex.php');

        exit;
    }

    return $key;
}


// 9. INFORMACIÓN DE CADA PÁGINA DEL PORTAL

function portal_page_info(): array
{
    $map = [
        'bienvenidoindex.php' => [
            'Inicio',
            'house',
            'ESPACIO DE TRABAJO',
        ],

        'calendarioindex.php' => [
            'Calendario docente',
            'calendar-days',
            'AGENDA',
        ],

        'mensajesindex.php' => [
            'Mensajes',
            'envelope',
            'COMUNICACIÓN',
        ],

        'recursosindex.php' => [
            'Recursos docentes',
            'lightbulb',
            'APOYO',
        ],
    ];

    $page = basename($_SERVER['PHP_SELF'] ?? '');

    return $map[$page] ?? [
        'Portal Docente',
        'house',
        'PORTAL DOCENTE',
    ];
}


// 10. CABECERA Y ESTRUCTURA PRINCIPAL DEL PORTAL

function portal_header(
    string $title,
    string $icon,
    string $kicker
): void {
    $page = basename($_SERVER['PHP_SELF'] ?? '');

    $name = portal_user_name();
    $initials = portal_user_initials();

    // Páginas disponibles en el menú lateral.
    $pages = [
        'bienvenidoindex.php' => [
            'Inicio',
            'house',
        ],

        'calendarioindex.php' => [
            'Calendario docente',
            'calendar-days',
        ],

        'mensajesindex.php' => [
            'Mensajes',
            'envelope',
        ],

        'recursosindex.php' => [
            'Recursos docentes',
            'lightbulb',
        ],
    ];


    // 10.1. INICIO DEL DOCUMENTO HTML

    echo '<!DOCTYPE html>';
    echo '<html lang="es">';
    echo '<head>';

    echo '<meta charset="UTF-8">';

    echo '<meta name="viewport" ';
    echo 'content="width=device-width, initial-scale=1.0">';

    echo '<title>INADI | ' . h($title) . '</title>';

    echo '<link rel="stylesheet" href="portalDocente.css">';

    echo '<link rel="stylesheet" ';
    echo 'href="https://cdnjs.cloudflare.com/ajax/libs/';
    echo 'font-awesome/6.5.2/css/all.min.css">';

    echo '</head>';
    echo '<body>';

    echo '<div class="portal-shell">';


    // 10.2. MENÚ LATERAL

    echo '<aside class="sidebar">';

    echo '<div class="brand">';

    echo '<button type="button" ';
    echo 'class="icon-button sidebar-collapse" ';
    echo 'aria-label="Contraer menú" ';
    echo 'aria-expanded="true">';

    echo '<i class="fa-solid fa-angles-left"></i>';

    echo '</button>';

    echo '<a href="../Inicio/inicioindex.php" ';
    echo 'class="brand-logo">';

    echo '<img src="../Elementos Gráficos/';
    echo 'Logo Inadi sin Brillo.png" ';
    echo 'alt="Logo de INADI">';

    echo '</a>';

    echo '<span class="brand-name">INADI</span>';

    echo '<div class="brand-since">PORTAL DOCENTE</div>';

    echo '<div class="brand-address">Salto, Uruguay</div>';

    echo '</div>';


    // 10.3. ENLACES DE NAVEGACIÓN

    echo '<nav class="side-nav" aria-label="Menú docente">';

    foreach ($pages as $file => $data) {
        $active = $file === $page ? ' active' : '';

        echo '<a href="' . h($file) . '" ';
        echo 'class="nav-link' . $active . '"';

        if ($active) {
            echo ' aria-current="page"';
        }

        echo '>';

        echo '<i class="fa-solid fa-' . h($data[1]) . '"></i>';

        echo '<span>' . h($data[0]) . '</span>';

        echo '</a>';
    }

    // Volver al sitio público.
    echo '<a href="../Inicio/inicioindex.php" ';
    echo 'class="nav-link">';

    echo '<i class="fa-solid fa-arrow-left"></i>';
    echo '<span>Volver al inicio</span>';

    echo '</a>';

    echo '</nav>';


    // 10.4. PIE DEL MENÚ LATERAL

    echo '<div class="sidebar-footer">';

    echo 'Instituto de Informática<br>';
    echo 'INADI · Salto';

    echo '</div>';

    echo '</aside>';


    // 10.5. CONTENEDOR PRINCIPAL

    echo '<div class="main-area">';


    // 10.6. BARRA SUPERIOR

    echo '<header class="topbar">';

    echo '<div class="news-strip">';

    echo '<div class="news-label">';
    echo '<i class="fa-solid fa-circle"></i>';
    echo ' PORTAL DOCENTE';
    echo '</div>';

    echo '<div class="news-track">';

    echo '<span>';
    echo '<i class="fa-solid fa-graduation-cap"></i>';
    echo ' Espacio de trabajo docente';
    echo '</span>';

    echo '<span>';
    echo '<i class="fa-solid fa-calendar-check"></i>';
    echo ' Organización académica';
    echo '</span>';

    echo '</div>';

    echo '</div>';


    // 10.7. ACCIONES Y PERFIL DEL DOCENTE

    echo '<div class="top-actions">';

    echo '<a href="calendarioindex.php" class="top-portal">';
    echo '<i class="fa-solid fa-calendar-days"></i>';
    echo ' Mi agenda';
    echo '</a>';

    echo '<div class="user-menu">';

    echo '<div class="user-avatar">';
    echo h($initials);
    echo '</div>';

    echo '<div class="user-data">';

    echo '<strong>' . h($name) . '</strong>';

    echo '<small>';
    echo '<i class="fa-solid fa-circle"></i>';
    echo ' Portal Docente';
    echo '</small>';

    echo '</div>';


    // Botón desplegable del perfil.
    echo '<button type="button" ';
    echo 'class="profile-chevron" ';
    echo 'aria-label="Abrir menú de usuario" ';
    echo 'aria-expanded="false">';

    echo '<i class="fa-solid fa-chevron-down"></i>';

    echo '</button>';


    // Opciones del perfil.
    echo '<div class="profile-dropdown">';

    echo '<a href="bienvenidoindex.php">';
    echo '<i class="fa-solid fa-house"></i>';
    echo ' Inicio docente';
    echo '</a>';

    echo '<a href="recursosindex.php">';
    echo '<i class="fa-solid fa-book-open"></i>';
    echo ' Recursos docentes';
    echo '</a>';

    echo '<a href="../Inicio/inicioindex.php">';
    echo '<i class="fa-solid fa-globe"></i>';
    echo ' Sitio principal';
    echo '</a>';

    echo '</div>';

    echo '</div>';

    echo '</div>';

    echo '</header>';


    // 10.8. CONTENIDO DE LA PÁGINA

    echo '<main class="dashboard-content">';

    echo '<div class="page-title">';

    echo '<span class="section-kicker">';
    echo h($kicker);
    echo '</span>';

    echo '<h1>';
    echo '<i class="fa-solid fa-' . h($icon) . '"></i> ';
    echo h($title);
    echo '</h1>';

    echo '<p>';
    echo 'Portal Docente · Instituto de Informática INADI';
    echo '</p>';

    echo '</div>';

    // Mostrar notificaciones pendientes.
    portal_show_flash();
}


// 11. PIE DE PÁGINA Y CIERRE DEL DOCUMENTO

function portal_footer(): void
{
    echo '<footer class="teacher-footer">';

    echo '<div>';
    echo '<strong>INADI</strong>';
    echo '<span>Portal Docente</span>';
    echo '</div>';

    echo '<p>';
    echo 'Instituto de Informática · Salto, Uruguay';
    echo '</p>';

    echo '<a href="../Inicio/inicioindex.php">';
    echo 'Volver al sitio principal';
    echo '</a>';

    echo '</footer>';

    echo '</main>';
    echo '</div>';
    echo '</div>';

    // Contenedor de notificaciones.
    echo '<div class="toast" role="status" ';
    echo 'aria-live="polite"></div>';

    // JavaScript compartido.
    echo '<script src="portal-docente.js" defer></script>';

    echo '</body>';
    echo '</html>';
}


// 12. MANEJO DE ERRORES DE BASE DE DATOS

function portal_db_error(
    Throwable $e,
    string $friendly
): void {
    error_log(
        'Portal Docente DB: ' . $e->getMessage()
    );

    portal_flash('error', $friendly);
}