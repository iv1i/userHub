<?php

use app\Controllers\AuthController;
use app\Controllers\UserController;
use core\Router;

Router::redirect('/', '/login');
Router::get('/login', [AuthController::class, 'loginView'], ['protected' => false]);
Router::post('/login', [AuthController::class, 'login'], ['protected' => false]);
Router::get('/logout', [AuthController::class, 'logout'], ['protected' => true]);

Router::group('/users', function() {
    Router::get('', [UserController::class, 'list'], ['name' => 'users.index']);
    Router::get('/create', [UserController::class, 'createView'], ['name' => 'users.create']);
    Router::get('/{id}', [UserController::class, 'view'], ['name' => 'users.show']);
    Router::get('/{id}/edit', [UserController::class, 'editView'], ['name' => 'users.edit']);
    Router::get('/{id}/delete', [UserController::class, 'delete'], ['name' => 'users.destroy']);

    Router::post('/create', [UserController::class, 'create'], ['name' => 'users.create']);
    Router::post('/{id}', [UserController::class, 'edit'], ['name' => 'users.update']);
}, ['protected' => true]);