<?php
function setMatriculaBtn(){
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

function setLogin(){
    if (!isset($_SESSION['nombre_usuario'])){
        echo '
            <a href="../Bienvenido/bienvenidoindex.html"
              class="user-icon"
              title="Iniciar sesión">

               <i class="fa-regular fa-user"></i>

           </a>
        ';
    }else{
        echo '
            <a href="../login/inicio.php"
              class="user-icon"
              title="Iniciar sesión">

               <i class="fa-regular fa-user"></i>

           </a>
        ';
    }
}
?>