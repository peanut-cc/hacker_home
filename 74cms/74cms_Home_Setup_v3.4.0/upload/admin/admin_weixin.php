<?php
 /*
 * 74cms 微信公众平台
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'set_weixin';
$smarty->assign('act',$act);
$smarty->assign('navlabel',$act);
$smarty->assign('pageheader',"微信公众平台");	
if($act == 'set_weixin')
{
	check_permissions($_SESSION['admin_purview'],"set_weixinconnect");	
	get_token();
	$smarty->assign('rand',rand(1,100));
	$smarty->assign('upfiles_dir',$upfiles_dir);	
	$smarty->assign('config',$_CFG);
	$smarty->display('weixin/admin_weixin.htm');
}
elseif($act == 'set_weixin_save')
{
	check_permissions($_SESSION['admin_purview'],"set_weixinconnect");	
	check_token();
		require_once(ADMIN_ROOT_PATH.'include/upload.php');
		if($_FILES['weixin_img']['name'])
		{
		$weixin_img=_asUpFiles($upfiles_dir, "weixin_img", 1024*2, 'jpg/gif/png',"weixin_img");
		!$db->query("UPDATE ".table('config')." SET value='$weixin_img' WHERE name='weixin_img'")?adminmsg('更新站点设置失败', 1):"";
		}
		foreach($_POST as $k => $v)
		{
		!$db->query("UPDATE ".table('config')." SET value='{$v}' WHERE name='{$k}'")?adminmsg('更新站点设置失败', 1):"";
		}
		refresh_cache('config');
		adminmsg("保存成功！",2);
}
?>