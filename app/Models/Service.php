<?php

namespace app\Models;

use app\Models\Modal;

class Service extends Modal
{
    protected static $tableName = 'tb_services';
    protected static $columns = ['id', 'created_at', 'updated_at', 'description', 'status', 'value', 'employee_id'];

    protected $columnTypes = [
        'id'          => 'integer',
        'created_at'  => 'timestamp',
        'updated_at'  => 'timestamp',
        'description' => 'string',
        'status'      => 'string',
        'value'       => 'string',
        'employee_id' => 'string'
    ];
}