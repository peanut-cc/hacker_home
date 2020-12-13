<?php
/*
 * 74cms 企业会员中心
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"promotion");
if ($act=='tpl')
{
		if (empty($company_profile['companyname']))
		{
		$link[0]['text'] = "填写企业资料";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("请先填写您的企业资料！",1,$link);
		}
	$smarty->assign('title','选择模版 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('comtpl',get_comtpl());
	if ($company_profile['tpl']=="")
	{
	$company_profile['tpl']=$_CFG['tpl_company'];
	}
	if($_CFG['operation_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);//获取会员套餐
		$smarty->assign('setmeal',$setmeal);
	}
	$smarty->assign('mytpl',$company_profile['tpl']);
	$smarty->assign('com_url',url_rewrite('QS_companyshow',array('id'=>$company_profile['id']),false));
	$smarty->display('member_company/company_tpl.htm'); 
}
elseif ($act=='tpl_save')
{
	$seltpl=trim($_POST['tpl']);
	if ($company_profile['tpl']=="")
	{
	$company_profile['tpl']=$_CFG['tpl_company'];
	}
	if ($company_profile['tpl']==$seltpl)
	{
	showmsg("设置成功！",2);
	}
	$comtpl=get_comtpl_one($seltpl);
	if (empty($comtpl))
	{
		showmsg("模版选择错误",0);
	}
	if($_CFG['operation_mode']=='1'){
		$user_points=get_user_points($_SESSION['uid']);
		if ($comtpl['tpl_val']>$user_points)
		{
			$link[0]['text'] = "返回上一页";
			$link[0]['href'] = 'javascript:history.go(-1)';
			$link[1]['text'] = "充值积分";
			$link[1]['href'] = 'company_service.php?act=order_add';
			showmsg("你的".$_CFG['points_byname']."不够进行此次操作，请先充值！",1,$link);
		}
	}elseif($_CFG['operation_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);//获取会员套餐
		$link[0]['text'] = "返回上一页";
		$link[0]['href'] = 'javascript:history.go(-1)';
		$link[1]['text'] = "重新开通服务";
		$link[1]['href'] = 'company_service.php?act=setmeal_list';
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			showmsg("您的服务已经到期，请重新开通",1,$link);
		}
		if ($setmeal['change_templates']=='0')
		{
			showmsg("你的套餐{$setmeal['setmeal_name']},没有自由切换模板的权限，请尽快开通新套餐",1,$link);
		}
	}
	$setsqlarr['tpl']=$seltpl;
	updatetable(table('company_profile'),$setsqlarr," uid='{$_SESSION['uid']}'");
	updatetable(table('jobs'),$setsqlarr," uid='{$_SESSION['uid']}'");
	updatetable(table('jobs_tmp'),$setsqlarr," uid='{$_SESSION['uid']}'");
 	if($_CFG['operation_mode']=='1'){
		if ($comtpl['tpl_val']>0)
		{
		report_deal($_SESSION['uid'],2,$comtpl['tpl_val']);
		$user_points=get_user_points($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"设置企业模版：{$comtpl['tpl_name']}，(-{$comtpl['tpl_val']})，(剩余:{$user_points})");
		}
	}elseif($_CFG['operation_mode']=='2'){
		write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"套餐：{$setmeal['setmeal_name']}，可自由切换模板，设置企业模版：{$comtpl['tpl_name']}");
	}
	write_memberslog($_SESSION['uid'],1,8007,$_SESSION['username'],"设置企业模版：{$comtpl['tpl_name']}");
	showmsg("设置成功！",2);
}
elseif ($act=='promotion')
{
	$promotionid=intval($_GET['promotionid']);
	$promotion=get_promotion_category_one($promotionid);//获取某种推广方案的详细信息
	$smarty->assign('title',"{$promotion['cat_name']} - 企业推广 - 企业会员中心 - {$_CFG['site_name']}");
	$smarty->assign('promotion',$promotion);
	$smarty->assign('time',time());
	$user_jobs=get_auditjobs($_SESSION['uid']);
	if (count($user_jobs)==0)
	{
	showmsg("推广失败，你没有发布职位或职位状态为不可见",1);
	}
	$smarty->assign('list',get_promotion($_SESSION['uid'],$promotionid));	//获取已经使用过某种推广的职位
	if ($_CFG['operation_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);//获取会员套餐
		$data=get_setmeal_promotion($_SESSION['uid'],$promotionid);//获取会员某种推广的剩余条数和天数，名称，总条数
		$smarty->assign('data',$data);
		$smarty->assign('setmeal',$setmeal);
	}
	$smarty->display('member_company/company_promotion.htm');
}
elseif ($act=='promotion_add')
{
	$promotionid=intval($_GET['promotionid']);
	$promotion=get_promotion_category_one($promotionid);
	$smarty->assign('title',"{$promotion['cat_name']} - 企业推广 - 企业会员中心 - {$_CFG['site_name']}");
	$smarty->assign('promotion',$promotion);
	$smarty->assign('jobs',get_auditjobs($_SESSION['uid']));
	if ($_CFG['operation_mode']=='2')
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);//获取会员套餐
		if($setmeal['endtime']<time() && $setmeal['endtime']<>'0'){
			$end=1;
			$smarty->assign('end',$end);//判断套餐是否到期
		}
		$data=get_setmeal_promotion($_SESSION['uid'],$promotionid);//获取会员某种推广的剩余条数和天数，名称，总条数
		$smarty->assign('setmeal',$setmeal);
		$smarty->assign('data',$data);
	}elseif($_CFG['operation_mode']=='1'){
		$smarty->assign('user_points',get_user_points($_SESSION['uid']));
	}
	$smarty->display('member_company/company_promotion_add.htm');
}
elseif ($act=='promotion_add_save')
{
	$jobsid=intval($_POST['jobsid']);
	$days=intval($_POST['days']);
	if ($jobsid>0 && $days>0)
	{
		$pro_cat=get_promotion_category_one(intval($_POST['promotionid']));
		if($_CFG['operation_mode']=='1'){
			if ($pro_cat['cat_points']>0)
			{
				$points=$pro_cat['cat_points']*$days;
				$user_points=get_user_points($_SESSION['uid']);
				if ($points>$user_points)
				{
				$link[0]['text'] = "返回上一页";
				$link[0]['href'] = 'javascript:history.go(-1)';
				$link[1]['text'] = "充值积分";
				$link[1]['href'] = 'company_service.php?act=order_add';
				showmsg("你的".$_CFG['points_byname']."不够进行此次操作，请先充值！",1,$link);
				}
			}
		}elseif($_CFG['operation_mode']=='2'){
			$setmeal=get_setmeal_promotion($_SESSION['uid'],intval($_POST['promotionid']));//获取会员套餐
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0){
				$link[0]['text'] = "返回上一页";
				$link[0]['href'] = 'javascript:history.go(-1)';
				$link[1]['text'] = "重新开通服务";
				$link[1]['href'] = 'company_service.php?act=setmeal_list';
				showmsg("你的套餐已到期或套餐内剩余{$pro_cat['cat_name']}不够，请尽快开通新套餐",1,$link);
			}
		}
		$info=get_promotion_one($jobsid,$_SESSION['uid'],$_POST['promotionid']);
		if (!empty($info))
		{
		showmsg("此职位正在推广中，请选择其他职位或其他方案",1);
		}
		$setsqlarr['cp_available']=1;
		$setsqlarr['cp_promotionid']=intval($_POST['promotionid']);
		$setsqlarr['cp_uid']=$_SESSION['uid'];
		$setsqlarr['cp_jobid']=$jobsid;
		$setsqlarr['cp_days']=$days;
		$setsqlarr['cp_starttime']=time();
		$setsqlarr['cp_endtime']=strtotime("{$days} day");
		$setsqlarr['cp_val']=$_POST['val'];
		if ($setsqlarr['cp_promotionid']=="4" && empty($setsqlarr['cp_val']))
		{
		showmsg("请选择颜色！",1);
		}
			if (inserttable(table('promotion'),$setsqlarr))
			{
				set_job_promotion($jobsid,$setsqlarr['cp_promotionid'],$_POST['val']);
				$jobs=get_jobs_one($jobsid,$_SESSION['uid']);
				if ($_CFG['operation_mode']=='1' && $pro_cat['cat_points']>0)
				{
					report_deal($_SESSION['uid'],2,$points);
					$user_points=get_user_points($_SESSION['uid']);
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"{$pro_cat['cat_name']}：<strong>{$jobs['jobs_name']}</strong>，推广 {$days} 天，(-{$points})，(剩余:{$user_points})");
				}elseif($_CFG['operation_mode']=='2'){
					$user_pname=trim($_POST['pro_name']);
					action_user_setmeal($_SESSION['uid'],$user_pname); //更新套餐中相应推广方式的条数
					$setmeal=get_user_setmeal($_SESSION['uid']);//获取会员套餐
					write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"{$pro_cat['cat_name']}：<strong>{$jobs['jobs_name']}</strong>，推广 {$days} 天，套餐内剩余{$pro_cat['cat_name']}条数：{$setmeal[$user_pname]}条。");//9002是套餐操作
				}
				write_memberslog($_SESSION['uid'],1,3004,$_SESSION['username'],"{$pro_cat['cat_name']}：<strong>{$jobs['jobs_name']}</strong>，推广 {$days} 天。");
				if ($_POST['golist'])
				{
				$link[0]['text'] = "返回职位列表";
				$link[0]['href'] = "company_jobs.php?act=jobs";
				$link[1]['text'] = "查看推广详情";
				$link[1]['href'] = "?act=promotion&promotionid={$_POST['promotionid']}";
				showmsg("推广成功！",2,$link);
				}
				else
				{
				$link[0]['text'] = "返回企业推广";
				$link[0]['href'] = "?act=promotion&promotionid={$_POST['promotionid']}";
				$link[1]['text'] = "职位列表";
				$link[1]['href'] = "company_jobs.php?act=jobs";
				showmsg("保存成功！",2,$link);
				}
			}
	}
	else
	{
	showmsg("参数错误",0);
	}
}
 elseif ($act=='promotion_edit')
{
	$jobsid=intval($_GET['jobsid']);
	$promotionid=intval($_GET['promotionid']);
	$info=get_promotion_one($jobsid,$_SESSION['uid'],$promotionid);
	$promotion=get_promotion_category_one($info['cp_promotionid']);
	$smarty->assign('title',"{$promotion['cat_name']} - 企业推广 - 企业会员中心 - {$_CFG['site_name']}");
	$smarty->assign('promotion',$promotion);
	$smarty->assign('info',$info);
	$smarty->assign('jobs',get_jobs_one($jobsid,$_SESSION['uid']));
	$smarty->assign('user_points',get_user_points($_SESSION['uid']));
	$smarty->display('member_company/company_promotion_edit.htm');
}
elseif ($act=='promotion_edit_save')
{
	$id=intval($_POST['id']);
	$promotionid=intval($_POST['promotionid']);
	$days=intval($_POST['days']);
	$jobid=intval($_POST['jobid']);
	$catinfo=get_promotion_category_one($promotionid);
	$points=$catinfo['cat_points']*$days;
	$user_points=get_user_points($_SESSION['uid']);
	if ($_CFG['operation_mode']=='1' && $points>$user_points)
	{
			$link[0]['text'] = "返回上一页";
			$link[0]['href'] = 'javascript:history.go(-1)';
			$link[1]['text'] = "充值积分";
			$link[1]['href'] = 'company_service.php?act=order_add';
			showmsg("你的".$_CFG['points_byname']."不够进行此次操作，请先充值！",1,$link);
	}
	if ($days>0)
	{
		if (intval($_POST['endtime'])>=time())
		{
		$setsqlarr['cp_endtime']=intval($_POST['endtime'])+($days*(60*60*24));
		}
		else
		{
		$setsqlarr['cp_endtime']=strtotime("".$days." day");
		}	
	}
	
	if ($promotionid=="4")
	{
		$setsqlarr['cp_val']=trim($_POST['val']);
		if (empty($setsqlarr['cp_val']))
		{
		showmsg("请选择颜色",1);
		}
	}
	if (!empty($setsqlarr))
	{
		if (!updatetable(table('promotion'),$setsqlarr," cp_id='{$id}' AND cp_uid='{$_SESSION['uid']}' ")) showmsg("保存失败！",0);
		if ($days>0)
		{			
			if ($_CFG['operation_mode']=='1' && $catinfo['cat_points']>0)
			{				
				report_deal($_SESSION['uid'],2,$points);
				$user_points=get_user_points($_SESSION['uid']);
				$jobs=get_jobs_one($jobid,$_SESSION['uid']);
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"延长推广有效期{$days}天，(推广类型：{$catinfo['cat_name']}，推广职位：{$jobs['jobs_name']})，(-{$points})，(剩余:{$user_points})");
				write_memberslog($_SESSION['uid'],1,3005,$_SESSION['username'],"延长职位推广{$days}天，(推广类型：{$catinfo['cat_name']}，推广职位ID：{$jobid})");
			}
		}
		if ($promotionid=="4")
		{
			$db->query("UPDATE ".table('jobs')." SET highlight='{$setsqlarr['cp_val']}' WHERE id='{$jobid}' ");
			$db->query("UPDATE ".table('jobs_tmp')." SET highlight='{$setsqlarr['cp_val']}' WHERE id='{$jobid}' ");
			write_memberslog($_SESSION['uid'],1,3005,$_SESSION['username'],"修改职位推广，(推广类型：{$catinfo['cat_name']}，推广职位ID：{$jobid})");
		}
		$link[0]['text'] = "返回企业推广";
		$link[0]['href'] = "?act=promotion&promotionid={$promotionid}";
		showmsg("修改成功！",2,$link);
		
	}
	else
	{
	 	showmsg("您没有做任何修改！",1);
	}	
}
elseif ($act=='promotion_del')
{
	$id=intval($_GET['id']);
	if ($n=promotion_del($id,$_SESSION['uid']))
	{
		showmsg("取消成功！",2);
	}
	else
	{
		showmsg("取消失败！",0);
	}
}
unset($smarty);
?>