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
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'reg';
$smarty->cache = false;
if ($act == 'reg')
{
	if ($_CFG['closereg']=='1')showmsg("��ͣ��Աע�ᣬ���Ժ��ٴγ��ԣ�",1);
	$smarty->display("wap/wap-reg.htm");
}
elseif ($act == 'do_reg')
{
	if ($_CFG['closereg']=='1')showmsg("��ͣ��Աע�ᣬ���Ժ��ٴγ��ԣ�",1);
	require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	$username = isset($_POST['username'])?trim($_POST['username']):"";
	$password = isset($_POST['password'])?trim($_POST['password']):"";
	$member_type = 2;
	$email = isset($_POST['email'])?trim($_POST['email']):"";
	if (empty($username)||empty($password)||empty($member_type)||empty($email))
	{
	$err="��Ϣ������";
	}
	elseif (strlen($username)<6 || strlen($username)>18)
	{
	$err="�û�������Ϊ6-18���ַ�";
	}
	elseif (strlen($password)<6 || strlen($password)>18)
	{
	$err="���볤��Ϊ6-18���ַ�";
	}
	elseif ($password<>$_POST['password1'])
	{
	$err="������������벻ͬ";
	}
	elseif ($password<>$_POST['password1'])
	{
	$err="������������벻ͬ";
	}
	elseif (empty($email) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$email))
	{
	$err="���������ʽ����";
	}
 	$ck_username=get_user_inusername($username);
	if (!empty($ck_username))
	{
	$err="�û����Ѿ�����";
	}
	$ck_email=get_user_inemail($email);
	if (!empty($ck_email))
	{
	$err="���������Ѿ�����";
	}	
	if ($err)
	{
	$smarty->assign('err',$err);
	$smarty->display("wap/wap-reg.htm");
	exit();
	}
	$smarty->assign('err',"ע�����");
	$smarty->display("wap/wap-reg.htm");
}
?>