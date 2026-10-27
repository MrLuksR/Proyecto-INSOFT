<?php

/* ==========================================
   INADI - FORMULARIO DE PAGO
   Pagos individuales y múltiples
========================================== */


// 1. INICIAR SESIÓN

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


// 2. TOKEN CSRF PARA INICIAR EL PAGO

if (empty($_SESSION['csrf_pago_mp'])) {
    $_SESSION['csrf_pago_mp'] = bin2hex(random_bytes(32));
}

// 3. CONEXIÓN

require_once __DIR__ . '/../database/consultas/conexion.php';


// 3. FUNCIÓN PARA PROTEGER CONTENIDO

function escPago($valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


// 4. OBTENER INSCRIPCIÓN

$inscripcion = $_SESSION['inscripcion_pendiente'] ?? null;

if (!is_array($inscripcion)) {

    header('Location: cursosindex.php');
    exit;

}


// 5. INICIALIZAR VARIABLES

$matriculas = [];

$totalCentavos = 0;

$tipoPago = $inscripcion['tipo_pago'] ?? 'individual';

$origenPago = $inscripcion['origen']
    ?? $_SESSION['origen_pago']
    ?? 'inscripcion';


// 6. COMPROBAR SI VIENE DESDE MIS CURSOS

$desdeMisCursos = (
    $origenPago === 'mis_cursos'
);


// 7. OBTENER MATRÍCULAS DEL PORTAL

if (
    $desdeMisCursos &&
    !empty($inscripcion['matriculas']) &&
    is_array($inscripcion['matriculas'])
) {

    foreach ($inscripcion['matriculas'] as $matricula) {

        $precioCentavos = (int) (
            $matricula['precio_centavos'] ?? 0
        );

        if ($precioCentavos <= 0) {
            continue;
        }


        $matriculas[] = [

            'id_matricula' => (int) $matricula['id_matricula'],

            'id_curso' => (int) $matricula['id_curso'],

            'nombre' => $matricula['nombre'],

            'modalidad' => $matricula['modalidad'],

            'duracion' => $matricula['duracion'],

            'precio_centavos' => $precioCentavos

        ];


        $totalCentavos += $precioCentavos;

    }

}


// 8. COMPATIBILIDAD CON INSCRIPCIÓN TRADICIONAL

elseif (!empty($inscripcion['id_curso'])) {

    $idCurso = filter_var(
        $inscripcion['id_curso'],
        FILTER_VALIDATE_INT
    );


    if (!$idCurso || $idCurso < 1) {

        header('Location: cursosindex.php');
        exit;

    }


    // CONSULTAR CURSO

    $consulta = $pdo->prepare("
        SELECT
            id_curso,
            nombre,
            modalidad,
            duracion,
            costo

        FROM curso

        WHERE id_curso = :id

        LIMIT 1
    ");


    $consulta->execute([
        ':id' => $idCurso
    ]);


    $curso = $consulta->fetch(PDO::FETCH_ASSOC);


    if (!$curso) {

        header('Location: cursosindex.php');
        exit;

    }


    // VALIDAR PRECIO

    $costo = trim((string) $curso['costo']);

    if (preg_match('/^\d+(?:\.\d{1,2})?$/', $costo)) {

        $partes = explode('.', $costo);

        $pesos = (int) $partes[0];

        $centavos = (int) str_pad(
            $partes[1] ?? '0',
            2,
            '0',
            STR_PAD_RIGHT
        );

        $precioCentavos = ($pesos * 100) + $centavos;

    } else {

        $precioCentavos = 0;

    }


    $matriculas[] = [

        'id_matricula' => 0,

        'id_curso' => (int) $curso['id_curso'],

        'nombre' => $curso['nombre'],

        'modalidad' => $inscripcion['modalidad']
            ?? $curso['modalidad'],

        'duracion' => $curso['duracion'],

        'precio_centavos' => $precioCentavos

    ];


    $totalCentavos = $precioCentavos;

}


// 9. VALIDAR MATRÍCULAS

if (empty($matriculas)) {

    header('Location: cursosindex.php');
    exit;

}


// 10. DETERMINAR SI ES PAGO MÚLTIPLE

$esPagoMultiple = count($matriculas) > 1;


// 11. OBTENER TOTAL

$totalValido = $totalCentavos > 0;

$totalFormateado = $totalValido
    ? number_format($totalCentavos / 100, 2, ',', '.')
    : 'A confirmar';


// 12. DATOS DEL ESTUDIANTE

$nombreCompleto = trim(
    ($inscripcion['nombre'] ?? '')
    . ' '
    . ($inscripcion['apellido'] ?? '')
);

$correo = $inscripcion['email'] ?? '';


// 13. CONFIGURAR BOTÓN DE REGRESO

if ($desdeMisCursos) {

    $urlVolver = '../PortalEstudiante/misCursos.php';

    $textoVolver = 'Volver a mis cursos';

} else {

    $idCurso = (int) $matriculas[0]['id_curso'];

    $urlVolver = 'matricularme.php?id=' . $idCurso;

    $textoVolver = 'Volver a mis datos';

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="description"
        content="Resumen de pago de inscripción a cursos de INADI"
    >

    <title>INADI | Confirmar pago</title>

    <link rel="stylesheet" href="pago.css">

</head>

<body>

<main class="payment-shell">

    <!-- MARCA -->

    <div class="payment-brand">

        <img src="../Elementos Gráficos/Logo Inadi sin Brillo.png" alt="" width="70px">

        <span>

            INADI

            <small style="
                display:block;
                font-size:.72rem;
                font-weight:500;
                color:#6f7288;
            ">
                Inscripciones y pagos
            </small>

        </span>

    </div>


    <div class="payment-layout">

        <!-- FORMULARIO PRINCIPAL -->

        <section class="payment-card">

            <span class="payment-kicker">

                <?= $esPagoMultiple
                    ? 'PAGO CONJUNTO'
                    : 'ÚLTIMO PASO' ?>

            </span>


            <h1>

                <?= $esPagoMultiple
                    ? 'Pagá tus matrículas'
                    : 'Continuá con tu inscripción' ?>

            </h1>


            <p class="payment-subtitle">

                Hola <?= escPago($nombreCompleto) ?>.

                <?= $esPagoMultiple
                    ? 'Revisá las matrículas incluidas antes de continuar al pago.'
                    : 'Revisá los datos antes de continuar al entorno de prueba de Mercado Pago.' ?>

            </p>


            <!-- INFORMACIÓN DE SEGURIDAD -->

            <div class="payment-secure">

                <i class="fa-solid fa-shield-halved"></i>

                <div>

                    <strong>
                        Pago de prueba
                    </strong>

                    <br>

                    El pago se procesa en Mercado Pago.
                    INADI no recibe ni almacena los datos
                    de tu tarjeta en esta página.

                </div>

            </div>


            <!-- DATOS DEL ESTUDIANTE -->

            <h2 style="
                font-size:1.05rem;
                margin:26px 0 4px;
            ">

                Datos del inscripto

            </h2>


            <div class="payment-row">

                <span>Nombre</span>

                <strong>
                    <?= escPago($nombreCompleto) ?>
                </strong>

            </div>


            <div class="payment-row">

                <span>Correo</span>

                <strong>
                    <?= escPago($correo ?: 'No disponible') ?>
                </strong>

            </div>


            <!-- LISTADO DE CURSOS -->

            <h2 style="
                font-size:1.05rem;
                margin:26px 0 4px;
            ">

                <?= $esPagoMultiple
                    ? 'Matrículas incluidas'
                    : 'Curso seleccionado' ?>

            </h2>


            <div class="payment-course-list">

                <?php foreach ($matriculas as $matricula): ?>

                    <div class="payment-course-item">

                        <div class="payment-course-info">

                            <strong>

                                <?= escPago($matricula['nombre']) ?>

                            </strong>


                            <small>

                                <?php if ($matricula['id_matricula'] > 0): ?>

                                    Matrícula
                                    #<?= (int) $matricula['id_matricula'] ?>

                                <?php else: ?>

                                    Inscripción al curso

                                <?php endif; ?>

                            </small>

                        </div>


                        <span class="payment-course-price">

                            <?php if ($matricula['precio_centavos'] > 0): ?>

                                $ <?= number_format(
                                    $matricula['precio_centavos'] / 100,
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            <?php else: ?>

                                A confirmar

                            <?php endif; ?>

                        </span>

                    </div>

                <?php endforeach; ?>

            </div>


            <!-- VALIDACIÓN DE PRECIO -->

            <?php if (!$totalValido): ?>

                <div
                    class="payment-secure"
                    style="
                        margin-top:18px;
                        background:#fff6e5;
                        color:#79520a;
                    "
                >

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    <div>

                        Uno o más cursos no tienen
                        un precio válido configurado.

                        Contactá a INADI antes de pagar.

                    </div>

                </div>

            <?php else: ?>

                <!-- BOTÓN DE MERCADO PAGO: PROCESAMIENTO DEL LADO DEL SERVIDOR -->

                <form
                    method="POST"
                    action="../PortalEstudiante/procesarPagoMP.php"
                    style="margin-top:25px"
                >
                    <input type="hidden" name="csrf_pago_mp" value="<?= escPago($_SESSION['csrf_pago_mp']) ?>">

                    <button
                        class="btn-mp"
                        type="submit"
                    >

                        <i class="fa-solid fa-wallet"></i>

                        <?php if ($esPagoMultiple): ?>

                            Pagar $ <?= $totalFormateado ?>
                            con Mercado Pago

                        <?php else: ?>

                            Pagar con Mercado Pago

                        <?php endif; ?>

                    </button>

                </form>


                <p class="payment-note">

                    <i class="fa-solid fa-lock"></i>

                    Serás redirigido a Mercado Pago
                    para completar la operación.

                </p>

            <?php endif; ?>


            <!-- BOTÓN DE REGRESO -->

            <a
                class="btn-secondary"
                href="<?= escPago($urlVolver) ?>"
            >

                <i class="fa-solid fa-arrow-left"></i>

                <?= escPago($textoVolver) ?>

            </a>

        </section>

        <!-- =====================================================
            RESUMEN DETALLADO DEL PAGO
        ===================================================== -->

        <aside class="payment-card payment-summary">

            <!-- ENCABEZADO -->

            <div class="payment-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

            <span class="payment-kicker">
                DETALLE DE TU COMPRA
            </span>

            <h2 class="payment-summary-title">
                Resumen del pago
            </h2>

            <p class="resumen-descripcion">
                Revisá los cursos incluidos y sus costos
                antes de confirmar el pago.
            </p>


            <!-- CANTIDAD DE CURSOS -->

            <div class="resumen-cantidad">

                <span>
                    <i class="fa-solid fa-book-open"></i>
                    Cursos seleccionados
                </span>

                <strong>
                    <?= count($matriculas) ?>
                </strong>

            </div>


            <!-- =====================================================
                DESGLOSE DE CURSOS
            ===================================================== -->

            <div class="resumen-cursos">

                <?php foreach ($matriculas as $indice => $matricula): ?>

                    <div class="resumen-curso">

                        <!-- INFORMACIÓN DEL CURSO -->

                        <div class="resumen-curso-encabezado">

                            <div class="resumen-curso-icono">
                                <i class="fa-solid fa-laptop-code"></i>
                            </div>

                            <div class="resumen-curso-informacion">

                                <span class="resumen-curso-numero">
                                    CURSO <?= $indice + 1 ?>
                                </span>

                                <h3>
                                    <?= escPago($matricula['nombre']) ?>
                                </h3>

                                <?php if ($matricula['id_matricula'] > 0): ?>

                                    <small>
                                        Matrícula
                                        #<?= (int) $matricula['id_matricula'] ?>
                                    </small>

                                <?php endif; ?>

                            </div>

                        </div>


                        <!-- DETALLES DEL CURSO -->

                        <div class="resumen-curso-detalles">

                            <div>

                                <span>
                                    <i class="fa-solid fa-desktop"></i>
                                    Modalidad
                                </span>

                                <strong>
                                    <?= escPago(
                                        $matricula['modalidad'] ?: 'A confirmar'
                                    ) ?>
                                </strong>

                            </div>


                            <div>

                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    Duración
                                </span>

                                <strong>
                                    <?= escPago(
                                        $matricula['duracion'] ?: 'A confirmar'
                                    ) ?>
                                </strong>

                            </div>

                        </div>


                        <!-- PRECIO INDIVIDUAL -->

                        <div class="resumen-curso-precio">

                            <span>
                                Costo del curso
                            </span>

                            <strong>

                                <?php if ($matricula['precio_centavos'] > 0): ?>

                                    $ <?= number_format(
                                        $matricula['precio_centavos'] / 100,
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                <?php else: ?>

                                    A confirmar

                                <?php endif; ?>

                            </strong>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


            <!-- =====================================================
                TOTAL DEL PAGO
            ===================================================== -->

            <div class="resumen-total">

                <div class="resumen-total-informacion">

                    <span>
                        Total a pagar
                    </span>

                    <small>
                        <?= count($matriculas) ?>
                        <?= count($matriculas) === 1 ? 'curso' : 'cursos' ?>
                    </small>

                </div>

                <strong>

                    <?= $totalValido
                        ? '$ ' . $totalFormateado
                        : 'A confirmar' ?>

                </strong>

            </div>


            <!-- MONEDA -->

            <div class="payment-currency">
                <i class="fa-solid fa-money-bill-wave"></i>
                Moneda: Pesos uruguayos (UYU)
            </div>


            <!-- INFORMACIÓN ADICIONAL -->

            <p class="payment-small resumen-aclaracion">

                <i class="fa-solid fa-circle-info"></i>

                El importe total corresponde a la suma
                de los cursos seleccionados.

            </p>

        </aside>

    </div>


    <!-- PIE DE PÁGINA -->

    <p
        class="payment-small"
        style="text-align:center;margin-top:22px"
    >

        © INADI <?= date('Y') ?> · Salto, Uruguay

    </p>

</main>

</body>
</html>