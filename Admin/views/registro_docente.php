<?php
// Este archivo se incluye desde Admin/index.php después de validar la sesión.
if (!isset($admin) || !($admin instanceof Administrativo)) { http_response_code(403); exit; }
require_once __DIR__ . '/../../database/consultas/encriptacion.php';
require_once __DIR__ . '/../../database/Clases/Docente.php';

// AJUSTAR ESTA RUTA AL ARCHIVO QUE DEFINE enviarCorreo() Y plantillaCorreo().
require_once __DIR__ . '/../../Mailer/enviarCorreo.php';
if (empty($_SESSION['csrf_docente'])) $_SESSION['csrf_docente']=bin2hex(random_bytes(32));
$errorDocente=null; $exitoDocente=false; $avisoCorreo=null;
$cursosDisponibles=[];
try {
    if (!($pdo instanceof PDO)) throw new RuntimeException('No hay conexión con la base de datos.');
    $consultaCursos=$pdo->prepare("SELECT id_curso, nombre, estado FROM curso WHERE nombre_usuario_docente IS NULL OR TRIM(nombre_usuario_docente) = '' ORDER BY nombre ASC");
    $consultaCursos->execute();
    $cursosDisponibles=$consultaCursos->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {

    // REGISTRAR ERROR EN EL SERVIDOR

    error_log(
        '[INSOFT registro docente] ' .
        $e->getMessage()
    );


    // MOSTRAR ERRORES DE VALIDACIÓN

    if (
        $e instanceof InvalidArgumentException
    ) {

        $errorDocente = $e->getMessage();

    } else {

        $errorDocente = 'No fue posible completar el registro.';

    }

}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['accion']??'')==='registrar_docente') {
    try {
        if (!hash_equals($_SESSION['csrf_docente'],(string)($_POST['csrf']??''))) throw new RuntimeException('Sesión del formulario inválida. Actualizá la página.');
        if (!($pdo instanceof PDO)) throw new RuntimeException('No hay conexión con la base de datos.');
        // IMPORTANTE: adaptar $key si encriptacion.php obtiene la clave con otro nombre.
        if (!isset($key) || !is_string($key)) throw new RuntimeException('Configurá la variable $key con la misma clave AES usada por Usuario.');
        if (($_POST['password']??'')!==($_POST['confirmar_password']??'')) throw new InvalidArgumentException('Las contraseñas no coinciden.');
        Docente::registrar(
            $pdo,
            $_POST,
            $_FILES['cv'] ?? [],
            $key
        );
        $exitoDocente=true;
        $_SESSION['csrf_docente']=bin2hex(random_bytes(32));

        // NOTIFICAR SIN INVALIDAR EL REGISTRO SI FALLA EL CORREO.
        try {
            $correoEnviado = correoRegistroDocente(
                trim((string) ($_POST['correo'] ?? '')),
                trim((string) ($_POST['nombre'] ?? '')),
                trim((string) ($_POST['nombre_usuario'] ?? '')),
                (string) ($_POST['password'] ?? '')
            );

            if (!$correoEnviado) {
                $avisoCorreo = 'El docente fue registrado, pero no se pudo enviar el correo de bienvenida.';
            }
        } catch (Throwable $errorCorreo) {
            error_log('[INSOFT correo docente] ' . $errorCorreo->getMessage());
            $avisoCorreo = 'El docente fue registrado, pero no se pudo enviar el correo de bienvenida.';
        }
    } catch (PDOException $e) {
        error_log('[INSOFT docente] '.$e->getMessage());
        $errorDocente=$e->getCode()==='23000'?'El nombre de usuario ya existe.':'No fue posible guardar el registro.';
    } catch (Throwable $e) { $errorDocente=$e instanceof InvalidArgumentException || $e instanceof RuntimeException ? $e->getMessage() : 'No fue posible completar el registro.'; }
}
/* ==========================================================
   CORREO DE REGISTRO DOCENTE
========================================================== */

function correoRegistroDocente($correo, $nombre, $nombreUsuario, $password){
    // PROTEGER DATOS

    $nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');

    $nombreUsuario = htmlspecialchars(
        $nombreUsuario,
        ENT_QUOTES,
        'UTF-8'
    );


    $password = htmlspecialchars(
        $password,
        ENT_QUOTES,
        'UTF-8'
    );

    // ASUNTO

    $asunto = 'Bienvenido al equipo docente de INADI';


    // MENSAJE

    $mensaje = '
        Estimado/a <strong>' . $nombre . '</strong>:<br><br>

        Nos complace informarte que tu registro como docente
        en el sistema institucional <strong>INSOFT</strong>
        se ha realizado correctamente.<br><br>

        A partir de este momento, formás parte del equipo docente
        del <strong>Instituto Nacional de Informática (INADI)</strong>
        y disponés de una cuenta para acceder a la plataforma
        y utilizar las herramientas correspondientes a tu rol.<br><br>

        Te damos la bienvenida y esperamos que esta plataforma
        contribuya al desarrollo de tus actividades académicas.
    ';


    // AVISO

    $aviso = '
        <strong>Información de tu cuenta</strong><br><br>

        <strong>Usuario:</strong> ' . $nombreUsuario . '<br>

        <strong>Contraseña inicial:</strong> ' . $password . '<br>

        <strong>Rol:</strong> Docente<br>

        <strong>Estado:</strong> Activo<br><br>

        <strong>Importante:</strong> Te recomendamos cambiar esta contraseña
        lo antes posible desde tu perfil en la plataforma docente.
        No compartas tus credenciales de acceso con otras personas.
    ';


    // GENERAR PLANTILLA

    $contenido = plantillaCorreo(
        '¡Bienvenido/a a INSOFT!',
        $mensaje,
        $aviso
    );


    // ENVIAR CORREO

    return enviarCorreo(
        $correo,
        $asunto,
        $contenido
    );
}

