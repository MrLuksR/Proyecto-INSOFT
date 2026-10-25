INADI - INTEGRACIÓN INICIAL DE MERCADO PAGO CHECKOUT PRO
========================================================

ARCHIVOS AGREGADOS
- pago.php: resumen de inscripción y botón para pagar.
- crear_preferencia.php: crea la preferencia en Mercado Pago desde el servidor.
- pago_resultado.php: consulta a Mercado Pago el estado del pago cuando vuelve el estudiante.
- mercadopago_config.php: Access Token y URL base.
- pago.css: estilos de las páginas de pago.

FLUJO
1. El estudiante completa Cursos/matricularme.php.
2. Los datos validados quedan en la sesión y se abre pago.php.
3. El botón envía a crear_preferencia.php, que obtiene el costo directamente de la base de datos.
4. El estudiante es redirigido a Mercado Pago Checkout Pro.
5. Al volver, pago_resultado.php consulta el pago a la API con el Access Token.

CONFIGURACIÓN
1. Abrí Cursos/mercadopago_config.php.
2. Reemplazá PEGAR_ACCESS_TOKEN_DE_PRUEBA_AQUI por el Access Token de PRUEBA de tu aplicación en Mercado Pago Developers.
3. Confirmá MP_BASE_URL. Para el proyecto local se dejó:
   http://localhost/Proyecto-INSOFT/Cursos
4. MP_CURRENCY_ID está configurado como UYU. Verificá que los costos de la base de datos estén expresados en pesos uruguayos. Si están en otra moneda, no habilites cobros reales hasta corregirlo.
5. PHP debe tener habilitada la extensión cURL (en XAMPP, revisá php.ini y reiniciá Apache).

IMPORTANTE
- No compartas ni publiques el Access Token. Para producción usá HTTPS y credenciales de producción protegidas fuera del repositorio.
- Mercado Pago puede exigir una URL pública válida para ciertas configuraciones. Si el retorno a localhost es rechazado, usá una URL HTTPS de desarrollo/túnel y actualizá MP_BASE_URL.
- La integración no crea aún una matrícula definitiva en la base de datos: no conocemos el esquema real de la tabla de matrículas del proyecto. El retorno aprobado se verifica consultando la API, pero falta conectar la confirmación con la tabla académica y procesar notificaciones/webhooks para una solución de producción.
- No marques una matrícula como pagada basándote solamente en que el navegador llegue a la página de éxito.
- Probá primero con credenciales y usuarios de prueba de Mercado Pago. No uses el Access Token de producción para las pruebas.
