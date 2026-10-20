-- Ejecutar en phpMyAdmin sobre la base de datos insoft.
-- No elimina ni modifica la tabla existente usuarios.
CREATE TABLE IF NOT EXISTS portal_eventos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    owner_key VARCHAR(255) NOT NULL,
    owner_name VARCHAR(180) NOT NULL,
    title VARCHAR(120) NOT NULL,
    event_type ENUM('Clase','Evaluación','Entrega','Reunión','Otro') NOT NULL DEFAULT 'Clase',
    event_date DATE NOT NULL,
    event_time TIME NULL,
    details TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_eventos_owner_date (owner_key, event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS portal_mensajes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sender_key VARCHAR(255) NOT NULL,
    sender_name VARCHAR(180) NOT NULL,
    recipient_key VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(180) NOT NULL,
    subject VARCHAR(160) NOT NULL,
    body TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    sender_deleted TINYINT(1) NOT NULL DEFAULT 0,
    recipient_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_mensajes_recipient (recipient_key, is_read, created_at),
    INDEX idx_mensajes_sender (sender_key, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS portal_recursos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    owner_key VARCHAR(255) NOT NULL,
    owner_name VARCHAR(180) NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    category ENUM('Guías y documentos','Herramientas digitales','Propuestas didácticas','Presentaciones','Otros') NOT NULL DEFAULT 'Guías y documentos',
    resource_url VARCHAR(1000) NULL,
    file_path VARCHAR(500) NULL,
    original_filename VARCHAR(255) NULL,
    mime_type VARCHAR(150) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recursos_category (category),
    INDEX idx_recursos_owner (owner_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
