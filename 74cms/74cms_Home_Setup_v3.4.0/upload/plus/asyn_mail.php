<?php
 /*
 * 74cms �����ʼ�
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
ignore_user_abort(true);
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
$uid=intval($_GET['uid']);
$key=trim($_GET['key']);
if (empty($uid) || empty($key))
{
 exit("error");
}
$asyn_userkey=asyn_userkey($uid);
if ($asyn_userkey<>$key)exit("error");
$mailconfig=get_cache('mailconfig');
$mail_templates=get_cache('mail_templates');
//����ע���ʼ�
if($act == 'reg'){
	if ($_GET['sendemail'] && $_GET['sendusername'] && $_GET['sendpassword'] && $mailconfig['set_reg']=="1")
	{
			$userinfo=get_user_inid($uid);
			if ($userinfo['username']==$_GET['sendusername'] && $userinfo['email']==$_GET['sendemail'])
			{ 
			$templates=label_replace($mail_templates['set_reg']);
			$templates_title=label_replace($mail_templates['set_reg_title']);
			smtp_mail($_GET['sendemail'],$templates_title,$templates);
			}
	}
}
//����ְλ�����ʼ�
elseif($act == 'jobs_apply')
{   
	$templates=label_replace($mail_templates['set_applyjobs']);
	$templates_title=label_replace($mail_templates['set_applyjobs_title']);
	smtp_mail($_GET['email'],$templates_title,$templates);
}
//�������Է����ʼ�
elseif($act == 'set_invite')
{
			$templates=label_replace($mail_templates['set_invite']);
			$templates_title=label_replace($mail_templates['set_invite_title']);
			smtp_mail($_GET['email'],$templates_title,$templates);
}
//�����ֵ�������ʼ�
elseif($act == 'set_order'){
			$templates=label_replace($mail_templates['set_order']);
			$templates_title=label_replace($mail_templates['set_order_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ֵ�ɹ��������ʼ�
elseif($act == 'set_payment'){
			$templates=label_replace($mail_templates['set_payment']);
			$templates_title=label_replace($mail_templates['set_payment_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//�޸����룬�����ʼ�
elseif($act == 'set_editpwd'){
			$templates=label_replace($mail_templates['set_editpwd']);
			$templates_title=label_replace($mail_templates['set_editpwd_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//ְλ���ͨ���������ʼ�
elseif($act == 'set_jobsallow'){
			$templates=label_replace($mail_templates['set_jobsallow']);
			$templates_title=label_replace($mail_templates['set_jobsallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//ְλδ���ͨ���������ʼ�
elseif($act == 'set_jobsnotallow'){
			$templates=label_replace($mail_templates['set_jobsnotallow']);
			$templates_title=label_replace($mail_templates['set_jobsnotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ҵ��֤ͨ���������ʼ�
elseif($act == 'set_licenseallow'){
			$templates=label_replace($mail_templates['set_licenseallow']);
			$templates_title=label_replace($mail_templates['set_licenseallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ҵ��֤δͨ���������ʼ�
elseif($act == 'set_licensenotallow'){
			$templates=label_replace($mail_templates['set_licensenotallow']);
			$templates_title=label_replace($mail_templates['set_licensenotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//��ҵ�����ر��Ƽ��������ʼ�
elseif($act == 'set_addmap'){
			$templates=label_replace($mail_templates['set_addmap']);
			$templates_title=label_replace($mail_templates['set_addmap_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//����ͨ����ˣ������ʼ�
elseif($act == 'set_resumeallow'){
			$templates=label_replace($mail_templates['set_resumeallow']);
			$templates_title=label_replace($mail_templates['set_resumeallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//����δͨ����ˣ������ʼ�
elseif($act == 'set_resumenotallow'){
			$templates=label_replace($mail_templates['set_resumenotallow']);
			$templates_title=label_replace($mail_templates['set_resumenotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
 ?>