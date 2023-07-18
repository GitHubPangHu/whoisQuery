# whoisQuery

whois查询，可以查询任意后缀域名。

这个是单个域名查询，如需多域名查询请下载BatchQuery文件夹，BatchQuery文件夹内的是批量查询代码。

Whois query, can query any suffix domain name.

This is a single domain name query. If you need multiple domain name queries, please download the BatchQuery folder, which contains the batch query code.

使用方法：

下载代码，运行index.html页面，输入域名查询即可，批量查询进入BatchQuery文件夹内查看。

请求改成ajax方法。可以前后端分离开。

php的代码运行方法很简单，使用小皮，宝塔等类似集成环境即可使用。

注意：

需要开启intl、curl扩展。

#### PHP版本>= 7.4

data.json文件中的几个对象代表的意思，如果查询时候没有显示信息，则是data.json文件中没有那个字符串，自己对应加上即可。或者提交给我域名后缀！
``` 
domain                   域名
domainCode               域名代码
CreationDate             创建日期
ExpiryDate               到期时间
UpdatedDate              更新时间
SponsoringRegistrar      注册商
RegistrarURL             服务商网址
Registrant               注册人
DomainStatus             域名状态
DNS                      dns服务器
DNSSEC
RegistrantContactEmail   注册人邮箱
unregistered             未注册
```

### whois.php文件返回类型，可以当api使用。自己写前端页面。把data.json和whois.php放到服务器，就可以api调用！！！

``` json
// 返回是404的就是没查到。看原始whois信息。
{
"main":{
"domain":"查询的域名",
"domainCode":"IDN域名的code编码。非IDN域名返回和域名一样",
"CreationDate":"创建时间",
"ExpiryDate":"到期时间",
"UpdatedDate":"更新日期",
"SponsoringRegistrar":"注册商",
"RegistrarURL":"注册商网址",
"Registrant":"注册人",
"DomainStatus":"域名状态",
"DNS":"dns服务器",
"DNSSEC":"unsigned",
"RegistrantContactEmail":"注册人邮箱号",
"unregistered":"如果404则已经注册，未注册返回的是'未注册'三个字"
},
"result":"状态，200正常，其他都是直接把对应错误返回，是字符串",
"whois":"whois原始信息"
}
```



更新：2023-7-18 胖虎

单个查询图
![](https://cdnjson.com/images/2023/07/18/whois.png)
