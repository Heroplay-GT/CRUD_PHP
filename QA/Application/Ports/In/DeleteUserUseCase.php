<?php

declare(strict_types=1);

namespace QA\Application\Ports\In;

use QA\Application\Services\Dto\Commands\DeleteUserCommand;

interface DeleteUserUseCase
{
    public function execute(DeleteUserCommand $command): void;
}
