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
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
if ($_SESSION['uid']=='' || $_SESSION['username']=='' || $_SESSION['utype']!='2')
{
	header("Location: wap.php");
}
elseif ($act == 'index')
{
$smarty->cache = false;
$smarty->display("wap/wap-user-index.htm");
}
elseif ($act == 'favorites')
{
	require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
	$perpage = 5;
	$count  = 0;
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	$theurl = "wap_user.php?act=favorites";
	$start = ($page-1)*$perpage;
	$wheresql=" WHERE f.personal_uid='{$_SESSION['uid']}' ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_favorites')." AS f {$wheresql} ";
	$count=$db->get_total($total_sql);
	$joinsql=" LEFT JOIN ".table('jobs')." as  j  ON f.jobs_id=j.id ";
	$smarty->assign('favorites',get_favorites($start, $perpage,$joinsql.$wheresql));
	$smarty->assign('pagehtml',wapmulti($count, $perpage, $page, $theurl));
	$smarty->display('wap/wap-user-favorites.htm');
}
elseif ($act == 'add_favorites')
{
	require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
	$id=isset($_GET['id'])?trim($_GET['id']):exit("出错了");
		$link[0]['text'] = "[返回上一页]";
		$link[0]['href'] = $_SERVER["HTTP_REFERER"];
		$link[1]['text'] = "[查看收藏夹]";
		$link[1]['href'] = 'wap_user.php?act=favorites';
	if(add_favorites($id,$_SESSION['uid'])==0)
	{
	WapShowMsg("添加失败，收藏夹中已经存在此职位",0,$link);
	}
	else
	{
	WapShowMsg("添加成功",2,$link);
	}
}
?>