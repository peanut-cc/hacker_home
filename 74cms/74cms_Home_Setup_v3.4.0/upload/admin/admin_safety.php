<?php
 /*
 * 74cms ��ȫ����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'captcha';
check_permissions($_SESSION['admin_purview'],"set_safety");
$smarty->assign('pageheader',"��ȫ����");
if($act == 'filte')
{
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('navlabel','filte');
	$smarty->display('safety/admin_safety_filter.htm');
}
if($act == 'ip')
{
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('navlabel','ip');
	$smarty->display('safety/admin_safety_ip.htm');
}
if($act == 'csrf')
{
	get_token();
	$smarty->assign('config',$_CFG);
	$smarty->assign('navlabel','csrf');
	$smarty->display('safety/admin_safety_csrf.htm');
}
elseif($act == 'setsave')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k'")?adminmsg('����վ������ʧ��', 1):"";
	}
	refresh_cache('config');
	adminmsg("����ɹ���",2);
}
if($act == 'captcha')
{
	get_token();
	$smarty->assign('captcha',get_cache('captcha'));
	$smarty->assign('navlabel','captcha');
	$smarty->display('safety/admin_safety_captcha.htm');
}
elseif($act == 'captcha_save')
{
	check_token();
	if ($_POST['captcha_lang']=='cn')
	{
		$dir =QISHI_ROOT_PATH.'data/font/cn/';
		if($handle = @opendir($dir))
		{
			$i = 0;
			while(false !== ($file = @readdir($handle)))
			{
				if(strcasecmp(substr($file,-4),'.ttf')===0)
				{
					$list[]= $file;
					$i++;
				}
			}
		}
		if (empty($list))
		{
		adminmsg("�޸�ʧ�ܣ�ʹ��������֤����Ҫ�����ĺ��ֵ�TTF�ļ��ϴ��� data/font/cn Ŀ¼��",0);
		}
	}
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('captcha')." SET value='$v' WHERE name='$k'")?adminmsg('����վ������ʧ��', 1):"";
	}
	refresh_cache('captcha');
	adminmsg("����ɹ���",2);
}
?>