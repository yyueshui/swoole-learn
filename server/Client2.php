<?php
/**
 * Created by PhpStorm.
 * Date: 2016/12/28
 * Time: 下午10:41
 */

class client2
{
	private $client;

	public function __construct()
	{
		$this->client = new swoole_client(SWOOLE_SOCK_TCP);
	}

	public function connect()
	{
		if(!$this->client->connect('127.0.0.1', 9502, 1)) {
			echo "Error: {$this->client->errCode}, {$this->client->errMsg}\n";
		}

		fwrite(STDOUT, '请输入消息：');
		$msg = trim(fgets(STDIN));
		$this->client->send($msg);

		$message = $this->client->recv();

		echo "Get Message From Server:{$message}\n";
	}
}

$client = new client2();
$client->connect();