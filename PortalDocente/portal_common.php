<?php
// Funciones compartidas del Portal Docente INADI.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$portalConnectionFile = dirname(__DIR__) . '/database/consultas/conexion.php';
if (is_file($portalConnectionFile)) {
    require_once $portalConnectionFile;
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log('Portal Docente: conexion.php no definió una conexión PDO en $pdo.');
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Verificá database/consultas/conexion.php y la variable $pdo.');
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function portal_user_name(): string {
    $name = $_SESSION['nombre_usuario'] ?? $_SESSION['nombre'] ?? null;
    if (!$name && !empty($_SESSION['nombre']) && !empty($_SESSION['apellido'])) {
        $name = trim($_SESSION['nombre'] . ' ' . $_SESSION['apellido']);
    }
    return trim((string)($name ?: 'Docente'));
}
function portal_user_key(): ?string {
    global $pdo;
    // Si la sesión solo contiene nombre_usuario, resolver la clave canónica
    // desde usuarios para que coincida con la clave usada al elegir destinatarios.
    $sessionValues = [
        'cedula' => $_SESSION['cedula'] ?? $_SESSION['ci'] ?? null,
        'correo' => $_SESSION['correo'] ?? $_SESSION['email'] ?? null,
        'nombre_usuario' => $_SESSION['nombre_usuario'] ?? null,
    ];
    if (isset($pdo) && $pdo instanceof PDO) {
        try {
            $where = []; $params = [];
            if (!empty($sessionValues['cedula'])) { $where[] = 'cedula = ?'; $params[] = trim((string)$sessionValues['cedula']); }
            if (!empty($sessionValues['correo'])) { $where[] = 'correo = ?'; $params[] = trim((string)$sessionValues['correo']); }
            if (!empty($sessionValues['nombre_usuario'])) { $where[] = 'nombre_usuario = ?'; $params[] = trim((string)$sessionValues['nombre_usuario']); }
            if ($where) {
                $stmt = $pdo->prepare('SELECT cedula, correo, nombre_usuario FROM usuarios WHERE ' . implode(' OR ', $where) . ' LIMIT 1');
                $stmt->execute($params); $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    if (!empty($row['cedula'])) return 'cedula:' . mb_strtolower(trim((string)$row['cedula']), 'UTF-8');
                    if (!empty($row['correo'])) return 'correo:' . mb_strtolower(trim((string)$row['correo']), 'UTF-8');
                    if (!empty($row['nombre_usuario'])) return 'usuario:' . mb_strtolower(trim((string)$row['nombre_usuario']), 'UTF-8');
                }
            }
        } catch (Throwable $e) { error_log('Portal Docente: no se pudo resolver la clave de sesión: ' . $e->getMessage()); }
    }
    $fallback = [
        'cedula' => $sessionValues['cedula'],
        'correo' => $sessionValues['correo'],
        'usuario' => $sessionValues['nombre_usuario'],
        'id' => $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null,
    ];
    foreach ($fallback as $prefix => $value) {
        if (is_scalar($value) && trim((string)$value) !== '') return $prefix . ':' . mb_strtolower(trim((string)$value), 'UTF-8');
    }
    return null;
}
function portal_user_initials(): string {
    $parts = preg_split('/\s+/', trim(portal_user_name()), -1, PREG_SPLIT_NO_EMPTY);
    $initials = '';
    foreach (array_slice($parts ?: [], 0, 2) as $part) $initials .= mb_substr($part, 0, 1, 'UTF-8');
    return $initials ?: 'D';
}
function portal_csrf_token(): string {
    if (empty($_SESSION['portal_csrf'])) $_SESSION['portal_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['portal_csrf'];
}
function portal_verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(portal_csrf_token(), $token)) {
        http_response_code(419);
        exit('La sesión del formulario venció. Volvé atrás, recargá la página e intentá nuevamente.');
    }
}
function portal_flash(string $type, string $message): void {
    $_SESSION['portal_flash'] = ['type' => $type, 'message' => $message];
}
function portal_show_flash(): void {
    if (empty($_SESSION['portal_flash'])) return;
    $flash = $_SESSION['portal_flash']; unset($_SESSION['portal_flash']);
    $class = $flash['type'] === 'success' ? 'notice-success' : 'notice-error';
    echo '<div class="notice ' . $class . '" role="status">' . h($flash['message']) . '</div>';
}
function portal_require_user_key(): string {
    $key = portal_user_key();
    if ($key === null) {
        portal_flash('error', 'Para guardar datos personales, iniciá sesión en el portal con tu cuenta docente.');
        header('Location: bienvenidoindex.php');
        exit;
    }
    return $key;
}
function portal_page_info(): array {
    $map = [
        'bienvenidoindex.php' => ['Inicio', 'house', 'ESPACIO DE TRABAJO'],
        'calendarioindex.php' => ['Calendario docente', 'calendar-days', 'AGENDA'],
        'mensajesindex.php' => ['Mensajes', 'envelope', 'COMUNICACIÓN'],
        'recursosindex.php' => ['Recursos docentes', 'lightbulb', 'APOYO'],
    ];
    return $map[basename($_SERVER['PHP_SELF'] ?? '')] ?? ['Portal Docente', 'house', 'PORTAL DOCENTE'];
}
function portal_header(string $title, string $icon, string $kicker): void {
    $page = basename($_SERVER['PHP_SELF'] ?? '');
    $name = portal_user_name();
    $initials = portal_user_initials();
    $pages = [
        'bienvenidoindex.php' => ['Inicio', 'house'],
        'calendarioindex.php' => ['Calendario docente', 'calendar-days'],
        'mensajesindex.php' => ['Mensajes', 'envelope'],
        'recursosindex.php' => ['Recursos docentes', 'lightbulb'],
    ];
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>INADI | ' . h($title) . '</title><link rel="stylesheet" href="portalDocente.css">';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"></head><body><div class="portal-shell">';
    echo '<aside class="sidebar"><div class="brand"><button type="button" class="icon-button sidebar-collapse" aria-label="Contraer menú" aria-expanded="true"><i class="fa-solid fa-angles-left"></i></button>';
    echo '<a href="../Inicio/inicioindex.php" class="brand-logo"><img src="../Elementos Gráficos/Logo Inadi sin Brillo.png" alt="Logo de INADI"></a><span class="brand-name">INADI</span><div class="brand-since">PORTAL DOCENTE</div><div class="brand-address">Salto, Uruguay</div></div>';
    echo '<nav class="side-nav" aria-label="Menú docente">';
    foreach ($pages as $file => $data) {
        $active = $file === $page ? ' active' : '';
        echo '<a href="' . h($file) . '" class="nav-link' . $active . '"' . ($active ? ' aria-current="page"' : '') . '><i class="fa-solid fa-' . h($data[1]) . '"></i><span>' . h($data[0]) . '</span></a>';
    }
    echo '<a href="../Inicio/inicioindex.php" class="nav-link"><i class="fa-solid fa-arrow-left"></i><span>Volver al inicio</span></a></nav>';
    echo '<div class="sidebar-footer">Instituto de Informática<br>INADI · Salto</div></aside><div class="main-area">';
    echo '<header class="topbar"><div class="news-strip"><div class="news-label"><i class="fa-solid fa-circle"></i> PORTAL DOCENTE</div><div class="news-track"><span><i class="fa-solid fa-graduation-cap"></i> Espacio de trabajo docente</span><span><i class="fa-solid fa-calendar-check"></i> Organización académica</span></div></div>';
    echo '<div class="top-actions"><a href="calendarioindex.php" class="top-portal"><i class="fa-solid fa-calendar-days"></i> Mi agenda</a><div class="user-menu"><div class="user-avatar">' . h($initials) . '</div><div class="user-data"><strong>' . h($name) . '</strong><small><i class="fa-solid fa-circle"></i> Portal Docente</small></div><button type="button" class="profile-chevron" aria-label="Abrir menú de usuario" aria-expanded="false"><i class="fa-solid fa-chevron-down"></i></button><div class="profile-dropdown"><a href="bienvenidoindex.php"><i class="fa-solid fa-house"></i> Inicio docente</a><a href="recursosindex.php"><i class="fa-solid fa-book-open"></i> Recursos docentes</a><a href="../Inicio/inicioindex.php"><i class="fa-solid fa-globe"></i> Sitio principal</a></div></div></div></header>';
    echo '<main class="dashboard-content"><div class="page-title"><span class="section-kicker">' . h($kicker) . '</span><h1><i class="fa-solid fa-' . h($icon) . '"></i> ' . h($title) . '</h1><p>Portal Docente · Instituto de Informática INADI</p></div>';
    portal_show_flash();
}
function portal_footer(): void {
    echo '<footer class="teacher-footer"><div><strong>INADI</strong><span>Portal Docente</span></div><p>Instituto de Informática · Salto, Uruguay</p><a href="../Inicio/inicioindex.php">Volver al sitio principal</a></footer></main></div></div><div class="toast" role="status" aria-live="polite"></div><script src="portal-docente.js" defer></script></body></html>';
}
function portal_db_error(Throwable $e, string $friendly): void {
    error_log('Portal Docente DB: ' . $e->getMessage());
    portal_flash('error', $friendly);
}
