<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Presentation;

/**
 * Mensajes flash en sesión (un solo uso por clave tras pull).
 */
final class Flash
{
    private const KEY = '_qa_flash';

    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /** @param array<string, string> $messages */
    public static function setMany(array $messages): void
    {
        $_SESSION[self::KEY] = $messages;
    }

    public static function set(string $key, string $value): void
    {
        $_SESSION[self::KEY] ??= [];
        $_SESSION[self::KEY][$key] = $value;
    }

    public static function get(string $key, string $default = ''): string
    {
        $value = $_SESSION[self::KEY][$key] ?? $default;
        unset($_SESSION[self::KEY][$key]);

        return (string) $value;
    }

    /** @param array<string, string> $form */
    public static function setOld(array $form): void
    {
        $_SESSION[self::KEY]['_old'] = $form;
    }

    /** @return array<string, string> */
    public static function old(): array
    {
        $old = $_SESSION[self::KEY]['_old'] ?? [];
        unset($_SESSION[self::KEY]['_old']);

        return is_array($old) ? $old : [];
    }

    /** @param array<string, string> $errors */
    public static function setErrors(array $errors): void
    {
        $_SESSION[self::KEY]['_errors'] = $errors;
    }

    /** @return array<string, string> */
    public static function errors(): array
    {
        $errors = $_SESSION[self::KEY]['_errors'] ?? [];
        unset($_SESSION[self::KEY]['_errors']);

        return is_array($errors) ? $errors : [];
    }

    public static function setMessage(string $message): void
    {
        self::set('message', $message);
    }

    public static function message(): string
    {
        return self::get('message', '');
    }

    public static function setSuccess(string $message): void
    {
        self::set('success', $message);
    }

    public static function success(): string
    {
        return self::get('success', '');
    }

    /** @return array<string, string> */
    public static function pullAll(): array
    {
        $all = $_SESSION[self::KEY] ?? [];
        unset($_SESSION[self::KEY]);

        return is_array($all) ? $all : [];
    }
}
