<?php

namespace app\Requests;

use app\DTO\UserDTO;
use core\exceptions\AuthException;
use core\exceptions\ValidationException;
use core\FormRequest;
use core\Security;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'max:255', 'confirmed'],
            'confirm_password' => ['required', 'string', 'min:6', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required'],
            'birthdate' => ['required'],
            'csrf_token' => ['required'],
        ];
    }

    /**
     * @throws ValidationException|AuthException
     */
    public function getDTO(): UserDTO
    {
        $validated = $this->validate();

        return new UserDTO(
            firstName: Security::sanitizeInput($validated['first_name'] ?? ''),
            lastName: Security::sanitizeInput($validated['last_name'] ?? ''),
            gender: Security::sanitizeInput($validated['gender'] ?? ''),
            birthdate: Security::sanitizeInput($validated['birthdate'] ?? ''),
            password: $validated['password'] ?? '',
            confirmPassword: $validated['confirm_password'] ?? '',
            username: Security::sanitizeInput($validated['username'] ?? ''),
            csrfToken: Security::sanitizeInput($validated['csrf_token'] ?? '')
        );
    }
}