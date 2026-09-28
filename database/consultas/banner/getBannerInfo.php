<?php
// CONEXIÓN A LA BASE DE DATOS

require_once __DIR__ . '/../conexion.php';


// OBTENER LOS BANNERS

function obtenerBanner($pdo) {
    $sql = "SELECT curso.nombre, curso.cupo, curso.modalidad, curso.img FROM curso INNER JOIN banner ON banner.id_curso = curso.id_curso;";

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

function mostrarBanner($banner){
    $nombre = $banner['nombre'];
    $cupo = $banner['cupo'];
    $modal = $banner['modalidad'];
    $img = $banner['img'];

    echo '
        <div class="course-slide">

            <img src="../Cursos/imagenes/'. $img . '"
                alt="'. $nombre .'">


            <div class="course-overlay">


                <div class="course-tag">

                    Inscripciones abiertas

                </div>


                <div class="course-category">

                    '. $modal .'

                </div>


                <h3>

                    '. $nombre .'

                </h3>


                <p>

                    '. $cupo .' lugares disponibles

                </p>


                <button class="course-button"
                        type="button">

                    Inscribirme

                </button>


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