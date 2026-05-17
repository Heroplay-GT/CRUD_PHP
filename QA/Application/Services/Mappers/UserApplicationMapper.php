<?php

declare(strict_types=1);

namespace QA\Application\Services\Mappers;

use QA\Application\Services\Dto\Commands\CreateUserCommand;
use QA\Application\Services\Dto\Commands\DeleteUserCommand;
use QA\Application\Services\Dto\Commands\UpdateUserCommand;
use QA\Application\Services\Dto\Queries\GetUserByIdQuery;
use QA\Domain\Models\UserModel;
use QA\Domain\ValueObjects\UserEmail;
use QA\Domain\ValueObjects\UserId;
use QA\Domain\ValueObjects\UserName;
use QA\Domain\ValueObjects\UserPassword;

final class UserApplicationMapper
{
    public static function fromCreateCommandToModel(CreateUserCommand $command): UserModel
    {
        return UserModel::create(
            new UserId($command->getId()),
            new UserName($command->getName()),
            new UserEmail($command->getEmail()),
            UserPassword::fromPlainText($command->getPassword()),
            $command->getRole(),
        );
    }

    /**
     * Útil cuando el comando trae contraseña en texto plano (no vacía).
     * Para edición con contraseña opcional, {@see UpdateUserService} construye el modelo manualmente.
     */
    public static function fromUpdateCommandToModel(UpdateUserCommand $command): UserModel
    {
        return new UserModel(
            new UserId($command->getId()),
            new UserName($command->getName()),
            new UserEmail($command->getEmail()),
            UserPassword::fromPlainText($command->getPassword()),
            $command->getRole(),
            $command->getStatus(),
        );
    }

    public static function fromGetUserByIdQueryToUserId(GetUserByIdQuery $query): UserId
    {
        return new UserId($query->getId());
    }

    public static function fromDeleteCommandToUserId(DeleteUserCommand $command): UserId
    {
        return new UserId($command->getId());
    }

    /**
     * @return array<string, string>
     */
    public static function fromModelToArray(UserModel $user): array
    {
        return [
            'id' => $user->id()->value(),
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'password' => $user->password()->value(),
            'role' => $user->role(),
            'status' => $user->status(),
        ];
    }

    /**
     * @param list<UserModel> $users
     *
     * @return list<array<string, string>>
     */
    public static function fromModelsToArray(array $users): array
    {
        $result = [];
        foreach ($users as $user) {
            $result[] = self::fromModelToArray($user);
        }

        return $result;
    }
}
