<?php

declare(strict_types=1);

namespace QA\Application\Services;

use QA\Application\Ports\In\GetUserByIdUseCase;
use QA\Application\Ports\Out\GetUserByIdPort;
use QA\Application\Services\Dto\Queries\GetUserByIdQuery;
use QA\Application\Services\Mappers\UserApplicationMapper;
use QA\Domain\Exceptions\UserNotFoundException;
use QA\Domain\Models\UserModel;

final class GetUserByIdService implements GetUserByIdUseCase
{
    public function __construct(
        private GetUserByIdPort $getUserByIdPort,
    ) {
    }

    public function execute(GetUserByIdQuery $query): UserModel
    {
        $userId = UserApplicationMapper::fromGetUserByIdQueryToUserId($query);
        $user = $this->getUserByIdPort->getById($userId);
        if ($user === null) {
            throw UserNotFoundException::becauseIdWasNotFound($userId->value());
        }

        return $user;
    }
}
