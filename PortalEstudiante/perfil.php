
<?php

require_once __DIR__ . '/portal_common.php';

/* ======================================================
   1. DATOS DEL PERFIL
====================================================== */

$nombreUsuario = portalNombreActual();
$fotoUsuario = null;

$usuarioActual = $_SESSION['usuario'] ?? null;

// Verificar que el objeto sea válido antes de utilizarlo.
if (
    $haySesionEstudiante &&
    is_object($usuarioActual) &&
    !($usuarioActual instanceof __PHP_Incomplete_Class) &&
    get_class($usuarioActual) === 'Estudiante'
) {
    if (method_exists($usuarioActual, 'getNombreUsuario')) {
        $nombre = trim((string) $usuarioActual->getNombreUsuario());

        if ($nombre !== '') {
            $nombreUsuario = $nombre;
        }
    }

    if (method_exists($usuarioActual, 'getFoto')) {
        $foto = $usuarioActual->getFoto();

        if (is_string($foto) && trim($foto) !== '') {
            $fotoUsuario = basename(trim($foto));
        }
    }
}

portalLayoutInicio('Mi perfil', 'perfil');
?>

<!-- ENCABEZADO DE LA PÁGINA -->
<section class="page-heading">
    <div>
        <p class="eyebrow">CUENTA</p>
        <h1>Mi perfil</h1>
        <p>Consultá la información de tu cuenta estudiantil.</p>
    </div>
</section>

<!-- TARJETA DEL PERFIL -->
<section class="profile-card">

    <!-- AVATAR -->
    <?php if ($fotoUsuario !== null): ?>

        <img
            src="../database/consultas/estudiantes/fotosEst/<?= e($fotoUsuario) ?>"
            alt="Foto de <?= e($nombreUsuario) ?>"
            class="profile-avatar"
            onerror="this.style.display='none'; this.nextElementSibling.hidden=false"
        >

        <div class="profile-avatar" hidden>
            <i class="fa-solid fa-user" aria-hidden="true"></i>
        </div>

    <?php else: ?>

        <div class="profile-avatar">
            <i class="fa-solid fa-user" aria-hidden="true"></i>
        </div>

    <?php endif; ?>

    <!-- INFORMACIÓN DEL USUARIO -->
    <div class="profile-info">

        <span class="eyebrow">USUARIO DE LA SESIÓN</span>

        <h2><?= e($nombreUsuario) ?></h2>

        <p class="profile-description">
            Este nombre corresponde al usuario que inició sesión
            en el portal estudiantil.
        </p>

        <!-- ESTADO DE LA CUENTA -->
        <div class="profile-status">
            <span class="status-indicator"></span>

            <?= !empty($haySesionEstudiante)
                ? 'Sesión estudiantil activa'
                : 'Modo de prueba' ?>
        </div>

        <!-- AVISO INFORMATIVO -->
        <div class="profile-note">
            <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>

            <p>
                Para proteger tus datos personales, esta página
                muestra únicamente la información disponible
                en la sesión actual. Los demás datos se incorporarán
                cuando se confirme su relación con la base de datos.
            </p>
        </div>

        <!-- ACCIONES -->
        <div class="profile-actions">

            <a href="portalEstudiante.php" class="btn btn-primary">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Volver al portal
            </a>

            <?php if (!empty($haySesionEstudiante)): ?>

                <a href="../login/logout.php" class="btn btn-outline">
                    <i
                        class="fa-solid fa-right-from-bracket"
                        aria-hidden="true"
                    ></i>
                    Cerrar sesión
                </a>

            <?php else: ?>

                <a
                    href="../Bienvenido/bienvenidoindex.php"
                    class="btn btn-outline"
                >
                    <i
                        class="fa-solid fa-right-to-bracket"
                        aria-hidden="true"
                    ></i>
                    Ir al inicio de sesión
                </a>

            <?php endif; ?>

        </div>

    </div>

</section>

<?php
portalLayoutFin();
?>
