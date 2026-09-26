<?php

namespace app\Models;

use app\Core\Database;
use PDO;

abstract class Modal
{
    protected static string $tableName = '';
    protected static string $primaryColumn = '';
    protected static array $columns = [];
    protected static array $hidden = ['password'];
    protected static array $columnTypes = [];
    protected Database $database;
    protected array $values = [];
    private string $where = '';
    private int $limit = 0;
    private array $params = [];

    protected function __construct()
    {
        $this->database = Database::getInstance();
    }

    public static function __callStatic(string $method, array $arguments)
    {
        $instance = new static();
        return $instance->__call($method, $arguments);
    }

    public function __call(string $method, array $arguments)
    {
        $internalMethod = '_' . $method;

        if (method_exists($this, $internalMethod)) {
            return call_user_func_array([$this, $internalMethod], $arguments);
        }

        $className = static::class;
        throw new \Exception("Method {$method} does not exist in class {$className}.");
    }

    public function __get(string $name): mixed
    {
        if (in_array($name, static::$hidden)) return null;

        $value = $this->values[$name]        ?? null;
        $type  = static::$columnTypes[$name] ?? null;

        return ($value !== null && $type) ? $this->castValue($value, $name) : $value;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->values[$name] = $this->castValue($value, $name);
    }

    private function castValue(mixed $value, string $columnName): mixed
    {
        if ($value == null) return null;

        return match (strtolower(static::$columnTypes[$columnName] ?? '')) {
            'int', 'integer'  => (int) $value,
            'float', 'double' => (float) $value,
            'bool', 'boolean' => (bool) $value,
            'string'          => (string) $value,
            'array', 'json'   => is_string($value) ? json_decode($value, true) : (array) $value,
            'timestamp', 'datetime', 'date' => ($value instanceof \DateTime) ?
                $value : new \DateTime($value ?? 'now'),
            default           => $value,
        };
    }

    private function getPdoTypeParam(string $columnName): int
    {
        return match (strtolower(static::$columnTypes[$columnName] ?? '')) {
            'int', 'integer'  => PDO::PARAM_INT,
            'float', 'double' => PDO::PARAM_STR,
            'bool', 'boolean' => PDO::PARAM_BOOL,
            'string'          => PDO::PARAM_STR,
            'array', 'json'   => PDO::PARAM_STR,
            'timestamp', 'date', 'datetime' => PDO::PARAM_STR,
            default           => PDO::PARAM_STR,
        };
    }

    private function loadFromArrayAssoc(array $arrayAssoc): void
    {
        foreach ($arrayAssoc as $column => $value) {
            if (is_numeric($column)) {
                continue;
            }

            $this->values[$column] = !in_array($column, self::$hidden, true) ? 
                        $this->castValue($value, $column) : null;
        }
    }

    protected function getTableName(): string
    {
        if (isset(static::$tableName) && !empty(static::$tableName)) {
            return static::$tableName;
        }

        return strtolower((new \ReflectionClass($this))->getShortName());
    }

    public function _limit(int $limit): self
    {
        if ($limit) $this->limit = $limit;

        return $this;
    }

    public function _where(array $filters = []): self
    {
        if (!$filters) return $this;

        $this->where = '';
        $filter = [];

        foreach ($filters as $column => $filterItem) {
            $operator = '=';
            $value1   = null;
            $value2   = null;

            if (is_array($filterItem)) {
                $column    = $filterItem[0] ?? $column;
                $operator  = $filterItem[1] ?? '=';
                $value1    = $filterItem[2] ?? null;
                $value2    = $filterItem[3] ?? null;
            } else {
                $value1 = $filterItem;
            }

            $paramKey = str_replace('.', '_', $column);

            if (strtoupper($operator) == 'BETWEEN') {
                $filter[] = "{$column} BETWEEN :{$paramKey}_BET1 AND :{$paramKey}_BET2";

                $this->params["{$paramKey}_BET1"] = [
                    $value1 ?? null,
                    $this->getPdoTypeParam($column)
                ];

                $this->params["{$paramKey}_BET2"] = [
                    $value2 ?? null,
                    $this->getPdoTypeParam($column)
                ];

                continue;
            }

            if (is_null($value1) && $operator === '=') {
                $filter[] = "{$column} IS NULL";
                continue;
            }

            if (is_null($value1) && in_array($operator, ['!=', '<>'])) {
                $filter[] = "{$column} IS NOT NULL";
                continue;
            }

            $filter[] = "{$column} {$operator} :{$paramKey}";
            $this->params[$paramKey] = [
                $value1 ?? null,
                $this->getPdoTypeParam($column)
            ];
        }

        $this->where = 'WHERE ' . implode(' AND ', $filter);
      
        return $this;
    }

