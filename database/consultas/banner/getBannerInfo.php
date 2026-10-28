<?php
// CONEXIÓN A LA BASE DE DATOS

require_once __DIR__ . '/../conexion.php';


// OBTENER LOS BANNERS

// OBTENER BANNERS ACTIVOS EN EL ORDEN CONFIGURADO

function obtenerBanner(PDO $pdo): array
{
    $sql = "
        SELECT
            curso.id_curso,
            curso.nombre,
            curso.cupo,
            curso.modalidad,
            curso.img

        FROM banner

        INNER JOIN curso
            ON banner.id_curso = curso.id_curso

        WHERE banner.estado = 'Activo'

        ORDER BY banner.orden ASC, banner.id_banner ASC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// MOSTRAR TODAS LAS IMÁGENES DE LA GALERÍA

function mostrarBanners($pdo) {
    // Obtener bann
    $bann = obtenerBanner($pdo);


    // Si no existen bann
    if (count($bann) === 0) {

        echo 'Nada para mostrar por aquí.';

        return;
    }


    // Mostrar cada curso
    foreach ($bann as $curso) {

        mostrarBanner($curso);

    }
}

// MOSTRAR UN BANNER

function mostrarBanner(array $banner): void
{
    // OBTENER DATOS

    $idCurso = (int) $banner['id_curso'];

    $nombre = htmlspecialchars(
        (string) $banner['nombre'],
        ENT_QUOTES,
        'UTF-8'
    );

    $cupo = (int) $banner['cupo'];

    $modalidad = htmlspecialchars(
        (string) $banner['modalidad'],
        ENT_QUOTES,
        'UTF-8'
    );

    $imagen = rawurlencode(
        basename((string) $banner['img'])
    );


    // RUTAS

    $urlCurso = '../Cursos/curso.php?id=' . $idCurso;

    $urlImagen = '../Cursos/imagenes/' . $imagen;


    // MOSTRAR BANNER

    echo '
        <div class="course-slide">

            <img
                src="' . $urlImagen . '"
                alt="' . $nombre . '"
            >

            <div class="course-overlay">

                <div class="course-tag">
                    Inscripciones abiertas
                </div>

                <div class="course-category">
                    ' . $modalidad . '
                </div>

                <h3>
                    ' . $nombre . '
                </h3>

                <p>
                    ' . $cupo . ' lugares disponibles
                </p>

                <a
                    class="course-button"
                    href="' . $urlCurso . '"
                >
                    Inscribirme
                </a>

            </div>

        </div>
    ';
}
// BOTONES DE CAMBIO
$num = count(obtenerBanner($pdo));
function setButtons($number){
    for ($i = 0; $i<$number; $i++){
        echo '
            <span class="carousel-dot"
                    onclick="irACurso('. $i .')"
                    role="button"
                    aria-label="Curso '. ($i + 1) .'">
            </span>
            ';
    }
}
?>