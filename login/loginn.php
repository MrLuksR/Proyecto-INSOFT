<?php

// =====================================================
// CONFIGURACIÓN
// =====================================================

$mensaje = "";
$tipoMensaje = "";


// =====================================================
// PROCESAR LOGIN
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombreUsuario = trim($_POST["nombre_usuario"] ?? "");
    $password = $_POST["password"] ?? "";


    // Verificar que los campos estén completos

    if ($nombreUsuario === "" || $password === "") {

        $mensaje = "Por favor, completa todos los campos.";
        $tipoMensaje = "error";

    } else {

        /*
         * ACÁ IRÍA LA COMPROBACIÓN CON LA BASE DE DATOS.
         *
         * Por ahora solamente mostramos un mensaje.
         */

        $mensaje = "Datos recibidos correctamente.";
        $tipoMensaje = "success";
    }
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        INADI | Iniciar sesión
    </title>

    <link
        rel="stylesheet"
        href="styles.css"
    >

</head>


<body>

    <main class="login">

        <section class="formulario">


            <!-- =================================================
                 TÍTULO
            ================================================== -->

            <div class="titulo">

                <span class="etiqueta">
                    INADI
                </span>

                <h1>
                    Iniciar sesión
                </h1>

                <p>
                    Ingresá con tu nombre de usuario y contraseña.
                </p>

            </div>


            <!-- =================================================
                 MENSAJE
            ================================================== -->

            <?php if ($mensaje !== ""): ?>

                <div class="mensaje <?php echo $tipoMensaje; ?>">

                    <?php
                        echo htmlspecialchars($mensaje);
                    ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORMULARIO
            ================================================== -->

            <form
                action="login.php"
                method="POST"
            >


                <!-- NOMBRE DE USUARIO -->

                <div class="campo">

                    <label for="nombre_usuario">

                        Nombre de usuario

                    </label>


                    <input
                        type="text"
                        id="nombre_usuario"
                        name="nombre_usuario"
                        placeholder="Ingresá tu nombre de usuario"
                        value="<?php echo htmlspecialchars($nombreUsuario ?? ''); ?>"
                        required
                    >

                </div>


                <!-- CONTRASEÑA -->

                <div class="campo">

                    <label for="password">

                        Contraseña

                    </label>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Ingresá tu contraseña"
                        required
                    >

                </div>


                <!-- BOTÓN -->

                <button
                    type="submit"
                    class="boton"
                >

                    INICIAR SESIÓN

                </button>


            </form>

        </section>

    </main>

</body>

</html>
