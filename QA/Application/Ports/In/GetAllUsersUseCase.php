<?php

declare(strict_types=1);

namespace QA\Application\Ports\In;

use QA\Application\Services\Dto\Queries\GetAllUsersQuery;
use QA\Domain\Models\UserModel;

interface GetAllUsersUseCase
{
    /**
     * @return list<UserModel>
     */
    public function execute(GetAllUsersQuery $query): array;
}
