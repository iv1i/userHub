<?php

namespace core\exceptions;

class ValidationException extends HttpException
{
    protected array $errors;

    public function __construct(array $errors, $message = 'Validation failed')
    {
        $this->errors = $errors;
        parent::__construct(422, $message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
