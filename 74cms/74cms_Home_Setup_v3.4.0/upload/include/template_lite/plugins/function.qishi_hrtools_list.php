<?php
function tpl_function_qishi_hrtools_list($params, &$smarty)
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
		case "��ʼλ��":
			$aset['start'] = $a[1];
			break;
		case "��ַ�":
			$aset['dot'] = $a[1];
			break;
		case "����ID":
			$aset['type_id'] = $a[1];
			break;
		}
	}
	if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
	$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
	$aset['row']=isset($aset['row'])?intval($aset['row']):10;
	$aset['start']=isset($aset['start'])?intval($aset['start']):0;
	$aset['titlelen']=isset($aset['titlelen'])?intval($aset['titlelen']):15;
	$aset['dot']=isset($aset['dot'])?$aset['dot']:'';
	$orderbysql=" ORDER BY h_order DESC,h_id ASC";
	if ($aset['type_id'])
	{
	$wheresql=" WHERE  h_typeid=".intval($aset['type_id']);
	}
	$limit=" LIMIT ".abs($aset['start']).','.$aset['row'];
	$result = $db->query("SELECT * FROM ".table('hrtools')." ".$wheresql.$orderbysql.$limit);
	$list = array();
	while($row = $db->fetch_array($result))
	{
		if ($row['h_strong']=="1")
		{
		$row['h_filename']="<strong>{$row['h_filename']}</strong>";
		}
		if ($row['h_color'])
		{
		$row['h_filename']="<span style=\"color:{$row['h_color']}\">{$row['h_filename']}</span>";
		}
		$row['h_fileurl']=substr($row['h_fileurl'],0,7)=="http://"?$row['h_fileurl']:$_CFG['site_dir'].$row['h_fileurl'];
		$list[] = $row;
	}
	$smarty->assign($aset['listname'],$list);
}
?>