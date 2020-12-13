<?php
 /*
 * 74cms ajax
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'total';
if($act == 'total')
{
	$total_jobs=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE audit=2");
	$total_company=$db->get_total("SELECT COUNT(*) AS num FROM ".table('company_profile')." WHERE audit=2");
	$total_payment_log=$db->get_total("SELECT COUNT(*) AS num FROM ".table('order')." WHERE is_paid=1 and utype=1");
 	$total_resume_audit=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')." WHERE audit=2");
	$total_resume_talent=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume')." WHERE talent=3");
	$total_resume_talent=$total_resume_talent+$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')." WHERE talent=3");
	$total_resume_photo_audit=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume')." WHERE photo_audit=2 ");
	$total_resume_photo_audit=$total_resume_photo_audit+$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')." WHERE photo_audit=2 ");
	$total_feedback_replyinfo=$db->get_total("SELECT COUNT(*) AS num FROM ".table('feedback')." WHERE replyinfo=1");//δ�ظ�����뽨��
	$total_report=$db->get_total("SELECT COUNT(*) AS num FROM ".table('report')." ");//����Ͷ����Ϣ
	
 	
	$str="[{$total_jobs}]";
	$str.=",[{$total_resume_audit}]";
	$str.=",[{$total_company}]";
	$str.=",[{$total_resume_talent}]";	
	$str.=",[{$total_payment_log}]";	
	$str.=",[{$total_resume_photo_audit}]";
	$str.=",[{$total_report}]";
	$str.=",[{$total_feedback_replyinfo}]";
 	exit($str);
}
elseif($act == 'get_cat_city')
{
	$pid=intval($_GET['pid']);
	$sql = "select * from ".table('category_district')." where parentid='{$pid}'  order BY category_order desc,id asc";
	$result = $db->query($sql);
	while($row = $db->fetch_array($result))
	{	
		$cat[]=$row['id'].",".$row['categoryname'].",".$row['category_order'];
	}
	if (!empty($cat))
	{
	exit(implode('|',$cat));
	}
}
elseif($act == 'get_cat_jobs')
{
	$pid=intval($_GET['pid']);
	$sql = "select * from ".table('category_jobs')." where parentid='{$pid}'  order BY category_order desc,id asc";
	$result = $db->query($sql);
	while($row = $db->fetch_array($result))
	{	
		$cat[]=$row['id'].",".$row['categoryname'].",".$row['category_order'];
	}
	if (!empty($cat))
	{
	exit(implode('|',$cat));
	}
}
 
elseif($act == 'get_jobs')
{
	$type=trim($_GET['type']);
	$key=trim($_GET['key']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$key=iconv("utf-8",QISHI_DBCHARSET,$key);
	}	
	if ($type=="get_id")
	{
		$id=intval($key);
		$sql = "select * from ".table('jobs')." where id='{$id}'  LIMIT 1";
	}
	elseif ($type=="get_jobname")
	{
		$sql = "select * from ".table('jobs')." where jobs_name like '%{$key}%'  LIMIT 30";
	}
	elseif ($type=="get_comname")
	{
		$sql = "select * from ".table('jobs')." where companyname like '%{$key}%'  LIMIT 30";
	}
	elseif ($type=="get_uid")
	{
		$uid=intval($key);
		$sql = "select * from ".table('jobs')." where uid='{$uid}'  LIMIT 30";		
	}
	else
	{
	exit();
	}
		$result = $db->query($sql);
		while($row = $db->fetch_array($result))
		{
			$row['addtime']=date("Y-m-d",$row['addtime']);
			$row['deadline']=date("Y-m-d",$row['deadline']);
			$row['refreshtime']=date("Y-m-d",$row['refreshtime']);
			$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['company_id']));
			$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['id']));
			$info[]=$row['id']."%%%".$row['jobs_name']."%%%".$row['jobs_url']."%%%".$row['companyname']."%%%".$row['company_url']."%%%".$row['addtime']."%%%".$row['deadline']."%%%".$row['refreshtime'];
		}
		if (!empty($info))
		{
		exit(implode('@@@',$info));
		}
		else
		{
		exit();
		}
}
elseif($act == 'get_company')
{
	$type=trim($_GET['type']);
	$key=trim($_GET['key']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$key=iconv("utf-8",QISHI_DBCHARSET,$key);
	}	
	if ($type=="getuname")
	{
		$sql = "select * from ".table('members')." AS m  LEFT JOIN  ".table('company_profile')." AS c ON  m.uid=c.uid where m.username like '{$key}%' AND m.utype=1 LIMIT 20";
	}
	elseif ($type=="getcomname")
	{
		$sql = "select * from ".table('company_profile')." where companyname like '%{$key}%'  LIMIT 30";
	}
	else
	{
	exit();
	}
		$result = $db->query($sql);
		while($row = $db->fetch_array($result))
		{
			if (empty($row['companyname']))
			{
			continue;
			}
			$row['addtime']=date("Y-m-d",$row['addtime']);
			$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['id']));
			$info[]=$row['id']."%%%".$row['companyname']."%%%".$row['company_url']."%%%".$row['addtime'];
		}
		if (!empty($info))
		{
		exit(implode('@@@',$info));
		}
}
elseif($act == 'get_user_info')
{
	$id=intval($_GET['id']);
	$info=$db->getone("select * from ".table('members')." where uid='{$id}' LIMIT 1");
	if (empty($info))
	{
	exit("��Ա��Ϣ�����ڣ������Ѿ���ɾ����");
	}
	$html="�û�����{$info['username']}&nbsp;&nbsp;<span style=\"color:#0033CC\">(uid:{$info['uid']})</span><br/>";
	if (!empty($info['mobile']))
	{
	$mobile_audit=$info['mobile_audit']=="1"?'<span style="color:#009900">[����֤]</span>':'<span style="color:#FF9900">[δ��֤]</span>';
	$info['mobile']=$info['mobile'].$mobile_audit;
	}
	else
	{
	$info['mobile']='----';
	}
	$html.="�ֻ���{$info['mobile']}<br/>";
	$email_audit=$info['email_audit']=="1"?'<span style="color:#009900">[����֤]</span>':'<span style="color:#FF9900">[δ��֤]</span>';
	$html.="���䣺{$info['email']}{$email_audit}<br/>";
	$info['reg_time']=$info['reg_time']?date("Y/m/d H:i",$info['reg_time']):'----';
	$html.="ע��ʱ�䣺{$info['reg_time']}<br/>";
	$info['reg_ip']=$info['reg_ip']?$info['reg_ip']:'----';
	$html.="ע��IP��{$info['reg_ip']}<br/>";
	$info['last_login_time']=$info['last_login_time']?date("Y/m/d H:i",$info['last_login_time']):'----';
	$html.="����¼ʱ�䣺{$info['last_login_time']}<br/>";
	$info['last_login_ip']=$info['last_login_ip']?$info['last_login_ip']:'----';
	$html.="����¼IP��{$info['last_login_ip']}<br/>";
	if ($info['utype']=="1")
	{
		$points=$db->getone("select points from ".table('members_points')." where uid = '{$id}'  LIMIT 1 ");
		$html.="{$_CFG['points_byname']}��{$points['points']}{$_CFG['points_quantifier']}<br/>";
		$com=$db->getone("select companyname from ".table('company_profile')." where uid='{$id}' LIMIT 1");
		if (empty($com['companyname']))
		{
		$com['companyname']="δ��д";
		}
		$html.="��˾���ƣ�{$com['companyname']}<br/>";
		$totaljob=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')."  where uid='{$id}'");
		$html.="����ְλ��{$totaljob}��<br/>";
		if ($_CFG['operation_mode']=="2")
		{
			$setmeal=$db->getone("select * from ".table('members_setmeal')."  WHERE uid='{$id}' AND  effective=1 LIMIT 1");
			if (!empty($setmeal))
			{
			$html.="�����ײͣ�{$setmeal['setmeal_name']}<br/>";
			$html.="�������ޣ�".date("Y/m/d",$setmeal['starttime'])."--".date("Y/m/d",$setmeal['endtime']);
			}
		}
	}
	if ($info['utype']=="2")
	{
		$totalresume=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume')."  where uid='{$id}'");
		$html.="����������{$totalresume}��<br/>";
	}
	exit($html);
}
?>