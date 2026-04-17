<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Controllers\Mapper;

use QA\Application\Services\Dto\Commands\CreateUserCommand;
use QA\Application\Services\Dto\Commands\UpdateUserCommand;
use QA\Domain\Enums\UserRoleEnum;
use QA\Domain\Models\UserModel;
use QA\Infrastructure\Entrypoints\Web\Controllers\Dto\CreateUserRequest;
use QA\Infrastructure\Entrypoints\Web\Controllers\Dto\LoginWebRequest;
use QA\Infrastructure\Entrypoints\Web\Controllers\Dto\UpdateUserRequest;
use QA\Infrastructure\Entrypoints\Web\Controllers\Dto\UserResponse;

/**
 * Puente Web ↔ aplicación (Commands / UserModel → respuesta de vista).
 */
final class UserWebMapper
{
    public function toCreateUserCommand(string $newId, CreateUserRequest $request): CreateUserCommand
    {
        return new CreateUserCommand(
            $newId,
            $request->name,
            $request->email,
            $request->password,
            $this->normalizeRole($request->roleId),
        );
    }

    public function toUpdateUserCommand(UpdateUserRequest $request): UpdateUserCommand
    {
        $password = $request->wantsPasswordChange() ? $request->password : '';

        return new UpdateUserCommand(
            $request->id,
            $request->name,
            $request->email,
            $password,
            $this->normalizeRole($request->roleId),
            $request->status,
        );
    }

    /**
     * @return array{email: string, password: string}
     */
    public function toLoginCommandPayload(LoginWebRequest $request): array
    {
        return [
            'email' => $request->email,
            'password' => $request->password,
        ];
    }

    public function userModelToResponse(UserModel $user): UserResponse
    {
        $row = [
            'id' => $user->id()->value(),
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'role' => $this->roleToLabel($user->role()),
            'status' => $user->status(),
        ];

        return UserResponse::fromStorageRow($row);
    }

    /**
     * @param array{id: string, name: string, email: string, role: string, status: string} $row
     */
    public function userRowToResponse(array $row): UserResponse
    {
        $display = $row;
        $display['role'] = $this->roleToLabel($row['role']);

        return UserResponse::fromStorageRow($display);
    }

    private function normalizeRole(string $value): string
    {
        $v = strtoupper(trim($value));
        if (UserRoleEnum::isValid($v)) {
            return $v;
        }

        return match ($v) {
            '1' => UserRoleEnum::ADMIN,
            '2' => UserRoleEnum::MEMBER,
            '3' => UserRoleEnum::REVIEWER,
            default => UserRoleEnum::ADMIN,
        };
    }

    private function roleToLabel(string $role): string
    {
        return match ($role) {
            UserRoleEnum::ADMIN => 'Administrador',
            UserRoleEnum::MEMBER => 'Miembro',
            UserRoleEnum::REVIEWER => 'Revisor',
            default => $role,
        };
    }
}
