<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Controllers\Dto;

/**
 * Datos del formulario web de alta de usuario.
 */
final readonly class CreateUserRequest
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $roleId,
        public string $status,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     */
    public static function fromPost(array $post): self
    {
        return new self(
            name: trim((string) ($post['name'] ?? '')),
            email: trim((string) ($post['email'] ?? '')),
            password: (string) ($post['password'] ?? ''),
            roleId: trim((string) ($post['role_id'] ?? $post['roleId'] ?? '')),
            status: trim((string) ($post['status'] ?? 'ACTIVE')) ?: 'ACTIVE',
        );
    }
}
