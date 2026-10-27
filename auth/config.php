<?php

require_once __DIR__ . '/../Estructura/topbar_controls.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function setMatriculaBtn(){

    echo '
    <li>
        <a href="../Matricula/matricula.php">
            <i class="fa-solid fa-file-signature"></i>
            <span>Matrícula</span>
        </a>
    </li>
    ';
}


// =====================================================
// INICIO DE SESIÓN / ÍCONO DE USUARIO
// =====================================================

function setLogin(){
    inadiUserControl();
}


// =====================================================
// AÑOS DE TRAYECTORIA DEL INSTITUTO
// =====================================================

function setTray(){

    // Fecha de inicio de INADI.
    $fechaInicio = new DateTime('1992-01-01');

    // Fecha actual.
    $fechaActual = new DateTime();

    // Calcular los años transcurridos.
    $diferencia = $fechaInicio->diff($fechaActual);

    // Mostrar los años de trayectoria.
    echo $diferencia->y;
}


// =====================================================
// OBTENER NOMBRE DE USUARIO DE ESTUDIANTE
// =====================================================

function getNombreUsuariosEstudiantes($pdo, $nombre_usuario){

    $sql = "SELECT nombre_usuario
            FROM usuarios
            WHERE nombre_usuario = :nombre_usuario";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nombre_usuario' => $nombre_usuario
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// =====================================================
// MOSTRAR FOTO O PLACEHOLDER DEL ESTUDIANTE
// =====================================================

function setFotoUser($estudiante, $foto){

    switch ($foto) {

        case 1:

            // Foto pequeña de perfil.
            setElement(
                $estudiante,
                'photo-placeholder',
                '<div class="photo-placeholder"></div>'
            );

            break;

        case 2:

            // Foto grande para la credencial.
            setElement(
                $estudiante,
                'credential-photo',
                '<div class="credential-photo">
                    <span>SIN FOTO DE PERFIL</span>
                </div>'
            );

            break;
    }
}


// =====================================================
// MOSTRAR ELEMENTO SEGÚN EXISTA FOTO DE PERFIL
// =====================================================

function setElement($estudiante, $className, $elemento){

    $foto = method_exists($estudiante, 'getFoto')
        ? (string) $estudiante->getFoto()
        : '';

    if ($foto !== '') {

        $fotoSegura = basename($foto);

        $rutaFisica = __DIR__
            . '/../database/consultas/estudiantes/fotosEst/'
            . $fotoSegura;

        if (is_file($rutaFisica)) {

            $rutaWeb = '../database/consultas/estudiantes/fotosEst/'
                . rawurlencode($fotoSegura);

            $nombreUsuario = method_exists($estudiante, 'getNombreUsuario')
                ? (string) $estudiante->getNombreUsuario()
                : 'usuario';

            echo '
            <img
                src="' . htmlspecialchars($rutaWeb, ENT_QUOTES, 'UTF-8') . '"
                class="' . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . '"
                alt="Foto de perfil de ' . htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') . '"
            >';

            return;
        }
    }

    // Mostrar el placeholder si no existe una foto válida.
    echo $elemento;
}


// =====================================================
// BOTÓN DEL PORTAL DEL ESTUDIANTE
// =====================================================

function setPortalBtn(){

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (
        isset($_SESSION['usuario'], $_SESSION['tipo_usuario'])
        && is_object($_SESSION['usuario'])
        && strtolower(trim((string) $_SESSION['tipo_usuario'])) === 'estudiante'
    ) {

        echo '
        <a
            class="portal-button portal-estudiante"
            href="../PortalEstudiante/portalEstudiante.php"
        >
            <i class="fa-solid fa-graduation-cap"></i>
            <span>Portal Estudiantes</span>
        </a>
        ';
    }
}

?>
