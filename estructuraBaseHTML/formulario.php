<!DOCTYPE html>
<html lang="es" style="">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" name="viewport">
<meta content="mobile_stack" name="shell-type">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<style>
@layer base{
  html,body{width:100vw;margin:0;padding:0;background-color:#f8f9ff;}
  body{overscroll-behavior:none;}
  .pb-safe{padding-bottom:env(safe-area-inset-bottom,0px);}
  .pt-safe{padding-top:env(safe-area-inset-top,0px);}
  main>:first-child{margin-top:0!important;}
  main>:last-child{margin-bottom:0!important;}
}
::-webkit-scrollbar{display:none;}
</style>
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">
tailwind.config = { 
  darkMode: "class", 
  theme: { 
    extend: { 
      colors: { 
        "surface-bright": "#f8f9ff", "primary-fixed-dim": "#afc8ed", "on-error-container": "#93000a", "on-error": "#ffffff", "on-tertiary-fixed": "#00201d", "on-primary-container": "#7a93b5", "primary": "#00162d", "error-container": "#ffdad6", "surface-container-lowest": "#ffffff", "tertiary-fixed-dim": "#6bd8cb", "secondary-container": "#5bb8fe", "on-surface": "#0b1c30", "outline-variant": "#c4c6ce", "on-tertiary": "#ffffff", "on-primary-fixed": "#001c37", "secondary-fixed": "#cce5ff", "surface-dim": "#cbdbf5", "inverse-on-surface": "#eaf1ff", "surface": "#f8f9ff", "primary-fixed": "d2e4ff", "surface-container": "#e5eeff", "on-primary": "#ffffff", "on-surface-variant": "#43474d", "secondary": "#006398", "on-secondary": "#ffffff", "on-tertiary-fixed-variant": "#005049", "surface-container-low": "#eff4ff", "on-background": "#0b1c30", "surface-variant": "#d3e4fe", "surface-container-highest": "#d3e4fe", "tertiary": "#001916", "on-tertiary-container": "#29a195", "background": "#f8f9ff", "inverse-surface": "#213145", "inverse-primary": "#afc8ed", "error": "#ba1a1a", "on-secondary-fixed": "#001d31", "tertiary-fixed": "#89f5e7", "secondary-fixed-dim": "#93ccff", "surface-tint": "#476080", "surface-container-high": "#dce9ff", "tertiary-container": "#00302b", "on-primary-fixed-variant": "#2f4867", "on-secondary-fixed-variant": "#004b73", "outline": "#74777e", "on-secondary-container": "#00476e", "primary-container": "#0f2b48" 
      }, 
      borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, 
      spacing: { "gutter": "1.5rem", "margin": "2rem", "space-lg": "1.5rem", "gutter-mobile": "1rem", "space-sm": "0.5rem", "space-xl": "2.5rem", "space-md": "1rem", "space-xs": "0.25rem", "margin-mobile": "1rem" }, 
      fontFamily: { "label-md": ["Inter"], "headline-sm": ["Inter"], "headline-xl": ["Inter"], "headline-xl-mobile": ["Inter"], "display-lg": ["Inter"], "display-lg-mobile": ["Inter"], "headline-md": ["Inter"], "label-lg": ["Inter"], "body-lg": ["Inter"], "label-sm": ["Inter"], "headline-lg": ["Inter"], "body-sm": ["Inter"], "body-md": ["Inter"] }, 
      fontSize: { "label-md": ["12px", { lineHeight: "16px", letterSpacing: "0.02em", fontWeight: "600" }], "headline-sm": ["18px", { lineHeight: "26px", letterSpacing: "0em", fontWeight: "600" }], "headline-xl": ["36px", { lineHeight: "44px", letterSpacing: "-0.02em", fontWeight: "600" }], "headline-xl-mobile": ["26px", { lineHeight: "34px", letterSpacing: "-0.01em", fontWeight: "600" }], "display-lg": ["48px", { lineHeight: "56px", letterSpacing: "-0.02em", fontWeight: "700" }], "display-lg-mobile": ["32px", { lineHeight: "40px", letterSpacing: "-0.01em", fontWeight: "700" }], "headline-md": ["22px", { lineHeight: "30px", letterSpacing: "-0.005em", fontWeight: "600" }], "label-lg": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "500" }], "body-lg": ["16px", { lineHeight: "26px", letterSpacing: "0em", fontWeight: "400" }], "label-sm": ["11px", { lineHeight: "14px", letterSpacing: "0.03em", fontWeight: "600" }], "headline-lg": ["28px", { lineHeight: "36px", letterSpacing: "-0.01em", fontWeight: "600" }], "body-sm": ["12px", { lineHeight: "18px", letterSpacing: "0.01em", fontWeight: "400" }], "body-md": ["14px", { lineHeight: "22px", letterSpacing: "0em", fontWeight: "400" }] } 
    } 
  } 
};
</script>
</head>
<body class="bg-surface font-body-md text-on-surface antialiased flex flex-col min-h-screen">
<header class="fixed top-0 w-full z-50 pt-safe bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
<div class="h-16 px-gutter-mobile flex items-center justify-between">
<div class="flex items-center gap-space-xs">
<button aria-label="Volver" class="w-11 h-11 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors" onclick="history.back()"><span class="material-symbols-outlined text-[24px]">arrow_back</span></button>
<img alt="Logo Instituto de Informática INADI" class="h-8 w-auto object-contain" src="https://lh3.googleusercontent.com/aida/AEtjO1XvNW9QZTWdyQW308SEpvjwA5UrthoPvONxXBHJU47EjYh-kCpyB6TFbxFaSQDPOHyTO5feJ6ZbMcvTDWJo0KpaRGuF-ajm4QDf-7n1VAE1PccMiYCNu0oFLPZ25dSq6gT-xQZGjA6K28y0SzW1HNDPMx3Pu7jFKFepLEf_XDNelMxioOmZsQuAO08e8EdHLd_tmBHGh95RLm3xTskWUfuFh50Q8wdgmJQctVH5uQBrln61ynoLeSfw59s">
<div class="flex flex-col ml-1">
<div class="flex items-center gap-space-xs">
<span class="font-label-lg text-label-lg text-primary tracking-tight font-bold">INADI Salto</span>
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium">• Acta Docente</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant font-normal leading-none">Instituto de Informática</span>
</div>
</div>
<div class="flex items-center gap-space-xs">
<a aria-label="Contacto y ayuda" class="w-11 h-11 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors" data-path="soporte-institucional" href="#"><span class="material-symbols-outlined text-[22px]">help_outline</span></a>
<div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-on-primary text-[18px]">person</span></div>
</div>
</div>
</header>
<main class="flex flex-col relative w-full pt-16 pb-safe px-gutter-mobile bg-surface min-h-screen">
<div class="flex flex-col w-full pb-10 pb-safe">
<!-- Hero / Header de Convocatoria -->
<div class="relative w-full rounded-2xl bg-surface-container-low p-space-md shadow-sm mb-space-md overflow-hidden">
<div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-secondary/10 pointer-events-none blur-2xl"></div>
<div class="flex items-center justify-between gap-space-xs mb-space-xs"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container text-primary font-label-sm text-label-sm font-semibold"><span class="w-2 h-2 rounded-full bg-secondary"></span>Sistema de Gestión Académica</span><span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">domain</span>Sede Salto</span></div>
<h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mb-1">Registro de Acta Docente</h1>
<p class="font-body-md text-body-md text-on-surface-variant mb-space-sm">Formulario administrativo para enrolamiento de docentes y sincronización en la base de datos de INADI Salto.</p>
</div>
<form class="flex flex-col gap-space-md" id="docente-form" onsubmit="event.preventDefault(); handleFormSubmit();">
<!-- BLOQUE 1: Cuenta y Acceso (Tabla: usuario) -->
<section class="rounded-2xl bg-surface-container-lowest p-space-md shadow-sm">
<div class="flex items-center justify-between mb-space-md pb-2 border-b border-surface-container">
<div class="flex items-center gap-2">
<div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">account_circle</span></div>
<div>
<h2 class="font-headline-sm text-headline-sm text-primary">1. Datos de Cuenta y Acceso</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Credenciales para autenticación institucional en la plataforma</p>
</div>
</div>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container-low text-primary font-label-sm text-label-sm border border-surface-container font-medium"><span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>estado: Activo</span>
</div>
<div class="flex flex-col gap-space-sm">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="nombre_usuario">Nombre de Usuario <span class="text-error">*</span></label>
<div class="relative"><span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-outline">alternate_email</span><input class="w-full pl-9 pr-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="nombre_usuario" name="nombre_usuario" placeholder="ej: mrodriguez" required="" type="text"></div>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="correo">Correo Electrónico <span class="text-error">*</span></label>
<div class="relative"><span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-outline">mail</span><input class="w-full pl-9 pr-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="correo" name="correo" placeholder="docente@inadi.edu.uy" required="" type="email"></div>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="password">Contraseña de Acceso <span class="text-error">*</span></label>
<div class="relative"><span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-outline">lock</span><input class="w-full pl-9 pr-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="password" name="password" placeholder="••••••••••••" required="" type="password"></div>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="estado">Estado en Sistema</label>
<select class="w-full px-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all appearance-none" id="estado" name="estado">
<option selected="" value="activo">Activo (Habilitado en sistema)</option>
<option value="pendiente">Pendiente de Aprobación</option>
<option value="inactivo">Inactivo / En reserva</option>
</select>
</div>
</div>
</div>
</section>
<!-- BLOQUE 2: Datos Personales e Identidad (Tabla: docente) -->
<section class="rounded-2xl bg-surface-container-lowest p-space-md shadow-sm">
<div class="flex items-center gap-2 mb-space-md pb-2 border-b border-surface-container">
<div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">badge</span></div>
<div>
<h2 class="font-headline-sm text-headline-sm text-primary">2. Datos Personales e Identidad</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Identificación legal para legajo y contratos</p>
</div>
</div>
<div class="flex flex-col gap-space-sm">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="nombre">Nombres <span class="text-error">*</span></label><input class="w-full px-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="nombre" maxlength="20" name="nombre" placeholder="Ej: Martín" required="" type="text">
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="apellido">Apellidos <span class="text-error">*</span></label><input class="w-full px-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="apellido" maxlength="50" name="apellido" placeholder="Ej: Rodríguez Silveira" required="" type="text">
</div>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="cedula">Cédula de Identidad (C.I.) <span class="text-error">*</span></label>
<div class="relative"><span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-outline">fingerprint</span><input class="w-full pl-9 pr-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="cedula" name="cedula" placeholder="Ej: 4.892.115-3" required="" type="text"></div>
</div>
</div>
</section>
<!-- BLOQUE 3: Perfil Profesional y Académico (Tabla: docente) -->
<section class="rounded-2xl bg-surface-container-lowest p-space-md shadow-sm">
<div class="flex items-center gap-2 mb-space-md pb-2 border-b border-surface-container">
<div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">school</span></div>
<div>
<h2 class="font-headline-sm text-headline-sm text-primary">3. Perfil Profesional y Cátedra</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Asignación académica, disponibilidad y legajo curricular</p>
</div>
</div>
<div class="flex flex-col gap-space-sm">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="titulo">Título Académico <span class="text-error">*</span></label><input class="w-full px-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="titulo" maxlength="100" name="titulo" placeholder="Ej: Licenciado en Informática / Ing. en Sistemas" required="" type="text">
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="especialidad">Especialidad o Cátedra <span class="text-error">*</span></label><input class="w-full px-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="especialidad" maxlength="100" name="especialidad" placeholder="Ej: Programación Web / Ciberseguridad / Bases de Datos" required="" type="text">
</div>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="horario">Disponibilidad Horaria <span class="text-error">*</span></label>
<select class="w-full px-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all appearance-none" id="horario" name="horario" required="">
<option value="Turno Nocturno (18:30 a 22:30)">Turno Nocturno (18:30 a 22:30)</option>
<option value="Turno Vespertino (14:00 a 18:00)">Turno Vespertino (14:00 a 18:00)</option>
<option value="Turno Matutino (08:30 a 12:30)">Turno Matutino (08:30 a 12:30)</option>
<option value="Sábados Intensivo (09:00 a 17:00)">Sábados Intensivo (09:00 a 17:00)</option>
<option value="Horario Flexible / Mixto">Horario Flexible / Mixto</option>
</select>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface mb-1" for="curriculum_vitae">Curriculum Vitae / Legajo Digital <span class="text-error">*</span></label>
<div class="group relative flex flex-col items-center justify-center p-space-md rounded-2xl bg-surface-container-low hover:bg-surface-container transition-all cursor-pointer text-center mb-2" onclick="document.getElementById('file-cv').click()"><input accept=".pdf" class="hidden" id="file-cv" onchange="document.getElementById('cv-file-name').innerText = this.files[0]?.name || 'CV cargado'; document.getElementById('curriculum_vitae').value = 'legajo/cv/' + (this.files[0]?.name || 'cv_docente.pdf')" type="file"><div class="w-12 h-12 rounded-full bg-surface-container-high text-secondary flex items-center justify-center mb-2 group-hover:scale-105 transition-transform"><span class="material-symbols-outlined text-[28px]">upload_file</span></div><span class="font-label-lg text-label-lg text-primary font-semibold" id="cv-file-name">Cargar archivo PDF de Legajo / CV</span><span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Al seleccionar se registrará la ruta para el campo en la base de datos</span></div>
<div class="relative"><span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-outline">link</span><input class="w-full pl-9 pr-3.5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md placeholder:text-outline focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-secondary transition-all" id="curriculum_vitae" maxlength="255" name="curriculum_vitae" placeholder="O ingrese URL directa / ruta de archivo (ej: https://inadi.edu.uy/legajos/mrodriguez_cv.pdf)" required="" type="text"></div>
</div>
</div>
</section>
<!-- BLOQUE 4: Confirmación y Acciones de Base de Datos -->
<section class="rounded-2xl bg-surface-container-lowest p-space-md shadow-sm">
<div class="flex flex-col gap-space-xs">
<button class="w-full py-3.5 px-space-md rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-bold flex items-center justify-center gap-2 shadow-md hover:bg-primary-container active:scale-[0.99] transition-all" type="submit"><span class="material-symbols-outlined text-[20px]">save</span>Guardar y Generar Acta Docente</button>
<button class="w-full py-2.5 px-space-md rounded-xl bg-surface-container-low text-primary font-label-md text-label-md font-semibold hover:bg-surface-container transition-colors flex items-center justify-center gap-1.5" onclick="showToast('Registro temporal guardado en memoria', 'bookmark')" type="button"><span class="material-symbols-outlined text-[18px]">drafts</span>Guardar Borrador Local</button>
</div>
<div class="mt-space-md pt-space-sm border-t border-transparent text-center">
<p class="font-body-sm text-body-sm text-on-surface-variant flex items-center justify-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">shield</span>Soporte Administrativo: <a class="text-secondary font-semibold hover:underline" href="mailto:rrhh@inadi.edu.uy">rrhh@inadi.edu.uy</a></p>
<p class="font-label-sm text-label-sm text-on-surface-variant mt-1">INADI Salto • Sede Académica Salto, Uruguay</p>
</div>
</section>
</form>
<!-- Notificación Toast Dinámica (Micro-interacción) -->
<div class="fixed bottom-6 left-4 right-4 max-w-sm mx-auto p-3.5 rounded-xl bg-primary text-on-primary shadow-xl font-label-md text-label-md flex items-center gap-2 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none z-50" id="toast">
<span class="material-symbols-outlined text-[20px] text-tertiary-fixed" id="toast-icon">check_circle</span>
<span id="toast-msg" class="">Borrador guardado exitosamente</span>
</div>
</div>
<script>
  function updateFileLabel(input, labelId) {
    if (input.files && input.files.length > 0) {
      const fileName = input.files.length === 1 ? input.files[0].name : input.files.length + ' archivos seleccionados';
      document.getElementById(labelId).innerText = fileName;
    }
  }

  function scrollToSection(sectionId) {
    const el = document.getElementById(sectionId);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function showToast(msg, icon = 'check_circle') {
    const toast = document.getElementById('toast');
    const msgEl = document.getElementById('toast-msg');
    const iconEl = document.getElementById('toast-icon');
    
    msgEl.innerText = msg;
    iconEl.innerText = icon;
    
    toast.classList.remove('translate-y-24', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');
    
    setTimeout(() => {
      toast.classList.add('translate-y-24', 'opacity-0');
      toast.classList.remove('translate-y-0', 'opacity-100');
    }, 3200);
  }

  function saveDraftToast() {
    showToast('Borrador guardado localmente en tu navegador', 'save');
  }

  function handleFormSubmit() {
    // Simulación de envío exitoso
    showToast('¡Postulación enviada con éxito a INADI Salto!', 'verified');
  }
</script>
</main>
</body>
</html>