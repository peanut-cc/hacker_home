<?php
function tpl_function_qishi_jobs_contrast($params, &$smarty)
{
	global $db,$timestamp,$_CFG;
	$arr=explode(',',$params['set']);
	foreach($arr as $str)
	{
	$a=explode(':',$str);
		switch ($a[0])
		{
		case "职位ID":
			$aset['id'] = $a[1];
			break;
		case "列表名":
			$aset['listname'] = $a[1];
			break;
		}
	}
	$aset=array_map("get_smarty_request",$aset);
	$aset['listname']=$aset['listname']?$aset['listname']:"list";
	if (strpos($aset['id'],"-"))
	{
		$aset['id']=str_replace("-",",",$aset['id']);
		if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$aset['id'])) exit("err");		
	}
	else
	{
	exit();
	}

	$wheresql=" WHERE id IN ({$aset['id']})";
	$sql = "select * from ".table('jobs').$wheresql." LIMIT 5";
	$val=$db->getall($sql);
$smarty->assign($aset['listname'],$val);
}
?>