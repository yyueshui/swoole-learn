<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 2017/1/6
 * Time: 下午3:42
 */
//获取错误
//echo swoole_strerror();
//echo "\r\n";
//echo swoole_errno();
//echo "\r\n";
//echo swoole_strerror(swoole_errno());

//获取操作系统用户信息
$user = posix_getpwnam('vagrant');
var_dump($user);