<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once __DIR__ . '/mercadopago_config.php';
require_once __DIR__ . '/../database/consultas/conexion.php';

function mpEsc($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function mpError(string $mensaje, int $codigo = 400): void {
    http_response_code($codigo);
    $mensajeHtml = mpEsc($mensaje);
    echo '<!doctype html><html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pago INADI</title><link rel="stylesheet" href="pago.css"><main class="payment-shell"><section class="payment-card"><div class="payment-icon error"><i class="fa-solid fa-circle-exclamation"></i></div><h1>No pudimos iniciar el pago</h1><p>' . $mensajeHtml . '</p><a class="btn-secondary" href="javascript:history.back()">Volver</a></section></main></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: cursosindex.php'); exit; }
if (empty($_SESSION['inscripcion_pendiente']) || !is_array($_SESSION['inscripcion_pendiente'])) {
    mpError('No encontramos una inscripción pendiente. Volvé a completar el formulario.', 400);
}
if (!function_exists('curl_init')) { mpError('Tu PHP no tiene habilitada la extensión cURL. Activala en la configuración de XAMPP y reiniciá Apache.', 500); }
if (MP_ACCESS_TOKEN === 'PEGAR_ACCESS_TOKEN_DE_PRUEBA_AQUI' || trim(MP_ACCESS_TOKEN) === '') {
    mpError('Falta configurar el Access Token de prueba en mercadopago_config.php.');
}

$inscripcion = $_SESSION['inscripcion_pendiente'];
$idCurso = filter_var($inscripcion['id_curso'] ?? null, FILTER_VALIDATE_INT);
if (!$idCurso || $idCurso < 1) { mpError('El curso seleccionado no es válido.'); }

try {
    $sql = 'SELECT id_curso, nombre, costo, estado FROM curso WHERE id_curso = :id LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idCurso]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('INADI Mercado Pago - consulta curso: ' . $e->getMessage());
    mpError('No se pudo consultar el curso. Revisá la conexión con la base de datos.', 500);
}
if (!$curso) { mpError('El curso seleccionado ya no existe.'); }

// El importe se toma de la base de datos, nunca de un campo enviado por el navegador.
$importeRaw = str_replace(',', '.', trim((string)($curso['costo'] ?? '')));
if (!is_numeric($importeRaw) || (float)$importeRaw <= 0) {
    mpError('Este curso no tiene un precio válido para pagar en línea. Verificá el costo registrado en la base de datos.');
}
$importe = round((float)$importeRaw, 2);
$email = filter_var($inscripcion['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) { mpError('El correo de la inscripción no es válido. Volvé al formulario y corregilo.'); }

$externalReference = 'INADI-' . $idCurso . '-' . bin2hex(random_bytes(8));
$base = rtrim(MP_BASE_URL, '/');
$payload = [
    'items' => [[
        'id' => (string)$idCurso,
        'title' => 'Curso INADI: ' . (string)$curso['nombre'],
        'description' => 'Inscripción a curso de INADI',
        'quantity' => 1,
        'currency_id' => MP_CURRENCY_ID,
        'unit_price' => $importe
    ]],
    'payer' => [
        'name' => (string)($inscripcion['nombre'] ?? ''),
        'surname' => (string)($inscripcion['apellido'] ?? ''),
        'email' => $email
    ],
    'external_reference' => $externalReference,
    'back_urls' => [
        'success' => $base . '/pago_resultado.php?resultado=success',
        'pending' => $base . '/pago_resultado.php?resultado=pending',
        'failure' => $base . '/pago_resultado.php?resultado=failure'
    ],
    'auto_return' => 'approved',
    'statement_descriptor' => 'INADI CURSOS',
    'metadata' => [
        'id_curso' => (int)$idCurso,
        'nombre' => (string)($inscripcion['nombre'] ?? ''),
        'apellido' => (string)($inscripcion['apellido'] ?? ''),
        'email' => $email,
        'telefono' => (string)($inscripcion['telefono'] ?? ''),
        'modalidad' => (string)($inscripcion['modalidad'] ?? '')
    ]
];

$ch = curl_init('https://api.mercadopago.com/checkout/preferences');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . MP_ACCESS_TOKEN,
        'Content-Type: application/json',
        'X-Idempotency-Key: ' . $externalReference
    ],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 25
]);
$response = curl_exec($ch);
$curlError = curl_error($ch);
$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $curlError !== '') {
    error_log('INADI Mercado Pago - cURL: ' . $curlError);
    mpError('No pudimos conectar con Mercado Pago. Revisá tu conexión y volvé a intentar.', 502);
}
$data = json_decode($response, true);
if ($status < 200 || $status >= 300 || empty($data['init_point'])) {
    error_log('INADI Mercado Pago - API (' . $status . '): ' . substr((string)$response, 0, 2000));
    mpError('Mercado Pago no pudo crear el pago. Verificá el Access Token, la moneda y la configuración de la aplicación.', 502);
}

$_SESSION['inscripcion_pendiente']['external_reference'] = $externalReference;
$_SESSION['inscripcion_pendiente']['preference_id'] = $data['id'] ?? null;
header('Location: ' . $data['init_point'], true, 303);
exit;
