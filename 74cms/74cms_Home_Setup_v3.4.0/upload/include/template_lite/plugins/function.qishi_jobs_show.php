<?php
function tpl_function_qishi_jobs_show($params, &$smarty)
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
		case "描述长度":
			$aset['brieflylen'] = $a[1];
			break;
		case "填补字符":
			$aset['dot'] = $a[1];
			break;
		}
	}
	$aset=array_map("get_smarty_request",$aset);
	$aset['id']=$aset['id']?intval($aset['id']):0;
	$aset['brieflylen']=isset($aset['brieflylen'])?intval($aset['brieflylen']):0;
	$aset['listname']=$aset['listname']?$aset['listname']:"list";
	$wheresql=" WHERE id={$aset['id']} ";
	$sql = "select * from ".table('jobs').$wheresql." LIMIT 1";
	$val=$db->getone($sql);
	if (empty($val))
	{
		header("HTTP/1.1 404 Not Found"); 
		$smarty->display("404.htm");
		exit();
	}
	else
	{
			if ($val['setmeal_deadline']<time() && $val['setmeal_deadline']<>"0" && $val['add_mode']=="2")
			{
			$val['deadline']=$val['setmeal_deadline'];
			}
			$val['amount']=$val['amount']=="0"?'若干':$val['amount'];
			$val['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$val['id']));
			$profile=GetJobsCompanyProfile($val['company_id']);
			$val['company']=$profile;
			$val['expire']=sub_day($val['deadline'],time());	
			$wheresql=" WHERE company_uid='{$row['uid']}' AND jobs_id= '{$row['id']}'";
			$val['countresume']=$db->get_total("SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." WHERE jobs_id= '{$val['id']}'");
			if ($aset['brieflylen']>0)
			{
				$val['briefly']=cut_str(strip_tags($val['contents']),$aset['brieflylen'],0,$val['dot']);
			}
			else
			{
				$val['briefly']=strip_tags($val['contents']);
			}
			$val['refreshtime_cn']=daterange(time(),$val['refreshtime'],'Y-m-d',"#FF3300");
			$val['company_url']=url_rewrite('QS_companyshow',array('id'=>$val['company_id']));
			if ($val['company']['logo'])
			{
			$val['company']['logo']=$_CFG['site_dir']."data/logo/".$val['company']['logo'];
			}
			else
			{
			$val['company']['logo']=$_CFG['site_dir']."data/logo/no_logo.gif";
			}
			if ($val['tag'])
			{
				$tag=explode('|',$val['tag']);
				$taglist=array();
				if (!empty($tag) && is_array($tag))
				{
					foreach($tag as $t)
					{
					$tli=explode(',',$t);
					$taglist[]=array($tli[0],$tli[1]);
					}
				}
				$val['tag']=$taglist;
			}
			else
			{
			$val['tag']=array();
			}
	}
$smarty->assign($aset['listname'],$val);
}
function GetJobsCompanyProfile($id)
{
	global $db;
	$sql = "select * from ".table('company_profile')." where id=".intval($id)." LIMIT 1 ";
	return $db->getone($sql);
}
?>