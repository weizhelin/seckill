<?php

require("./common/function.php");

$http = new Swoole\Http\Server("0.0.0.0",9503);

$http->on("Request",function($request,$response){
	var_dump($request->server);
	if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->end();
        return;
    }
	$rand = rand(1000,9999);	
	$response->header("Content-Type","text/html;charset=UTF-8");

	$content = file_get_contents('http.html');
	$response->end($content);
	//$response->end("<h1>Hello Swoole：{$rand}</h1>");
});

$http->start();