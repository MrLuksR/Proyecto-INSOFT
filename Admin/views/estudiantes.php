<?php

/**
 * INSOFT - ADMINISTRACIÓN DE ESTUDIANTES
 */

// INICIAR SESIÓN PARA MENSAJES Y PROTECCIÓN CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// TOKEN PARA ACCIONES SOBRE ESTUDIANTES
if (empty($_SESSION['csrf_estudiantes'])) {
    $_SESSION['csrf_estudiantes'] = bin2hex(random_bytes(32));
}

// RECUPERAR MENSAJE
$mensajeEstudiantes = $_SESSION['mensaje_estudiantes'] ?? null;

unset($_SESSION['mensaje_estudiantes']);

if (empty($_SESSION['csrf_registro_admin'])) {
    $_SESSION['csrf_registro_admin'] = bin2hex(random_bytes(32));
}

$errorRegistro = (string) ($_SESSION['admin_error_registro'] ?? '');
$exitoRegistro = (string) ($_SESSION['admin_registro_exitoso'] ?? '');
$datosRegistro = (array) ($_SESSION['admin_datos_registro'] ?? []);

unset($_SESSION['admin_error_registro'], $_SESSION['admin_registro_exitoso'], $_SESSION['admin_datos_registro']);

$valorRegistro = static function (string $campo) use ($datosRegistro): string {
    return htmlspecialchars((string) ($datosRegistro[$campo] ?? ''), ENT_QUOTES, 'UTF-8');
};

// OBTENER LISTADO
$datosListado = adminListado($pdo ?? null, $page);

$columnas = $datosListado['columnas'];
$filas = $datosListado['filas'];

// OBTENER CURSOS ACTIVOS
$cursosRegistro = [];
$errorCursos = false;

try {

    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('Conexión no disponible.');
    }

    $consultaCursos = $pdo->prepare(
        "SELECT id_curso, nombre
         FROM curso
         WHERE estado = :estado
         ORDER BY nombre ASC"
    );

    $consultaCursos->execute([
        ':estado' => 'Activo'
    ]);

    $cursosRegistro = $consultaCursos->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $error) {

    error_log('[INSOFT][Admin] ' . $error->getMessage());

    $errorCursos = true;
}

?>

<!-- ENCABEZADO -->

<div class="welcome-row">

    <div>

        <div class="eyebrow">
            Registros de INSOFT
        </div>

        <h1 class="heading">
            <?= e($pageTitle) ?>
        </h1>

        <p class="subtitle">
            Administración y registro de estudiantes.
        </p>

    </div>

    <span class="status-pill pill-blue">
        <?= count($filas) ?> registros
    </span>

</div>


<!-- MENSAJES DEL REGISTRO -->
<?php if ($exitoRegistro !== ''): ?>
    <div class="admin-mensaje admin-mensaje-exito" role="status"><?= e($exitoRegistro) ?></div>
<?php endif; ?>
<?php if ($errorRegistro !== ''): ?>
    <div class="admin-mensaje admin-mensaje-error" role="alert"><?= e($errorRegistro) ?></div>
<?php endif; ?>

<?php if (is_array($mensajeEstudiantes)): ?>

    <div
        class="admin-mensaje <?= ($mensajeEstudiantes['tipo'] ?? '') === 'exito'
            ? 'admin-mensaje-exito'
            : 'admin-mensaje-error' ?>"
        role="status"
    >
        <?= e($mensajeEstudiantes['texto'] ?? '') ?>
    </div>

<?php endif; ?>

<!-- PANEL DE ESTUDIANTES -->

