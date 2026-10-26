
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errores = $_SESSION['errores_registro'] ?? [];
$datos = $_SESSION['datos_registro'] ?? [];
$registroExitoso = !empty($_SESSION['registro_exitoso']);
$nombreRegistrado = (string) ($_SESSION['nombre_registrado'] ?? '');

unset(
    $_SESSION['errores_registro'],
    $_SESSION['datos_registro'],
    $_SESSION['registro_exitoso'],
    $_SESSION['nombre_registrado']
);

function valor(string $campo, array $datos): string
{
    return htmlspecialchars(
        (string) ($datos[$campo] ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$cursos = [
    'Introducción a la Informática' => [
        'icono' => 'laptop',
        'detalle' => 'Fundamentos y herramientas digitales'
    ],
    'Alfabetización Digital' => [
        'icono' => 'monitor',
        'detalle' => 'Competencias para el mundo digital'
    ],
    'Programación con Python' => [
        'icono' => 'code',
        'detalle' => 'Aprendé a programar desde cero'
    ],
    'Desarrollo Web con HTML, CSS y JavaScript' => [
        'icono' => 'panels',
        'detalle' => 'Creá sitios web modernos'
    ],
    'Programación PHP' => [
        'icono' => 'terminal',
        'detalle' => 'Desarrollo web del lado del servidor'
    ],
    'Bases de Datos con SQL y MySQL' => [
        'icono' => 'database',
        'detalle' => 'Organizá y consultá información'
    ],
    'Ingeniería de Software' => [
        'icono' => 'workflow',
        'detalle' => 'Diseño y desarrollo de sistemas'
    ],
    'Inteligencia Artificial Aplicada' => [
        'icono' => 'brain',
        'detalle' => 'Explorá nuevas tecnologías'
    ],
    'Ciberseguridad' => [
        'icono' => 'shield-check',
        'detalle' => 'Protección y seguridad digital'
    ],
    'Redes y Soporte Técnico' => [
        'icono' => 'network',
        'detalle' => 'Conectividad y soporte'
    ],
    'Desarrollo de Videojuegos' => [
        'icono' => 'gamepad-2',
        'detalle' => 'Diseñá experiencias interactivas'
    ],
    'Diseño UI/UX' => [
        'icono' => 'palette',
        'detalle' => 'Diseño de experiencias digitales'
    ],
    'Desarrollo de Aplicaciones' => [
        'icono' => 'smartphone',
        'detalle' => 'Soluciones para dispositivos'
    ],
    'Git y GitHub' => [
        'icono' => 'git-branch',
        'detalle' => 'Versionado y colaboración'
    ]
];

$cursoSeleccionado = (string) ($datos['curso'] ?? '');

$logoInadi = '/Proyecto-INSOFT/Elementos%20Gr%C3%A1ficos/Logo%20Inadi%20con%20Brillo.png';
$urlInicio = '/Proyecto-INSOFT/Bienvenido/bienvenidoindex.php';
$urlLogin = '/Proyecto-INSOFT/login/loginn.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#101a33">

    <title>Crear cuenta | INADI</title>

    <link rel="stylesheet" href="css/stylesregistro.css?v=31">
    <script src="js/registro.js?v=31" defer></script>
</head>

<body>

<div class="fondo-decorativo fondo-decorativo-uno" aria-hidden="true"></div>
<div class="fondo-decorativo fondo-decorativo-dos" aria-hidden="true"></div>

<header class="barra-superior">

    <a class="marca" href="<?= htmlspecialchars($urlInicio, ENT_QUOTES, 'UTF-8') ?>"
       aria-label="INADI, Instituto Nacional de Informática">

        <img
            class="marca-logo"
            src="<?= htmlspecialchars($logoInadi, ENT_QUOTES, 'UTF-8') ?>"
            alt="Logo oficial de INADI"
        >

        <span class="marca-texto">
            <strong>INADI</strong>
            <small>INSTITUTO NACIONAL DE INFORMÁTICA</small>
        </span>

    </a>

    <div class="barra-acceso">
        <span>¿Ya tenés una cuenta?</span>
        <a href="<?= htmlspecialchars($urlLogin, ENT_QUOTES, 'UTF-8') ?>">
            Iniciar sesión
        </a>
    </div>

</header>

<main class="pagina-registro">

    <!-- PRESENTACIÓN -->
    <section class="intro">

        <a href="<?= htmlspecialchars($urlInicio, ENT_QUOTES, 'UTF-8') ?>"
           class="enlace-volver">
            Volver al inicio
        </a>

        <div class="intro-etiqueta">
            <span class="punto-vivo"></span>
            TU PRÓXIMO CAPÍTULO EMPIEZA ACÁ
        </div>

        <h1>
            Tu futuro digital<br>
            <span>empieza hoy.</span>
        </h1>

        <p class="intro-descripcion">
            Creá tu cuenta y preparate para descubrir nuevas habilidades,
            explorar la tecnología y llevar tus ideas más lejos.
        </p>

        <div class="intro-beneficios">

            <div>
                <span class="beneficio-icono" aria-hidden="true">✳</span>
                <span>
                    <strong>Aprendé haciendo</strong>
                    <small>Convertí ideas en habilidades.</small>
                </span>
            </div>

            <div>
                <span class="beneficio-icono" aria-hidden="true">⌘</span>
                <span>
                    <strong>Encontrá tu camino</strong>
                    <small>Elegí el área que te inspira.</small>
                </span>
            </div>

            <div>
                <span class="beneficio-icono" aria-hidden="true">↗</span>
                <span>
                    <strong>Construí tu futuro</strong>
                    <small>Un paso a la vez.</small>
                </span>
            </div>

        </div>

        <div class="decoracion-codigo" aria-hidden="true">
            <span class="codigo-punto punto-rojo"></span>
            <span class="codigo-punto punto-amarillo"></span>
            <span class="codigo-punto punto-verde"></span>

            <code>
                <span>const</span> futuro = <em>"tuyo"</em>;<br>
                <span>inadi</span>.comenzar(futuro);
            </code>

            <div class="codigo-etiqueta">
                APRENDER · CREAR · CRECER
            </div>
        </div>

    </section>

    <!-- FORMULARIO -->
    <section class="panel-registro" aria-labelledby="tituloRegistro">

        <div class="panel-cabecera">
            <div>
                <span class="sobretitulo">INSCRIPCIÓN DE ESTUDIANTE</span>
                <h2 id="tituloRegistro">Creá tu cuenta</h2>
                <p>Completá tus datos en unos simples pasos.</p>
            </div>

            <div class="icono-perfil" aria-hidden="true">✦</div>
        </div>

        <!-- MENSAJE DE REGISTRO EXITOSO -->
        <?php if ($registroExitoso): ?>

            <div class="mensaje mensaje-exito" role="status" aria-live="polite">

                <strong>¡Registro exitoso!</strong>

                <p>
                    <?php if ($nombreRegistrado !== ''): ?>
                        ¡Bienvenido/a,
                        <?= htmlspecialchars($nombreRegistrado, ENT_QUOTES, 'UTF-8') ?>!
                        Tu cuenta fue creada correctamente.
                    <?php else: ?>
                        Tu cuenta fue creada correctamente.
                    <?php endif; ?>
                </p>

                <p>En 10 segundos te enviaremos al inicio de sesión.</p>

                <div class="progreso-redireccion" aria-hidden="true">
                    <span></span>
                </div>

                <a href="<?= htmlspecialchars($urlLogin, ENT_QUOTES, 'UTF-8') ?>">
                    Ir al inicio de sesión ahora
                </a>

            </div>

            <script>
                window.setTimeout(function () {
                    window.location.href =
                        <?= json_encode($urlLogin . '?registro=exitoso') ?>;
                }, 10000);
            </script>

        <?php endif; ?>

        <!-- ERRORES DEL SERVIDOR -->
        <?php if (!empty($errores)): ?>

            <div class="mensaje mensaje-error" role="alert">
                <strong>No se pudo completar el registro.</strong>

                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li>
                            <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; ?>

        <!-- INDICADOR DE PASOS -->
        <div class="pasos-indicador" aria-label="Progreso del registro">

            <div class="paso-indicador activo" data-indicador="0">
                <span>01</span>
                <small>Tu perfil</small>
            </div>

            <div class="paso-linea"><span></span></div>

            <div class="paso-indicador" data-indicador="1">
                <span>02</span>
                <small>Tu cuenta</small>
            </div>

            <div class="paso-linea"><span></span></div>

            <div class="paso-indicador" data-indicador="2">
                <span>03</span>
                <small>Confirmación</small>
            </div>

        </div>

        <form
            id="formularioRegistro"
            action="../database/consultas/estudiantes/registro.php"
            method="POST"
            enctype="multipart/form-data"
            novalidate
        >

            <!-- PASO 1: PERFIL -->
            <section
                class="paso-formulario activo"
                data-paso="0"
                aria-labelledby="tituloPaso1"
            >

                <div class="titulo-paso">
                    <span class="numero-paso">01</span>
                    <div>
                        <h3 id="tituloPaso1">Empecemos por vos</h3>
                        <p>Contanos un poco sobre vos.</p>
                    </div>
                </div>

                <div class="grupo-campos">

                    <div class="campo">
                        <label for="nombre">Nombre <span>*</span></label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            placeholder="Tu nombre"
                            value="<?= valor('nombre', $datos) ?>"
                            autocomplete="given-name"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="apellido">Apellido <span>*</span></label>
                        <input
                            type="text"
                            id="apellido"
                            name="apellido"
                            placeholder="Tu apellido"
                            value="<?= valor('apellido', $datos) ?>"
                            autocomplete="family-name"
                            maxlength="100"
                            required
                        >
                    </div>

                </div>

                <div class="campo">
                    <label for="cedula">
                        Cédula de identidad <span>*</span>
                    </label>

                    <div class="campo-con-icono">
                        <span aria-hidden="true">▤</span>
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
                    </div>

                    <small id="estadoCedula" class="ayuda-campo" aria-live="polite">
                        Ingresá tu cédula uruguaya de 8 dígitos.
                    </small>
                </div>

                <div class="grupo-campos">

                    <div class="campo">
                        <label for="fecha_nacimiento">
                            Fecha de nacimiento <span>*</span>
                        </label>
                        <input
                            type="date"
                            id="fecha_nacimiento"
                            name="fecha_nacimiento"
                            value="<?= valor('fecha_nacimiento', $datos) ?>"
                            autocomplete="bday"
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

                <div class="nota-privacidad">
                    <span aria-hidden="true">♧</span>
                    <p>
                        Tus datos personales se utilizarán para gestionar tu inscripción.
                    </p>
                </div>

                <button type="button" class="boton-principal boton-siguiente">
                    Continuar
                </button>

            </section>

            <!-- PASO 2: CUENTA -->
            <section
                class="paso-formulario"
                data-paso="1"
                aria-labelledby="tituloPaso2"
                hidden
            >

                <div class="titulo-paso">
                    <span class="numero-paso">02</span>
                    <div>
                        <h3 id="tituloPaso2">Tu espacio digital</h3>
                        <p>Elegí tu curso y prepará tus datos de acceso.</p>
                    </div>
                </div>

                <div class="campo">
                    <label for="email">
                        Correo electrónico <span>*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="vos@correo.com"
                        value="<?= valor('email', $datos) ?>"
                        autocomplete="email"
                        maxlength="190"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="username">
                        Nombre de usuario <span>*</span>
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Elegí tu usuario"
                        value="<?= valor('username', $datos) ?>"
                        autocomplete="username"
                        maxlength="60"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="password">
                        Contraseña <span>*</span>
                    </label>

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

                    <div class="fortaleza-password" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <small id="ayudaPassword" class="ayuda-campo">
                        Usá al menos 8 caracteres.
                    </small>
                </div>

                <div class="campo">
                    <label>
                        ¿Qué te gustaría aprender? <span>*</span>
                    </label>

                    <p class="ayuda-campo ayuda-cursos">
                        Elegí el curso que mejor conecte con tus intereses.
                    </p>

                    <div class="selector-cursos" id="selectorCursos">

                        <?php
                        $iconos = [
                            'laptop' => '▣',
                            'monitor' => '▤',
                            'code' => '</>',
                            'panels' => '▥',
                            'terminal' => '>_',
                            'database' => '▤',
                            'workflow' => '⌘',
                            'brain' => '✳',
                            'shield-check' => '◇',
                            'network' => '⌁',
                            'gamepad-2' => '✣',
                            'palette' => '◒',
                            'smartphone' => '▯',
                            'git-branch' => '⑂'
                        ];
                        ?>

                        <?php foreach ($cursos as $curso => $info): ?>

                            <label class="tarjeta-curso <?= $cursoSeleccionado === $curso ? 'seleccionado' : '' ?>">

                                <input
                                    type="radio"
                                    name="curso"
                                    value="<?= htmlspecialchars($curso, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $cursoSeleccionado === $curso ? 'checked' : '' ?>
                                    required
                                >

                                <span class="curso-icono" aria-hidden="true">
                                    <?= htmlspecialchars($iconos[$info['icono']] ?? '✦', ENT_QUOTES, 'UTF-8') ?>
                                </span>

                                <span class="curso-texto">
                                    <strong>
                                        <?= htmlspecialchars($curso, ENT_QUOTES, 'UTF-8') ?>
                                    </strong>
                                    <small>
                                        <?= htmlspecialchars($info['detalle'], ENT_QUOTES, 'UTF-8') ?>
                                    </small>
                                </span>

                                <span class="curso-check" aria-hidden="true">✓</span>

                            </label>

                        <?php endforeach; ?>

                    </div>

                    <small id="estadoCurso" class="ayuda-campo" aria-live="polite"></small>
                </div>

                <div class="campo">
                    <label for="foto">
                        Fotografía de perfil
                        <span class="opcional">OPCIONAL</span>
                    </label>

                    <div class="subida-foto">
                        <div class="subida-foto-icono" aria-hidden="true">↑</div>

                        <div>
                            <strong>Elegí una fotografía</strong>
                            <small>JPG, PNG o WEBP. Máximo 5 MB</small>
                        </div>

                        <input
                            type="file"
                            id="foto"
                            name="foto"
                            accept="image/jpeg,image/png,image/webp"
                        >
                    </div>

                    <small id="estadoFoto" class="ayuda-campo" aria-live="polite"></small>
                </div>

                <!-- DATOS DEL TUTOR PARA MENORES -->
                <section id="datosTutor" class="datos-tutor" hidden>

                    <div class="tutor-titulo">
                        <span aria-hidden="true">♧</span>
                        <div>
                            <h4>Datos del adulto responsable</h4>
                            <p>
                                Como tenés menos de 18 años, necesitamos esta información.
                            </p>
                        </div>
                    </div>

                    <div class="campo">
                        <label for="nombre_tutor">
                            Nombre completo <span>*</span>
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
                            Teléfono de contacto <span>*</span>
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

                <div class="botones-navegacion">

                    <button type="button" class="boton-secundario boton-anterior">
                        Volver
                    </button>

                    <button type="button" class="boton-principal boton-siguiente">
                        Revisar datos
                    </button>

                </div>

            </section>

            <!-- PASO 3: CONFIRMACIÓN -->
            <section
                class="paso-formulario"
                data-paso="2"
                aria-labelledby="tituloPaso3"
                hidden
            >

                <div class="titulo-paso">
                    <span class="numero-paso">03</span>
                    <div>
                        <h3 id="tituloPaso3">¡Todo listo para empezar!</h3>
                        <p>Revisá tus datos antes de crear tu cuenta.</p>
                    </div>
                </div>

                <div class="resumen-registro">

                    <div class="resumen-cabecera">
                        <span class="resumen-icono">✓</span>
                        <div>
                            <strong>Tu perfil está casi listo</strong>
                            <p>Verificá que la información sea correcta.</p>
                        </div>
                    </div>

                    <div class="resumen-fila">
                        <span>Nombre completo</span>
                        <strong id="resumenNombre">—</strong>
                    </div>

                    <div class="resumen-fila">
                        <span>Cédula</span>
                        <strong id="resumenCedula">—</strong>
                    </div>

                    <div class="resumen-fila">
                        <span>Fecha de nacimiento</span>
                        <strong id="resumenFecha">—</strong>
                    </div>

                    <div class="resumen-fila">
                        <span>Correo electrónico</span>
                        <strong id="resumenEmail">—</strong>
                    </div>

                    <div class="resumen-fila">
                        <span>Nombre de usuario</span>
                        <strong id="resumenUsername">—</strong>
                    </div>

                    <div class="resumen-fila">
                        <span>Curso elegido</span>
                        <strong id="resumenCurso">—</strong>
                    </div>

                    <div class="resumen-fila">
                        <span>Fotografía</span>
                        <strong id="resumenFoto">Sin fotografía</strong>
                    </div>

                </div>

                <div class="nota-privacidad">
                    <span aria-hidden="true">◇</span>
                    <p>
                        Al crear tu cuenta, confirmás que los datos ingresados son correctos.
                    </p>
                </div>

                <div class="botones-navegacion">

                    <button type="button" class="boton-secundario boton-anterior">
                        Volver a editar
                    </button>

                    <button
                        type="submit"
                        class="boton-principal"
                        id="botonCrearCuenta"
                    >
                        Crear mi cuenta
                    </button>

                </div>

            </section>

        </form>

        <div class="pie-seguridad">
            <span aria-hidden="true">▣</span>
            Tus datos se envían de forma segura a INADI.
        </div>

    </section>

    <!-- VISTA PREVIA DE LA CREDENCIAL -->
    <aside class="panel-credencial" aria-label="Vista previa de credencial estudiantil">

        <div class="credencial-encabezado">
            <span class="credencial-etiqueta">TU IDENTIDAD INADI</span>

            <span class="credencial-pulso">
                <i></i>
                VISTA PREVIA
            </span>
        </div>

        <article class="credencial" id="credencial">

            <div class="credencial-brillo" aria-hidden="true"></div>

            <header class="credencial-cabecera">

                <img
                    class="logo-credencial"
                    src="<?= htmlspecialchars($logoInadi, ENT_QUOTES, 'UTF-8') ?>"
                    alt="Logo oficial de INADI"
                >

                <div class="credencial-marca-texto">
                    <p class="credencial-institucion">INADI</p>
                    <p class="credencial-subtitulo">
                        INSTITUTO NACIONAL DE INFORMÁTICA
                    </p>
                </div>

                <div class="credencial-sello" aria-hidden="true">✳</div>

            </header>

            <div class="credencial-contenido">

                <div class="credencial-foto">

                    <img
                        id="previewFoto"
                        src=""
                        alt="Fotografía del estudiante"
                        hidden
                    >

                    <span id="textoFoto" aria-hidden="true">
                        TU<br>FOTO
                    </span>

                </div>

                <div class="credencial-datos">

                    <p class="credencial-etiqueta-alumno">ESTUDIANTE</p>

                    <h3 id="previewNombre">
                        <?= valor('nombre', $datos) !== ''
                            ? valor('nombre', $datos)
                            : 'Tu nombre' ?>
                    </h3>

                    <h3 id="previewApellido">
                        <?= valor('apellido', $datos) !== ''
                            ? valor('apellido', $datos)
                            : 'Tu apellido' ?>
                    </h3>

                    <div class="credencial-linea"></div>

                    <p>
                        <span>CÉDULA</span>
                        <strong id="previewCedula">
                            <?= valor('cedula', $datos) !== ''
                                ? valor('cedula', $datos)
                                : '—' ?>
                        </strong>
                    </p>

                    <p>
                        <span>FECHA DE NACIMIENTO</span>
                        <strong id="previewFecha">
                            <?= valor('fecha_nacimiento', $datos) !== ''
                                ? valor('fecha_nacimiento', $datos)
                                : '—' ?>
                        </strong>
                    </p>

                    <p>
                        <span>CURSO</span>
                        <strong id="previewCurso">
                            <?= htmlspecialchars(
                                $cursoSeleccionado !== ''
                                    ? $cursoSeleccionado
                                    : 'Elegí tu curso',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </p>

                </div>

            </div>

            <footer class="credencial-pie">
                <span>APRENDER · CREAR · CRECER</span>
                <span>INADI / ESTUDIANTE</span>
            </footer>

        </article>

        <div class="credencial-ayuda">
            <span class="ayuda-destello" aria-hidden="true">✦</span>
            <p>
                <strong>Tu camino, tu credencial.</strong><br>
                Así se verá tu identificación estudiantil cuando completes tu registro.
            </p>
        </div>

        <div class="mini-estadisticas">

            <div>
                <span class="mini-icono" aria-hidden="true">⌘</span>
                <span>
                    <strong>Tu cuenta</strong>
                    <small>Tu espacio personal</small>
                </span>
            </div>

            <div>
                <span class="mini-icono" aria-hidden="true">↗</span>
                <span>
                    <strong>Tu aprendizaje</strong>
                    <small>Un nuevo comienzo</small>
                </span>
            </div>

        </div>

    </aside>

</main>

<footer class="pie-pagina">
    <span>
        © <?= date('Y') ?> INADI · Instituto Nacional de Informática
    </span>
    <span>Aprender abre caminos.</span>
</footer>

</body>
</html>
