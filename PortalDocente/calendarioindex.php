
<?php

require_once __DIR__ . '/portal_common.php';


/* 1. CONFIGURACIÓN INICIAL */

[$titulo, $icono, $kicker] = portal_page_info();

$key = portal_user_key();


/* 2. PROCESAMIENTO DE FORMULARIOS */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    portal_verify_csrf();

    $key = portal_require_user_key();

    $action = $_POST['action'] ?? '';

    $id = null;


    try {

        /* 2.1. GUARDAR O ACTUALIZAR ACTIVIDAD */

        if ($action === 'save') {

            // Obtener los datos enviados desde el formulario.

            $id = filter_input(
                INPUT_POST,
                'event_id',
                FILTER_VALIDATE_INT
            ) ?: null;


            $title = trim(
                (string) ($_POST['title'] ?? '')
            );

            $type = trim(
                (string) ($_POST['event_type'] ?? 'Clase')
            );

            $date = trim(
                (string) ($_POST['event_date'] ?? '')
            );

            $time = trim(
                (string) ($_POST['event_time'] ?? '')
            );

            $details = trim(
                (string) ($_POST['details'] ?? '')
            );


            /* Validar los tipos de actividad permitidos. */

            $allowed = [
                'Clase',
                'Evaluación',
                'Entrega',
                'Reunión',
                'Otro'
            ];


            /* Validar el formato de la fecha. */

            $dateObject = DateTime::createFromFormat(
                '!Y-m-d',
                $date
            );


            $validDate = $dateObject &&
                $dateObject->format('Y-m-d') === $date;


            /* Validar los campos obligatorios y sus límites. */

            if (
                $title === '' ||
                mb_strlen($title) > 120 ||
                !$validDate ||
                !in_array($type, $allowed, true) ||
                mb_strlen($details) > 1500
            ) {

                throw new InvalidArgumentException(
                    'Revisá el título, tipo, fecha y detalles de la actividad.'
                );
            }


            /* Validar el formato de la hora. */

            if (
                $time !== '' &&
                !preg_match(
                    '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                    $time
                )
            ) {

                throw new InvalidArgumentException(
                    'La hora no tiene un formato válido.'
                );
            }


            // Convertir los campos opcionales vacíos en NULL.

            $timeValue = $time !== ''
                ? $time
                : null;

            $detailsValue = $details !== ''
                ? $details
                : null;


            /* 2.1.1. ACTUALIZAR UNA ACTIVIDAD EXISTENTE */

            if ($id !== null) {

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


                /* Verificar si la actividad existe y pertenece al usuario. */

                if ($query->rowCount() === 0) {

                    $check = $pdo->prepare(
                        'SELECT id
                         FROM portal_eventos
                         WHERE id = ?
                         AND owner_key = ?'
                    );

                    $check->execute([
                        $id,
                        $key
                    ]);


                    if (!$check->fetch(PDO::FETCH_ASSOC)) {

                        throw new RuntimeException(
                            'No se encontró esa actividad o no tenés permiso para modificarla.'
                        );
                    }
                }


                portal_flash(
                    'success',
                    'La actividad se actualizó correctamente.'
                );


            /* 2.1.2. CREAR UNA ACTIVIDAD NUEVA */

            } else {

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
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
                    )'
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


            // Volver al calendario después de guardar.

            header(
                'Location: calendarioindex.php'
            );

            exit;
        }


        /* 2.2. ELIMINAR ACTIVIDAD */

        elseif ($action === 'delete') {

            $id = filter_input(
                INPUT_POST,
                'event_id',
                FILTER_VALIDATE_INT
            );


            // Comprobar que se haya seleccionado una actividad válida.

            if (!$id) {

                throw new InvalidArgumentException(
                    'La actividad seleccionada no es válida.'
                );
            }


            // El usuario solo puede eliminar sus propias actividades.

            $query = $pdo->prepare(
                'DELETE FROM portal_eventos
                 WHERE id = ?
                 AND owner_key = ?'
            );


            $query->execute([
                $id,
                $key
            ]);


            $deleted = $query->rowCount() > 0;


            // Informar el resultado de la operación.

            portal_flash(
                $deleted ? 'success' : 'error',
                $deleted
                    ? 'La actividad se eliminó.'
                    : 'No se encontró la actividad o no tenés permiso para eliminarla.'
            );


            header(
                'Location: calendarioindex.php'
            );

            exit;
        }


    /* 2.3. MANEJO DE ERRORES*/

    } catch (InvalidArgumentException $e) {

        portal_flash(
            'error',
            $e->getMessage()
        );


        // Mantener la edición cuando falla una validación.

        $redirect = 'calendarioindex.php';

        if ($id !== null) {

            $redirect .= '?edit=' . (int) $id;
        }


        header(
            'Location: ' . $redirect
        );

        exit;


    } catch (Throwable $e) {

        portal_db_error(
            $e,
            'No se pudo guardar la actividad. ' .
            'Verificá que ejecutaste instalar_portal.sql.'
        );


        header(
            'Location: calendarioindex.php'
        );

        exit;
    }
}


