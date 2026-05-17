<?php

declare(strict_types=1);

namespace QA\Domain\ValueObjects;

use QA\Domain\Exceptions\InvalidUserNameException;

final class UserName
{
    private const MIN_LENGTH = 3;

    private string $value;

    public function __construct(string $value)
    {
        $normalized = trim($value);
        if ($normalized === '') {
            throw InvalidUserNameException::becauseValueIsEmpty();
        }
        if (mb_strlen($normalized) < self::MIN_LENGTH) {
            throw InvalidUserNameException::becauseLengthIsTooShort(self::MIN_LENGTH);
        }
        $this->value = $normalized;
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
