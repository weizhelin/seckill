<?php
namespace app\models;

use Medoo\Medoo;

class BaseDao extends Medoo
{
    function __construct()
    {
        $options = [
            'database_type' => 'mysql',
            'database_name' => 'sldtest',
            'server'        => 'localhost',
            'username'      => 'root',
            'password'      => '123456',
            'prefix'        => 'bbc_p_',
        ];
        parent::__construct($options);
    }
}