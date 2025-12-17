<?php

namespace core;

abstract class Request
{
    protected array $query;
    protected array $request;
    protected array $attributes;
    protected array $cookies;
    protected array $files;
    protected array $server;
    protected array $headers;

    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->attributes = [];
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->getHeaders();
    }
    
    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }
    
    public function input($key, $default = null)
    {
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    /** @noinspection PhpUnused */
    public function query($key = null, $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    /** @noinspection PhpUnused */
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $this->request;
        }
        return $this->request[$key] ?? $default;
    }

    public function file($key): mixed
    {
        return $this->files[$key] ?? null;
    }

    /** @noinspection PhpUnused */
    public function files(): array
    {
        return $this->files;
    }
    
    public function has($key): bool
    {
        return isset($this->request[$key]) || isset($this->query[$key]);
    }

    /** @noinspection PhpUnused */
    public function only($keys): array
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->input($key);
        }

        return $results;
    }

    /** @noinspection PhpUnused */
    public function except($keys): array
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $all = $this->all();

        foreach ($keys as $key) {
            unset($all[$key]);
        }

        return $all;
    }
    
    public function method(): mixed
    {
        return $this->server['REQUEST_METHOD'];
    }

    /** @noinspection PhpUnused */
    public function isMethod($method): bool
    {
        return strtoupper($method) === $this->method();
    }

    /** @noinspection PhpUnused */
    public function path(): bool|string
    {
        $path = $this->server['REQUEST_URI'] ?? '/';
        return strtok($path, '?');
    }
    
    /** @noinspection PhpUnused */
    public function url(): mixed
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    /** @noinspection PhpUnused */
    public function ip(): mixed
    {
        return $this->server['HTTP_CLIENT_IP'] ??
            $this->server['HTTP_X_FORWARDED_FOR'] ??
            $this->server['REMOTE_ADDR'] ?? null;
    }

    /** @noinspection PhpUnused */
    public function userAgent(): mixed
    {
        return $this->server['HTTP_USER_AGENT'] ?? null;
    }
    
    private function getHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    
    public function header($key = null, $default = null): mixed
    {
        if ($key === null) {
            return $this->headers;
        }
        return $this->headers[$key] ?? $default;
    }

    /** @noinspection PhpUnused */
    public function hasHeader($key): bool
    {
        return isset($this->headers[$key]);
    }
    
    /** @noinspection PhpUnused */
    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization', '');

        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }

    /** @noinspection PhpUnused */
    public function json($key = null, $default = null): mixed
    {
        $content = file_get_contents('php://input');
        $data = json_decode($content, true);

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }
    
    public function __get($name)
    {
        return $this->input($name);
    }
    
    public function __isset($name)
    {
        return $this->has($name);
    }
}