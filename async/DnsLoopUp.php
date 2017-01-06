<?php
/**
 * Created by PhpStorm.
 * User: yuanyueshui
 * Date: 2017/1/4
 * Time: 下午3:28
 */

swoole_async_set(array(
	//'disable_dns_cache' => true,
	'dns_lookup_random' => true,
));
swoole_async_dns_lookup("www.sina.com.cn", function ($host, $ip)
{
	echo "{$host} reslove to {$ip}\n";
	swoole_async_dns_lookup("www.sina.com.cn", function ($host, $ip)
	{
		echo "{$host} reslove to {$ip}\n";
	});
});