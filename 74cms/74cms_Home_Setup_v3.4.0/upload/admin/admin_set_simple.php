<?php
 /*
 * 74cms ΢��Ƹ
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
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set';
$smarty->assign('act',$act);
$smarty->assign('pageheader',"΢��Ƹ");
check_permissions($_SESSION['admin_purview'],"set_simple");	
if($act == 'set')
{
	get_token();	
	$smarty->assign('config',$_CFG);
	$smarty->assign('text',get_cache('text'));
	$smarty->display('set_simple/admin_set_simple.htm');
}
elseif($act == 'set_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k'")?adminmsg('��������ʧ��', 1):"";
	}
	refresh_cache('config');
	adminmsg("����ɹ���",2);
}
?>