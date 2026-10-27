<?php

/**
 * INSOFT - CONFIGURACIÓN MERCADO PAGO
 * ENTORNO EXCLUSIVO DE PRUEBAS
 */

// ACCESS TOKEN DE PRUEBA
define(
    'MP_ACCESS_TOKEN',
    getenv('MP_TEST_ACCESS_TOKEN') ?: ''
);

// PUBLIC KEY DE PRUEBA
define(
    'MP_PUBLIC_KEY',
    getenv('MP_TEST_PUBLIC_KEY') ?: ''
);

// VALIDAR CONFIGURACIÓN
if (MP_ACCESS_TOKEN === '') {
    throw new RuntimeException(
        'No se configuró el Access Token de prueba.'
    );
}