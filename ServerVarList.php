<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 2017/1/6
 * Time: 下午3:59
 */
class Server
{
	private $serv;
	public function __construct() {
		$this->serv = new swoole_server("0.0.0.0", 9501);
		$this->serv->set(array(
			'worker_num' => 8,
			'daemonize' => false,
			'max_request' => 10000,
			'dispatch_mode' => 2,
			'debug_mode'=> 1
		));
		$this->serv->on('Start', array($this, 'onStart'));
		$this->serv->on('Connect', array($this, 'onConnect'));
		$this->serv->on('Receive', array($this, 'onReceive'));
		$this->serv->on('Close', array($this, 'onClose'));
		$this->serv->start();
	}
	public function onStart( $serv ) {
		var_dump($serv->setting);
		var_dump($serv->master_pid);
		var_dump($serv->manager_pid);


		echo "Start\n";
	}
	public function onConnect( $serv, $fd, $from_id ) {
		$serv->send( $fd, "Hello {$fd}!" );
	}
	public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
		echo "Get Message From Client {$fd}:{$data}\n";
	}
	public function onClose( $serv, $fd, $from_id ) {
		var_dump($serv->worker_id);
		var_dump($serv->worker_pid);
		var_dump($serv->stats());
		echo "Client {$fd} close connection\n";
	}
}
$server = new Server();
