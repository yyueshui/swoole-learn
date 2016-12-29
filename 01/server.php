<?php
/**
 * Created by PhpStorm.
 * User: yuanyueshui
 * Date: 2016/12/28
 * Time: 下午10:00
 */

class server
{
	private $server;

	public function __construct()
	{
		$this->server = new swoole_server('0.0.0.0', 9501);
		$this->server->set(array(
			'worker_num' => 8,
			'daemonize' => false,
		));

		$this->server->on('Start', array($this, 'onStart'));
		$this->server->on('Connect', array($this, 'onConnect'));
		$this->server->on('Receive', array($this, 'onReceive'));
		$this->server->on('Close', array($this, 'onClose'));

		$this->server->start();
	}

	public function onStart($server)
	{
		echo "start \n";
	}

	public function onConnect($server, $fd, $fromId)
	{
		$server->send($fd, "hello {$fd}");
	}

	public function onReceive(swoole_server $server, $fd, $fromId, $data)
	{
		echo "Get Message From Client {$fd}: {$data} \n";
		$server->send($fd, $data);
	}

	public function onClose(swoole_server $server, $fd, $fromId)
	{
		echo "Client {$fd} Close Connection\n";
	}
}

$server = new Server();