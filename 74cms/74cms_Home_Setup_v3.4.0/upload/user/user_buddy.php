<?php
 /*
 * 74cms 加好友
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
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'add';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if (empty($_SESSION['uid']) || empty($_SESSION['username']))
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
$tuid=intval($_GET['tuid']);
if (empty($tuid))
{
exit("<div align=\"center\">添加失败！</div>");
}
elseif ($tuid==$_SESSION['uid'])
{
exit("<div align=\"center\">您不能添加自己为好友！</div>");
}
else
{
	$info=$db->getone("SELECT uid FROM ".table('members_buddy')." WHERE uid ='{$_SESSION['uid']}' AND tuid='{$tuid}' LIMIT 1");
	if (empty($info))
	{
	$addtime=time();
	$db->query("INSERT INTO ".table('members_buddy')." (uid,tuid,addtime) VALUES ('{$_SESSION['uid']}', '{$tuid}', '{$addtime}')");
	exit("<div align=\"center\"> 添加成功！您可以在会员中心好友列表中查看！</div>");
	}
	else
	{
	exit("<div align=\"center\">添加失败，你的好友列表中已经存在！</div>");
	}
}
?>