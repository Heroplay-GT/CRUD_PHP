<?php

declare(strict_types=1);

namespace QA\Domain\Events;

use QA\Domain\ValueObjects\UserId;

final class UserDeletedDomainEvent extends DomainEvent
{
    private UserId $userId;

    public function __construct(UserId $userId)
    {
        parent::__construct('user.deleted');
        $this->userId = $userId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function payload(): array
    {
        return [
            'id' => $this->userId->value(),
        ];
    }
}
