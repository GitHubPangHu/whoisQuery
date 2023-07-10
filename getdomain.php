<?php
require 'whois.php';

// 允许所有源发起的跨域请求
//如果自己使用，最好设置好允许的请求域名，防止盗用
header('Access-Control-Allow-Origin: *');
// 允许特定的 HTTP 方法
header('Access-Control-Allow-Methods: POST');

$domain = $_POST['domain'];
$whoisQuery = new Whois();
$whois = $whoisQuery->query($domain);
echo json_encode($whois);
