<?php
 /*
 * 74cms 验证邮箱
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
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : ''; 
$email=trim($_POST['email']);
$send_key=trim($_POST['send_key']);
if (empty($send_key) || $send_key<>$_SESSION['send_key'])
{
exit("效验码错误");
}
if ($act=="send_code")
{
		if (empty($email) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]w+)*$/",$email))
		{
		exit("邮箱格式错误");
		}
		$sql = "select * from ".table('members')." where email = '{$email}' LIMIT 1";
		$userinfo=$db->getone($sql);
		if ($userinfo && $userinfo['uid']<>$_SESSION['uid'])
		{
		exit("邮箱已经存在！请填写其他邮箱");
		}
		elseif(!empty($userinfo['email']) && $userinfo['email_audit']=="1" && $userinfo['email']==$email)
		{
		exit("你的邮箱 {$email} 已经通过验证！");
		}
		else
		{
			if ($_SESSION['sendemail_time'] && (time()-$_SESSION['sendemail_time'])<60)
			{
			exit("请60秒后再进行验证！");
			}
			$rand=mt_rand(100000, 999999);
			if (smtp_mail($email,"{$_CFG['site_name']}邮件认证","{$QISHI['site_name']}提醒您：<br>您正在进行邮箱验证，验证码为:<strong>{$rand}</strong>"))
			{
			$_SESSION['verify_email']=$email;
			$_SESSION['email_rand']=$rand;
			$_SESSION['sendemail_time']=time();
			exit("success");
			}
			else
			{
			exit("邮箱配置出错，请联系网站管理员");
			}
		} 
}
elseif ($act=="verify_code")
{
	$verifycode=trim($_POST['verifycode']);
	if (empty($verifycode) || empty($_SESSION['email_rand']) || $verifycode<>$_SESSION['email_rand'])
	{
		exit("验证码错误");
	}
	else
	{
			$uid=intval($_SESSION['uid']);
			if (empty($uid))
			{
				exit("系统错误，UID丢失！");
			}
			else
			{
					$setsqlarr['email']=$_SESSION['verify_email'];
					$setsqlarr['email_audit']=1;
					updatetable(table('members'),$setsqlarr," uid='{$uid}'");
					if ($_SESSION['utype']=="2")
					{
					$u['email']=$_SESSION['verify_email'];
					updatetable(table('resume'),$u," uid='{$uid}'");
					updatetable(table('resume_tmp'),$u," uid='{$uid}'");
					}
					unset($setsqlarr,$_SESSION['verify_email'],$_SESSION['email_rand'],$u);
					if ($_CFG['operation_mode']=='1' && $_SESSION['utype']=='1')
					{
						$rule=get_cache('points_rule');
						if ($rule['verifyemail']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='verifyemail'   LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'verifyemail','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_company.php');
							report_deal($_SESSION['uid'],$rule['verifyemail']['type'],$rule['verifyemail']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['verifyemail']['type']=="1"?"+":"-";
							$_SESSION['handsel_verifyemail']=$_CFG['points_byname'].$operator.$rule['verifyemail']['value'];
							write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username']," 邮箱通过验证，{$_CFG['points_byname']}({$operator}{$rule['verifyemail']['value']})，(剩余:{$user_points})");
							}
						}
					} 
					exit("success");
			}
	}
}
?>