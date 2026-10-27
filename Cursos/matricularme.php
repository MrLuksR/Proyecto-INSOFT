<?php

// =====================================================
// DEPENDENCIAS E INICIALIZACIÓN
// =====================================================

include '../database/Clases/Estudiante.php';
include '../auth/config.php';
require_once '../database/consultas/cursos/getCursos.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// =====================================================
// TOKEN DE SEGURIDAD PARA CANCELAR MATRÍCULA
// =====================================================

if (empty($_SESSION['csrf_matricula'])) {

    $_SESSION['csrf_matricula'] = bin2hex(
        random_bytes(32)
    );

}

require_once __DIR__ . '/../database/consultas/conexion.php';

$anioActual = date('Y');

// =====================================================
// OBTENER Y VALIDAR EL CURSO
// =====================================================

$idCurso = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idCurso || $idCurso <= 0) {
    header('Location: cursosindex.php');
    exit;
}

$sql = "
    SELECT
        curso.id_curso,
        curso.nombre,
        curso.modalidad,
        curso.duracion,
        curso.costo,
        curso.img,
        curso.estado,
        categoria_cursos.nombre AS categoria
    FROM curso
    INNER JOIN categoria_cursos
        ON curso.id_categoria = categoria_cursos.id_categoria
    WHERE curso.id_curso = :idCurso
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':idCurso' => $idCurso]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    header('Location: cursosindex.php');
    exit;
}

// =====================================================
// FUNCIONES Y VARIABLES DEL FORMULARIO
// =====================================================

function escMatricula($valor)
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$nombreCurso = escMatricula($curso['nombre']);
$errores = [];
$enviado = false;

// =====================================================
// OBTENER DATOS DEL ESTUDIANTE AUTENTICADO
// =====================================================

// VALIDAR SESIÓN
if (
    !isset($_SESSION['usuario'], $_SESSION['tipo_usuario']) ||
    $_SESSION['tipo_usuario'] !== 'estudiante' ||
    !($_SESSION['usuario'] instanceof Estudiante)
) {
    header('Location: ../auth/loginn.php');
    exit;
}

// OBTENER OBJETO DEL ESTUDIANTE
$estudiante = $_SESSION['usuario'];

// =====================================================
// COMPROBAR SI EL ESTUDIANTE YA ESTÁ MATRICULADO
// =====================================================

// OBTENER IDENTIFICADOR DEL ESTUDIANTE
$nombreUsuario = $estudiante->getNombreUsuario();

// CONSULTAR MATRÍCULA EXISTENTE
$sql = "
    SELECT
        id_matricula,
        estado

    FROM matricula

    WHERE nombre_usuario_estudiante = :usuario
      AND id_curso = :curso

    LIMIT 1
";

$consulta = $pdo->prepare($sql);

$consulta->execute([
    ':usuario' => $nombreUsuario,
    ':curso' => $idCurso
]);

$matriculaExistente = $consulta->fetch(PDO::FETCH_ASSOC);

// COMPROBAR SI YA ESTÁ MATRICULADO
$yaMatriculado = (bool) $matriculaExistente;

// OBTENER CLAVE DE CIFRADO
// Ajustar $key al nombre real definido en auth/config.php.

// DATOS DEL FORMULARIO
$datos = [
    'nombre' => $estudiante->getNombre(),
    'apellido' => $estudiante->getApellido(),
    'email' => $estudiante->getCorreo($key),
    'telefono' => $estudiante->getTelefono($key),
    'modalidad' => (string) ($curso['modalidad'] ?? '')
];

// =====================================================
// PROCESAR SOLICITUD DE INSCRIPCIÓN
// =====================================================

