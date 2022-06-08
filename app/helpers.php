<?php

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
 * 功能：返回二维数组中某个键对应的键的集合
 * @param array list 二维数组
 * @param string key 键
 * @return array values 键值数组
 */
if (!function_exists('get_key_values')){
    function get_key_values($list = false , $key =  false): array
    {
        if(!$list || !$key){
            return array();
        }
        $values = array();
        foreach ($list as $k => $v) {
            $values[] = $v[$key];
        }
        $values = array_unique($values);
        return $values;
    }
}


/**
 * 功能：以指定键的值为键，重组二维数据
 * @param $datalist array 二维数组
 * @param $key string 键的名
 * @return array
 */
if (!function_exists('array_create_key')) {
    function array_create_key(array $datalist, string $key): array
    {
        $values = [];
        foreach ($datalist as $k => $v) {
            $values[$v[$key]] = $v;
        }
        return $values;
    }
}

/**
 * 功能：获取二维数组指定键的和
 * @param $list array 二维数组
 * @param $key string key 指定键
 * @param $where array 筛选条件
 * @return int
 */
if (!function_exists('get_key_sum')){
    function get_key_sum(array $list, string $key = '' , array $where = array()): int
    {
        if (!$list || !$key) {
            return 0;
        }
        $sum = 0;
        foreach ($list as $k => $v) {
            if ($where){
                $continue = 0;
                foreach ($where as $wk => $wv){
                    if (is_array($wv)){
                        switch ($wv[0]){
                            case "neq":
                                if ($v[$wk] == $wv[1]){
                                    $continue = 1;
                                }
                                break;
                            default:
                                break;
                        }
                        if ($continue == 1){
                            break;
                        }
                    }else{
                        if ($v[$wk] != $wv){
                            $continue = 1;
                            break;
                        }
                    }
                }
                if ($continue){
                    continue;
                }else{
                    $sum += $v[$key];
                }
            }else{
                $sum += $v[$key];
            }
        }
        return $sum;
    }
}

/**
 * 过滤满足条件的二维数组
 * @param array $list
 * @param $exp
 * @return array
 */
