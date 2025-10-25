<?php

namespace core;

use Exception;

final class TemplateEngine
{
    private array $sections = [];
    private ?string $currentSection = null;
    private ?string $layout = null;
    private array $pushes = [];

    /**
     * @throws Exception
     */
    public function render($view, $data = []): false|string
    {
        
        extract($data);

        
        $viewPath = BASE_PATH . 'resources/views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            throw new Exception("View not found: $view");
        }

        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        if ($this->layout) {
            $layoutPath = BASE_PATH . 'resources/views/layouts/' . $this->layout . '.php';
            if (!file_exists($layoutPath)) {
                throw new Exception("Layout not found: $this->layout");
            }

            ob_start();
            include $layoutPath;
            $content = ob_get_clean();
        }

        return $content;
    }

    public function extends($layout): void
    {
        $this->layout = $layout;
    }

    public function section($name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    public function endsection(): void
    {
        if ($this->currentSection) {
            $this->sections[$this->currentSection] = ob_get_clean();
            $this->currentSection = null;
        }
    }

    public function yield($name, $default = '')
    {
        return $this->sections[$name] ?? $default;
    }

    public function push($stack, $content = null): void
    {
        if (!isset($this->pushes[$stack])) {
            $this->pushes[$stack] = [];
        }

        if ($content === null) {
            $this->currentSection = "push_$stack";
            ob_start();
        } else {
            $this->pushes[$stack][] = $content;
        }
    }

    public function endpush(): void
    {
        if ($this->currentSection && str_starts_with($this->currentSection, 'push_')) {
            $stack = substr($this->currentSection, 5);
            $this->pushes[$stack][] = ob_get_clean();
            $this->currentSection = null;
        }
    }

    public function stack($name): string
    {
        if (isset($this->pushes[$name])) {
            return implode('', $this->pushes[$name]);
        }
        return '';
    }

    /**
     * @throws Exception
     */
    public function include($view, $data = []): void
    {
        $viewPath = BASE_PATH . 'resources/views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            throw new Exception("Include view not found: $view");
        }

        extract($data);
        include $viewPath;
    }
}