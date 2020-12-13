<?php
/*********************************************
*΢��Ƹ
********************************************/
function tpl_function_qishi_simple_list($params, &$smarty)
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
	case "��ʼλ��":
		$aset['start'] = $a[1];
		break;
	case "ְλ������":
		$aset['jobslen'] = $a[1];
		break;
	case "��ҵ������":
		$aset['companynamelen'] = $a[1];
		break;
	case "��������":
		$aset['brieflylen'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;	 
	case "�ؼ���":
		$aset['key'] = $a[1];
		break;
	case "�ؼ�������":
		$aset['keytype'] = $a[1];
		break;
	case "���ڷ�Χ":
		$aset['settr'] = $a[1];
		break;
	case "����":
		$aset['displayorder'] = $a[1];
		break;
	case "��ҳ��ʾ":
		$aset['page'] = $a[1];
		break;
	case "ҳ��":
		$aset['simpleshow'] = $a[1];
		break;	
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=isset($aset['row'])?intval($aset['row']):10;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['jobslen']=isset($aset['jobslen'])?intval($aset['jobslen']):8;
$aset['companynamelen']=isset($aset['companynamelen'])?intval($aset['companynamelen']):15;
$aset['brieflylen']=isset($aset['brieflylen'])?intval($aset['brieflylen']):0;
$aset['simpleshow']=isset($aset['simpleshow'])?$aset['simpleshow']:'QS_simpleshow';
if (isset($aset['displayorder']))
{
	if (strpos($aset['displayorder'],'>'))
	{
	$arr=explode('>',$aset['displayorder']);
	$arr[0]=preg_match('/refreshtime|id|click/',$arr[0])?$arr[0]:"";
	$arr[1]=preg_match('/asc|desc/',$arr[1])?$arr[1]:"";
		if ($arr[0] && $arr[1])
		{
		$orderbysql=" ORDER BY {$arr[0]} {$arr[1]}";
		}
	}
}
$wheresql=" AND audit=1 ";
if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_simple']=="1")
{
	$wheresql.=" AND (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
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
if (isset($aset['key']) && !empty($aset['key']))
{
	$aset['key']=trim($aset['key']);
	if ($aset['keytype']=="1" || $aset['keytype']=="")
	{
		$wheresql.=" AND  jobname like '%{$aset['key']}%'";
		$orderbysql="";
	}
	elseif ($aset['keytype']=="2")
	{
		$wheresql.=" AND  MATCH (`key`) AGAINST ('".fulltextpad($aset['key'])."') ";
		$orderbysql="";
	}
}
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
if (isset($aset['page']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('simple').$wheresql;
	//echo $total_sql;
	$total_count=$db->get_total($total_sql);	
	$page = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>'QS_simplelist','getarray'=>$_GET));
	$currenpage=$page->nowindex;
	$aset['start']=($currenpage-1)*$aset['row'];
	$smarty->assign('page',$page->show(3));
	$smarty->assign('total',$total_count);
}
	$limit=" LIMIT ".abs($aset['start']).','.$aset['row'];
	$result = $db->query("SELECT * FROM ".table('simple')." ".$wheresql.$orderbysql.$limit);
	$list   = array();
	//echo "SELECT * FROM ".table('jobs')." ".$wheresql.$orderbysql.$limit;
		while($row = $db->fetch_array($result))
		{
		$row['jobname_']=$row['jobname'];
		$row['jobname']=cut_str($row['jobname'],$aset['jobslen'],0,$aset['dot']);
		$row['detailed_']=strip_tags($row['detailed']);
		if ($aset['brieflylen']>0)
			{
				$row['detailed']=cut_str($row['detailed_'],$aset['brieflylen'],0,$aset['dot']);
			}
			else
			{
				$row['detailed']=$row['detailed_'];
			}
		$row['comname_']=$row['comname'];
		$row['refreshtime_cn']=daterange(time(),$row['refreshtime'],'Y-m-d',"#FF3300");
		$row['comname']=cut_str($row['comname'],$aset['companynamelen'],0,$aset['dot']);
		$row['simple_url']=url_rewrite($aset['simpleshow'],array('id'=>$row['id']));
		$list[] = $row;
		}
		$smarty->assign($aset['listname'],$list);
}
?>