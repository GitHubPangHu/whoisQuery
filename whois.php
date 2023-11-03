<?php
// 允许所有源发起的跨域请求
//如果自己使用，最好设置好允许的请求域名，防止盗用
header('Access-Control-Allow-Origin: *');
// 允许特定的 HTTP 方法
header('Access-Control-Allow-Methods: POST');

////////////测试代码//////////////////////
//$whoisQuery = new Whois();
//echo "<pre>";
//$whoisQuery->splitWhois([]);die();
///////////////////////////////


$domain = $_POST['domain'];
if (empty($domain)) {
    echo json_encode(['result' => "输入值不正确"]);
    die();
}

//代码实现功能，判断域名是否有http或者https前缀（即网址），没有加上，再取域名。有就直接取域名
if (!preg_match("~^(?:f|ht)tps?://~i", $domain)) {
    $domain = "http://" . $domain;
}

$parsedUrl = parse_url($domain);

if ($parsedUrl && isset($parsedUrl['host'])) {
    $domain = $parsedUrl['host'];

    $whoisQuery = new Whois();
    $whois = $whoisQuery->query($domain);
    echo $whois;

} else {
    echo "无效的域名";
    die();
}

class Whois
{
    private string $domain;
    private array $main;


    /**
     * @param String $suffix 域名后缀
     * @return bool|string 返回注册局信息
     * 通过域名后缀找到对应注册局的信息
     */
    private function getWhoisAddress(string $suffix)
    {
        $curl = curl_init();
        $url = "https://www.iana.org/whois?q=" . $suffix;
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
    private function intercept(string $original_string): string
    {
        //判断是否是没有的后缀
        $noWhois = "This query returned 0 objects.";
        $bool = strpos($original_string, $noWhois);
        if ($bool !== false) {
            echo json_encode(['result' => "暂不支持此后缀或者无此后缀域名！"]);
            die();
        }

        $start_character = "whois:";
        $end_character = "status";

        $start_pos = strpos($original_string, $start_character);
        $end_pos = strpos($original_string, $end_character, $start_pos + 1);
        $result = substr($original_string, $start_pos + 6, $end_pos - $start_pos - 6);
        //去除前后空格等符号
        return trim($result);
    }

    /**
     * @param string $whoisServiceAddress whois服务器地址
     * @param string $domain 要查询的域名
     * @return string|void whois查询到的信息
     */
    private function getDomainWhois(string $whoisServiceAddress, string $domain)
    {
        $fp = @fsockopen($whoisServiceAddress, 43, $errno, $errstr, 3);
        if (!$fp) {
            echo json_encode(['result' => "$errstr"]);
            die();
        } else {
            $domain = idn_to_ascii($domain);// 将域名转换为 IDNA ASCII 格式
            $out = $domain . "\r\n";
            fwrite($fp, $out);
            $whoisInformation = "";
            while (!feof($fp)) {
                $whoisInformation .= fgets($fp) . "<br>";
            }
            fclose($fp);

            $this->splitWhois($whoisInformation);
            return str_replace(' ', '', $whoisInformation);//返回的原始信息
        }
    }

    /**
     * @param string $whoisInformation whois的信息文本
     * @return void
     * 把whois的结果进行拆分，拿出几个重要点
     */
    private function splitWhois(string $whoisInformation)
    {
        //        此方法获取的结果需要赋值给属性
        $main = array('domain' => $this->domain, 'domainCode' => idn_to_ascii($this->domain));

        $data = json_decode(file_get_contents('./data.json'), true);

        foreach ($data["unregistered"] as $keyword) {
            if (strpos($whoisInformation, $keyword) !== false) {
                echo json_encode(["unregistered" => "未注册"]);
                die();
            }
        }


        foreach ($data as $key => $value) {
            $pattern = implode('|', $value);
            $Regular = '/(?:'.$pattern.'):(.*?)(?:\n|$)/i';
            // 执行匹配
            if (preg_match_all($Regular, $whoisInformation, $matches)) {
                //只有dns和域名状态会是多条，需要循环，其他的只要拿第一条即可
                if ($key == "DomainStatus" || $key == "DNS") {
                    $a = array();
                    foreach ($matches[1] as $values) {
                        $parts = explode(" ", strtolower(trim($values)));//为了把状态的后半部分去掉，保留状态信息
                        $a[] = $parts[0];
                    }
                    $rowData = implode('<br>', array_unique($a));
                } else {
                    $rowData = trim($matches[1][0]);
                }
                $rowData = $this->utc($rowData);//判断是否是日期

                $main[$key] = $rowData;//$this->convertToShanghaiTime($rowData);
            } else {
                $main[$key] = "404";//未找到的字段返回404
            }
        }
        $this->main = $main;
    }

    /**
     * @param $rowData //传递whois结果的行数据，如果是时间，则转换成正常国内utc8日期。
     * @return mixed|string 日期类型返回转换过的国内上海标准时间，其他类型原样返回
     */
    private function utc($rowData)
    {

        $inputFormat = 'Y-m-d\TH:i:s\Z';
        $inputFormatTwo = 'Y-m-d\TH:i:s.u\Z';
        $outputFormat = 'Y-m-d H:i:s';
        $timezoneOffset = '+08:00';
        $date = DateTime::createFromFormat($inputFormat, $rowData);
        $dateTwo = DateTime::createFromFormat($inputFormatTwo, $rowData);
        if ($date !== false) {
            $date->setTimezone(new DateTimeZone($timezoneOffset));
            // 加上8个小时
            $date->modify('+8 hours');
            $formattedDate = $date->format($outputFormat);
            $rowData = $formattedDate . "&nbsp;&nbsp;UTC+8";
        } else if ($dateTwo !== false) {
            $dateTwo->setTimezone(new DateTimeZone($timezoneOffset));
            // 加上8个小时
            $dateTwo->modify('+8 hours');
            $formattedDate = $dateTwo->format($outputFormat);
            $rowData = $formattedDate . "&nbsp;&nbsp;UTC+8";
        }
        return $rowData;
    }

    public function query($domain)
    {
        $this->domain = $domain;
        $suffix = ltrim(strstr($domain, '.'), ".");//获取域名后缀
        $data = $this->getWhoisAddress($suffix);
        $whoisServiceAddress = $this->intercept($data);
        $whois = $this->getDomainWhois($whoisServiceAddress, $domain);
        return json_encode(['main' => $this->main, 'result' => "200", 'whois' => $whois]);
    }
}
