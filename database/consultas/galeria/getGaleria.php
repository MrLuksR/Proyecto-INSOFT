<?php
// CONEXIÓN A LA BASE DE DATOS

require_once __DIR__ . '/../conexion.php';


// OBTENER TODAS LAS IMAGENES DE LA GALERÍA

function obtenerImagen($pdo) {
    $sql = "SELECT titulo, fecha, img FROM galeria ORDER BY fecha DESC;";

    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// MOSTRAR TODAS LAS IMÁGENES DE LA GALERÍA

function mostrarImagenes($pdo) {
    // Obtener img
    $img = obtenerImagen($pdo);


    // Si no existen img
    if (count($img) === 0) {

        echo 'Nada para mostrar por aquí.';

        return;
    }


    // Mostrar cada curso
    foreach ($img as $imagen) {

        mostrarImagen($imagen);

    }
}

function mostrarImagen($imagen){
    $titulo = $imagen['titulo'];
    $fecha = $imagen['fecha'];
    $img = $imagen['img'];

    echo '
        <div class="gallery-item">

            <img
                src="imagenes galeria/'. $img .'"
                alt="'. $titulo .'"
            >

            <div class="gallery-overlay">

                <i class="fa-solid fa-magnifying-glass-plus"></i>

                <span>
                    Ver imagen
                </span>

            </div>

        </div>
    ';
}

?>