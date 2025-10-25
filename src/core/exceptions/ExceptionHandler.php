<?php

namespace core\exceptions;

use Throwable;

class ExceptionHandler
{
    protected bool $debug;
    protected array $templatePaths;

    public function __construct()
    {
        $this->debug = env('APP_DEBUG', false);
        $this->templatePaths = $this->getTemplatePaths();
    }

    protected function getTemplatePaths(): array
    {
        return [
            
            BASE_PATH . 'resources/views/errors/',
            
            BASE_PATH . 'core/views/errors/',
        ];
    }

    public function handle(Throwable $exception): void
    {
        
        $this->logException($exception);

        
        if ($exception instanceof HttpException) {
            http_response_code($exception->getStatusCode());

            
            foreach ($exception->getHeaders() as $key => $value) {
                header("$key: $value");
            }
        } else {
            http_response_code(500);
        }

        
        $this->render($exception);
    }

    protected function logException(Throwable $exception): void
    {
        $message = "Uncaught exception: " . get_class($exception);
        $message .= " with message: " . $exception->getMessage();
        $message .= " in " . $exception->getFile() . ":" . $exception->getLine();
        $message .= "\nStack trace:\n" . $exception->getTraceAsString();

        error_log($message);
    }

    protected function render(Throwable $exception): void
    {
        
        if ($this->isAjaxRequest()) {
            $this->renderJson($exception);
            return;
        }

        
        $this->renderHtml($exception);
    }

    protected function renderJson(Throwable $exception): void
    {
        header('Content-Type: application/json');

        $response = [
            'error' => [
                'message' => $exception->getMessage(),
                'type' => get_class($exception),
            ]
        ];

        if ($exception instanceof ValidationException) {
            $response['error']['errors'] = $exception->getErrors();
        }

        if ($this->debug) {
            $response['error']['file'] = $exception->getFile();
            $response['error']['line'] = $exception->getLine();
            $response['error']['trace'] = $exception->getTrace();
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    protected function renderHtml(Throwable $exception): void
    {
        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;

        $data = [
            'exception' => $exception,
            'statusCode' => $statusCode,
            'debug' => $this->debug,
            'message' => $exception->getMessage(),
        ];

        
        $template = $this->findErrorTemplate($statusCode);

        if ($template) {
            $this->renderTemplate($template, $data);
        } else {
            
            $this->renderFallback($exception, $statusCode);
        }
    }

    protected function findErrorTemplate($statusCode): ?string
    {
        $templateName = $statusCode . '.php';

        foreach ($this->templatePaths as $path) {
            $fullPath = $path . $templateName;
            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }

        return null;
    }

    protected function renderTemplate($templatePath, $data): void
    {
        extract($data);
        require $templatePath;
    }

    protected function renderFallback(Throwable $exception, $statusCode): void
    {
        $message = $exception->getMessage();

        if (!$this->debug && $statusCode === 500) {
            $message = 'Something went wrong. Please try again later.';
        }

        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Error $statusCode</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .error-container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .error-code { color: #e74c3c; font-size: 24px; margin-bottom: 10px; }
                .error-message { color: #333; font-size: 16px; margin-bottom: 20px; }
                .debug-info { background: #f8f9fa; padding: 15px; border-radius: 3px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='error-container'>
                <div class='error-code'>Error $statusCode</div>
                <div class='error-message'>$message</div>
        ";

        if ($this->debug) {
            echo "
                <div class='debug-info'>
                    <strong>File:</strong> {$exception->getFile()}<br>
                    <strong>Line:</strong> {$exception->getLine()}<br>
                    <strong>Stack trace:</strong><br>
                    <pre>{$exception->getTraceAsString()}</pre>
                </div>
            ";
        }

        echo "
            </div>
        </body>
        </html>
        ";
    }

    protected function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /** @noinspection PhpUnused */
    public function addTemplatePath($path): static
    {
        if (!in_array($path, $this->templatePaths)) {
            array_unshift($this->templatePaths, $path); 
        }
        return $this;
    }
}