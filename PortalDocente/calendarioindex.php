
<?php

require_once __DIR__ . '/portal_common.php';

/* =========================================================
   1. CONFIGURACIÓN INICIAL
========================================================= */

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log('Portal calendario: conexión PDO no disponible.');
    http_response_code(500);
    exit('No se pudo establecer la conexión con la base de datos.');
}

[$titulo, $icono, $kicker] = portal_page_info();

$key = portal_user_key();

/* =========================================================
   2. PROCESAMIENTO DE FORMULARIOS
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    portal_verify_csrf();

    // Exigir una cuenta identificada para modificar la agenda.
    $key = portal_require_user_key();

    $action = $_POST['action'] ?? '';
    $id = null;

    try {

        /* -------------------------------------------------
           2.1. GUARDAR O ACTUALIZAR ACTIVIDAD
        ------------------------------------------------- */

        if ($action === 'save') {

            $id = filter_input(
                INPUT_POST,
                'event_id',
                FILTER_VALIDATE_INT
            ) ?: null;

            $title = trim((string) ($_POST['title'] ?? ''));
            $type = trim((string) ($_POST['event_type'] ?? 'Clase'));
            $date = trim((string) ($_POST['event_date'] ?? ''));
            $time = trim((string) ($_POST['event_time'] ?? ''));
            $details = trim((string) ($_POST['details'] ?? ''));

            $allowedTypes = [
                'Clase',
                'Evaluación',
                'Entrega',
                'Reunión',
                'Otro'
            ];

            // Validar la fecha.
            $dateObject = DateTime::createFromFormat('!Y-m-d', $date);

            $validDate = $dateObject !== false
                && $dateObject->format('Y-m-d') === $date;

            // Validar los campos.
            if (
                $title === ''
                || mb_strlen($title) > 120
                || !$validDate
                || !in_array($type, $allowedTypes, true)
                || mb_strlen($details) > 1500
            ) {
                throw new InvalidArgumentException(
                    'Revisá el título, tipo, fecha y detalles de la actividad.'
                );
            }

            // Validar la hora opcional.
            if (
                $time !== ''
                && !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)
            ) {
                throw new InvalidArgumentException(
                    'La hora ingresada no tiene un formato válido.'
                );
            }

            $timeValue = $time !== '' ? $time : null;
            $detailsValue = $details !== '' ? $details : null;

            /* Actualizar actividad existente. */

            if ($id !== null) {

                if ($id < 1) {
                    throw new InvalidArgumentException(
                        'La actividad seleccionada no es válida.'
                    );
                }

                // Verificar primero que la actividad pertenezca al usuario.
                $check = $pdo->prepare(
                    'SELECT id
                     FROM portal_eventos
                     WHERE id = ?
                       AND owner_key = ?
                     LIMIT 1'
                );

                $check->execute([$id, $key]);

                if (!$check->fetch(PDO::FETCH_ASSOC)) {
                    throw new RuntimeException(
                        'No se encontró esa actividad o no tenés permiso para modificarla.'
                    );
                }

                $query = $pdo->prepare(
                    'UPDATE portal_eventos
                     SET title = ?,
                         event_type = ?,
                         event_date = ?,
                         event_time = ?,
                         details = ?,
                         updated_at = NOW()
                     WHERE id = ?
                       AND owner_key = ?'
                );

                $query->execute([
                    $title,
                    $type,
                    $date,
                    $timeValue,
                    $detailsValue,
                    $id,
                    $key
                ]);

                portal_flash(
                    'success',
                    'La actividad se actualizó correctamente.'
                );

            } else {

                /* Crear actividad nueva. */

                $query = $pdo->prepare(
                    'INSERT INTO portal_eventos (
                        owner_key,
                        owner_name,
                        title,
                        event_type,
                        event_date,
                        event_time,
                        details,
                        created_at,
                        updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
                );

                $query->execute([
                    $key,
                    portal_user_name(),
                    $title,
                    $type,
                    $date,
                    $timeValue,
                    $detailsValue
                ]);

                portal_flash(
                    'success',
                    'La actividad se agregó al calendario.'
                );
            }

            header('Location: calendarioindex.php');
            exit;
        }

        /* -------------------------------------------------
           2.2. ELIMINAR ACTIVIDAD
        ------------------------------------------------- */

        elseif ($action === 'delete') {

            $id = filter_input(
                INPUT_POST,
                'event_id',
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new InvalidArgumentException(
                    'La actividad seleccionada no es válida.'
                );
            }

            $query = $pdo->prepare(
                'DELETE FROM portal_eventos
                 WHERE id = ?
                   AND owner_key = ?'
            );

            $query->execute([$id, $key]);

            if ($query->rowCount() > 0) {
                portal_flash('success', 'La actividad se eliminó correctamente.');
            } else {
                portal_flash(
                    'error',
                    'No se encontró la actividad o no tenés permiso para eliminarla.'
                );
            }

            header('Location: calendarioindex.php');
            exit;
        }

        /* -------------------------------------------------
           2.3. ACCIÓN DESCONOCIDA
        ------------------------------------------------- */

        else {
            throw new InvalidArgumentException(
                'La operación solicitada no es válida.'
            );
        }

    } catch (InvalidArgumentException $e) {

        portal_flash('error', $e->getMessage());

        $redirect = 'calendarioindex.php';

        if ($id !== null && $action === 'save') {
            $redirect .= '?edit=' . (int) $id;
        }

        header('Location: ' . $redirect);
        exit;

    } catch (Throwable $e) {

        error_log('Portal calendario: ' . $e->getMessage());

        portal_flash(
            'error',
            'No se pudo completar la operación. Verificá la tabla portal_eventos y la conexión a MySQL.'
        );

        header('Location: calendarioindex.php');
        exit;
    }
}

