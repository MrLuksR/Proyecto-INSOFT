
<div class="welcome-row">
    <div>
        <div class="eyebrow">Administración del portal</div>
        <h1 class="heading"><?= e($pageTitle) ?>
        </h1>
        <p class="subtitle"><?= e($modules[$page][2]) ?>. Administrá los registros desde esta pantalla.</p>
    </div>
    <button class="btn" type="button" id="newRecordBtn">＋ &nbsp;Nuevo registro</button>
</div>
<section class="panel" id="recordsPanel">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Listado de <?= e(mb_strtolower($pageTitle, 'UTF-8')) ?>
            </h2>
            <div class="panel-sub">Buscá registros y usá las acciones para consultar, editar o eliminar.</div>
        </div>
        <span class="status-pill pill-blue" id="recordCount">0 registros</span>
    </div>
    <div class="toolbar" style="margin-bottom:17px">
        <div class="search">
            <input id="recordSearch" type="search" placeholder="Buscar por nombre o descripción..." aria-label="Buscar registros">
        </div>
        <select id="statusFilter" aria-label="Filtrar por estado" style="border:1px solid var(--line);border-radius:10px;padding:11px;color:var(--ink);background:#fff">
        <option value="">Todos los estados</option>
        <option>Publicado</option>
        <option>Borrador</option>
        <option>Inactivo</option>
        </select>
    </div>
    <div class="table-wrap">
        <table id="recordsTable">
            <thead>
                <tr>
                    <th>Registro</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Última actualización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="recordsBody">
            </tbody>
        </table>
    </div>
    <div class="empty-state" id="recordsEmpty" hidden>
        <div class="big"><?= e($modules[$page][1]) ?>
        </div>
        <strong id="emptyTitle">Todavía no hay registros</strong>
        <span id="emptyDescription">Creá un registro con el botón “Nuevo registro”.</span>
    </div>
</section>
<section class="panel" id="recordEditor" hidden>
    <div class="panel-head">
        <div>
            <h2 class="panel-title" id="editorTitle">Nuevo registro</h2>
            <div class="panel-sub">Completá la información y guardá los cambios.</div>
        </div>
        <button class="btn btn-light btn-sm" type="button" id="cancelEditor">Volver al listado</button>
    </div>
    <form id="recordForm">
        <input type="hidden" id="recordId">
        <div class="form-grid">
            <div class="field full">
                <label for="recordTitle"><?= in_array($page, ['estudiantes'], true) ? 'Nombre completo del estudiante *' : (in_array($page, ['pagos'], true) ? 'Número de comprobante / referencia *' : (in_array($page, ['solicitudes_docentes'], true) ? 'Nombre del postulante *' : (in_array($page, ['servicios'], true) ? 'Nombre del servicio *' : 'Nombre / título *'))) ?>
                </label>
                <input id="recordTitle" maxlength="190" required placeholder="Ingresá la información principal">
            </div>
            <div class="field full">
                <label for="recordDescription"><?= in_array($page, ['estudiantes'], true) ? 'Datos de contacto, documento y observaciones' : (in_array($page, ['pagos'], true) ? 'Detalle del pago, concepto y referencia de transacción' : (in_array($page, ['solicitudes_docentes'], true) ? 'Especialidad, experiencia y mensaje de solicitud' : 'Descripción / observaciones')) ?>
                </label>
                <textarea id="recordDescription" rows="4" maxlength="3000" placeholder="Agregá la información relevante">
                </textarea>
            </div>
            <div class="field">
                <label for="recordStatus">Estado</label>
                <select id="recordStatus">
                <option>Publicado</option>
                <option>Borrador</option>
                <option>Inactivo</option>
                </select>
            </div>
            <div class="field">
                <label for="recordLink">Enlace (opcional)</label>
                <input id="recordLink" type="url" placeholder="https://...">
            </div>
            <div class="field full">
                <label for="recordImage">URL de imagen (opcional)</label>
                <input id="recordImage" type="url" placeholder="https://...">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-light" type="button" id="cancelEditorBottom">Cancelar</button>
            <button class="btn" type="submit">Guardar registro</button>
        </div>
    </form>
</section>
<section class="panel" id="recordDetails" hidden>
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Consulta del registro</h2>
            <div class="panel-sub">Vista detallada de la información guardada.</div>
        </div>
        <button class="btn btn-light btn-sm" type="button" id="closeDetails">Volver al listado</button>
    </div>
    <div class="form-grid">
        <div class="field">
            <label>Nombre / título</label>
            <input id="detailTitle" readonly>
        </div>
        <div class="field">
            <label>Estado</label>
            <input id="detailStatus" readonly>
        </div>
        <div class="field full">
            <label>Descripción</label>
            <textarea id="detailDescription" rows="4" readonly>
            </textarea>
        </div>
        <div class="field full">
            <label>Enlace</label>
            <input id="detailLink" readonly>
        </div>
        <div class="field full">
            <label>URL de imagen</label>
            <input id="detailImage" readonly>
        </div>
        <div class="field">
            <label>Creado</label>
            <input id="detailCreated" readonly>
        </div>
        <div class="field">
            <label>Actualizado</label>
            <input id="detailUpdated" readonly>
        </div>
    </div>
    <div class="form-actions">
        <button class="btn" type="button" id="editFromDetails">Editar registro</button>
    </div>
</section>
<div class="input-note" style="margin-top:14px">ⓘ Estas acciones son una maqueta funcional del frontend: los datos se guardan en este navegador para probar la interfaz. Para que sean compartidos y persistentes en el sistema, hay que conectarlos al backend y a la base de datos.</div>

