<?php
namespace core\controller;

use Exception;

class Conf
{
    static public $conf = array();

    /**
     * @param $name
     * @param $file
     * @return mixed
     * @throws Exception
     */
    static public function get($name, $file)
    {
        /**
         * 1 判断配置文件是否存在
         * 2 判断配置是否存在
         * 3 缓存配置
         */
        $file = ROOT_PATH.'/core/'.$file.'.php';
        if (is_file($file)) {
            $conf = include($file);
            if (isset($conf[$name])) {
                self::$conf[$file]  = $conf;
                return $conf[$name];
            }else{
                throw new Exception("没有这个配置项".$name, 1);
            }
        }else{
            throw new Exception("找不到配置文件", 1);

        }
    }
}