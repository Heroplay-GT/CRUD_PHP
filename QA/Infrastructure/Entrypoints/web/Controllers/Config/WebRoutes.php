<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Controllers\Config;

/**
 * Registra rutas: nombre → [método HTTP, acción Controlador@método].
 * El front controller lee ?route= y despacha aquí.
 */
final class WebRoutes
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function table(): array
    {
        return [
            'home' => ['GET', 'UserController@home'],

            'auth.login' => ['GET', 'UserController@loginForm'],
            'auth.authenticate' => ['POST', 'UserController@authenticate'],
            'auth.logout' => ['GET', 'UserController@logout'],
            'auth.forgot' => ['GET', 'UserController@forgotForm'],
            'auth.forgot.send' => ['POST', 'UserController@sendForgot'],
            'auth.reset' => ['GET', 'UserController@resetForm'],
            'auth.reset.update' => ['POST', 'UserController@resetUpdate'],

            'users.index' => ['GET', 'UserController@index'],
            'users.create' => ['GET', 'UserController@createForm'],
            'users.store' => ['POST', 'UserController@store'],
            'users.show' => ['GET', 'UserController@show'],
            'users.edit' => ['GET', 'UserController@editForm'],
            'users.update' => ['POST', 'UserController@update'],
            'users.delete' => ['POST', 'UserController@destroy'],
        ];
    }

    /**
     * @return array{route: string, action: string}|null
     */
    public static function match(?string $route, string $httpMethod): ?array
    {
        if ($route === null || $route === '') {
            $route = 'home';
        }

        $table = self::table();
        if (!isset($table[$route])) {
            return null;
        }

        [$expectedMethod, $action] = $table[$route];
        if (strtoupper($httpMethod) !== strtoupper($expectedMethod)) {
            return null;
        }

        return ['route' => $route, 'action' => $action];
    }

    /**
     * Rutas que no exigen sesión autenticada (alineado a la guía).
     *
     * @return list<string>
     */
    public static function publicRouteNames(): array
    {
        return [
            'home',
            'auth.login',
            'auth.authenticate',
            'auth.logout',
            'auth.forgot',
            'auth.forgot.send',
            'auth.reset',
            'auth.reset.update',
        ];
    }
}
