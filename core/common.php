<?php
/**
 * 框架通用函数
 */

/**
 * [p 数据格式化输出函数]
 * @param  [type] $var [数据]
 * @return [type]      [description]
 */
if(!function_exists('p')){
    function p(){
        $args = func_get_args();
        foreach ($args as $var){
            if (is_bool($var)) {
                var_dump($var);
            }elseif (is_null($var)) {
                var_dump(NULL);
            }else{
                echo '<pre style="position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9">'.print_r($var,true).'</pre>';
            }
        }
    }
}

/**
 * [list_sort_by 二维数组排序函数]
 * @param  [type] $list   [要进行排序的数组]
 * @param  [type] $field  [根据该键进行排序]
 * @param  string $sortby [排序方式]
 * @return [type]         [description]
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }
        switch ($sortby) {
            case 'asc':			 	// 正向排序
                asort($refer);
                break;
            case 'desc': 			// 逆向排序
                arsort($refer);
                break;
            case 'nat': 			// 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }
        return $resultSet;
    }
    return false;
}

function curl_getheader($url){
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);        //返回头信息
    curl_setopt($ch, CURLOPT_NOBODY, true);        //不返回内容
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //返回数据不直接输出
    $content = curl_exec($ch);                    //执行并存储结果
    curl_close($ch);
    return $content;
}

function curl_get($url){
    $curl = curl_init();                                // cURL初始化
    curl_setopt($curl, CURLOPT_URL, $url) ;             // 设置访问网页的URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   // 执行之后不直接打印出来
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 100);
    $output = curl_exec($curl);                         // 执行
    curl_close($curl);                                  // 关闭cURL
    return $output;
}



/**
 *   获取IP地址
 **/
function get_real_ip(){
    static $realip = null;
    if($realip!==null){
        return $realip;
    }

    if(getenv('REMOTE_ADDR')){
        $realip = getenv('REMOTE_ADDR');
    }elseif(getenv('HTTP_CLIENT_IP')){
        $realip = getenv('HTTP_CLIENT_IP');
    }elseif (getenv('HTTP_X_FOWFORD_FOR')) {
        $realip = getenv('HTTP_X_FORWARDED_FOR');
    }
    return $realip;
}



/**
 *使用反斜线，递归转义 字符串或数组
 * @param arr 待转义的数组
 * @return arr 被转义后的数组
 */
function  _addslashes($arr){
    foreach ($arr as $k => $v) {
        if(is_string($v)){
            $arr[$k]=addslashes($v);
        }elseif(is_array($v)){
            $arr[$k] = _addslashes($v);
        }
    }
    return $arr;
}


if (!function_exists('return_success_msg')) {
    function return_success_msg($msg,$data = []){
        $code = 1;
        $res = compact('code','msg','data');
        exit(json_encode($res));
    }
}

if (!function_exists('return_error_msg')) {
    function return_error_msg($msg,$data = []){
        $code = 0;
        $res = compact('code','msg','data');
        exit(json_encode($res));
    }
}

/**
 * [A 调用当前模块或其他模块的控制器]
 * @param [type] $ctrl_name [description]
 */
function controller($ctrl_name){
    if (strpos($ctrl_name, '/') === false) {
        $controller = "\\".strtolower(MODULE )."\\controller\\{$ctrl_name}";
    }else{
        $list = explode('/', $ctrl_name);
        $controller = "\\".strtolower($list[0])."\\controller\\".$list[1];
    }
    $controller = new $controller();
    return $controller;
}

/**
 * 实例化 还需完善
 * @param [type] $table [description]
 */
function model($classname = false, $class = false, $prKey=false){

    if ($class) {
        $cl = '\\'.MODULE .'\\Model\\'.$class;
    }else{
        $cl = '\\'.MODULE .'\\Model\\Common';
    }

    $class = new $cl();
    if ($classname) {
        $info = preg_split('/(?=[A-Z])/', $classname);
        $table = implode("_", $info);
        $table = strtolower($table);
        $table = trim($table,'_');

        $class->table = $table;

        $prKey = $prKey?:$info[count($info)-1].'_id';
        $class->prkey = strtolower($prKey);
    }

    return $class;
}

/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function config($name=null, $value=null,$default=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            if (is_null($value))
                return $_config[$name] ?? $default;
            $_config[$name] = $value;
        }else{
            // 二维数组设置和获取支持
            $name = explode('.', $name);
            $name[0]   =  strtolower($name[0]);
            if (is_null($value))
                return $_config[$name[0]][$name[1]] ?? $default;
            $_config[$name[0]][$name[1]] = $value;
        }
    }elseif (is_array($name)){
        // 批量设置
        $_config = array_merge($_config, array_change_key_case($name));
    }
    return true;
}

/**
 * [U U方法，路径生成]
 */
function U($url){
    $info = parse_url($url);
    $path = $info['path'];
    $uri = explode('/', $path);
    if (isset($info['query'])) {
        p(parse_str($info['query']));
    }
}