// =====================================================
// PROCESAR SOLICITUD DE MATRICULACIÓN
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // COMPROBAR SI YA EXISTE UNA MATRÍCULA
    if ($yaMatriculado) {

        header('Location: matricularme.php?id=' . $idCurso);
        exit;

    }

    // VALIDAR CONFIRMACIÓN
    if (!isset($_POST['acepto']) || $_POST['acepto'] !== '1') {

        $errores[] = 'Debés confirmar que los datos son correctos.';

    }

    // VALIDAR DATOS DEL ESTUDIANTE
    if (
        trim((string) $datos['nombre']) === '' ||
        trim((string) $datos['apellido']) === ''
    ) {

        $errores[] = 'Los datos de tu cuenta están incompletos.';

    }

    // REGISTRAR MATRÍCULA
    if (!$errores) {

        try {

            // INICIAR TRANSACCIÓN
            $pdo->beginTransaction();

            // INSERTAR MATRÍCULA
            $sql = "
                INSERT INTO matricula (
                    fecha,
                    estado,
                    nombre_usuario_estudiante,
                    id_curso
                )

                VALUES (
                    CURRENT_DATE(),
                    'Pendiente de pago',
                    :usuario,
                    :curso
                )
            ";

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                ':usuario' => $nombreUsuario,
                ':curso' => $idCurso
            ]);

            // OBTENER IDENTIFICADOR GENERADO
            $idMatricula = (int) $pdo->lastInsertId();

            // CONFIRMAR TRANSACCIÓN
            $pdo->commit();

            // REDIRIGIR A LA MISMA PÁGINA
            header(
                'Location: matricularme.php?id=' . $idCurso,
                true,
                303
            );

            exit;

        } catch (Throwable $e) {

            // REVERTIR TRANSACCIÓN
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            // REGISTRAR ERROR
            error_log(
                '[INADI] Error al registrar matrícula: '
                . $e->getMessage()
            );

            $errores[] = 'No se pudo registrar la matrícula. Intentá nuevamente.';

        }

    }

}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Matriculación</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../Inicio/CSS/stylesinicio.css?v=8">
    <link rel="stylesheet" href="cursostyles.css?v=6">
    <link rel="stylesheet" href="matricularme.css?v=1">
    <link rel="stylesheet" href="../Estructura/topbar-controls.css">
</head>

