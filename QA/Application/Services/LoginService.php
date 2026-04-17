<?php

declare(strict_types=1);

namespace QA\Application\Services;

use QA\Application\Ports\In\LoginUseCase;
use QA\Application\Ports\Out\GetUserByEmailPort;
use QA\Application\Services\Dto\Commands\LoginCommand;
use QA\Domain\Enums\UserStatusEnum;
use QA\Domain\Exceptions\InvalidCredentialsException;
use QA\Domain\Models\UserModel;
use QA\Domain\ValueObjects\UserEmail;

final class LoginService implements LoginUseCase
{
    public function __construct(
        private GetUserByEmailPort $getUserByEmailPort,
    ) {
    }

    public function execute(LoginCommand $command): UserModel
    {
        $email = new UserEmail($command->getEmail());
        $user = $this->getUserByEmailPort->getByEmail($email);
        if ($user === null || !$user->password()->verifyPlain($command->getPassword())) {
            throw InvalidCredentialsException::becauseCredentialsAreInvalid();
        }

        if ($user->status() !== UserStatusEnum::ACTIVE) {
            throw InvalidCredentialsException::becauseUserIsNotActive();
        }

        return $user;
    }
}
