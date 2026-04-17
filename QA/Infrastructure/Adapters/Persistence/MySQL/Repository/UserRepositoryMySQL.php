<?php

declare(strict_types=1);

namespace QA\Infrastructure\Adapters\Persistence\MySQL\Repository;

use PDO;
use QA\Application\Ports\Out\DeleteUserPort;
use QA\Application\Ports\Out\GetAllUsersPort;
use QA\Application\Ports\Out\GetUserByEmailPort;
use QA\Application\Ports\Out\GetUserByIdPort;
use QA\Application\Ports\Out\SaveUserPort;
use QA\Application\Ports\Out\UpdateUserPort;
use QA\Domain\Models\UserModel;
use QA\Domain\ValueObjects\UserEmail;
use QA\Domain\ValueObjects\UserId;
use QA\Infrastructure\Adapters\Persistence\MySQL\Mapper\UserPersistenceMapper;
use RuntimeException;

final class UserRepositoryMySQL implements
    SaveUserPort,
    UpdateUserPort,
    GetUserByIdPort,
    GetUserByEmailPort,
    GetAllUsersPort,
    DeleteUserPort
{
    public function __construct(
        private PDO $pdo,
        private UserPersistenceMapper $mapper,
    ) {
    }

    public function save(UserModel $user): UserModel
    {
        $dto = $this->mapper->fromModelToDto($user);
        $sql = <<<'SQL'
INSERT INTO users (
    id,
    name,
    email,
    password,
    role,
    status,
    created_at,
    updated_at
) VALUES (
    :id,
    :name,
    :email,
    :password,
    :role,
    :status,
    NOW(),
    NOW()
)
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':id' => $dto->id(),
            ':name' => $dto->name(),
            ':email' => $dto->email(),
            ':password' => $dto->password(),
            ':role' => $dto->role(),
            ':status' => $dto->status(),
        ]);

        $savedUser = $this->getById(new UserId($dto->id()));
        if ($savedUser === null) {
            throw new RuntimeException('The user could not be recovered after save.');
        }

        return $savedUser;
    }

    public function update(UserModel $user): UserModel
    {
        $dto = $this->mapper->fromModelToDto($user);
        $sql = <<<'SQL'
UPDATE users
SET name = :name,
    email = :email,
    password = :password,
    role = :role,
    status = :status,
    updated_at = NOW()
WHERE id = :id
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':id' => $dto->id(),
            ':name' => $dto->name(),
            ':email' => $dto->email(),
            ':password' => $dto->password(),
            ':role' => $dto->role(),
            ':status' => $dto->status(),
        ]);

        $updatedUser = $this->getById(new UserId($dto->id()));
        if ($updatedUser === null) {
            throw new RuntimeException('The user could not be recovered after update.');
        }

        return $updatedUser;
    }

    public function getById(UserId $userId): ?UserModel
    {
        $sql = <<<'SQL'
SELECT
    id,
    name,
    email,
    password,
    role,
    status,
    created_at,
    updated_at
FROM users
WHERE id = :id
LIMIT 1
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->execute([':id' => $userId->value()]);
        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        /** @var array<string, mixed> $row */
        return $this->mapper->fromRowToModel($row);
    }

    public function getByEmail(UserEmail $email): ?UserModel
    {
        $sql = <<<'SQL'
SELECT
    id,
    name,
    email,
    password,
    role,
    status,
    created_at,
    updated_at
FROM users
WHERE email = :email
LIMIT 1
SQL;
        $statement = $this->pdo->prepare($sql);
        $statement->execute([':email' => $email->value()]);
        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        /** @var array<string, mixed> $row */
        return $this->mapper->fromRowToModel($row);
    }

    /**
     * @return list<UserModel>
     */
    public function getAll(): array
    {
        $sql = <<<'SQL'
SELECT
    id,
    name,
    email,
    password,
    role,
    status,
    created_at,
    updated_at
FROM users
ORDER BY name ASC
SQL;
        $statement = $this->pdo->query($sql);
        $rows = $statement->fetchAll();

        /** @var list<array<string, mixed>> $rows */
        return $this->mapper->fromRowsToModels($rows);
    }

    public function delete(UserId $userId): void
    {
        $sql = 'DELETE FROM users WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute([':id' => $userId->value()]);
    }
}
