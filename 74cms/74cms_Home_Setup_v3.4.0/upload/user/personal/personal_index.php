<?php
/*
 * 74cms 个人会员中心
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');
$smarty->assign('leftmenu',"index");
if ($act=='index')
{
	$uid=intval($_SESSION['uid']);
	$smarty->assign('title','个人会员中心 - '.$_CFG['site_name']);
	$smarty->assign('user',$user);

	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$smarty->assign('loginlog',get_loginlog_one($uid,'1001'));

	$smarty->assign('interest_id',get_interest_jobs_id($uid));
	$smarty->assign('rand',rand(1,100));
	$smarty->assign('count_resume',count_resume($uid));
	$smarty->assign('count_interview',count_interview($uid));
	$smarty->assign('countinterview',$countinterview);
	$smarty->assign('count_down',count_personal_resume_down($uid));
	$smarty->assign('msg_total1',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1' AND `replyuid`<>'{$uid}' AND msgtype=1"));
	$smarty->assign('msg_total2',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1' AND `replyuid`<>'{$uid}' AND msgtype=2"));
	$smarty->display('member_personal/personal_index.htm');
}
unset($smarty);
?>