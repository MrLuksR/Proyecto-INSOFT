<?php
    // Función de encriptación
    function encrypt_aes_256_gcm($plaintext, $key)
    {
        $iv = random_bytes(12);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext, // El texto a cifrar
            'aes-256-gcm',
            $key, // Clave maestra
            OPENSSL_RAW_DATA,
            $iv, // Initiation vector inicia el decifrado
            $tag // Sello que permite detectar modificaciones (generado automáticamente)
        );

        if ($ciphertext === false) {
            throw new Exception('Error al cifrar los datos.');
        }

        return [
            'ciphertext' => $ciphertext,
            'iv' => $iv,
            'tag' => $tag
        ];
    }

    // Función de desencriptar
    function decrypt_aes_256_gcm($ciphertext, $key, $iv, $tag)
    {
        $plaintext = openssl_decrypt(
            $ciphertext, // Texto cifrado
            'aes-256-gcm', // Tipo de cifrado (OpenSSL)
            $key, // Llave maestra
            OPENSSL_RAW_DATA, // Constante criptográfica
            $iv, // iv generado
            $tag // tag generado
        );

        if ($plaintext === false) {
            throw new Exception('No se pudieron descifrar los datos.');
        }

        return $plaintext;
    }
?>