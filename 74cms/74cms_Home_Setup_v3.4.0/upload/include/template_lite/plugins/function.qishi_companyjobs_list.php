<?php
function tpl_function_qishi_companyjobs_list($params, &$smarty)
{
global $db,$_CFG;
$arrset=explode(',',$params['set']);
foreach($arrset as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "�б���":
		$aset['listname'] = $a[1];
		break;
	case "��ʾ��Ŀ":
		$aset['row'] = $a[1];
		break;
	case "�Ƽ�":
		$aset['recommend'] = $a[1];
		break;
	case "��ʼλ��":
		$aset['start'] = $a[1];
		break;
	case "ְλ������":
		$aset['jobslen'] = $a[1];
		break;
	case "��ʾְλ":
		$aset['jobsrow'] = $a[1];
		break;
	case "��ҵ������":
		$aset['companynamelen'] = $a[1];
		break;
	case "��������":
		$aset['district'] = $a[1];
		break;
	case "����С��":
		$aset['sdistrict'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "��ҵ":
		$aset['trade'] = $a[1];
		break;
	case "���ڷ�Χ":
		$aset['settr'] = $a[1];
		break;		
	case "������Ƹ":
		$aset['emergency'] = $a[1];
		break;
	case "����":
		$aset['displayorder'] = $a[1];
		break;
	case "��ҳ��ʾ":
		$aset['paged'] = $a[1];
		break;
	case "��˾ҳ��":
		$aset['companyshow'] = $a[1];
		break;
	case "ְλҳ��":
		$aset['jobsshow'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=isset($aset['row'])?intval($aset['row']):10;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['jobslen']=isset($aset['jobslen'])?intval($aset['jobslen']):8;
$aset['jobsrow']=isset($aset['jobsrow'])?intval($aset['jobsrow']):3;
$aset['companynamelen']=isset($aset['companynamelen'])?intval($aset['companynamelen']):16;
$aset['dot']=isset($aset['dot'])?$aset['dot']:null;
$aset['companyshow']=isset($aset['companyshow'])?$aset['companyshow']:'QS_companyshow';
$aset['jobsshow']=isset($aset['jobsshow'])?$aset['jobsshow']:'QS_jobsshow';
if (isset($aset['displayorder']))
{
		$arr=explode('>',$aset['displayorder']);
		$arr[1]=preg_match('/asc|desc/',$arr[1])?$arr[1]:"desc";
		if ($arr[0]=="rtime")
		{
		$orderbysql=" ORDER BY refreshtime {$arr[1]}";
		$jobstable=table('jobs_search_rtime');		
		}
		elseif ($arr[0]=="stickrtime")
		{
		$orderbysql=" ORDER BY stick {$arr[1]} , refreshtime {$arr[1]}";
		$jobstable=table('jobs_search_stickrtime');		
		}
		elseif ($arr[0]=="hot")
		{
		$orderbysql=" ORDER BY click {$arr[1]}";
		$jobstable=table('jobs_search_hot');		
		}
		elseif ($arr[0]=="scale")
		{
		$orderbysql=" ORDER BY scale {$arr[1]},refreshtime {$arr[1]}";
		$jobstable=table('jobs_search_scale');		
		}
		elseif ($arr[0]=="wage")
		{
		$orderbysql=" ORDER BY wage {$arr[1]},refreshtime {$arr[1]}";
		$jobstable=table('jobs_search_wage');		
		}
		else
		{
		$orderbysql=" ORDER BY refreshtime {$arr[1]}";
		$jobstable=table('jobs_search_rtime');	
		}
}
else
{
		$orderbysql=" ORDER BY refreshtime {$arr[1]}";
		$jobstable=table('jobs_search_rtime');	
}
if ($_CFG['subsite']=="1" && empty($aset['district']) && empty($aset['sdistrict']) && $_CFG['subsite_filter_jobs']=="1" )
{
	$wheresql.=" AND (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
}
if (isset($aset['recommend']))
{
	$wheresql.=" AND recommend=".intval($aset['recommend']);
}

if (isset($aset['emergency']))
{
	$wheresql.=" AND emergency=".intval($aset['emergency']);
}
if (isset($aset['settr']) && $aset['settr']<>'')
{
	$settr=intval($aset['settr']);
	if ($settr>0)
	{
	$settr_val=intval(strtotime("-".$aset['settr']." day"));
	$wheresql.=" AND refreshtime>".$settr_val;
	}
}
if (isset($aset['trade']))
{
	$wheresql.=" AND trade=".intval($aset['trade']);
}
if (isset($aset['district'])  && $aset['district']<>'')
{
	if (strpos($aset['district'],"-"))
	{
		$or=$orsql="";
		$arr=explode("-",$aset['district']);
		foreach($arr as $sid)
		{
			$orsql.=$or." district=".intval($sid);
			$or=" OR ";
		}
		$wheresql.=" AND  (".$orsql.") ";
	}
	else
	{
	$wheresql.=" AND district=".intval($aset['district'])." ";
	}
}
$limit=" LIMIT {$aset['start']},{$aset['row']}";
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
$uidlimit=" LIMIT {$aset['start']},".$aset['row']*15;
$sql1="SELECT id,uid FROM ".$jobstable.$wheresql.$orderbysql.$uidlimit;
//echo $sql1;
$result1 = $db->query($sql1);
$uidarr= array();
while($row = $db->fetch_array($result1))
{
	if (count($uidarr)>=$aset['row']) break;
	$uidarr[$row['uid']]=$row['uid'];
}
if (!empty($uidarr))
{
	$uidarr= implode(",",$uidarr);
	$wheresql=$wheresql?$wheresql." AND uid IN ({$uidarr}) ":" WHERE uid IN ({$uidarr}) ";
	$sql2="SELECT company_id,companyname,company_addtime,refreshtime,id,jobs_name,addtime,uid,click,highlight,highlight,setmeal_id,setmeal_name FROM ".table('jobs').$wheresql.$orderbysql;
	//echo $sql2;
	$result2 = $db->query($sql2);
	$countuid=array();
	while($row = $db->fetch_array($result2))
	{		
		$countuid[$row['uid']][]=$row['uid'];
		if (count($countuid[$row['uid']])>$aset['jobsrow'])continue;
		$companyarray[$row['uid']]['companyname_']=$row['companyname'];
		$companyarray[$row['uid']]['companyname']=cut_str($row['companyname'],$aset['companynamelen'],0,$aset['dot']);
		$companyarray[$row['uid']]['company_url']=url_rewrite($aset['companyshow'],array('id'=>$row['company_id']));
		$companyarray[$row['uid']]['company_addtime']=$row['company_addtime'];
		$companyarray[$row['uid']]['company_id']=$row['company_id'];
		$companyarray[$row['uid']]['refreshtime']=$companyarray[$row['uid']]['refreshtime']>$row['refreshtime']?$companyarray[$row['uid']]['refreshtime']:$row['refreshtime'];
		$companyarray[$row['uid']]['refreshtime_cn']=daterange(time(),$companyarray[$row['uid']]['refreshtime'],'m-d',"#FF3300");
		$companyarray[$row['uid']]['setmeal_id']=$row['setmeal_id'];
		$companyarray[$row['uid']]['setmeal_name']=$row['setmeal_name'];
		$companyarray[$row['uid']]['uid']=$row['uid'];
		$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_addtime']=$row['addtime'];
		$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_refreshtime']=$row['refreshtime'];
		$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_click']=$row['click'];
		$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_name']=cut_str($row['jobs_name'],$aset['jobslen'],0,$aset['dot']);
			if (!empty($row['highlight']))
			{
			$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_name']="<span style=\"color:{$row['highlight']}\">{$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_name']}</span>";
			}
		$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_url']=url_rewrite($aset['jobsshow'],array('id'=>$row['id']));
		$companyarray[$row['uid']]['jobs'][$row['id']]['id']=$row['id'];
	}
}
$smarty->assign($aset['listname'],$companyarray);
}
?>