if (function_exists('array_value_filter')){
    function array_value_filter(array $list, $exp): array
    {
        if (!$list || !$exp){
            return [];
        }
        $res = [];
        foreach ($list as $key => $value){
            foreach ($exp as $e => $p){
                if ((is_numeric($p) || is_string($p)) && $value[$e] != $p){
                    unset($list[$key]);
                    break;
                }
                if (is_array($p)){
                    if ($p[0] == 'in' && !in_array($value[$e],$p[1])){
                        unset($list[$key]);
                        break;
                    }
                    if ($p[0] == 'exp'){
                        if ($p[1] == 'notempty'){
                            if (empty(trim($value[$e]))){
                                unset($list[$key]);
                                break;
                            }
                        }
                        if ($p[1] == 'empty'){
                            if (!empty(trim($value[$e]))){
                                unset($list[$key]);
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }
}


/**
 * [getExt 获取文件后缀]
 * @param string $str [文件名]
 * @param integer $with [是否带分隔符]
 * @param string $delimiter [分隔符]
 * @return string             [后缀名]
 */
if (function_exists('get_ext')){
    function get_ext(string $str, $with=1, $delimiter='.'): string
    {
        if ($with) {
            return strrchr($str, '.');
        }else{
            return substr(strrchr($str, $delimiter), 1);
        }
    }
}

/**
 * 获取页码数组
 * @param $total int 记录总条数
 * @param $curr int 当前页面
 * @param $perPage int 每页条数
 * @param $pageNum int 显示页码数
 * @return array
 */
if (!function_exists('get_page')){
    function get_page(int $total, int $curr, int $perPage, int $pageNum): array
    {
        $max = ceil($total/$perPage);                       //获取最大页码数
        $left = max(1,$curr-floor($pageNum/2));             //最左侧页码
        $right = min($max,$left+$pageNum);                  //最右侧页码
        $left = max($right-$pageNum,1);
        $page = array();
        for($i=$left;$i<=$right;$i++){
            $_GET['page']=$i;
            $page[$i] = http_build_query($_GET);
        }
        return $page;
    }
}

/**
 * 功能：对数组进行递归转码
 * @param mixed $data 目标数组
 * @param string $output 目标编码
 * @return array|false|string|string[]|null
 */
if(!function_exists('array_iconv')){
    function array_iconv($data, $output = 'utf-8')
    {
        $encode_arr = array('UTF-8', 'ASCII', 'GBK', 'GB2312', 'BIG5', 'JIS', 'eucjp-win', 'sjis-win', 'EUC-JP');
        $encoded = mb_detect_encoding($data, $encode_arr);
        if (!is_array($data)) {
            return mb_convert_encoding($data, $output, $encoded);
        } else {
            foreach ($data as $key => $val) {
                $key = array_iconv($key, $output);
                if (is_array($val)) {
                    $data[$key] = array_iconv($val, $output);
                } else {
                    $data[$key] = mb_convert_encoding($data, $output, $encoded);
                }
            }
            return $data;
        }
    }
}

/**
 * [gbk2utf8 递归转义gbk至utf8]
 * @param  [type] $data [description]
 * @return array|false|int|string [type]       [description]
 */
if (!function_exists('gbk2utf8')){
    function gbk2utf8($data){
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = gbk2utf8($value);
                }elseif (!is_numeric($value)) {
                    $data[$key] = iconv("gbk","utf-8",$value);
                }
            }
            return $data;
        }elseif (is_numeric($data)){
            return $data;
        }else{
            return iconv("gbk","utf-8",$data);
        }
    }
}

/**
 * [get_rand_str 获取随机字符串]
 * @param int $length [description]
 * @param int $withNum
 * @return false|string [type]          [description]
 */
if(!function_exists('get_rand_str')){
    function get_rand_str($length = 32 , $withNum = 0){
        $str  = "abcdefhijklmnopqrstuvwxyz";
        $str .= strtoupper($str);
        if ($withNum) {
            $str .= '0123456780';
        }
        $str = str_shuffle($str);
        return substr($str,0,$length);
    }
}

if (!function_exists('scan_dir')){
    function scan_dir($path): array
    {
        $files = [];
        if (is_dir($path)) {
            $path = rtrim($path,'/');
            $subs = scandir($path);
            foreach ($subs as $key => $sub) {
                if (is_dir($path.'/'.$sub)) {
                    if ($sub != '.' && $sub != '..') {
                        $files[$sub] = scan_dir($path.'/'.$sub);
                    }
                }else{
                    $files[] = $sub;
                }
            }
        }
        return $files;
    }
}


/**
 * [list_sort_by 二维数组排序函数]
 * @param $list
 * @param $field
 * @param string $sortBy [排序方式]
 * @return array|false [type]         [description]
 */
function list_sort_by($list, $field, $sortBy = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }
        switch ($sortBy) {
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

if(!function_exists('config')){
    function config($file): array
    {
        $configFile = ROOT_PATH . DIRECTORY_SEPARATOR .'config' .DIRECTORY_SEPARATOR .$file . '.php';
        if (file_exists($configFile)){
            try {
                $config = include($configFile);
                if ($config && is_array($config)){
                    return $config;
                }
                return [];
            }catch (\Exception $e){
                return [];
            }
        }else{
            return [];
        }
    }
}

//获取当前URL方法：
function getCurrentUrl(): string
{
    $url = 'http://';
    if (isset($_SERVER['SERVER_HTTPS']) &&  $_SERVER['SERVER_HTTPS'] == 'on'){
        $url = 'https://';
    }
    //判断端口
    if($_SERVER['SERVER_PORT'] != 80){
        $url .=$_SERVER['SERVER_name'].':'.$_SERVER['SERVER_PORT'];
    } else{
        $url .=$_SERVER['SERVER_name'];
    }
    return $url;
}

function is_id_card($id): bool
{
    $id = strtoupper($id);

    //判断身份证号格式
    $pattern = '/^[\d]{17}[\dxX]$/';
    if (!preg_match($pattern,$id)){
        return false;
    }

    $provinceCodes = array(
        11,12,13,14,15,
        21,22,23,
        31,32,33,34,35,36,37,
        41,42,43,44,45,46,
        50,51,52,53,54,
        61,62,63,64,65,
        81,82,83
    );

    //判断首两位是否在省份代码中
    $first2 = substr($id,0,2);
    if (!in_array($first2,$provinceCodes)){
        return false;
    }

    //将前面的身份证号码17位数分别乘以不同的系数，将这17位数字和系数相乘的结果相加。
    $coefficients = array(
        7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2
    );

    //拆分成数组
    $codes = array();
    $len = mb_strlen($id, "UTF-8");
    for ($i = 0; $i < $len; $i +=1) {
        $codes[] = mb_substr($id, $i, 1, "UTF-8");
    }

    //求和
    $sum = 0;
    for ($j = 0;$j<17;$j++ ){
        $sum += $coefficients[$j] * $codes[$j];
    }

    //求余数
    $remainder = $sum % 11;

    //余数对应的校验码
    $checkCodes = array(
        1,0,'X',9,8,7,6,5,4,3,2
    );

    if ($checkCodes[$remainder] != $codes[17]){
        return false;
    }

    return true;
}



if(!function_exists('curlPostJ')){
    function curlPostJ($url,$params = array(),$type = 'default',$header = array(),$timeOut = 10){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);

        if($type == 'json'){
            $header = array_merge(array('Content-type:application/json;charset=UTF-8'),$header);
            $params = json_encode($params,JSON_UNESCAPED_UNICODE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }else{
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $params = http_build_query($params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $data = curl_exec($ch);

        if (curl_errno($ch)) {//出错则显示错误信息
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 0) {
                return '发送超时';
            }
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 100) {
                return '错误响应100';
            }
            return print_r(curl_getinfo($ch, CURLINFO_HTTP_CODE), true).'错误信息：'.print_r(curl_error($ch), true);
        }
        curl_close($ch);
        return $data;
    }
}


if (!function_exists('env')) {
    /**
     * 获取环境变量值
     * @access public
     * @param string $name    环境变量名（支持二级 .号分割）
     * @param string $default 默认值
     * @return mixed
     */
    function env(string $name = null, $default = null)
    {
        return Env::get($name, $default);
    }
}
