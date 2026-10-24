
<?php
require_once __DIR__ . '/portal_common.php';

$nombreUsuario = $usuario->getNombreUsuario();
$fotoUsuario = $usuario->getFoto();
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
    <?php if ($fotoUsuario === NULL){
            echo '<div class="profile-avatar">
                <i class="fa-solid fa-user"></i>
            </div>';
        } else{
            echo '<img src="../database/consultas/estudiantes/fotosEst/'. $fotoUsuario .'" alt="Foto de '. $nombreUsuario .'" class="profile-avatar">';
        }
    ?>

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
            <i class="fa-solid fa-shield-halved"></i>

            <p>
                Para proteger tus datos personales, esta página
                muestra únicamente la información disponible
                en la sesión actual. Los demás datos se incorporarán
                cuando se confirme su relación con la base de datos.
            </p>
        </div>

        <!-- ACCIONES -->
        <div class="profile-actions">

            <!-- EDITAR PERFIL -->
            <?php if (!empty($haySesionEstudiante)): ?>

                <a href="editarPerfil.php" class="btn btn-primary">
                    <i class="fa-solid fa-user-pen"></i>
                    Editar perfil
                </a>

            <?php endif; ?>

            <!-- VOLVER AL PORTAL -->
            <a href="portalEstudiante.php" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i>
                Volver al portal
            </a>

            <!-- CERRAR SESIÓN -->
            <?php if (!empty($haySesionEstudiante)): ?>

                <a href="../auth/logout.php" class="btn btn-outline">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Cerrar sesión
                </a>

            <?php endif; ?>

        </div>

    </div>

</section>

<?php
portalLayoutFin();
?>
