<?php
 /*
 * 74cms �Ӻ���
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'add';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if (empty($_SESSION['uid']) || empty($_SESSION['username']))
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
$tuid=intval($_GET['tuid']);
if (empty($tuid))
{
exit("<div align=\"center\">���ʧ�ܣ�</div>");
}
elseif ($tuid==$_SESSION['uid'])
{
exit("<div align=\"center\">����������Լ�Ϊ���ѣ�</div>");
}
else
{
	$info=$db->getone("SELECT uid FROM ".table('members_buddy')." WHERE uid ='{$_SESSION['uid']}' AND tuid='{$tuid}' LIMIT 1");
	if (empty($info))
	{
	$addtime=time();
	$db->query("INSERT INTO ".table('members_buddy')." (uid,tuid,addtime) VALUES ('{$_SESSION['uid']}', '{$tuid}', '{$addtime}')");
	exit("<div align=\"center\"> ��ӳɹ����������ڻ�Ա���ĺ����б��в鿴��</div>");
	}
	else
	{
	exit("<div align=\"center\">���ʧ�ܣ���ĺ����б����Ѿ����ڣ�</div>");
	}
}
?>