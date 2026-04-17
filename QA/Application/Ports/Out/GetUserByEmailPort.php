<?php

declare(strict_types=1);

namespace QA\Application\Ports\Out;

use QA\Domain\Models\UserModel;
use QA\Domain\ValueObjects\UserEmail;

interface GetUserByEmailPort
{
    public function getByEmail(UserEmail $email): ?UserModel;
}
