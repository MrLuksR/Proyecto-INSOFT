<?php
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
    if (!isset($_SESSION['nombre_usuario'])){
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

        if (is_dir($carpeta) && $_SESSION['foto'] != NULL){
            $foto = $carpeta . $_SESSION['foto'];

            echo '
                <a href="../login/inicio.php"
                    class="user-icon"
                    title="Perfil de Usuario">

                    <img src="'. $foto .'" alt="Imagen de '. $_SESSION['nombre_usuario'] .'">

                </a>
            ';
        }else {
            echo '
                <a href="../login/inicio.php"
                class="user-icon"
                title="Iniciar sesión">

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
?>