<?php

declare(strict_types=1);

namespace QA\Application\Ports\In;

use QA\Application\Services\Dto\Commands\UpdateUserCommand;
use QA\Domain\Models\UserModel;

interface UpdateUserUseCase
{
    public function execute(UpdateUserCommand $command): UserModel;
}
