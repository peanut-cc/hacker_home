<?php
function tpl_function_qishi_news_property($params, &$smarty)
{
global $db;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "�б���":
		$aset['listname'] = $a[1];
		break;
	case "���Ƴ���":
		$aset['titlelen'] = $a[1];
		break;
	case "��ַ�":
		$aset['dot'] = $a[1];
		break;
	case "����ID":
		$aset['ID'] = $a[1];
		break;
	}
}
if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
$aset['listname']=$aset['listname']?$aset['listname']:"list";
$aset['titlelen']=$aset['titlelen']?intval($aset['titlelen']):8;
if ($aset['ID'])
{
$wheresql=" WHERE id=".intval($aset['ID']);
}
$List = $db->getall("SELECT * FROM ".table('article_property')." ".$wheresql);
$smarty->assign($aset['listname'],$List);
}
?>