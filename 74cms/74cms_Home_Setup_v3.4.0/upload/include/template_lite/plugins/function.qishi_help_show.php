<?php
function tpl_function_qishi_help_show($params, &$smarty)
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
		case "�б���":
			$aset['listname'] = $a[1];
			break;
		}
	}
	$aset=array_map("get_smarty_request",$aset);
	$aset['listname']=$aset['listname']?$aset['listname']:"list";
	$sql = "select * from ".table('help')." WHERE  id=".intval($aset['id'])." LIMIT   1";
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