/* 3. CARGAR ACTIVIDAD PARA EDITAR */

$editEvent = null;


if (
    isset($_GET['edit']) &&
    $key !== null
) {

    $editId = filter_input(
        INPUT_GET,
        'edit',
        FILTER_VALIDATE_INT
    );


    if ($editId) {

        $query = $pdo->prepare(
            'SELECT *
             FROM portal_eventos
             WHERE id = ?
             AND owner_key = ?'
        );


        $query->execute([
            $editId,
            $key
        ]);


        $editEvent = $query->fetch(
            PDO::FETCH_ASSOC
        ) ?: null;
    }
}


/* 4. CONFIGURAR LA AGENDA */

// Inicializar la lista de actividades.

$events = [];


// Abreviaturas de los meses.

$monthLabels = [
    'ENE',
    'FEB',
    'MAR',
    'ABR',
    'MAY',
    'JUN',
    'JUL',
    'AGO',
    'SEP',
    'OCT',
    'NOV',
    'DIC'
];


/* 5. OBTENER ACTIVIDADES DE LA BASE DE DATOS */

try {

    if ($key !== null) {

        $query = $pdo->prepare(
            'SELECT *
             FROM portal_eventos
             WHERE owner_key = ?
             ORDER BY
                 event_date ASC,
                 event_time ASC,
                 id DESC'
        );


        $query->execute([
            $key
        ]);


        $events = $query->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


} catch (Throwable $e) {

    error_log(
        'Portal calendario: ' . $e->getMessage()
    );


    portal_flash(
        'error',
        'No se pudieron cargar las actividades. ' .
        'Ejecutá instalar_portal.sql.'
    );
}


/* 6. ENCABEZADO DEL PORTAL */

portal_header(
    $titulo,
    $icono,
    $kicker
);

?>


<!-- 7. FORMULARIO DE PLANIFICACIÓN -->

<section class="dashboard-section">


    <!-- Encabezado de la sección -->

    <div class="section-heading">

        <div>

            <span class="section-kicker">
                PLANIFICACIÓN
            </span>


            <h2>

                <?= $editEvent
                    ? 'Editar actividad'
                    : 'Agregar actividad'
                ?>

            </h2>

        </div>

    </div>


    <!-- Formulario -->

    <form
        class="form-panel"
        method="post"
        action="calendarioindex.php"
        autocomplete="off"
    >

        <!-- Token CSRF -->

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h(portal_csrf_token()) ?>"
        >


        <!-- Acción del formulario -->

        <input
            type="hidden"
            name="action"
            value="save"
        >


        <!-- Identificador de la actividad -->

        <input
            type="hidden"
            name="event_id"
            value="<?= h($editEvent['id'] ?? '') ?>"
        >


        <!-- Campos principales -->

        <div class="form-grid">


            <!-- Nombre de la actividad -->

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


            <!-- Tipo de actividad -->

            <label>

                Tipo

                <select
                    name="event_type"
                    required
                >

                    <?php
                    $types = [
                        'Clase',
                        'Evaluación',
                        'Entrega',
                        'Reunión',
                        'Otro'
                    ];
                    ?>


                    <?php foreach ($types as $type): ?>

                        <option
                            value="<?= h($type) ?>"
                            <?= (
                                ($editEvent['event_type'] ?? 'Clase') === $type
                            ) ? 'selected' : '' ?>
                        >

                            <?= h($type) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </label>


            <!-- Fecha -->

            <label>

                Fecha

                <input
                    type="date"
                    name="event_date"
                    required
                    value="<?= h($editEvent['event_date'] ?? '') ?>"
                >

            </label>


            <!-- Hora -->

            <label>

                Hora

                <input
                    type="time"
                    name="event_time"
                    value="<?= h(
                        isset($editEvent['event_time'])
                            ? substr(
                                (string) $editEvent['event_time'],
                                0,
                                5
                            )
                            : ''
                    ) ?>"
                >

            </label>


        </div>


        <!-- Detalles adicionales -->

        <label>

            Detalles

            <textarea
                name="details"
                rows="3"
                maxlength="1500"
                placeholder="Aula, tema o recordatorios (opcional)"
            ><?= h($editEvent['details'] ?? '') ?></textarea>

        </label>


        <!-- Botones del formulario -->

        <div class="form-actions">


            <button
                class="primary-button"
                type="submit"
            >

                <i class="fa-solid fa-floppy-disk"></i>


                <?= $editEvent
                    ? 'Guardar cambios'
                    : 'Agregar al calendario'
                ?>

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


        <!-- Información complementaria -->

        <p class="form-note">

            Las actividades se guardan en MySQL y son visibles
            para la cuenta que las creó.

        </p>


    </form>

