
<?php
session_start();

$error = '';
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['host'] ?? 'localhost');
    $db = trim($_POST['db'] ?? 'insoft');
    $dbuser = trim($_POST['dbuser'] ?? 'root');
    $dbpass = $_POST['dbpass'] ?? '';
    $admin = trim($_POST['admin'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $db)) {
            throw new RuntimeException(
                'El nombre de la base solo puede contener letras, números y guion bajo.'
            );
        }

        if (
            $host === '' ||
            $dbuser === '' ||
            $admin === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($password) < 10
        ) {
            throw new RuntimeException(
                'Ingresá el servidor, usuario MySQL, nombre, correo válido y una contraseña de al menos 10 caracteres.'
            );
        }

        // Conexión a la base de datos existente.
        $pdo = new PDO(
            "mysql:host={$host};dbname={$db};charset=utf8mb4",
            $dbuser,
            $dbpass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        // Tabla de administradores.
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
              DEFAULT CHARSET=utf8mb4
              COLLATE=utf8mb4_unicode_ci
        ");

        // Tabla de contenidos administrables.
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                module VARCHAR(60) NOT NULL,
                title VARCHAR(190) NOT NULL,
                description TEXT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'Publicado',
                link_url VARCHAR(500) NULL,
                image_url VARCHAR(500) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_module (module)
            ) ENGINE=InnoDB
              DEFAULT CHARSET=utf8mb4
              COLLATE=utf8mb4_unicode_ci
        ");

        // Evitar duplicar el administrador si ya existe su correo.
        $check = $pdo->prepare(
            'SELECT id FROM admin_users WHERE email = ? LIMIT 1'
        );
        $check->execute([$email]);

        if ($check->fetch()) {
            throw new RuntimeException(
                'Ya existe un administrador registrado con ese correo. Probá con otro correo o ingresá desde login.php.'
            );
        }

        // Registrar al administrador con contraseña cifrada mediante hash.
        $stmt = $pdo->prepare("
            INSERT INTO admin_users (name, email, password_hash)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $admin,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);

        // Guardar la configuración de conexión.
        $config = "<?php\nreturn " . var_export([
            'host' => $host,
            'db'   => $db,
            'user' => $dbuser,
            'pass' => $dbpass
        ], true) . ";\n";

        if (
            file_put_contents(
                __DIR__ . '/config.php',
                $config,
                LOCK_EX
            ) === false
        ) {
            throw new RuntimeException(
                'No se pudo crear config.php. Revisá los permisos de la carpeta.'
            );
        }

        $done = true;

    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Instalación | INADI Administración</title>

    <link rel="stylesheet" href="assets/admin.css">

    <style>
        :root {
            --primary: #45459c;
            --primary-dark: #36367f;
            --primary-deep: #292965;
            --accent: #f5a623;
        }

        * {
            box-sizing: border-box;
        }

        body.login-body {
            min-height: 100vh;
            margin: 0;
            padding: 35px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(102, 102, 190, .20),
                    transparent 38%
                ),
                linear-gradient(135deg, #f1f2fb, #e5e8f5);
        }

        .setup-card {
            width: 100%;
            max-width: 650px;
            padding: 35px;
            background: #fff;
            border: 1px solid rgba(69, 69, 156, .12);
            border-radius: 22px;
            box-shadow: 0 20px 60px rgba(41, 41, 101, .13);
        }

        .setup-header {
            margin-bottom: 28px;
            text-align: center;
        }

        .setup-logo {
            display: block;
            width: 145px;
            height: 110px;
            margin: 0 auto 15px;
            object-fit: contain;
        }

        .eyebrow {
            margin: 0 0 9px;
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .setup-card h1 {
            margin: 0;
            color: var(--primary-deep);
            font-size: 27px;
            text-align: center;
        }

        .muted {
            margin: 12px 0 0;
            color: #77778b;
            font-size: 14px;
            line-height: 1.6;
            text-align: center;
        }

        .section-title {
            margin: 25px 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ececf6;
            color: var(--primary);
            font-size: 14px;
            font-weight: 800;
        }

        .setup-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 17px;
        }

        .setup-form label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            color: #35354e;
            font-size: 13px;
            font-weight: 700;
        }

        .setup-form input {
            width: 100%;
            min-height: 44px;
            padding: 11px 13px;
            border: 1px solid #dcdced;
            border-radius: 9px;
            background: #fafaff;
            color: #292945;
            font: inherit;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .setup-form input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(69, 69, 156, .12);
        }

        .field-full {
            grid-column: 1 / -1;
        }

        .setup-card .btn.primary {
            width: 100%;
            min-height: 48px;
            margin-top: 25px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(
                135deg,
                var(--primary),
                var(--primary-deep)
            );
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
        }

        .setup-card .btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(69, 69, 156, .23);
        }

        .notice {
            margin: 20px 0;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }

        .notice.error {
            border: 1px solid #f0c3c3;
            background: #fff0f0;
            color: #a52828;
        }

        .notice.success {
            border: 1px solid #b9e5ce;
            background: #effbf4;
            color: #176b3a;
            text-align: center;
        }

        .notice a {
            display: inline-block;
            margin-top: 8px;
            color: var(--primary);
            font-weight: 800;
        }

        .setup-footer {
            margin: 25px 0 0;
            padding-top: 18px;
            border-top: 1px solid #eeeeF6;
            color: #88889b;
            font-size: 12px;
            text-align: center;
        }

        .security-note {
            margin-top: 15px;
            padding: 12px;
            border-radius: 9px;
            background: #fff8e9;
            color: #79571a;
            font-size: 12px;
            line-height: 1.6;
        }

        @media (max-width: 560px) {
            .setup-card {
                padding: 25px 20px;
            }

            .setup-form {
                grid-template-columns: 1fr;
            }

            .field-full {
                grid-column: auto;
            }

            .setup-card h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body class="login-body">

    <main class="setup-card">

        <header class="setup-header">
            <img
                class="setup-logo"
                src="../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png"
                alt="Logo institucional de INADI"
                onerror="this.style.display='none';"
            >

            <p class="eyebrow">CONFIGURACIÓN INICIAL</p>

            <h1>Configurar INADI Admin</h1>

            <p class="muted">
                Conectá el portal con MySQL y creá tu cuenta de administrador.
            </p>
        </header>

        <?php if ($done): ?>

            <div class="notice success" role="status">
                <strong>¡Instalación completada!</strong>
                <p>La configuración y el usuario administrador fueron creados.</p>
                <a href="login.php">Ingresar al portal administrativo</a>
            </div>

            <div class="security-note">
                Por seguridad, eliminá o restringí el acceso a
                <strong>setup.php</strong> después de verificar que podés ingresar.
            </div>

        <?php else: ?>

            <?php if ($error !== ''): ?>
                <div class="notice error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post" class="setup-form">

                <div class="field-full">
                    <h2 class="section-title">01 · Conexión a la base de datos</h2>
                </div>

                <label>
                    Servidor MySQL
                    <input
                        type="text"
                        name="host"
                        value="<?= htmlspecialchars($_POST['host'] ?? 'localhost', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </label>

                <label>
                    Nombre de la base de datos
                    <input
                        type="text"
                        name="db"
                        value="<?= htmlspecialchars($_POST['db'] ?? 'insoft', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </label>

                <label>
                    Usuario MySQL
                    <input
                        type="text"
                        name="dbuser"
                        value="<?= htmlspecialchars($_POST['dbuser'] ?? 'root', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </label>

                <label>
                    Contraseña MySQL
                    <input
                        type="password"
                        name="dbpass"
                        autocomplete="new-password"
                    >
                </label>

                <div class="field-full">
                    <h2 class="section-title">02 · Crear cuenta de administrador</h2>
                </div>

                <label class="field-full">
                    Nombre completo
                    <input
                        type="text"
                        name="admin"
                        value="<?= htmlspecialchars($_POST['admin'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="120"
                        autocomplete="name"
                        required
                    >
                </label>

                <label class="field-full">
                    Correo electrónico
                    <input
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="190"
                        autocomplete="email"
                        required
                    >
                </label>

                <label class="field-full">
                    Contraseña del administrador
                    <input
                        type="password"
                        name="password"
                        minlength="10"
                        autocomplete="new-password"
                        required
                    >
                    <small>Debe tener al menos 10 caracteres.</small>
                </label>

                <div class="field-full">
                    <button type="submit" class="btn primary full">
                        Instalar portal administrativo
                    </button>
                </div>

            </form>

            <div class="security-note">
                <strong>Importante:</strong> usá una contraseña segura.
                Este instalador necesita permiso para crear tablas en la base
                seleccionada.
            </div>

        <?php endif; ?>

        <footer class="setup-footer">
            Instituto de capacitación · INADI
        </footer>

    </main>

</body>
</html>
