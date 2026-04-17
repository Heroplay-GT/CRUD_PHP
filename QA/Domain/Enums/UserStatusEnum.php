<?php

declare(strict_types=1);

namespace QA\Domain\Enums;

use QA\Domain\Exceptions\InvalidUserStatusException;

final class UserStatusEnum
{
    public const ACTIVE = 'ACTIVE';

    public const INACTIVE = 'INACTIVE';

    public const PENDING = 'PENDING';

    public const BLOCKED = 'BLOCKED';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [self::ACTIVE, self::INACTIVE, self::PENDING, self::BLOCKED];
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    public static function ensureIsValid(string $value): void
    {
        if (!self::isValid($value)) {
            throw InvalidUserStatusException::becauseValueIsInvalid($value);
        }
    }
}
