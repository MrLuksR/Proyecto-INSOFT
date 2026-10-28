
<div class="welcome-row">
    <div>
        <div class="eyebrow">Indicadores del sistema</div>
        <h1 class="heading"><?= e($pageTitle) ?>
        </h1>
        <p class="subtitle">Vista demostrativa de indicadores. Los valores son de ejemplo y deberán alimentarse con datos reales del backend.</p>
    </div>
    <button class="btn btn-light" type="button" onclick="window.print()">Imprimir reporte</button>
</div>
<div class="stats">
    <div class="stat-card">
        <div class="stat-top">Visitas del mes <span class="stat-icon">◉</span>
    </div>
    <div class="stat-num">12.840</div>
    <div class="stat-foot">
        <span class="positive">+8,4%</span> frente al mes anterior</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">Contrataciones <span class="stat-icon mint">✓</span>
    </div>
    <div class="stat-num">186</div>
    <div class="stat-foot">Solicitudes confirmadas · demo</div>
</div>
<div class="stat-card">
    <div class="stat-top">Solicitudes pendientes <span class="stat-icon amber">◷</span>
</div>
<div class="stat-num">24</div>
<div class="stat-foot">Requieren revisión administrativa</div>
</div>
<div class="stat-card">
    <div class="stat-top">Tasa de conversión <span class="stat-icon violet">↗</span>
</div>
<div class="stat-num">4,8%</div>
<div class="stat-foot">Visitas que generan una solicitud · demo</div>
</div>
</div>
<div class="columns">
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Actividad mensual</h2>
                <div class="panel-sub">Visitas y contrataciones de ejemplo</div>
            </div>
            <span class="status-pill pill-blue">Últimos 6 meses</span>
        </div>
        <div class="chart-bars" role="img" aria-label="Gráfico de barras ilustrativo de actividad mensual">
            <div class="chart-bar-group">
                <div class="chart-bars-inner">
                    <i style="height:38%">
                    </i>
                    <b style="height:22%">
                    </b>
                </div>
                <span>Abr</span>
            </div>
            <div class="chart-bar-group">
                <div class="chart-bars-inner">
                    <i style="height:48%">
                    </i>
                    <b style="height:29%">
                    </b>
                </div>
                <span>May</span>
            </div>
            <div class="chart-bar-group">
                <div class="chart-bars-inner">
                    <i style="height:57%">
                    </i>
                    <b style="height:36%">
                    </b>
                </div>
                <span>Jun</span>
            </div>
            <div class="chart-bar-group">
                <div class="chart-bars-inner">
                    <i style="height:45%">
                    </i>
                    <b style="height:31%">
                    </b>
                </div>
                <span>Jul</span>
            </div>
            <div class="chart-bar-group">
                <div class="chart-bars-inner">
                    <i style="height:72%">
                    </i>
                    <b style="height:48%">
                    </b>
                </div>
                <span>Ago</span>
            </div>
            <div class="chart-bar-group">
                <div class="chart-bars-inner">
                    <i style="height:88%">
                    </i>
                    <b style="height:62%">
                    </b>
                </div>
                <span>Set</span>
            </div>
        </div>
        <div class="chart-legend">
            <span>
            <i>
            </i> Visitas</span>
            <span>
            <i>
            </i> Contrataciones</span>
        </div>
    </section>
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Resumen de actividad</h2>
                <div class="panel-sub">Datos ilustrativos</div>
            </div>
        </div>
        <div class="activity">
            <div class="activity-icon">♙</div>
            <div class="activity-text">
                <strong>Altas de estudiantes</strong>
                <div class="activity-time">48 registros durante el período</div>
            </div>
        </div>
        <div class="activity">
            <div class="activity-icon">✉</div>
            <div class="activity-text">
                <strong>Solicitudes docentes</strong>
                <div class="activity-time">12 solicitudes por revisar</div>
            </div>
        </div>
        <div class="activity">
            <div class="activity-icon">＄</div>
            <div class="activity-text">
                <strong>Pagos confirmados</strong>
                <div class="activity-time">Estado a verificar con la pasarela o el registro real</div>
            </div>
        </div>
    </section>
</div>
<section class="panel">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Últimos eventos</h2>
            <div class="panel-sub">Ejemplo de reporte de actividad administrativa</div>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Actividad</th>
                    <th>Módulo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>26/10/2026</td>
                    <td>Alta de estudiante (demostración)</td>
                    <td>Estudiantes</td>
                    <td>
                        <span class="status-pill pill-blue">Ejemplo</span>
                    </td>
                </tr>
                <tr>
                    <td>25/10/2026</td>
                    <td>Solicitud de contratación recibida</td>
                    <td>Contratación docente</td>
                    <td>
                        <span class="status-pill pill-orange">Pendiente</span>
                    </td>
                </tr>
                <tr>
                    <td>24/10/2026</td>
                    <td>Comprobante de pago emitido</td>
                    <td>Pagos</td>
                    <td>
                        <span class="status-pill pill-green">Confirmado · demo</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

