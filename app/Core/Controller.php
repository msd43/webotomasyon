<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected string $viewsPath;

    public function __construct(?string $viewsPath = null)
    {
        $this->viewsPath = $viewsPath ?? dirname(__DIR__) . '/Views';
    }

    protected function view(string $view, array $data = []): void
    {
        $viewFile = rtrim($this->viewsPath, '/\\') . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            throw new \RuntimeException(sprintf('View file not found: %s', $viewFile));
        }

        extract($data, EXTR_SKIP);
        require $viewFile;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url, true, 302);
        exit;
    }
}
