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
$smarty->assign('leftmenu',"jobs");
if ($act=='jobs')
{
	$jobtype=intval($_GET['jobtype']);
	$wheresql=" WHERE uid='{$_SESSION['uid']}' ";
	$orderby=" order by refreshtime desc";
	if($jobtype===1)
	{
	$tabletype="jobs";
	}
	elseif($jobtype===2)
	{
	$tabletype="jobs_tmp";
	}
	else
	{
	$tabletype="all";
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	if ($tabletype=="all")
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobs').$wheresql." UNION ALL  SELECT COUNT(*) AS num FROM ".table('jobs_tmp').$wheresql;
	}
	else
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table($tabletype).$wheresql;
	}
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$offset=($page->nowindex-1)*$perpage;
	$smarty->assign('title','职位管理 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('audit',$audit);
	if ($tabletype=="all")
	{
	$sql="SELECT * FROM ".table('jobs').$wheresql." UNION ALL SELECT * FROM ".table('jobs_tmp').$wheresql.$orderby;
	}
	else
	{
	$sql="SELECT * FROM ".table($tabletype).$wheresql.$orderby;
	}
	$total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')." WHERE uid='{$_SESSION['uid']}'");
	$total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE uid='{$_SESSION['uid']}'");
	$total[2]=$total[0]+$total[1];
	$smarty->assign('total',$total);
	$smarty->assign('jobs',get_jobs($offset,$perpage,$sql,true));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('points_rule',get_cache('points_rule'));
	$smarty->display('member_company/company_jobs.htm');
}
 elseif ($act=='addjobs')
{
		$smarty->assign('user',$user);
		if ($company_profile['companyname'])
		{
			$_SESSION['addrand']=rand(1000,5000);
			$smarty->assign('addrand',$_SESSION['addrand']);
			$smarty->assign('title','发布职位 - 企业会员中心 - '.$_CFG['site_name']);
			$smarty->assign('company_profile',$company_profile);
			if ($_CFG['operation_mode']=="2")
			{
				$setmeal=get_user_setmeal($_SESSION['uid']);
				$smarty->assign('setmeal',$setmeal);
				$smarty->assign('add_mode',2);
			}
			elseif ($_CFG['operation_mode']=="1")
			{
				$smarty->assign('points_total',get_user_points($_SESSION['uid']));
				$smarty->assign('points',get_cache('points_rule'));
				$smarty->assign('add_mode',1);
			}
			$captcha=get_cache('captcha');
			$smarty->assign('verify_addjob',$captcha['verify_addjob']);
			$smarty->display('member_company/company_addjobs.htm');
		}
		else
		{
		$link[0]['text'] = "完善企业资料";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("为了达到更好的招聘效果，请先完善您的企业资料！",1,$link);
		}
}
elseif ($act=='addjobs_save')
{
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_addjob']=='1' && empty($postcaptcha))
	{
		showmsg("请填写验证码",1);
 	}
	if ($captcha['verify_addjob']=='1' && strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("验证码错误",1);
	}
	$add_mode=trim($_POST['add_mode']);
	$days=intval($_POST['days']);
	if ($days<$_CFG['company_add_days_min'])
	{
	showmsg("有效时间最少为 ".$_CFG['company_add_days_min']." 天！",1);
	}
	if ($_CFG['operation_mode']=='1')
	{
					$points_rule=get_cache('points_rule');
					$user_points=get_user_points($_SESSION['uid']);
					$total=0;
					if ($points_rule['jobs_add']['type']=="2" && $points_rule['jobs_add']['value']>0)
					{
					$total=$points_rule['jobs_add']['value'];
					}
					if ($points_rule['jobs_daily']['type']=="2" && $points_rule['jobs_daily']['value']>0)
					{
					$total=$total+($days*$points_rule['jobs_daily']['value']);
					}
					if ($total>$user_points)
					{
					$link[0]['text'] = "立即充值";
					$link[0]['href'] = 'company_service.php?act=order_add';
					$link[1]['text'] = "会员中心首页";
					$link[1]['href'] = 'company_index.php?act=';
					showmsg("你的".$_CFG['points_byname']."不足，请充值后再发布！",0,$link);
					}
					$setsqlarr['setmeal_deadline']=0;
	}
	elseif ($_CFG['operation_mode']=='2')
	{
					$link[0]['text'] = "立即开通服务";
					$link[0]['href'] = 'company_service.php?act=setmeal_list';
					$link[1]['text'] = "会员中心首页";
					$link[1]['href'] = 'company_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					showmsg("您的服务已经到期，请重新开通",1,$link);
				}
				if ($setmeal['jobs_ordinary']<=0)
				{
					showmsg("当前发布的职位已经超过了最大限制，请升级服务套餐！",1,$link);
				}
				$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
				$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
				$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
	}
	$addrand=intval($_POST['addrand']);
	if($_SESSION['addrand']==$addrand){
	unset($_SESSION['addrand']);
	$setsqlarr['add_mode']=intval($add_mode);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['companyname']=$company_profile['companyname'];
	$setsqlarr['company_id']=$company_profile['id'];
	$setsqlarr['company_addtime']=$company_profile['addtime'];
	$setsqlarr['company_audit']=$company_profile['audit'];
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('您没有填写职位名称！',1);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('您没有填写职位描述！',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('请选择职位类别！',1);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['trade']=$company_profile['trade'];
	$setsqlarr['trade_cn']=$company_profile['trade_cn'];
	$setsqlarr['scale']=$company_profile['scale'];
	$setsqlarr['scale_cn']=$company_profile['scale_cn'];
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('请选择工作地区！',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['street']=$company_profile['street'];
	$setsqlarr['street_cn']=$company_profile['street_cn'];
	$setsqlarr['officebuilding']=$company_profile['officebuilding'];
	$setsqlarr['officebuilding_cn']=$company_profile['officebuilding_cn'];	
	$setsqlarr['education']=intval($_POST['education']);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['deadline']=strtotime("".intval($_POST['days'])." day");
	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['subsite_id']=intval($_CFG['subsite_id']);
	$setsqlarr['tpl']=$company_profile['tpl'];
	$setsqlarr['map_x']=$company_profile['map_x'];
	$setsqlarr['map_y']=$company_profile['map_y'];
	if ($company_profile['audit']=="1")
	{
	$setsqlarr['audit']=intval($_CFG['audit_verifycom_addjob']);
	}
	else
	{
	$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_addjob']);
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('您没有填写联系人！',1);
	$setsqlarr_contact['qq']=trim($_POST['qq']);
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('您没有填写联系电话！',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('您没有填写联系地址！',1);
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('您没有填写联系邮箱！',1);
	$setsqlarr_contact['notify']=intval($_POST['notify']);
	
	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	$setsqlarr_contact['qq_show']=intval($_POST['qq_show']);
	
	//添加职位信息
	$pid=inserttable(table('jobs'),$setsqlarr,true);
	empty($pid)?showmsg("添加失败！",0):'';
	//添加联系方式
	$setsqlarr_contact['pid']=$pid;
	!inserttable(table('jobs_contact'),$setsqlarr_contact)?showmsg("添加失败！",0):'';
	if ($_CFG['operation_mode']=='1')
	{
		if ($points_rule['jobs_add']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['jobs_add']['type'],$points_rule['jobs_add']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_add']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"发布了职位：<strong>{$setsqlarr['jobs_name']}</strong>，({$operator}{$points_rule['jobs_add']['value']})，(剩余:{$user_points})");
		}
		if (intval($_POST['days'])>0 && $points_rule['jobs_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['jobs_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['jobs_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"发布普通职位:<strong>{$_POST['jobs_name']}</strong>，有效期为{$_POST['days']}天，({$operator}{$points_day})，(剩余:{$user_points})");
		}
	}
	elseif ($_CFG['operation_mode']=='2')
	{
		action_user_setmeal($_SESSION['uid'],"jobs_ordinary");
		$setmeal=get_user_setmeal($_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"发布普通职位:<strong>{$_POST['jobs_name']}</strong>，还可以发布普通职位:<strong>{$setmeal['jobs_ordinary']}</strong>条");
	}
	$searchtab['id']=$pid;
	$searchtab['uid']=$setsqlarr['uid'];
	$searchtab['subsite_id']=$setsqlarr['subsite_id'];
	$searchtab['recommend']=$setsqlarr['recommend'];
	$searchtab['emergency']=$setsqlarr['emergency'];
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['trade']=$setsqlarr['trade'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];	
	$searchtab['street']=$company_profile['street'];
	$searchtab['officebuilding']=$company_profile['officebuilding'];	
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	$searchtab['refreshtime']=$setsqlarr['refreshtime'];
	$searchtab['scale']=$setsqlarr['scale'];	
	//
	inserttable(table('jobs_search_wage'),$searchtab);
	inserttable(table('jobs_search_scale'),$searchtab);
	//
	$searchtab['map_x']=$setsqlarr['map_x'];
	$searchtab['map_y']=$setsqlarr['map_y'];
	inserttable(table('jobs_search_rtime'),$searchtab);
	unset($searchtab['map_x'],$searchtab['map_y']);
	//
	$searchtab['stick']=$setsqlarr['stick'];
	inserttable(table('jobs_search_stickrtime'),$searchtab);
	unset($searchtab['stick']);
	//
	$searchtab['click']=$setsqlarr['click'];
	inserttable(table('jobs_search_hot'),$searchtab);
	unset($searchtab['click']);
	//
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$setsqlarr['companyname'];
	$searchtab['map_x']=$setsqlarr['map_x'];
	$searchtab['map_y']=$setsqlarr['map_y'];
	inserttable(table('jobs_search_key'),$searchtab);
	unset($searchtab);
	//
		$tag=explode('|',$setsqlarr['tag']);
		$tagindex=1;
		$tagsql['tag1']=$tagsql['tag2']=$tagsql['tag3']=$tagsql['tag4']=$tagsql['tag5']=0;
		if (!empty($tag) && is_array($tag))
		{
			foreach($tag as $v)
			{
			$vid=explode(',',$v);
			$tagsql['tag'.$tagindex]=intval($vid[0]);
			$tagindex++;
			}
		}
		$tagsql['id']=$pid;
		$tagsql['uid']=$setsqlarr['uid'];
		$tagsql['subsite_id']=$setsqlarr['subsite_id'];
		$tagsql['category']=$setsqlarr['category'];
		$tagsql['subclass']=$setsqlarr['subclass'];
		$tagsql['district']=$setsqlarr['district'];
		$tagsql['sdistrict']=$setsqlarr['sdistrict'];	
		inserttable(table('jobs_search_tag'),$tagsql);
	distribution_jobs($pid,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],1,2001,$_SESSION['username'],"发布了职位：{$setsqlarr['jobs_name']}");
	}
	$link[0]['text'] = "已发布职位";
	$link[0]['href'] = '?act=jobs';
	$link[1]['text'] = "继续发布职位";
	$link[1]['href'] = '?act=addjobs';
	$link[3]['text'] = "会员中心首页";
	$link[3]['href'] = "company_index.php?";
	showmsg("发布成功！",2,$link);
}
elseif ($act=='jobs_perform')
{
	$yid =!empty($_POST['y_id'])?$_POST['y_id']:$_GET['y_id'];
    $jobs_num=count($yid);
	if (empty($yid))
	{
	showmsg("你没有选择职位！",1);
	}
	$refresh=!empty($_POST['refresh'])?$_POST['refresh']:$_GET['refresh'];
	$delete=!empty($_POST['delete'])?$_POST['delete']:$_GET['delete'];
    if ($refresh)
	{
		//积分模式
		if($_CFG['operation_mode']=='1'){
			//限制刷新时间
			//最经一次的刷新时间
			$refrestime=get_last_refresh_date($_SESSION['uid'],"1001");
			$duringtime=time()-$refrestime['max(addtime)'];
			$space = $_CFG['com_pointsmode_refresh_space']*60;
			$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001");
			if($_CFG['com_pointsmode_refresh_time']!=0&&($refresh_time['count(*)']>=$_CFG['com_pointsmode_refresh_time']))
			{
			showmsg("每天最多只能刷新".$_CFG['com_pointsmode_refresh_time']."次,您今天已超过最大刷新次数限制！",2);	
			}
			elseif($duringtime<=$space){
			showmsg($_CFG['com_pointsmode_refresh_space']."分钟内不能重复刷新职位！",2);
			}
			else 
			{
				$points_rule=get_cache('points_rule');
				if($points_rule['jobs_refresh']['value']>0)
				{
					$user_points=get_user_points($_SESSION['uid']);
					$total_point=$jobs_num*$points_rule['jobs_refresh']['value'];
					if ($total_point>$user_points && $points_rule['jobs_refresh']['type']=="2")
					{
							$link[0]['text'] = "返回上一页";
							$link[0]['href'] = 'javascript:history.go(-1)';
							$link[1]['text'] = "立即充值";
							$link[1]['href'] = 'company_service.php?act=order_add';
					showmsg("您的".$_CFG['points_byname']."不足，请先充值！",0,$link);
					}
					report_deal($_SESSION['uid'],$points_rule['jobs_refresh']['type'],$total_point);
					$user_points=get_user_points($_SESSION['uid']);
					$operator=$points_rule['jobs_refresh']['type']=="1"?"+":"-";
					write_memberslog($_SESSION['uid'],1,2004,$_SESSION['username'],"刷新了{$jobs_num}条职位，({$operator}{$total_point})，(剩余:{$user_points})");
				}
			}
		}	
		//套餐模式
		elseif($_CFG['operation_mode']=='2') 
		{
			//限制刷新时间
			//最经一次的刷新时间
			$link[0]['text'] = "立即开通服务";
			$link[0]['href'] = 'company_service.php?act=setmeal_list';
			$link[1]['text'] = "会员中心首页";
			$link[1]['href'] = 'company_index.php?act=';
			$setmeal=get_user_setmeal($_SESSION['uid']);
			if (empty($setmeal))
			{					
				showmsg("您还没有开通服务，请开通",1,$link);
			}
			elseif ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
			{					
				showmsg("您的服务已经到期，请重新开通",1,$link);
			}
			else
			{
				$refrestime=get_last_refresh_date($_SESSION['uid'],"1001");
				$duringtime=time()-$refrestime['max(addtime)'];
				$space = $setmeal['refresh_jobs_space']*60;
				$refresh_time = get_today_refresh_times($_SESSION['uid'],"1001");
				if($setmeal['refresh_jobs_time']!=0&&($refresh_time['count(*)']>=$setmeal['refresh_jobs_time']))
				{
				showmsg("每天最多只能刷新".$setmeal['refresh_jobs_time']."次,您今天已超过最大刷新次数限制！",2);
				}
				elseif($duringtime<=$space){
				showmsg($setmeal['refresh_jobs_space']."分钟内不能重复刷新职位！",2);	
				}
			}
		}
		refresh_jobs($yid,$_SESSION['uid']);
		write_memberslog($_SESSION['uid'],1,2004,$_SESSION['username'],"刷新职位");	
		write_refresh_log($_SESSION['uid'],1001);			
		showmsg("刷新职位成功！",2);
	}
	elseif ($delete)
	{
		if($n=del_jobs($yid,$_SESSION['uid']))
		{
			showmsg("删除成功！共删除 {$n} 行",2);
		}
		else
		{
		showmsg("删除失败！",0);
		}
	}
	elseif (!empty($_POST['display1']))
	{
	activate_jobs($yid,1,$_SESSION['uid']);
	showmsg("设置成功！",2);
	}
	elseif (!empty($_POST['display2']))
	{
	activate_jobs($yid,2,$_SESSION['uid']);
	showmsg("设置成功！",2);
	}
}
elseif ($act=='editjobs')
{
	$jobs=get_jobs_one(intval($_GET['id']),$_SESSION['uid']);
	if (empty($jobs)) showmsg("参数错误！",1);
	$smarty->assign('user',$user);
	$smarty->assign('title','修改职位 - 企业会员中心 - '.$_CFG['site_name']);
	$smarty->assign('points_total',get_user_points($_SESSION['uid']));
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->assign('jobs',$jobs);
	$smarty->display('member_company/company_editjobs.htm');
}
elseif ($act=='editjobs_save')
{
	$id=intval($_POST['id']);
	$add_mode=trim($_POST['add_mode']);
	$days=intval($_POST['days']);
	if ($_CFG['operation_mode']=='1')
	{
					$points_rule=get_cache('points_rule');
					$user_points=get_user_points($_SESSION['uid']);
					$total=0;
					if($points_rule['jobs_edit']['type']=="2" && $points_rule['jobs_edit']['value']>0)
					{
					$total=$points_rule['jobs_edit']['value'];
					}
					if($points_rule['jobs_daily']['type']=="2" && $points_rule['jobs_daily']['value']>0)
					{
					$total=$total+($days*$points_rule['jobs_daily']['value']);
					}
					if ($total>$user_points)
					{
					$link[0]['text'] = "返回上一页";
					$link[0]['href'] = 'javascript:history.go(-1)';
					$link[1]['text'] = "立即充值";
					$link[1]['href'] = 'company_service.php?act=order_add';
					showmsg("你的".$_CFG['points_byname']."不足，请充值后再发布！",0,$link);
					}
	}
	elseif ($_CFG['operation_mode']=='2')
	{
					$link[0]['text'] = "立即开通服务";
					$link[0]['href'] = 'company_service.php?act=setmeal_list';
					$link[1]['text'] = "会员中心首页";
					$link[1]['href'] = 'company_index.php?act=';
				$setmeal=get_user_setmeal($_SESSION['uid']);
				if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
				{					
					showmsg("您的套餐已经到期，请重新开通",1,$link);
				}
	}
	$setsqlarr['jobs_name']=!empty($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('您没有填写职位名称！',1);
	$setsqlarr['contents']=!empty($_POST['contents'])?trim($_POST['contents']):showmsg('您没有填写职位描述！',1);
	check_word($_CFG['filter'],$_POST['contents'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['category']=!empty($_POST['category'])?intval($_POST['category']):showmsg('请选择职位类别！',1);
	$setsqlarr['subclass']=trim($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['district']=!empty($_POST['district'])?intval($_POST['district']):showmsg('请选择工作地区！',1);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['education']=intval($_POST['education']);		
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);		
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);		
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	if ($_CFG['operation_mode']=='1'){
		$setsqlarr['setmeal_deadline']=0;
		$setsqlarr['add_mode']=1;
	}elseif($_CFG['operation_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);
		$setsqlarr['setmeal_deadline']=$setmeal['endtime'];
		$setsqlarr['setmeal_id']=$setmeal['setmeal_id'];
		$setsqlarr['setmeal_name']=$setmeal['setmeal_name'];
		$setsqlarr['add_mode']=2;
	}
	if ($days>0)
	{
		if (intval($_POST['olddeadline'])>=time())
		{
			 $setsqlarr['deadline']=intval($_POST['olddeadline'])+($days*(60*60*24));
		}
		else
		{
			 $setsqlarr['deadline']=strtotime("{$days} day");
		}
	}
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$setsqlarr['subsite_id']=intval($_CFG['subsite_id']);
	if ($company_profile['audit']=="1")
	{
	$_CFG['audit_verifycom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_verifycom_editjob']):'';
	}
	else
	{
	$_CFG['audit_unexaminedcom_editjob']<>"-1"?$setsqlarr['audit']=intval($_CFG['audit_unexaminedcom_editjob']):'';
	}
	$setsqlarr_contact['contact']=!empty($_POST['contact'])?trim($_POST['contact']):showmsg('您没有填写联系人！',1);
	$setsqlarr_contact['qq']=trim($_POST['qq']);
	$setsqlarr_contact['telephone']=!empty($_POST['telephone'])?trim($_POST['telephone']):showmsg('您没有填写联系电话！',1);
	check_word($_CFG['filter'],$_POST['telephone'])?showmsg($_CFG['filter_tips'],0):'';
	$setsqlarr_contact['address']=!empty($_POST['address'])?trim($_POST['address']):showmsg('您没有填写联系地址！',1);
	$setsqlarr_contact['email']=!empty($_POST['email'])?trim($_POST['email']):showmsg('您没有填写联系邮箱！',1);
	$setsqlarr_contact['notify']=trim($_POST['notify']);
	
  	$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	$setsqlarr_contact['qq_show']=intval($_POST['qq_show']);
 		
	if (!updatetable(table('jobs'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("保存失败！",0);
	if (!updatetable(table('jobs_tmp'), $setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}' ")) showmsg("保存失败！",0);
	if (!updatetable(table('jobs_contact'), $setsqlarr_contact," pid='{$id}' ")) showmsg("保存失败！",0);
	if ($_CFG['operation_mode']=='1')
	{
		if ($points_rule['jobs_edit']['value']>0)
		{
		report_deal($_SESSION['uid'],$points_rule['jobs_edit']['type'],$points_rule['jobs_edit']['value']);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_edit']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"修改职位：<strong>{$setsqlarr['jobs_name']}</strong>，({$operator}{$points_rule['jobs_edit']['value']})，(剩余:{$user_points})");
		}
		if ($days>0 && $points_rule['jobs_daily']['value']>0)
		{
		$points_day=intval($_POST['days'])*$points_rule['jobs_daily']['value'];
		report_deal($_SESSION['uid'],$points_rule['jobs_daily']['type'],$points_day);
		$user_points=get_user_points($_SESSION['uid']);
		$operator=$points_rule['jobs_daily']['type']=="1"?"+":"-";
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"延长职位({$_POST['jobs_name']})有效期为{$_POST['days']}天，({$operator}{$points_day})，(剩余:{$user_points})");
		}
	}	 
	$link[0]['text'] = "职位列表";
	$link[0]['href'] = '?act=jobs';
	$link[1]['text'] = "查看修改结果";
	$link[1]['href'] = "?act=editjobs&id={$id}";
	$link[2]['text'] = "会员中心首页";
	$link[2]['href'] = "company_index.php";
	//
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['trade']=$setsqlarr['trade'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	//
	updatetable(table('jobs_search_wage'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_rtime'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_stickrtime'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_hot'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	updatetable(table('jobs_search_scale'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}'");
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$setsqlarr['companyname'];
	updatetable(table('jobs_search_key'),$searchtab," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	unset($searchtab);
		$tag=explode('|',$setsqlarr['tag']);
		$tagindex=1;
		foreach($tag as $v)
		{
		$vid=explode(',',$v);
		$tagsql['tag'.$tagindex]=intval($vid[0]);
		$tagindex++;
		}
		$tagsql['id']=$id;
		$tagsql['uid']=$_SESSION['uid'];
		$tagsql['category']=$setsqlarr['category'];
		$tagsql['subclass']=$setsqlarr['subclass'];
		$tagsql['district']=$setsqlarr['district'];
		$tagsql['sdistrict']=$setsqlarr['sdistrict'];
	updatetable(table('jobs_search_tag'),$tagsql," id='{$id}' AND uid='{$_SESSION['uid']}' ");
	distribution_jobs($id,$_SESSION['uid']);
	write_memberslog($_SESSION['uid'],$_SESSION['utype'],2002,$_SESSION['username'],"修改了职位：{$setsqlarr['jobs_name']}，职位ID：{$id}");
	showmsg("修改成功！",2,$link);
}
 unset($smarty);
?>