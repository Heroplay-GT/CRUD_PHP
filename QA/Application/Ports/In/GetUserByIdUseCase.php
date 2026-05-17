<?php

declare(strict_types=1);

namespace QA\Application\Ports\In;

use QA\Application\Services\Dto\Queries\GetUserByIdQuery;
use QA\Domain\Models\UserModel;

interface GetUserByIdUseCase
{
    public function execute(GetUserByIdQuery $query): UserModel;
}
