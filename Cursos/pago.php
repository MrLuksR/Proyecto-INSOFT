<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once __DIR__ . '/../database/consultas/conexion.php';
function escPago($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
$inscripcion = $_SESSION['inscripcion_pendiente'] ?? null;
if (!is_array($inscripcion) || empty($inscripcion['id_curso'])) { header('Location: cursosindex.php'); exit; }
$idCurso = filter_var($inscripcion['id_curso'], FILTER_VALIDATE_INT);
if (!$idCurso || $idCurso < 1) { header('Location: cursosindex.php'); exit; }
$stmt = $pdo->prepare('SELECT id_curso, nombre, modalidad, duracion, costo FROM curso WHERE id_curso = :id LIMIT 1');
$stmt->execute([':id' => $idCurso]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$curso) { header('Location: cursosindex.php'); exit; }
$precioRaw = str_replace(',', '.', trim((string)($curso['costo'] ?? '')));
$precioValido = is_numeric($precioRaw) && (float)$precioRaw > 0;
$precio = $precioValido ? number_format((float)$precioRaw, 2, ',', '.') : 'A confirmar';
$nombreCompleto = trim(($inscripcion['nombre'] ?? '') . ' ' . ($inscripcion['apellido'] ?? ''));
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content="Resumen de pago de inscripción a cursos de INADI"><title>INADI | Confirmar pago</title><link rel="stylesheet" href="pago.css"></head><body>
<main class="payment-shell"><div class="payment-brand"><span class="payment-brand-mark"><i class="fa-solid fa-graduation-cap"></i></span><span>INADI <small style="display:block;font-size:.72rem;font-weight:500;color:#6f7288">Inscripciones y pagos</small></span></div>
<div class="payment-layout"><section class="payment-card"><span class="payment-kicker">ÚLTIMO PASO</span><h1>Continuá con tu inscripción</h1><p class="payment-subtitle">Hola <?php echo escPago($nombreCompleto); ?>. Revisá los datos antes de continuar al entorno seguro de Mercado Pago.</p>
<div class="payment-secure"><i class="fa-solid fa-shield-halved"></i><div><strong>Pago seguro</strong><br>El pago se procesa en Mercado Pago. INADI no recibe ni almacena los datos de tu tarjeta en esta página.</div></div>
<h2 style="font-size:1.05rem;margin:26px 0 4px">Datos del inscripto</h2><div class="payment-row"><span>Nombre</span><strong><?php echo escPago($nombreCompleto); ?></strong></div><div class="payment-row"><span>Correo</span><strong><?php echo escPago($inscripcion['email'] ?? ''); ?></strong></div><div class="payment-row"><span>Modalidad</span><strong><?php echo escPago($inscripcion['modalidad'] ?? $curso['modalidad'] ?? 'A confirmar'); ?></strong></div>
<?php if (!$precioValido): ?><div class="payment-secure" style="margin-top:18px;background:#fff6e5;color:#79520a"><i class="fa-solid fa-triangle-exclamation"></i><div>El curso no tiene un precio válido configurado. Contactá a INADI antes de pagar.</div></div><?php else: ?><form method="post" action="crear_preferencia.php"><button class="btn-mp" type="submit"><i class="fa-solid fa-wallet"></i> Pagar con Mercado Pago</button></form><p class="payment-note"><i class="fa-solid fa-lock"></i> Serás redirigido a Mercado Pago para completar la operación.</p><?php endif; ?>
<a class="btn-secondary" href="matricularme.php?id=<?php echo (int)$idCurso; ?>"><i class="fa-solid fa-arrow-left"></i> Volver a mis datos</a></section>
<aside class="payment-card payment-summary"><div class="payment-icon"><i class="fa-solid fa-book-open"></i></div><span class="payment-kicker">RESUMEN DEL CURSO</span><h2 class="payment-summary-title"><?php echo escPago($curso['nombre']); ?></h2><div class="payment-row"><span>Duración</span><strong><?php echo escPago($curso['duracion'] ?: 'A confirmar'); ?></strong></div><div class="payment-row"><span>Modalidad</span><strong><?php echo escPago($inscripcion['modalidad'] ?? $curso['modalidad'] ?? 'A confirmar'); ?></strong></div><div class="payment-total"><span>Total a pagar</span><strong><?php echo $precioValido ? '$ ' . $precio : $precio; ?></strong></div><div class="payment-currency">Moneda configurada: UYU</div><p class="payment-small">Si el precio de la base de datos está expresado en otra moneda, ajustá la moneda del curso y <code>MP_CURRENCY_ID</code> en la configuración antes de usar pagos reales.</p></aside></div>
<p class="payment-small" style="text-align:center;margin-top:22px">© INADI <?php echo date('Y'); ?> · Salto, Uruguay</p></main></body></html>
