<?php
function tpl_function_qishi_company_show($params, &$smarty)
{ 
	global $db,$_CFG;
	$arr=explode(',',$params['set']);
	foreach($arr as $str)
	{
	$a=explode(':',$str);
		switch ($a[0])
		{
		case "企业ID":
			$aset['id'] = $a[1];
			break;
		case "列表名":
			$aset['listname'] = $a[1];
			break;
		}
	}
		$aset=array_map("get_smarty_request",$aset);
		$aset['id']=$aset['id']?intval($aset['id']):0;
		$aset['listname']=$aset['listname']?$aset['listname']:"list";
		$wheresql.=" AND  user_status=1 ";
		$sql = "select * from ".table('company_profile')." WHERE  id='{$aset['id']}' {$wheresql} LIMIT  1";
		$profile=$db->getone($sql);
		if (empty($profile))
		{
			header("HTTP/1.1 404 Not Found"); 
			$smarty->display("404.htm");
			exit();
		}
		else
		{
		$profile['company_url']=url_rewrite('QS_companyshow',array('id'=>$profile['id']));
		$profile['company_profile']=$profile['contents'];
		$profile['description']=cut_str(strip_tags($profile['contents']),50,0,"...");
			if ($profile['logo'])
			{
			$profile['logo']=$_CFG['site_dir']."data/logo/".$profile['logo'];
			}
			else
			{
			$profile['logo']=$_CFG['site_dir']."data/logo/no_logo.gif";
			}
		}
	$smarty->assign($aset['listname'],$profile);
}
?>