<?php

namespace app\Models;

use app\Models\Modal;

class Service extends Modal
{
    protected static string $tableName = 'service';
    protected static string $primaryColumn = 'id_service';
    protected static array $columns = ['id_service', 'description', 'price', 'created_at', 'update_at', 'finished_at', 'commission_user', 'user_id_user'];

    protected static array $columnTypes = [
        'id_service'      => 'integer',
        'description'     => 'string',
        'price'           => 'float',
        'created_at'      => 'timestamp',
        'update_at'       => 'timestamp',
        'finished_at'     => 'timestamp',
        'commission_user' => 'float',
        'user_id_user'    => 'integer'
    ];
}