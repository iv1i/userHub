<?php

namespace app\DTO;

class LoginDTO
{
    public function __construct(
        public string $username,
        public string $password,
        public string $csrf_token
    ) {
    }
}