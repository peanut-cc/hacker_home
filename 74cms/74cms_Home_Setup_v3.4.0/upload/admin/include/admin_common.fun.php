<?php
 /*
 * 74cms 管理中心共用函数
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
function admin_addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('admin_addslashes_deep', $value) : addslashes($value);
    }
}
function deep_stripslashes($str)
 {
 	if(is_array($str)){
 		foreach($str as $key => $val){
 			$str[$key] = deep_stripslashes($val);
 		}
 	} else {
 		$str = stripslashes($str);
 	}
 	return $str;
 }
function adminmsg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = true,$seconds=3)
{
	global $smarty;
    if (count($links) == 0)
    {
        $links[0]['text'] = '返回上一页';
        $links[0]['href'] = 'javascript:history.go(-1)';
    }
   $smarty->assign('ur_here',     '系统提示');
   $smarty->assign('msg_detail',  $msg_detail);
   $smarty->assign('msg_type',    $msg_type);
   $smarty->assign('links',       $links);
   $smarty->assign('default_url', $links[0]['href']);
   $smarty->assign('auto_redirect', $auto_redirect);
   $smarty->assign('seconds', $seconds);
   $smarty->display('sys/admin_showmsg.htm');
	exit();
}
function get_token()
{
	global $_CFG;
	if ($_CFG["open_csrf"]=="1")
	{
		global $smarty;
		if (!empty($_SESSION['token']))
		{
		unset($_SESSION['token']);
		}
		$hash = md5(uniqid(rand(), true));
		$n = mt_rand(1, 24);
		$token = substr($hash, $n, 8);
		$page=!empty($_SERVER['PHP_SELF'])?md5($_SERVER['PHP_SELF']):'token';
		$_SESSION['token'][$page] = $token;
		$smarty->assign('inputtoken', "<input type=\"hidden\"  name=\"hiddentoken\" value=\"{$_SESSION['token'][$page]}\" />");
		$smarty->assign('urltoken', "hiddentoken={$_SESSION['token'][$page]}");
	}
}
function check_token()
{
	global $_CFG;
	if ($_CFG["open_csrf"]=="1")
	{
		if (empty($_SESSION['token']))
		{
		unset($_SESSION['token']);
		global $smarty;
		$smarty->display('sys/admin_csrf.htm');
		exit();
		}
		else
		{
			$page=!empty($_SERVER['PHP_SELF'])?md5($_SERVER['PHP_SELF']):'token';
			$hiddentoken=!empty($_POST['hiddentoken'])?$_POST['hiddentoken']:$_GET['hiddentoken'];
			if ($_SESSION['token'][$page]!=$hiddentoken)
			{
			unset($_SESSION['token'],$hiddentoken);
			global $smarty;
			$smarty->display('sys/admin_csrf.htm');
			exit();
			}
		}
	unset($_SESSION['token'],$hiddentoken);
	}
}
function refresh_cache($cachename)
{
	global $db;
	$config_arr = array();
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_".$cachename.".php";
	$sql = "SELECT * FROM ".table($cachename);
	$arr = $db->getall($sql);
		foreach($arr as $key=> $val)
		{
		$config_arr[$val['name']] = $val['value'];
		}
	write_static_cache($cache_file_path,$config_arr);
}
function refresh_page_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_page.php";
		$sql = "SELECT * FROM ".table('page');
		$arr = $db->getall($sql);
			foreach($arr as $key=> $val)
			{
			$config_arr[$val['alias']] =array("file"=>$val['file'],"tpl"=>$val['tpl'],"rewrite"=>$val['rewrite'],"url"=>$val['url'],"caching"=>intval($val['caching'])*60,"tag"=>$val['tag'],"alias"=>$val['alias'],"pagetpye"=>$val['pagetpye']);
			}
		write_static_cache($cache_file_path,$config_arr);
}
function refresh_points_rule_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_points_rule.php";
		$sql = "SELECT * FROM ".table('members_points_rule');
		$arr = $db->getall($sql);
			foreach($arr as $key=> $val)
			{
			$config_arr[$val['name']] =array("type"=>$val['operation'],"value"=>$val['value']);
			}
		write_static_cache($cache_file_path,$config_arr);
}
function refresh_category_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_category.php";
	$sql = "SELECT * FROM ".table('category')."  ORDER BY c_order DESC,c_id ASC";
	$result = $db->query($sql);
		while($row = $db->fetch_array($result))
		{
			if ($row['c_alias']=="QS_officebuilding" || $row['c_alias']=="QS_street")
			{
			continue;
			}
			$catarr[$row['c_alias']][$row['c_id']] =array("id"=>$row['c_id'],"parentid"=>$row['c_parentid'],"categoryname"=>$row['c_name'],"stat_jobs"=>$row['stat_jobs'],"stat_resume "=>$row['stat_resume ']);
		}
		write_static_cache($cache_file_path,$catarr);
}
function refresh_nav_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_nav.php";
		$sql = "SELECT * FROM ".table('navigation')." WHERE display=1   ORDER BY navigationorder DESC";
		$result = $db->query($sql);
			while($row = $db->fetch_array($result))
			{
				$row['color']?$row['title']="<span style=\"color:".$row['color']."\">".$row['title']."</span>":'';
				if ($row['urltype']=="0")
				{
				$row['url']=url_rewrite($row['pagealias'],!empty($row['list_id'])?array('id'=>$row['list_id']):'');
				}
			$catarr[$row['alias']][] =array("title"=>$row['title'],"url"=>$row['url'],"target"=>$row['target'],"tag"=>$row['tag']);
			}
		write_static_cache($cache_file_path,$catarr);
}
function write_static_cache($cache_file_path, $config_arr)
{
	$content = "<?php\r\n";
	$content .= "\$data = " . var_export($config_arr, true) . ";\r\n";
	$content .= "?>";
	if (!file_put_contents($cache_file_path, $content, LOCK_EX))
	{
		$fp = @fopen($cache_file_path, 'wb+');
		if (!$fp)
		{
			exit('生成缓存文件失败');
		}
		if (!@fwrite($fp, trim($content)))
		{
			exit('生成缓存文件失败');
		}
		@fclose($fp);
	}
}
function write_log($str, $user,$log_type=1)
{
 	global $db, $timestamp,$online_ip;
 	$sql = "INSERT INTO ".table('admin_log')." (log_id, admin_name, add_time, log_value,log_ip,log_type) VALUES ('', '$user', '$timestamp', '$str','$online_ip','".intval($log_type)."')"; 
	return $db->query($sql);
}
function check_admin($name,$pwd)
{
 	global $db,$QS_pwdhash;
	$admin=get_admin_one($name);
	$md5_pwd=md5($pwd.$admin['pwd_hash'].$QS_pwdhash);
 	$row = $db->getone("SELECT COUNT(*) AS num FROM ".table('admin')." WHERE admin_name='$name' and pwd ='".$md5_pwd."' ");
 	if($row['num'] > 0){
 		return true;
 	}else{
 		return false;
 	}
}
function update_admin_info($admin_name,$refresh = true)
{
 	global $timestamp, $online_ip, $db;
	$admin = $db->getone("SELECT admin_id, admin_name, purview FROM ".table('admin')." WHERE admin_name = '$admin_name'");
 	$_SESSION['admin_id'] = $admin['admin_id'];
 	$_SESSION['admin_name'] = $admin['admin_name'];
 	$_SESSION['admin_purview'] = $admin['purview'];
	if ($refresh == true)
	{
		$last_login_time = $timestamp;
		$last_login_ip = $online_ip;
		$sql = "UPDATE ".table('admin')." SET last_login_time = '$last_login_time', last_login_ip = '$last_login_ip' WHERE admin_id='$_SESSION[admin_id]'";
		$db->query($sql);
		del_log($admin['admin_name'],90);
	}
 }
function check_cookie($user_name, $pwd)
 {
 	global $db,$QS_pwdhash;
 	$sql = "SELECT * FROM ".table('admin')." WHERE admin_name='".$user_name."' ";
 	$user = $db->getone($sql);
 	if(md5($user['admin_name'].$user['pwd'].$user['pwd_hash'].$QS_pwdhash) == $pwd)
	{
	return true;
	}
	return false;
 }
function get_admin_one($username){
	global $db;
	$sql = "select * from ".table('admin')." where admin_name = '".$username."' LIMIT 1";
	return $db->getone($sql);
}

function del_log($admin_name,$settr=30)
{
global $db;
$settr_val=strtotime("-".$settr." day");
if (!$db->query("Delete from ".table('admin_log')." WHERE admin_name='".$admin_name."' AND  add_time<".$settr_val." ")) return false;
return true;
}
function check_permissions($purview,$str)
{
	 if ($purview=="all")
	 {
	 return true;
	 }
	 else
	 {
	 $purview_arr=explode(',',$purview);
	 }
	 	if (in_array($str,$purview_arr))
		{
		return true;
		}
		else
		{
		permissions_insufficient();
		}
}
function permissions_insufficient()
{
	global $smarty;
    $smarty->display('sys/admin_sotp.htm');
	exit();
}
function html2text($str){
	$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
	$alltext = "";
	$start = 1;
	for($i=0;$i<strlen($str);$i++)
	{
		if($start==0 && $str[$i]==">")
		{
			$start = 1;
		}
		else if($start==1)
		{
			if($str[$i]=="<")
			{
				$start = 0;
				$alltext .= " ";
			}
			else if(ord($str[$i])>31)
			{
				$alltext .= $str[$i];
			}
		}
	}
	$alltext = str_replace("　"," ",$alltext);
	$alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
	$alltext = preg_replace("/[ ]+/s"," ",$alltext);
	return $alltext;
}
 

function get_subsite_one($id)
{
	global $db;
	$sql = "select * from ".table('subsite')." where s_id=".intval($id)." LIMIT 1";
	return $db->getone($sql);
}
function del_subsite($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('subsite')." WHERE s_id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
function refresh_subsite_cache()
{
	global $db;
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_subsite.php";
		$sql = "SELECT * FROM ".table('subsite')." WHERE s_effective=1 ORDER BY s_order desc,s_id desc";
		$arr = $db->getall($sql);
			foreach($arr as $key=> $val)
			{
			$config_arr[$val['s_domain']] =array("id"=>$val['s_id'],"sitename"=>$val['s_sitename'],"district"=>$val['s_district'],"districtname"=>$val['s_districtname'],"tpl"=>$val['s_tpl'],"logo"=>$val['s_logo'],"filter_notice"=>$val['s_filter_notice'],"filter_jobs"=>$val['s_filter_jobs'],"filter_resume"=>$val['s_filter_resume'],"filter_ad"=>$val['s_filter_ad'],"filter_links"=>$val['s_filter_links'],"filter_news"=>$val['s_filter_news'],"filter_explain"=>$val['s_filter_explain'],"filter_jobfair"=>$val['s_filter_jobfair'],"filter_simple"=>$val['s_filter_simple']);
			}
		write_static_cache($cache_file_path,$config_arr);
}
function getsubdirs($dir) {
        $subdirs = array();
        if(!$dh = opendir($dir)) return $subdirs;
        while ($f = readdir($dh)) {
                if($f =='.' || $f =='..') continue;
                $path = $dir.'/'.$f;  //如果只要子目录名, path = $f;
				$subdir=$f;
                if(is_dir($path)) {
                        $subdirs[] = $subdir;
                }
        }
		closedir($dh);
        return $subdirs;
}
function makejs_classify()
{
	global $db;
	$content = "//JavaScript Document 生成时间：".date("Y-m-d  H:i:s")."\n\n";
	$sql = "select * from ".table('category_district')." where parentid=0 order BY category_order desc,id asc";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$parentarr[]="\"".$parent['id'].",".$parent['categoryname']."\"";
	}
	$content .= "var QS_city_parent=new Array(".implode(',',$parentarr).");\n";	
	unset($parentarr);
	$content .= "var QS_city=new Array();\n";
	foreach($list as $val)
	{
		$sql1 = "select * from ".table('category_district')." where parentid=".$val['id']."  order BY category_order desc,id asc";
		$list1=$db->getall($sql1);
		if (is_array($list1))
		{	
			foreach($list1 as $val1)
			{
			$sarr[]=$val1['id'].",".$val1['categoryname'];
			}
		$content .= "QS_city[".$val['id']."]=\"".implode('|',$sarr)."\"; \n";	
		unset($sarr);
		}
	}
	$sql = "select * from ".table('category_jobs')." where parentid=0 ";
	$list=$db->getall($sql);
	foreach($list as $parent)
	{
	$parentarr[]="\"".$parent['id'].",".$parent['categoryname']."\"";
	}
	$content .= "var QS_jobs_parent=new Array(".implode(',',$parentarr).");\n";	
	$content .= "var QS_jobs=new Array(); \n";
	foreach($list as $val)
	{
		$sql1 = "select * from ".table('category_jobs')." where parentid=".$val['id']."  order BY category_order desc,id asc";
		$list1=$db->getall($sql1);
		if (is_array($list1))
		{	
			foreach($list1 as $val1)
			{
			$sarr[]=$val1['id'].",".$val1['categoryname'];
			}
		$content .= "QS_jobs[".$val['id']."]=\"".implode('|',$sarr)."\"; \n";	
		unset($sarr);
		}
	}
 	//
	$sql = "select * from ".table('category')." ORDER BY c_order DESC,c_id ASC";
	$list=$db->getall($sql);
	foreach($list as $li)
	{
		if ($li['c_alias']=="QS_trade")
		{
		$trade[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_company_type")
		{
		$companytype[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_wage")
		{
		$wage[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_jobs_nature")
		{
		$jobsnature[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_education")
		{
		$education[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_experience")
		{
		$experience[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_scale")
		{
		$scale[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_jobtag")
		{
		$jobtag[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
		elseif ($li['c_alias']=="QS_resumetag")
		{
		$resumetag[]="\"".$li['c_id'].",".$li['c_name']."\"";
		}
 	
	}
	$content .= "var QS_trade=new Array(".implode(',',$trade).");\n";
	$content .= "var QS_companytype=new Array(".implode(',',$companytype).");\n";
	$content .= "var QS_wage=new Array(".implode(',',$wage).");\n";
	$content .= "var QS_jobsnature=new Array(".implode(',',$jobsnature).");\n";
	$content .= "var QS_education=new Array(".implode(',',$education).");\n";
	$content .= "var QS_experience=new Array(".implode(',',$experience).");\n";
	$content .= "var QS_scale=new Array(".implode(',',$scale).");\n";
	$content .= "var QS_jobtag=new Array(".implode(',',$jobtag).");\n";
	$content .= "var QS_resumetag=new Array(".implode(',',$resumetag).");\n";
 	
	$fp = @fopen(QISHI_ROOT_PATH . 'data/cache_classify.js', 'wb+');
	if (!$fp){
			exit('生成JS文件失败');
		}
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$content=iconv(QISHI_DBCHARSET,"utf-8//IGNORE",$content);
	}
	 if (!@fwrite($fp, trim($content))){
			exit('写入JS文件失败');
		}
	@fclose($fp);
}
 ?>