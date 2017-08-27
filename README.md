# shared
shared code




##Docker是什么?
应用容器

## 阿里云服务器
本文主要说明手动安装docker  


> Docker要求64位的系统且内核版本至少为3.10  

- 添加源。  
```
yum install epel-release –y 
yum clean all
yum list
```

- 安装并运行Docker。
```
yum install docker-io –y
systemctl start docker
```


## docker基本用法
下载基础镜像
```
docker pull registry.cn-hangzhou.aliyuncs.com/lxepoo/apache-php5
```

- 查看已有镜像。
```
Docker ps 
主要有些参数要说一下 
1. 不加参数，表示查看当前正在运行的容器 
2. -a，查看所有容器包括停止状态的容器 
3. -l，查看最新创建的容器 
4. -n=x，查看最后创建的x个容器 
```


##有问题反馈
防火墙问题

* 邮件(dev.hubo#gmail.com, 把#换成@)
* QQ: 287759234
* weibo: [@草依山](http://weibo.com/ihubo)
* twitter: [@ihubo](http://twitter.com/ihubo)

重启防火墙
> systemctl restart firewalld.service

查看端口
> firewall-cmd --query-port=80/tcp

开启端口
> firewall-cmd --add-port=80/tcp


* [阿里云 ESC 上搭建Docker](http://mouapp.com/) 
* [ace](http://ace.ajax.org/)
* [jquery](http://jquery.com)

##关于作者
```javascript

```
