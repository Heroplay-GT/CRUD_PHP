<?php

declare(strict_types=1);

namespace QA\Application\Services;

use QA\Application\Ports\In\UpdateUserUseCase;
use QA\Application\Ports\Out\GetUserByEmailPort;
use QA\Application\Ports\Out\GetUserByIdPort;
use QA\Application\Ports\Out\UpdateUserPort;
use QA\Application\Services\Dto\Commands\UpdateUserCommand;
use QA\Domain\Exceptions\UserAlreadyExistsException;
use QA\Domain\Exceptions\UserNotFoundException;
use QA\Domain\Models\UserModel;
use QA\Domain\ValueObjects\UserEmail;
use QA\Domain\ValueObjects\UserId;
use QA\Domain\ValueObjects\UserName;
use QA\Domain\ValueObjects\UserPassword;

final class UpdateUserService implements UpdateUserUseCase
{
    public function __construct(
        private UpdateUserPort $updateUserPort,
        private GetUserByIdPort $getUserByIdPort,
        private GetUserByEmailPort $getUserByEmailPort,
    ) {
    }

    public function execute(UpdateUserCommand $command): UserModel
    {
        $userId = new UserId($command->getId());
        $currentUser = $this->getUserByIdPort->getById($userId);
        if ($currentUser === null) {
            throw UserNotFoundException::becauseIdWasNotFound($userId->value());
        }

        $newEmail = new UserEmail($command->getEmail());
        $userWithSameEmail = $this->getUserByEmailPort->getByEmail($newEmail);
        if ($userWithSameEmail !== null && !$userWithSameEmail->id()->equals($userId)) {
            throw UserAlreadyExistsException::becauseEmailAlreadyExists($newEmail->value());
        }

        $password = $command->getPassword() !== ''
            ? UserPassword::fromPlainText($command->getPassword())
            : $currentUser->password();

        $userToUpdate = new UserModel(
            $userId,
            new UserName($command->getName()),
            new UserEmail($command->getEmail()),
            $password,
            $command->getRole(),
            $command->getStatus(),
        );

        return $this->updateUserPort->update($userToUpdate);
    }
}
