<?php
/*
 * 74cms ��ҵ��Ա����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/company_common.php');
$smarty->assign('leftmenu',"promotion");
if ($act=='tpl')
{
		if (empty($company_profile['companyname']))
		{
		$link[0]['text'] = "��д��ҵ����";
		$link[0]['href'] = 'company_info.php?act=company_profile';
		showmsg("������д������ҵ���ϣ�",1,$link);
		}
	$smarty->assign('title','ѡ��ģ�� - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('comtpl',get_comtpl());
	if ($company_profile['tpl']=="")
	{
	$company_profile['tpl']=$_CFG['tpl_company'];
	}
	if($_CFG['operation_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
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
	showmsg("���óɹ���",2);
	}
	$comtpl=get_comtpl_one($seltpl);
	if (empty($comtpl))
	{
		showmsg("ģ��ѡ�����",0);
	}
	if($_CFG['operation_mode']=='1'){
		$user_points=get_user_points($_SESSION['uid']);
		if ($comtpl['tpl_val']>$user_points)
		{
			$link[0]['text'] = "������һҳ";
			$link[0]['href'] = 'javascript:history.go(-1)';
			$link[1]['text'] = "��ֵ����";
			$link[1]['href'] = 'company_service.php?act=order_add';
			showmsg("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��",1,$link);
		}
	}elseif($_CFG['operation_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
		$link[0]['text'] = "������һҳ";
		$link[0]['href'] = 'javascript:history.go(-1)';
		$link[1]['text'] = "���¿�ͨ����";
		$link[1]['href'] = 'company_service.php?act=setmeal_list';
		if ($setmeal['endtime']<time() && $setmeal['endtime']<>"0")
		{					
			showmsg("���ķ����Ѿ����ڣ������¿�ͨ",1,$link);
		}
		if ($setmeal['change_templates']=='0')
		{
			showmsg("����ײ�{$setmeal['setmeal_name']},û�������л�ģ���Ȩ�ޣ��뾡�쿪ͨ���ײ�",1,$link);
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
		write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"������ҵģ�棺{$comtpl['tpl_name']}��(-{$comtpl['tpl_val']})��(ʣ��:{$user_points})");
		}
	}elseif($_CFG['operation_mode']=='2'){
		write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"�ײͣ�{$setmeal['setmeal_name']}���������л�ģ�壬������ҵģ�棺{$comtpl['tpl_name']}");
	}
	write_memberslog($_SESSION['uid'],1,8007,$_SESSION['username'],"������ҵģ�棺{$comtpl['tpl_name']}");
	showmsg("���óɹ���",2);
}
elseif ($act=='promotion')
{
	$promotionid=intval($_GET['promotionid']);
	$promotion=get_promotion_category_one($promotionid);//��ȡĳ���ƹ㷽������ϸ��Ϣ
	$smarty->assign('title',"{$promotion['cat_name']} - ��ҵ�ƹ� - ��ҵ��Ա���� - {$_CFG['site_name']}");
	$smarty->assign('promotion',$promotion);
	$smarty->assign('time',time());
	$user_jobs=get_auditjobs($_SESSION['uid']);
	if (count($user_jobs)==0)
	{
	showmsg("�ƹ�ʧ�ܣ���û�з���ְλ��ְλ״̬Ϊ���ɼ�",1);
	}
	$smarty->assign('list',get_promotion($_SESSION['uid'],$promotionid));	//��ȡ�Ѿ�ʹ�ù�ĳ���ƹ��ְλ
	if ($_CFG['operation_mode']=='2'){
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
		$data=get_setmeal_promotion($_SESSION['uid'],$promotionid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
		$smarty->assign('data',$data);
		$smarty->assign('setmeal',$setmeal);
	}
	$smarty->display('member_company/company_promotion.htm');
}
elseif ($act=='promotion_add')
{
	$promotionid=intval($_GET['promotionid']);
	$promotion=get_promotion_category_one($promotionid);
	$smarty->assign('title',"{$promotion['cat_name']} - ��ҵ�ƹ� - ��ҵ��Ա���� - {$_CFG['site_name']}");
	$smarty->assign('promotion',$promotion);
	$smarty->assign('jobs',get_auditjobs($_SESSION['uid']));
	if ($_CFG['operation_mode']=='2')
	{
		$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
		if($setmeal['endtime']<time() && $setmeal['endtime']<>'0'){
			$end=1;
			$smarty->assign('end',$end);//�ж��ײ��Ƿ���
		}
		$data=get_setmeal_promotion($_SESSION['uid'],$promotionid);//��ȡ��Աĳ���ƹ��ʣ�����������������ƣ�������
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
				$link[0]['text'] = "������һҳ";
				$link[0]['href'] = 'javascript:history.go(-1)';
				$link[1]['text'] = "��ֵ����";
				$link[1]['href'] = 'company_service.php?act=order_add';
				showmsg("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��",1,$link);
				}
			}
		}elseif($_CFG['operation_mode']=='2'){
			$setmeal=get_setmeal_promotion($_SESSION['uid'],intval($_POST['promotionid']));//��ȡ��Ա�ײ�
			$num=$setmeal['num'];
			if(($setmeal['endtime']<time() && $setmeal['endtime']<>'0') || $num<=0){
				$link[0]['text'] = "������һҳ";
				$link[0]['href'] = 'javascript:history.go(-1)';
				$link[1]['text'] = "���¿�ͨ����";
				$link[1]['href'] = 'company_service.php?act=setmeal_list';
				showmsg("����ײ��ѵ��ڻ��ײ���ʣ��{$pro_cat['cat_name']}�������뾡�쿪ͨ���ײ�",1,$link);
			}
		}
		$info=get_promotion_one($jobsid,$_SESSION['uid'],$_POST['promotionid']);
		if (!empty($info))
		{
		showmsg("��ְλ�����ƹ��У���ѡ������ְλ����������",1);
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
		showmsg("��ѡ����ɫ��",1);
		}
			if (inserttable(table('promotion'),$setsqlarr))
			{
				set_job_promotion($jobsid,$setsqlarr['cp_promotionid'],$_POST['val']);
				$jobs=get_jobs_one($jobsid,$_SESSION['uid']);
				if ($_CFG['operation_mode']=='1' && $pro_cat['cat_points']>0)
				{
					report_deal($_SESSION['uid'],2,$points);
					$user_points=get_user_points($_SESSION['uid']);
					write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �죬(-{$points})��(ʣ��:{$user_points})");
				}elseif($_CFG['operation_mode']=='2'){
					$user_pname=trim($_POST['pro_name']);
					action_user_setmeal($_SESSION['uid'],$user_pname); //�����ײ�����Ӧ�ƹ㷽ʽ������
					$setmeal=get_user_setmeal($_SESSION['uid']);//��ȡ��Ա�ײ�
					write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �죬�ײ���ʣ��{$pro_cat['cat_name']}������{$setmeal[$user_pname]}����");//9002���ײͲ���
				}
				write_memberslog($_SESSION['uid'],1,3004,$_SESSION['username'],"{$pro_cat['cat_name']}��<strong>{$jobs['jobs_name']}</strong>���ƹ� {$days} �졣");
				if ($_POST['golist'])
				{
				$link[0]['text'] = "����ְλ�б�";
				$link[0]['href'] = "company_jobs.php?act=jobs";
				$link[1]['text'] = "�鿴�ƹ�����";
				$link[1]['href'] = "?act=promotion&promotionid={$_POST['promotionid']}";
				showmsg("�ƹ�ɹ���",2,$link);
				}
				else
				{
				$link[0]['text'] = "������ҵ�ƹ�";
				$link[0]['href'] = "?act=promotion&promotionid={$_POST['promotionid']}";
				$link[1]['text'] = "ְλ�б�";
				$link[1]['href'] = "company_jobs.php?act=jobs";
				showmsg("����ɹ���",2,$link);
				}
			}
	}
	else
	{
	showmsg("��������",0);
	}
}
 elseif ($act=='promotion_edit')
{
	$jobsid=intval($_GET['jobsid']);
	$promotionid=intval($_GET['promotionid']);
	$info=get_promotion_one($jobsid,$_SESSION['uid'],$promotionid);
	$promotion=get_promotion_category_one($info['cp_promotionid']);
	$smarty->assign('title',"{$promotion['cat_name']} - ��ҵ�ƹ� - ��ҵ��Ա���� - {$_CFG['site_name']}");
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
			$link[0]['text'] = "������һҳ";
			$link[0]['href'] = 'javascript:history.go(-1)';
			$link[1]['text'] = "��ֵ����";
			$link[1]['href'] = 'company_service.php?act=order_add';
			showmsg("���".$_CFG['points_byname']."�������д˴β��������ȳ�ֵ��",1,$link);
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
		showmsg("��ѡ����ɫ",1);
		}
	}
	if (!empty($setsqlarr))
	{
		if (!updatetable(table('promotion'),$setsqlarr," cp_id='{$id}' AND cp_uid='{$_SESSION['uid']}' ")) showmsg("����ʧ�ܣ�",0);
		if ($days>0)
		{			
			if ($_CFG['operation_mode']=='1' && $catinfo['cat_points']>0)
			{				
				report_deal($_SESSION['uid'],2,$points);
				$user_points=get_user_points($_SESSION['uid']);
				$jobs=get_jobs_one($jobid,$_SESSION['uid']);
				write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"�ӳ��ƹ���Ч��{$days}�죬(�ƹ����ͣ�{$catinfo['cat_name']}���ƹ�ְλ��{$jobs['jobs_name']})��(-{$points})��(ʣ��:{$user_points})");
				write_memberslog($_SESSION['uid'],1,3005,$_SESSION['username'],"�ӳ�ְλ�ƹ�{$days}�죬(�ƹ����ͣ�{$catinfo['cat_name']}���ƹ�ְλID��{$jobid})");
			}
		}
		if ($promotionid=="4")
		{
			$db->query("UPDATE ".table('jobs')." SET highlight='{$setsqlarr['cp_val']}' WHERE id='{$jobid}' ");
			$db->query("UPDATE ".table('jobs_tmp')." SET highlight='{$setsqlarr['cp_val']}' WHERE id='{$jobid}' ");
			write_memberslog($_SESSION['uid'],1,3005,$_SESSION['username'],"�޸�ְλ�ƹ㣬(�ƹ����ͣ�{$catinfo['cat_name']}���ƹ�ְλID��{$jobid})");
		}
		$link[0]['text'] = "������ҵ�ƹ�";
		$link[0]['href'] = "?act=promotion&promotionid={$promotionid}";
		showmsg("�޸ĳɹ���",2,$link);
		
	}
	else
	{
	 	showmsg("��û�����κ��޸ģ�",1);
	}	
}
elseif ($act=='promotion_del')
{
	$id=intval($_GET['id']);
	if ($n=promotion_del($id,$_SESSION['uid']))
	{
		showmsg("ȡ���ɹ���",2);
	}
	else
	{
		showmsg("ȡ��ʧ�ܣ�",0);
	}
}
unset($smarty);
?>