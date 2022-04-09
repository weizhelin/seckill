<?php


require("./common/function.php");

//创建Server对象，监听127.0.0.1：9501 端口
$server = new Swoole\Server('1270.0.0.1',9501);

//监听连接进入事件
$server->on("Connect",function($server,$fd){
	$time = time();
	echo "Client {$fd} connect at {$time}\n";
	//$fd 是一个数字型参数，为连

	//保存连接记录

	//返回信息
	$server->send($fd,"Welcome {$fd}\n");

});

$server->on("Receive",function($server,$fd,$reactor_id,$data){
	$time = time();
	$data = trim($data);
	echo "Receive {$data} from fd {$fd} reactor_id {$reactor_id} at {$time}\n";
	$server->send($fd,"Server Receive data from {$fd}:{$data}\n");

	if($fd == 2){
		$server->send(1,"{$fd}:{$data}\n");
	}

	if($fd == 1){
		$server->send(2,"{$fd}:{$data}\n");
	}
});

$server->on('Close',function($server,$fd){
	echo "Client {$fd} close ";
});

//启动服务器
$server->start();