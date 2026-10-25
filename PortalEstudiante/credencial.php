
<?php

/* 1. CARGA DE FUNCIONES COMUNES */
require_once __DIR__ . '/portal_common.php';

/* 2. OBTENER LOS DATOS SIN ASUMIR QUE $usuario ES UN OBJETO */
$nombreUsuario = 'Estudiante';
$fotoUsuario = null;

if (isset($usuario) && is_object($usuario)) {
    if (method_exists($usuario, 'getNombreUsuario')) {
        $nombreUsuario = (string) $usuario->getNombreUsuario();
    }

    if (method_exists($usuario, 'getFoto')) {
        $fotoUsuario = $usuario->getFoto();
    }
}

/* Recuperar el nombre desde la sesión si no se obtuvo del objeto */
if ($nombreUsuario === 'Estudiante' || trim($nombreUsuario) === '') {
    $nombreUsuario = (string) (
        $_SESSION['nombre_usuario']
        ?? $_SESSION['nombre']
        ?? portalNombreActual()
    );
}

$nombreUsuario = trim($nombreUsuario) !== ''
    ? trim($nombreUsuario)
    : 'Estudiante';

/* Escapar valores que se muestran en HTML */
$nombreSeguro = e($nombreUsuario);

/* Validar el nombre del archivo de la foto */
$rutaFoto = null;

if (is_string($fotoUsuario) && trim($fotoUsuario) !== '') {
    $archivoFoto = basename($fotoUsuario);
    $rutaFisica = __DIR__
        . '/../database/consultas/estudiantes/fotosEst/'
        . $archivoFoto;

    if (is_file($rutaFisica)) {
        $rutaFoto = '../database/consultas/estudiantes/fotosEst/'
            . rawurlencode($archivoFoto);
    }
}

/* 3. INICIO DEL DISEÑO */
portalLayoutInicio(
    'Credencial estudiantil',
    'credencial'
);

?>

<!-- ENCABEZADO Y BOTÓN DE IMPRESIÓN -->
<section class="page-heading no-print">
    <div>
        <p class="eyebrow">IDENTIFICACIÓN</p>
        <h1>Credencial estudiantil</h1>
        <p>Vista imprimible con los datos confirmados disponibles.</p>
    </div>

    <button
        class="btn btn-primary"
        type="button"
        onclick="window.print()"
    >
        <i class="fa-solid fa-print" aria-hidden="true"></i>
        Imprimir
    </button>
</section>

<!-- CONTENEDOR PRINCIPAL DE LA CREDENCIAL -->
<div class="student-credential">

    <!-- ENCABEZADO -->
    <div class="credential-header">
        <div class="credential-logo">INADI</div>

        <div>
            <strong>INSTITUTO DE CAPACITACIÓN</strong>
            <small>Credencial estudiantil · Vista informativa</small>
        </div>
    </div>

    <!-- DATOS DEL ESTUDIANTE -->
    <div class="credential-body">

        <!-- FOTO O ICONO -->
        <?php if ($rutaFoto !== null): ?>
            <img
                src="<?= e($rutaFoto) ?>"
                alt="Foto de <?= $nombreSeguro ?>"
                class="credential-photo"
            >
        <?php else: ?>
            <div class="credential-photo" aria-label="Sin foto disponible">
                <i class="fa-solid fa-user" aria-hidden="true"></i>
            </div>
        <?php endif; ?>

        <!-- INFORMACIÓN DEL USUARIO -->
        <div>
            <span>Usuario</span>

            <h2><?= $nombreSeguro ?></h2>

            <span>Estado de identificación</span>

            <p>No oficial · Requiere validación administrativa</p>
        </div>

    </div>

    <!-- PIE DE LA CREDENCIAL -->
    <div class="credential-footer">
        Esta vista no sustituye una credencial oficial
        emitida por INADI.
    </div>

</div>

<?php
/* 4. CIERRE DEL DISEÑO */
portalLayoutFin();
?>
