<?php

namespace app\Repositories;

use app\DTO\UsersListDTO;
use app\Models\User;
use core\Pagination;

class UserRepository
{
    public function allPaginate(User $user, UsersListDTO $dto): array
    {
        $pagination = new Pagination($dto->page, 10, $user->countAll());
        $users = $user->readAll($dto->page, 10, $dto->sort_by, $dto->sort_order);
        
        return [
            'users' => $users,
            'pagination' => $pagination,
        ];
    }

    public function find(User $user): ?User
    {
        if($user->readOne()) return $user;
        else return null;
    }

    public function create(User $user, array $data): bool
    {
        return $user->create($data);
    }

    public function checkExistUser(User $user): bool
    {
        return $user->usernameExists();
    }

    public function update(User $user, array $data):bool
    {
        return $user->update($user->id, $data);
    }

    public function delete(User $user, int $id): bool
    {
        return $user->delete($id);
    }
}