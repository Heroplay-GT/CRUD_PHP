<?php

declare(strict_types=1);

namespace QA\Application\Services;

use QA\Application\Ports\In\CreateUserUseCase;
use QA\Application\Ports\Out\GetUserByEmailPort;
use QA\Application\Ports\Out\SaveUserPort;
use QA\Application\Services\Dto\Commands\CreateUserCommand;
use QA\Application\Services\Mappers\UserApplicationMapper;
use QA\Domain\Exceptions\UserAlreadyExistsException;
use QA\Domain\Models\UserModel;
use QA\Domain\ValueObjects\UserEmail;

final class CreateUserService implements CreateUserUseCase
{
    public function __construct(
        private SaveUserPort $saveUserPort,
        private GetUserByEmailPort $getUserByEmailPort,
    ) {
    }

    public function execute(CreateUserCommand $command): UserModel
    {
        $email = new UserEmail($command->getEmail());
        $existingUser = $this->getUserByEmailPort->getByEmail($email);
        if ($existingUser !== null) {
            throw UserAlreadyExistsException::becauseEmailAlreadyExists($email->value());
        }

        $user = UserApplicationMapper::fromCreateCommandToModel($command);
        // Alta desde panel administrativo: queda operativo sin flujo de verificación de email.
        $user = $user->activate();

        return $this->saveUserPort->save($user);
    }
}
