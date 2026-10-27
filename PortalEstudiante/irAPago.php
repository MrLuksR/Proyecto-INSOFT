<?php

/* ==========================================
   INADI - PREPARAR PAGO DE MATRÍCULAS
   Permite pagos individuales y múltiples
========================================== */

require_once __DIR__ . '/portal_common.php';


// 1. VALIDAR MÉTODO DE SOLICITUD

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: misCursos.php');
    exit;

}


// 2. VALIDAR SESIÓN DEL ESTUDIANTE

if (
    !$haySesionEstudiante ||
    !isset($_SESSION['usuario']) ||
    !is_object($_SESSION['usuario'])
) {

    header('Location: misCursos.php');
    exit;

}


// 3. VALIDAR CONEXIÓN

if ($errorConexion || !($pdo instanceof PDO)) {

    http_response_code(503);

    exit('No se pudo conectar con la base de datos.');

}


// 4. IDENTIFICAR TIPO DE PAGO

$tipoPago = $_POST['tipo_pago'] ?? 'individual';

if (!in_array($tipoPago, ['individual', 'multiple'], true)) {

    http_response_code(400);

    exit('Tipo de pago no válido.');

}


// 5. OBTENER IDENTIFICADOR SI ES INDIVIDUAL

$idMatricula = null;

if ($tipoPago === 'individual') {

    $idMatricula = filter_input(
        INPUT_POST,
        'id_matricula',
        FILTER_VALIDATE_INT
    );

    if (!$idMatricula || $idMatricula < 1) {

        header('Location: misCursos.php');
        exit;

    }

}


// 6. CONSULTAR MATRÍCULAS

try {

    $sql = "
        SELECT
            m.id_matricula,
            m.id_curso,
            m.estado,
            c.nombre,
            c.modalidad,
            c.duracion,
            c.costo

        FROM matricula m

        INNER JOIN curso c
            ON c.id_curso = m.id_curso

        WHERE m.nombre_usuario_estudiante = :usuario
    ";


    // SI ES INDIVIDUAL, FILTRAR POR MATRÍCULA

    if ($tipoPago === 'individual') {

        $sql .= "
            AND m.id_matricula = :id_matricula
        ";

    }


    $sql .= "
        ORDER BY m.id_matricula ASC
    ";


    $consulta = $pdo->prepare($sql);


    $parametros = [
        ':usuario' => $nombreUsuario
    ];


    if ($tipoPago === 'individual') {

        $parametros[':id_matricula'] = $idMatricula;

    }


    $consulta->execute($parametros);

    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);


    // 7. FILTRAR MATRÍCULAS PENDIENTES

    $matriculas = [];

    foreach ($resultados as $matricula) {

        $estado = mb_strtolower(
            trim((string) $matricula['estado']),
            'UTF-8'
        );


        if ($estado !== 'pendiente de pago') {
            continue;
        }


        // VALIDAR PRECIO DECIMAL DE LA BASE DE DATOS

        $costo = trim((string) $matricula['costo']);

        if (!preg_match('/^\d+(?:\.\d{1,2})?$/', $costo)) {

            throw new RuntimeException(
                'Una matrícula tiene un precio inválido.'
            );

        }


        $partes = explode('.', $costo);

        $pesos = (int) $partes[0];

        $centavos = (int) str_pad(
            $partes[1] ?? '0',
            2,
            '0',
            STR_PAD_RIGHT
        );

        $precioCentavos = ($pesos * 100) + $centavos;


        if ($precioCentavos <= 0) {

            throw new RuntimeException(
                'Una matrícula no tiene un precio válido.'
            );

        }


        $matriculas[] = [
            'id_matricula' => (int) $matricula['id_matricula'],
            'id_curso' => (int) $matricula['id_curso'],
            'nombre' => $matricula['nombre'],
            'modalidad' => $matricula['modalidad'],
            'duracion' => $matricula['duracion'],
            'precio_centavos' => $precioCentavos
        ];

    }


    // 8. VALIDAR CANTIDAD DE MATRÍCULAS

    $cantidad = count($matriculas);

    if ($tipoPago === 'individual' && $cantidad !== 1) {

        header('Location: misCursos.php');
        exit;

    }


    if ($tipoPago === 'multiple' && $cantidad < 2) {

        header('Location: misCursos.php');
        exit;

    }


    // 9. OBTENER DATOS DEL ESTUDIANTE

    $usuario = $_SESSION['usuario'];


    // 10. PREPARAR SESIÓN DE PAGO

    $_SESSION['inscripcion_pendiente'] = [

        'tipo_pago' => $tipoPago,

        'matriculas' => $matriculas,

        'nombre' => $usuario->getNombre(),

        'apellido' => $usuario->getApellido(),

        'origen' => 'mis_cursos'

    ];


    // 11. GUARDAR ORIGEN

    $_SESSION['origen_pago'] = 'mis_cursos';


    // 12. REDIRIGIR AL FORMULARIO DE PAGO

    header('Location: ../Cursos/pago.php');
    exit;

} catch (Throwable $e) {

    error_log(
        '[INADI] Error al preparar pago: '
        . $e->getMessage()
    );

    http_response_code(400);

    exit(
        'No se pudo preparar el pago. '
        . 'Verificá que las matrículas tengan precios válidos.'
    );

}