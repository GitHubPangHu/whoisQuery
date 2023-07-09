<?php
require 'whois.php';
$domain = $_POST['domain'];

$a = new whois();
$whois = $a->test($domain);
//得到的是一个素组，遍历出来，如果需要单个显示，则用$whois[i]方式单条显示
foreach ($whois as $a){
    echo $a;
}
