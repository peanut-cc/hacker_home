<?php
 /*
 * 74cms AJAX 推荐给好友
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) :trim($_POST['act']);
 require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if ($_SESSION['uid']=='' || $_SESSION['username']=='')
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
$uid=intval($_SESSION['uid']);
 $user=$db->getone("select * from ".table('members')." where uid ='{$uid}' LIMIT 1");
if ($user['email_audit']=="0")
{
$verifyurl=$_SESSION['utype']=="1"?'/company_user.php?act=user_email':'/personal_user.php?act=user_email';
$verifyurl=get_member_url($_SESSION['utype'],true).$verifyurl;
exit(" 您的邮箱 <strong style=\"color:#FF6600\">{$user['email']}</strong> 没有验证，请先 <a href=\"{$verifyurl}\">[验证邮箱]</a>");
}
if ($act=='recommendjobs')
{
	$info=$db->getone("select * from ".table('members_info')." where uid ='{$uid}' LIMIT 1");
	$smarty->assign('info',$info);
	$smarty->assign('user',$user);
	$smarty->assign('job',explode('|',$_GET['job']));
	$captcha=get_cache('captcha');
	$smarty->assign('verify_recommendjobs',$captcha['verify_recommendjobs']);
	$smarty->display('plus/recommend/recommend_jobs.htm');
	exit();
}
elseif ($act=='send_recommendjobs')
{
	$sendemail=trim($_POST['sendemail']);
	$realname=trim($_POST['realname']);	
	$message=trim($_POST['message']);
	$jobname=trim($_POST['jobname']);
	$joburl=trim($_POST['joburl']);
	

	$captcha=get_cache('captcha');
	if ($captcha['verify_recommendjobs']=="1")
	{
		$postcaptcha=$_POST['postcaptcha'];
		if ($captcha['captcha_lang']=="cn" && strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
		{
		$postcaptcha=utf8_to_gbk($postcaptcha);
		}
		if (empty($postcaptcha) || empty($_SESSION['imageCaptcha_content']) || strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
		{
		unset($_SESSION['imageCaptcha_content']);
		exit("errcaptcha_false");
		}
	}

	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$realname=utf8_to_gbk($realname);
	$message=utf8_to_gbk($message);
	$jobname=utf8_to_gbk($jobname);
	}
	
	$uid=intval($_SESSION['uid']);
	$user=$db->getone("select * from ".table('members')." where uid ='{$uid}' LIMIT 1");
	if ($user['email_audit']=="0")
	{
	exit("email_audit_false");
	}
	$send_title="{$realname}向您推荐了职位，赶快看看吧！";
	$send_body="我在<a href=\"{$_CFG['site_domain']}{$_CFG['site_dir']}\" target=\"_blank\">{$_CFG['site_name']}</a>上看到一个招聘信息：\"<a href=\"{$joburl}\" target=\"_blank\">{$jobname}</a>\"，向你强烈推荐！";
	if (!empty($message))
	{
	$send_body=$send_body."<br/>留言:<br/>".$message;
	}
	if(smtp_mail($sendemail,$send_title,$send_body,$user['email'],$realname))
	{
	exit("mail_true");
	}
	else
	{
	exit("mail_false");
	}

}
elseif ($act=='recommendresume')
{
	$info=$db->getone("select * from ".table('members_info')." where uid ='{$uid}' LIMIT 1");
	$smarty->assign('info',$info);
	$smarty->assign('user',$user);
	$smarty->assign('resume',explode('|',$_GET['resume']));
	$captcha=get_cache('captcha');
	$smarty->assign('verify_recommendresume',$captcha['verify_recommendresume']);
	$smarty->display('plus/recommend/recommend_resume.htm');
	exit();
}
elseif ($act=='send_recommendresume')
{
	$sendemail=trim($_POST['sendemail']);
	$realname=trim($_POST['realname']);	
	$message=trim($_POST['message']);
	$resumename=trim($_POST['resumename']);
	$resumeurl=trim($_POST['resumeurl']);

	$captcha=get_cache('captcha');
	if ($captcha['verify_recommendresume']=="1")
	{
		$postcaptcha=$_POST['postcaptcha'];
		if ($captcha['captcha_lang']=="cn" && strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
		{
		$postcaptcha=utf8_to_gbk($postcaptcha);
		}
		if (empty($postcaptcha) || empty($_SESSION['imageCaptcha_content']) || strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
		{
		unset($_SESSION['imageCaptcha_content']);
		exit("errcaptcha_false");
		}
	}

	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$realname=utf8_to_gbk($realname);
	$message=utf8_to_gbk($message);
	$resumename=utf8_to_gbk($resumename);
	}
	$uid=intval($_SESSION['uid']);
	$user=$db->getone("select * from ".table('members')." where uid ='{$uid}' LIMIT 1");
	if ($user['email_audit']=="0")
	{
	exit("false");
	}
	$send_title="{$realname}向您推荐了求职简历，赶快看看吧！";
	$send_body="我在<a href=\"{$_CFG['site_domain']}{$_CFG['site_dir']}\" target=\"_blank\">{$_CFG['site_name']}</a>上看到一份求职简历：\"<a href=\"{$resumeurl}\" target=\"_blank\">{$resumename}的求职简历</a>\"，向你强烈推荐！";
	if (!empty($message))
	{
	$send_body=$send_body."<br/>留言:<br/>".$message;
	}
	if(smtp_mail($sendemail,$send_title,$send_body,$user['email'],$realname))
	{
	exit("true");
	}
	else
	{
	exit("false");
	}	
}
?>