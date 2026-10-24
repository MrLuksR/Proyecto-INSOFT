<?php

require_once __DIR__ . '/portal_common.php';

// OBTENER MENSAJES DE SESIÓN
$error = $_SESSION['error_perfil'] ?? '';
$exito = $_SESSION['exito_perfil'] ?? '';

// ELIMINAR MENSAJES PARA QUE APAREZCAN UNA SOLA VEZ
unset(
    $_SESSION['error_perfil'],
    $_SESSION['exito_perfil']
);

// VERIFICAR QUE EXISTA UNA SESIÓN ESTUDIANTIL
if (
    empty($haySesionEstudiante) ||
    !isset($usuario) ||
    !($usuario instanceof Estudiante)
) {
    header('Location: ../Bienvenido/bienvenidoindex.php');
    exit;
}

// OBTENER DATOS DEL OBJETO ESTUDIANTE
$nombreUsuario = $usuario->getNombreUsuario();
$nombre = $usuario->getNombre();
$apellido = $usuario->getApellido();
$fotoUsuario = $usuario->getFoto();

// OBTENER DATOS CIFRADOS
// $key debe estar definida mediante la configuración segura
// del sistema antes de ejecutar estos getters.

$cedula = $usuario->getCedula($key);
$correo = $usuario->getCorreo($key);
$telefono = $usuario->getTelefono($key);
$fechaNacimiento = $usuario->getFechaNacimiento($key);

// INICIAR DISEÑO DEL PORTAL
portalLayoutInicio('Editar perfil', 'perfil');

?>

<!-- ENCABEZADO DE LA PÁGINA -->
<section class="page-heading">

    <div>
        <p class="eyebrow">CUENTA</p>

        <h1>Editar perfil</h1>

        <p>
            Actualizá la información personal de tu cuenta estudiantil.
        </p>
    </div>

</section>

<!-- NOTIFICACIONES DEL PERFIL -->
<?php if ($error !== '' || $exito !== ''): ?>

    <?php
        $esError = ($error !== '');

        $tipo = $esError ? 'error' : 'exito';

        $titulo = $esError
            ? 'No se pudo actualizar el perfil.'
            : 'Perfil actualizado correctamente.';

        $mensaje = $esError ? $error : $exito;

        $icono = $esError
            ? 'fa-circle-exclamation'
            : 'fa-circle-check';
    ?>

    <div
        class="mensaje-perfil mensaje-perfil-<?= $tipo ?>"
        role="alert"
    >

        <div class="mensaje-perfil-icono">
            <i class="fa-solid <?= $icono ?>"></i>
        </div>

        <div class="mensaje-perfil-contenido">

            <strong>
                <?= e($titulo) ?>
            </strong>

            <p>
                <?= e($mensaje) ?>
            </p>

        </div>

    </div>

<?php endif; ?>


<!-- FORMULARIO DE EDICIÓN -->
<form
    action="guardarPerfil.php"
    method="POST"
    enctype="multipart/form-data"
    class="edit-profile-form"
