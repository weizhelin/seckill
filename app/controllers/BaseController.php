<?php
namespace app\controllers;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BaseController
{

    protected $data = [];

    /**
     * @var \Twig\Environment
     */
    private $twig;

    //构造方法
    public function __construct()
    {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/views/');
        $this->twig   = new Environment($loader, [
            //'cache' => '/path/to/compilation_cache',
        ]);
    }

    /**
     * 分配变量给到模板
     * @param $var
     * @param null $value
     */
    public function assign($var, $value = null)
    {
        if (is_array($var)) {
            $this->data = array_merge($this->data, $var);
        } else {
            $this->data[$var] = $value;
        }
    }

    public function display($template)
    {
        try {
            echo $this->twig->render($template . ".html", $this->data);
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
    }


    //成功之后
    function success($url, $msg)
    {
        echo "<script>";
        echo "alert('{$msg}')";
        echo "location.href='{$url}'";
        echo "</script>";
    }

    //失败之后
    function error($url, $msg)
    {
        echo "<script>";
        echo "alert('error: {$msg}')";
        echo "location.href='{$url}'";
        echo "</script>";
    }

}