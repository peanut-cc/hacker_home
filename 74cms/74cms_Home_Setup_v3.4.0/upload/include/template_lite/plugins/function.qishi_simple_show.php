<?php
function tpl_function_qishi_simple_show($params, &$smarty)
{
global $db;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "ID":
		$aset['id'] = $a[1];
		break;
	case "СаБэУћ":
		$aset['listname'] = $a[1];
		break;
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['id']=$aset['id']?intval($aset['id']):0;
$aset['listname']=$aset['listname']?$aset['listname']:"list";
unset($arr,$str,$a,$params);
$sql = "select * from ".table('simple')." WHERE  id='{$aset['id']}' AND audit=1  LIMIT 1";
$val=$db->getone($sql);
if (empty($val))
{
	header("HTTP/1.1 404 Not Found"); 
	$smarty->display("404.htm");
	exit();
}
$val['keywords']="{$val['jobname']} {$val['comname']} ";
$val['description']=cut_str(strip_tags($val['detailed']),60,0,"");
$smarty->assign($aset['listname'],$val);
}
?>