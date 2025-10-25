<?php

namespace core\exceptions;

class AuthException extends HttpException
{
    public function __construct($message = 'Authentication required')
    {
        parent::__construct(401, $message);
    }
}