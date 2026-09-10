<?php

namespace app\Models;

use app\Core\Database;

abstract class Modal
{
    protected static string $tableName;
    protected static array $columns = ['id'];
    protected static $hidden = ['password'];
    protected array $values = [];
    protected array $columnTypes = ['id' => 'integer'];
    private string $where = '';
    private array $params = [];
    private Database $database;

    private function __construct()
    {
        $this->database = Database::getInstance();
    }

    public static function __callStatic(string $method, array $arguments)
    {
        $instance = new static();

        if (method_exists($instance, $method)) {
            return call_user_func_array([$instance, $method], $arguments);
        }

        $className = static::class;
        throw new \Exception("Method {$method} does not exist in class {$className}.");
    }

    public function __get(string $name): mixed
    {
        return $this->values[$name];
    }

    public function __set(string $name, mixed $value): void
    {
        $this->values[$name] = $value;
    }

    protected function getTableName(): string
    {
        return $this->tableName ?? strtolower(get_class($this));
    }

    

    public function where(array $filter = []): self
    {


        return $this;
    }

    public function first()
    {
        $table = $this->getTableName();
        $result = $this->database->getResult(
            "SELECT * FROM $table {$this->where}"
        , $this->params);

        return "Oiiii";
        if ($result) {
            


            return $this;
        }

        return null;
    }

    public function getAll(): array
    {
        $table = $this->getTableName();
        return $this->database->getResult(
            "SELECT * FROM $table {$this->where}"
        , $this->params);
    }
}