<?php

use core\exceptions\ExceptionHandler;

function autoload(): void
{
    spl_autoload_register(function ($className) {
        $baseDir = __DIR__ . '/';
        $relativeClass = str_replace('App\\', 'app\\', $className);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        } else {
            error_log("Class file not found: " . $file);
        }
    });
    set_exception_handler(function ($exception) {
        $handler = new ExceptionHandler();
        $handler->handle($exception);
    });


    set_error_handler(
        /**
        * @throws ErrorException
        */ 
        function ($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return;
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
}
