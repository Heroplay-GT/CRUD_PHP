<?php

declare(strict_types=1);

namespace QA\Application\Services;

use QA\Application\Ports\In\GetAllUsersUseCase;
use QA\Application\Ports\Out\GetAllUsersPort;
use QA\Application\Services\Dto\Queries\GetAllUsersQuery;
use QA\Domain\Models\UserModel;

final class GetAllUsersService implements GetAllUsersUseCase
{
    public function __construct(
        private GetAllUsersPort $getAllUsersPort,
    ) {
    }

    /**
     * @return list<UserModel>
     */
    public function execute(GetAllUsersQuery $query): array
    {
        return $this->getAllUsersPort->getAll();
    }
}