>

    <!-- TARJETA PRINCIPAL -->
    <section class="profile-card edit-profile-card">

        <!-- FOTOGRAFÍA DEL ESTUDIANTE -->
        <div class="edit-profile-photo">

            <div class="edit-avatar-container">

                <?php if ($fotoUsuario === NULL || $fotoUsuario === ''): ?>

                    <div class="profile-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                <?php else: ?>

                    <img
                        src="../database/consultas/estudiantes/fotosEst/<?= e($fotoUsuario) ?>"
                        alt="Foto de <?= e($nombreUsuario) ?>"
                        class="profile-avatar"
                        id="vistaPreviaFoto"
                    >

                <?php endif; ?>

            </div>

            <label for="foto" class="btn btn-outline">

                <i class="fa-solid fa-camera"></i>

                Cambiar fotografía

            </label>

            <input
                type="file"
                name="foto"
                id="foto"
                accept="image/jpeg,image/png,image/webp"
                hidden
            >

            <p class="edit-photo-description">
                Seleccioná una imagen JPG, PNG o WEBP.
            </p>

        </div>


        <!-- INFORMACIÓN DEL ESTUDIANTE -->
        <div class="profile-info">

            <span class="eyebrow">
                INFORMACIÓN PERSONAL
            </span>

            <h2>
                <?= e($nombre) ?> <?= e($apellido) ?>
            </h2>

            <p class="profile-description">
                Modificá los datos personales asociados a tu cuenta.
            </p>


            <!-- CAMPOS DEL FORMULARIO -->
            <div class="edit-profile-fields">

                <!-- NOMBRE DE USUARIO -->
                <div class="edit-form-group edit-full-width">

                    <label for="nombre_usuario">
                        Nombre de usuario
                    </label>

                    <div class="edit-input-icon">

                        <i class="fa-solid fa-user"></i>

                        <input
                            type="text"
                            id="nombre_usuario"
                            value="<?= e($nombreUsuario) ?>"
                            disabled
                        >

                    </div>

                    <small>
                        El nombre de usuario no puede modificarse.
                    </small>

                </div>


                <!-- NOMBRE -->
                <div class="edit-form-group">

                    <label for="nombre">
                        Nombre
                    </label>
                    <span class="edit-change-badge" aria-live="polite">Modificado</span>

                    <div class="edit-input-icon">

                        <i class="fa-solid fa-id-card"></i>

                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            value="<?= e($nombre) ?>"
                            maxlength="100"

                        >

                    </div>

                </div>


                <!-- APELLIDO -->
                <div class="edit-form-group">

                    <label for="apellido">
                        Apellido
                    </label>
                    <span class="edit-change-badge" aria-live="polite">Modificado</span>

                    <div class="edit-input-icon">

                        <i class="fa-solid fa-id-card"></i>

                        <input
                            type="text"
                            name="apellido"
                            id="apellido"
                            value="<?= e($apellido) ?>"
                            maxlength="100"

                        >

                    </div>

                </div>


                <!-- CÉDULA -->
                <div class="edit-form-group">

                    <label for="cedula">
                        Cédula de identidad
                    </label>

                    <div class="edit-input-icon">

                        <i class="fa-solid fa-address-card"></i>

                        <input
                            type="text"
                                                        id="cedula"
                            value="<?= e($cedula) ?>"
                            disabled
                        >

                    </div>

                </div>


                <!-- FECHA DE NACIMIENTO -->
                <div class="edit-form-group">

                    <label for="fecha_nacimiento">
                        Fecha de nacimiento
                    </label>
                    <span class="edit-change-badge" aria-live="polite">Modificado</span>

                    <div class="edit-input-icon">

                        <i class="fa-solid fa-calendar-days"></i>

                        <input
                            type="date"
                            name="fecha_nacimiento"
                            id="fecha_nacimiento"
                            value="<?= e($fechaNacimiento) ?>"
                            placeholder="dd/mm/aaaa"
                            pattern="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/[0-9]{4}"
                            title="Ingresá una fecha con formato dd/mm/aaaa"
                        >

                    </div>

                </div>


                <!-- CORREO ELECTRÓNICO -->
                <div class="edit-form-group">

                    <label for="correo">
                        Correo electrónico
                    </label>
                    <span class="edit-change-badge" aria-live="polite">Modificado</span>

                    <div class="edit-input-icon">

                        <i class="fa-solid fa-envelope"></i>

                        <input
                            type="email"
                            name="correo"
                            pattern="[^\s@]+@[^\s@]+\.[^\s@]+"
                            id="correo"
                            value="<?= e($correo) ?>"

                        >

                    </div>

                </div>


                <!-- TELÉFONO -->
                <div class="edit-form-group">

                    <label for="telefono">
                        Teléfono
                    </label>
                    <span class="edit-change-badge" aria-live="polite">Modificado</span>

                    <div class="edit-input-icon">

                        <i class="fa-solid fa-phone"></i>

                        <input
                            type="tel"
                            name="telefono"
                            id="telefono"
                            value="<?= e($telefono) ?>"
                        >

                    </div>

                </div>

            </div>


            <!-- AVISO DE SEGURIDAD -->
            <div class="profile-note">

                <i class="fa-solid fa-shield-halved"></i>

                <p>
                    Tu información personal se almacena de forma
                    protegida. Los cambios realizados se aplicarán
                    cuando confirmes la actualización de tu perfil.
                </p>

            </div>


            <!-- ACCIONES -->
            <div class="profile-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="fa-solid fa-floppy-disk"></i>

                    Guardar cambios
                </button>

                <a
                    href="perfil.php"
                    class="btn btn-outline"
                >
                    <i class="fa-solid fa-arrow-left"></i>

                    Volver
                </a>

            </div>

        </div>

    </section>

</form>


<!-- La interacción del formulario se gestiona desde portal.js -->

<?php

portalLayoutFin();

?>