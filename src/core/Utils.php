<?php

function env(string $key, $default = null) {
    static $envVars = null;

    if ($envVars === null) {
        $envPath = __DIR__ . '/../../.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                list($name, $value) = explode('=', $line, 2);
                $envVars[trim($name)] = trim($value);
            }
        }
    }

    $value = $envVars[$key] ?? $default;
    
    if (is_string($value)) {
        $lowerValue = strtolower($value);
        if ($lowerValue === 'true') {
            return true;
        } elseif ($lowerValue === 'false') {
            return false;
        }
    }

    return $value;
}
