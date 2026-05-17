<?php

declare(strict_types=1);

namespace QA\Application\Ports\Out;

use QA\Domain\Models\UserModel;

interface SaveUserPort
{
    public function save(UserModel $user): UserModel;
}
