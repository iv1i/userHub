<?php

namespace core;

use core\exceptions\AuthException;
use core\exceptions\ValidationException;
use JetBrains\PhpStorm\NoReturn;

abstract class FormRequest extends Request
{
    public function rules(): array
    {
        return [];
    }
    
    public function messages(): array
    {
        return [];
    }
    
    public function attributes(): array
    {
        return [];
    }
    
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @throws ValidationException|AuthException
     */
    public function validate(): mixed
    {
        if (!$this->authorize()) {
            return $this->failedAuthorization();
        }

        $validator = new Validator($this->all(), $this->rules(), $this->messages(), $this->attributes());

        if ($validator->fails()) {
            $this->failedValidation($validator);
        }

        return $this->validated();
    }

    /**
     * @throws ValidationException
     */
    public function validated(): array
    {
        $validator = new Validator($this->all(), $this->rules());
        return $validator->validated();
    }

    /**
     * @throws AuthException
     */
    protected function failedAuthorization(): array
    {
        return [
            throw new AuthException('This action is unauthorized.')
        ];
    }

    #[NoReturn]
    protected function failedValidation(Validator $validator): void
    {
        $_SESSION['validation_errors'] = $validator->errors();
        $_SESSION['old_input'] = $this->all();

        // Редирект назад
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }
}