<?php

function getFoto($username)
{
    // COMPROBAR SI SE ENVIÓ UNA FOTOGRAFÍA
    if (
        !isset($_FILES['foto']) ||
        $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    // COMPROBAR ERRORES
    if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir la fotografía.');
    }

    // OBTENER ARCHIVO TEMPORAL
    $archivoTemporal = $_FILES['foto']['tmp_name'];

    // VALIDAR TAMAÑO MÁXIMO (5 MB)
    if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
        throw new Exception('La fotografía no puede superar los 5 MB.');
    }

    // VALIDAR QUE SEA UN ARCHIVO SUBIDO POR HTTP
    if (!is_uploaded_file($archivoTemporal)) {
        throw new Exception('El archivo recibido no es válido.');
    }

    // VALIDAR TIPO REAL DE IMAGEN
    $tipo = mime_content_type($archivoTemporal);

    $extensiones = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($extensiones[$tipo])) {
        throw new Exception('Formato de fotografía no permitido.');
    }

    // COMPROBAR QUE SEA UNA IMAGEN VÁLIDA
    if (@getimagesize($archivoTemporal) === false) {
        throw new Exception('La fotografía no es una imagen válida.');
    }

    // LIMPIAR NOMBRE DE USUARIO
    $textoExtra = trim($username);

    $textoExtra = preg_replace(
        '/[^a-zA-Z0-9_-]/',
        '_',
        $textoExtra
    );

    // GENERAR NOMBRE DE FOTOGRAFÍA
    $archivo = 'FotoPerfil_'
        . $textoExtra
        . '.'
        . $extensiones[$tipo];

    // DIRECTORIO DE FOTOGRAFÍAS
    $carpeta = __DIR__ . '/fotosEst/';

    // CREAR CARPETA SI NO EXISTE
    if (!is_dir($carpeta)) {

        if (!mkdir($carpeta, 0755, true) && !is_dir($carpeta)) {
            throw new Exception(
                'No se pudo crear la carpeta de fotografías.'
            );
        }
    }

    // RUTA FINAL
    $rutaFoto = $carpeta . $archivo;

    // GUARDAR FOTOGRAFÍA
    if (!move_uploaded_file($archivoTemporal, $rutaFoto)) {
        throw new Exception('No se pudo guardar la fotografía.');
    }

    // DEVOLVER ÚNICAMENTE EL NOMBRE
    return $archivo;
}

?>