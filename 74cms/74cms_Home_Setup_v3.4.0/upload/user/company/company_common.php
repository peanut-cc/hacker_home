<?php
/*
 * 74cms ��ҵ��Ա����
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
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	if ($_SESSION['uid']=='' || $_SESSION['username']=='' || intval($_SESSION['uid'])===0)
	{
		header("Location: ".url_rewrite('QS_login')."?act=logout");
		exit();
	}
	elseif ($_SESSION['utype']!='1') 
	{
	$link[0]['text'] = "��Ա����";
	$link[0]['href'] = url_rewrite('QS_login');
	showmsg('�����ʵ�ҳ����Ҫ ��ҵ��Ա ��¼��',1,$link);
	}
	$act = !empty($_GET['act']) ? trim($_GET['act']) : 'index';
	$smarty->cache = false;
	$user=get_user_info($_SESSION['uid']);
	if ($user['status']=="2" && $act!='index' && $act!='user_status'  && $act!='user_status_save') 
	{
		$link[0]['text'] = "�����˺�״̬";
		$link[0]['href'] = 'company_user.php?act=user_status';
		$link[1]['text'] = "���ػ�Ա������ҳ";
		$link[1]['href'] = 'company_index.php?act=';
	exit(showmsg('�����˺Ŵ�����ͣ״̬��������Ϊ��������в�����',1,$link));	
	}
	elseif (empty($user))
	{
	unset($_SESSION['utype'],$_SESSION['uid'],$_SESSION['username']);
	header("Location:".url_rewrite('QS_login')."?url=".$_SERVER["REQUEST_URI"]);
	exit();
	}
	if ($_CFG['login_com_audit_email'] && $user['email_audit']=="0" && $act!='user_email' && $act!='user_mobile')
	{
		$link[0]['text'] = "��֤����";
		$link[0]['href'] = 'company_user.php?act=user_email';
		$link[1]['text'] = "��վ��ҳ";
		$link[1]['href'] = $_CFG['site_dir'];
		showmsg('��������δ��֤����֤����ܽ�������������',1,$link,true,6);
		exit();
	}
	$sms=get_cache('sms_config');
	if ($_CFG['login_com_audit_mobile'] && $user['mobile_audit']=="0" && $act!='user_mobile' && $act!='user_email' && $sms['open']=="1")
	{
		$link[0]['text'] = "��֤�ֻ�";
		$link[0]['href'] = 'company_user.php?act=user_mobile';
		$link[1]['text'] = "��վ��ҳ";
		$link[1]['href'] = $_CFG['site_dir'];
		showmsg('�����ֻ�δ��֤����֤����ܽ�������������',1,$link,true,6);
		exit();
	}
	$smarty->assign('sms',$sms);
	$smarty->assign('promotion_category',get_promotion_category());
	$company_profile=get_company($_SESSION['uid']);
	if (!empty($company_profile))
	{
	$smarty->assign('company_url',url_rewrite('QS_companyshow',array('id'=>$company_profile['id'])));
	}	
	$smarty->assign('userindexurl','company_index.php');
	if ($_SESSION['handsel_userlogin'])
	{
	//��һ�ε�¼��ʾ
	$smarty->assign('handsel_userlogin',$_SESSION['handsel_userlogin']);
	unset($_SESSION['handsel_userlogin']);
	}
?>