<?php

declare(strict_types=1);

namespace QA\Application\Ports\Out;

use QA\Domain\Models\UserModel;

interface GetAllUsersPort
{
    /**
     * @return list<UserModel>
     */
    public function getAll(): array;
}
