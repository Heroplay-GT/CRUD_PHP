<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Controllers\Dto;

final readonly class LoginWebRequest
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     */
    public static function fromPost(array $post): self
    {
        return new self(
            email: trim((string) ($post['email'] ?? '')),
            password: (string) ($post['password'] ?? ''),
        );
    }
}
