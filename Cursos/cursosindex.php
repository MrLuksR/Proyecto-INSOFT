<?php


// ============================================================
// INADI - Página principal
// Archivo: index.php
// ============================================================

$cursos = [
    [
        'categoria' => 'TECNOLOGÍA',
        'titulo' => 'INTELIGENCIA ARTIFICIAL (IA) APLICADA',
        'descripcion' => 'Machine Learning, Python y automatización.',
        'duracion' => '6 meses',
        'icono' => '🤖',
        'color' => 'ia',
        'inicio' => 'Inicio inmediato'
    ],
    [
        'categoria' => 'SEGURIDAD INFORMÁTICA',
        'titulo' => 'FORMACIÓN EN CIBERSEGURIDAD',
        'descripcion' => 'Protección de sistemas, redes y datos.',
        'duracion' => '9 meses',
        'icono' => '🛡️',
        'color' => 'cyber',
        'inicio' => 'Inicio inmediato'
    ],
    [
        'categoria' => 'PROGRAMACIÓN',
        'titulo' => 'PYTHON BÁSICO',
        'descripcion' => 'Aprendé programación desde cero con Python.',
        'duracion' => '4 meses',
        'icono' => '🐍',
        'color' => 'python',
        'inicio' => 'Inicio inmediato'
    ],
    [
        'categoria' => 'DISEÑO',
        'titulo' => 'DISEÑO GRÁFICO',
        'descripcion' => 'Diseño digital, composición y herramientas creativas.',
        'duracion' => '6 meses',
        'icono' => '🎨',
        'color' => 'design',
        'inicio' => 'Inicio inmediato'
    ],
];

