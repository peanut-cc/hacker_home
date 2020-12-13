<?php
function tpl_function_qishi_resume_list($params, &$smarty)
{
global $db,$_CFG;
$arrset=explode(',',$params['set']);
foreach($arrset as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "列表名":
		$aset['listname'] = $a[1];
		break;
	case "显示数目":
		$aset['row'] = $a[1];
		break;
	case "开始位置":
		$aset['start'] = $a[1];
		break;
	case "姓名长度":
		$aset['namelen'] = $a[1];
		break;
	case "特长描述长度":
		$aset['specialtylen'] = $a[1];
		break;
	case "意向职位长度":
		$aset['jobslen'] = $a[1];
		break;
	case "填补字符":
		$aset['dot'] = $a[1];
		break;
	case "职位分类":
		$aset['jobcategory'] = trim($a[1]);
		break;
	case "职位大类":
		$aset['category'] = trim($a[1]);
		break;
	case "职位小类":
		$aset['subclass'] = trim($a[1]);
		break;
	case "地区分类":
		$aset['citycategory'] = trim($a[1]);
		break;
	case "地区大类":
		$aset['district'] = $a[1];
		break;
	case "地区小类":
		$aset['sdistrict'] = $a[1];
		break;
	case "标签":
		$aset['tag'] = $a[1];
		break;
	case "学历":
		$aset['education'] = $a[1];
		break;
	case "工作经验":
		$aset['experience'] = $a[1];
		break;
	case "等级":
		$aset['talent'] = $a[1];
		break;
	case "性别":
		$aset['sex'] = $a[1];
		break;
	case "照片":
		$aset['photo'] = $a[1];
		break;
	case "关键字":
		$aset['key'] = $a[1];
		break;
	case "日期范围":
		$aset['settr'] = $a[1];
		break;
	case "排序":
		$aset['displayorder'] = $a[1];
		break;
	case "分页显示":
		$aset['paged'] = $a[1];
		break;
	case "页面":
		$aset['showname'] = $a[1];
		break;
	case "列表页":
		$aset['listpage'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=intval($aset['row'])>0?intval($aset['row']):10;
if ($aset['row']>30)$aset['row']=30;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['namelen']=isset($aset['namelen'])?intval($aset['namelen']):4;
$aset['specialtylen']=isset($aset['specialtylen'])?intval($aset['specialtylen']):0;
$aset['jobslen']=isset($aset['jobslen'])?intval($aset['jobslen']):0;
$aset['dot']=isset($aset['dot'])?$aset['dot']:null;
$aset['showname']=isset($aset['showname'])?$aset['showname']:'QS_resumeshow';
$aset['listpage']=isset($aset['listpage'])?$aset['listpage']:'QS_resumelist';
$resumetable=table('resume_search_rtime');
if (isset($aset['displayorder']))
{
	$arr=explode('>',$aset['displayorder']);
	$arr[1]=preg_match('/asc|desc/',$arr[1])?$arr[1]:"desc";
	if ($arr[0]=="rtime")
	{
		$orderbysql=" ORDER BY r.refreshtime {$arr[1]}";
	}
}
if (!empty($aset['category']) || !empty($aset['subclass']) || !empty($aset['jobcategory']))
{
	if (!empty($aset['jobcategory']))
	{
					$dsql=$xsql="";
					$arr=explode("-",$aset['jobcategory']);
					$arr=array_unique($arr);
					foreach($arr as $sid)
					{
						$cat=explode(".",$sid);
						if (intval($cat[1])===0)
						{
						$dsql.= " OR category =".intval($cat[0]);
						}
						else
						{
						$xsql.= " OR subclass =".intval($cat[1]);
						}
					}
					$joinwheresql.=" WHERE ".ltrim(ltrim($dsql.$xsql),'OR');
	}
	else
	{
					if (!empty($aset['category']))
					{
						if (strpos($aset['category'],"-"))
						{
							$or=$orsql="";
							$arr=explode("-",$aset['category']);
							$sqlin=implode(",",$arr);
							if (count($arr)>10) exit();
							$sqlin=implode(",",$arr);
							if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
							{
								$joinwheresql.=" AND category IN  ({$sqlin}) ";
							}
						}
						else
						{
							$joinwheresql.=" AND  category=".intval($aset['category']);
						}
					}
					if (!empty($aset['subclass']))
					{
						if (strpos($aset['subclass'],"-"))
						{
							$or=$orsql="";
							$arr=explode("-",$aset['subclass']);
							if (count($arr)>10) exit();
							$sqlin=implode(",",$arr);
							if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
							{
								$joinwheresql.=" AND subclass IN  ({$sqlin}) ";
							}
						}
						else
						{
						$joinwheresql.=" AND subclass=".intval($aset['subclass']);
						}
					}
					if (!empty($joinwheresql))
					{
					$joinwheresql=" WHERE ".ltrim(ltrim($joinwheresql),'AND');
					}
					
	}
	$joinsql="  INNER  JOIN  ( SELECT DISTINCT pid FROM ".table('resume_jobs')." {$joinwheresql} )AS j ON  r.id=j.pid ";
}
if (!empty($aset['citycategory']))
{
		if (strpos($aset['citycategory'],"-"))
		{
					$dsql=$xsql="";
					$arr=explode("-",$aset['citycategory']);
					$arr=array_unique($arr);
					if (count($arr)>10) exit();					 
					foreach($arr as $sid)
					{
						$cat=explode(".",$sid);
						if (intval($cat[1])===0)
						{
						$dsql.= " OR r.district =".intval($cat[0]);
						}
						else
						{
						$xsql.= " OR r.sdistrict =".intval($cat[1]);
						}
					}
					$wheresql.=" AND  (".ltrim(ltrim($dsql.$xsql),'OR').") ";
		}
		else
		{
				$cat=explode(".",$aset['citycategory']);
				if (intval($cat[1])>0)
				{
				$wheresql.=" AND r.sdistrict =".intval($cat[1]);
				}
				else
				{
				$wheresql.=" AND r.district=".intval($cat[0])." ";
				}
		}
}
else
{
			if (isset($aset['district'])  && $aset['district']<>'')
			{
				if (strpos($aset['district'],"-"))
				{
					$or=$orsql="";
					$arr=explode("-",$aset['district']);
					$arr=array_unique($arr);
					if (count($arr)>10) exit();
					$sqlin=implode(",",$arr);
					if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
					{
						$wheresql.=" AND r.district IN  ({$sqlin}) ";
					}
				}
				else
				{
				$wheresql.=" AND r.district=".intval($aset['district'])." ";
				}
			}
			if (isset($aset['sdistrict'])  && $aset['sdistrict']<>'')
			{
				if (strpos($aset['sdistrict'],"-"))
				{
					$or=$orsql="";
					$arr=explode("-",$aset['sdistrict']);
					$arr=array_unique($arr);					
					if (count($arr)>10) exit();
					$sqlin=implode(",",$arr);
					if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
					{
						$wheresql.=" AND r.sdistrict IN  ({$sqlin}) ";
					}
				}
				else
				{
					$wheresql.=" AND r.sdistrict=".intval($aset['sdistrict'])." ";
				}
			}	
}
if ($_CFG['subsite']=="1" && empty($aset['citycategory']) && empty($aset['district']) && empty($aset['sdistrict']) && $_CFG['subsite_filter_resume']=="1")
{
	$wheresql.=" AND (r.subsite_id=0 OR r.subsite_id=".intval($_CFG['subsite_id']).") ";
}
if (isset($aset['settr']) && !empty($aset['settr']))
{
	$settr_val=intval(strtotime("-".intval($aset['settr'])." day"));
	$wheresql.=" AND r.refreshtime > ".$settr_val;
}
if (isset($aset['experience']) && !empty($aset['experience']))
{
	$wheresql.=" AND r.experience=".intval($aset['experience'])." ";
}
if (isset($aset['education']) && !empty($aset['education']))
{
	$wheresql.=" AND r.education=".intval($aset['education'])." ";
}
if (isset($aset['talent']) && !empty($aset['talent']))
{
	$wheresql.=" AND r.talent=".intval($aset['talent'])." ";
}
if (isset($aset['photo']) && !empty($aset['photo']))
{
	$wheresql.=" AND r.photo='".intval($aset['photo'])."' ";
}
if (isset($aset['sex'])  && !empty($aset['sex']))
{
	$wheresql.=" AND r.sex=".intval($aset['sex'])."";
}
if (isset($aset['key']) && !empty($aset['key']))
{
	if ($_CFG['resumesearch_purview']=='2')
	{
		if ($_SESSION['username']=='')
		{
		header("Location: ".url_rewrite('QS_login')."?url=".urlencode($_SERVER["REQUEST_URI"]));
		}
	}
	$key=trim($aset['key']);
	if ($_CFG['resumesearch_type']=='1')
	{
		$akey=explode(' ',$key);
		if (count($akey)>1)
		{
		$akey=array_filter($akey);
		$akey=array_slice($akey,0,2);
		$akey=array_map("fulltextpad",$akey);
		$key='+'.implode(' +',$akey);
		$mode=' IN BOOLEAN MODE';
		}
		else
		{
		$key=fulltextpad($key);
		$mode=' ';
		}
		$wheresql.=" AND  MATCH (r.`key`) AGAINST ('{$key}'{$mode}) ";
	}
	else
	{
		$wheresql.=" AND r.likekey LIKE '%{$key}%' ";
	}
		if ($_CFG['resumesearch_sort']=='1')
		{
		$orderbysql="";
		}
		else
		{
		$orderbysql=" ORDER BY r.refreshtime DESC ";
		}
	$resumetable=table('resume_search_key');
}
if (isset($aset['tag']) && !empty($aset['tag']))
{
	$tag=intval($aset['tag']);
	$wheresql.=" AND  (tag1='{$tag}' OR tag2='{$tag}' OR tag3='{$tag}' OR tag4='{$tag}' OR tag5='{$tag}') ";
	$orderbysql="";
	$resumetable=table('resume_search_tag');
}
$wheresql.=" AND r.display='1' ";
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
		if (isset($aset['paged']))
		{
			require_once(QISHI_ROOT_PATH.'include/page.class.php');
			$total_sql="SELECT  COUNT(*) AS num  FROM  {$resumetable} AS r ".$joinsql.$wheresql;
			$total_count=$db->get_total($total_sql);
			if (intval($_CFG['resume_list_max'])>0)
			{
				$total_count>intval($_CFG['resume_list_max']) && $total_count=intval($_CFG['resume_list_max']);
			}
			//echo $total_sql;
			//echo "SELECT  COUNT(DISTINCT r.id) AS num  FROM  ".table('resume')." AS r ".$wheresql;
			$page = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>$aset['listpage'],'getarray'=>$_GET));
			$currenpage=$page->nowindex;
			$aset['start']=abs($currenpage-1)*$aset['row'];
			$smarty->assign('page',$page->show(3));
			$smarty->assign('pagemin',$page->show(4));
			$smarty->assign('total',$total_count);
		}
	$limit=" LIMIT {$aset['start']} , {$aset['row']}";
	$list = $id = array();
	$idresult = $db->query("SELECT id FROM {$resumetable}  AS r".$joinsql.$wheresql.$orderbysql.$limit);
	//echo "SELECT id FROM {$resumetable}  AS r".$joinsql.$wheresql.$orderbysql.$limit;
	while($row = $db->fetch_array($idresult))
	{
	$id[]=$row['id'];
	}
	if (!empty($id))
	{
	$wheresql=" WHERE id IN (".implode(',',$id).") ";
	$result = $db->query("SELECT * FROM ".table('resume')."  AS r ".$wheresql.$orderbysql);
		while($row = $db->fetch_array($result))
		{
				if ($row['display_name']=="2")
				{
					$row['fullname']="N".str_pad($row['id'],7,"0",STR_PAD_LEFT);
					$row['fullname_']=$row['fullname'];		
				}
				elseif($row['display_name']=="3")
				{
					$row['fullname']=cut_str($row['fullname'],1,0,"**");
					$row['fullname_']=$row['fullname'];	
				}
				else
				{
					$row['fullname_']=$row['fullname'];
					$row['fullname']=cut_str($row['fullname'],$aset['namelen'],0,$aset['dot']);
				}
			$row['specialty_']=strip_tags($row['specialty']);
			if ($aset['specialtylen']>0)
			{
			$row['specialty']=cut_str(strip_tags($row['specialty']),$aset['specialtylen'],0,$aset['dot']);
			}
			if ($aset['jobslen']>0)
			{
			$row['intention_jobs']=cut_str(strip_tags($row['intention_jobs']),$aset['jobslen'],0,$aset['dot']);
			}
			$row['photosrc']=$row['photo']?$_CFG['resume_photo_dir_thumb'].$row['photo_img']:$_CFG['resume_photo_dir_thumb']."no_photo.gif";
			$row['resume_url']=url_rewrite($aset['showname'],array('id'=>$row['id']));
			$row['refreshtime_cn']=daterange(time(),$row['refreshtime'],'Y-m-d',"#FF3300");
			$row['age']=date("Y")-$row['birthdate'];
			if ($row['tag'])
			{
				$tag=explode('|',$row['tag']);
				$taglist=array();
				if (!empty($tag) && is_array($tag))
				{
					foreach($tag as $t)
					{
					$tli=explode(',',$t);
					$taglist[]=array($tli[0],$tli[1]);
					}
				}
				$row['tag']=$taglist;
			}
			else
			{
			$row['tag']=array();
			}
			$list[] = $row;
		}
	}
	else
	{
	$list=array();
	}
	$smarty->assign($aset['listname'], $list);
}
?>