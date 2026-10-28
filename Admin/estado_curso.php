<?php

/* ==========================================================
   1. CARGAR CLASES
========================================================== */

require_once __DIR__ . '/../database/Clases/Administrativo.php';
require_once __DIR__ . '/../database/Clases/Curso.php';


// INICIAR SESIÓN

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// COMPROBAR SESIÓN ADMINISTRATIVA

if (
    !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
    $_SESSION['tipo_usuario'] !== 'administrativo' ||
    !($_SESSION['usuario'] instanceof Administrativo)
) {
    header('Location: ../login/loginn.php');
    exit;
}


/* ==========================================================
   2. CARGAR CONEXIÓN
========================================================== */

require_once __DIR__ . '/../database/consultas/conexion.php';


/* ==========================================================
   3. VALIDAR SOLICITUD
========================================================== */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=cursos');
    exit;
}


/* ==========================================================
   4. CAMBIAR ESTADO DEL CURSO
========================================================== */

try {

    // VALIDAR CSRF

    if (
        !isset($_SESSION['csrf_cursos']) ||
        !hash_equals(
            $_SESSION['csrf_cursos'],
            (string) ($_POST['csrf'] ?? '')
        )
    ) {
        throw new RuntimeException('Solicitud inválida.');
    }


    // OBTENER DATOS

    $idCurso = filter_var(
        $_POST['id_curso'] ?? null,
        FILTER_VALIDATE_INT
    );

    $accion = (string) ($_POST['accion'] ?? '');


    if ($idCurso === false || $idCurso < 1) {
        throw new InvalidArgumentException(
            'El identificador del curso no es válido.'
        );
    }


    /* ======================================================
       5. OBTENER CURSO
    ====================================================== */

    $consulta = $pdo->prepare("
        SELECT
            id_curso,
            nombre,
            modalidad,
            duracion,
            descripcion,
            costo,
            cupo,
            estado,
            img,
            id_categoria

        FROM curso

        WHERE id_curso = :id

        LIMIT 1
    ");

    $consulta->execute([
        ':id' => $idCurso
    ]);

    $datos = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        throw new RuntimeException(
            'No se encontró el curso.'
        );
    }


    /* ======================================================
       6. CREAR OBJETO CON DATOS EXISTENTES
    ====================================================== */

    $curso = new Curso(
        $datos['nombre'],
        $datos['modalidad'],
        $datos['duracion'],
        $datos['descripcion'],
        (float) $datos['costo'],
        (int) $datos['cupo'],
        $datos['estado'],
        $datos['img'],
        (int) $datos['id_categoria'],
        (int) $datos['id_curso']
    );


    /* ======================================================
       7. CAMBIAR ESTADO MEDIANTE LA CLASE
    ====================================================== */

    if ($accion === 'baja') {

        $resultado = $curso->darDeBaja($pdo);

        $mensaje = 'El curso fue dado de baja correctamente.';

    } elseif ($accion === 'alta') {

        $resultado = $curso->reactivar($pdo);

        $mensaje = 'El curso fue dado de alta correctamente.';

    } else {

        throw new InvalidArgumentException(
            'Acción no válida.'
        );
    }


    if (!$resultado) {
        throw new RuntimeException(
            'No se pudo actualizar el estado del curso.'
        );
    }


    /* ======================================================
       8. CONFIRMAR OPERACIÓN
    ====================================================== */

    $_SESSION['mensaje_cursos'] = $mensaje;

    $_SESSION['csrf_cursos'] = bin2hex(random_bytes(32));

} catch (Throwable $e) {

    error_log(
        '[INSOFT estado curso] ' . $e->getMessage()
    );

    $_SESSION['error_cursos'] =
        'No se pudo actualizar el estado del curso.';

}


/* ==========================================================
   9. REDIRECCIONAR
========================================================== */

header('Location: index.php?page=cursos');
exit;