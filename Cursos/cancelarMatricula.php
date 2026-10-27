<?php

/* =====================================================
   INADI - ELIMINAR MATRÍCULA
===================================================== */

// DEPENDENCIAS
require_once __DIR__ . '/../database/Clases/Estudiante.php';
require_once __DIR__ . '/../database/consultas/conexion.php';


// INICIAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// VALIDAR MÉTODO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cursosindex.php');
    exit;
}


// VALIDAR ESTUDIANTE AUTENTICADO
if (
    !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
    $_SESSION['tipo_usuario'] !== 'estudiante' ||
    !($_SESSION['usuario'] instanceof Estudiante)
) {
    header('Location: ../auth/loginn.php');
    exit;
}


// VALIDAR TOKEN CSRF
if (
    !isset($_SESSION['csrf_matricula'], $_POST['csrf_token']) ||
    !is_string($_POST['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_matricula'],
        $_POST['csrf_token']
    )
) {
    http_response_code(403);
    exit('Solicitud no autorizada.');
}


// OBTENER IDENTIFICADOR DEL CURSO
$idCurso = filter_input(
    INPUT_POST,
    'id_curso',
    FILTER_VALIDATE_INT
);


// VALIDAR IDENTIFICADOR
if (!$idCurso || $idCurso < 1) {
    header('Location: cursosindex.php');
    exit;
}


// OBTENER ESTUDIANTE AUTENTICADO
$estudiante = $_SESSION['usuario'];

$nombreUsuario = $estudiante->getNombreUsuario();


try {

    // INICIAR TRANSACCIÓN
    $pdo->beginTransaction();


    // BUSCAR MATRÍCULA DEL ESTUDIANTE
    $sql = "
        SELECT
            id_matricula,
            estado

        FROM matricula

        WHERE nombre_usuario_estudiante = :usuario
          AND id_curso = :curso

        LIMIT 1

        FOR UPDATE
    ";

    $consulta = $pdo->prepare($sql);

    $consulta->execute([
        ':usuario' => $nombreUsuario,
        ':curso' => $idCurso
    ]);

    $matricula = $consulta->fetch(PDO::FETCH_ASSOC);


    // COMPROBAR EXISTENCIA
    if (!$matricula) {

        $pdo->rollBack();

        header('Location: matricularme.php?id=' . $idCurso);
        exit;

    }


    // VALIDAR ESTADO
    $estado = mb_strtolower(
        trim((string) $matricula['estado']),
        'UTF-8'
    );


    if ($estado !== 'pendiente de pago') {

        $pdo->rollBack();

        $_SESSION['error_matricula'] =
            'No se puede cancelar una matrícula que ya fue pagada.';

        header('Location: matricularme.php?id=' . $idCurso);
        exit;

    }


    // ELIMINAR MATRÍCULA
    $sql = "
        DELETE FROM matricula

        WHERE id_matricula = :id_matricula
          AND nombre_usuario_estudiante = :usuario
          AND estado = :estado
    ";

    $eliminar = $pdo->prepare($sql);

    $eliminar->execute([
        ':id_matricula' => $matricula['id_matricula'],
        ':usuario' => $nombreUsuario,
        ':estado' => $matricula['estado']
    ]);


    // CONFIRMAR ELIMINACIÓN
    if ($eliminar->rowCount() !== 1) {
        throw new RuntimeException(
            'No se pudo eliminar la matrícula.'
        );
    }


    $pdo->commit();


    // INVALIDAR TOKEN
    unset($_SESSION['csrf_matricula']);


    // LIMPIAR INSCRIPCIÓN PENDIENTE SI CORRESPONDE
    if (
        isset($_SESSION['inscripcion_pendiente']['id_curso']) &&
        (int) $_SESSION['inscripcion_pendiente']['id_curso'] === $idCurso
    ) {
        unset($_SESSION['inscripcion_pendiente']);
    }


    // MENSAJE DE CONFIRMACIÓN
    $_SESSION['exito_matricula'] =
        'Tu matrícula fue cancelada correctamente.';


    // REGRESAR AL FORMULARIO
    header('Location: matricularme.php?id=' . $idCurso);
    exit;

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        '[INADI] Error al eliminar matrícula: '
        . $e->getMessage()
    );

    $_SESSION['error_matricula'] =
        'No se pudo cancelar la matrícula. '
        . 'Si tiene un pago asociado, contactá a INADI.';

    header('Location: matricularme.php?id=' . $idCurso);
    exit;

}