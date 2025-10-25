<?php

namespace core;

use Exception;

final class Views
{
    private static ?TemplateEngine $engine = null;

    private static function getEngine(): ?TemplateEngine
    {
        if (self::$engine === null) {
            self::$engine = new TemplateEngine();
        }
        return self::$engine;
    }

    /** @noinspection PhpUnused */
    public static function exists(string $viewPath): bool
    {
        $view = BASE_PATH . 'resources/views/' . $viewPath .'.php';
        if(file_exists($view)) {
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public static function render(string $view, array $data = []): string
    {
        return self::getEngine()->render($view, $data);
    }

    /**
     * @throws Exception
     */
    public static function make(string $view, array $data = []): string
    {
        return self::render($view, $data);
    }

    public static function extends($layout): void
    {
        self::getEngine()->extends($layout);
    }

    public static function section($name): void
    {
        self::getEngine()->section($name);
    }

    public static function endsection(): void
    {
        self::getEngine()->endsection();
    }

    public static function yield($name, $default = '')
    {
        return self::getEngine()->yield($name, $default);
    }

    /** @noinspection PhpUnused */

    public static function push($stack, $content = null): void
    {
        self::getEngine()->push($stack, $content);
    }

    public static function endpush(): void
    {
        self::getEngine()->endpush();
    }

    public static function stack($name): string
    {
        return self::getEngine()->stack($name);
    }

    /**
     * @throws Exception
     */
    public static function include($view, $data = []): void
    {
        self::getEngine()->include($view, $data);
    }
}