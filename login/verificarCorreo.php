
<?php

// INICIAR SESIÓN TEMPORAL
session_start();

// OBTENER MENSAJE DE ERROR
$error = $_SESSION['error_verificacion'] ?? '';

// ELIMINAR MENSAJE PARA QUE NO SE REPITA
unset($_SESSION['error_verificacion']);

// COMPROBAR SI EXISTE UNA VERIFICACIÓN PENDIENTE
if (!isset($_SESSION['verificacion']) && $error === '') {
    header('Location: loginn.php');
    exit;
}

// COMPROBAR SI EL CÓDIGO VENCIÓ
if (
    isset($_SESSION['verificacion']) &&
    time() > $_SESSION['verificacion']['expira']
) {

    unset($_SESSION['verificacion']);

    $error = 'El código de verificación ha vencido. Iniciá sesión nuevamente.';
}

// DETERMINAR SI SE PUEDE VERIFICAR
$puedeVerificar = isset($_SESSION['verificacion']);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Verificación de correo | INADI</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f1f5f9;
            font-family: Arial, sans-serif;
        }

        .contenedor {
            background: white;
            padding: 35px;
            width: 100%;
            max-width: 400px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }

        h2 {
            color: #102b4e;
        }

        p {
            color: #64748b;
            line-height: 1.6;
        }

        input {
            width: 100%;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            text-align: center;
            font-size: 25px;
            letter-spacing: 10px;
        }

        input:focus {
            outline: none;
            border-color: #45459c;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #45459c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #35357e;
        }

        .volver {
            display: block;
            margin-top: 20px;
            color: #45459c;
            text-decoration: none;
        }

        /* MENSAJES DE ERROR */

        .mensaje-error {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
            border-radius: 8px;
            padding: 14px;
            margin: 20px 0;
            font-size: 14px;
            text-align: left;
            line-height: 1.5;
        }

    </style>

</head>

<body>

    <div class="contenedor">

        <h2>
            Verifica que eres tú
        </h2>

        <p>
            Enviamos un código de verificación
            de 4 cifras a tu correo electrónico.
        </p>

        <p>
            Ingresa el código para continuar
            con el inicio de sesión.
        </p>

        <!-- MOSTRAR MENSAJE DE ERROR -->

        <?php if ($error !== ''): ?>

            <div class="mensaje-error">

                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>

        <!-- FORMULARIO DE VERIFICACIÓN -->

        <?php if ($puedeVerificar): ?>

            <form
                action="procesarVerificacion.php"
                method="POST"
            >

                <input
                    type="text"
                    name="codigo"
                    placeholder="0000"
                    maxlength="4"
                    minlength="4"
                    inputmode="numeric"
                    pattern="[0-9]{4}"
                    autocomplete="one-time-code"
                    required
                    autofocus
                >

                <button type="submit">
                    Verificar código
                </button>

            </form>

            <p>
                El código tiene una validez de 10 minutos.
            </p>

        <?php else: ?>

            <p>
                Para continuar, necesitás solicitar
                un nuevo código de verificación.
            </p>

            <a href="loginn.php" class="volver">
                Volver al inicio de sesión
            </a>

        <?php endif; ?>

        <?php if ($puedeVerificar): ?>

            <a href="loginn.php" class="volver">
                Volver al inicio de sesión
            </a>

        <?php endif; ?>

    </div>

</body>

</html>