<section class="panel">

    <div class="panel-head">

        <div>

            <h2 class="panel-title">
                Listado de estudiantes
            </h2>

            <div class="panel-sub">
                Información registrada en INSOFT
            </div>

        </div>

        <button
            type="button"
            class="admin-boton-registrar"
            id="abrirRegistroEstudiante"
            aria-expanded="false"
            aria-controls="panelRegistroEstudiante"
        >
            + Registrar estudiante
        </button>

    </div>


    <!-- FORMULARIO DE REGISTRO -->

    <div
        id="panelRegistroEstudiante"
        class="admin-registro"
        hidden
    >

        <div class="admin-registro-encabezado">

            <div>

                <h2>Registrar nuevo estudiante</h2>

                <p>
                    Completá la información para crear
                    una cuenta estudiantil.
                </p>

            </div>

            <button
                type="button"
                id="cerrarRegistroEstudiante"
                class="admin-registro-cerrar"
                aria-label="Cerrar formulario"
            >
                ×
            </button>

        </div>

        <form
            id="formularioRegistroAdmin"
            action="../database/consultas/estudiantes/registro.php"
            method="POST"
            enctype="multipart/form-data"
        >
            <input type="hidden" name="registro_admin" value="1">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_registro_admin']) ?>">

            <!-- IDENTIFICACIÓN -->

            <div class="admin-registro-seccion">

                <h3>Información personal</h3>

                <div class="admin-registro-grid">

                    <div class="admin-registro-campo">

                        <label for="adminNombre">
                            Nombre *
                        </label>

                        <input
                            type="text"
                            id="adminNombre"
                            name="nombre"
                            value="<?= $valorRegistro('nombre') ?>"
                            maxlength="100"
                            required
                        >

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminApellido">
                            Apellido *
                        </label>

                        <input
                            type="text"
                            id="adminApellido"
                            name="apellido"
                            value="<?= $valorRegistro('apellido') ?>"
                            maxlength="100"
                            required
                        >

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminCedula">
                            Cédula de identidad *
                        </label>

                        <input
                            type="text"
                            id="adminCedula"
                            name="cedula"
                            value="<?= $valorRegistro('cedula') ?>"
                            placeholder="4.567.890-1"
                            maxlength="10"
                            required
                        >

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminFecha">
                            Fecha de nacimiento *
                        </label>

                        <input
                            type="date"
                            id="adminFecha"
                            value="<?= e((string) ($datosRegistro['fecha_iso'] ?? '')) ?>"
                            required
                        >

                        <input
                            type="hidden"
                            id="adminFechaConvertida"
                            name="fecha"
                        >

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminTelefono">
                            Teléfono
                        </label>

                        <input
                            type="tel"
                            id="adminTelefono"
                            name="telefono"
                        value="<?= $valorRegistro('telefono') ?>"
                            >

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminCorreo">
                            Correo electrónico *
                        </label>

                        <input
                            type="email"
                            id="adminCorreo"
                            name="correo"
                            value="<?= $valorRegistro('correo') ?>"
                            maxlength="190"
                            required
                        >

                    </div>

                </div>

            </div>


            <!-- DATOS DE ACCESO -->

            <div class="admin-registro-seccion">

                <h3>Datos de acceso</h3>

                <div class="admin-registro-grid">

                    <div class="admin-registro-campo">

                        <label for="adminUsuario">
                            Nombre de usuario *
                        </label>

                        <input
                            type="text"
                            id="adminUsuario"
                            name="nombre_usuario"
                            value="<?= $valorRegistro('nombre_usuario') ?>"
                            minlength="5"
                            maxlength="60"
                            pattern="[A-Za-z0-9_]+"
                            required
                        >

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminPassword">
                            Contraseña *
                        </label>

                        <input
                            type="password"
                            id="adminPassword"
                            name="password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminConfirmarPassword">
                            Confirmar contraseña *
                        </label>

                        <input
                            type="password"
                            id="adminConfirmarPassword"
                            name="confirmar_password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                </div>

            </div>


            <!-- INFORMACIÓN ACADÉMICA -->

            <div class="admin-registro-seccion">

                <h3>Información académica</h3>

                <div class="admin-registro-grid">

                    <div class="admin-registro-campo">

                        <label for="adminCurso">
                            Curso *
                        </label>

                        <select
                            id="adminCurso"
                            name="curso"
                            required
                        >

                            <option value="">
                                Seleccionar curso
                            </option>

                            <?php foreach ($cursosRegistro as $curso): ?>

                                <option value="<?= e($curso['nombre']) ?>" <?= (($datosRegistro['curso'] ?? '') === $curso['nombre']) ? 'selected' : '' ?>>
                                    <?= e($curso['nombre']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <?php if ($errorCursos): ?>

                            <small>
                                No se pudieron cargar los cursos.
                            </small>

                        <?php endif; ?>

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminFoto">
                            Fotografía de perfil
                        </label>

                        <input
                            type="file"
                            id="adminFoto"
                            name="foto"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <small>
                            JPG, PNG o WEBP. Máximo 5 MB.
                        </small>

                    </div>

                </div>

            </div>


            <!-- DATOS DEL TUTOR -->

            <div
                class="admin-registro-seccion"
                id="adminDatosTutor"
                hidden
            >

                <h3>Adulto responsable</h3>

                <p>
                    Información requerida para estudiantes menores de edad.
                </p>

                <div class="admin-registro-grid">

                    <div class="admin-registro-campo">

                        <label for="adminNombreTutor">
                            Nombre completo *
                        </label>

                        <input
                            type="text"
                            id="adminNombreTutor"
                            name="nombre_tutor"
                            maxlength="150"
                        >

                    </div>

                    <div class="admin-registro-campo">

                        <label for="adminTelefonoTutor">
                            Teléfono *
                        </label>

                        <input
                            type="tel"
                            id="adminTelefonoTutor"
                            name="telefono_tutor"
                        >

                    </div>

                </div>

            </div>


            <!-- ACCIONES -->

            <div class="admin-registro-acciones">

                <button
                    type="button"
                    id="cancelarRegistroEstudiante"
                    class="admin-boton-cancelar"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="admin-boton-guardar"
                >
                    Registrar estudiante
                </button>

            </div>

        </form>

    </div>


    <!-- BUSCADOR -->

    <div class="toolbar" style="margin-bottom:18px">

        <div class="search">

            <input
                type="search"
                id="listadoBusqueda"
                placeholder="Buscar en los registros..."
                aria-label="Buscar registros"
            >

        </div>

    </div>


    <!-- TABLA -->

    <div class="table-wrap">

        <table id="listadoTabla">

            <thead>

                <tr>

                    <?php foreach ($columnas as $etiqueta): ?>

                        <th>
                            <?= e($etiqueta) ?>
                        </th>

                    <?php endforeach; ?>

                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($filas as $fila): ?>

                    <tr>

                        <?php foreach ($columnas as $columna => $etiqueta): ?>

                            <td>
                                <?= e($fila[$columna] ?? '—') ?>
                            </td>

                        <?php endforeach; ?>

                        <?php
                            $usuarioEstudiante = (string) ($fila['nombre_usuario'] ?? '');

                            $estadoEstudiante = mb_strtolower(
                                trim((string) ($fila['estado'] ?? '')),
                                'UTF-8'
                            );

                            $esActivo = $estadoEstudiante === 'activo';
                            $esInactivo = $estadoEstudiante === 'inactivo';
                        ?>

                        <td>

                            <?php if ($usuarioEstudiante !== '' && ($esActivo || $esInactivo)): ?>

                                <form
                                    action="estado_estudiante.php"
                                    method="POST"
                                    onsubmit="return confirmarCambioEstado(this);"
                                >

                                    <input
                                        type="hidden"
                                        name="nombre_usuario"
                                        value="<?= e($usuarioEstudiante) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e($_SESSION['csrf_estudiantes']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="<?= $esActivo ? 'baja' : 'alta' ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="<?= $esActivo ? 'admin-boton-baja' : 'admin-boton-alta' ?>"
                                    >
                                        <?= $esActivo ? 'Dar de baja' : 'Dar de alta' ?>
                                    </button>

                                </form>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                <?php if (!$filas): ?>

                    <tr>

                        <td colspan="<?= count($columnas) + 1 ?>">
                            No hay registros para mostrar.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- ESTILOS DEL REGISTRO ADMINISTRATIVO -->

<style>
.admin-mensaje { padding: 15px 18px; margin-bottom: 20px; border-radius: 10px; font-size: 14px; }
.admin-mensaje-exito { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.admin-mensaje-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

.admin-boton-baja {
    padding: 8px 13px;
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}
.admin-boton-baja:hover { background: #fecaca; }
.admin-estado-inactivo { color: #64748b; font-size: 12px; }

.admin-boton-registrar {
    padding: 11px 18px;
    background: #45459c;
    color: #ffffff;
    border: none;
    border-radius: 9px;
    font-weight: 600;
    cursor: pointer;
}

.admin-boton-registrar:hover {
    background: #373780;
}

.admin-registro {
    margin: 22px 0;
    padding: 26px;
    background: #f8faff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
}

.admin-registro[hidden] {
    display: none;
}

.admin-registro-encabezado {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 25px;
}

.admin-registro-encabezado h2 {
    margin: 0 0 6px;
    font-size: 20px;
    color: #172b4d;
}

.admin-registro-encabezado p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.admin-registro-cerrar {
    background: transparent;
    border: none;
    font-size: 28px;
    color: #64748b;
    cursor: pointer;
}

.admin-registro-seccion {
    margin-bottom: 25px;
    padding-bottom: 22px;
    border-bottom: 1px solid #e2e8f0;
}

.admin-registro-seccion h3 {
    margin: 0 0 18px;
    font-size: 16px;
    color: #172b4d;
}

.admin-registro-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.admin-registro-campo {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.admin-registro-campo label {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
}

.admin-registro-campo input,
.admin-registro-campo select {
    width: 100%;
    min-height: 43px;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    color: #1e293b;
    font: inherit;
    box-sizing: border-box;
}

.admin-registro-campo input:focus,
.admin-registro-campo select:focus {
    outline: none;
    border-color: #45459c;
    box-shadow: 0 0 0 3px rgba(69, 69, 156, 0.12);
}

.admin-registro-campo small {
    color: #64748b;
    font-size: 12px;
}

.admin-registro-acciones {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.admin-boton-cancelar,
.admin-boton-guardar {
    padding: 11px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.admin-boton-cancelar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
}

.admin-boton-guardar {
    background: #45459c;
    border: none;
    color: #ffffff;
}

.admin-boton-guardar:hover {
    background: #373780;
}

@media (max-width: 700px) {

    .admin-registro {
        padding: 18px;
    }

    .admin-registro-grid {
        grid-template-columns: 1fr;
    }

    .admin-registro-acciones {
        flex-direction: column;
    }

    .admin-registro-acciones button {
        width: 100%;
    }

}

</style>


<!-- JAVASCRIPT -->

<script>

(function () {

    // ELEMENTOS
    const abrir = document.getElementById('abrirRegistroEstudiante');
    const cerrar = document.getElementById('cerrarRegistroEstudiante');
    const cancelar = document.getElementById('cancelarRegistroEstudiante');
    const panel = document.getElementById('panelRegistroEstudiante');
    const formulario = document.getElementById('formularioRegistroAdmin');

    const fecha = document.getElementById('adminFecha');
    const fechaConvertida = document.getElementById('adminFechaConvertida');

    const password = document.getElementById('adminPassword');
    const confirmar = document.getElementById('adminConfirmarPassword');

    const tutor = document.getElementById('adminDatosTutor');
    const nombreTutor = document.getElementById('adminNombreTutor');
    const telefonoTutor = document.getElementById('adminTelefonoTutor');

    // ABRIR EL FORMULARIO AUTOMÁTICAMENTE TRAS UN ERROR
    if (<?= json_encode($errorRegistro !== '') ?> && panel && abrir) {
        panel.hidden = false;
        abrir.setAttribute('aria-expanded', 'true');
    }

    // ABRIR FORMULARIO
    abrir?.addEventListener('click', function () {

        panel.hidden = false;
        abrir.setAttribute('aria-expanded', 'true');

        panel.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    });

    // CERRAR FORMULARIO
    function cerrarFormulario() {

        panel.hidden = true;
        abrir.setAttribute('aria-expanded', 'false');

    }

    cerrar?.addEventListener('click', cerrarFormulario);
    cancelar?.addEventListener('click', cerrarFormulario);

    // COMPROBAR EDAD
    function comprobarEdad() {

        if (!fecha.value) {

            tutor.hidden = true;
            nombreTutor.required = false;
            telefonoTutor.required = false;

            return;
        }

        const nacimiento = new Date(fecha.value + 'T12:00:00');
        const hoy = new Date();

        let edad = hoy.getFullYear() - nacimiento.getFullYear();

        const cumplio = (
            hoy.getMonth() > nacimiento.getMonth() ||
            (
                hoy.getMonth() === nacimiento.getMonth() &&
                hoy.getDate() >= nacimiento.getDate()
            )
        );

        if (!cumplio) {
            edad--;
        }

        const esMenor = edad < 18;

        tutor.hidden = !esMenor;
        nombreTutor.required = esMenor;
        telefonoTutor.required = esMenor;

    }

    fecha?.addEventListener('change', comprobarEdad);

    // VALIDAR Y CONVERTIR FECHA
    formulario?.addEventListener('submit', function (evento) {

        // COMPROBAR CONTRASEÑAS
        if (password.value !== confirmar.value) {

            evento.preventDefault();

            confirmar.setCustomValidity(
                'Las contraseñas no coinciden.'
            );

            confirmar.reportValidity();

            return;
        }

        confirmar.setCustomValidity('');

        // CONVERTIR FECHA A DD/MM/AAAA
        if (fecha.value) {

            const partes = fecha.value.split('-');

            fechaConvertida.value =
                partes[2] + '/' + partes[1] + '/' + partes[0];

        }

    });

    confirmar?.addEventListener('input', function () {
        confirmar.setCustomValidity('');
    });

    // BUSCADOR
    const busqueda = document.getElementById('listadoBusqueda');

    busqueda?.addEventListener('input', function () {

        const texto = this.value.toLocaleLowerCase('es');

        document.querySelectorAll('#listadoTabla tbody tr')
            .forEach(function (fila) {

                fila.hidden = !fila.innerText
                    .toLocaleLowerCase('es')
                    .includes(texto);

            });

    });

})();

function confirmarCambioEstado(formulario) {

    const accion = formulario.querySelector('[name="accion"]').value;

    // DAR DE BAJA
    if (accion === 'baja') {

        const motivo = prompt('¿Cuál es el motivo de la baja del estudiante?');

        // CANCELAR O VALIDAR MOTIVO VACÍO
        if (motivo === null || motivo.trim() === '') {
            alert('Debés ingresar un motivo para dar de baja al estudiante.');
            return false;
        }

        // GUARDAR MOTIVO EN EL FORMULARIO
        let campoMotivo = formulario.querySelector('[name="motivo_baja"]');

        if (!campoMotivo) {
            campoMotivo = document.createElement('input');
            campoMotivo.type = 'hidden';
            campoMotivo.name = 'motivo_baja';
            formulario.appendChild(campoMotivo);
        }

        campoMotivo.value = motivo.trim();

        return true;
    }

    // DAR DE ALTA
    return confirm('¿Confirmás la reactivación del estudiante?');
}

</script>