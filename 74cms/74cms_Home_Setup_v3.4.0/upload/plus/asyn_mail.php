<?php
 /*
 * 74cms 发送邮件
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
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
//发送注册邮件
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
//申请职位发送邮件
elseif($act == 'jobs_apply')
{   
	$templates=label_replace($mail_templates['set_applyjobs']);
	$templates_title=label_replace($mail_templates['set_applyjobs_title']);
	smtp_mail($_GET['email'],$templates_title,$templates);
}
//邀请面试发送邮件
elseif($act == 'set_invite')
{
			$templates=label_replace($mail_templates['set_invite']);
			$templates_title=label_replace($mail_templates['set_invite_title']);
			smtp_mail($_GET['email'],$templates_title,$templates);
}
//申请充值，发送邮件
elseif($act == 'set_order'){
			$templates=label_replace($mail_templates['set_order']);
			$templates_title=label_replace($mail_templates['set_order_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//充值成功，发送邮件
elseif($act == 'set_payment'){
			$templates=label_replace($mail_templates['set_payment']);
			$templates_title=label_replace($mail_templates['set_payment_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//修改密码，发送邮件
elseif($act == 'set_editpwd'){
			$templates=label_replace($mail_templates['set_editpwd']);
			$templates_title=label_replace($mail_templates['set_editpwd_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//职位审核通过，发送邮件
elseif($act == 'set_jobsallow'){
			$templates=label_replace($mail_templates['set_jobsallow']);
			$templates_title=label_replace($mail_templates['set_jobsallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//职位未审核通过，发送邮件
elseif($act == 'set_jobsnotallow'){
			$templates=label_replace($mail_templates['set_jobsnotallow']);
			$templates_title=label_replace($mail_templates['set_jobsnotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//企业认证通过，发送邮件
elseif($act == 'set_licenseallow'){
			$templates=label_replace($mail_templates['set_licenseallow']);
			$templates_title=label_replace($mail_templates['set_licenseallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//企业认证未通过，发送邮件
elseif($act == 'set_licensenotallow'){
			$templates=label_replace($mail_templates['set_licensenotallow']);
			$templates_title=label_replace($mail_templates['set_licensenotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//企业加入特别推荐，发送邮件
elseif($act == 'set_addmap'){
			$templates=label_replace($mail_templates['set_addmap']);
			$templates_title=label_replace($mail_templates['set_addmap_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//简历通过审核，发送邮件
elseif($act == 'set_resumeallow'){
			$templates=label_replace($mail_templates['set_resumeallow']);
			$templates_title=label_replace($mail_templates['set_resumeallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
//简历未通过审核，发送邮件
elseif($act == 'set_resumenotallow'){
			$templates=label_replace($mail_templates['set_resumenotallow']);
			$templates_title=label_replace($mail_templates['set_resumenotallow_title']);
			$useremail=get_user_inid($uid);
			smtp_mail($useremail['email'],$templates_title,$templates);
}
 ?>