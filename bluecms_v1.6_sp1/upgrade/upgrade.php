<?php
/**
 * BlueCMS v1.6 to v1.6 sp1 �����ļ�
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
	showmsg('����Ŀ¼data/upload/image�������ֶ�������linux�û����޸�Ŀ¼Ȩ��');
}
else
{
	@chmod('data/upload/image', 0777);
}
showmsg('��ϲ���ɹ�������BlueCMS v1.6 sp1�汾', 'index.php');



?>