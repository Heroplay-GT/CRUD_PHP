<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Presentation;

/**
 * Motor de plantillas mínimo: extract() + require (según la guía).
 */
final class View
{
    public function __construct(
        private readonly string $viewsDirectory,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        $path = $this->viewsDirectory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $template) . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException('Vista no encontrada: ' . $template);
        }

        ob_start();
        require $path;

        return (string) ob_get_clean();
    }

    /**
     * Redirección relativa al index.php del entrypoint (patrón PRG en POST).
     *
     * @param array<string, string|int> $queryParams ej. ['id' => 3] → index.php?route=users.show&id=3
     */
    public function redirectToRoute(string $route, array $queryParams = []): never
    {
        $query = array_merge(['route' => $route], $queryParams);
        header('Location: index.php?' . http_build_query($query));
        exit;
    }

    public function redirect(string $route): never
    {
        $this->redirectToRoute($route);
    }
}
