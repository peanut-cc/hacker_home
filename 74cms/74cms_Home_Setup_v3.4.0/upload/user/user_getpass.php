<?php
 /*
 * 74cms ��Աע��
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$smarty->cache = false;
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'enter';
if ($act=='enter')
{
	$smarty->assign('title','�һ����� - '.$_CFG['site_name']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_getpwd',$captcha['verify_getpwd']);
	$smarty->assign('sms',get_cache('sms_config'));
	$smarty->assign('step',"1");
	$smarty->display('user/getpass.htm');
}
//�һ������2��
elseif ($act=='get_pass')
{
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_getpwd']=='1' && empty($postcaptcha))
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_getpwd']=='1' &&  strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	$postusername=trim($_POST['username'])?trim($_POST['username']):showmsg('�������û�����',1);
	if (empty($_POST['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$_POST['email']))
	{
	showmsg('���������ʽ����',1);
	}
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$userinfo=get_user_inusername($postusername);
	if (empty($userinfo) || $userinfo['email']<>$_POST['email'])
	{
	showmsg('�û�����ע��������д����',1);
	}
	else
	{
			$mailconfig=get_cache('mailconfig');
			$arr['username']=$userinfo['username'];
			$arr['password']=rand(100000,999999);
				if (smtp_mail($userinfo['email'],"�һ�����","����������Ϊ��".$arr['password']))
				{
					$md5password=md5(md5($arr['password']).$userinfo['pwd_hash'].$QS_pwdhash);
					if (!$db->query( "UPDATE ".table('members')." SET password = '$md5password'  WHERE uid='{$userinfo['uid']}'"))
					{
					showmsg('�����޸�ʧ��',1);
					}
 					$smarty->assign('step',"2");
					$smarty->assign('email',$userinfo['email']);
					$smarty->assign('title','�һ����� - '.$_CFG['site_name']);
					$smarty->display('user/getpass.htm');
				}
				else
				{
					showmsg('�ʼ�����ʧ�ܣ�����ϵ��վ����Ա',0);
				}
	}
}
unset($smarty);
?>