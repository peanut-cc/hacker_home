<?php
function tpl_function_qishi_help_list($params, &$smarty)
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
	case "����":
		$aset['parentid'] = $a[1];
		break;
	case "С��":
		$aset['type_id'] = $a[1];
		break;
	case "�ؼ���":
		$aset['key'] = $a[1];
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
	case "��ҳ��ʾ":
		$aset['paged'] = $a[1];
		break;
	case "ҳ��":
		$aset['showname'] = $a[1];
		break;
	case "�б�ҳ":
		$aset['listpage'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
$aset['row']=isset($aset['row'])?intval($aset['row']):10;
$aset['start']=isset($aset['start'])?intval($aset['start']):0;
$aset['titlelen']=isset($aset['titlelen'])?intval($aset['titlelen']):15;
$aset['infolen']=isset($aset['infolen'])?intval($aset['infolen']):0;
$aset['showname']=isset($aset['showname'])?$aset['showname']:'QS_helpshow';
$aset['listpage']=isset($aset['listpage'])?$aset['listpage']:'QS_helplist';
$orderbysql=" ORDER BY `order` DESC ,id DESC";
isset($aset['parentid'])?$wheresql.=" AND parentid=".intval($aset['parentid'])." ":'';
isset($aset['type_id'])?$wheresql.=" AND type_id=".intval($aset['type_id'])." ":'';
if (!empty($aset['key']))
{
$key=trim($aset['key']);
$wheresql.=" AND title like '%{$key}%'";
}
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}
if (isset($aset['paged']))
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('help').$wheresql;
	$total_count=$db->get_total($total_sql);
	$pagelist = new page(array('total'=>$total_count, 'perpage'=>$aset['row'],'alias'=>$aset['listpage'],'getarray'=>$_GET));
	$currenpage=$pagelist->nowindex;
	$aset['start']=($currenpage-1)*$aset['row'];
	$smarty->assign('page',$pagelist->show(3));
	$smarty->assign('total',$total_count);
}
$limit=" LIMIT ".abs($aset['start']).','.$aset['row'];
$result = $db->query("SELECT * FROM ".table('help')." ".$wheresql.$orderbysql.$limit);
$list= array();
while($row = $db->fetch_array($result))
{
	$row['title_']=$row['title'];
	$row['title']=cut_str($row['title'],$aset['titlelen'],0,$aset['dot']);
	$row['url'] = url_rewrite($aset['showname'],array('id'=>$row['id']));
	$row['content']=str_replace('&nbsp;','',$row['content']);
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