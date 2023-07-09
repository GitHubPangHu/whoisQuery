<?php
class whois{
//获取whois地址页面
    function getWhoisAddress($suffix){
        $curl = curl_init();
        $url = "https://www.iana.org/whois?q=".$suffix;
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //请求头设置
        $userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3";
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        return $data;
    }
    //截取whois地址
    function intercept($original_string){
        $start_character = "whois:";
        $end_character = "status";
        $start_pos = strpos($original_string, $start_character);
        $end_pos = strpos($original_string, $end_character, $start_pos + 1);
        $result = substr($original_string, $start_pos + 6, $end_pos - $start_pos - 6);
        //去除前后空格等符号
        $result = trim($result);
        return $result;
    }
    //getDomainWhois 获取域名的whois
    function getDomainWhois($result,$domain){
        $fp = fsockopen($result, 43, $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            $out = $domain."\r\n";
            fwrite($fp, $out);
            $whoisInformation[]="";
            while (!feof($fp)) {
                echo "<pre>";
                 $whoisInformation[] .= fgets($fp);
            }

            fclose($fp);
            return $whoisInformation;
        }

    }

    function test($domain){
       $suffix = ltrim(strstr( $domain, '.'),".");

        $data = $this->getWhoisAddress($suffix);
        $result = $this->intercept($data);
        $whois = $this->getDomainWhois($result,$domain);
        return $whois;
    }
}
