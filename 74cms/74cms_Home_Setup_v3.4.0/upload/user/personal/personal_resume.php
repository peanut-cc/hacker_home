<?php
/*
 * 74cms ���˻�Ա����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');
$smarty->assign('leftmenu',"resume");
if ($act=='resume_show')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_GET['pid']);
	$resume=get_resume_basic($uid,$pid);
	if (empty($resume))
	{
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	showmsg('���������ڻ��Ѿ���ɾ����',1,$link);
	}
	$smarty->assign('random',mt_rand());
	$smarty->assign('time',time());
	$smarty->assign('user',$user);
	$smarty->assign('resume_basic',$resume);
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('title','Ԥ������ - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_resume.htm');
}
elseif ($act=='refresh')
{
	$refrestime=get_last_refresh_date($_SESSION['uid'],"2001");
		$duringtime=time()-$refrestime['max(addtime)'];
		$space = $_CFG['per_refresh_resume_space']*60;
		$refresh_time = get_today_refresh_times($_SESSION['uid'],"2001");
		if($_CFG['per_refresh_resume_time']!=0&&($refresh_time['count(*)']>=$_CFG['per_refresh_resume_time']))
		{
		showmsg("ÿ�����ֻ��ˢ��".$_CFG['per_refresh_resume_time']."��,�������ѳ������ˢ�´������ƣ�",2);	
		}
		elseif($duringtime<=$space){
		showmsg($_CFG['per_refresh_resume_space']."�����ڲ����ظ�ˢ�¼�����",2);
		}
		else 
		{
		refresh_resume($_SESSION['uid'])?showmsg('�����ɹ���',2):showmsg('����ʧ�ܣ�',0);
		}
}
//ɾ������
elseif ($act=='del_resume')
{
	if (empty($_REQUEST['y_id']))
	{
	showmsg('��û��ѡ�������',1);
	}
	else
	{
	del_resume($_SESSION['uid'],$_REQUEST['y_id'])?showmsg('ɾ���ɹ���',2):showmsg('ɾ��ʧ�ܣ�',0);
	}
}
//�����б�
elseif ($act=='resume_list')
{
	$tabletype=intval($_GET['tabletype']);
	if($tabletype===1)
	{
	$table="resume";
	}
	elseif($tabletype===2)
	{
	$table="resume_tmp";
	}
	else
	{
	$table="all";
	}
	$wheresql=" WHERE uid='".$_SESSION['uid']."' ";
	if ($table=="all")
	{
	$sql="SELECT * FROM ".table('resume').$wheresql." UNION ALL SELECT * FROM ".table('resume_tmp').$wheresql;
	}
	else
	{
	$sql="SELECT * FROM ".table($table).$wheresql;
	}
	$total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume')." WHERE uid='{$_SESSION['uid']}'");
	$total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')." WHERE uid='{$_SESSION['uid']}'");
	$total[2]=$total[0]+$total[1];
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$smarty->assign('total',$total);
	$smarty->assign('resume_list',get_resume_list($sql,12,true,true));
	$smarty->display('member_personal/personal_resume_list.htm');
}
//��������-������Ϣ
elseif ($act=='make1')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$smarty->assign('resume_basic',get_resume_basic($uid,$pid));
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('user',$user);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_resume',$captcha['verify_resume']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_personal/personal_resume_make1.htm');
}
//�������� -���������Ϣ
elseif ($act=='make1_save')
{
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_resume']=='1' && empty($postcaptcha) && intval($_REQUEST['pid'])===0)
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_resume']=='1' && intval($_REQUEST['pid'])===0 &&  strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):showmsg('����д�������ƣ�',1);
	$setsqlarr['fullname']=trim($_POST['fullname'])?trim($_POST['fullname']):showmsg('����д������',1);
	$setsqlarr['sex']=trim($_POST['sex'])?intval($_POST['sex']):showmsg('��ѡ���Ա�',1);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['birthdate']=intval($_POST['birthdate'])>1945?intval($_POST['birthdate']):showmsg('����ȷ��д�������',1);
	$setsqlarr['height']=intval($_POST['height']);
	$setsqlarr['marriage']=intval($_POST['marriage']);
	$setsqlarr['marriage_cn']=trim($_POST['marriage_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['householdaddress']=trim($_POST['householdaddress'])?trim($_POST['householdaddress']):showmsg('����д�������ڵأ�',1);	
	$setsqlarr['education']=intval($_POST['education']);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['tag']=trim($_POST['tag']);
	$setsqlarr['telephone']=trim($_POST['telephone'])?trim($_POST['telephone']):showmsg('����д��ϵ�绰��',1);
	$setsqlarr['email']=$user['email'];
	$setsqlarr['email_notify']=$_POST['email_notify']=="1"?1:0;
	$setsqlarr['address']=trim($_POST['address'])?trim($_POST['address']):showmsg('����дͨѶ��ַ��',1);
	$setsqlarr['website']=trim($_POST['website']);
	$setsqlarr['qq']=trim($_POST['qq']);
	$setsqlarr['refreshtime']=$timestamp;
	$setsqlarr['subsite_id']=intval($_CFG['subsite_id']);
	$setsqlarr['display_name']=intval($_CFG['resume_privacy']);	
	if (intval($_REQUEST['pid'])===0)
	{	
			$setsqlarr['audit']=intval($_CFG['audit_resume']);
			$total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume')." WHERE uid='{$_SESSION['uid']}'");
			$total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')." WHERE uid='{$_SESSION['uid']}'");
			$total[2]=$total[0]+$total[1];
			if ($total[2]>=intval($_CFG['resume_max']))
			{
			showmsg("�������Դ���{$_CFG['resume_max']} �ݼ���,�Ѿ�������������ƣ�",1);
			}
			else
			{
			$setsqlarr['addtime']=$timestamp;
			$pid=inserttable(table('resume'),$setsqlarr,1);
			if (empty($pid))showmsg("����ʧ�ܣ�",0);
			check_resume($_SESSION['uid'],$pid);
			write_memberslog($_SESSION['uid'],2,1101,$_SESSION['username'],"�����˼���");
			header("Location: ?act=make2&pid=".$pid);
			}
	}
	else
	{
		$_CFG['audit_edit_resume']!="-1"?$setsqlarr['audit']=intval($_CFG['audit_edit_resume']):"";
		updatetable(table('resume'),$setsqlarr," id='".intval($_REQUEST['pid'])."'  AND uid='{$setsqlarr['uid']}'");
		updatetable(table('resume_tmp'),$setsqlarr," id='".intval($_REQUEST['pid'])."'  AND uid='{$setsqlarr['uid']}'");
		check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
		write_memberslog($_SESSION['uid'],2,1105,$_SESSION['username'],"�޸��˼���({$_POST['title']})");
		if ($_POST['go_resume_show'])
		{
		header("Location: ?act=resume_show&pid={$_REQUEST['pid']}");
		}
		else
		{
		header("Location: ?act=make2&pid={$_REQUEST['pid']}");
		}
	}		
}
//��������-��ְ����
elseif ($act=='make2')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
			$resume_basic=get_resume_basic($uid,$pid);
			$link[0]['text'] = "��д����������Ϣ";
			$link[0]['href'] = '?act=make1';
			if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
			$smarty->assign('resume_basic',get_resume_basic($uid,$pid));
			$smarty->assign('resume_education',get_resume_education($uid,$pid));
			$smarty->assign('resume_work',get_resume_work($uid,$pid));
			$smarty->assign('resume_training',get_resume_training($uid,$pid));
			$resume_jobs=get_resume_jobs($pid);
			if ($resume_jobs)
			{
				foreach($resume_jobs as $rjob)
				{
				$jobsid[]=$rjob['category'].".".$rjob['subclass'];
				}
				$resume_jobs_id=implode("-",$jobsid);
			}
			$smarty->assign('resume_jobs_id',$resume_jobs_id);
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_personal/personal_resume_make2.htm');
}
//����-��ְ����
elseif ($act=='make2_save')
{
	
	$resumeuid=intval($_SESSION['uid']);
	$resumepid=intval($_REQUEST['pid']);
	if ($resumeuid==0 || $resumepid==0 ) showmsg('��������',1);
	$resumearr['recentjobs']=trim($_POST['recentjobs']);
	$resumearr['nature']=intval($_POST['nature'])?intval($_POST['nature']):showmsg('��ѡ��������λ���ʣ�',1);
	$resumearr['nature_cn']=trim($_POST['nature_cn']);
	$resumearr['district']=trim($_POST['district'])?intval($_POST['district']):showmsg('��ѡ�����������أ�',1);
	$resumearr['sdistrict']=intval($_POST['sdistrict']);
	$resumearr['district_cn']=trim($_POST['district_cn']);
	$resumearr['wage']=intval($_POST['wage'])?intval($_POST['wage']):showmsg('��ѡ��������н��',1);
	$resumearr['wage_cn']=trim($_POST['wage_cn']);
	$resumearr['trade']=$_POST['trade']?trim($_POST['trade']):showmsg('��ѡ���������µ���ҵ��',1);
	$resumearr['trade_cn']=trim($_POST['trade_cn']);
	$resumearr['intention_jobs']=trim($_POST['intention_jobs']);
	if ($_CFG['audit_edit_resume']!="-1")
	{
	$resumearr['audit']=$_CFG['audit_edit_resume'];
	}
	add_resume_jobs($resumepid,$_SESSION['uid'],$_POST['intention_jobs_id'])?"":showmsg('����ʧ�ܣ�',0);
	updatetable(table('resume'),$resumearr," id='{$resumepid}'  AND   uid='{$resumeuid}'");
	updatetable(table('resume_tmp'),$resumearr," id='{$resumepid}'  AND   uid='{$resumeuid}'");
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	if ($_POST['go_resume_show'])
	{
		header("Location: ?act=resume_show&pid={$resumepid}");
	}
	else
	{
	header("Location: ?act=make3&pid=".intval($_POST['pid']));
	}
}
//��������-��������
elseif ($act=='make3')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
				$resume_basic=get_resume_basic($uid,$pid);
				$link[0]['text'] = "��д����������Ϣ";
				$link[0]['href'] = '?act=make1';
				if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
				$link[0]['text'] = "����д��ְ����";
				$link[0]['href'] = '?act=make2&pid='.$pid;
				if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_personal/personal_resume_make3.htm');
}
elseif ($act=='make3_save')
{
	
	if (intval($_POST['pid'])==0 ) showmsg('��������',1);
	$setsqlarrspecialty['specialty']=!empty($_POST['specialty'])?$_POST['specialty']:showmsg('����д���ļ����س���',1);
	$_CFG['audit_edit_resume']!="-1"?$setsqlarrspecialty['audit']=intval($_CFG['audit_edit_resume']):"";
	updatetable(table('resume'),$setsqlarrspecialty," id='".intval($_POST['pid'])."' AND uid='".intval($_SESSION['uid'])."'");
	updatetable(table('resume_tmp'),$setsqlarrspecialty," id='".intval($_POST['pid'])."' AND uid='".intval($_SESSION['uid'])."'");
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	if ($_POST['go_resume_show'])
	{
		header("Location: ?act=resume_show&pid={$_POST['pid']}");
	}
	else
	{
		header("Location: ?act=make4&pid=".intval($_POST['pid']));
	}
}
//��������-��������
elseif ($act=='make4')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
				$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
				$link[0]['text'] = "��д����������Ϣ";
				$link[0]['href'] = '?act=make1';
				if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
				$link[0]['text'] = "��д��ְ����";
				$link[0]['href'] = '?act=make2&pid='.intval($_REQUEST['pid']);
				if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
				$link[0]['text'] = "��д�����س�";
				$link[0]['href'] = '?act=make3&pid='.intval($_REQUEST['pid']);
				if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
	//
	$smarty->assign('resume_basic',$resume_basic);//������Ϣ	
	$smarty->assign('resume_education',get_resume_education($uid,$pid));//��������
	$smarty->assign('resume_work',get_resume_work($uid,$pid));//��������
	$smarty->assign('resume_training',get_resume_training($uid,$pid));//��ѵ����
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('resume_education',get_resume_education($_SESSION['uid'],$_REQUEST['pid']));	
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_personal/personal_resume_make4.htm');
}
//��������-�����������
elseif ($act=='make4_save')
{
	$resume_education=get_resume_education($_SESSION['uid'],$_REQUEST['pid']);
	if (count($resume_education)>=6) showmsg('�����������ܳ���6����',1,$link);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['pid']=intval($_REQUEST['pid']);
	if ($setsqlarr['uid']==0 || $setsqlarr['pid']==0 ) showmsg('��������',1);
	$setsqlarr['start']=trim($_POST['start'])?$_POST['start']:showmsg('����д��ʼʱ�䣡',1,$link);
	$setsqlarr['endtime']=trim($_POST['endtime'])?$_POST['endtime']:showmsg('����д����ʱ�䣡',1,$link);
	$setsqlarr['school']=trim($_POST['school'])?$_POST['school']:showmsg('����дѧУ���ƣ�',1,$link);
	$setsqlarr['speciality']=trim($_POST['speciality'])?$_POST['speciality']:showmsg('����дרҵ���ƣ�',1,$link);
	$setsqlarr['education']=trim($_POST['education'])?$_POST['education']:showmsg('��ѡ����ѧ����',1,$link);
	$setsqlarr['education_cn']=trim($_POST['education_cn'])?$_POST['education_cn']:showmsg('��ѡ����ѧ����',1,$link);
		if (inserttable(table('resume_education'),$setsqlarr))
		{
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			if ($_POST['go_resume_show'])
			{
				header("Location: ?act=resume_show&pid={$setsqlarr['pid']}");
			}
			else
			{
			$link[0]['text'] = "������ӽ�������";
			$link[0]['href'] = '?act=make4&pid='.intval($_REQUEST['pid']);
			$link[1]['text'] = "������һ��";
			$link[1]['href'] = '?act=make5&pid='.intval($_REQUEST['pid']);
			$link[2]['text'] = "�鿴�ҵĽ�������";
			$link[2]['href'] = '?act=make4&pid='.intval($_REQUEST['pid']);
			showmsg("��ӳɹ�,�����Լ�����ӽ���������������һ�� ",2,$link,true,15);
			}	
		}
		else
		{
		showmsg("����ʧ�ܣ�",0,$link);
		}
}
//��������-ɾ����������
elseif ($act=='del_education')
{
	 $id=intval($_GET['id']);
	 $sql="Delete from ".table('resume_education')." WHERE id='{$id}'  AND uid='".intval($_SESSION['uid'])."' AND pid='".intval($_REQUEST['pid'])."' LIMIT 1 ";
	if ($db->query($sql))
	{
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));//���¼������״̬
	showmsg('ɾ���ɹ���',2);
	}
	else
	{
	showmsg('ɾ��ʧ�ܣ�',0);
	}	
}
//��������-�޸Ľ�������
elseif ($act=='edit_education')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
				$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
				$link[0]['text'] = "��д����������Ϣ";
				$link[0]['href'] = '?act=make1';
				if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
				$link[0]['text'] = "��д��ְ����";
				$link[0]['href'] = '?act=make2&pid='.intval($_REQUEST['pid']);
				if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
				$link[0]['text'] = "��д�����س�";
				$link[0]['href'] = '?act=make3&pid='.intval($_REQUEST['pid']);
				if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
	//
	$smarty->assign('resume_basic',$resume_basic);	
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$id=intval($_GET['id'])?intval($_GET['id']):showmsg('��������',1);
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->assign('education_edit',get_resume_education_one($_SESSION['uid'],$id));
	$smarty->assign('title','�༭���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_resume_education_edit.htm');
}
//�����޸ĵĽ�������
elseif ($act=='save_resume_education_edit')
{
	
	$id=trim($_POST['id'])?$_POST['id']:showmsg('��������',1);
	$setsqlarr['start']=trim($_POST['start'])?$_POST['start']:showmsg('����д��ʼʱ�䣡',1,$link);
	$setsqlarr['endtime']=trim($_POST['endtime'])?$_POST['endtime']:showmsg('����д����ʱ�䣡',1,$link);
	$setsqlarr['school']=trim($_POST['school'])?$_POST['school']:showmsg('����дѧУ���ƣ�',1,$link);
	$setsqlarr['speciality']=trim($_POST['speciality'])?$_POST['speciality']:showmsg('����дרҵ���ƣ�',1,$link);
	$setsqlarr['education']=trim($_POST['education'])?$_POST['education']:showmsg('��ѡ����ѧ����',1,$link);
	$setsqlarr['education_cn']=trim($_POST['education_cn'])?$_POST['education_cn']:showmsg('��ѡ����ѧ����',1,$link);
	if (updatetable(table('resume_education'),$setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}'"))
		{
			if ($_POST['go_resume_show'])
			{
				header("Location: ?act=resume_show&pid={$_REQUEST['pid']}");
			}
			else
			{
			$link[0]['text'] = "������һҳ";
			$link[0]['href'] = "?act=make4&pid={$_REQUEST['pid']}";
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));	
			showmsg("�޸ĳɹ���",2,$link);
			}			
		}
		else
		{
		showmsg("����ʧ�ܣ�",0,$link);
		}
}
//��������-��������
elseif ($act=='make5')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
				$resume_basic=get_resume_basic($uid,$pid);
				$link[0]['text'] = "��д����������Ϣ";
				$link[0]['href'] = '?act=make1';
				if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
				$link[0]['text'] = "��д��ְ����";
				$link[0]['href'] = '?act=make2&pid='.$pid;
				if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
				$link[0]['text'] = "��д�����س�";
				$link[0]['href'] = '?act=make3&pid='.$pid;
				if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
				$resume_education=get_resume_education($uid,$pid);
				$link[0]['text'] = "��д������д��������";
				$link[0]['href'] = '?act=make4&pid='.$pid;
				if (empty($resume_education)) showmsg("������д����������",1,$link);
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->assign('resume_education',$resume_education);
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_resume_make5.htm');
}
//��������-������ӵĹ�������
elseif ($act=='make5_save')
{
	$resume_work=get_resume_work($_SESSION['uid'],$_REQUEST['pid']);
	if (count($resume_work)>=10) showmsg('�����������ܳ���10����',1);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['pid']=intval($_REQUEST['pid']);
	if ($setsqlarr['pid']==0) showmsg('��������',1);
	$setsqlarr['start']=trim($_POST['start'])?$_POST['start']:showmsg('����д��ʼʱ�䣡',1,$link);
	$setsqlarr['endtime']=trim($_POST['endtime'])?$_POST['endtime']:showmsg('����д����ʱ�䣡',1,$link);
	$setsqlarr['companyname']=trim($_POST['companyname'])?$_POST['companyname']:showmsg('����д��ҵ���ƣ�',1,$link);
	$setsqlarr['jobs']=trim($_POST['jobs'])?$_POST['jobs']:showmsg('����дְλ���ƣ�',1,$link);
	$setsqlarr['companyprofile']=trim($_POST['companyprofile']);
	$setsqlarr['achievements']=trim($_POST['achievements']);
	if (inserttable(table('resume_work'),$setsqlarr))
		{
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			if ($_POST['go_resume_show'])
			{
			header("Location: ?act=resume_show&pid={$setsqlarr['pid']}");
			}
			else
			{
			$link[0]['text'] = "������ӹ�������";
			$link[0]['href'] = '?act=make5&pid='.intval($_REQUEST['pid']);
			$link[1]['text'] = "������һ��";
			$link[1]['href'] = '?act=make6&pid='.intval($_REQUEST['pid']);
			$link[2]['text'] = "�鿴�ҵĹ�������";
			$link[2]['href'] = '?act=make5&pid='.intval($_REQUEST['pid']);
			showmsg("��ӳɹ�,�����Լ�����ӹ���������������һ�� ",2,$link,true,15);
			}	
		
		}
		else
		{
		showmsg("����ʧ�ܣ�",0,$link);
		}
}
elseif ($act=='del_work')
{
	$id=intval($_GET['id']);
	$sql="Delete from ".table('resume_work')." WHERE id='".$id."' AND uid='".$_SESSION['uid']."' AND pid='".$_REQUEST['pid']."' LIMIT 1 ";
	if ($db->query($sql))
	{
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	showmsg('ɾ���ɹ���',2);
	}
	else
	{
	showmsg('ɾ��ʧ�ܣ�',0);
	}
}
elseif ($act=='edit_work')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
				$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
				$link[0]['text'] = "��д����������Ϣ";
				$link[0]['href'] = '?act=make1';
				if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
				$link[0]['text'] = "��д��ְ����";
				$link[0]['href'] = '?act=make2&pid='.intval($_REQUEST['pid']);
				if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
				$link[0]['text'] = "��д�����س�";
				$link[0]['href'] = '?act=make3&pid='.intval($_REQUEST['pid']);
				if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
	$id=intval($_GET['id']);
	//
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->assign('work_edit',get_resume_work_one($_SESSION['uid'],$pid,$id));
	$smarty->assign('title','�༭���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_resume_work_edit.htm');
}
elseif ($act=='save_resume_work_edit')
{	
	$id=intval($_POST['id']);
	$setsqlarr['start']=trim($_POST['start'])?$_POST['start']:showmsg('����д��ʼʱ�䣡',1,$link);
	$setsqlarr['endtime']=trim($_POST['endtime'])?$_POST['endtime']:showmsg('����д����ʱ�䣡',1,$link);
	$setsqlarr['companyname']=trim($_POST['companyname'])?$_POST['companyname']:showmsg('����д��ҵ���ƣ�',1,$link);
	$setsqlarr['jobs']=trim($_POST['jobs'])?trim($_POST['jobs']):showmsg('����дְλ���ƣ�',1,$link);
	$setsqlarr['companyprofile']=trim($_POST['companyprofile']);
	$setsqlarr['achievements']=trim($_POST['achievements']);
	if (updatetable(table('resume_work'),$setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}'"))
		{
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			if ($_POST['go_resume_show'])
			{
				header("Location: ?act=resume_show&pid={$_REQUEST['pid']}");
			}
			else
			{
			$link[0]['text'] = "������һҳ";
			$link[0]['href'] = "?act=make5&pid={$_REQUEST['pid']}";
			showmsg("�޸ĳɹ���",2,$link);
			}
		}
		else
		{
		showmsg("����ʧ�ܣ�",0,$link);
		}
}
//��������-��ѵ����
elseif ($act=='make6')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
				$resume_basic=get_resume_basic($uid,$pid);
				$link[0]['text'] = "��д����������Ϣ";
				$link[0]['href'] = '?act=make1';
				if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
				$link[0]['text'] = "��д��ְ����";
				$link[0]['href'] = '?act=make2&pid='.$pid;
				if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
				$link[0]['text'] = "��д�����س�";
				$link[0]['href'] = '?act=make3&pid='.$pid;
				if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
				$resume_education=get_resume_education($uid,$pid);
				$link[0]['text'] = "��д������д��������";
				$link[0]['href'] = '?act=make4&pid='.$pid;
				if (empty($resume_education)) showmsg("������д����������",1,$link);
					//
	$smarty->assign('resume_basic',$resume_basic);//������Ϣ	
	$smarty->assign('resume_education',$resume_education);//��������
	$smarty->assign('resume_work',get_resume_work($uid,$pid));//��������
	$smarty->assign('resume_training',get_resume_training($uid,$pid));//��ѵ����
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('title','�ҵļ��� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->display('member_personal/personal_resume_make6.htm');
}
//����-��ӵ���ѵ����
elseif ($act=='make6_save')
{
	$resume_training=get_resume_training($_SESSION['uid'],$_REQUEST['pid']);
	if (count($resume_training)>=8) showmsg('��ѵ�������ܳ���10����',1);
	$setsqlarr['uid']=intval($_SESSION['uid']);
	$setsqlarr['pid']=intval($_REQUEST['pid']);
	if ($setsqlarr['uid']==0 || $setsqlarr['pid']==0 )  showmsg("��������",0,$link);
	$setsqlarr['start']=trim($_POST['start'])?$_POST['start']:showmsg('����д��ʼʱ�䣡',1,$link);
	$setsqlarr['endtime']=trim($_POST['endtime'])?$_POST['endtime']:showmsg('����д����ʱ�䣡',1,$link);
	$setsqlarr['agency']=trim($_POST['agency'])?$_POST['agency']:showmsg('����д�������ƣ�',1,$link);
	$setsqlarr['course']=trim($_POST['course'])?$_POST['course']:showmsg('����д�γ����ƣ�',1,$link);
	$setsqlarr['description']=trim($_POST['description']);
		if (inserttable(table('resume_training'),$setsqlarr))
		{
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			if ($_POST['go_resume_show'])
			{
				header("Location: ?act=resume_show&pid={$setsqlarr['pid']}");
			}
			else
			{
			$link[0]['text'] = "���������ѵ����";
			$link[0]['href'] = '?act=make6&pid='.intval($_REQUEST['pid']);
			$link[1]['text'] = "������һ��";
			$link[1]['href'] = '?act=make7&pid='.intval($_REQUEST['pid']);
			$link[2]['text'] = "�鿴�ҵ���ѵ����";
			$link[2]['href'] = '?act=make6&pid='.intval($_REQUEST['pid']);
			showmsg("��ӳɹ�,�����Լ��������ѵ������������һ�� ",2,$link,true,15);
			}		
		}
		else
		{
		showmsg("����ʧ�ܣ�",0,$link);
		}
}
//ɾ����ѵ����
elseif ($act=='del_training')
{
	$id=!empty($_GET['id'])?intval($_GET['id']):showmsg('��������',1);
	$sql="Delete from ".table('resume_training')." WHERE id='{$id}' AND uid='{$_SESSION['uid']}' AND pid='".intval($_REQUEST['pid'])."' LIMIT 1 ";
	if ($db->query($sql))
	{
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	showmsg('ɾ���ɹ���',2);
	}
	else
	{
	showmsg('ɾ��ʧ�ܣ�',0);
	}
}
//�޸���ѵ����
elseif ($act=='edit_training')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
				$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
				$link[0]['text'] = "��д����������Ϣ";
				$link[0]['href'] = '?act=make1';
				if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);

				$link[0]['text'] = "��д��ְ����";
				$link[0]['href'] = '?act=make2&pid='.intval($_REQUEST['pid']);
				if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
				$link[0]['text'] = "��д�����س�";
				$link[0]['href'] = '?act=make3&pid='.intval($_REQUEST['pid']);
				if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
					//
	$smarty->assign('resume_basic',$resume_basic);	
	$smarty->assign('resume_education',get_resume_education($uid,$pid));
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$id=intval($_GET['id']);
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('go_resume_show',$_GET['go_resume_show']);
	$smarty->assign('training_edit',get_resume_training_one($_SESSION['uid'],$_REQUEST['pid'],$id));
	$smarty->assign('title','�༭���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_resume_training_edit.htm');
}
elseif ($act=='save_resume_training_edit')
{
	$id=intval($_POST['id']);
	$setsqlarr['start']=trim($_POST['start'])?$_POST['start']:showmsg('����д��ʼʱ�䣡',1,$link);
	$setsqlarr['endtime']=trim($_POST['endtime'])?$_POST['endtime']:showmsg('����д����ʱ�䣡',1,$link);
	$setsqlarr['agency']=trim($_POST['agency'])?$_POST['agency']:showmsg('����д�������ƣ�',1,$link);
	$setsqlarr['course']=trim($_POST['course'])?$_POST['course']:showmsg('����д�γ����ƣ�',1,$link);
	$setsqlarr['description']=trim($_POST['description']);
		if (updatetable(table('resume_training'),$setsqlarr," id='{$id}' AND uid='{$_SESSION['uid']}'"))
		{		
			check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
			if ($_POST['go_resume_show'])
			{
				header("Location: ?act=resume_show&pid={$_REQUEST['pid']}");
			}
			else
			{
			$link[0]['text'] = "������һҳ";
			$link[0]['href'] = "?act=make6&pid={$_REQUEST['pid']}";
			showmsg("�޸ĳɹ���",2,$link);
			}
		}
	!edit_training($setsqlarr)?showmsg("�޸�ʧ�ܣ�",0,$link):showmsg("�޸ĳɹ���",2,$link);
}
elseif ($act=='make7')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=resume_list';
	if ($uid==0 || $pid==0) showmsg('���������ڣ�',1,$link);
					$resume_basic=get_resume_basic($uid,$pid);
					$link[0]['text'] = "��д����������Ϣ";
					$link[0]['href'] = '?act=make1';
					if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
					$link[0]['text'] = "��д��ְ����";
					$link[0]['href'] = '?act=make2&pid='.$pid;
					if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
					$link[0]['text'] = "��д�����س�";
					$link[0]['href'] = '?act=make3&pid='.$pid;
					if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
					$resume_education=get_resume_education($uid,$pid);
					$link[0]['text'] = "��д������д��������";
					$link[0]['href'] = '?act=make4&pid='.$pid;
					if (empty($resume_education)) showmsg("������д����������",1,$link);
		 if ($resume_basic['photo_img'] && empty($_GET['addphoto']))
		 {
		 	header("Location: ?act=photo_cutting&pid=".$pid);
		 }
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->assign('resume_education',$resume_education);
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('act',$act);
	$smarty->assign('pid',$pid);
	$smarty->assign('title','�༭���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_resume_make7.htm');
}
elseif ($act=='make7_save')
{
	!$_FILES['photo']['name']?showmsg('���ϴ�ͼƬ��',1):"";
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	if (intval($_REQUEST['pid'])==0) showmsg('��������',0);
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic['photo_img']))
	{
	$setsqlarr['photo_audit']=$_CFG['audit_resume_photo'];
	}
	else
	{
	$_CFG['audit_edit_photo']!="-1"?$setsqlarr['photo_audit']=intval($_CFG['audit_edit_photo']):"";
	}
	$photo_dir=substr($_CFG['resume_photo_dir'],strlen($_CFG['site_dir']));
	$photo_dir="../../".$photo_dir.date("Y/m/d/");
	make_dir($photo_dir);
	$setsqlarr['photo_img']=_asUpFiles($photo_dir, "photo",$_CFG['resume_photo_max'],'gif/jpg/bmp/png',true);
	$setsqlarr['photo_img']=date("Y/m/d/").$setsqlarr['photo_img'];
	!updatetable(table('resume'),$setsqlarr," id='".intval($_REQUEST['pid'])."' AND uid='".intval($_SESSION['uid'])."'")?showmsg("����ʧ�ܣ�",0):'';
	!updatetable(table('resume_tmp'),$setsqlarr," id='".intval($_REQUEST['pid'])."' AND uid='".intval($_SESSION['uid'])."'")?showmsg("����ʧ�ܣ�",0):'';
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	header("Location: ?act=photo_cutting&pid=".intval($_REQUEST['pid']));
}
//����-������Ƭ
elseif ($act=='photo_cutting')
{
					$uid=intval($_SESSION['uid']);
					$pid=intval($_REQUEST['pid']);
					$resume_basic=get_resume_basic($uid,$pid);
					$link[0]['text'] = "��д����������Ϣ";
					$link[0]['href'] = '?act=make1';
					if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
					$link[0]['text'] = "��д��ְ����";
					$link[0]['href'] = '?act=make2&pid='.$pid;
					if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
					$link[0]['text'] = "��д�����س�";
					$link[0]['href'] = '?act=make3&pid='.$pid;
					if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
					$resume_education=get_resume_education($uid,$pid);
					$link[0]['text'] = "��д������д��������";
					$link[0]['href'] = '?act=make4&pid='.$pid;
					if (empty($resume_education)) showmsg("������д����������",1,$link);
					if (empty($resume_basic['photo_img']))
					{
					header('Location: ?act=make7&pid='.$_REQUEST['pid']);
					}
	$photo_thumb_dir=QISHI_ROOT_PATH.substr($_CFG['resume_photo_dir_thumb'],strlen($_CFG['site_dir']));
	make_dir($photo_thumb_dir.dirname($resume_basic['photo_img']));
	if (file_exists($photo_thumb_dir.$resume_basic['photo_img']))
	{
		$smarty->assign('resume_thumb_photo',$resume_basic['photo_img']);
	}
	$smarty->assign('resume_photo',$resume_basic['photo_img']);
	$smarty->assign('act',$act);
	$smarty->assign('pid',$_REQUEST['pid']);
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->assign('resume_education',$resume_education);
	$smarty->assign('resume_work',get_resume_work($uid,$pid));
	$smarty->assign('resume_training',get_resume_training($uid,$pid));
	$smarty->assign('title','������Ƭ - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_resume_photo_cutting.htm');
}
//����-������Ƭ
elseif ($act=='save_resume_photo_cutting')
{
	$resume_basic=get_resume_basic(intval($_SESSION['uid']),intval($_REQUEST['pid']));
	if (empty($resume_basic)) showmsg("������д����������Ϣ��",0);
	require_once(QISHI_ROOT_PATH.'include/imageresize.class.php');
	$imgresize = new ImageResize();
	$photo_dir=QISHI_ROOT_PATH.substr($_CFG['resume_photo_dir'],strlen($_CFG['site_dir']));
	$photo_thumb_dir=QISHI_ROOT_PATH.substr($_CFG['resume_photo_dir_thumb'],strlen($_CFG['site_dir']));
	$imgresize->load($photo_dir.$resume_basic['photo_img']);
	$posary=explode(',', $_POST['cut_pos']);
	foreach($posary as $k=>$v) $posary[$k]=intval($v); 
	if($posary[2]>0 && $posary[3]>0) $imgresize->resize($posary[2], $posary[3]);
	$imgresize->cut(120,150, intval($posary[0]), intval($posary[1]));
	$imgresize->save($photo_thumb_dir.$resume_basic['photo_img']);
	header('Location: ?act=photo_cutting&show=ok&pid='.$_REQUEST['pid']);
}
elseif ($act=='edit_photo_display')
{
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	header('Location: ?act=resume_show&pid='.intval($_REQUEST['pid']));
}
elseif ($act=='addcomplete')
{
					$uid=intval($_SESSION['uid']);
					$pid=intval($_REQUEST['pid']);
					$resume_basic=get_resume_basic($uid,$pid);
					$link[0]['text'] = "��д����������Ϣ";
					$link[0]['href'] = '?act=make1';
					if (empty($resume_basic)) showmsg("������д����������Ϣ��",1,$link);
					$link[0]['text'] = "��д��ְ����";
					$link[0]['href'] = '?act=make2&pid='.$pid;
					if (empty($resume_basic['intention_jobs'])) showmsg("������д��ְ����",1,$link);
					$link[0]['text'] = "��д�����س�";
					$link[0]['href'] = '?act=make3&pid='.$pid;
					if (empty($resume_basic['specialty'])) showmsg("������д��ְ����",1,$link);
					$resume_education=get_resume_education($uid,$pid);
					$link[0]['text'] = "��д������д��������";
					$link[0]['href'] = '?act=make4&pid='.$pid;
					if (empty($resume_education)) showmsg("������д����������",1,$link);
	$link[0]['text'] = "�鿴����";
	$link[0]['href'] ="?act=resume_show&pid={$pid}";
	$link[1]['text'] = "�����ҵļ���";
	$link[1]['href'] ="?act=resume_list";
	showmsg("������ɣ�",2,$link);
}
elseif ($act=='resume_privacy')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$resume_basic=get_resume_basic($uid,$pid);
	if (empty($resume_basic)) showmsg("���������ڣ�",0);
	$smarty->assign('title','������˽���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('resume_basic',$resume_basic);
	$smarty->assign('pid',$pid);
	$smarty->display('member_personal/personal_resume_privacy.htm');
}
elseif ($act=='save_resume_privacy')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['display_name']=intval($_POST['display_name']);
	$setsqlarr['photo_display']=intval($_POST['photo_display']);
	$wheresql=" uid='".$_SESSION['uid']."' ";
	!updatetable(table('resume'),$setsqlarr," uid='{$uid}' AND  id='{$pid}'")?showmsg("����ʧ�ܣ�",0):'';
	!updatetable(table('resume_tmp'),$setsqlarr," uid='{$uid}' AND  id='{$pid}'")?showmsg("����ʧ�ܣ�",0):'';
	$setsqlarrdisplay['display']=intval($_POST['display']);
	!updatetable(table('resume_search_key'),$setsqlarrdisplay," uid='{$uid}' AND  id='{$pid}'")?showmsg("����ʧ�ܣ�",0):'';
	!updatetable(table('resume_search_rtime'),$setsqlarrdisplay," uid='{$uid}' AND  id='{$pid}'")?showmsg("����ʧ�ܣ�",0):'';
	!updatetable(table('resume_search_tag'),$setsqlarrdisplay," uid='{$uid}' AND  id='{$pid}'")?showmsg("����ʧ�ܣ�",0):'';
	check_resume($_SESSION['uid'],intval($_REQUEST['pid']));
	distribution_resume($pid,$uid);
	write_memberslog($_SESSION['uid'],2,1104,$_SESSION['username'],"���ü�����˽({$pid})");
	$link[0]['text'] = "�鿴����";
	$link[0]['href'] = '?act=resume_show&pid='.$pid;
	$link[1]['text'] = "��������";
	$link[1]['href'] = 'javascript:history.go(-1)';
	$link[2]['text'] = "���ؼ����б�";
	$link[2]['href'] = '?act=resume_list';
	showmsg('���óɹ���',2,$link);
}
elseif ($act=='tpl')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_GET['pid']);
	$resume_basic=get_resume_basic($uid,$pid);
	if (empty($resume_basic)) showmsg("���������ڣ�",0);
	$smarty->assign('title','����ģ�� - ���˻�Ա���� - '.$_CFG['site_name']);
	if ($resume_basic['tpl']=="")
	{
	$resume_basic['tpl']=$_CFG['tpl_personal'];
	}
	$smarty->assign('mytpl',$resume_basic['tpl']);
	$smarty->assign('resume_url',url_rewrite('QS_resumeshow',array('id'=>$resume_basic['id']),false));
	$smarty->assign('pid',$pid);
	$smarty->assign('resumetpl',get_resumetpl());	
	$smarty->display('member_personal/personal_resume_tpl.htm');
}
elseif ($act=='tpl_save')
{
	$link[0]['text'] = "�鿴����";
	$link[0]['href'] = '?act=resume_list';
	$setsqlarr['tpl']=trim($_POST['tpl']);
	write_memberslog($_SESSION['uid'],2,1106,$_SESSION['username'],"���ü���ģ��");
	updatetable(table('resume'),$setsqlarr," id='".intval($_POST['pid'])."' AND uid='".intval($_SESSION['uid'])."'");
	updatetable(table('resume_tmp'),$setsqlarr," id='".intval($_POST['pid'])."' AND uid='".intval($_SESSION['uid'])."'");
	showmsg("����ɹ���",2,$link);
}
elseif ($act=='talent')
{
	$smarty->assign('title','�������� - ���˻�Ա���� - '.$_CFG['site_name']);
	$resume_list=get_auditresume_list($_SESSION['uid'],15);
	$smarty->assign('resume_list',$resume_list);
	$text=get_cache('text');
	$smarty->assign('personal_talent_requirement',$text['personal_talent_requirement']);
	$smarty->display('member_personal/personal_talent.htm');
}
elseif ($act=='talent_save')
{
	$uid=intval($_SESSION['uid']);
	$pid=intval($_REQUEST['pid']);
	$resume=get_resume_basic($uid,$pid);
	if ($resume['complete_percent']<$_CFG['elite_resume_complete_percent'])
	{
	showmsg("��������ָ��С��{$_CFG['elite_resume_complete_percent']}%����ֹ���룡",0);
	}
	$setsqlarr['talent']=3;
	$wheresql=" uid='{$uid}' AND id='{$pid}' ";
	updatetable(table('resume'),$setsqlarr,$wheresql);
	updatetable(table('resume_tmp'),$setsqlarr,$wheresql);
	write_memberslog($uid,2,1107,$_SESSION['username'],"����߼��˲�");
	showmsg('����ɹ�����ȴ�����Ա��ˣ�',2);
}
unset($smarty);
?>