<?php

require("./common/function.php");


$ws = new Swoole\WebSocket\Server("0.0.0.0",9505);

$ws->on("Open",function($ws,$request){
	echo $request->fd ." connected\n";
	$ws->push($request->fd,"Hello fd{$request->fd},welcome!\n");
});

$ws->on("Message",function($ws,$frame){
	echo "get message from fd {$frame->fd} with content {$frame->data}\n";
	$ws->push($frame->fd,"server:{$frame->data}\n");
});

$ws->on('Close',function($ws,$fd){
	echo "client {$fd} is closed\n";
});

$ws->start();