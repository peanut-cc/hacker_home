<?php
function tpl_function_qishi_link($params, &$smarty)
{
global $db,$_CFG;
$arr=explode(',',$params['set']);
foreach($arr as $str)
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
	case "���ֳ���":
		$aset['len'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "����":
		$aset['linktype'] = $a[1];
		break;
	case "��������":
		$aset['alias'] = $a[1];
		break;
	}
}
	$aset=array_map("get_smarty_request",$aset);
	$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
	$aset['row']=isset($aset['row'])?intval($aset['row']):60;
	$aset['start']=isset($aset['start'])?intval($aset['start']):0;
	$aset['len']=isset($aset['len'])?intval($aset['len']):8;
	$aset['linktype']=isset($aset['linktype'])?intval($aset['linktype']):1;
	$aset['dot']=isset($aset['dot'])?$aset['dot']:'';
	if ($aset['linktype']=="1"){
	$wheresql=" WHERE link_logo='' ";
	}
	else
	{
	$wheresql=" WHERE link_logo<>'' ";
	}
	$wheresql.=" AND display=1 ";
	if ($aset['alias']) $wheresql.=" AND alias='".$aset['alias']."' ";
	if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_links']=="1")
	{
		$wheresql.=" AND (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
	}
	$limit=" LIMIT ".intval($aset['start']).','.intval($aset['row']);
	$result = $db->query("SELECT link_url,link_name,link_logo FROM ".table('link')." ".$wheresql." ORDER BY show_order DESC ".$limit);
	$list = array();
	while($row = $db->fetch_array($result))
	{
		$row['title_']=$row['link_name'];
		$row['title']=cut_str($row['link_name'],$aset['len'],0,$aset['dot']);
		$list[] = $row;
	}
unset($arr,$str,$a,$params);
$smarty->assign($aset['listname'],$list);
}
?>