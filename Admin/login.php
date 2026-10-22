
<?php
session_start();

// Evitar que el navegador almacene la página de inicio de sesión.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Si el administrador ya inició sesión, redirigir al panel.
if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// Procesar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar que exista el archivo de configuración.
        $configPath = __DIR__ . '/config.php';

        if (!file_exists($configPath)) {
            throw new RuntimeException(
                'No se encontró la configuración. Revisá Admin/config.php.'
            );
        }

        // Cargar la configuración de MySQL.
        $cfg = require $configPath;

        if (!is_array($cfg)) {
            throw new RuntimeException(
                'El archivo config.php no devuelve una configuración válida.'
            );
        }

        // Comprobar los datos necesarios.
        foreach (['host', 'db', 'user', 'pass'] as $key) {
            if (!array_key_exists($key, $cfg)) {
                throw new RuntimeException(
                    'La configuración de la base de datos está incompleta.'
                );
            }
        }

        // Conectar con MySQL.
        $pdo = new PDO(
            "mysql:host={$cfg['host']};dbname={$cfg['db']};charset=utf8mb4",
            $cfg['user'],
            $cfg['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        // Obtener los datos ingresados.
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validar los datos.
        if (
            !is_string($email) ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 190 ||
            !is_string($password) ||
            $password === ''
        ) {
            $error = 'Ingresá un correo electrónico válido y tu contraseña.';
        } else {
            // Buscar al administrador por correo.
            $stmt = $pdo->prepare(
                'SELECT id, name, email, password_hash
                 FROM admin_users
                 WHERE email = ?
                 LIMIT 1'
            );

            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Verificar las credenciales.
            if (
                $user &&
                isset($user['password_hash']) &&
                password_verify($password, $user['password_hash'])
            ) {
                // Renovar el identificador de sesión.
                session_regenerate_id(true);

                $_SESSION['admin_id'] = (int) $user['id'];
                $_SESSION['admin_name'] = $user['name'];

                // Redirigir al panel administrativo.
                header('Location: index.php');
                exit;
            }

            $error = 'Correo electrónico o contraseña incorrectos.';
        }
    } catch (Throwable $e) {
        // Guardar los detalles técnicos en el registro del servidor.
        error_log('Error en Admin/login.php: ' . $e->getMessage());

        if ($e instanceof PDOException) {
            $error = 'No se pudo conectar con la base de datos. Revisá la configuración de MySQL.';
        } elseif ($e instanceof RuntimeException) {
            $error = $e->getMessage();
        } else {
            $error = 'Ocurrió un error al iniciar sesión. Revisá la configuración del portal.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | INADI Administración</title>

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
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(102, 102, 190, 0.22),
                    transparent 38%
                ),
                linear-gradient(135deg, #f1f2fb, #e5e8f5);
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 38px;
            background: #fff;
            border: 1px solid rgba(69, 69, 156, 0.12);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(41, 41, 101, 0.14);
            text-align: center;
        }

        .login-logo {
            display: block;
            width: 150px;
            height: 125px;
            margin: 0 auto 18px;
            object-fit: contain;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .login-card h1 {
            margin: 0;
            color: var(--primary-deep);
            font-size: 30px;
            font-weight: 800;
        }

        .login-card h1 span {
            color: var(--primary);
            font-weight: 400;
        }

        .muted {
            margin: 12px 0 28px;
            color: #77778b;
            font-size: 14px;
            line-height: 1.6;
        }

        .notice.error {
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #f3c5c5;
            border-radius: 10px;
            background: #fff0f0;
            color: #a52828;
            font-size: 13px;
            line-height: 1.5;
            text-align: left;
            overflow-wrap: anywhere;
        }

        .login-card form {
            display: flex;
            flex-direction: column;
            gap: 18px;
            text-align: left;
        }

        .login-card label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            color: #35354e;
            font-size: 13px;
            font-weight: 700;
        }

        .login-card input {
            width: 100%;
            min-height: 46px;
            padding: 12px 14px;
            border: 1px solid #dcdced;
            border-radius: 10px;
            background: #fafaff;
            color: #292945;
            font: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .login-card input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(69, 69, 156, 0.12);
        }

        .login-card .btn.primary {
            width: 100%;
            min-height: 48px;
            margin-top: 4px;
            padding: 12px 18px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(
                135deg,
                var(--primary),
                var(--primary-deep)
            );
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .login-card .btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(69, 69, 156, 0.25);
        }

        .login-foot {
            margin: 28px 0 0;
            padding-top: 18px;
            border-top: 1px solid #eeeef6;
            color: #88889b;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            body.login-body {
                padding: 16px;
            }

            .login-card {
                padding: 28px 22px;
                border-radius: 18px;
            }

            .login-logo {
                width: 125px;
                height: 105px;
            }

            .login-card h1 {
                font-size: 26px;
            }
        }
    </style>
</head>

<body class="login-body">

    <main class="login-card">

        <img
            class="login-logo"
            src="../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png"
            alt="Logo de INADI"
            onerror="this.style.display='none';"
        >

        <p class="eyebrow">PANEL DE CONTROL</p>

        <h1>INADI <span>Admin</span></h1>

        <p class="muted">
            Administración del sitio institucional
        </p>

        <?php if ($error !== ''): ?>
            <div class="notice error" role="alert">
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php">

            <label for="email">
                Correo electrónico

                <input
                    type="email"
                    id="email"
                    name="email"
                    autocomplete="username"
                    maxlength="190"
                    value="<?= htmlspecialchars(
                        $_POST['email'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >
            </label>

            <label for="password">
                Contraseña

                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </label>

            <button type="submit" class="btn primary">
                Iniciar sesión
            </button>

        </form>

        <p class="login-foot">
            Instituto de capacitación · INADI
        </p>

    </main>

</body>
</html>
