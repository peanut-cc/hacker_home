<?php
 /*
 * 74cms 会员注册
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
$alias="QS_login";
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
require_once(QISHI_ROOT_PATH.'include/fun_user.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$smarty->cache = false;
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'reg';
if(!$_SESSION['uid'] && !$_SESSION['username'] && !$_SESSION['utype'] &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'] )
{
	if(check_cookie($_COOKIE['QS']['uid'],$_COOKIE['QS']['username'],$_COOKIE['QS']['password']))
	{
	update_user_info($_COOKIE['QS']['uid'],false,false);
	header("Location:".get_member_url($_SESSION['utype']));
	}
	else
	{
	setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[username]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie('QS[password]',"", time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
	header("Location:".url_rewrite('QS_login'));
	}
}
 elseif ($_SESSION['username'] && $_SESSION['utype'] &&  $_COOKIE['QS']['username'] && $_COOKIE['QS']['password'])
{
	header("Location:".get_member_url($_SESSION['utype']));
}
elseif ($act=='reg')
{
	if ($_CFG['closereg']=='1')showmsg("网站暂停会员注册，请稍后再次尝试！",1);
	$smarty->assign('title','会员注册 - '.$_CFG['site_name']);
	$smarty->display('user/reg.htm');
}
elseif ($act=='form')
{
	if ($_CFG['closereg']=='1')showmsg("网站暂停会员注册，请稍后再次尝试！",1);
	if (intval($_GET['type'])==0)showmsg("请选择注册类型！",1);
	$smarty->assign('title','快速注册 - '.$_CFG['site_name']);
	$smarty->assign('type',$_GET['type']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userreg',$captcha['verify_userreg']);
	$smarty->display('user/reg_form.htm');
}
unset($smarty);
?>