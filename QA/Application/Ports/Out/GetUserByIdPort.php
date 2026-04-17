<?php

declare(strict_types=1);

namespace QA\Application\Ports\Out;

use QA\Domain\Models\UserModel;
use QA\Domain\ValueObjects\UserId;

interface GetUserByIdPort
{
    public function getById(UserId $userId): ?UserModel;
}
