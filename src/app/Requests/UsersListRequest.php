<?php

namespace app\Requests;

use app\DTO\UsersListDTO;
use core\FormRequest;

class UsersListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getDTO(): UsersListDTO
    {
        $data = $this->all();
        return new UsersListDTO(
            page: $data['page'] ?? 1,
            sort_by: $data['sort_by'] ?? 'id',
            sort_order: $data['sort_order'] ?? 'asc',
        );
    }
}