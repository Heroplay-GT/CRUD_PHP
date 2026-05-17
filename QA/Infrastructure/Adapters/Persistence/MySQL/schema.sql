-- Base y tabla según doc 04 (MySQL)
CREATE DATABASE IF NOT EXISTS crud_usuarios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crud_usuarios;

CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL,
    status VARCHAR(30) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_users_email (email)
);

-- Demo: email demo@demo.com, contraseña: demodemo12 (≥8 caracteres, dominio UserPassword)
INSERT INTO users (id, name, email, password, role, status, created_at, updated_at)
VALUES (
    '00000000-0000-4000-8000-000000000001',
    'Usuario demo',
    'demo@demo.com',
    '$2y$10$aZ/VEeSmKN0gElVfLpBgmeFIhxOfZqwixsZMGherrHmc58QZDMVdW',
    'ADMIN',
    'ACTIVE',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE updated_at = NOW();
