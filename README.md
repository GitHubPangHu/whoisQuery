# whoisQuery

whois查询，可以查询任意后缀域名。

使用方法：

下载代码，运行index.html页面，输入域名查询即可，临时写的，没有美化，没有进行把数据拆开显示，也没写批量查询等等。

请求改成ajax方法。可以前后端分离开。

php的代码运行方法很简单。

注意：

需要开启intl、curl扩展。

data.json文件中的几个对象代表的意思，如果查询时候没有显示信息，则是data.json文件中没有那个字符串，自己对应加上即可。或者提交给我域名后缀！

domain   							域名

domainCode  					域名代码

CreationDate 					创建日期

ExpiryDate						到期时间

UpdatedDate					更新时间

SponsoringRegistrar		注册商

RegistrarURL 					服务商网址

Registrant   						注册人

DomainStatus					域名状态

DNS 								dns服务器

DNSSEC

RegistrantContactEmail   注册人邮箱

unregistered  					未注册

2023-7-17 胖虎
