<?php

declare(strict_types=1);

namespace QA\Domain\Exceptions;

final class InvalidUserEmailException extends \InvalidArgumentException
{
    public static function becauseFormatIsInvalid(string $email): self
    {
        return new self('El formato del email es inválido: ' . $email);
    }

    public static function becauseValueIsEmpty(): self
    {
        return new self('El email del usuario no puede estar vacío.');
    }
}
