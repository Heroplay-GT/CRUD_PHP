<?php

declare(strict_types=1);

namespace QA\Application\Ports\Out;

use QA\Domain\Models\UserModel;

interface UpdateUserPort
{
    public function update(UserModel $user): UserModel;
}
