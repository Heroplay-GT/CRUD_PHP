<?php

declare(strict_types=1);

namespace QA\Domain\ValueObjects;

use QA\Domain\Exceptions\InvalidUserEmailException;

final class UserEmail
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);
        if ($normalized === '') {
            throw InvalidUserEmailException::becauseValueIsEmpty();
        }
        if (filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
            throw InvalidUserEmailException::becauseFormatIsInvalid($normalized);
        }
        $this->value = strtolower($normalized);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