<body>

    <div class="page">

        <!-- =====================================================
             ENCABEZADO PRINCIPAL
        ====================================================== -->
        <header class="topbar">

            <div class="topbar-left">
                <a href="../Inicio/inicioindex.php" class="top-logo">
                    <img src="../Elementos Gráficos/Logo Inadi con Brillo.png" alt="Logo INADI">
                    <span>INADI</span>
                </a>

                <div class="news">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>NOTICIAS</span>
                </div>
            </div>

            <div class="top-search">
                <form action="cursosindex.php" method="GET">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        type="search"
                        name="buscar"
                        placeholder="Buscar información..."
                        aria-label="Buscar información"
                    >
                    <button type="submit">Buscar</button>
                </form>
            </div>

            <div class="topbar-right">
                <?php
                inadiLanguageControl();
                setLogin();
                ?>
            </div>

        </header>

        <!-- =====================================================
             MENÚ LATERAL
        ====================================================== -->
        <aside class="sidebar" aria-label="Menú principal">

            <button
                class="sidebar-toggle"
                id="sidebarToggle"
                type="button"
                aria-label="Contraer menú"
                aria-expanded="true"
                title="Contraer menú"
            >
                <i class="fa-solid fa-angles-left"></i>
            </button>

            <ul class="menu">
                <li>
                    <a href="../Inicio/inicioindex.php">
                        <i class="fa-solid fa-house"></i>
                        <span>Inicio</span>
                    </a>
                </li>

                <li>
                    <a href="../Nosotros/nosotros.php">
                        <i class="fa-solid fa-building-columns"></i>
                        <span>Nosotros</span>
                    </a>
                </li>

                <li class="active">
                    <a href="cursosindex.php" aria-current="page">
                        <i class="fa-solid fa-book"></i>
                        <span>Cursos</span>
                    </a>
                </li>

                <li>
                    <a href="../Contacto/contacto.php">
                        <i class="fa-solid fa-envelope"></i>
                        <span>Contacto</span>
                    </a>
                </li>

                <li>
                    <a href="../Galeria/galeria.php">
                        <i class="fa-solid fa-image"></i>
                        <span>Galería</span>
                    </a>
                </li>

                <li>
                    <a href="../Convenios/convenios.php">
                        <i class="fa-solid fa-handshake"></i>
                        <span>Convenios</span>
                    </a>
                </li>

                <li>
                    <a href="../Empresas/empresas.php">
                        <i class="fa-solid fa-city"></i>
                        <span>Empresas</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-bottom">
                © INADI <?= escMatricula($anioActual) ?><br>
                Instituto de Informática<br>
                Salto - Uruguay
            </div>

        </aside>

        <!-- =====================================================
             CONTENIDO PRINCIPAL
        ====================================================== -->
        <div class="content">

            <main class="main matricula-main">

                <a href="curso.php?id=<?= (int) $idCurso ?>" class="back-button">
                    <i class="fa-solid fa-arrow-left"></i>
                    Volver al curso
                </a>

                <!-- TÍTULO -->
                <div class="matricula-heading">
                    <span class="matricula-kicker">
                        <i class="fa-solid fa-graduation-cap"></i>
                        INSCRIPCIONES INADI
                    </span>

                    <h1>Matricularme a un curso</h1>
                    <p>Completá tus datos para continuar con la inscripción.</p>
                </div>

                <section class="matricula-layout">

                    <!-- =========================================
                         RESUMEN DEL CURSO
                    ========================================== -->
                    <aside class="matricula-resumen">

                        <div class="resumen-icon">
                            <i class="fa-solid fa-laptop-code"></i>
                        </div>

                        <span class="resumen-label">CURSO SELECCIONADO</span>

                        <h2><?= $nombreCurso ?></h2>

                        <div class="resumen-dato">
                            <i class="fa-solid fa-layer-group"></i>
                            <span><?= escMatricula($curso['categoria'] ?? 'Informática') ?></span>
                        </div>

                        <div class="resumen-dato">
                            <i class="fa-solid fa-desktop"></i>
                            <span><?= escMatricula($curso['modalidad'] ?? 'A confirmar') ?></span>
                        </div>

                        <div class="resumen-dato">
                            <i class="fa-regular fa-clock"></i>
                            <span><?= escMatricula($curso['duracion'] ?? 'Duración a confirmar') ?></span>
                        </div>

                        <?php if (($curso['costo'] ?? '') !== ''): ?>
                            <div class="resumen-precio">
                                $<?= escMatricula($curso['costo']) ?>
                            </div>
                        <?php endif; ?>

                        <a href="curso.php?id=<?= (int) $idCurso ?>" class="resumen-link">
                            Ver detalles del curso
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>

                    </aside>
                    <?php if ($yaMatriculado): ?>

                    <!-- ESTUDIANTE YA MATRICULADO -->

                    <div class="matricula-form-card">

                        <div class="matricula-aviso" style="
                            background: #dcfce7;
                            color: #166534;
                            border: 1px solid #bbf7d0;
                            padding: 20px;
                            border-radius: 12px;
                        ">

                            <i class="fa-solid fa-circle-check"></i>

                            <div>

                                <strong>
                                    Ya estás matriculado en este curso
                                </strong>

                                <p>
                                    Tu matrícula fue registrada correctamente.
                                    Podés consultar su estado y realizar
                                    el pago desde tu portal estudiantil.
                                </p>

                            </div>

                        </div>

                        <a
                            href="../PortalEstudiante/misCursos.php"
                            class="btn-confirmar"
                            style="
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                gap: 10px;
                                text-decoration: none;
                                margin-top: 20px;
                            "
                        >

                            <i class="fa-solid fa-user-graduate"></i>

                            Ver en mi perfil

                        </a>

                        <!-- =====================================================
                            CANCELAR MATRÍCULA
                        ===================================================== -->

                        <?php
                        $estadoMatricula = mb_strtolower(
                            trim((string) ($matriculaExistente['estado'] ?? '')),
                            'UTF-8'
                        );
                        ?>

                        <?php if ($estadoMatricula === 'pendiente de pago'): ?>

                            <form
                                action="cancelarMatricula.php"
                                method="POST"
                                onsubmit="return confirm('¿Estás seguro de que querés cancelar tu matrícula?');"
                                style="margin-top: 12px;"
                            >

                                <!-- IDENTIFICADOR DEL CURSO -->

                                <input
                                    type="hidden"
                                    name="id_curso"
                                    value="<?= (int) $idCurso ?>"
                                >


                                <!-- TOKEN DE SEGURIDAD -->

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= escMatricula($_SESSION['csrf_matricula']) ?>"
                                >


                                <!-- BOTÓN DE CANCELACIÓN -->

                                <button
                                    type="submit"
                                    class="btn-cancelar-matricula"
                                >

                                    <i class="fa-solid fa-trash-can"></i>

                                    Cancelar matrícula

                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                <?php else: ?>

                    <!-- =========================================
                         FORMULARIO DE MATRICULACIÓN
                    ========================================== -->
                    <div class="matricula-form-card">

                        <!-- MENSAJES DE ERROR -->
                        <?php if ($errores): ?>
                            <div class="matricula-aviso aviso-error">
                                <i class="fa-solid fa-circle-exclamation"></i>

                                <div>
                                    <strong>Revisá los siguientes datos:</strong>

                                    <ul>
                                        <?php foreach ($errores as $error): ?>
                                            <li><?= escMatricula($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <h2>Tus datos</h2>
                        <p class="form-intro">
                            Usaremos esta información para contactarte sobre la inscripción.
                        </p>

                        <form
                            method="POST"
                            action="matricularme.php?id=<?= (int) $idCurso ?>"
                            class="form-matricula"
                            novalidate
                        >

                            <!-- NOMBRE Y APELLIDO -->
                            <div class="form-row">

                                <label>
                                    Nombre <span>*</span>

                                    <input
                                        type="text"
                                        name="nombre"
                                        value="<?= escMatricula($datos['nombre']) ?>"
                                        autocomplete="given-name"
                                        required
                                        readonly
                                    >
                                </label>

                                <label>
                                    Apellido <span>*</span>

                                    <input
                                        type="text"
                                        name="apellido"
                                        value="<?= escMatricula($datos['apellido']) ?>"
                                        autocomplete="family-name"
                                        required
                                        readonly
                                    >
                                </label>

                            </div>

                            <!-- CORREO Y TELÉFONO -->
                            <div class="form-row">

                                <label>
                                    Correo electrónico <span>*</span>

                                    <input
                                        type="email"
                                        name="email"
                                        value="<?= escMatricula($datos['email']) ?>"
                                        placeholder="tu@correo.com"
                                        autocomplete="email"
                                        required
                                        readonly
                                    >
                                </label>

                                <label>
                                    Teléfono <span>*</span>

                                    <input
                                        type="tel"
                                        name="telefono"
                                        value="<?= escMatricula($datos['telefono']) ?>"
                                        placeholder="099 123 456"
                                        autocomplete="tel"
                                        required
                                        readonly
                                    >
                                </label>

                            </div>

                            <!-- CONFIRMACIÓN -->
                            <label class="acepto-label">
                                <input
                                    type="checkbox"
                                    name="acepto"
                                    value="1"
                                    <?= isset($_POST['acepto']) ? 'checked' : '' ?>
                                    required
                                >

                                <span>
                                    Confirmo que los datos ingresados son correctos.
                                </span>
                            </label>

                            <!-- ENVIAR -->
                            <button type="submit" class="btn-confirmar">
                                <i class="fa-solid fa-paper-plane"></i>
                                Continuar con mi inscripción
                            </button>

                            <p class="form-privacidad">
                                <i class="fa-solid fa-lock"></i>
                                Tus datos deben tratarse de forma responsable y segura.
                            </p>

                        </form>

                    </div>

                <?php endif; ?>

                </section>

            </main>

        </div>

        <!-- =====================================================
             PIE DE PÁGINA
        ====================================================== -->
        <footer>

            <div class="footer-content">

                <div class="footer-column">
                    <div class="footer-brand-title">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>INADI</span>
                    </div>

                    <p class="footer-description">
                        Instituto de Informática comprometido con la formación
                        tecnológica y el desarrollo educativo.
                    </p>

                    <p class="footer-description">
                        © INADI <?= escMatricula($anioActual) ?>.
                        Todos los derechos reservados.
                    </p>
                </div>

                <div class="footer-column">
                    <h3>CONTACTO</h3>

                    <p>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Artigas 827, Salto, Uruguay</span>
                    </p>

                    <p>
                        <i class="fa-solid fa-building"></i>
                        <span>Instituto de Informática</span>
                    </p>
                </div>

                <div class="footer-column">
                    <h3>FORMACIÓN</h3>

                    <p>
                        <i class="fa-solid fa-book"></i>
                        <span>Cursos de informática</span>
                    </p>

                    <p>
                        <i class="fa-solid fa-laptop"></i>
                        <span>Formación tecnológica</span>
                    </p>
                </div>

                <div class="footer-column">
                    <h3>NAVEGACIÓN</h3>

                    <p>
                        <i class="fa-solid fa-house"></i>
                        <a href="../Inicio/inicioindex.php">Inicio</a>
                    </p>

                    <p>
                        <i class="fa-solid fa-building-columns"></i>
                        <a href="../Nosotros/nosotros.php">Nosotros</a>
                    </p>

                    <p>
                        <i class="fa-solid fa-envelope"></i>
                        <a href="../Contacto/contacto.php">Contacto</a>
                    </p>
                </div>

            </div>

        </footer>

    </div>

    <!-- =====================================================
         CONTROL DEL MENÚ LATERAL
    ====================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const sidebar = document.querySelector('.sidebar');
            const page = document.querySelector('.page');
            const toggle = document.getElementById('sidebarToggle');

            if (!sidebar || !page || !toggle) {
                return;
            }

            toggle.addEventListener('click', function () {

                const collapsed = sidebar.classList.toggle('sidebar-collapsed');

                page.classList.toggle('sidebar-collapsed', collapsed);

                toggle.setAttribute('aria-expanded', String(!collapsed));

                toggle.setAttribute(
                    'aria-label',
                    collapsed ? 'Mostrar menú' : 'Contraer menú'
                );

                toggle.title = collapsed ? 'Mostrar menú' : 'Contraer menú';

                const icon = toggle.querySelector('i');

                if (icon) {
                    icon.classList.toggle('fa-angles-left', !collapsed);
                    icon.classList.toggle('fa-angles-right', collapsed);
                }

            });

        });
    </script>

</body>

</html>