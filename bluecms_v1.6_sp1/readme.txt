/***************************
*                          *
* BlueCMS v1.6 安装相关说明  *
*                          *
***************************/

 author:lucks

     QQ: 515909747
     QQ: 378467397

 e-mail: lucks.yu@gmail.com

(一) 运行环境需求：
可用的 httpd 服务器（如 Apache、Zeus、IIS 等） 
PHP 4.3.0 及以上 
MySQL 4.1 及以上

(二) 安装步骤：

(1) Linux 或 Freebsd 服务器下安装方法。
    第一步：使用ftp工具中的二进制模式，将该软件包里的目录及其文件上传到您的空间，假设上传后目录仍旧为 mypic。
    第二步：先确认以下目录或文件属性为 (777) 可写模式。
        uploads/
		'data',
                'data/cache',
                'data/upload',
                'data/compile',
                'data/backup',
                'include'
		'install'

    第三步：运行 http://yourwebsite/安装目录/ 安装程序，填入安装相关信息与资料，完成安装！
    
(2) Windows 服务器下安装方法。
    第一步：使用ftp工具，将该软件包里的目录及其文件上传到您的空间。
    第二步：运行 http://yourwebsite/安装目录/ 安装程序，填入安装相关信息与资料，完成安装！
    
(3) 后台管理地址
	http://yourwebsite/安装目录/admin

(4) 安装完成,请删除install文件夹
	
(三) 版权说明
	你可以免费使用,传播本程式,也可以修改本程式,但拒绝在作者不知情的情况下以自己的名义发布修改后的版本.

(四) 相关帮助:
      v1.6演示地址: http://www.bluecms.net/demo
      官方网站：http://www.bluecms.net/
      官方论坛：htpp://www.bluecms.net/bbs