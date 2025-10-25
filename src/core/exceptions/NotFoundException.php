<?php

namespace core\exceptions;

class NotFoundException extends HttpException
{
    public function __construct($message = 'Page not found')
    {
        parent::__construct(404, $message);
    }
}