<?php

declare(strict_types=1);

namespace QA\Application\Ports\In;

use QA\Application\Services\Dto\Commands\CreateUserCommand;
use QA\Domain\Models\UserModel;

interface CreateUserUseCase
{
    public function execute(CreateUserCommand $command): UserModel;
}
