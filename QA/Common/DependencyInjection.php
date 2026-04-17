<?php

declare(strict_types=1);

namespace QA\Common;

use PDO;
use QA\Application\Services\CreateUserService;
use QA\Application\Services\DeleteUserService;
use QA\Application\Services\GetAllUsersService;
use QA\Application\Services\GetUserByIdService;
use QA\Application\Services\LoginService;
use QA\Application\Services\UpdateUserService;
use QA\Infrastructure\Adapters\Persistence\MySQL\Config\Connection;
use QA\Infrastructure\Adapters\Persistence\MySQL\Mapper\UserPersistenceMapper;
use QA\Infrastructure\Adapters\Persistence\MySQL\Repository\UserRepositoryMySQL;
use QA\Infrastructure\Entrypoints\Web\Controllers\Mapper\UserWebMapper;
use QA\Infrastructure\Entrypoints\Web\Controllers\UserController;
use QA\Infrastructure\Entrypoints\Web\Presentation\View;

/**
 * Fábrica central: MySQL + repositorio + casos de uso + controlador web.
 */
final class DependencyInjection
{
    private static ?View $view = null;

    private static ?PDO $pdo = null;

    private static ?UserPersistenceMapper $persistenceMapper = null;

    private static ?UserRepositoryMySQL $userRepository = null;

    public static function boot(string $projectRoot): void
    {
        ClassLoader::register($projectRoot);
    }

    public static function getView(): View
    {
        if (self::$view === null) {
            $views = dirname(__DIR__) . '/Infrastructure/Entrypoints/web/Presentation/Views';
            self::$view = new View($views);
        }

        return self::$view;
    }

    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            /** @var array{host: string, port: int, database: string, username: string, password: string, charset: string} $cfg */
            $cfg = require dirname(__DIR__) . '/config/database.php';
            $connection = new Connection(
                $cfg['host'],
                $cfg['port'],
                $cfg['database'],
                $cfg['username'],
                $cfg['password'],
                $cfg['charset'],
            );
            self::$pdo = $connection->createPdo();
        }

        return self::$pdo;
    }

    public static function getUserPersistenceMapper(): UserPersistenceMapper
    {
        if (self::$persistenceMapper === null) {
            self::$persistenceMapper = new UserPersistenceMapper();
        }

        return self::$persistenceMapper;
    }

    public static function getUserRepository(): UserRepositoryMySQL
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepositoryMySQL(
                self::getPdo(),
                self::getUserPersistenceMapper(),
            );
        }

        return self::$userRepository;
    }

    public static function getUserWebMapper(): UserWebMapper
    {
        return new UserWebMapper();
    }

    public static function getCreateUserUseCase(): CreateUserService
    {
        $repo = self::getUserRepository();

        return new CreateUserService($repo, $repo);
    }

    public static function getUpdateUserUseCase(): UpdateUserService
    {
        $repo = self::getUserRepository();

        return new UpdateUserService($repo, $repo, $repo);
    }

    public static function getDeleteUserUseCase(): DeleteUserService
    {
        $repo = self::getUserRepository();

        return new DeleteUserService($repo, $repo);
    }

    public static function getGetUserByIdUseCase(): GetUserByIdService
    {
        return new GetUserByIdService(self::getUserRepository());
    }

    public static function getGetAllUsersUseCase(): GetAllUsersService
    {
        return new GetAllUsersService(self::getUserRepository());
    }

    public static function getLoginUseCase(): LoginService
    {
        return new LoginService(self::getUserRepository());
    }

    public static function getUserController(): UserController
    {
        $repo = self::getUserRepository();

        return new UserController(
            self::getView(),
            self::getUserWebMapper(),
            self::getCreateUserUseCase(),
            self::getUpdateUserUseCase(),
            self::getDeleteUserUseCase(),
            self::getGetUserByIdUseCase(),
            self::getGetAllUsersUseCase(),
            self::getLoginUseCase(),
            $repo,
            $repo,
        );
    }
}
