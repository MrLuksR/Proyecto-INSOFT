<?php
include("../database/Clases/Estudiante.php");
function setMatriculaBtn(){ // Bloquea la visión del botón de Matrícula si no iniciaste sesión
    echo '
    <li>

        <a href="../Matricula/matricula.php">

            <i class="fa-solid fa-file-signature"></i>

            <span>
                Matrícula
            </span>

        </a>

    </li>
    ';
}

function setLogin(){ // Si ya iniciaste sesión, te manda a la misma para poder cerrarla
    if (!isset($_SESSION['usuario'])){
        echo '
            <a href="../Bienvenido/bienvenidoindex.php"
              class="user-icon"
              title="Iniciar sesión">

               <i class="fa-regular fa-user"></i>

           </a>
        ';
    }else{
        // Evaluar si existe por lo menos una foto de perfil
        $carpeta = "../database/consultas/estudiantes/fotosEst/";
        $estudiante = $_SESSION['usuario'];

        if (is_dir($carpeta) && !empty($estudiante->getFoto())){
            $foto = $carpeta . $estudiante->getFoto();

            echo '
                <a href="../login/inicio.php"
                    class="user-icon"
                    title="Perfil de Usuario">

                    <img src="'. $foto .'" alt="Foto de perfil'. $estudiante->getNombreUsuario() .'">

                </a>
            ';
        }else {
            echo '
                <a href="../login/inicio.php"
                class="user-icon"
                title="Sesión de"'. $estudiante->getNombreUsuario() .'>

                <i class="fa-regular fa-user"></i>

            </a>
            ';
        }
    }
}

function setTray(){ // Establece los años de trayectoria del instituto de forma automática
    // 1. Definir la fecha de inicio (enero de 1992)
    $fechaInicio = new DateTime('1992-01-01');

    // 2. Definir la fecha actual
    $fechaActual = new DateTime();

    // 3. Calcular la diferencia entre ambas fechas
    $diferencia = $fechaInicio->diff($fechaActual);

    // 4. Obtener y mostrar únicamente los años transcurridos
    echo $diferencia->y;
}

function getNombreUsuariosEstudiantes($pdo, $nombre_usuario) {
    $sql = "SELECT nombre_usuario 
            FROM usuarios
            WHERE nombre_usuario = :nombre_usuario;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre_usuario' => $nombre_usuario
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function setFotoUser($estudiante, $foto){ // Establecer fotos en el Portal estudiantil
    switch($foto){
        case 1: // Foto pequeña place-holder
            setElement($estudiante, 'photo-placeholder', '<div class="photo-placeholder"></div>');
            break;
        case 2: // Foto grande credencial
            setElement($estudiante, 'credential-photo',
                    '<div class="credential-photo">
                        <span>
                            SIN FOTO DE PERFIL
                        </span>
                    </div>');
            break;
    }
}

function setElement($estudiante, $className, $elemento){ // Establece el elemento de html correspondiente a si tiene foto o no
    if (!empty($estudiante->getFoto()))
        echo '<img src="../database/consultas/estudiantes/fotosEst/'. $estudiante->getFoto() . '" class="'. $className .'" alt="Foto de perfil '. $estudiante->getNombreUsuario() .'">';
    else
        echo $elemento;
}
?>