/* =========================================================
   3. CARGAR ACTIVIDAD PARA EDITAR
========================================================= */

$editEvent = null;

if (isset($_GET['edit']) && $key !== null) {

    $editId = filter_input(
        INPUT_GET,
        'edit',
        FILTER_VALIDATE_INT
    );

    if ($editId && $editId > 0) {

        try {

            $query = $pdo->prepare(
                'SELECT *
                 FROM portal_eventos
                 WHERE id = ?
                   AND owner_key = ?
                 LIMIT 1'
            );

            $query->execute([$editId, $key]);

            $editEvent = $query->fetch(PDO::FETCH_ASSOC) ?: null;

            if (!$editEvent) {
                portal_flash(
                    'error',
                    'No se encontró la actividad que querías editar.'
                );
            }

        } catch (Throwable $e) {

            error_log('Portal calendario, edición: ' . $e->getMessage());

            portal_flash(
                'error',
                'No se pudo cargar la actividad para editar.'
            );
        }
    }
}

/* =========================================================
   4. CONFIGURAR LA AGENDA
========================================================= */

$events = [];

$monthLabels = [
    'ENE', 'FEB', 'MAR', 'ABR',
    'MAY', 'JUN', 'JUL', 'AGO',
    'SEP', 'OCT', 'NOV', 'DIC'
];

/* =========================================================
   5. OBTENER ACTIVIDADES DE LA BASE DE DATOS
========================================================= */

