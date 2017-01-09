<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 2017/1/9
 * Time: 下午5:49
 */

$server = new \swoole_server("127.0.0.1",8088,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);

$server->on('connect', function ($serv, $fd){
	$serv->tick(1000, function() use ($serv, $fd) {
		$serv->send($fd, "这是一条定时消息\n");
	});

    //启动一个循环，定时向客户端发一个消息
});

$server->on('receive', function ($serv, $fd, $from_id, $data) {
	//根据收到的消息做出不同的响应
	switch($data)
	{
		case 1:
		{
			foreach($serv->connections as $tempFD) {
				# 注: $tempFD 是全体client, $fd 是当前client.
				$serv->send($tempFD,"client {$fd} say : 1 for apple\n");
			}
			break;
		}
		case 2:
		{
			$serv->send($fd,"2 for boy\n");
			break;
		}
		default:
		{
			$serv->send($fd,"Others is default\n");
		}
	}
});

$server->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$server -> start();