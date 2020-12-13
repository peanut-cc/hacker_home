<?php
 /*
 * 74cms �Ա����ʺŵ�¼
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
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'login';
$top_parameters=trim($_REQUEST['top_parameters']);
$top_sign=trim($_REQUEST['top_sign']);
if($act == 'login' && empty($top_parameters))
{
	$url="https://oauth.taobao.com/authorize?response_type=user&client_id={$_CFG['taobao_appkey']}&redirect_uri=";
	$url.=urlencode("{$_CFG['site_domain']}{$_CFG['site_dir']}user/connect_taobao.php");
	header("Location:{$url}");	
}
elseif($act == 'login' && !empty($top_parameters))
{
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	unset($dbhost,$dbuser,$dbpass,$dbname);
	require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
	if (empty($top_sign))
	{
	exit('��������');
	}
	$base64str=base64_encode(md5($top_parameters.$_CFG['taobao_appsecret'],TRUE ));
	if ($base64str<>$top_sign)
	{
	exit('�����Ƿ���');
	}
	else
	{
	$code=base64_decode($top_parameters);
	parse_str($code,$code);
	$token=md5($code['nick'].$code['user_id']);
	}
	if (empty($token))
	{
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] = "{$_CFG['site_domain']}{$_CFG['site_dir']}user/connect_taobao.php";
	showmsg('��¼ʧ�ܣ�token��ȡʧ��',0);
	}
	else
	{
				require_once(QISHI_ROOT_PATH.'include/fun_user.php');
				$uinfo=get_user_intaobao_access_token($token);
				if (!empty($uinfo))
				{
					update_user_info($uinfo['uid']);
					$member_url=get_member_url($_SESSION['utype']);
					header("Location: {$member_url}");
				}
				else
				{
					if (!empty($_SESSION['uid']) && !empty($_SESSION['utype']))
					{
					$db->query("UPDATE ".table('members')." SET taobao_access_token = '{$token}'  WHERE uid='{$_SESSION[uid]}' AND taobao_access_token='' LIMIT 1");
					$link[0]['text'] = "�����Ա����";
					$link[0]['href'] = get_member_url($_SESSION['utype']);
					showmsg('���ʺųɹ���',2,$link);
					}
					else
					{
					$_SESSION['taobao_access_token']=$token;
					header("Location:?act=reg");
					}
				}
	}
	
}
elseif ($act=='reg')
{
	if (empty($_SESSION["taobao_access_token"]))
	{
		exit("access_token is empty");
	}
	else
	{
		require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
		$smarty->assign('title','������Ϣ - '.$_CFG['site_name']);
		$smarty->assign('t_url',"?act=");
		$smarty->display('user/connect-taobao.htm');
	}
}
elseif ($act=='reg_save')
{
	if (empty($_SESSION["taobao_access_token"]))
	{
		exit("access_token is empty");
	}
	$val['username']=!empty($_POST['username'])?trim($_POST['username']):exit("err");
	$val['email']=!empty($_POST['email'])?trim($_POST['email']):exit("err");
	$val['member_type']=intval($_POST['member_type']);
	$val['password']=!empty($_POST['password'])?trim($_POST['password']):exit("err");	
	require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
	$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
	unset($dbhost,$dbuser,$dbpass,$dbname);
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$userid=user_register($val['username'],$val['password'],$val['member_type'],$val['email']);
	if ($userid)
	{
		$db->query("UPDATE ".table('members')." SET taobao_access_token = '{$_SESSION['taobao_access_token']}'  WHERE uid='{$userid}' AND taobao_access_token='' LIMIT 1");
		unset($_SESSION["taobao_access_token"]);
		update_user_info($userid);
		$userurl=get_member_url($val['member_type']);
		header("Location:{$userurl}");
	}
	else
	{
		unset($_SESSION["taobao_access_token"]);
		require_once(QISHI_ROOT_PATH.'include/tpl.inc.php');
		$link[0]['text'] = "������ҳ";
		$link[0]['href'] = "{$_CFG['site_domain']}{$_CFG['site_dir']}";
		showmsg('ע��ʧ�ܣ�',0,$link);
	}
	
}