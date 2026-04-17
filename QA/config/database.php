<?php

declare(strict_types=1);

/**
 * Ajusta host, base, usuario y clave de MySQL (Laragon por defecto).
 * Puedes sobreescribir con variables de entorno QA_DB_*.
 *
 * @return array{host: string, port: int, database: string, username: string, password: string, charset: string}
 */
return [
    'host' => getenv('QA_DB_HOST') !== false && getenv('QA_DB_HOST') !== '' ? getenv('QA_DB_HOST') : '127.0.0.1',
    'port' => (int) (getenv('QA_DB_PORT') !== false && getenv('QA_DB_PORT') !== '' ? getenv('QA_DB_PORT') : '3306'),
    'database' => getenv('QA_DB_NAME') !== false && getenv('QA_DB_NAME') !== '' ? getenv('QA_DB_NAME') : 'crud_usuarios',
    'username' => getenv('QA_DB_USER') !== false && getenv('QA_DB_USER') !== '' ? getenv('QA_DB_USER') : 'root',
    'password' => getenv('QA_DB_PASS') !== false ? getenv('QA_DB_PASS') : '',
    'charset' => 'utf8mb4',
];
