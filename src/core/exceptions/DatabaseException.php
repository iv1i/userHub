<?php

namespace core\exceptions;

use Exception;

class DatabaseException extends HttpException
{
    public function __construct($message = 'Database error occurred', ?Exception $previous = null)
    {
        parent::__construct(500, $message, $previous);
    }
}