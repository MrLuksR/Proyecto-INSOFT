<?php
/**
 * INSOFT - RESULTADO DE MERCADO PAGO (SIN WEBHOOK)
 * Ubicación: Cursos/pago_resultado.php
 * Requiere: config/mercadoPago.php y columnas pagos.referencia_externa, pagos.moneda.
 * Contrato: procesarPagoMP.php guarda referencias <operacion>:<id_matricula>
 * y envía external_reference=<operacion> a Mercado Pago.
 */
declare(strict_types=1);

require_once __DIR__ . '/../database/Clases/Estudiante.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../auth/mercadoPago.php';
require_once __DIR__ . '/../database/consultas/conexion.php';
// Ajustá esta ruta a donde guardaste enviarCorreo() y plantillaCorreo().
require_once __DIR__ . '/../Mailer/enviarCorreo.php';

function escResultado(mixed $valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function centavosResultado(mixed $importe): ?int
{
    $valor = trim((string) $importe);
    if (!preg_match('/^\d+(?:\.\d{1,2})?$/', $valor)) {
        return null;
    }
    [$pesos, $centavos] = array_pad(explode('.', $valor, 2), 2, '0');
    return ((int) $pesos * 100) + (int) str_pad($centavos, 2, '0');
}

/**
 * Devuelve un correo en texto plano. El correo cifrado de usuarios NO debe
 * utilizarse directamente: si tu clase lo devuelve cifrado, integrá aquí
 * tu función de descifrado existente.
 */
function correoParaComprobante(): ?string
{
    $candidatos = [
        $_SESSION['correo_descifrado'] ?? null,
        $_SESSION['correo'] ?? null,
    ];

    $usuario = $_SESSION['usuario'] ?? null;
    if ($usuario instanceof Estudiante && method_exists($usuario, 'getCorreo')) {
        $candidatos[] = $usuario->getCorreo();
    }

    foreach ($candidatos as $candidato) {
        if (is_string($candidato) && filter_var(trim($candidato), FILTER_VALIDATE_EMAIL)) {
            return trim($candidato);
        }
    }
    return null;
}

$paymentId = trim((string) ($_GET['payment_id'] ?? $_GET['collection_id'] ?? ''));
$mensaje = 'Todavía no pudimos verificar tu pago.';
$estado = 'Sin verificar';
$icono = 'fa-circle-info';
$clase = '';
$detalle = 'Tu matrícula seguirá pendiente hasta que se confirme el pago.';
$enviarComprobante = false;
$datosComprobante = [];

try {
    if ($paymentId === '' || !ctype_digit($paymentId)) {
        throw new RuntimeException('No se recibió un identificador de pago válido.');
    }
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException('La conexión con la base de datos no está disponible.');
    }
    if (!function_exists('curl_init')) {
        throw new RuntimeException('La extensión cURL no está habilitada.');
    }
    if (!defined('MP_ACCESS_TOKEN') || MP_ACCESS_TOKEN === '') {
        throw new RuntimeException('No se configuró Mercado Pago.');
    }

    // Nunca aceptar como prueba una operación de producción.
    $ch = curl_init('https://api.mercadopago.com/v1/payments/' . rawurlencode($paymentId));
    if ($ch === false) {
        throw new RuntimeException('No se pudo iniciar la verificación.');
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . MP_ACCESS_TOKEN,
            'Accept: application/json',
        ],
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
    ]);
    $respuesta = curl_exec($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!is_string($respuesta) || $http !== 200) {
        throw new RuntimeException('La API no confirmó la operación. HTTP ' . $http);
    }
    $pagoMP = json_decode($respuesta, true);
    if (!is_array($pagoMP) || (string) ($pagoMP['id'] ?? '') !== $paymentId) {
        throw new RuntimeException('La respuesta de Mercado Pago no coincide.');
    }
    // Excepción limitada a las dos cuentas de prueba verificadas.
    // ACTIVAR ÚNICAMENTE en el Apache local de desarrollo.
    $permitirCuentasPrueba = getenv('INSOFT_MP_TEST_USERS_ONLY') === '1';
    $esCuentaPruebaConocida =
        (string) ($pagoMP['collector_id'] ?? '') === '3798133546' &&
        (string) ($pagoMP['payer']['id'] ?? '') === '3798133548';
    $liveMode = $pagoMP['live_mode'] ?? null;
    $estadoMP = (string) ($pagoMP['status'] ?? '');

    // live_mode=true NO demuestra que el pago sea de prueba.
    // La excepción solo se admite con opt-in y ambos IDs exactos.
    if ($liveMode !== false && !($permitirCuentasPrueba && $esCuentaPruebaConocida)) {
        throw new RuntimeException('Operación fuera del entorno de pruebas autorizado.');
    }

    if ($permitirCuentasPrueba && $esCuentaPruebaConocida && $liveMode !== false) {
        error_log('[INSOFT][MP] Excepción local de cuentas de prueba aplicada; live_mode=' . var_export($liveMode, true));
    }

    $estadoMP = (string) ($pagoMP['status'] ?? '');
    $referencia = (string) ($pagoMP['external_reference'] ?? '');
    $moneda = strtoupper((string) ($pagoMP['currency_id'] ?? ''));
    $importeMP = centavosResultado($pagoMP['transaction_amount'] ?? '');

    if ($referencia === '' || $moneda !== 'UYU' || $importeMP === null || $importeMP <= 0) {
        throw new RuntimeException('La operación no contiene datos de cobro válidos.');
    }
    if (!preg_match('/^[A-Za-z0-9_-]{8,90}$/', $referencia)) {
        throw new RuntimeException('La referencia de la operación no es válida.');
    }

    // El procesamiento debe usar referencias <operacion>:<id_matricula>.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    $buscar = $pdo->prepare(
        'SELECT p.id_pago, p.id_matricula, p.monto, p.moneda, p.estado,
                p.ref_pasarela, p.referencia_externa,
                m.nombre_usuario_estudiante, m.estado AS estado_matricula
         FROM pagos p
         INNER JOIN matricula m ON m.id_matricula = p.id_matricula
         WHERE p.referencia_externa LIKE :prefijo
         FOR UPDATE'
    );
    // Escapar comodines de LIKE; el formato permitido evita %, _ y barras invertidas.
    $buscar->execute([':prefijo' => str_replace('_', '\\_', $referencia) . ':%']);
    $filas = $buscar->fetchAll(PDO::FETCH_ASSOC);

    if (!$filas) {
        throw new RuntimeException('No existe un intento de pago local para esta operación.');
    }

    // Identificar al estudiante desde la sesión utilizada por el portal.
    $usuarioSesion = trim((string) ($_SESSION['nombre_usuario'] ?? ''));
    if ($usuarioSesion === '' &&
        isset($_SESSION['usuario']) &&
        $_SESSION['usuario'] instanceof Estudiante) {
        $usuarioSesion = trim((string) $_SESSION['usuario']->getNombreUsuario());
    }
    if ($usuarioSesion === '' ||
        strtolower(trim((string) ($_SESSION['tipo_usuario'] ?? ''))) !== 'estudiante') {
        throw new RuntimeException('La sesión del estudiante no está disponible.');
    }

    $totalLocal = 0;
    $idsVistos = [];
    foreach ($filas as $fila) {
        $idMatricula = (int) $fila['id_matricula'];
        if ((string) $fila['referencia_externa'] !== $referencia . ':' . $idMatricula) {
            throw new RuntimeException('Se detectó una referencia de matrícula inesperada.');
        }
        if (isset($idsVistos[$idMatricula])) {
            throw new RuntimeException('La operación contiene matrículas duplicadas.');
        }
        $idsVistos[$idMatricula] = true;
        if (!hash_equals((string) $fila['nombre_usuario_estudiante'], $usuarioSesion)) {
            throw new RuntimeException('El pago no corresponde al estudiante autenticado.');
        }
        if (strtoupper((string) $fila['moneda']) !== 'UYU') {
            throw new RuntimeException('La moneda de una matrícula no coincide.');
        }
        $centavos = centavosResultado($fila['monto']);
        if ($centavos === null || $centavos <= 0) {
            throw new RuntimeException('Hay un importe local inválido.');
        }
        $totalLocal += $centavos;
        if (!empty($fila['ref_pasarela']) && (string) $fila['ref_pasarela'] !== $paymentId) {
            throw new RuntimeException('El intento de pago pertenece a otra operación.');
        }
    }
    if ($totalLocal !== $importeMP) {
        throw new RuntimeException('El importe no coincide con las matrículas.');
    }

    // Evitar que el mismo ID de Mercado Pago se asigne a otra operación local.
    $duplicado = $pdo->prepare(
        'SELECT COUNT(*) FROM pagos WHERE ref_pasarela = :id
         AND referencia_externa NOT LIKE :prefijo'
    );
    $duplicado->execute([
        ':id' => $paymentId,
        ':prefijo' => str_replace('_', '\\_', $referencia) . ':%',
    ]);
    if ((int) $duplicado->fetchColumn() > 0) {
        throw new RuntimeException('Este identificador ya está asociado a otra operación.');
    }

    $mapaEstados = [
        'approved' => 'confirmado',
        'pending' => 'pendiente',
        'in_process' => 'pendiente',
        'rejected' => 'rechazado',
        'cancelled' => 'cancelado',
        'refunded' => 'reembolsado',
        'charged_back' => 'contracargo',
    ];

    if (!isset($mapaEstados[$estadoMP])) {
        throw new RuntimeException('Estado de Mercado Pago no reconocido.');
    }

    // Sólo enviar al producirse una confirmación nueva.
    $enviarComprobante = $estadoMP === 'approved' && count(array_filter(
        $filas, static fn(array $fila): bool => strtolower(trim((string) $fila['estado'])) !== 'confirmado'
    )) > 0;

    if (isset($mapaEstados[$estadoMP])) {
        $actualizarPago = $pdo->prepare(
            'UPDATE pagos SET estado = :estado, ref_pasarela = :ref,
             metodo_pago = :metodo WHERE id_pago = :id'
        );
        $activarMatricula = $pdo->prepare(
            "UPDATE matricula SET estado = 'Activa'
             WHERE id_matricula = :id AND LOWER(TRIM(estado)) = 'pendiente de pago'"
        );
        foreach ($filas as $fila) {
            // No degradar un pago ya confirmado por un retorno atrasado.
            if (strtolower((string) $fila['estado']) === 'confirmado' && $estadoMP !== 'approved') {
                continue;
            }
            $actualizarPago->execute([
                ':estado' => $mapaEstados[$estadoMP],
                ':ref' => $paymentId,
                ':metodo' => 'Mercado Pago (prueba)',
                ':id' => $fila['id_pago'],
            ]);
            if ($estadoMP === 'approved') {
                $activarMatricula->execute([':id' => $fila['id_matricula']]);
            }
        }
    }
    $pdo->commit();

    // Enviar fuera de la transacción: un fallo SMTP no revierte el pago.
    if ($enviarComprobante) {
        $correo = correoParaComprobante();
        if ($correo === null) {
            error_log('[INSOFT][Comprobante] No se encontró correo descifrado válido para ' . $usuarioSesion);
        } else {
            $filasHtml = '';
            foreach ($filas as $fila) {
                $idMatricula = (int) $fila['id_matricula'];
                $monto = number_format((float) $fila['monto'], 2, ',', '.');
                $filasHtml .= '<tr><td style="padding:9px;border-bottom:1px solid #e5e7eb">Matrícula #' . $idMatricula . '</td>'
                    . '<td style="padding:9px;border-bottom:1px solid #e5e7eb;text-align:right">$ ' . $monto . ' UYU</td></tr>';
            }
            $importeTotal = number_format($importeMP / 100, 2, ',', '.');
            $idSeguro = escResultado($paymentId);
            $fechaSegura = escResultado(date('d/m/Y H:i'));
            $mensajeCorreo = '<strong>Tu pago fue aprobado.</strong> Este es tu comprobante de operación:'
                . '<br><br><strong>Número de operación:</strong> ' . $idSeguro
                . '<br><strong>Fecha de confirmación:</strong> ' . $fechaSegura
                . '<br><strong>Medio:</strong> Mercado Pago'
                . '<br><strong>Estado:</strong> Confirmado'
                . '<br><br><table style="width:100%;border-collapse:collapse">' . $filasHtml . '</table>'
                . '<p><strong>Total abonado: $ ' . $importeTotal . ' UYU</strong></p>';
            try {
                $enviado = enviarCorreo(
                    $correo,
                    'INADI - Comprobante de pago #' . $paymentId,
                    plantillaCorreo('Comprobante de pago', $mensajeCorreo, 'Conservá este mensaje como constancia de tu operación.')
                );
                if (!$enviado) {
                    error_log('[INSOFT][Comprobante] No se pudo enviar comprobante del pago ' . $paymentId);
                }
            } catch (Throwable $errorCorreo) {
                error_log('[INSOFT][Comprobante] ' . $errorCorreo->getMessage());
            }
        }
    }


    $estado = $estadoMP;
    if ($estadoMP === 'approved') {
        $mensaje = '¡Pago aprobado!';
        $icono = 'fa-circle-check';
        $clase = 'success';
        $detalle = 'Las matrículas asociadas a esta operación se actualizaron en INSOFT.';
    } elseif (in_array($estadoMP, ['pending', 'in_process'], true)) {
        $mensaje = 'Tu pago todavía está pendiente.';
        $detalle = 'Las matrículas no se activarán hasta que Mercado Pago confirme la aprobación.';
    } else {
        $mensaje = 'El pago no fue aprobado.';
        $icono = 'fa-circle-xmark';
        $clase = 'error';
        $detalle = 'Podés volver al resumen y realizar una nueva simulación.';
    }
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('[INSOFT][Resultado MP] ' . $e->getMessage());
    $mensaje = 'No se pudo confirmar el pago.';
    $detalle = 'No se modificaron las matrículas durante esta verificación. Revisá la sesión, la configuración y los registros del servidor.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INADI | Estado del pago</title>
    <link rel="stylesheet" href="pago.css">
</head>
<body>
<main class="payment-shell">
    <div class="payment-brand">
        <span class="payment-brand-mark"><i class="fa-solid fa-graduation-cap"></i></span>
        <span>INADI <small style="display:block;font-size:.72rem;font-weight:500;color:#6f7288">Inscripciones y pagos</small></span>
    </div>
    <section class="payment-card payment-result">
        <div class="payment-icon <?= escResultado($clase) ?>">
            <i class="fa-solid <?= escResultado($icono) ?>"></i>
        </div>
        <span class="payment-kicker">ESTADO DE LA OPERACIÓN · PRUEBA</span>
        <h1><?= escResultado($mensaje) ?></h1>
        <span class="payment-status">Estado: <?= escResultado($estado) ?></span>
        <p><?= escResultado($detalle) ?></p>
        <?php if ($paymentId !== ''): ?>
            <p class="payment-small">Identificador de pago: <?= escResultado($paymentId) ?></p>
        <?php endif; ?>
        <a class="btn-mp" href="../PortalEstudiante/misCursos.php">Volver a mis cursos</a>
        <?php if (!empty($_SESSION['inscripcion_pendiente'])): ?>
            <a class="btn-secondary" href="pago.php">Volver al resumen de pago</a>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
