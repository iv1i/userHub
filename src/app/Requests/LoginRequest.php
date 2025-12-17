<?php

namespace app\Requests;

use app\DTO\LoginDTO;
use core\exceptions\AuthException;
use core\exceptions\ValidationException;
use core\FormRequest;
use core\Security;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['string'],
            'password' => ['string'],
            'csrf_token' => ['string'],
        ];
    }

    /**
     * @throws ValidationException|AuthException
     */
    public function getDTO(): LoginDTO
    {
        $validated = $this->validate();

        return new LoginDTO(
            username: Security::sanitizeInput($validated['username'] ?? ''),
            password: Security::sanitizeInput($validated['password'] ?? ''),
            csrf_token: Security::sanitizeInput($validated['csrf_token'] ?? '')
        );
    }
}