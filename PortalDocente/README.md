# Portal Docente INADI – INSOFT

## Contenido

- `index.php`: interfaz principal y navegación de módulos.
- `CSS/docente.css`: diseño responsive basado en la paleta azul del panel administrativo de referencia.
- `JS/docente.js`: menú móvil accesible.
- `Estructura/conexion.php.example`: ejemplo orientativo de conexión PDO; no reemplaza la conexión existente.
- `INTEGRACION.md`: pasos necesarios antes de habilitar datos reales.

## Instalación local

1. Descomprime `Portal_Docente_INADI_INSOFT.zip` dentro de la carpeta del proyecto en `htdocs`, por ejemplo `C:\xampp\htdocs\insoft\Docente`.
2. Comprueba la ruta real de la aplicación. Los enlaces a `../index.php` y `../logout.php` son ejemplos que deben coincidir con el proyecto instalado.
3. Inicia Apache y MySQL desde XAMPP.
4. Inicia sesión con el mecanismo de autenticación existente de INSOFT y asegúrate de que la sesión establezca `$_SESSION['docente_id']` y, si existe, `$_SESSION['docente_nombre']`.
5. Abre la ruta local correspondiente, por ejemplo `http://localhost/insoft/Docente/`.
6. Si tu aplicación usa otra clave de sesión, adapta la comprobación al inicio de `index.php` para reutilizar la sesión existente; no crees un inicio de sesión paralelo.

## Importante

Este ZIP es una **base de interfaz y navegación**. No se puede afirmar que esté integrado con la base de datos real porque todavía no se ha verificado el esquema SQL de `insoft`, las relaciones académicas ni el archivo real de autenticación/conexión.

Los módulos muestran explícitamente que falta esa integración y no inventan cursos, estudiantes, notas, asistencias ni estadísticas. Para completar la funcionalidad se necesita el SQL de la base de datos (estructura de tablas, sin necesidad de datos personales), además de los archivos reales de conexión, sesión, menú y cierre de sesión.

No uses el ejemplo de conexión para sobrescribir la configuración que ya existe. No publiques este portal en producción hasta verificar permisos, consultas, rutas y protección de formularios.
