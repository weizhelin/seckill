<?php

require("./common/function.php");

$server = new Swoole\Server("127.0.0.1",9502,SWOOLE_PROCESS,SWOOLE_SOCK_UDP);

$server->on('Packet',function($server,$data,$clientInfo){
	p($clientInfo);
	$server->sendTo($clientInfo['address'],$clientInfo['port'],"Server:{$data}");
});

$server->start();