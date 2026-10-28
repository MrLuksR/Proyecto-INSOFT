
<div class="welcome-row">
    <div>
        <div class="eyebrow">Cuenta institucional</div>
        <h1 class="heading">Mi perfil</h1>
        <p class="subtitle">Información de la sesión administrativa actual.</p>
    </div>
</div>
<section class="panel" style="max-width:700px">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Datos de la cuenta</h2>
            <div class="panel-sub">Los cambios de perfil deben conectarse al backend.</div>
        </div>
    </div>
    <div class="form-grid">
        <div class="field">
            <label>Nombre</label>
            <input value="<?= e($nombre) ?>" readonly>
        </div>
        <div class="field">
            <label>Apellido</label>
            <input value="<?= e($apellido) ?>" readonly>
        </div>
        <div class="field">
            <label>Nombre de usuario</label>
            <input value="<?= e($admin->getNombreUsuario()) ?>" readonly>
        </div>
        <div class="field">
            <label>Rol</label>
            <input value="Administrativo" readonly>
        </div>
        <div class="field">
            <label>Cargo</label>
            <input value="<?= e($cargo) ?>" readonly>
        </div>
        <div class="field">
            <label>Área</label>
            <input value="<?= e($area) ?>" readonly>
        </div>
        <div class="field">
            <label>Nivel de acceso</label>
            <input value="<?= e($nivelAcceso) ?>" readonly>
        </div>
        <div class="field">
            <label>Fecha de ingreso</label>
            <input value="<?= e($fechaIngreso) ?>" readonly>
        </div>
    </div>
</section>

