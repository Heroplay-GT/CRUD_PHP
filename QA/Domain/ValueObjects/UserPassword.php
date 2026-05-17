<?php

declare(strict_types=1);

namespace QA\Domain\ValueObjects;

use QA\Domain\Exceptions\InvalidUserPasswordException;

/**
 * Almacena siempre el hash (bcrypt). fromPlainText hashea; fromHash carga desde BD; verifyPlain usa password_verify.
 */
final class UserPassword
{
    private const MIN_PLAIN_LENGTH = 8;

    private const MIN_STORED_LENGTH = 8;

    private string $value;

    /**
     * Uso interno: valor almacenado (hash) que cumple longitud mínima.
     */
    public function __construct(string $value)
    {
        $normalized = trim($value);
        if ($normalized === '') {
            throw InvalidUserPasswordException::becauseValueIsEmpty();
        }
        if (strlen($normalized) < self::MIN_STORED_LENGTH) {
            throw InvalidUserPasswordException::becauseLengthIsTooShort(self::MIN_STORED_LENGTH);
        }
        $this->value = $normalized;
    }

    public static function fromPlainText(string $raw): self
    {
        $plain = trim($raw);
        if ($plain === '') {
            throw InvalidUserPasswordException::becauseValueIsEmpty();
        }
        if (strlen($plain) < self::MIN_PLAIN_LENGTH) {
            throw InvalidUserPasswordException::becauseLengthIsTooShort(self::MIN_PLAIN_LENGTH);
        }

        $hash = password_hash($plain, PASSWORD_BCRYPT);
        if ($hash === false) {
            throw new \RuntimeException('No se pudo generar el hash de contraseña.');
        }

        return new self($hash);
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function verifyPlain(string $plain): bool
    {
        return password_verify($plain, $this->value);
    }

    public function equals(self $other): bool
    {
        return hash_equals($this->value, $other->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
