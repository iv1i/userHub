<?php

namespace app\Repositories;

use app\DTO\UsersListDTO;
use app\Models\User;
use core\Pagination;

class UserRepository
{
    public function allPaginate(User $user, UsersListDTO $dto): array
    {
        $pagination = new Pagination($dto->page, 10, $user->count());
        $users = $user->findAll($dto->page, 10, $dto->sort_by, $dto->sort_order);
        
        return [
            'users' => $users,
            'pagination' => $pagination,
        ];
    }

    public function find(int $id,User $user): ?User
    {
        return $user->find($id);
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
        return $user->delete();
    }
}