<?php

namespace app\Controllers;

use app\Models\User;
use app\Requests\CreateUserRequest;
use app\Requests\UpdateUserRequest;
use app\Requests\UsersListRequest;
use app\Services\UserService;
use core\exceptions\ValidationException;
use core\Views;
use Exception;

class UserController {
    private User $user;
    private UserService $service;

    public function __construct()
    {
        $this->user = new User();
        $this->service = new UserService();
    }

    /**
     * @throws Exception
     */
    public function list(UsersListRequest $request): void
    {
        $data = $this->service->list($request->getDTO(), $this->user);

        echo Views::make('users/list', $data);
    }

    /**
     * @throws Exception
     */
    public function view($id): void {
        $user = $this->service->view($id, $this->user);
        
        $data = [
            'user' => $user,
        ];

        echo Views::make('users/view', $data);
    }


    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function create(CreateUserRequest $request): void {
        $data = $this->service->create($request->getDTO(), $this->user);

        echo Views::make('users/create', $data);
    }

    /**
     * @throws Exception
     */
    public function createView(): void
    {
        echo Views::make('users/create');
    }

    /**
     * @throws Exception
     */
    public function editView(int $id): void
    {
        $user = $this->service->view($id, $this->user);

        $data = [
            'user' => $user,
        ];

        echo Views::make('users/edit', $data);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function edit(UpdateUserRequest $request, int $id): void {
        $user = $this->service->view($id, $this->user);
        
        $data = $this->service->edit($request->getDTO(), $user);

        echo Views::make('users/edit', $data);
    }

    public function delete($id): void {
        $this->service->delete($id, $this->user);

        header('Location: /users');
        exit;
    }
}
