<?php
/*
 * 74cms ���˻�Ա����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
if(!defined('IN_QISHI')) die('Access Denied!');
$page_select="user";
require_once(dirname(dirname(dirname(__FILE__))).'/include/common.inc.php');
$smarty->cache = false;
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['uid'])===0)
{
	header("Location: ".url_rewrite('QS_login')."?act=logout");
	exit();
}
elseif ($_SESSION['utype']!='2')
{
	$link[0]['text'] = "��Ա����";
	$link[0]['href'] = url_rewrite('QS_login');
	showmsg('�����ʵ�ҳ����Ҫ ���˻�Ա ��¼��',1,$link);
}
	$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
	$user=get_user_info($_SESSION['uid']);	
	if (empty($user))
	{
	unset($_SESSION['utype'],$_SESSION['uid'],$_SESSION['username']);
	header("Location: ".url_rewrite('QS_login')."?url=".$_SERVER["REQUEST_URI"]);
	exit();
	}
	elseif ($user['status']=="2" && $act!='index' && $act!='user_status'  && $act!='user_status_save') 
	{
		$link[0]['text'] = "�����˺�״̬";
		$link[0]['href'] = 'personal_user.php?act=user_status';
		$link[1]['text'] = "���ػ�Ա������ҳ";
		$link[1]['href'] = 'personal_index.php?act=';
		exit(showmsg('�����˺Ŵ�����ͣ״̬��������Ϊ��������в�����',1,$link));	
	}
	if ($_CFG['login_per_audit_email'] && $user['email_audit']=="0" && $act!='user_email' && $act!='user_mobile')
	{
		$link[0]['text'] = "��֤����";
		$link[0]['href'] = 'personal_user.php?act=user_email';
		$link[1]['text'] = "��վ��ҳ";
		$link[1]['href'] = $_CFG['site_dir'];
		showmsg('��������δ��֤����֤����ܽ�������������',1,$link,true,6);
		exit();
	}
	$sms=get_cache('sms_config');
	if ($_CFG['login_per_audit_mobile'] && $user['mobile_audit']=="0" && $act!='user_mobile' && $act!='user_email' && $sms['open']=="1")
	{
		$link[0]['text'] = "��֤�ֻ�";
		$link[0]['href'] = 'personal_user.php?act=user_mobile';
		$link[1]['text'] = "��վ��ҳ";
		$link[1]['href'] = $_CFG['site_dir'];
		showmsg('�����ֻ�δ��֤����֤����ܽ�������������',1,$link,true,6);
		exit();
	}
	$smarty->assign('userindexurl','personal_index.php');
	$smarty->assign('auditresume',get_auditresume_list($_SESSION['uid']));
	$smarty->assign('sms',$sms);
?>