?>
<div class="welcome-row">
    <div>
        <div class="eyebrow">Gestión institucional</div>
        <h1 class="heading">Contratación docente</h1>
        <p class="subtitle">Registrá un nuevo docente y creá sus credenciales.</p>
    </div>
</div>

<section class="panel" style="max-width:850px;padding:28px">
    <?php if ($exitoDocente): ?>
        <div role="status" style="text-align:center;padding:36px 12px">
            <div style="font-size:48px;color:#15803d">✓</div>
            <h2>Docente registrado correctamente</h2>
            <p>La cuenta, la ficha docente, el CV y los cursos asignados se guardaron correctamente.</p>
            <?php if ($avisoCorreo): ?>
                <p role="alert" style="background:#fff7ed;color:#9a3412;padding:14px;border-radius:10px">
                    <?= e($avisoCorreo) ?>
                </p>
            <?php else: ?>
                <p>Se envió un correo de bienvenida al docente.</p>
            <?php endif; ?>
            <a class="btn" href="?page=solicitudes_docentes">＋ Registrar otro docente</a>
            <a class="btn btn-light" href="?page=docentes">Ver docentes</a>
        </div>
    <?php else: ?>
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Nuevo docente</h2>
                <div class="panel-sub">Los campos con * son obligatorios.</div>
            </div>
        </div>

        <?php if ($errorDocente): ?>
            <p role="alert" style="background:#fee2e2;color:#991b1b;padding:14px;border-radius:10px">
                <?= e($errorDocente) ?>
            </p>
        <?php endif; ?>

        <form
            id="registroDocenteForm"
            method="post"
            action="?page=solicitudes_docentes"
            enctype="multipart/form-data"
            autocomplete="off"
        >
            <input type="hidden" name="accion" value="registrar_docente">
            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf_docente']) ?>">

            <div class="form-grid">
                <?php
                $campos = [
                    'nombre_usuario' => ['Nombre de usuario *', 50, 'text', true],
                    'nombre' => ['Nombre *', 20, 'text', true],
                    'apellido' => ['Apellido *', 50, 'text', true],
                    'cedula' => ['Cédula *', 12, 'text', true],
                    'correo' => ['Correo electrónico *', 200, 'email', true],
                    'titulo' => ['Título académico', 100, 'text', false],
                    'especialidad' => ['Especialidad', 100, 'text', false]
                ];
                ?>

                <?php foreach ($campos as $campo => $config): ?>
                    <div class="field <?= $campo === 'correo' ? 'full' : '' ?>">
                        <label for="doc_<?= e($campo) ?>"><?= e($config[0]) ?></label>
                        <input
                            id="doc_<?= e($campo) ?>"
                            name="<?= e($campo) ?>"
                            type="<?= e($config[2]) ?>"
                            maxlength="<?= (int) $config[1] ?>"
                            value="<?= e($_POST[$campo] ?? '') ?>"
                            <?= $config[3] ? 'required' : '' ?>
                        >
                    </div>
                <?php endforeach; ?>

                <div class="field full">
                    <label for="doc_horario">Horario del docente</label>
                    <input
                        id="doc_horario"
                        name="horario"
                        maxlength="100"
                        placeholder="Ej. Lunes 09:00 a 12:00; Martes 14:00 a 16:00"
                        value="<?= e($_POST['horario'] ?? '') ?>"
                    >
                    <small>El horario se almacena como texto (máximo 100 caracteres según la base actual).</small>
                </div>

                <div class="field full">
                    <label for="doc_cv">Currículum vitae (PDF, máximo 5 MB) *</label>
                    <input
                        id="doc_cv"
                        name="cv"
                        type="file"
                        accept=".pdf,application/pdf"
                        required
                    >
                </div>

                <div class="field full">
                    <label for="doc_curso">Curso a asignar *</label>
                    <select
                        id="doc_curso"
                        name="cursos[]"
                        required
                        <?= empty($cursosDisponibles) ? 'disabled' : '' ?>
                    >
                        <option value="">Seleccioná un curso sin docente</option>
                        <?php foreach ($cursosDisponibles as $curso): ?>
                            <option
                                value="<?= (int) $curso['id_curso'] ?>"
                                <?= in_array((string) $curso['id_curso'], array_map('strval', (array) ($_POST['cursos'] ?? [])), true) ? 'selected' : '' ?>
                            >
                                <?= e($curso['nombre']) ?> (<?= e($curso['estado']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($cursosDisponibles)): ?>
                        <small>No hay cursos sin docente disponibles.</small>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="doc_password">Contraseña inicial *</label>
                    <input
                        id="doc_password"
                        name="password"
                        type="password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="field">
                    <label for="doc_confirmar">Confirmar contraseña *</label>
                    <input
                        id="doc_confirmar"
                        name="confirmar_password"
                        type="password"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                </div>
            </div>

            <div class="form-actions">
                <button
                    class="btn"
                    type="submit"
                    <?= empty($cursosDisponibles) ? 'disabled' : '' ?>
                >
                    Registrar docente y asignar curso
                </button>
                <a class="btn btn-light" href="?page=docentes">Cancelar</a>
            </div>
        </form>
    <?php endif; ?>
</section>
