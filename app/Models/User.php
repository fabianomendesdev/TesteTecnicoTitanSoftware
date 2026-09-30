<?php

namespace app\Models;

use app\Models\Modal;

class User extends Modal
{
    protected static string $tableName = 'user';
    protected static string $primaryColumn = 'id_user';
    protected static array $columns = ['id_user', 'name', 'email', 'password', 'created_at', 'updated_at', 'ativo', 'is_admin', 'session_token'];

    protected static array $hidden = ['password', 'session_token'];

    protected static array $columnTypes = [
        'id_user'       => 'int',
        'name'          => 'string',
        'email'         => 'string',
        'password'      => 'string',
        'created_at'    => 'timestamp',
        'updated_at'    => 'timestamp',
        'ativo'         => 'boolean',
        'is_admin'      => 'boolean',
        'session_token' => 'string'
    ];

    public function passwordVerify(string $password): bool
    {
        $table         = static::$tableName;
        $primaryColumn = static::$primaryColumn;

        if (!$this->{$primaryColumn}) {
            return false;
        }

        $result = $this->database->getResult(
            "SELECT password FROM {$table} WHERE {$primaryColumn} = :{$primaryColumn} LIMIT 1",
            [$primaryColumn => [$this->{$primaryColumn}, \PDO::PARAM_INT]]
        );

        $hash = $result[0]['password'] ?? null;

        if (!$hash) {
            return false;
        }

        return password_verify($password, $hash);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}