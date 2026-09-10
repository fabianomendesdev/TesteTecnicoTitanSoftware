<?php

namespace app\Models;

use app\Models\Modal;

class Employee extends Modal
{
    protected static $tableName = 'tb_employees';
    protected static $columns = ['id', 'created_at', 'updated_at', 'name', 'position'];

    protected $columnTypes = [
        'id'         => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'name'       => 'string',
        'position'   => 'string'
    ];
}