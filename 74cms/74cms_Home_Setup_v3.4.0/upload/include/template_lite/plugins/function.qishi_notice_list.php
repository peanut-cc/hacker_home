<?php
function tpl_function_qishi_notice_list($params, &$smarty)
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
	case "���ⳤ��":
		$aset['titlelen'] = $a[1];
		break;
	case "ժҪ����":
		$aset['infolen'] = $a[1];
		break;		
	case "��ʼλ��":
		$aset['start'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "����":
		$aset['type_id'] = $a[1];
		break;
	case "����":
		$aset['displayorder'] = $a[1];
		break;
	case "��ҳ��ʾ":
		$aset['paged'] = $a[1];
		break;
	case "ҳ��":
		$aset['showname'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=isset($aset['row'])?intval($aset['row']):10;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['titlelen']=isset($aset['titlelen'])?intval($aset['titlelen']):15;
$aset['infolen']=isset($aset['infolen'])?intval($aset['infolen']):0;
$aset['showname']=isset($aset['showname'])?$aset['showname']:'QS_noticeshow';
if ($aset['displayorder'])
{
	if (strpos($aset['displayorder'],'>'))
	{
	$arr=explode('>',$aset['displayorder']);
	$arr[0]=preg_match('/sort|id/',$arr[0])?$arr[0]:"";
	$arr[1]=preg_match('/asc|desc/',$arr[1])?$arr[1]:"";
		if ($arr[0] && $arr[1])
		{
		$orderbysql=" ORDER BY ".$arr[0]." ".$arr[1];
		}
	}
}
else
{
	$orderbysql="  ORDER BY `sort` desc , id desc";
}
$wheresql=" WHERE is_display=1 ";
$aset['type_id']?$wheresql.=" AND type_id=".intval($aset['type_id'])." ":'';
if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_notice']=="1")
{
	$wheresql.=" AND (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
}
if (isset($aset['paged']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('notice').$wheresql;
	$total_count=$db->get_total($total_sql);
	$pagelist = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>'QS_noticelist','getarray'=>$_GET));
	$currenpage=$pagelist->nowindex;
	$aset['start']=($currenpage-1)*$aset['row'];
		if ($total_count>$aset['row'])
		{
		$smarty->assign('page',$pagelist->show(3));
		}
		$smarty->assign('total',$total_count);
}
$limit=" LIMIT ".abs($aset['start']).','.$aset['row'];
$result = $db->query("SELECT * FROM ".table('notice')." ".$wheresql.$orderbysql.$limit);
//echo "SELECT * FROM ".table('notice')." ".$wheresql.$orderbysql.$limit;
$list=array();
while($row = $db->fetch_array($result))
{
$row['title_']=$row['title'];
$style_color=$row['tit_color']?"color:".$row['tit_color'].";":'';
$style_font=$row['tit_b']=="1"?"font-weight:bold;":'';
$row['title']=cut_str($row['title'],$aset['titlelen'],0,$aset['dot']);
if ($style_color || $style_font)$row['title']="<span style=".$style_color.$style_font.">".$row['title']."</span>";
$row['url'] =$row['is_url']<>"http://"?$row['is_url']:url_rewrite($aset['showname'],array('id'=>$row['id']));
$row['briefly_']=strip_tags($row['content']);
	if ($aset['infolen']>0)
	{
	$row['briefly']=cut_str(strip_tags($row['content']),$aset['infolen'],0,$aset['dot']);
	}
$list[] = $row;
}
$smarty->assign($aset['listname'],$list);
}
?>