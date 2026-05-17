<?php

declare(strict_types=1);

namespace QA\Application\Ports\Out;

use QA\Domain\ValueObjects\UserId;

interface DeleteUserPort
{
    public function delete(UserId $userId): void;
}
