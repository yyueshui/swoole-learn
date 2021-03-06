<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 2017/1/10
 * Time: 下午6:07
 */

$server = new \swoole_server("127.0.0.1",8088,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);

$server->on('connect', function ($serv, $fd){ });

$server->on('receive', function ($serv, $fd, $from_id, $data){ });

$server->on('close', function ($serv, $fd){ });
//$server->set([
	//"daemonize"=>true,
	//"reactor_num"=>2,
	//"worker_num"=>4,
//]);

$server->BaseProcess = 'I am base process.';
$server->ManagerToWorker = '';
$server->MasterToManager = '';

//三种进程的OnStart方法被回调的时候都有一定的延迟，底层事实上已经完工了fork的行为，才回调的，因此，默认启动的时候，我们在OnMasterStart、OnManagerStart中写入的数据并不能按预期被fork到Manager进程或者Worker进程。
//测试方法，kill -10 masterPid  重启worker，  kill -15 masterPid  终止master进程
// 为了便于阅读，以下回调方法按照被起调的顺序组织
// 1. 首先启动Master进程
$server->on('start', function(swoole_server $server){
	echo 'On master start'. PHP_EOL;
	//先打印在交互进程写入的数据
	echo "server->BaseProcess = ". $server->BaseProcess.PHP_EOL;
	// 修改交互进程中写入的数据
	$server->BaseProcess = 'I am changed by master.';
	$server->MasterToManager = 'Hello manager, I am master';

});

// 2. Master进程拉起Manager进程
$server->on('ManagerStart', function(swoole_server $server){
	echo "on manager start".PHP_EOL;
	// 打印，然后修改交互进程中写入的数据
	echo 'server->BaseProcess = '.$server->BaseProcess.PHP_EOL;
	$server->BaseProcess = "I'm changed by manager.";
	// 打印，然后修改在Master进程中写入的数据
	echo "server->MasterToManager = ".$server->MasterToManager.PHP_EOL;
	$server->MasterToManager = "This value has changed in manager.";
	// 写入传递给Worker进程的数据
	$server->ManagerToWorker = "Hello worker, I'm manager.";
});

// 3. Manager进程拉起Worker进程
$server->on('WorkerStart', function (\swoole_server $server, $worker_id){
	echo "Worker start".PHP_EOL;
	// 打印在交互进程写入，然后在Master进程，又在Manager进程被修改的数据
	echo "server->BaseProcess = ".$server->BaseProcess.PHP_EOL;

	// 打印，并修改Master写入给Manager的数据
	echo "server->MasterToManager = ".$server->MasterToManager.PHP_EOL;
	$server->MasterToManager = "This value has changed in worker.";

	// 打印，并修改Manager传递给Worker进程的数据
	echo "server->ManagerToWorker = ".$server->ManagerToWorker.PHP_EOL;
	$server->ManagerToWorker = "This value is changed in worker.";
});

// 4. 正常结束Server的时候，首先结束Worker进程
$server->on('WorkerStop', function(\swoole_server $server, $worker_id){
	echo "Worker stop".PHP_EOL;
	// 分别打印之前的数据
	echo "server->ManagerToWorker = ".$server->ManagerToWorker.PHP_EOL;
	echo "server->MasterToManager = ".$server->MasterToManager.PHP_EOL;
	echo "server->BaseProcess = ".$server->BaseProcess.PHP_EOL;
});

// 5. 紧接着结束Manager进程
$server->on('ManagerStop', function (\swoole_server $server){
	echo "Manager stop.".PHP_EOL;
	// 分别打印之前的数据
	echo "server->ManagerToWorker = ".$server->ManagerToWorker.PHP_EOL;
	echo "server->MasterToManager = ".$server->MasterToManager.PHP_EOL;
	echo "server->BaseProcess = ".$server->BaseProcess.PHP_EOL;
});
// 6. 最后回收Master进程
$server->on('shutdown', function (\swoole_server $server){
	echo "Master shutdown.".PHP_EOL;
	// 分别打印之前的数据
	echo "server->ManagerToWorker = ".$server->ManagerToWorker.PHP_EOL;
	echo "server->MasterToManager = ".$server->MasterToManager.PHP_EOL;
	echo "server->BaseProcess = ".$server->BaseProcess.PHP_EOL;
});

$server -> start();