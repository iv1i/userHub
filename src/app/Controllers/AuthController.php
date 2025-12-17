<?php

namespace app\Controllers;

use app\Requests\LoginRequest;
use app\Services\AuthService;
use core\Auth;
use core\Views;
use Exception;

class AuthController {
    public AuthService $service;

    public function __construct(
    ) {
        $this->service = new AuthService();
    }

    /**
     * @throws Exception
     */
    public function loginView(): void
    {
        if (Auth::isLoggedIn()) {
            header('location: /users');
        }
        echo Views::make('auth/login');
    }

    /**
     * @throws Exception
     */
    public function login(LoginRequest $request): void
    {
        $data = $this->service->login($request->getDTO());

        echo Views::make('auth/login', $data);
    }
    
    public function logout(): void {
        $this->service->logout();
    }
}
