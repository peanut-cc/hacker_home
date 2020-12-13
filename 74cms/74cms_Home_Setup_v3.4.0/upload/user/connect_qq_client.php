<?php
 /*
 * 74cms QQ���� client-sideģʽ
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'QQlogin';
if($act == 'QQlogin')
{
$url="https://graph.qq.com/oauth2.0/authorize?response_type=token&client_id={$_CFG['qq_appid']}&redirect_uri={$_CFG['site_domain']}{$_CFG['site_dir']}user/connect_qq_client.php".urlencode('?act=login_check');
header("Location:{$url}");	
}
elseif ($act=='login_check')
{
	$html ="<script type=\"text/javascript\" src=\"http://qzonestyle.gtimg.cn/qzone/openapi/qc_loader.js\" charset=\"utf-8\" data-callback=\"true\"></script> ";
	$html.="<script type=\"text/javascript\">";
	$html.="if(QC.Login.check())";
	$html.="{";
	$html.="QC.Login.getMe(function(openId, accessToken)";
	$html.="{";
	$html.="window.location.href = '?act=login_go&openid='+openId;"; 
	$html.="});";
	$html.="}";
	$html.="</script>";
	exit($html);
}
elseif ($act=='login_go')
{
	$_SESSION["openid"] = trim($_GET['openid']);
	if (empty($_SESSION["openid"]))
	{
		showmsg('��¼ʧ�ܣ�openid��ȡ����',0);
	}
	else
	{
			require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
			$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
			unset($dbhost,$dbuser,$dbpass,$dbname);
			require_once(QISHI_ROOT_PATH.'include/fun_user.php');
			$user=get_user_inqqopenid($_SESSION["openid"]);
			if (!empty($user))
			{
				update_user_info($user['uid']);
				$userurl=get_member_url($_SESSION['utype']);
				header("Location:{$userurl}");
			}
			else
			{
				if (!empty($_SESSION['uid']) && !empty($_SESSION['utype']) && !empty($_SESSION['openid']))
				{
					require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
					$db->query("UPDATE ".table('members')." SET qq_openid = '{$_SESSION['openid']}'  WHERE uid='{$_SESSION[uid]}' AND qq_openid='' LIMIT 1");
					$link[0]['text'] = "�����Ա����";
					$link[0]['href'] = get_member_url($_SESSION['utype']);
					$_SESSION['uqqid']=$_SESSION['openid'];
					showmsg('��QQ�ʺųɹ���',2,$link);
				}
				else
				{
					header("Location:?act=reg");
				}
			}
	}
}
elseif ($act=='reg')
{
	if (empty($_SESSION["openid"]))
	{
		exit("openid is empty");
	}
	else
	{
		require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
		$smarty->assign('title','������Ϣ - '.$_CFG['site_name']);
		$smarty->assign('qqurl',"?act=");
		$smarty->display('user/connect-qq.htm');
	}
}
elseif ($act=='reg_save')
{
	if (empty($_SESSION["openid"]))
	{
		exit("openid is empty");
	}
	$openid=trim($_SESSION["openid"]);
	$openid=substr($openid,1,5);
	$openid=$openid.date("Ymd").mt_rand(1000,9999);
	$val['username']=$openid;
	$val['email']=$openid.'@qq.com';
	$val['member_type']=intval($_POST['member_type']);
	$val['password']=mt_rand(111111,999999);	
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	unset($dbhost,$dbuser,$dbpass,$dbname);
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$userid=user_register($val['username'],$val['password'],$val['member_type'],$val['email']);
	if ($userid)
	{
		$db->query("UPDATE ".table('members')." SET qq_openid = '{$_SESSION['openid']}'  WHERE uid='{$userid}' AND qq_openid='' LIMIT 1");
		update_user_info($userid);
		$userurl=get_member_url($val['member_type']);
		header("Location:{$userurl}");
	}
	else
	{
		require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
		$link[0]['text'] = "������ҳ";
		$link[0]['href'] = "{$_CFG['site_domain']}{$_CFG['site_dir']}";
		showmsg('ע��ʧ�ܣ�',0,$link);
	}
	
}
elseif($act == 'binding')
{
	$url="https://graph.qq.com/oauth2.0/authorize?response_type=token&client_id={$_CFG['qq_appid']}&redirect_uri={$_CFG['site_domain']}{$_CFG['site_dir']}user/connect_qq_client.php".urlencode('?act=binding_check');
header("Location:{$url}");
}
elseif ($act=='binding_check')
{
	$html ="<script type=\"text/javascript\" src=\"http://qzonestyle.gtimg.cn/qzone/openapi/qc_loader.js\" charset=\"utf-8\" data-callback=\"true\"></script> ";
	$html.="<script type=\"text/javascript\">";
	$html.="if(QC.Login.check())";
	$html.="{";
	$html.="QC.Login.getMe(function(openId, accessToken)";
	$html.="{";
	$html.="window.location.href = '?act=binding_callback&openid='+openId;"; 
	$html.="});";
	$html.="}";
	$html.="</script>";
	exit($html);
}
elseif ($act=='binding_callback')
{
		if (empty($_SESSION['uid']) || empty($_SESSION['utype']))
		{
			exit("error");
		}
		$_SESSION["openid"] = trim($_GET['openid']);
		if (empty($_SESSION['openid']))
		{
			exit("error");
		}
			require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
			$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
			unset($dbhost,$dbuser,$dbpass,$dbname);
			require_once(QISHI_ROOT_PATH.'include/fun_user.php');
			$user=get_user_inqqopenid($_SESSION["openid"]);
			require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
			if (!empty($user))
			{
					$link[0]['text'] = "�ñ��QQ�ʺŰ�";
					$link[0]['href'] = "?act=binding";
					$link[1]['text'] = "�����Ա����";
					$link[1]['href'] =get_member_url($_SESSION['utype']);
					showmsg('��QQ�ʺ��Ѿ�����������Ա,�뻻һ��QQ�ʺţ�',2,$link);
			}
			else
			{
					$db->query("UPDATE ".table('members')." SET qq_openid = '{$_SESSION['openid']}'  WHERE uid='{$_SESSION[uid]}' AND qq_openid='' LIMIT 1");
					$link[0]['text'] = "�����Ա����";
					$link[0]['href'] = get_member_url($_SESSION['utype']);
					$_SESSION['uqqid']=$_SESSION['openid'];
					showmsg('��QQ�ʺųɹ���',2,$link);
			}
}
?>