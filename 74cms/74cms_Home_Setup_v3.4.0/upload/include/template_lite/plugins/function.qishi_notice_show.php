<?php
function tpl_function_qishi_notice_show($params, &$smarty)
{
global $db;
$arr=explode(',',$params['set']);
foreach($arr as $str)
{
$a=explode(':',$str);
	switch ($a[0])
	{
	case "����ID":
		$aset['id'] = $a[1];
		break;
	case "�б���":
		$aset['listname'] = $a[1];
		break;
	}
}
$aset=array_map("get_smarty_request",$aset);
$aset['id']=$aset['id']?intval($aset['id']):0;
$aset['listname']=$aset['listname']?$aset['listname']:"list";
unset($arr,$str,$a,$params);
$sql = "select * from ".table('notice')." WHERE  id=".intval($aset['id'])." AND  is_display=1 LIMIT 1";
$val=$db->getone($sql);
if (empty($val))
	{
			header("HTTP/1.1 404 Not Found"); 
			$smarty->display("404.htm");
			exit();
	}
	if ($val['seo_keywords']=="")
	{
	$val['keywords']=$val['title'];
	}
	else
	{
	$val['keywords']=$val['seo_keywords'];
	}
	if ($val['seo_description']=="")
	{
	$val['description']=cut_str(strip_tags($val['content']),60,0,"");
	}
	else
	{
	$val['description']=$val['seo_description'];
	}
$smarty->assign($aset['listname'],$val);
}
?>