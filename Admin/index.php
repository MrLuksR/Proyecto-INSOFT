
<?php

session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if (!file_exists(__DIR__ . '/config.php')) {
    header('Location: setup.php');
    exit;
}

$cfg = require __DIR__ . '/config.php';

/* LOGO INSTITUCIONAL */
$logoUrl = '../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png';

try {
    $pdo = new PDO(
        "mysql:host={$cfg['host']};dbname={$cfg['db']};charset=utf8mb4",
        $cfg['user'],
        $cfg['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (Throwable $e) {
    http_response_code(500);
    die('No se pudo conectar a MySQL. Revisá config.php y el estado de MySQL.');
}

$modules = [
    'dashboard'   => ['Inicio', '▦', 'Resumen general'],
    'cursos'      => ['Cursos', '▤', 'Administrá la oferta educativa'],
    'noticias'    => ['Noticias', '▣', 'Novedades y publicaciones'],
    'banners'     => ['Banners', '▧', 'Imágenes principales del sitio'],
    'galeria'     => ['Galería de fotos', '▧', 'Imágenes institucionales'],
    'empresas'    => ['Empresas que confían', '♧', 'Convenios y organizaciones'],
    'comentarios' => ['Comentarios', '☷', 'Mensajes y comentarios recibidos'],
    'formularios' => ['Formularios', '▤', 'Consultas y formularios recibidos'],
    'slides'      => ['Slides', '▱', 'Carruseles y presentaciones'],
    'usuarios'    => ['Usuarios', '♙', 'Accesos del portal'],
    'ajustes'     => ['Ajustes', '⚙', 'Configuración administrativa'],
    'perfil'      => ['Mi perfil', '♙', 'Datos de tu cuenta']
];

$page = $_GET['page'] ?? 'dashboard';

if (!isset($modules[$page])) {
    $page = 'dashboard';
}

function h(mixed $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function go(string $page): never
{
    header('Location: index.php?page=' . urlencode($page));
    exit;
}

$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $module = $_POST['module'] ?? $page;

    $editableModules = [
        'cursos', 'noticias', 'banners', 'galeria',
        'empresas', 'comentarios', 'formularios', 'slides'
    ];

    if (!in_array($module, array_merge($editableModules, ['usuarios']), true)) {
        $module = $page;
    }

    try {
        if ($action === 'save_item') {
            if (!in_array($module, $editableModules, true)) {
                throw new RuntimeException('Módulo no válido.');
            }

            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');

            if ($title === '') {
                throw new RuntimeException('El título es obligatorio.');
            }

            $data = [
                $module,
                $title,
                trim($_POST['description'] ?? ''),
                trim($_POST['status'] ?? 'Publicado'),
                trim($_POST['link_url'] ?? ''),
                trim($_POST['image_url'] ?? '')
            ];

            if ($id > 0) {
                $data[] = $id;
                $data[] = $module;

                $stmt = $pdo->prepare(
                    'UPDATE admin_items
                     SET module=?, title=?, description=?, status=?,
                         link_url=?, image_url=?
                     WHERE id=? AND module=?'
                );
                $stmt->execute($data);
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO admin_items
                     (module, title, description, status, link_url, image_url)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute($data);
            }

            $_SESSION['flash'] = 'Registro guardado correctamente.';
            go($module);
        }

        if ($action === 'delete_item') {
            if (!in_array($module, $editableModules, true)) {
                throw new RuntimeException('Módulo no válido.');
            }

            $stmt = $pdo->prepare(
                'DELETE FROM admin_items WHERE id=? AND module=?'
            );
            $stmt->execute([(int)($_POST['id'] ?? 0), $module]);

            $_SESSION['flash'] = 'Registro eliminado.';
            go($module);
        }

        if ($action === 'update_profile') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Completá nombre y correo válido.');
            }

            $stmt = $pdo->prepare(
                'UPDATE admin_users SET name=?, email=? WHERE id=?'
            );
            $stmt->execute([$name, $email, $_SESSION['admin_id']]);

            $_SESSION['admin_name'] = $name;
            $_SESSION['flash'] = 'Perfil actualizado.';
            go('perfil');
        }

        if ($action === 'change_password') {
            $old = $_POST['old_password'] ?? '';
            $new = $_POST['new_password'] ?? '';

            $stmt = $pdo->prepare(
                'SELECT password_hash FROM admin_users WHERE id=?'
            );
            $stmt->execute([$_SESSION['admin_id']]);
            $u = $stmt->fetch();

            if (!$u || !password_verify($old, $u['password_hash'])) {
                throw new RuntimeException('La contraseña actual no coincide.');
            }

            if (strlen($new) < 10) {
                throw new RuntimeException(
                    'La nueva contraseña debe tener al menos 10 caracteres.'
                );
            }

            $stmt = $pdo->prepare(
                'UPDATE admin_users SET password_hash=? WHERE id=?'
            );
            $stmt->execute([
                password_hash($new, PASSWORD_DEFAULT),
                $_SESSION['admin_id']
            ]);

            $_SESSION['flash'] = 'Contraseña actualizada.';
            go('perfil');
        }
    } catch (Throwable $e) {
        $flash = $e->getMessage();
    }
}

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

