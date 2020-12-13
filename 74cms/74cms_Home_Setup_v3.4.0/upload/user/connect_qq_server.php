<?php
 /*
 * 74cms QQ互联 server-side模式
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'QQlogin';
$login_allback="{$_CFG['site_domain']}{$_CFG['site_dir']}user/connect_qq_server.php?act=login_allback" ;
$binding_callback="{$_CFG['site_domain']}{$_CFG['site_dir']}user/connect_qq_server.php?act=binding_callback" ;
if (!function_exists('json_decode'))
{
exit('您的php不支持json_decode');
}
if ($_CFG['qq_appid']=="0" || empty($_CFG['qq_appid']) || empty($_CFG['qq_appkey']))
{
header("Location:{$_SERVER['HTTP_REFERER']}");
}
elseif($act == 'QQlogin')
{
	$scope="get_user_info";
	$_SESSION['state'] = md5(uniqid(rand(), TRUE));
	$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
	.$_CFG['qq_appid']. "&redirect_uri=" . urlencode($login_allback)
	. "&state=" . $_SESSION['state']
	. "&scope=".$scope;
	header("Location:{$login_url}");
}
elseif ($act=='login_allback')
{
		if($_REQUEST['state'] != $_SESSION['state'])
		{
			exit("The state does not match. You may be a victim of CSRF.");
		}
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" .$_CFG['qq_appid']. "&redirect_uri=" . urlencode($login_allback)
            . "&client_secret=" .$_CFG['qq_appkey']. "&code=" . $_REQUEST["code"];
        $response = get_url_contents($token_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if (isset($msg->error))
            {
                echo "<h3>error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }
        $params = array();
        parse_str($response, $params);
        $access_token=$params["access_token"];
		if (empty($access_token))
		{
			exit("access_token is empty");
		}
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
		$str  = get_url_contents($graph_url);
		if (strpos($str, "callback") !== false)
		{
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}
		$user = json_decode($str);
		if (isset($user->error))
		{
			echo "<h3>error:</h3>" . $user->error;
			echo "<h3>msg  :</h3>" . $user->error_description;
			exit;
		}
    	$_SESSION["openid"] = $user->openid;
		if (!empty($_SESSION["openid"]))
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
					$link[0]['text'] = "进入会员中心";
					$link[0]['href'] = get_member_url($_SESSION['utype']);
					$_SESSION['uqqid']=$_SESSION['openid'];
					showmsg('绑定QQ帐号成功！',2,$link);
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
		$smarty->assign('title','补充信息 - '.$_CFG['site_name']);
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
		$link[0]['text'] = "返回首页";
		$link[0]['href'] = "{$_CFG['site_domain']}{$_CFG['site_dir']}";
		showmsg('注册失败！',0,$link);
	}
	
}
elseif($act == 'binding')
{
	if (empty($_SESSION['uid']) || empty($_SESSION['utype']) || !empty($_SESSION['uqqid']))
	{
		exit("error");
	}
	$scope="get_user_info";
	$_SESSION['state'] = md5(uniqid(rand(), TRUE));
	$binding_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
	.$_CFG['qq_appid']. "&redirect_uri=" . urlencode($binding_callback)
	. "&state=" . $_SESSION['state']
	. "&scope=".$scope;
	header("Location:{$binding_url}");
}
elseif ($act=='binding_callback')
{
		if (empty($_SESSION['uid']) || empty($_SESSION['utype']) || !empty($_SESSION['uqqid']))
		{
			exit("error");
		}
		if($_REQUEST['state'] != $_SESSION['state'])
		{
			exit("The state does not match. You may be a victim of CSRF.");
		}
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" .$_CFG['qq_appid']. "&redirect_uri=" . urlencode($binding_callback)
            . "&client_secret=" .$_CFG['qq_appkey']. "&code=" . $_REQUEST["code"];
        $response = get_url_contents($token_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if (isset($msg->error))
            {
                echo "<h3>error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }
        $params = array();
        parse_str($response, $params);
        $access_token=$params["access_token"];
		if (empty($access_token))
		{
			exit("access_token is empty");
		}
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
		$str  = get_url_contents($graph_url);
		if (strpos($str, "callback") !== false)
		{
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}
		$user = json_decode($str);
		if (isset($user->error))
		{
			echo "<h3>error:</h3>" . $user->error;
			echo "<h3>msg  :</h3>" . $user->error_description;
			exit;
		}
    	$_SESSION["openid"] = $user->openid;
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
					$link[0]['text'] = "用别的QQ帐号绑定";
					$link[0]['href'] = "?act=binding";
					$link[1]['text'] = "进入会员中心";
					$link[1]['href'] =get_member_url($_SESSION['utype']);
					showmsg('此QQ帐号已经绑定了其他会员,请换一个QQ帐号！',2,$link);
			}
			else
			{
					$db->query("UPDATE ".table('members')." SET qq_openid = '{$_SESSION['openid']}'  WHERE uid='{$_SESSION[uid]}' AND qq_openid='' LIMIT 1");
					$link[0]['text'] = "进入会员中心";
					$link[0]['href'] = get_member_url($_SESSION['utype']);
					$_SESSION['uqqid']=$_SESSION['openid'];
					showmsg('绑定QQ帐号成功！',2,$link);
			}
}
function get_url_contents($url)
{
    if (ini_get("allow_url_fopen") == "1")
	{
        return file_get_contents($url);
	}
	elseif(function_exists(curl_init))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result =  curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	else
	{
		exit("请把allow_url_fopen设为On或打开CURL扩展");
	}  
}
?>