<?php

use core\Router;
use core\exceptions\ExceptionHandler;

const BASE_PATH = __DIR__ . '/../';

require_once BASE_PATH . 'autoload.php';


try {
    autoload();
} catch (ErrorException $e) {
    $handler = new ExceptionHandler();
    $handler->handle($e);
}

session_start();
require_once BASE_PATH . 'routes/web.php';
require_once BASE_PATH . 'core/Utils.php';

try {
    Router::handle();
} catch (Throwable $e) {
    $handler = new ExceptionHandler();
    $handler->handle($e);
}