# aliyunddns
p阿里云解析api实现动态dns解析迷你php版
只用到获取记录列表和修改绑定的ip功能，也可以自己对应api文档修改参数实现其它功能

  可以没几个人用得到吧，我是路由刷的openwrt装的lnmp环境，上面弄了个博客，本来想去租云服务器的，好贵啊，就捣腾路由了呗，用得到的人就拿去用吧。本来看到有其它这种项目的，但是是用java或python的，弄不怎么懂，只会php，就写了个php版的，官方jdk太麻烦了。
  
简易使用说明:
1.脚本只需填入access key、access key secret、域名名称
2.crontab 添加计划任务调用php-cgi执行此脚本就行


  
