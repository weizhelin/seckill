<?php


namespace app\controllers;


use app\util\MySQL;

class MySQLController
{


    public function index(){
        try {
            $this->getCount();
            p();
            $this->gets();
        } catch (\Exception $e) {
        }
    }


    protected function getCount(){
        $connection = MySQL::getConnection();
        if (!$connection){
            return false;
        }
        p($connection);
        $res = mysqli_query($connection,'select count( distinct customerNumber) count from orders');
        $arr = mysqli_fetch_assoc($res);
        $rs = MySQL::close();
        p($rs);
        return $arr['count'];
    }

    protected function gets(){
        $sql = "SELECT 
    officeCode, 
    city, 
    phone, 
    country
FROM
    offices limit 5";

        $sqls = [];
        $sqls[] = $sql;
        $sqls[] = "SELECT 
    t1.id, t2.id
FROM
    t1
        LEFT JOIN
    t2 ON t1.pattern = t2.pattern
ORDER BY t1.id;";


        $connection = MySQL::getConnection();
        p($connection);

        foreach ($sqls as $sql){
            $res = mysqli_query($connection,$sql);
            p($res);
            $rows = [];
            while ($row = mysqli_fetch_assoc($res)){
                $rows[] = $row;
            }
            p($rows);
        }

    }

}