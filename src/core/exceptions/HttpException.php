<?php

namespace core\exceptions;

use Exception;

class HttpException extends Exception
{
    protected int $statusCode;
    protected array $headers;

    public function __construct($statusCode = 500, $message = null, Exception $previous = null, array $headers = [], $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        if ($message === null) {
            $message = $this->getDefaultMessage($statusCode);
        }

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    protected function getDefaultMessage($statusCode): string
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];

        return $messages[$statusCode] ?? 'Unknown Error';
    }
}