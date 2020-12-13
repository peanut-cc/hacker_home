<?php
 /*
 * 74cms WAP
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
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'reg';
$smarty->cache = false;
if ($act == 'reg')
{
	if ($_CFG['closereg']=='1')showmsg("暂停会员注册，请稍后再次尝试！",1);
	$smarty->display("wap/wap-reg.htm");
}
elseif ($act == 'do_reg')
{
	if ($_CFG['closereg']=='1')showmsg("暂停会员注册，请稍后再次尝试！",1);
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
	$err="信息不完整";
	}
	elseif (strlen($username)<6 || strlen($username)>18)
	{
	$err="用户名长度为6-18个字符";
	}
	elseif (strlen($password)<6 || strlen($password)>18)
	{
	$err="密码长度为6-18个字符";
	}
	elseif ($password<>$_POST['password1'])
	{
	$err="两次输入的密码不同";
	}
	elseif ($password<>$_POST['password1'])
	{
	$err="两次输入的密码不同";
	}
	elseif (empty($email) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$email))
	{
	$err="电子邮箱格式错误";
	}
 	$ck_username=get_user_inusername($username);
	if (!empty($ck_username))
	{
	$err="用户名已经存在";
	}
	$ck_email=get_user_inemail($email);
	if (!empty($ck_email))
	{
	$err="电子邮箱已经存在";
	}	
	if ($err)
	{
	$smarty->assign('err',$err);
	$smarty->display("wap/wap-reg.htm");
	exit();
	}
	$smarty->assign('err',"注册出错");
	$smarty->display("wap/wap-reg.htm");
}
?>