try {

    if ($key !== null) {

        $query = $pdo->prepare(
            'SELECT *
             FROM portal_eventos
             WHERE owner_key = ?
             ORDER BY event_date ASC, event_time ASC, id DESC'
        );

        $query->execute([$key]);

        $events = $query->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Throwable $e) {

    error_log('Portal calendario, listado: ' . $e->getMessage());

    portal_flash(
        'error',
        'No se pudieron cargar las actividades. Verificá la tabla portal_eventos.'
    );
}

/* =========================================================
   6. ENCABEZADO DEL PORTAL
========================================================= */

portal_header($titulo, $icono, $kicker);

?>

<!-- =====================================================
     7. FORMULARIO DE PLANIFICACIÓN
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <span class="section-kicker">PLANIFICACIÓN</span>

            <h2>
                <?= $editEvent ? 'Editar actividad' : 'Agregar actividad' ?>
            </h2>
        </div>
    </div>

    <form
        class="form-panel"
        method="post"
        action="calendarioindex.php"
        autocomplete="off"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h(portal_csrf_token()) ?>"
        >

        <input type="hidden" name="action" value="save">

        <input
            type="hidden"
            name="event_id"
            value="<?= h($editEvent['id'] ?? '') ?>"
        >

        <div class="form-grid">

            <label>
                Actividad

                <input
                    type="text"
                    name="title"
                    required
                    maxlength="120"
                    value="<?= h($editEvent['title'] ?? '') ?>"
                    placeholder="Ej.: Clase de programación"
                >
            </label>

            <label>
                Tipo

                <?php
                $types = [
                    'Clase',
                    'Evaluación',
                    'Entrega',
                    'Reunión',
                    'Otro'
                ];

                $selectedType = $editEvent['event_type'] ?? 'Clase';
                ?>

                <select name="event_type" required>
                    <?php foreach ($types as $type): ?>
                        <option
                            value="<?= h($type) ?>"
                            <?= $selectedType === $type ? 'selected' : '' ?>
                        >
                            <?= h($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Fecha

                <input
                    type="date"
                    name="event_date"
                    required
                    value="<?= h($editEvent['event_date'] ?? '') ?>"
                >
            </label>

            <label>
                Hora

                <input
                    type="time"
                    name="event_time"
                    value="<?= h(
                        isset($editEvent['event_time'])
                            ? substr((string) $editEvent['event_time'], 0, 5)
                            : ''
                    ) ?>"
                >
            </label>

        </div>

        <label>
            Detalles

            <textarea
                name="details"
                rows="3"
                maxlength="1500"
                placeholder="Aula, tema o recordatorios (opcional)"
            ><?= h($editEvent['details'] ?? '') ?></textarea>
        </label>

        <div class="form-actions">

            <button class="primary-button" type="submit">
                <i class="fa-solid fa-floppy-disk"></i>

                <?= $editEvent ? 'Guardar cambios' : 'Agregar al calendario' ?>
            </button>

            <?php if ($editEvent): ?>
                <a
                    class="secondary-button"
                    href="calendarioindex.php"
                >
                    Cancelar edición
                </a>
            <?php endif; ?>

        </div>

        <p class="form-note">
            Las actividades se guardan en MySQL y son visibles
            para la cuenta que las creó.
        </p>

    </form>

</section>

<!-- =====================================================
     8. LISTADO DE ACTIVIDADES
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <span class="section-kicker">AGENDA</span>
            <h2>Tus actividades</h2>
        </div>

        <span class="count-pill">
            <?= count($events) ?> en total
        </span>
    </div>

    <div class="event-list">

        <?php if ($key === null || $key === ''): ?>

            <div class="empty-state">
                <i class="fa-solid fa-user-lock"></i>

                <h3>Iniciá sesión para usar tu agenda</h3>

                <p>
                    La agenda necesita identificar tu cuenta
                    para guardar actividades privadas.
                </p>
            </div>

        <?php elseif (!$events): ?>

            <div class="empty-state">
                <i class="fa-regular fa-calendar"></i>

                <h3>Tu agenda está vacía</h3>

                <p>
                    Agregá una actividad usando el formulario.
                </p>
            </div>

        <?php else: ?>

            <?php foreach ($events as $event): ?>

                <?php
                $timestamp = strtotime((string) $event['event_date']);

                if ($timestamp === false) {
                    continue;
                }

                $monthIndex = (int) date('n', $timestamp) - 1;
                ?>

                <article class="event-item">

                    <div class="event-datebox">
                        <strong><?= date('d', $timestamp) ?></strong>

                        <span>
                            <?= h($monthLabels[$monthIndex] ?? '') ?>
                        </span>
                    </div>

                    <div class="event-info">

                        <div class="event-title-line">
                            <h3><?= h($event['title'] ?? '') ?></h3>

                            <span class="type-pill">
                                <?= h($event['event_type'] ?? '') ?>
                            </span>
                        </div>

                        <p>
                            <?= h(date('d/m/Y', $timestamp)) ?>

                            <?php if (!empty($event['event_time'])): ?>
                                ·
                                <?= h(substr((string) $event['event_time'], 0, 5)) ?>
                            <?php endif; ?>
                        </p>

                        <?php if (!empty($event['details'])): ?>
                            <p><?= nl2br(h($event['details'])) ?></p>
                        <?php endif; ?>

                        <div class="event-meta">
                            Creada por vos ·
                            <?= h($event['owner_name'] ?? portal_user_name()) ?>
                        </div>

                    </div>

                    <div class="event-actions">

                        <a
                            class="icon-action"
                            href="calendarioindex.php?edit=<?= (int) $event['id'] ?>"
                            aria-label="Editar actividad"
                            title="Editar actividad"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <form
                            method="post"
                            action="calendarioindex.php"
                            onsubmit="return confirm('¿Querés eliminar esta actividad?');"
                        >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= h(portal_csrf_token()) ?>"
                            >

                            <input type="hidden" name="action" value="delete">

                            <input
                                type="hidden"
                                name="event_id"
                                value="<?= (int) $event['id'] ?>"
                            >

                            <button
                                class="icon-action danger"
                                type="submit"
                                aria-label="Eliminar actividad"
                                title="Eliminar actividad"
                            >
                                <i class="fa-regular fa-trash-can"></i>
                            </button>

                        </form>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</section>

<!-- =====================================================
     9. PIE DEL PORTAL
====================================================== -->

<?php
portal_footer();
?>
