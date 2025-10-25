<?php

namespace core\exceptions;

class ForbiddenException extends HttpException
{
    public function __construct($message = 'Access denied')
    {
        parent::__construct(403, $message);
    }
}