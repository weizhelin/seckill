<?php
namespace master\controller;
class Index extends Common
{
    public function index(){
        header("location:/index.php?c=laonanren.reward");
    }
}