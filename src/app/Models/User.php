<?php

namespace app\Models;

use core\Model;

/**
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $birthdate
 * @property string $created_at
 * @property string $updated_at
 */
final class User extends Model
{
    protected string $table_name = "users";
    protected string $primary_key = "id";
    protected array $fillable = [
        'username',
        'password_hash',
        'first_name',
        'last_name',
        'gender',
        'birthdate'
    ];
    protected bool $timestamps = true;

    public function usernameExists(): bool
    {
        return $this->exists('username', $this->username, $this->id ?? null);
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'username', 'first_name', 'last_name', 'created_at'];
    }
}