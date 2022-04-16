<?php


namespace app\note\controller;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BaseController
{
    private $twig;
    private $data=[];

    function assign($var,$value=null){
        if(is_array($var)){
            $this->data=array_merge($this->data,$var);  //如是数组 就组合数组格式
        } else{
            $this->data[$var]=$value;  //如果不是数组 就生成一个数组 其中var是建 value是值
        }
    }

    function display($template){
        echo  $this->twig->render($template.'.html',$this->data);
    }

}