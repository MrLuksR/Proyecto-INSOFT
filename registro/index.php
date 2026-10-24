
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = $_SESSION['error_usuario'] ?? '';
$datos = $_SESSION['formulario'] ?? [];

unset($_SESSION['error_usuario'], $_SESSION['formulario']);

/**
 * Escapa los valores antes de mostrarlos en HTML.
 */
function valor(string $campo, array $datos): string
{
    return htmlspecialchars(
        (string) ($datos[$campo] ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


$cursos = [
    'Introducción a la Informática',
    'Alfabetización Digital',
    'Programación con Python',
    'Desarrollo Web con HTML, CSS y JavaScript',
    'Programación PHP',
    'Bases de Datos con SQL y MySQL',
    'Ingeniería de Software',
    'Inteligencia Artificial Aplicada',
    'Ciberseguridad',
    'Redes y Soporte Técnico',
    'Desarrollo de Videojuegos',
    'Diseño UI/UX',
    'Desarrollo de Aplicaciones',
    'Git y GitHub',
];

$cursoSeleccionado = (string) ($datos['curso'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registro de estudiantes | INADI</title>

    <link rel="stylesheet" href="css/stylesregistro.css?v=20">
    <script src="js/registro.js?v=20" defer></script>
</head>

<body>

<main class="contenedor-registro">

    <!-- =====================================
         FORMULARIO DE REGISTRO
    ====================================== -->

    <section class="panel-formulario" aria-labelledby="tituloRegistro">

        <a
            href="../Bienvenido/bienvenidoindex.php"
            class="boton-volver"
        >Volver al inicio</a>

        <header class="encabezado">

            <img
                src="../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png"
                alt="Logo de INADI"
                class="logo-inadi"
            >

            <p class="etiqueta">PLATAFORMA EDUCATIVA</p>

            <h1 id="tituloRegistro">Registro de estudiante</h1>

            <p>
                Completá tus datos para crear tu cuenta en INADI.
            </p>

        </header>

        <?php if ($error !== ''): ?>
            <div class="mensaje-error" role="alert">
                <strong>No se pudo completar el registro.</strong>
                <p><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <form
            id="formularioRegistro"
            action="../database/consultas/estudiantes/registro.php"
            method="POST"
            enctype="multipart/form-data"
        >

            <!-- NOMBRE Y APELLIDO -->

            <div class="grupo-campos">

                <div class="campo">
                    <label for="nombre">Nombre</label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        placeholder="Ingresá tu nombre"
                        value="<?= valor('nombre', $datos) ?>"
                        autocomplete="given-name"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="apellido">Apellido</label>

                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        placeholder="Ingresá tu apellido"
                        value="<?= valor('apellido', $datos) ?>"
                        autocomplete="family-name"
                        maxlength="100"
                        required
                    >
                </div>

            </div>

            <!-- CÉDULA -->

            <div class="campo">

                <label for="cedula">Cédula de identidad</label>

                <input
                    type="text"
                    id="cedula"
                    name="cedula"
                    placeholder="Ej.: 4.567.890-1"
                    value="<?= valor('cedula', $datos) ?>"
                    inputmode="numeric"
                    autocomplete="off"
                    maxlength="10"
                    required
                >

                <small id="estadoCedula" aria-live="polite">
                    Ingresá tu cédula uruguaya.
                </small>

            </div>

            <!-- FECHA Y TELÉFONO -->

            <div class="grupo-campos">

                <div class="campo">

                    <label for="fecha_nacimiento">
                        Fecha de nacimiento
                    </label>

                    <input
                        type="date"
                        id="fecha_nacimiento"
                        name="fecha"
                        value="<?= valor('fecha', $datos) ?>"
                        required
                    >

                </div>

                <div class="campo">

                    <label for="telefono">Teléfono</label>

                    <input
                        type="tel"
                        id="telefono"
                        name="telefono"
                        placeholder="Tu número de contacto"
                        value="<?= valor('telefono', $datos) ?>"
                        autocomplete="tel"
                    >

                </div>

            </div>

            <!-- CORREO ELECTRÓNICO -->

            <div class="campo">

                <label for="email">Correo electrónico</label>

                <input
                    type="email"
                    id="email"
                    name="correo"
                    placeholder="ejemplo@correo.com"
                    value="<?= valor('correo', $datos) ?>"
                    autocomplete="email"
                    maxlength="190"
                    required
                >

            </div>

            <!-- NOMBRE DE USUARIO -->

            <div class="campo">

                <label for="username">Nombre de usuario</label>

                <input
                    type="text"
                    id="username"
                    name="nombre_usuario"
                    placeholder="Elegí tu nombre de usuario"
                    value="<?= valor('nombre_usuario', $datos) ?>"
                    autocomplete="username"
                    maxlength="60"
                    required
                >

            </div>

            <!-- CONTRASEÑA -->

            <div class="campo">

                <label for="password">Contraseña</label>

                <div class="campo-password">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Creá una contraseña segura"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                    <button
                        type="button"
                        id="togglePassword"
                        class="boton-mostrar-password"
                        aria-label="Mostrar contraseña"
                        aria-pressed="false"
                    >Ver</button>

                </div>

                <small>Utilizá al menos 8 caracteres.</small>

            </div>

            <!-- CURSOS DE INFORMÁTICA -->

            <div class="campo">

                <label for="curso">Curso de Informática</label>

                <select
                    id="curso"
                    name="curso"
                    required
                >

                    <option value="">Seleccioná un curso</option>

                    <?php foreach ($cursos as $curso): ?>

                        <option
                            value="<?= htmlspecialchars(
                                $curso,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            <?= $cursoSeleccionado === $curso
                                ? 'selected'
                                : '' ?>
                        >
                            <?= htmlspecialchars(
                                $curso,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <small>
                    Elegí el área tecnológica en la que querés formarte.
                </small>

            </div>

            <!-- FOTOGRAFÍA -->

            <div class="campo">

                <label for="foto">Fotografía de perfil</label>

                <input
                    type="file"
                    id="foto"
                    name="foto"
                    accept="image/jpeg,image/png,image/webp"
                >

                <small>
                    Formatos permitidos: JPG, PNG o WEBP.
                    Tamaño máximo: 5 MB.
                </small>

            </div>

            <!-- DATOS DEL ADULTO RESPONSABLE -->

            <section id="datosTutor" class="datos-tutor" hidden>

                <h2>Datos del adulto responsable</h2>

                <p>
                    Como el estudiante es menor de 18 años,
                    necesitamos los datos de su adulto responsable.
                </p>

                <div class="campo">

                    <label for="nombre_tutor">
                        Nombre completo del adulto responsable
                    </label>

                    <input
                        type="text"
                        id="nombre_tutor"
                        name="nombre_tutor"
                        value="<?= valor('nombre_tutor', $datos) ?>"
                        autocomplete="name"
                        maxlength="150"
                    >

                </div>

                <div class="campo">

                    <label for="telefono_tutor">
                        Teléfono del adulto responsable
                    </label>

                    <input
                        type="tel"
                        id="telefono_tutor"
                        name="telefono_tutor"
                        value="<?= valor('telefono_tutor', $datos) ?>"
                        autocomplete="tel"
                    >

                </div>

            </section>

            <!-- CREAR CUENTA -->

            <button type="submit" class="boton-registro">
                Crear cuenta
            </button>

            <p class="texto-login">
                ¿Ya tenés una cuenta?
                <a href="../login/loginn.php">Iniciar sesión</a>
            </p>

        </form>

    </section>

    <!-- =====================================
         VISTA PREVIA DE LA CREDENCIAL
    ====================================== -->

    <aside
        class="panel-credencial"
        aria-label="Vista previa de credencial estudiantil"
    >

        <article class="credencial" id="credencial">

            <header class="credencial-cabecera">

                <img
                    src="../Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png"
                    alt="Logo de INADI"
                    class="logo-credencial"
                >

                <div class="identidad-institucional">

                    <p class="credencial-institucion">INADI</p>

                    <p class="credencial-subtitulo">
                        CREDENCIAL ESTUDIANTIL
                    </p>

                </div>

            </header>

            <div class="credencial-contenido">

                <div class="credencial-foto">

                    <img
                        id="previewFoto"
                        src=""
                        alt="Fotografía del estudiante"
                        hidden
                    >

                    <span id="textoFoto">FOTO</span>

                </div>

                <div class="credencial-datos">

                    <p class="credencial-etiqueta">ESTUDIANTE</p>

                    <h2 id="previewNombre">
                        <?= valor('nombre', $datos) !== ''
                            ? valor('nombre', $datos)
                            : 'Nombre' ?>
                    </h2>

                    <h2 id="previewApellido">
                        <?= valor('apellido', $datos) !== ''
                            ? valor('apellido', $datos)
                            : 'Apellido' ?>
                    </h2>

                    <p>
                        <strong>Cédula:</strong>
                        <span id="previewCedula">
                            <?= valor('cedula', $datos) !== ''
                                ? valor('cedula', $datos)
                                : '—' ?>
                        </span>
                    </p>

                    <p>
                        <strong>Fecha de nacimiento:</strong>
                        <span id="previewFecha">
                            <?= valor('fecha', $datos) !== ''
                                ? valor('fecha', $datos)
                                : '—' ?>
                        </span>
                    </p>

                    <p>
                        <strong>Curso:</strong>
                        <span id="previewCurso">
                            <?= htmlspecialchars(
                                $cursoSeleccionado !== ''
                                    ? $cursoSeleccionado
                                    : 'Sin seleccionar',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </p>

                </div>

            </div>

            <footer class="credencial-pie">

                <span>IDENTIFICACIÓN ESTUDIANTIL</span>
                <span>INADI</span>

            </footer>

        </article>

        <p class="ayuda-credencial">
            Vista previa de tu credencial. La emisión definitiva
            dependerá de la confirmación del registro.
        </p>

    </aside>

</main>

</body>
</html>
