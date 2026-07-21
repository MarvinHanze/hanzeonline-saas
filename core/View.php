<?php
declare(strict_types=1);

namespace Core;

class View
{
    private static string $basePath = '';

    public static function init(string $basePath): void
    {
        self::$basePath = $basePath;
    }

    public static function render(string $view, array $data = []): void
    {
        extract($data);
        $path = self::$basePath . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($path)) {
            throw new \RuntimeException("View not found: $view");
        }
        require $path;
    }

    public static function partial(string $partial, array $data = []): void
    {
        self::render('_partials/' . $partial, $data);
    }

    public static function layout(string $layout, array $data = []): callable
    {
        return function () use ($layout, $data, $view) {
            $content = ob_get_clean();
            self::render($layout, array_merge($data, ['content' => $content]));
        };
    }
}
