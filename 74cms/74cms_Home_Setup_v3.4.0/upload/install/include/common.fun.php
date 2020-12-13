<?php
 /*
 * 74cms ��װ�򵼺���
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}

function install_addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
		if (!get_magic_quotes_gpc())
		{
		$value=is_array($value) ? array_map('install_addslashes_deep', $value) : addslashes($value);
		}
		$value=is_array($value) ? array_map('install_addslashes_deep', $value) : mystrip_tags($value);
		return $value;
    }
}
function mystrip_tags($string)
{
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
	$string = strip_tags($string);
	return $string;
}
function table($table)
{
 	global $pre;
    return  $pre .$table ;
}
 function install_showmsg($msg,$gourl='goback', $is_write = false)
 {
 	global $install_smarty;
	$install_smarty->cache = false;
 	$install_smarty->assign("msg",$msg);
 	$install_smarty->assign("gourl",$gourl);
 	$install_smarty->display("showmsg.htm");
 	exit();
 }
 function check_dirs($dirs)
{
    $checked_dirs = array();
    foreach ($dirs AS $k=> $dir)
    {
	$checked_dirs[$k]['dir'] = $dir;
        if (!file_exists(QISHI_ROOT_PATH .'/'. $dir))
        {
            $checked_dirs[$k]['read'] = '<span style="color:red;">Ŀ¼������</span>';
			$checked_dirs[$k]['write'] = '<span style="color:red;">Ŀ¼������</span>';
        }
		else
		{		
        if (is_readable(QISHI_ROOT_PATH.'/'.$dir))
        {
            $checked_dirs[$k]['read'] = '<span style="color:green;">�̿ɶ�</span>';
        }else{
            $checked_dirs[$k]['read'] = '<span sylt="color:red;">�����ɶ�</span>';
        }
        if(is_writable(QISHI_ROOT_PATH.'/'.$dir)){
        	$checked_dirs[$k]['write'] = '<span style="color:green;">�̿�д</span>';
        }else{
        	$checked_dirs[$k]['write'] = '<span style="color:red;">������д</span>';
        }
		}
    }
    return $checked_dirs;
}
 function randstr($length=6)
{
$hash='';
$chars= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz@#!~?:-=';   
$max=strlen($chars)-1;   
mt_srand((double)microtime()*1000000);   
for($i=0;$i<$length;$i++)   {   
$hash.=$chars[mt_rand(0,$max)];   
}   
return $hash;   
}
function get_cache($cachename)
{
	$cache_file_path =QISHI_ROOT_PATH. "data/cache_".$cachename.".php";
	if (file_exists($cache_file_path))
	{
	include($cache_file_path);
	return $data;
	}
	else
	{
	exit("�����ļ����ⶪʧ���뵽�����̨���»��棡");
	}
}
//���»���
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
			$config_arr[$val['alias']] =array("file"=>$val['file'],"tpl"=>$val['tpl'],"rewrite"=>$val['rewrite'],"html"=>$val['html'],"url"=>$val['url'],"caching"=>$val['caching'],"tag"=>$val['tag'],"alias"=>$val['alias']);
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
			exit('���ɻ����ļ�ʧ��');
		}
		if (!@fwrite($fp, trim($content)))
		{
			exit('���ɻ����ļ�ʧ��');
		}
		@fclose($fp);
	}
}
function makejs_classify()
{
	global $db;
	$content = "//JavaScript Document ����ʱ�䣺".date("Y-m-d  H:i:s")."\n\n";
	$sql = "select * from ".table('category_district')." where parentid=0 ";
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
			exit('����JS�ļ�ʧ��');
		}
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$content=iconv(QISHI_DBCHARSET,"utf-8//IGNORE",$content);
	}
	 if (!@fwrite($fp, trim($content))){
			exit('д��JS�ļ�ʧ��');
		}
	@fclose($fp);
}
function url_rewrite($alias=NULL,$get=NULL,$rewrite=true)
{
	global $_CFG,$_PAGE;
	$url ='';
	if ($_PAGE[$alias]['url']=='0' || $rewrite==false)
	{
			if (!empty($get))
			{
				foreach($get as $k=>$v)
				{
				$url .="{$k}={$v}&amp;";
				}
			}
			$url=!empty($url)?"?".rtrim($url,'&amp;'):'';
			return $_CFG['site_domain'].$_CFG['site_dir'].$_PAGE[$alias]['file'].$url;
	}
	else 
	{
			$url = $_CFG['site_domain'].$_CFG['site_dir'].$_PAGE[$alias]['rewrite'];
			if ($_PAGE[$alias]['pagetpye']=='2' && empty($get['page']))
			{
			$get['page']=1;
			}
			foreach($get as $k=>$v)
			{
			$url=str_replace('($'.$k.')',$v,$url);
			}
			$url=preg_replace('/\(\$(.+?)\)/','',$url);
			if(substr($url,-5)=='?key=')
			{
			$url=rtrim($url,'?key=');
			}
			return $url;
	}
}

 ?>