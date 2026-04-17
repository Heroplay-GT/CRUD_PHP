<?php

declare(strict_types=1);

namespace QA\Domain\Enums;

use QA\Domain\Exceptions\InvalidUserRoleException;

final class UserRoleEnum
{
    public const ADMIN = 'ADMIN';

    public const MEMBER = 'MEMBER';

    public const REVIEWER = 'REVIEWER';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [self::ADMIN, self::MEMBER, self::REVIEWER];
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    public static function ensureIsValid(string $value): void
    {
        if (!self::isValid($value)) {
            throw InvalidUserRoleException::becauseValueIsInvalid($value);
        }
    }
}
