<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once __DIR__ . '/mercadopago_config.php';
function escResultado($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
$paymentId = trim((string)($_GET['payment_id'] ?? $_GET['collection_id'] ?? ''));
$mensaje = 'Volviste desde Mercado Pago, pero todavía no pudimos confirmar el estado de tu pago.';
$estado = 'Sin verificar';
$icono = 'fa-circle-info'; $clase = '';
$detalle = 'No consideres la inscripción confirmada hasta que INADI verifique el pago.';
if ($paymentId !== '' && ctype_digit($paymentId) && MP_ACCESS_TOKEN !== 'PEGAR_ACCESS_TOKEN_DE_PRUEBA_AQUI' && function_exists('curl_init')) {
    $ch = curl_init('https://api.mercadopago.com/v1/payments/' . rawurlencode($paymentId));
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . MP_ACCESS_TOKEN], CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 20]);
    $response = curl_exec($ch); $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $payment = is_string($response) ? json_decode($response, true) : null;
    if ($http >= 200 && $http < 300 && is_array($payment)) {
        $estado = (string)($payment['status'] ?? 'unknown');
        if ($estado === 'approved') { $mensaje = 'Mercado Pago informa que el pago fue aprobado.'; $icono = 'fa-circle-check'; $clase = 'success'; $detalle = 'Guardá el número de operación: ' . $paymentId . '. Esta pantalla no crea por sí sola una matrícula en la base de datos; la confirmación académica debe integrarse con la tabla de matrículas de INADI.'; }
        elseif ($estado === 'pending' || $estado === 'in_process') { $mensaje = 'Tu pago está pendiente de confirmación.'; $detalle = 'Cuando Mercado Pago confirme el resultado, INADI podrá verificarlo. No repitas el pago si ya aparece un movimiento en tu cuenta.'; }
        else { $mensaje = 'Mercado Pago informa que el pago no fue aprobado.'; $icono = 'fa-circle-xmark'; $clase = 'error'; $detalle = 'Podés volver a intentar el pago. Si ves un débito en tu cuenta, consultá el estado con Mercado Pago antes de repetir la operación.'; }
    }
}
if ($paymentId === '' && ($_GET['resultado'] ?? '') === 'failure') { $mensaje = 'No se completó el pago.'; $icono = 'fa-circle-xmark'; $clase = 'error'; $detalle = 'Podés volver al resumen y probar nuevamente.'; }
if ($paymentId === '' && ($_GET['resultado'] ?? '') === 'pending') { $mensaje = 'El pago quedó pendiente.'; $detalle = 'El estado definitivo debe confirmarse en Mercado Pago.'; }
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>INADI | Estado del pago</title><link rel="stylesheet" href="pago.css"></head><body><main class="payment-shell"><div class="payment-brand"><span class="payment-brand-mark"><i class="fa-solid fa-graduation-cap"></i></span><span>INADI <small style="display:block;font-size:.72rem;font-weight:500;color:#6f7288">Inscripciones y pagos</small></span></div><section class="payment-card payment-result"><div class="payment-icon <?php echo escResultado($clase); ?>"><i class="fa-solid <?php echo escResultado($icono); ?>"></i></div><span class="payment-kicker">ESTADO DE LA OPERACIÓN</span><h1><?php echo escResultado($mensaje); ?></h1><span class="payment-status">Estado: <?php echo escResultado($estado); ?></span><p><?php echo escResultado($detalle); ?></p><?php if ($paymentId !== ''): ?><p class="payment-small">Identificador de pago: <?php echo escResultado($paymentId); ?></p><?php endif; ?><a class="btn-mp" href="cursosindex.php">Volver a los cursos</a><?php if (!empty($_SESSION['inscripcion_pendiente']['id_curso'])): ?><a class="btn-secondary" href="pago.php">Volver al resumen de pago</a><?php endif; ?></section></main></body></html>
