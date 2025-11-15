<?php

namespace core;

use Exception;
use PDO;
use PDOStatement;
use ReflectionClass;

abstract class Model
{
    protected ?PDO $conn;
    protected string $table_name;
    protected string $primary_key = 'id';
    protected array $fillable = [];
    protected array $guarded = ['id', 'created_at'];
    protected bool $timestamps = false;

    // Добавляем свойство для хранения данных модели
    protected array $attributes = [];

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();


        if (empty($this->table_name)) {
            $reflection = new ReflectionClass($this);
            $className = $reflection->getShortName();
            $this->table_name = strtolower($className) . 's';
        }
    }

    /**
     * Статический метод для поиска по ID с возвратом экземпляра модели
     */
    public static function find($id, array $columns = ['*']): ?static
    {
        $instance = new static();
        $data = $instance->findById($id, $columns);

        if ($data) {
            return $instance->fill($data);
        }

        return null;
    }

    /**
     * Нестатический метод для поиска по ID (возвращает массив)
     */
    public function findById($id, array $columns = ['*']): ?array
    {
        $validatedColumns = $this->validateColumns($columns);

        $query = sprintf(
            "SELECT %s FROM %s WHERE %s = :id LIMIT 1",
            $validatedColumns,
            $this->table_name,
            $this->primary_key
        );

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Заполнение модели данными
     */
    public function fill(array $data): static
    {
        $this->attributes = $data;

        // Также устанавливаем публичные свойства, если они существуют
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Магический метод для доступа к атрибутам
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Магический метод для установки атрибутов
     */
    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;

        // Также устанавливаем публичное свойство, если оно существует
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    /**
     * Магический метод для проверки существования атрибута
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Получение всех атрибутов модели
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Преобразование модели в массив
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Сохранение модели (создание или обновление)
     */
    public function save(): bool
    {
        if (isset($this->attributes[$this->primary_key])) {
            // Обновление существующей записи
            return $this->update($this->attributes[$this->primary_key], $this->attributes);
        } else {
            // Создание новой записи
            $result = $this->create($this->attributes);
            if ($result) {
                $this->attributes[$this->primary_key] = $this->lastInsertId();
            }
            return $result;
        }
    }

    // Остальные методы остаются без изменений, но обновляем findWhere и getWhere:

    /**
     * Поиск по условиям с возвратом экземпляра модели
     */
    public static function findWhere(array $conditions, array $columns = ['*']): ?static
    {
        $instance = new static();
        $data = $instance->findWhereAsArray($conditions, $columns);

        if ($data) {
            return $instance->fill($data);
        }

        return null;
    }

    /**
     * Поиск по условиям (возвращает массив)
     */
    public function findWhereAsArray(array $conditions, array $columns = ['*']): ?array
    {
        $validatedColumns = $this->validateColumns($columns);
        $whereClause = $this->buildWhereClause($conditions);

        $query = sprintf(
            "SELECT %s FROM %s WHERE %s LIMIT 1",
            $validatedColumns,
            $this->table_name,
            $whereClause
        );

        $stmt = $this->conn->prepare($query);
        $this->bindConditions($stmt, $conditions);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Получение нескольких записей по условиям с возвратом массива моделей
     */
    public static function getWhere(array $conditions, array $columns = ['*']): array
    {
        $instance = new static();
        $data = $instance->getWhereAsArray($conditions, $columns);

        $models = [];
        foreach ($data as $item) {
            $model = new static();
            $models[] = $model->fill($item);
        }

        return $models;
    }

    /**
     * Получение нескольких записей по условиям (возвращает массив)
     */
    public function getWhereAsArray(array $conditions, array $columns = ['*']): array
    {
        $validatedColumns = $this->validateColumns($columns);
        $whereClause = $this->buildWhereClause($conditions);

        $query = sprintf(
            "SELECT %s FROM %s WHERE %s",
            $validatedColumns,
            $this->table_name,
            $whereClause
        );

        $stmt = $this->conn->prepare($query);
        $this->bindConditions($stmt, $conditions);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Остальные методы остаются без изменений:

    public function findAll(
        int $page = 1,
        int $perPage = 10,
        string $sortBy = 'id',
        string $sortOrder = 'ASC',
        array $columns = ['*']
    ): array {
        $offset = ($page - 1) * $perPage;
        $validatedColumns = $this->validateColumns($columns);
        $validatedSort = $this->validateSortParams($sortBy, $sortOrder);

        $query = sprintf(
            "SELECT %s FROM %s ORDER BY %s %s LIMIT :limit OFFSET :offset",
            $validatedColumns,
            $this->table_name,
            $validatedSort['sort_by'],
            $validatedSort['sort_order']
        );

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool
    {
        $filteredData = $this->filterData($data);
        $columns = array_keys($filteredData);
        $placeholders = ':' . implode(', :', $columns);


        if ($this->timestamps) {
            $filteredData['created_at'] = date('Y-m-d H:i:s');
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
            $columns[] = 'created_at';
            $columns[] = 'updated_at';
            $placeholders .= ', :created_at, :updated_at';
        }

        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table_name,
            implode(', ', $columns),
            $placeholders
        );

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($filteredData);
    }

    public function update($id, array $data): bool
    {
        $filteredData = $this->filterData($data);


        if ($this->timestamps) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }

        $setClause = implode(', ', array_map(function($col) {
            return "$col = :$col";
        }, array_keys($filteredData)));

        $query = sprintf(
            "UPDATE %s SET %s WHERE %s = :id",
            $this->table_name,
            $setClause,
            $this->primary_key
        );

        $stmt = $this->conn->prepare($query);
        $filteredData['id'] = $id;

        return $stmt->execute($filteredData);
    }

    public function delete(): bool
    {
        if (!isset($this->attributes[$this->primary_key])) {
            return false;
        }

        $query = sprintf(
            "DELETE FROM %s WHERE %s = :id",
            $this->table_name,
            $this->primary_key
        );

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $this->attributes[$this->primary_key]);

        return $stmt->execute();
    }

    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function countWhere(array $conditions): int
    {
        $whereClause = $this->buildWhereClause($conditions);

        $query = sprintf(
            "SELECT COUNT(*) as total FROM %s WHERE %s",
            $this->table_name,
            $whereClause
        );

        $stmt = $this->conn->prepare($query);
        $this->bindConditions($stmt, $conditions);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }

    public function exists(string $field, $value, $excludeId = null): bool
    {
        $query = "SELECT 1 FROM " . $this->table_name . " WHERE {$field} = :value";
        $params = ['value' => $value];

        if ($excludeId) {
            $query .= " AND {$this->primary_key} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $query .= " LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    public function beginTransaction(): bool
    {
        return $this->conn->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->conn->commit();
    }

    public function rollBack(): bool
    {
        return $this->conn->rollBack();
    }

    public function lastInsertId(): string
    {
        return $this->conn->lastInsertId();
    }

    protected function validateColumns(array $columns): string
    {
        if (in_array('*', $columns)) {
            return '*';
        }

        return implode(', ', $columns);
    }

    protected function validateSortParams(string $sortBy, string $sortOrder): array
    {
        $allowedSorts = $this->getAllowedSortFields();
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : $this->primary_key;
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        return [
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder
        ];
    }

    protected function getAllowedSortFields(): array
    {
        return ['id', 'created_at'];
    }

    protected function filterData(array $data): array
    {
        $filtered = [];

        foreach ($data as $key => $value) {

            if (in_array($key, $this->guarded)) {
                continue;
            }


            if (!empty($this->fillable) && !in_array($key, $this->fillable)) {
                continue;
            }

            $filtered[$key] = Security::sanitizeInput($value);
        }

        return $filtered;
    }

    protected function buildWhereClause(array $conditions): string
    {
        $clauses = [];

        foreach (array_keys($conditions) as $column) {
            $clauses[] = "{$column} = :{$column}";
        }

        return implode(' AND ', $clauses);
    }

    protected function bindConditions(PDOStatement $stmt, array $conditions): void
    {
        foreach ($conditions as $column => $value) {
            $stmt->bindValue(":{$column}", $value);
        }
    }
}