    public function _first()
    {
        $table = $this->getTableName();
        $result = $this->database->getResult(
            "SELECT * FROM $table {$this->where} LIMIT 1"
        , $this->params);

        if ($result) {
            $this->loadFromArrayAssoc($result[0] ?? []);
            return $this;
        }

        return null;
    }

    public function _getAll(): array
    {
        $table = $this->getTableName();
        $fetchAll = $this->database->getResult(
            "SELECT * FROM $table {$this->where} ". ($this->limit ? "LIMIT $this->limit" : '')
            , $this->params);

        if ($fetchAll) {
            $return = [];

            foreach ($fetchAll as $fetch) {
                $instance = new static();
                $instance->loadFromArrayAssoc($fetch ?? []);
                $return[] = $instance;
            }

            return $return;
        }

        return [];
    }

    public function _create(array $property): ?Modal
    {
        $table      = $this->getTableName();
        $primaryKey = !empty(static::$primaryColumn) ? static::$primaryColumn : 'id';

        foreach ($property as $key => $value) {
            if (!in_array($key, static::$columns)) {
                unset($property[$key]);
            }
        }

        $columns = array_keys($property);

        $placeholders = array_map(fn($col) => ":{$col}", $columns);

        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") " .
           "VALUES (" . implode(', ', $placeholders) . ")";

        $params = [];
        foreach ($property as $column => $value) {
            $params[$column] = [
                $value ?? null,
                $this->getPdoTypeParam($column)
            ];
        }

        $insertId = $this->database->sqlExec($sql, $params);

        return $this->where([$primaryKey => $insertId])->first();
    }

    public function delete(): bool
    {
        $table      = $this->getTableName();
        $primaryKey = !empty(static::$primaryColumn) ? static::$primaryColumn : 'id';
        $primaryVal = $this->values[$primaryKey] ?? null;

        if (!$primaryVal) {
            throw new \Exception("Chave primária ({$primaryKey}) não encontrada ou vazia.");
        }

        $sql = "DELETE FROM {$table} ";

        if ($this->where) {
            $sql .= $this->where;
        } else {
            $sql .= "WHERE {$primaryKey} = :pk_{$primaryKey}";
            $this->params["pk_{$primaryKey}"] = [$primaryVal, PDO::PARAM_INT];
        }

        return (bool) $this->database->sqlExec($sql, $this->params, $primaryKey);
    }

    public function save(array $columnsSave = []): bool
    {
        $table      = $this->getTableName();
        $primaryKey = !empty(static::$primaryColumn) ? static::$primaryColumn : 'id';
        $primaryVal = $this->values[$primaryKey] ?? null;

        if (!$primaryVal) {
            throw new \Exception("Chave primária ({$primaryKey}) não encontrada ou vazia.");
        }

        $setFields = [];
        $params    = [];
        foreach ($this->values as $column => $value) {
            if (
                $column === $primaryKey ||
                (!empty($columnsSave) && !in_array($column, $columnsSave))
            ) continue;

            if ($value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }

            $setFields[] = "{$column} = :{$column}";
            $params[$column] = [
                $value,
                PDO::PARAM_STR
            ];
        }

        $params["pk_{$primaryKey}"] = [$primaryVal, PDO::PARAM_INT];
        $sql = "UPDATE {$table} SET " . implode(', ', $setFields) . " WHERE {$primaryKey} = :pk_{$primaryKey}";

        return (bool) $this->database->sqlExec($sql, $params, $primaryKey);
    }

    public function toArray(): array
    {
        return $this->values;
    }
}