## 此文件夹内的是可以批量查询whois信息的

当输入单个域名时候，和外面的单个查询是相同的。当输入的是多个域名时候，则会变成按行列出。
列出的内容会与单个查询结果有所不同。其中的原始whois内容在多域名查询时候会默认不返回（防止大量数据导致返回卡顿），
如果需要，可以在whois.php中最底下，把那个代码改一下即可。

将就使用吧，前端排版和拿数据拆分真的好难！！！我一个小菜鸟，玩不来。又不是不能用，嘿嘿！

有能力的大佬们，也可以在此基础上创作美化。欢迎各位提交代码！

#### PHP版本>= 7.4

### whois.php返回的json格式，可以当api使用。自己写前端页面。把data.json和whois.php放到服务器，就可以api调用！！！

批量查询时候，默认不返回whois查询信息的原始数据，因为数据量太大。如果需要返回，自行在whois.php文件底部修改一下，已经备注位置修改方法

``` json
[
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
"whois":"whois原始信息，批量查询时候，默认不返回数据" 
},
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
"whois":"whois原始信息，批量查询时候，默认不返回数据"
}
]
```

### 单个查询图
![](https://cdnjson.com/images/2023/07/18/whois.png)

### 批量查询
![](https://github.com/GitHubPangHu/image/raw/main/plwhois.png)

