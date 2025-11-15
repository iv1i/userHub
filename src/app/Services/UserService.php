<?php

namespace app\Services;

use app\DTO\UserDTO;
use app\DTO\UsersListDTO;
use app\Models\User;
use app\Repositories\UserRepository;
use core\Security;

class UserService
{
    private UserRepository $repository;
    
    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function list(UsersListDTO $dto, User $user): array
    {
        return $this->repository->allPaginate($user, $dto);
    }

    public function view(string $id, User $user): ?User
    {
        $user->id = $id;
        return $this->repository->find($id, $user);
    }

    public function create(UserDTO $dto, User $user): array
    {
        $error = '';
        $success = '';

        $csrfToken = $dto->csrfToken;

        if (!Security::verifyCSRFToken($csrfToken)) {
            $error = "Invalid CSRF token.";
        } 
        else {
            $user->username = $dto->username;
            
            if ($this->repository->checkExistUser($user)) {
                $error = "Username already exists.";
            }
            else {
                $user->password_hash = Security::hashPassword($dto->password);
                $user->first_name = $dto->firstName;
                $user->last_name = $dto->lastName;
                $user->gender = $dto->gender;
                $user->birthdate = $dto->birthdate;
                $data = [
                    'username' => $dto->username,
                    'password_hash' => $dto->password,
                    'first_name' => $dto->firstName,
                    'last_name' => $dto->lastName,
                    'gender' => $dto->gender,
                    'birthdate' => $dto->birthdate,
                ];

                if ($this->repository->create($user, $data)) {
                    $success = "User created successfully.";
                }
                else {
                    $error = "Unable to create user. Please try again.";
                }
            }
        }
        
        return [
            'error' => $error,
            'success' => $success,
        ];
    }

    public function edit(UserDTO $dto, User $user): array {

        $error = '';
        $success = '';

        $csrfToken = $dto->csrfToken;

        if (!Security::verifyCSRFToken($csrfToken)) {
            $error = "Invalid CSRF token.";
        }
        else {
            $user->username = $dto->username;
            $user->first_name = $dto->firstName;
            $user->last_name = $dto->lastName;
            $user->gender = $dto->gender;
            $user->birthdate = $dto->birthdate;

            $data = [
                'username' => $dto->username,
                'password_hash' => $dto->password,
                'first_name' => $dto->firstName,
                'last_name' => $dto->lastName,
                'gender' => $dto->gender,
                'birthdate' => $dto->birthdate,
            ];

            
            if ($user->usernameExists()) {
                $error = "Username already exists.";
            } 
            else {
                if (!empty($dto->password)) {
                    $user->password_hash = Security::hashPassword($dto->password);
                }
                
                if ($this->repository->update($user, $data)) {
                    $success = "User updated successfully.";
                } else {
                    $error = "Unable to update user. Please try again.";
                }
            }
        }
        
        return [
            'error' => $error,
            'success' => $success,
            'user' => $user,
        ];

    }

    public function delete(int $id, User $user): void
    {

        if ($id <= 0) {
            $_SESSION['error'] = "Invalid user ID.";
        } else {
            $user->id = $id;

            if ($this->repository->delete($user, $id)) {
                $_SESSION['success'] = "User deleted successfully.";
            } else {
                $_SESSION['error'] = "Unable to delete user. Please try again.";
            }
        }

        header('Location: /users');
        exit;
    }
}