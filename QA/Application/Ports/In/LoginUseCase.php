<?php

declare(strict_types=1);

namespace QA\Application\Ports\In;

use QA\Application\Services\Dto\Commands\LoginCommand;
use QA\Domain\Models\UserModel;

interface LoginUseCase
{
    public function execute(LoginCommand $command): UserModel;
}
