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


if (!function_exists('dd')) {
    /**
     * Dump and die - аналог Laravel dd()
     */
    function dd(...$vars): void
    {
        echo '<style>
            .dd-wrapper {
                background: #1e1e1e;
                color: #e0e0e0;
                padding: 20px;
                margin: 10px;
                border-radius: 5px;
                font-family: "Courier New", monospace;
                font-size: 14px;
                line-height: 1.4;
                border-left: 4px solid #dc3545;
            }
            .dd-header {
                color: #dc3545;
                font-weight: bold;
                margin-bottom: 10px;
                font-size: 16px;
            }
            .dd-item {
                margin: 8px 0;
                padding: 8px;
                background: #2d2d2d;
                border-radius: 3px;
            }
            .dd-type {
                color: #569cd6;
                font-weight: bold;
            }
            .dd-value {
                color: #ce9178;
            }
            .dd-file {
                color: #6a9955;
                font-size: 12px;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px solid #444;
            }
        </style>';

        echo '<div class="dd-wrapper">';
        echo '<div class="dd-header">DUMP AND DIE</div>';

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];

        foreach ($vars as $var) {
            echo '<div class="dd-item">';
            echo '<span class="dd-type">(' . gettype($var) . ')</span> ';
            echo '<span class="dd-value">';
            highlight_var($var);
            echo '</span>';
            echo '</div>';
        }

        echo '<div class="dd-file">';
        echo 'File: ' . ($backtrace['file'] ?? 'unknown') . '<br>';
        echo 'Line: ' . ($backtrace['line'] ?? 'unknown');
        echo '</div>';

        echo '</div>';

        exit(1);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump without dying - аналог Laravel dump()
     */
    function dump(...$vars): void
    {
        echo '<style>
            .dump-wrapper {
                background: #2c3e50;
                color: #ecf0f1;
                padding: 15px;
                margin: 10px;
                border-radius: 5px;
                font-family: "Courier New", monospace;
                font-size: 13px;
                border-left: 4px solid #3498db;
            }
            .dump-header {
                color: #3498db;
                font-weight: bold;
                margin-bottom: 8px;
            }
            .dump-item {
                margin: 5px 0;
                padding: 5px;
                background: #34495e;
                border-radius: 3px;
            }
        </style>';

        echo '<div class="dump-wrapper">';
        echo '<div class="dump-header">DUMP</div>';

        foreach ($vars as $var) {
            echo '<div class="dump-item">';
            highlight_var($var);
            echo '</div>';
        }

        echo '</div>';
    }
}

if (!function_exists('highlight_var')) {
    /**
     * Красивое отображение переменной
     */
    function highlight_var($var): void
    {
        switch (gettype($var)) {
            case 'NULL':
                echo '<span style="color: #569cd6;">null</span>';
                break;
            case 'boolean':
                echo '<span style="color: #569cd6;">' . ($var ? 'true' : 'false') . '</span>';
                break;
            case 'integer':
            case 'double':
                echo '<span style="color: #b5cea8;">' . $var . '</span>';
                break;
            case 'string':
                echo '<span style="color: #ce9178;">"' . htmlspecialchars($var) . '"</span>';
                break;
            case 'array':
                echo '<span style="color: #569cd6;">array</span>';
                echo '<span style="color: #cccccc;">(' . count($var) . ')</span> ';
                highlight_array($var);
                break;
            case 'object':
                echo '<span style="color: #4ec9b0;">' . get_class($var) . '</span>';
                echo '<span style="color: #cccccc;"> Object</span>';
                highlight_object($var);
                break;
            default:
                echo '<span style="color: #cccccc;">' . gettype($var) . '</span>';
        }
    }
}

if (!function_exists('highlight_array')) {
    /**
     * Красивое отображение массива
     */
    function highlight_array(array $array, int $depth = 1): void
    {
        if (empty($array)) {
            echo '[]';
            return;
        }

        $indent = str_repeat('  ', $depth);
        echo "[\n";

        foreach ($array as $key => $value) {
            echo $indent . '  ';

            if (is_string($key)) {
                echo '<span style="color: #9cdcfe;">"' . $key . '"</span>';
            } else {
                echo '<span style="color: #b5cea8;">' . $key . '</span>';
            }

            echo ' => ';
            highlight_var($value);
            echo ",\n";
        }

        echo $indent . ']';
    }
}

if (!function_exists('highlight_object')) {
    /**
     * Красивое отображение объекта
     */
    function highlight_object(object $object, int $depth = 1): void
    {
        $reflection = new ReflectionClass($object);
        $properties = $reflection->getProperties();

        if (empty($properties)) {
            echo ' {}';
            return;
        }

        $indent = str_repeat('  ', $depth);
        echo " {\n";

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);

            echo $indent . '  ';
            echo '<span style="color: #9cdcfe;">' . $property->getName() . '</span>';
            echo ': ';
            highlight_var($value);
            echo ",\n";
        }

        echo $indent . '}';
    }
}
