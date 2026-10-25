# Guía de integración con INSOFT

## 1. Datos que hay que confirmar

- Esquema SQL de la base `insoft` (estructura de tablas y claves foráneas).
- Archivo actual de conexión PDO.
- Claves de sesión establecidas después del login.
- Nombre y ubicación reales del archivo para cerrar sesión.
- Tablas/relaciones que vinculan usuarios docentes con cursos y estudiantes.
- Roles y permisos existentes.

## 2. Seguridad

- Reutilizar autenticación institucional, no crear contraseñas paralelas.
- Verificar el rol en el servidor en cada módulo y operación.
- Limitar cada consulta al identificador del docente autenticado.
- Usar consultas preparadas PDO para valores variables.
- Validar datos en servidor; escapar salida HTML con `htmlspecialchars`.
- Agregar tokens CSRF a operaciones POST antes de habilitar escritura.
- Proteger materiales y descargas para que no se puedan abrir mediante una URL pública sin autorización.
- Registrar los cambios sensibles cuando el esquema del sistema lo permita.
- No mostrar mensajes internos de PDO al usuario.

## 3. No inventar el esquema

No se incluyen consultas académicas ni tablas nuevas porque no se ha verificado el esquema real. En particular, no se debe asumir que las tablas administrativas `admin_users` o `admin_items` representan docentes, cursos o estudiantes.

Cuando se disponga del SQL, implementar los módulos uno a uno:
1. Cursos asignados y dashboard con conteos reales.
2. Estudiantes filtrados por curso y permisos.
3. Calificaciones y evaluaciones.
4. Asistencia.
5. Actividades y entregas.
6. Materiales, calendario, notificaciones e informes.
7. Perfil docente, respetando los campos disponibles.

## 4. Identidad visual

La interfaz utiliza una paleta de referencia:
- Azul principal `#2455a6`
- Azul oscuro `#193e80`
- Azul profundo `#122d5d`
- Fondo `#f3f6fb`
- Tarjetas blancas y bordes `#e1e7f0`
- Cian de acento `#42b7d5`

El logo institucional del proyecto de referencia se encuentra indicado como `../Elementos Gráficos/Logo Inadi con Brillo.png`. En este paquete se utiliza una marca tipográfica provisional para evitar una ruta de imagen rota. Sustituirla por el archivo institucional existente cuando se confirme su ruta relativa correcta.
