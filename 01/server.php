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
			'task_worker_num' => 3,
			'daemonize' => false,
		));
		//监听多个端口
		$this->server->addlistener('0.0.0.0', 9502, SWOOLE_TCP);

		$this->server->on('Start', array($this, 'onStart'));
		$this->server->on('Connect', array($this, 'onConnect'));
		$this->server->on('Receive', array($this, 'onReceive'));
		$this->server->on('Close', array($this, 'onClose'));
		//设置了task_worker_num 以下两个是必须的
		$this->server->on('Task', array($this, 'onTask'));
		$this->server->on('Finish', array($this, 'onFinish'));

		$this->server->start();
	}

	public function onStart($server)
	{
		echo "start \n";
	}

	public function onConnect(swoole_server $server, $fd, $fromId)
	{
		$clientInfo = $server->connection_info($fd, $fromId);

		if($clientInfo['server_port'] == 9502) {
			$server->send($fd, "hello 9502 {$fd}");
		} else {
			$server->send($fd, "hello 9501 {$fd}");
		}
	}

	public function onReceive(swoole_server $server, $fd, $fromId, $data)
	{
		//多端口区别
		$clientInfo = $server->connection_info($fd, $fromId);
		if($clientInfo['server_port'] == 9502) {
			$server->send($fd, "hello 9502 {$fd}");
		} else {
			$server->send($fd, "hello 9501 {$fd}");
		}

		//task
		//$task_id = $server->task($data, 0);
		//$server->send($fd, "Dispath AsyncTask: id=$task_id\n");


		//echo "Get Message From Client {$fd}: {$data} \n";
		//$server->send($fd, $data);
	}

	public function onClose(swoole_server $server, $fd, $fromId)
	{
		echo "Client {$fd} Close Connection\n";
	}

	public function onTask(swoole_server $server, $df, $fromId)
	{
		//$server->task("taskcallback", -1, function (swoole_server $server, $task_id, $data) {
			echo "Task Callback: ";
		//});
	}

	public function onFinish(swoole_server $server, $df, $fromId)
	{
		//$server->finish("taskcallback", -1, function (swoole_server $server, $task_id, $data) {
			echo "Task Finish: ";
		//});
	}
}

$server = new Server();