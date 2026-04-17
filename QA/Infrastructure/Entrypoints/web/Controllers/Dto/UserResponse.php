<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Controllers\Dto;

/**
 * DTO de salida hacia la capa de presentación (sin lógica de negocio).
 */
final readonly class UserResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $roleLabel,
        public string $status,
    ) {
    }

    /**
     * @param array{id: string, name: string, email: string, role: string, status: string} $row
     */
    public static function fromStorageRow(array $row): self
    {
        return new self(
            id: (string) $row['id'],
            name: (string) $row['name'],
            email: (string) $row['email'],
            roleLabel: (string) $row['role'],
            status: (string) $row['status'],
        );
    }

    /** @return array{id: string, name: string, email: string, role: string, status: string} */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->roleLabel,
            'status' => $this->status,
        ];
    }
}
