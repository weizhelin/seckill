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
 * 功能：生成页码列表
 * @param string $preUrl url前缀
 * @param int $total 总条数
 * @param int $perPage 每页显示条数
 * @param int $curr 当前页
 * @param int $pageNum 显示页码数
 * @return string
 */
if(!function_exists('create_page_list')){
    function create_page_list(string $preUrl, int $total, int $perPage, int $curr, int $pageNum): string
    {
        $max = ceil($total/$perPage);                   //最大页码
        $left = max(1,$curr-floor($pageNum/2));
        $right = min($max,$left+$pageNum);
        $left = max($right-$pageNum,1);
        $pages = array();
        for($i=$left;$i<=$right;$i++){
            $pages[$i] = $i;
        }
        $pageList = '';
        if ($max>1) {
            if ($curr>1) {
                $pageList .= '<a href="'.$preUrl.'" style="display:inline-block;margin-right:8px;margin-right:8px;">首页</a> ';
                $pageList .= '<a href="'.$preUrl.'&pageno='.($curr-1).'" style="display:inline-block;margin-left:8px;margin-right:8px;">上一页</a>';
            }else{
                $pageList .= '<span class="current" style="display:inline-block;margin-left:8px;margin-right:8px;">首页</span>';
                $pageList .= '<span class="current" style="display:inline-block;margin-left:8px;margin-right:8px;">上一页</span>';
            }
            $pageList .= '<span class="hiddenonoff">';
            foreach ($pages as $key => $page) {
                if ($curr==$key){
                    $pageList .= '</span>';
                    $pageList .= '<span class="current" style="display:inline-block;margin-left:8px;margin-right:8px;">'.$key.'</span>';
                    $pageList .= '<span class="hiddenonoff">';
                }else{
                    $pageList .='<a href="'.$preUrl.'&pageno='.$page.'" style="display:inline-block;margin-left:8px;margin-right:8px;">'.$key.'</a>';
                }
            }
            $pageList .= '</span>';

            if ($curr!=$max) {
                $pageList .= '<a href="'.$preUrl.'&pageno='.($curr+1).'" style="display:inline-block;margin-left:8px;margin-right:8px;">下一页</a>';
                $pageList .='<a href="'.$preUrl.'&pageno='.$max.'" style="display:inline-block;margin-left:8px;margin-right:8px;">尾页</a> ';
            }else{
                $pageList .='<span class="current" style="display:inline-block;margin-left:8px;margin-right:8px;">下一页</span>';
                $pageList .= '<span class="current" style="display:inline-block;margin-left:8px;margin-right:8px;">尾页</span>';
            }
        }
        return $pageList;
    }
}

/**
 * 功能：对数组进行递归转码
 * @param mixed $data 目标数组
 * @param string $output 目标编码
 * @return array|false|string|string[]|null
 */
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