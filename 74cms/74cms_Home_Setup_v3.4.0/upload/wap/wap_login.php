<?php
 /*
 * 74cms WAP
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
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'login';
if($act == 'logout')
{
	unset($_SESSION['uid']);
	unset($_SESSION['username']);
	unset($_SESSION['utype']);
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	unset($_SESSION['activate_username']);
	unset($_SESSION['activate_email']);
	header("location:wap.php"); 
}
elseif ($act == 'login')
{
$smarty->cache = false;
$smarty->display("wap/wap-login.htm");
}
elseif ($act == 'do_login')
{
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	$username=isset($_POST['username'])?trim($_POST['username']):"";
	$password=isset($_POST['password'])?trim($_POST['password']):"";	
	if ($username && $password)
	{
		$login_js=user_login($username,$password,1,true);
		if (!empty($login_js['qs_login']) && $_SESSION['utype']=="2")
		{
		header("location:wap.php");
		}
		else
		{
			$smarty->cache = false;
			$smarty->assign('err',"1");
			$smarty->display("wap/wap-login.htm");
		}		
	}
	else
	{
	$smarty->cache = false;
	$smarty->assign('err',"1");
	$smarty->display("wap/wap-login.htm");
	}
}
?>