$cursosRapidos = [
    ['nombre' => 'Python Básico', 'icono' => '🐍', 'color' => 'python'],
    ['nombre' => 'Diseño Gráfico', 'icono' => '🎨', 'color' => 'design'],
    ['nombre' => 'Ciberseguridad', 'icono' => '🛡️', 'color' => 'cyber'],
    ['nombre' => 'Inteligencia Artificial', 'icono' => '🤖', 'color' => 'ia'],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>INADI | Instituto de Informática</title>

    <meta
        name="description"
        content="INADI - Instituto de Informática. Cursos de tecnología, programación, diseño, inteligencia artificial y ciberseguridad."
    >

    <link rel="stylesheet" href="syles.css">
</head>

<body>

<div class="app">

    <!-- =====================================================
         SIDEBAR
    ====================================================== -->
    <aside class="sidebar">

        <div class="brand">

            <div class="brand-logo">
                <span>INADI</span>
            </div>

            <div class="brand-name">
                <strong>INADI</strong>
                <small>DESDE 1992</small>
            </div>

        </div>

        <nav class="menu">

            <a href="#" class="menu-item">
                <span class="menu-icon">⌂</span>
                <span>Nosotros</span>
            </a>

            <a href="#cursos" class="menu-item active">
                <span class="menu-icon">▣</span>
                <span>Cursos</span>
            </a>

            <a href="#" class="menu-item">
                <span class="menu-icon">☷</span>
                <span>Matrícula</span>
            </a>

            <a href="#contacto" class="menu-item">
                <span class="menu-icon">✉</span>
                <span>Contacto</span>
            </a>

            <a href="#" class="menu-item">
                <span class="menu-icon">▦</span>
                <span>Galería</span>
            </a>

            <a href="#" class="menu-item">
                <span class="menu-icon">♧</span>
                <span>Convenios</span>
            </a>

        </nav>

        <div class="sidebar-bottom">

            <a href="#" class="menu-item">
                <span class="menu-icon">▦</span>
                <span>Empresas</span>
            </a>

            <a href="#contacto" class="contact-button">
                Contactar
            </a>

        </div>

    </aside>


    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->
    <main class="main">

        <!-- HEADER -->
        <header class="page-header">

            <div>
                <p class="eyebrow">
                    INSTITUTO DE INFORMÁTICA · SALTO · URUGUAY
                </p>

                <h1>
                    Bienvenido a <span>INADI</span> 👋
                </h1>

                <p class="header-description">
                    Formación tecnológica y profesional desde 1992.
                </p>
            </div>

            <div class="header-badge">
                <span>34</span>
                <small>AÑOS DE EXPERIENCIA</small>
            </div>

        </header>


        <!-- =================================================
             ESTADÍSTICAS
        ================================================== -->
        <section class="stats">

            <div class="stat-card">
                <div class="stat-icon gold">🏆</div>

                <div>
                    <strong>34</strong>
                    <span>Años de experiencia</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue">📚</div>

                <div>
                    <strong>10</strong>
                    <span>Cursos activos</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">🎓</div>

                <div>
                    <strong>10K</strong>
                    <span>Certificados emitidos</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">🤝</div>

                <div>
                    <strong>50</strong>
                    <span>Empresas capacitadas</span>
                </div>
            </div>

        </section>


        <!-- =================================================
             BANNER AULA VIRTUAL
        ================================================== -->
        <section class="virtual-banner">

            <div class="banner-content">

                <span class="banner-label">
                    ESPACIO VIRTUAL · AULAS DIGITALES ACTIVAS
                </span>

                <h2>
                    INICIÁ SESIÓN O REGISTRATE
                </h2>

                <p>
                    Accedé a tus cursos, materiales y contenidos
                    educativos desde cualquier lugar.
                </p>

                <div class="banner-buttons">

                    <a href="#" class="btn btn-primary">
                        🔑 Acceder al Aula Virtual
                    </a>

                    <a href="#cursos" class="btn btn-outline">
                        👤 Registrarme en Cursos
                    </a>

                </div>

            </div>

            <div class="banner-decoration">
                💻
            </div>

        </section>


        <!-- =================================================
             CURSOS DESTACADOS
        ================================================== -->
        <section class="quick-courses">

            <div class="section-heading">

                <div>
                    <span class="section-label">
                        CURSOS DE INICIO INMEDIATO
                    </span>

                    <h2>
                        Comenzá a estudiar hoy
                    </h2>
                </div>

                <a href="#cursos" class="link-arrow">
                    Ver todos →
                </a>

            </div>


            <div class="quick-grid">

                <?php foreach ($cursosRapidos as $curso): ?>

                    <a href="#cursos"
                       class="quick-card <?= htmlspecialchars($curso['color']) ?>">

                        <div class="quick-icon">
                            <?= $curso['icono'] ?>
                        </div>

                        <div>
                            <strong>
                                <?= htmlspecialchars($curso['nombre']) ?>
                            </strong>

                            <span>
                                Ver curso →
                            </span>
                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

        </section>


        <!-- =================================================
             AVISO ACADÉMICO
        ================================================== -->
        <div class="academic-notice">

            <span class="notice-icon">📢</span>

            <div>
                <strong>
                    Período Lectivo 2026
                </strong>

                <p>
                    Inscripciones abiertas para todos los cursos
                    en INADI Salto.
                </p>
            </div>

            <a href="#">
                Ver calendario académico →
            </a>

        </div>


        <!-- =================================================
             SECCIÓN CURSOS
        ================================================== -->
        <section id="cursos" class="courses-section">

            <div class="section-heading courses-heading">

                <div>

                    <span class="section-label">
                        PLATAFORMA VIRTUAL INADI · AULAS DIGITALES ACTIVAS
                    </span>

                    <h2>
                        Cursos oficiales INADI
                    </h2>

                    <p>
                        Elegí la formación que querés comenzar.
                    </p>

                </div>

                <div class="course-count">
                    <strong>10</strong>
                    <span>cursos activos</span>
                </div>

            </div>


            <!-- FILTROS -->
            <div class="filters">

                <button class="filter active">
                    Mis cursos
                </button>

                <button class="filter">
                    Todos los cursos
                </button>

                <button class="filter">
                    Todas las duraciones
                </button>

                <div class="filter-search">
                    🔎
                    <input
                        type="text"
                        placeholder="Filtrar por código de asignatura"
                    >
                </div>

                <div class="location">
                    📍 INADI Salto
                </div>

            </div>


            <!-- TARJETAS -->
            <div class="courses-grid">

                <?php foreach ($cursos as $curso): ?>

                    <article class="course-card">

                        <div class="course-image <?= htmlspecialchars($curso['color']) ?>">

                            <div class="course-image-icon">
                                <?= $curso['icono'] ?>
                            </div>

                            <span class="course-tag">
                                <?= htmlspecialchars($curso['categoria']) ?>
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                <?= htmlspecialchars($curso['titulo']) ?>
                            </h3>

                            <p class="course-description">
                                <?= htmlspecialchars($curso['descripcion']) ?>
                            </p>

                            <div class="course-info">

                                <span>
                                    ⏱️ <?= htmlspecialchars($curso['duracion']) ?>
                                </span>

                                <span>
                                    📍 INADI Salto
                                </span>

                                <span>
                                    🟢 <?= htmlspecialchars($curso['inicio']) ?>
                                </span>

                            </div>

                            <a href="#" class="course-button">
                                Ver información del curso →
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>


        <!-- =================================================
             CONTACTO
        ================================================== -->
        <section id="contacto" class="contact-section">

            <div>
                <span class="section-label">
                    ¿TENÉS ALGUNA CONSULTA?
                </span>

                <h2>
                    Estamos para ayudarte
                </h2>

                <p>
                    Comunicate con INADI para recibir información
                    sobre cursos, matrículas y propuestas para empresas.
                </p>
            </div>

            <a href="#" class="btn btn-primary">
                Contactar a INADI
            </a>

        </section>


        <!-- FOOTER -->
        <footer class="footer">

            <div>
                <strong>INADI</strong>
                <span>
                    Instituto de Informática
                </span>
            </div>

            <p>
                © <?= date('Y') ?> INADI · Salto, Uruguay
            </p>

        </footer>

    </main>

</div>

</body>
</html>