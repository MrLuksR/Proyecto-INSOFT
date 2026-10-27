<?php
/** INSOFT - Checkout Pro, únicamente para pagos de prueba. */
declare(strict_types=1);

require_once __DIR__ . '/../database/Clases/Estudiante.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function fallarPago(int $codigo, string $mensaje): never
{
    http_response_code($codigo);
    header('Content-Type: text/plain; charset=utf-8');
    exit($mensaje);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Cursos/pago.php', true, 303);
    exit;
}

$csrfEnviado = (string) ($_POST['csrf_pago_mp'] ?? '');
$csrfSesion = (string) ($_SESSION['csrf_pago_mp'] ?? '');
if ($csrfEnviado === '' || $csrfSesion === '' || !hash_equals($csrfSesion, $csrfEnviado)) {
    fallarPago(403, 'Solicitud inválida. Volvé al resumen e intentá nuevamente.');
}

require_once __DIR__ . '/../auth/mercadoPago.php';
error_log(
    '[INSOFT DEBUG] Token cargado: ' .
    (defined('MP_ACCESS_TOKEN') && MP_ACCESS_TOKEN !== ''
        ? 'SI'
        : 'NO')
);
require_once __DIR__ . '/../database/consultas/conexion.php';

if (!isset($pdo) || !$pdo instanceof PDO) {
    fallarPago(503, 'No se pudo conectar con la base de datos.');
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario instanceof Estudiante ||
    strtolower(trim((string) ($_SESSION['tipo_usuario'] ?? ''))) !== 'estudiante') {
    fallarPago(401, 'Debés iniciar sesión como estudiante.');
}
$nombreUsuario = trim((string) $usuario->getNombreUsuario());
if ($nombreUsuario === '') {
    fallarPago(401, 'La sesión del estudiante no es válida.');
}

