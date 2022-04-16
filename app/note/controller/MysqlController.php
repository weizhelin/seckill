<?php

namespace app\note\controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class MysqlController extends BaseController
{

    protected function getViewPath(): string
    {
        $controllerPath = str_replace(ROOT_PATH,'',__DIR__);
        $class = str_replace('\\',DIRECTORY_SEPARATOR,__CLASS__);
        $className = str_replace($controllerPath,'',$class);
        $className = ltrim($className,DIRECTORY_SEPARATOR);
        $className = str_ireplace('controller','',$className);
        $viewPath = str_replace('controller','view',$controllerPath);
        $viewPath .= DIRECTORY_SEPARATOR . strtolower($className);
        return $viewPath;
    }

    protected function getTwig(): Environment
    {
        $viewPath = $this->getViewPath();
        $loader = new FilesystemLoader(ROOT_PATH.$viewPath);
        return new Environment($loader);
    }

    public function index(){
        try {
            $twig = $this->getTwig();
            $template = $twig->load('index.tpl');
            $template->display(array('title' => 'Mysql学习笔记'));
        }catch (\Exception $e){
            throw $e;
        }

    }
}