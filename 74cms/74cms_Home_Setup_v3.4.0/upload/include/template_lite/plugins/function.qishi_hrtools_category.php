<?php
function tpl_function_qishi_hrtools_category($params, &$smarty)
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
$wheresql=" WHERE c_id='".intval($aset['ID'])."' ";
}
$result = $db->query("SELECT * FROM ".table('hrtools_category')." ".$wheresql." ORDER BY c_order DESC,c_id ASC");
while($row = $db->fetch_array($result))
{
	$row['url'] = url_rewrite('QS_hrtoolslist',array('id'=>$row['c_id']));
	$list[] = $row;
}
$smarty->assign($aset['listname'],$list);
}
?>