$inscripcion = $_SESSION['inscripcion_pendiente'] ?? null;
if (!is_array($inscripcion) || ($inscripcion['origen'] ?? '') !== 'mis_cursos') {
    fallarPago(422, 'Esta modalidad todavía no tiene matrículas asociadas.');
}
$solicitadas = $inscripcion['matriculas'] ?? null;
if (!is_array($solicitadas) || !$solicitadas) {
    fallarPago(422, 'No hay matrículas seleccionadas.');
}
$ids = [];
foreach ($solicitadas as $fila) {
    $id = filter_var($fila['id_matricula'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1 || isset($ids[$id])) {
        fallarPago(422, 'La selección de matrículas no es válida.');
    }
    $ids[$id] = $id;
}
if (count($ids) > 30) {
    fallarPago(422, 'Demasiadas matrículas para una operación.');
}

$baseUrl = rtrim((string) (getenv('MP_BASE_URL') ?: ''), '/');
if (!filter_var($baseUrl, FILTER_VALIDATE_URL) || !str_starts_with($baseUrl, 'https://')) {
    fallarPago(503, 'Configurá MP_BASE_URL con una dirección HTTPS pública.');
}
if (!defined('MP_ACCESS_TOKEN') || MP_ACCESS_TOKEN === '') {
    fallarPago(503, 'Falta configurar el Access Token.');
}
if (!function_exists('curl_init')) {
    fallarPago(503, 'La extensión PHP cURL no está habilitada.');
}

try {
    $marcadores = implode(',', array_fill(0, count($ids), '?'));
    $consulta = $pdo->prepare(
        "SELECT m.id_matricula, m.estado, c.nombre, c.costo
         FROM matricula m
         INNER JOIN curso c ON c.id_curso = m.id_curso
         WHERE m.nombre_usuario_estudiante = ? AND m.id_matricula IN ($marcadores)"
    );
    $consulta->execute(array_merge([$nombreUsuario], array_values($ids)));
    $filas = $consulta->fetchAll(PDO::FETCH_ASSOC);
    if (count($filas) !== count($ids)) {
        fallarPago(422, 'Una o más matrículas no pertenecen a tu cuenta.');
    }

    $items = [];
    $pagosLocales = [];
    foreach ($filas as $fila) {
        if (mb_strtolower(trim((string) $fila['estado']), 'UTF-8') !== 'pendiente de pago') {
            fallarPago(422, 'Una matrícula ya no está pendiente de pago.');
        }
        // curso.costo es DECIMAL(10,0) en la BD actual.
        $costo = trim((string) $fila['costo']);
        if (!preg_match('/^\d+$/', $costo) || (int) $costo <= 0) {
            fallarPago(422, 'Un curso tiene un costo inválido.');
        }
        $items[] = [
            'id' => 'matricula-' . $fila['id_matricula'],
            'title' => (string) $fila['nombre'],
            'quantity' => 1,
            'currency_id' => 'UYU',
            'unit_price' => (int) $costo,
        ];
        $pagosLocales[] = [
            'id_matricula' => (int) $fila['id_matricula'],
            'monto' => number_format((float) $costo, 2, '.', ''),
        ];
    }

    $operacion = 'INSOFT-' . bin2hex(random_bytes(12));
    $urlResultado = $baseUrl . '/Cursos/pago_resultado.php';
    $preferencia = [
        'items' => $items,
        'external_reference' => $operacion,
        'back_urls' => [
            'success' => $urlResultado,
            'pending' => $urlResultado,
            'failure' => $urlResultado,
        ],
        'auto_return' => 'approved',
    ];

    $ch = curl_init('https://api.mercadopago.com/checkout/preferences');
    if ($ch === false) {
        throw new RuntimeException('No se pudo iniciar cURL.');
    }
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . MP_ACCESS_TOKEN,
            'Content-Type: application/json',
            'X-Idempotency-Key: ' . $operacion,
        ],
        CURLOPT_POSTFIELDS => json_encode($preferencia, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
    ]);
    $respuesta = curl_exec($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errorCurl = curl_error($ch);
    curl_close($ch);
    if (!is_string($respuesta) || $http < 200 || $http >= 300) {
        throw new RuntimeException('Error de preferencia MP HTTP ' . $http . ': ' . substr($errorCurl ?: (string) $respuesta, 0, 500));
    }
    $datos = json_decode($respuesta, true, 512, JSON_THROW_ON_ERROR);
    // INFORMACIÓN DE LA PREFERENCIA
error_log(
    '[INSOFT PREFERENCIA] live_mode = ' .
    var_export($datos['live_mode'] ?? null, true)
);

error_log(
    '[INSOFT PREFERENCIA] sandbox_init_point = ' .
    (!empty($datos['sandbox_init_point']) ? 'SI' : 'NO')
);
    $urlCheckout = (string) ($datos['sandbox_init_point'] ?? $datos['init_point'] ?? '');
    $host = strtolower((string) parse_url($urlCheckout, PHP_URL_HOST));
    if (!filter_var($urlCheckout, FILTER_VALIDATE_URL) ||
        parse_url($urlCheckout, PHP_URL_SCHEME) !== 'https' ||
        !preg_match('/(^|\.)mercadopago\.(com|com\.uy|com\.ar|com\.br|com\.mx|com\.co|cl|com\.pe)$/i', $host)) {
        throw new RuntimeException('La URL de Checkout devuelta no es válida.');
    }

    // La BD actual define pagos.ref_pasarela como NOT NULL: usar cadena vacía
    // hasta que Mercado Pago asigne un identificador de pago.
    $pdo->beginTransaction();
    $insertar = $pdo->prepare(
        "INSERT INTO pagos
         (fecha_pago, monto, metodo_pago, ref_pasarela, estado,
          id_matricula, referencia_externa, moneda)
         VALUES (CURDATE(), :monto, :metodo, '', 'pendiente',
                 :matricula, :referencia, 'UYU')"
    );
    foreach ($pagosLocales as $pago) {
        $insertar->execute([
            ':monto' => $pago['monto'],
            ':metodo' => 'Mercado Pago (prueba)',
            ':matricula' => $pago['id_matricula'],
            ':referencia' => $operacion . ':' . $pago['id_matricula'],
        ]);
    }
    $pdo->commit();
    $_SESSION['ultima_operacion_mp'] = $operacion;
    header('Location: ' . $urlCheckout, true, 303);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('[INSOFT][procesarPagoMP] ' . $e->getMessage());
    fallarPago(502, 'No se pudo iniciar el pago de prueba. Revisá los registros del servidor.');
}
