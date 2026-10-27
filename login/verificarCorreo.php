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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Verificación de correo | INADI</title>

    <link rel="stylesheet" href="stylesVerifCorreo.css">

    <script src="verificarCorreo.js" defer></script>

</head>

<body>

    <main class="verificacion-contenedor">

        <section class="formulario">

            <!-- ENCABEZADO -->
            <header class="encabezado">

                <a href="loginn.php" class="volver">
                    Volver al inicio de sesión
                </a>

                <div class="icono-seguridad">
                    <svg
                        viewBox="0 0 24 24"
                        width="35"
                        height="35"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <rect
                            x="5"
                            y="10"
                            width="14"
                            height="11"
                            rx="2"
                        />

                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />

                        <circle
                            cx="12"
                            cy="15.5"
                            r="1"
                        />
                    </svg>
                </div>

            </header>


            <!-- TÍTULO -->
            <div class="titulo">

                <span class="etiqueta">
                    SEGURIDAD DE LA CUENTA
                </span>

                <h1>
                    Verificá tu identidad
                </h1>

                <p>
                    Enviamos un código de verificación
                    de 4 dígitos a tu correo electrónico.
                </p>

                <p class="descripcion-secundaria">
                    Ingresá el código para continuar
                    con el inicio de sesión.
                </p>

            </div>


            <!-- MENSAJES DE ERROR -->
            <?php if ($error !== ''): ?>

                <div class="mensaje error" role="alert">

                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- FORMULARIO -->
            <?php if ($puedeVerificar): ?>

                <form
                    action="procesarVerificacion.php"
                    method="POST"
                    id="formularioVerificacion"
                >

                    <div class="campo">

                        <label id="etiquetaCodigo">
                            Código de verificación
                        </label>

                        <!-- CUATRO CASILLAS DEL CÓDIGO -->
                        <div class="codigo-contenedor">

                            <input
                                type="text"
                                name="codigo1"
                                class="codigo-input"
                                inputmode="numeric"
                                maxlength="1"
                                pattern="[0-9]"
                                autocomplete="one-time-code"
                                aria-label="Primer dígito"
                                required
                                autofocus
                            >

                            <input
                                type="text"
                                name="codigo2"
                                class="codigo-input"
                                inputmode="numeric"
                                maxlength="1"
                                pattern="[0-9]"
                                aria-label="Segundo dígito"
                                required
                            >

                            <input
                                type="text"
                                name="codigo3"
                                class="codigo-input"
                                inputmode="numeric"
                                maxlength="1"
                                pattern="[0-9]"
                                aria-label="Tercer dígito"
                                required
                            >

                            <input
                                type="text"
                                name="codigo4"
                                class="codigo-input"
                                inputmode="numeric"
                                maxlength="1"
                                pattern="[0-9]"
                                aria-label="Cuarto dígito"
                                required
                            >

                        </div>

                    </div>


                    <!-- INFORMACIÓN DE VENCIMIENTO -->
                    <div class="aviso-tiempo">

                        <svg
                            viewBox="0 0 24 24"
                            width="18"
                            height="18"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>

                        <span>
                            El código tiene una validez de
                            <strong>10 minutos.</strong>
                        </span>

                    </div>


                    <!-- BOTÓN -->
                    <button
                        type="submit"
                        class="boton"
                        id="botonVerificar"
                        disabled
                    >

                        Verificar código

                        <span aria-hidden="true"></span>

                    </button>

                </form>

            <?php else: ?>

                <div class="aviso-vencido">

                    <strong>
                        Verificación no disponible
                    </strong>

                    <p>
                        Para continuar, necesitás iniciar
                        sesión nuevamente y solicitar
                        un nuevo código.
                    </p>

                </div>

                <a
                    href="loginn.php"
                    class="boton boton-enlace"
                >
                    Volver al inicio de sesión

                    <span aria-hidden="true"></span>
                </a>

            <?php endif; ?>


            <!-- PIE -->
            <footer class="pie-formulario">

                INADI · Instituto Nacional de Informática

            </footer>

        </section>

    </main>

</body>

</html>