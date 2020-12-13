<?php
/**
 * BlueCMS v1.6 to v1.6 sp1 升级文件
 * 
 * $Author: lucks
 * $Id: upgrade.php
*/
define('IN_BLUE', TRUE);
require dirname(__FILE__) . '/include/common.inc.php';

if (file_exists(BLUE_ROOT . 'templates/default/css/common1.css'))    
{
	@unlink(BLUE_ROOT . 'templates/default/css/common1.css');
}
if (file_exists(BLUE_ROOT . 'templates/default/css/imagePreview.js'))
{
    @unlink(BLUE_ROOT . 'templates/default/css/imagePreview.js');
}
if (file_exists(BLUE_ROOT . 'admin/css/jquery.js'))
{
    @unlink(BLUE_ROOT . 'admin/css/jquery.js');
}

$db->query("ALTER TABLE " . table('post') . " ADD ip varchar(15) NOT NULL AFTER comment");
$db->query("ALTER TABLE " . table('pay') . " ADD logo varchar(40) NOT NULL AFTER fee");
$db->query("UPDATE " . table('pay') . " SET logo='images/alipay.jpg' 10');

if (!mkdir('data/upload/image', 0777))
{
	showmsg('创建目录data/upload/image出错，请手动创建。linux用户请修改目录权限');
}
else
{
	@chmod('data/upload/image', 0777);
}
showmsg('恭喜您成功升级到BlueCMS v1.6 sp1版本', 'index.php');



?>