</section>


<!-- 8. LISTADO DE ACTIVIDADES -->

<section class="dashboard-section">


    <!-- Encabezado de la agenda -->

    <div class="section-heading">

        <div>

            <span class="section-kicker">
                AGENDA
            </span>


            <h2>
                Tus actividades
            </h2>

        </div>


        <!-- Cantidad de actividades -->

        <span class="count-pill">

            <?= count($events) ?> en total

        </span>

    </div>


    <!-- Contenedor de eventos -->

    <div class="event-list">


        <!-- Caso 1: usuario no autenticado -->

        <?php if (!$key): ?>

            <div class="empty-state">

                <i class="fa-solid fa-user-lock"></i>


                <h3>
                    Iniciá sesión para usar tu agenda
                </h3>


                <p>

                    La agenda necesita identificar tu cuenta
                    para guardar actividades privadas.

                </p>

            </div>


        <!-- Caso 2: no hay actividades -->

        <?php elseif (!$events): ?>

            <div class="empty-state">

                <i class="fa-regular fa-calendar"></i>


                <h3>
                    Tu agenda está vacía
                </h3>


                <p>

                    Agregá una actividad usando el formulario.

                </p>

            </div>


        <!-- Caso 3: mostrar las actividades -->

        <?php else: ?>

            <?php foreach ($events as $event): ?>

                <?php

                $timestamp = strtotime(
                    $event['event_date']
                );

                ?>


                <article class="event-item">


                    <!-- Fecha de la actividad -->

                    <div class="event-datebox">

                        <strong>

                            <?= date('d', $timestamp) ?>

                        </strong>


                        <span>

                            <?= h(
                                $monthLabels[
                                    (int) date('n', $timestamp) - 1
                                ]
                            ) ?>

                        </span>

                    </div>


                    <!-- Información de la actividad -->

                    <div class="event-info">


                        <!-- Título y tipo -->

                        <div class="event-title-line">

                            <h3>

                                <?= h($event['title']) ?>

                            </h3>


                            <span class="type-pill">

                                <?= h($event['event_type']) ?>

                            </span>

                        </div>


                        <!-- Fecha y hora -->

                        <p>

                            <?= h(
                                date('d/m/Y', $timestamp)
                            ) ?>


                            <?php if (!empty($event['event_time'])): ?>

                                ·

                                <?= h(
                                    substr(
                                        $event['event_time'],
                                        0,
                                        5
                                    )
                                ) ?>

                            <?php endif; ?>

                        </p>


                        <!-- Detalles -->

                        <?php if (!empty($event['details'])): ?>

                            <p>

                                <?= nl2br(
                                    h($event['details'])
                                ) ?>

                            </p>

                        <?php endif; ?>


                        <!-- Información de creación -->

                        <div class="event-meta">

                            Creada por vos ·

                            <?= h(
                                $event['owner_name'] ?? portal_user_name()
                            ) ?>

                        </div>


                    </div>


                    <!-- Acciones de la actividad -->

                    <div class="event-actions">


                        <!-- Editar -->

                        <a
                            class="icon-action"
                            href="calendarioindex.php?edit=<?= (int) $event['id'] ?>"
                            aria-label="Editar actividad"
                            title="Editar actividad"
                        >

                            <i class="fa-solid fa-pen"></i>

                        </a>


                        <!-- Eliminar -->

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


                            <input
                                type="hidden"
                                name="action"
                                value="delete"
                            >


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


<!-- 9. PIE DEL PORTAL -->

<?php

portal_footer();

?>
