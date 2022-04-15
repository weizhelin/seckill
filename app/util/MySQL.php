<?php


namespace app\util;


class MySQL
{

    protected static $connection;

    public function __construct(){

    }

    public static function getConnection($file = 'mysql'){
        $mysqlConfig = config($file);
        if (empty($mysqlConfig)){
            return false;
        }
        // 假定数据库用户名：root，密码：123456，数据库：RUNOOB
        $con = mysqli_connect($mysqlConfig['hostname'],$mysqlConfig['username'],$mysqlConfig['password'],$mysqlConfig['database']);
        if (mysqli_connect_errno())
        {
            return false;
        }
        self::$connection = $con;
        return $con;
    }

    public static function close(): bool
    {
        return mysqli_close(self::$connection);
    }
}