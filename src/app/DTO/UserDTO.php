<?php

namespace app\DTO;

class UserDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $gender,
        public string $birthdate,
        public string $password,
        public string $confirmPassword,
        public string $username,
        public string $csrfToken,
    ) {
    }
}