<?php

declare(strict_types=1);

namespace QA\Common;

/**
 * Mapea clases QA\* → rutas de archivo y registra spl_autoload_register.
 * Equivale al ClassLoader del material del curso; aquí se usa namespace QA\ con raíz en QA/.
 */
final class ClassLoader
{
    public static function register(string $projectRoot): void
    {
        $root = rtrim($projectRoot, '/\\');

        spl_autoload_register(static function (string $class) use ($root): void {
            $prefix = 'QA\\';
            if (!str_starts_with($class, $prefix)) {
                return;
            }

            $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
            $webNs = 'Infrastructure/Entrypoints/Web/';
            if (str_starts_with($relative, $webNs)) {
                $relative = 'Infrastructure/Entrypoints/web/' . substr($relative, strlen($webNs));
            }

            $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative) . '.php';
            if (is_file($path)) {
                require $path;
            }
        });
    }
}
