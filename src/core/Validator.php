<?php
namespace core;

use core\exceptions\ValidationException;
use Exception;
use PDOException;

final class Validator
{
    protected array $data;
    protected array $rules;
    protected array $messages;
    protected array $attributes;
    protected array $errors = [];
    protected Database $database;
    protected array $validatedData = [];

    public function __construct($data, $rules, $messages = [], $attributes = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->attributes = $attributes;
        $this->database = new Database();
        $this->validate();
    }

    public function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            $rules = is_array($rules) ? $rules : explode('|', $rules);
            $value = $this->getValue($field);
            
            if (in_array('nullable', $rules) && (is_null($value) || $value === '')) {
                $this->validatedData[$field] = $value;
                continue;
            }
            
            if (in_array('required', $rules) && !$this->validateRequired($field, $value, [])) {
                $this->addError($field, 'required', []);
                continue;
            }
            
            if (!in_array('required', $rules) && (is_null($value) || $value === '')) {
                $this->validatedData[$field] = $value;
                continue;
            }

            foreach ($rules as $rule) {
                if ($rule === 'nullable' || $rule === 'required') {
                    continue;
                }

                $this->applyRule($field, $value, $rule);
                
                if (isset($this->errors[$field])) {
                    break;
                }
            }

            if (!isset($this->errors[$field])) {
                $_SESSION['validation_errors'] = null;
                $this->validatedData[$field] = $this->sanitizeValue($field, $value, $rules);
            }
        }
    }

    protected function applyRule($field, $value, $rule): void
    {
        $params = [];

        if (str_contains($rule, ':')) {
            list($rule, $param) = explode(':', $rule, 2);
            $params = explode(',', $param);
        }

        $method = 'validate' . ucfirst($rule);

        if (method_exists($this, $method)) {
            if (!$this->$method($field, $value, $params)) {
                $this->addError($field, $rule, $params);
            }
        } else {
            
            $this->validateCustom($field, $value, $rule, $params);
        }
    }

    protected function sanitizeValue($field, $value, $rules): string
    {
        
        if ($field === 'password' || $field === 'confirm_password') {
            return $value; 
        }
        
        foreach ($rules as $rule) {
            if (str_contains($rule, 'integer') || str_contains($rule, 'numeric') !== false) {
                return (int) $value;
            }
            if (str_contains($rule, 'float') !== false || str_contains($rule, 'decimal') !== false) {
                return (float) $value;
            }
            if (str_contains($rule, 'boolean') !== false) {
                return (bool) $value;
            }
        }
        
        return is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') : $value;
    }

    protected function getValue($field): ?string
    {
        if (str_contains($field, '.')) {
            return $this->getNestedValue($field);
        }

        return $this->data[$field] ?? null;
    }

    protected function getNestedValue($field)
    {
        $keys = explode('.', $field);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    protected function addError($field, $rule, $params = []): void
    {
        $attribute = $this->attributes[$field] ?? $this->getFieldName($field);
        $messageKey = "$field.$rule";

        if (isset($this->messages[$messageKey])) {
            $message = $this->messages[$messageKey];
        } elseif (isset($this->messages[$rule])) {
            $message = $this->messages[$rule];
        } else {
            $message = $this->getDefaultMessage($rule, $attribute);
        }

        $this->errors[$field][] = $this->replacePlaceholders($message, $attribute, $params);
    }

    protected function getFieldName($field): array|string
    {
        return str_replace(['_', '.'], ' ', ucfirst($field));
    }

    protected function replacePlaceholders($message, $attribute, $params): array|string
    {
        $replacements = [
            ':attribute' => $attribute,
        ];

        foreach ($params as $key => $param) {
            $replacements[":$key"] = $param;
            $replacements[":".($key + 1)] = $param;
        }

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    protected function getDefaultMessage($rule, $attribute): string
    {
        $messages = [
            'required' => "Поле $attribute обязательно для заполнения.",
            'email' => "Поле $attribute должно быть валидным email адресом.",
            'min' => "Поле $attribute должно быть не менее :min символов.",
            'max' => "Поле $attribute должно быть не более :max символов.",
            'between' => "Поле $attribute должно быть между :min и :max символов.",
            'confirmed' => "Поле $attribute не совпадает с подтверждением.",
            'unique' => "Такое значение поля $attribute уже существует.",
            'exists' => "Выбранное значение для $attribute не существует.",
            'integer' => "Поле $attribute должно быть целым числом.",
            'numeric' => "Поле $attribute должно быть числом.",
            'string' => "Поле $attribute должно быть строкой.",
            'boolean' => "Поле $attribute должно быть true или false.",
            'array' => "Поле $attribute должно быть массивом.",
            'in' => "Поле $attribute должно быть одним из: :values.",
            'not_in' => "Поле $attribute не должно быть одним из: :values.",
            'date' => "Поле $attribute должно быть валидной датой.",
            'url' => "Поле $attribute должно быть валидным URL.",
            'ip' => "Поле $attribute должно быть валидным IP адресом.",
            'regex' => "Поле $attribute имеет неверный формат.",
            'digits' => "Поле $attribute должно состоять из :digits цифр.",
            'digits_between' => "Поле $attribute должно состоять из :min до :max цифр.",
            'size' => "Поле $attribute должно быть размером :size.",
            'nullable' => "Поле $attribute может быть пустым.",
        ];

        return $messages[$rule] ?? "Поле $attribute не прошло проверку $rule.";
    }

    protected function validateRequired($field, $value, $params): bool
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && count($value) === 0) {
            return false;
        }

        return true;
    }

    /** @noinspection PhpUnused */
    protected function validateNullable($field, $value, $params): true
    {
        return true; 
    }

    /** @noinspection PhpUnused */
    protected function validateInteger($field, $value, $params): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
    
    /** @noinspection PhpUnused */
    protected function validateNumeric($field, $value, $params): bool
    {
        return is_numeric($value);
    }

    /** @noinspection PhpUnused */
    protected function validateString($field, $value, $params): bool
    {
        return is_string($value);
    }

    /** @noinspection PhpUnused */
    protected function validateBoolean($field, $value, $params): bool
    {
        $acceptable = [true, false, 0, 1, '0', '1'];
        return in_array($value, $acceptable, true);
    }

    /** @noinspection PhpUnused */
    protected function validateArray($field, $value, $params): bool
    {
        return is_array($value);
    }

    /** @noinspection PhpUnused */
    protected function validateEmail($field, $value, $params): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /** @noinspection PhpUnused */
    protected function validateDate($field, $value, $params): bool
    {
        if (strtotime($value) === false) {
            return false;
        }

        $date = date_parse($value);
        return checkdate($date['month'], $date['day'], $date['year']);
    }

    /** @noinspection PhpUnused */
    protected function validateUrl($field, $value, $params): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /** @noinspection PhpUnused */
    protected function validateIp($field, $value, $params): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /** @noinspection PhpUnused */
    protected function validateRegex($field, $value, $params): bool
    {
        if (empty($params)) {
            return false;
        }

        return preg_match($params[0], $value) === 1;
    }

    /** @noinspection PhpUnused */
    protected function validateMin($field, $value, $params): bool
    {
        if (empty($params)) return false;

        $min = (int)$params[0];

        if (is_string($value)) {
            return mb_strlen($value) >= $min;
        } elseif (is_array($value)) {
            return count($value) >= $min;
        }

        return false;
    }

    /** @noinspection PhpUnused */
    protected function validateMax($field, $value, $params): bool
    {
        if (empty($params)) return false;

        $max = (int)$params[0];

        if (is_string($value)) {
            return mb_strlen($value) <= $max;
        } elseif (is_array($value)) {
            return count($value) <= $max;
        }

        return false;
    }

    /** @noinspection PhpUnused */
    protected function validateBetween($field, $value, $params): bool
    {
        if (count($params) < 2) return false;

        $min = (int)$params[0];
        $max = (int)$params[1];

        if (is_numeric($value)) {
            return $value >= $min && $value <= $max;
        } elseif (is_string($value)) {
            $length = mb_strlen($value);
            return $length >= $min && $length <= $max;
        } elseif (is_array($value)) {
            $count = count($value);
            return $count >= $min && $count <= $max;
        }

        return false;
    }

    /** @noinspection PhpUnused */
    protected function validateSize($field, $value, $params): bool
    {
        if (empty($params)) return false;

        $size = (int)$params[0];

        if (is_numeric($value)) {
            return $value == $size;
        } elseif (is_string($value)) {
            return mb_strlen($value) == $size;
        } elseif (is_array($value)) {
            return count($value) == $size;
        }

        return false;
    }

    /** @noinspection PhpUnused */
    protected function validateDigits($field, $value, $params): bool
    {
        if (empty($params)) return false;

        $digits = (int)$params[0];
        return is_numeric($value) && strlen((string)$value) === $digits;
    }

    /** @noinspection PhpUnused */
    protected function validateDigitsBetween($field, $value, $params): bool
    {
        if (count($params) < 2) return false;

        $min = (int)$params[0];
        $max = (int)$params[1];
        $length = strlen((string)$value);

        return is_numeric($value) && $length >= $min && $length <= $max;
    }

    /** @noinspection PhpUnused */
    protected function validateIn($field, $value, $params): bool
    {
        return in_array($value, $params);
    }

    /** @noinspection PhpUnused */
    protected function validateNotIn($field, $value, $params): bool
    {
        return !in_array($value, $params);
    }

    /** @noinspection PhpUnused */
    protected function validateConfirmed($field, $value, $params): bool
    {
        $confirmationField = $params[0] ?? "confirm_$field";
        return $value === $this->getValue($confirmationField);
    }

    /** @noinspection PhpUnused */
    /**
     * @throws Exception
     */
    protected function validateUnique($field, $value, $params): bool
    {
        if (empty($value)) return true;

        try {
            $table = $params[0] ?? 'users';
            $column = $params[1] ?? $field;
            $ignoreId = $params[2] ?? null;
            $ignoreColumn = $params[3] ?? 'id';

            $query = "SELECT COUNT(*) FROM `$table` WHERE `$column` = ?";
            $bindings = [$value];

            if ($ignoreId) {
                $query .= " AND `$ignoreColumn` != ?";
                $bindings[] = $ignoreId;
            }

            $conn = $this->database->getConnection();
            $stmt = $conn->prepare($query);
            $stmt->execute($bindings);

            return $stmt->fetchColumn() == 0;
        } catch (PDOException $e) {
            error_log("Database error in unique validation: " . $e->getMessage());
            return false;
        }
    }

    /** @noinspection PhpUnused */
    /**
     * @throws Exception
     */
    protected function validateExists($field, $value, $params): bool
    {
        if (empty($value)) return true;

        try {
            $table = $params[0] ?? 'users';
            $column = $params[1] ?? $field;

            $query = "SELECT COUNT(*) FROM `$table` WHERE `$column` = ?";

            $conn = $this->database->getConnection();
            $stmt = $conn->prepare($query);
            $stmt->execute([$value]);

            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error in exists validation: " . $e->getMessage());
            return false;
        }
    }

    /** @noinspection PhpUnused */
    protected function validateCustom($field, $value, $rule, $params): void
    {
        $customRules = $this->getCustomRules();

        if (isset($customRules[$rule]) && is_callable($customRules[$rule])) {
            $result = call_user_func($customRules[$rule], $field, $value, $params, $this->data);
            if (!$result) {
                $this->addError($field, $rule, $params);
            }
            return;
        }

        
        $this->addError($field, $rule, $params);
    }

    protected function getCustomRules(): array
    {
        return [
            'phone' => function($field, $value, $params, $data) {
                return preg_match('/^\+?[0-9\s\-()]{10,}$/', $value);
            },
            'password_strength' => function($field, $value, $params, $data) {
                return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
            }
        ];
    }
    
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /** @noinspection PhpUnused */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @throws ValidationException
     */
    public function validated(): array
    {
        if ($this->fails()) {
            throw new ValidationException($this->errors());
        }

        return $this->validatedData;
    }

    /**
     * @throws ValidationException
     */
    /** @noinspection PhpUnused */
    public function getValidatedData(): array
    {
        if ($this->fails()) {
            throw new ValidationException($this->errors());
        }

        return $this->validatedData;
    }

    /** @noinspection PhpUnused */
    public function setDatabase(Database $database): static
    {
        $this->database = $database;
        return $this;
    }

    /** @noinspection PhpUnused */
    public function sometimes($rules): static
    {
        foreach ($rules as $field => $fieldRules) {
            if ($this->has($field)) {
                $this->rules[$field] = array_merge($this->rules[$field] ?? [], $fieldRules);
            }
        }
        $this->validate();
        return $this;
    }

    public function has($field): bool
    {
        return array_key_exists($field, $this->data);
    }

    /** @noinspection PhpUnused */
    public function addRule($field, $rule): static
    {
        if (!isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }
        $this->rules[$field][] = $rule;
        $this->validate();
        return $this;
    }
}