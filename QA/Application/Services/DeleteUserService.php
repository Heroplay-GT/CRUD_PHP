<?php

declare(strict_types=1);

namespace QA\Application\Services;

use QA\Application\Ports\In\DeleteUserUseCase;
use QA\Application\Ports\Out\DeleteUserPort;
use QA\Application\Ports\Out\GetUserByIdPort;
use QA\Application\Services\Dto\Commands\DeleteUserCommand;
use QA\Application\Services\Mappers\UserApplicationMapper;
use QA\Domain\Exceptions\UserNotFoundException;

final class DeleteUserService implements DeleteUserUseCase
{
    public function __construct(
        private DeleteUserPort $deleteUserPort,
        private GetUserByIdPort $getUserByIdPort,
    ) {
    }

    public function execute(DeleteUserCommand $command): void
    {
        $userId = UserApplicationMapper::fromDeleteCommandToUserId($command);
        $existingUser = $this->getUserByIdPort->getById($userId);
        if ($existingUser === null) {
            throw UserNotFoundException::becauseIdWasNotFound($userId->value());
        }

        $this->deleteUserPort->delete($userId);
    }
}
