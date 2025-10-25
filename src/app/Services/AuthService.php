<?php

namespace app\Services;

use app\DTO\LoginDTO;
use core\Auth;
use core\Security;

class AuthService
{
    public function login(LoginDTO $dto): array
    {
        if (!Security::verifyCSRFToken($dto->csrf_token)) {
            $error = "Invalid CSRF token.";
        } else {
            $username = $dto->username;
            $password = $dto->password;

            if (empty($username) || empty($password)) {
                $error = "Please fill in all fields.";
            } elseif (Auth::login($username, $password)) {
                header('Location: /users');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        }
        return ['error' => $error];
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}