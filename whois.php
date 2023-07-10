<?php
class Whois{
    /**
     * @param String $suffix 域名后缀
     * @return bool|string 返回注册局信息
     * 通过域名后缀找到对应注册局的信息
     */
    private function getWhoisAddress(String $suffix){
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

    /**
     * @param string $original_string 注册局信息
     * @return string 返回whois查询地址
     * 从注册局信息中截取whois查询地址，并返回
     */
    private function intercept(string $original_string){
        //判断是否是没有的后缀
        $noWhois = "This query returned 0 objects.";
        $bool = strpos($original_string,$noWhois);
        if ($bool !== false){
            echo "暂不支持此后缀或者无此后缀域名！";
            die();
        }

        $start_character = "whois:";
        $end_character = "status";
        $start_pos = strpos($original_string, $start_character);
        $end_pos = strpos($original_string, $end_character, $start_pos + 1);
        $result = substr($original_string, $start_pos + 6, $end_pos - $start_pos - 6);
        //去除前后空格等符号
        $result = trim($result);
        return $result;
    }

    /**
     * @param string $whoisServiceAddress whois服务器地址
     * @param string $domain 要查询的域名
     * @return array|void whois查询到的信息
     */
    private function getDomainWhois(string $whoisServiceAddress,string $domain){
        $fp = fsockopen($whoisServiceAddress, 43, $errno, $errstr, 30);
        if (!$fp) {
            echo $whoisServiceAddress."<br />\n";
            echo $domain;
            echo "<br /> \n";
            echo "$errstr ($errno)<br />\n";
            die();
        } else {
            $domain = idn_to_ascii($domain);// 将域名转换为 IDNA ASCII 格式
            $out = $domain."\r\n";
            fwrite($fp, $out);
            $whoisInformation[] ="";// array();
            while (!feof($fp)) {
                 $whoisInformation[] .= fgets($fp);
            }
            fclose($fp);

            //判断是否是IDN域名，如果是的则在第一个数组处加上他的IDN域名
            $bool = strpos($domain,"xn--");
            if ($bool !== false){
                $whoisInformation[0] = "IDN域名：".idn_to_utf8($domain);
            }
            return $whoisInformation;
        }

    }

    public function query($domain){
       $suffix = ltrim(strstr( $domain, '.'),".");
        $data = $this->getWhoisAddress($suffix);
        $whoisServiceAddress = $this->intercept($data);
        $whois = $this->getDomainWhois($whoisServiceAddress,$domain);
        return $whois;
    }
}