/* CONTADORES */
$counts = [];

foreach (
    ['cursos', 'noticias', 'banners', 'galeria',
     'empresas', 'comentarios', 'formularios', 'slides'] as $m
) {
    $s = $pdo->prepare(
        'SELECT COUNT(*) FROM admin_items WHERE module=?'
    );
    $s->execute([$m]);
    $counts[$m] = (int)$s->fetchColumn();
}

/* REGISTRO EN EDICIÓN */
$editing = null;

if (
    isset($_GET['edit']) &&
    in_array($page, [
        'cursos', 'noticias', 'banners', 'galeria',
        'empresas', 'comentarios', 'formularios', 'slides'
    ], true)
) {
    $s = $pdo->prepare(
        'SELECT * FROM admin_items WHERE id=? AND module=?'
    );
    $s->execute([(int)$_GET['edit'], $page]);
    $editing = $s->fetch() ?: null;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#45459c">

    <title><?= h($modules[$page][0]) ?> | INADI Admin</title>

    <link rel="stylesheet" href="assets/admin.css">
</head>

<body class="admin-body">

<header class="topbar">
    <a class="top-brand" href="index.php?page=dashboard">
        <img
            src="<?= h($logoUrl) ?>"
            alt="Logo institucional de INADI"
            class="admin-logo"
        >
        <span class="top-divider">/</span>
        <span class="brand-description">
            <b>INADI</b>
            <small>Panel administrativo</small>
        </span>
    </a>

    <div class="top-actions">
        <a href="index.php?page=dashboard">
            ⌂ <span>Ver escritorio</span>
        </a>

        <span class="admin-chip">
            <?= h($_SESSION['admin_name'] ?? 'Administrador') ?>
        </span>

        <a class="logout" href="logout.php">Salir ↗</a>
    </div>
</header>

<aside class="sidebar">

    <div class="sidebar-brand">
        <a
            href="index.php?page=dashboard"
            class="sidebar-logo-link"
            aria-label="Ir al inicio del panel"
        >
            <img
                src="<?= h($logoUrl) ?>"
                alt="Logo de INADI"
                class="sidebar-logo"
            >
        </a>

        <div class="sidebar-brand-text">
            <strong>INADI</strong>
            <span>Gestión institucional</span>
        </div>
    </div>

    <div class="side-caption">MENÚ PRINCIPAL</div>

    <?php foreach ($modules as $key => $item): ?>
        <?php if ($key === 'perfil'): ?>
            <div class="side-caption side-spacer">CUENTA</div>
        <?php endif; ?>

        <a
            class="nav-link <?= $page === $key ? 'active' : '' ?>"
            href="index.php?page=<?= h($key) ?>"
        >
            <span class="nav-icon"><?= h($item[1]) ?></span>
            <span><?= h($item[0]) ?></span>

            <?php if (($counts[$key] ?? 0) > 0): ?>
                <small><?= h($counts[$key]) ?></small>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>

    <div class="sidebar-bottom">
        <div class="online-dot"></div>
        <div>
            <b>Portal INADI</b>
            <small>Panel administrativo</small>
        </div>
    </div>
</aside>

<main class="main-content">

    <div class="page-heading">
        <div>
            <p class="eyebrow">INADI / ADMINISTRACIÓN</p>
            <h1><?= h($modules[$page][0]) ?></h1>
            <p class="muted"><?= h($modules[$page][2]) ?></p>
        </div>

        <div class="heading-date"><?= date('d/m/Y') ?></div>
    </div>

    <?php if ($flash): ?>
        <div class="notice <?= (
            strpos($flash, 'obligatorio') !== false ||
            strpos($flash, 'coincide') !== false ||
            strpos($flash, 'debe tener') !== false
        ) ? 'error' : 'success' ?>">
            <?= h($flash) ?>
        </div>
    <?php endif; ?>

    <?php if ($page === 'dashboard'): ?>

        <section class="welcome-panel">
            <div class="welcome-content">
                <span class="welcome-kicker">ESCRITORIO INSTITUCIONAL</span>

                <h2>¡Bienvenido/a al portal de INADI!</h2>

                <p>
                    Desde aquí podés organizar el contenido del sitio
                    y consultar los módulos administrativos.
                </p>

                <a class="btn white" href="index.php?page=cursos">
                    Administrar cursos 
                </a>
            </div>

            <div class="welcome-art">
                <img
                    src="<?= h($logoUrl) ?>"
                    alt="Logo institucional de INADI"
                    class="welcome-logo"
                >
            </div>
        </section>

        <div class="section-title">
            <div>
                <h2>Resumen del sitio</h2>
                <p class="muted">Registros cargados en el portal administrativo</p>
            </div>

            <span class="status-pill"><i></i> Sistema activo</span>
        </div>

        <div class="stats-grid">
            <?php foreach ([
                'cursos' => 'Cursos',
                'noticias' => 'Noticias',
                'banners' => 'Banners',
                'galeria' => 'Fotos de galería'
            ] as $m => $label): ?>
                <a class="stat-card" href="index.php?page=<?= h($m) ?>">
                    <div class="stat-top">
                        <span><?= h($label) ?></span>
                        <b><?= h($modules[$m][1]) ?></b>
                    </div>
                    <strong><?= number_format($counts[$m] ?? 0) ?></strong>
                    <small>Ver y administrar <span>↗</span></small>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="dashboard-columns">

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h3>Accesos rápidos</h3>
                        <p>Ir directamente a una tarea</p>
                    </div>
                </div>

                <div class="quick-grid">
                    <?php foreach ([
                        'cursos', 'noticias', 'banners',
                        'empresas', 'galeria', 'formularios'
                    ] as $m): ?>
                        <a href="index.php?page=<?= h($m) ?>" class="quick-link">
                            <span><?= h($modules[$m][1]) ?></span>
                            <div>
                                <b><?= h($modules[$m][0]) ?></b>
                                <small><?= h($modules[$m][2]) ?></small>
                            </div>
                            
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h3>Actividad reciente</h3>
                        <p>Últimos registros creados</p>
                    </div>
                </div>

                <?php
                $recent = $pdo->query(
                    'SELECT module, title, created_at
                     FROM admin_items ORDER BY id DESC LIMIT 6'
                )->fetchAll();
                ?>

                <?php if (!$recent): ?>
                    <div class="empty-state">
                        <div>▤</div>
                        <b>Todo listo para comenzar</b>
                        <p>Cuando agregues registros, aparecerán aquí.</p>
                        <a href="index.php?page=cursos">Crear primer registro →</a>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recent as $r): ?>
                            <div class="activity-row">
                                <span class="activity-dot"></span>
                                <div>
                                    <b><?= h($r['title']) ?></b>
                                    <small>
                                        <?= h($modules[$r['module']][0] ?? $r['module']) ?>
                                        · <?= h($r['created_at']) ?>
                                    </small>
                                </div>
                                <a href="index.php?page=<?= h($r['module']) ?>">Ver</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        </div>

    <?php elseif ($page === 'perfil'): ?>

        <?php
        $s = $pdo->prepare(
            'SELECT name, email FROM admin_users WHERE id=?'
        );
        $s->execute([$_SESSION['admin_id']]);
        $profile = $s->fetch() ?: ['name' => '', 'email' => ''];
        ?>

        <div class="form-grid">

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h3>Datos de la cuenta</h3>
                        <p>Actualizá tu nombre y correo</p>
                    </div>
                </div>

                <form method="post" class="admin-form">
                    <input type="hidden" name="action" value="update_profile">

                    <label>
                        Nombre completo
                        <input name="name" value="<?= h($profile['name']) ?>" required>
                    </label>

                    <label>
                        Correo electrónico
                        <input type="email" name="email" value="<?= h($profile['email']) ?>" required>
                    </label>

                    <button class="btn primary">Guardar cambios</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h3>Cambiar contraseña</h3>
                        <p>Usá una contraseña robusta de 10 caracteres o más</p>
                    </div>
                </div>

                <form method="post" class="admin-form">
                    <input type="hidden" name="action" value="change_password">

                    <label>
                        Contraseña actual
                        <input type="password" name="old_password" required>
                    </label>

                    <label>
                        Nueva contraseña
                        <input type="password" name="new_password" minlength="10" required>
                    </label>

                    <button class="btn primary">Actualizar contraseña</button>
                </form>
            </section>

        </div>

    <?php elseif ($page === 'ajustes'): ?>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>Configuración del portal</h3>
                    <p>Información y recomendaciones para mantener el sitio operativo</p>
                </div>
            </div>

            <div class="settings-list">
                <div>
                    <span class="setting-icon">✓</span>
                    <div>
                        <b>Conexión a base de datos</b>
                        <p>Conectado a la base configurada en config.php.</p>
                    </div>
                    <span class="status-pill"><i></i> Conectado</span>
                </div>

                <div>
                    <span class="setting-icon">⌘</span>
                    <div>
                        <b>Gestión de contenido</b>
                        <p>Los registros de este panel se almacenan en admin_items.</p>
                    </div>
                </div>

                <div>
                    <span class="setting-icon">♙</span>
                    <div>
                        <b>Seguridad</b>
                        <p>El acceso requiere inicio de sesión y las contraseñas se almacenan con hash.</p>
                    </div>
                </div>
            </div>
        </section>

    <?php elseif ($page === 'usuarios'): ?>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h3>Usuarios administradores</h3>
                    <p>Personas con acceso al portal</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Nombre</th><th>Correo</th><th>Creado</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (
                            $pdo->query(
                                'SELECT name, email, created_at
                                 FROM admin_users ORDER BY id'
                            ) as $u
                        ): ?>
                            <tr>
                                <td><b><?= h($u['name']) ?></b></td>
                                <td><?= h($u['email']) ?></td>
                                <td><?= h($u['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="helper">
                Los usuarios se crean inicialmente desde setup.php.
            </p>
        </section>

    <?php else: ?>

        <div class="module-layout">

            <section class="panel editor-panel">
                <div class="panel-head">
                    <div>
                        <h3><?= $editing ? 'Editar registro' : 'Agregar nuevo registro' ?></h3>
                        <p>Completá los datos y guardá los cambios</p>
                    </div>

                    <?php if ($editing): ?>
                        <a class="subtle-link" href="index.php?page=<?= h($page) ?>">
                            Cancelar edición
                        </a>
                    <?php endif; ?>
                </div>

                <form method="post" class="admin-form">
                    <input type="hidden" name="action" value="save_item">
                    <input type="hidden" name="module" value="<?= h($page) ?>">
                    <input type="hidden" name="id" value="<?= h($editing['id'] ?? 0) ?>">

                    <label>
                        Título / nombre
                        <input
                            name="title"
                            maxlength="190"
                            value="<?= h($editing['title'] ?? '') ?>"
                            required
                            placeholder="Ej.: Curso de Diseño Web"
                        >
                    </label>

                    <label>
                        Descripción
                        <textarea name="description" rows="4" placeholder="Descripción del contenido..."><?= h($editing['description'] ?? '') ?></textarea>
                    </label>

                    <div class="two-fields">
                        <label>
                            Estado
                            <select name="status">
                                <?php foreach (['Publicado', 'Borrador', 'Pendiente', 'Archivado'] as $st): ?>
                                    <option <?= ($editing['status'] ?? 'Publicado') === $st ? 'selected' : '' ?>>
                                        <?= h($st) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label>
                            Enlace URL
                            <input
                                name="link_url"
                                type="url"
                                value="<?= h($editing['link_url'] ?? '') ?>"
                                placeholder="https://..."
                            >
                        </label>
                    </div>

                    <label>
                        URL de imagen (opcional)
                        <input
                            name="image_url"
                            type="url"
                            value="<?= h($editing['image_url'] ?? '') ?>"
                            placeholder="https://..."
                        >
                    </label>

                    <button class="btn primary">
                        <?= $editing ? 'Guardar edición' : '＋ Guardar registro' ?>
                    </button>
                </form>
            </section>

            <section class="panel list-panel">
                <div class="panel-head">
                    <div>
                        <h3><?= h($modules[$page][0]) ?> cargados</h3>
                        <?php
                        $countStmt = $pdo->prepare(
                            'SELECT COUNT(*) FROM admin_items WHERE module=?'
                        );
                        $countStmt->execute([$page]);
                        ?>
                        <p><?= (int)$countStmt->fetchColumn() ?> registros en este módulo</p>
                    </div>
                </div>

                <form class="search-bar" method="get">
                    <input type="hidden" name="page" value="<?= h($page) ?>">
                    <input
                        name="q"
                        value="<?= h($_GET['q'] ?? '') ?>"
                        placeholder="Buscar por título..."
                    >
                    <button class="btn secondary">Buscar</button>

                    <?php if (!empty($_GET['q'])): ?>
                        <a href="index.php?page=<?= h($page) ?>" class="subtle-link">Limpiar</a>
                    <?php endif; ?>
                </form>

                <?php
                $q = trim($_GET['q'] ?? '');

                if ($q !== '') {
                    $s = $pdo->prepare(
                        'SELECT * FROM admin_items
                         WHERE module=? AND title LIKE ?
                         ORDER BY id DESC'
                    );
                    $s->execute([$page, '%' . $q . '%']);
                } else {
                    $s = $pdo->prepare(
                        'SELECT * FROM admin_items WHERE module=? ORDER BY id DESC'
                    );
                    $s->execute([$page]);
                }

                $items = $s->fetchAll();
                ?>

                <?php if (!$items): ?>
                    <div class="empty-state">
                        <div><?= h($modules[$page][1]) ?></div>
                        <b>No hay registros todavía</b>
                        <p>Usá el formulario para crear el primero.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr><th>Contenido</th><th>Estado</th><th>Acciones</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td>
                                            <b><?= h($it['title']) ?></b>

                                            <?php if ($it['description']): ?>
                                                <small class="cell-description">
                                                    <?= h(mb_strimwidth($it['description'], 0, 90, '…', 'UTF-8')) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <span class="state <?= strtolower($it['status']) === 'publicado' ? 'published' : 'draft' ?>">
                                                <?= h($it['status']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <div class="row-actions">
                                                <a href="index.php?page=<?= h($page) ?>&edit=<?= (int)$it['id'] ?>">
                                                    Editar
                                                </a>

                                                <form method="post" onsubmit="return confirm('¿Eliminar este registro?');">
                                                    <input type="hidden" name="action" value="delete_item">
                                                    <input type="hidden" name="module" value="<?= h($page) ?>">
                                                    <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                                                    <button class="text-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

        </div>

    <?php endif; ?>

    <footer class="footer">
        <span>© <?= date('Y') ?> INADI · Portal administrativo</span>
        <span>Interfaz azul institucional</span>
    </footer>

</main>

</body>
</html>
