
<?php
include("../auth/config.php");
include("../database/Clases/Estudiante.php");
require_once("../database/consultas/cursos/getCursos.php");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Bienvenido/bienvenidoindex.php");
    exit;
}

// Conexión a la base de datos
require_once __DIR__ . '/../database/consultas/conexion.php';

$anioActual = date("Y");

// Obtener ID del curso
$idCurso = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

// Si no recibimos un ID válido, volver a cursos
if (!$idCurso || $idCurso <= 0) {
    header("Location: cursosindex.php");
    exit;
}

// Buscar curso en la base de datos
$sql = "
    SELECT
        curso.id_curso,
        curso.nombre,
        curso.modalidad,
        curso.duracion,
        curso.descripcion,
        curso.costo,
        curso.cupo,
        curso.estado,
        curso.img,
        categoria_cursos.nombre AS categoria
    FROM curso
    INNER JOIN categoria_cursos
        ON curso.id_categoria = categoria_cursos.id_categoria
    WHERE curso.id_curso = :idCurso
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':idCurso' => $idCurso
]);

$curso = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el curso no existe, volver al catálogo
if (!$curso) {
    header("Location: cursosindex.php");
    exit;
}

// Función para escapar texto HTML
function escaparCurso($valor)
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Datos del curso
$nombre = escaparCurso($curso['nombre']);
$categoria = escaparCurso($curso['categoria']);
$modalidad = escaparCurso($curso['modalidad']);
$duracion = escaparCurso($curso['duracion']);
$descripcion = escaparCurso($curso['descripcion']);
$costo = escaparCurso($curso['costo']);
$cupo = escaparCurso($curso['cupo']);
$estado = escaparCurso($curso['estado']);

$imagen = trim((string) ($curso['img'] ?? ''));

// Imagen predeterminada para Python (curso ID 3)
if ($imagen === '' && (int) $idCurso === 3) {
    $imagen = 'phyton.jpeg';
}

// Ruta de imagen
$rutaImagen = '';

if ($imagen !== '') {
    // Evitar rutas inesperadas provenientes de la base de datos
    $nombreImagen = basename(
        str_replace('\\', '/', $imagen)
    );

    $rutaImagen = 'imagenes/' . rawurlencode($nombreImagen);
}

// Datos de usuario para setLogin(), definido en tu proyecto
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Información del curso <?php echo $nombre; ?> de INADI."
    >

    <title>INADI | <?php echo $nombre; ?></title>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- CSS del detalle del curso -->
    <link rel="stylesheet" href="cursocurso.css">
</head>

<body>

<div class="page">

    <!-- BARRA SUPERIOR -->
    <header class="topbar">

        <div class="topbar-left">

            <a href="../Inicio/inicioindex.php" class="top-logo">
                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                >
                <span>INADI</span>
            </a>

            <div class="news">
                <i class="fa-solid fa-triangle-exclamation"></i>
                NOTICIAS
            </div>

        </div>

        <div class="topbar-right">

            <span class="language">
                <i class="fa-solid fa-globe"></i>
                ES
            </span>

            <?php
            if (function_exists('setLogin')) {
                setLogin();
            }
            ?>

        </div>

    </header>

    <!-- MENÚ LATERAL -->
    <aside class="sidebar">

        <ul class="menu">

            <li>
                <a href="../Inicio/inicioindex.php">
                    <i class="fa-solid fa-house"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <li>
                <a href="../Nosotros/nosotros.php">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Nosotros</span>
                </a>
            </li>

            <li class="active">
                <a href="cursosindex.php">
                    <i class="fa-solid fa-book"></i>
                    <span>Cursos</span>
                </a>
            </li>

            <li>
                <a href="../Contacto/contacto.php">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Contacto</span>
                </a>
            </li>

            <li>
                <a href="../Galeria/galeria.php">
                    <i class="fa-solid fa-image"></i>
                    <span>Galería</span>
                </a>
            </li>

            <li>
                <a href="../Admin/convenios.php">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Convenios</span>
                </a>
            </li>

            <li>
                <a href="../Empresas/empresas.php">
                    <i class="fa-solid fa-city"></i>
                    <span>Empresas</span>
                </a>
            </li>

        </ul>

        <div class="sidebar-bottom">
            © INADI <?php echo $anioActual; ?><br>
            Instituto de Informática<br>
            Salto - Uruguay
        </div>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="content">

        <main class="main">

            <!-- VOLVER AL CATÁLOGO -->
            <a href="cursosindex.php" class="back-button">
                <i class="fa-solid fa-arrow-left"></i>
                Volver a cursos
            </a>

            <!-- DETALLE DEL CURSO -->
            <section class="course-detail">

                <!-- IMAGEN -->
                <div class="course-image">

                    <?php if ($rutaImagen !== ''): ?>

                        <img
                            src="<?php echo escaparCurso($rutaImagen); ?>"
                            alt="Imagen del curso <?php echo $nombre; ?>"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >

                        <div class="course-no-image" style="display:none;">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>

                    <?php else: ?>

                        <div class="course-no-image">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>

                    <?php endif; ?>

                </div>

                <!-- INFORMACIÓN DEL CURSO -->
                <div class="course-information">

                    <span class="course-category">
                        <?php echo $categoria; ?>
                    </span>

                    <h1><?php echo $nombre; ?></h1>

                    <?php if (trim(strip_tags($descripcion)) !== ''): ?>
                        <p class="course-description">
                            <?php echo nl2br($descripcion); ?>
                        </p>
                    <?php else: ?>
                        <p class="course-description">
                            Conoce los detalles de esta propuesta formativa
                            y regístrate para comenzar tu experiencia
                            de aprendizaje en INADI.
                        </p>
                    <?php endif; ?>

                    <!-- DATOS DEL CURSO -->
                    <div class="course-data">

                        <?php if ($modalidad !== ''): ?>
                            <div class="data-item">
                                <i class="fa-solid fa-display"></i>
                                <div>
                                    <small>Modalidad</small>
                                    <strong><?php echo $modalidad; ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($duracion !== ''): ?>
                            <div class="data-item">
                                <i class="fa-regular fa-clock"></i>
                                <div>
                                    <small>Duración</small>
                                    <strong><?php echo $duracion; ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($costo !== ''): ?>
                            <div class="data-item">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <div>
                                    <small>Costo</small>
                                    <strong>$<?php echo $costo; ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($cupo !== ''): ?>
                            <div class="data-item">
                                <i class="fa-solid fa-users"></i>
                                <div>
                                    <small>Cupos</small>
                                    <strong><?php echo $cupo; ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- ESTADO DEL CURSO -->
                    <?php if ($estado !== ''): ?>
                        <div class="course-status">
                            <i class="fa-solid fa-circle"></i>
                            <?php echo $estado; ?>
                        </div>
                    <?php endif; ?>

                    <!-- BOTÓN REGISTRARME -->
                    <a
                        href="matricularme.php?id=<?php echo (int) $idCurso; ?>"
                        class="btn-registrarme"
                    >
                        <i class="fa-solid fa-user-plus"></i>
                        Registrarme
                    </a>

                </div>

            </section>

            <!-- VOLVER AL CATÁLOGO -->
            <div class="bottom-actions">
                <a href="cursosindex.php" class="back-button">
                    <i class="fa-solid fa-arrow-left"></i>
                    Explorar otros cursos
                </a>
            </div>

        </main>

    </div>

</div>

</body>
</html>
