<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Controllers\Dto;

/**
 * Edición: la contraseña es opcional (vacía = conservar hash actual en el caso de uso).
 */
final readonly class UpdateUserRequest
{
    public function __construct(
        public string $id,
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
            id: trim((string) ($post['id'] ?? '')),
            name: trim((string) ($post['name'] ?? '')),
            email: trim((string) ($post['email'] ?? '')),
            password: (string) ($post['password'] ?? ''),
            roleId: trim((string) ($post['role_id'] ?? $post['roleId'] ?? '')),
            status: trim((string) ($post['status'] ?? 'ACTIVE')) ?: 'ACTIVE',
        );
    }

    public function wantsPasswordChange(): bool
    {
        return $this->password !== '';
    }
}
