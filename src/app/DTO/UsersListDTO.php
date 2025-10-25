<?php

namespace app\DTO;

class UsersListDTO
{
    public function __construct(
        public int $page,
        public string $sort_by,
        public string $sort_order,
    ) {
    }
}