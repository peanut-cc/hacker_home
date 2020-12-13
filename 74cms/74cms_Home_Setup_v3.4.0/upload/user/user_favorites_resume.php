<?php
 /*
 * 74cms 添加到人才库
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
if ($_SESSION['uid']=='' || $_SESSION['username']=='')
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
if ($_SESSION['utype']!='1')
{
	exit("必须是企业会员才可以使用人才库！");
}
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
$user=get_user_info($_SESSION['uid']);
if ($user['status']=="2") 
{
	$str="<a href=\"".get_member_url(2,true)."company_user.php?act=user_status\">[设置帐号状态]</a>";
	exit("您的账号处于暂停状态，请先设为正常后进行操作！".$str);
}
if ($act=="add")
{
	$id=isset($_GET['id'])?$_GET['id']:exit("出错了"); 
	$add_return=add_favorites($id,$_SESSION['uid']);
	if ($add_return==="full")
	{
	exit("添加失败，人才库容量已经超出最大限制!");
	}
	elseif ($add_return=="0")
	{
	exit("添加失败，人才库中已经存在！");
	}
	else
	{
?>
<table width="100%" border="0" cellspacing="8" cellpadding="0" id="add_ok">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/13.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">添加成功!共添加 <?php echo $add_return?>份简历</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;">
	<a href="<?php echo get_member_url(1,true)?>company_recruitment.php?act=favorites_list" >查看人才库</a><br />

	<a href="javascript:void(0)"  class="DialogClose">添加完成</a>
	
	</div>
	</td>
  </tr>
</table>
<?php
}
}
?>