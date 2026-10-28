<?php /** Menú lateral compartido. $modules y $page se definen en index.php. */ ?>

<aside class="sidebar" id="sidebar">

    <a class="brand" href="index.php?page=dashboard" aria-label="INADI INSOFT - Inicio">

        <img src="../Elementos Gráficos/Logo Inadi sin Brillo.png" alt="" width="60px" class="brand-mark">

        <div>
            <div class="brand-name">INADI</div>
            <div class="brand-sub">PLATAFORMA INSOFT</div>
        </div>

    </a>

    <div class="nav-label">Menú principal</div>

    <nav class="nav">

        <?php

        $renderNavLink = static function (string $key) use ($modules, $page): void {

            $item = $modules[$key];

            echo '<a class="nav-link ' . ($page === $key ? 'active' : '') . '" href="?page=' . e($key) . '"><span class="nav-icon">' . e($item[1]) . '</span><span>' . e($item[0]) . '</span>';

            if ($page === $key) {
                echo '<span class="nav-dot"></span>';
            }

            echo '</a>';

        };

        ?>


        <?php $renderNavLink('dashboard'); ?>


        <!-- MENÚ DOCENTE -->

        <details class="nav-group" <?= in_array($page, ['panel_docente', 'docentes', 'solicitudes_docentes', 'historial_docente'], true) ? 'open' : '' ?>>

            <summary>
                <span class="nav-icon">♙</span>
                <span>Docente</span>
                <span class="nav-chevron">⌄</span>
            </summary>

            <div class="nav-submenu">

                <?php $renderNavLink('panel_docente'); ?>

                <?php $renderNavLink('docentes'); ?>

                <?php $renderNavLink('solicitudes_docentes'); ?>

                <?php $renderNavLink('historial_docente'); ?>

            </div>

        </details>


        <!-- MENÚ ESTUDIANTE -->

        <details class="nav-group" <?= in_array($page, ['panel_estudiante', 'estudiantes', 'pagos'], true) ? 'open' : '' ?>>

            <summary>
                <span class="nav-icon">♧</span>
                <span>Estudiante</span>
                <span class="nav-chevron">⌄</span>
            </summary>

            <div class="nav-submenu">

                <?php $renderNavLink('panel_estudiante'); ?>

                <?php $renderNavLink('estudiantes'); ?>

                <?php $renderNavLink('pagos'); ?>

            </div>

        </details>


        <!-- RESTO DE MÓDULOS -->

        <?php

        // MOSTRAR LOS MÓDULOS RESTANTES

        foreach (
            [
                'cursos',
                'servicios',
                'estadisticas',
                'reportes',
                'analiticas',
                'noticias',
                'banners',
                'galeria',
                'empresas',
                'comentarios',
                'formularios',
                'slides',
                'usuarios',
                'ajustes'
            ] as $modulo
        ) {
            $renderNavLink($modulo);
        }

        ?>

    </nav>

    <!-- VOLVER A LA PÁGINA PRINCIPAL -->

    <div class="sidebar-back">

        <a
            href="../Inicio/inicioindex.php"
            class="nav-link"
            title="Volver a la página principal"
        >

            <span class="nav-icon">
                <i class="fa-solid fa-arrow-left"></i>
            </span>

            <span>Volver al inicio</span>

        </a>

    </div>

</aside>