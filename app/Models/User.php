<?php

namespace app\Models;

use app\Models\Modal;

class User extends Modal
{
    protected static $tableName = 'tb_users';
    protected static $columns = ['id', 'created_at', 'updated_at', 'name'];

    protected $hidden = ['password'];

    protected $columnTypes = [
        'id'         => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'name'       => 'string',
        'password'   => 'string',
    ];

    public function passwordVerify(string $password): bool
    {
        return password_verify($this->password, $password);
    }
}