<?php

namespace app\Models;

use core\Model;
use PDO;
use PDOStatement;

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

    public int $id;
    public string $username;
    public string $password_hash;
    public string $first_name;
    public string $last_name;
    public string $gender;
    public string $birthdate;

    public function readAll($page = 1, $records_per_page = 10, $sort_by = 'id', $sort_order = 'ASC'): bool|PDOStatement
    {
        $offset = ($page - 1) * $records_per_page;

        $validatedSort = $this->validateSortParams($sort_by, $sort_order);
        $columns = ['id', 'username', 'first_name', 'last_name', 'gender', 'birthdate', 'created_at'];

        $query = sprintf(
            "SELECT %s FROM %s ORDER BY %s %s LIMIT :limit OFFSET :offset",
            implode(', ', $columns),
            $this->table_name,
            $validatedSort['sort_by'],
            $validatedSort['sort_order']
        );

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }
    
    public function countAll(): int
    {
        return $this->count();
    }

    public function readOne(): bool
    {
        $user = $this->find($this->id);

        if ($user) {
            foreach ($user as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            return true;
        }

        return false;
    }
    
    public function usernameExists(): bool
    {
        return $this->exists('username', $this->username, $this->id);
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'username', 'first_name', 'last_name', 'created_at'];
    }
}