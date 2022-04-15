<?php


namespace app\controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigTest;

//https://twig.symfony.com/doc/2.x/templates.html#synopsis
class Operation
{

    public function index(){
        //使用文档：twig\twig\doc\api.rst
        $path = ROOT_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'views';

        $loader = new FilesystemLoader($path);
        $twig = new \Twig\Environment($loader);
        try {
            $template = $twig->load('Hello.tpl');
            $template->display(array('name' => 'Fabien'));
        }catch (\Exception $e){
            throw $e;
        }
    }

}