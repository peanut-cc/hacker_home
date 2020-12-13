<?php
/*
 * 74cms 个人会员中心
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__) . '/personal_common.php');
$smarty->assign('leftmenu',"apply");
if ($act=='down')
{
	$perpage=10;
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$joinsql=" LEFT JOIN  ".table('company_profile')." AS c  ON d.company_uid=c.uid ";
	$wheresql=" WHERE d.resume_uid='{$_SESSION['uid']}' ";
	$resume_id =intval($_GET['resume_id']);
	if($resume_id>0)$wheresql.=" AND  d.resume_id='{$resume_id}' ";	
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND d.down_addtime>".$settr_val;
	}
	$total_sql="SELECT COUNT(*) AS num from ".table('company_down_resume')." AS d {$wheresql}";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title',"谁下载的我的简历 - 个人会员中心 - {$_CFG['site_name']}");
	$smarty->assign('mylist',get_com_downresume($offset,$perpage,$joinsql.$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('count',$total_val);
	$smarty->assign('act',$act);
	$smarty->assign('resume_list',get_auditresume_list($_SESSION['uid']));
	$smarty->display('member_personal/personal_downresume.htm');
}
elseif ($act=='interview')
{
	$perpage=10;
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE  i.resume_uid='{$_SESSION['uid']}' ";
	$look=intval($_GET['look']);
	if($look>0)
	{
	$wheresql.=" AND  i.personal_look={$look}";
	}
	$resume_id =intval($_GET['resume_id']);
	if($resume_id>0)
	{
	$wheresql.=" AND  i.resume_id='{$resume_id}' ";	
	}
	$total_sql="SELECT COUNT(*) AS num from ".table('company_interview')." AS i {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','收到的面试邀请 - 个人会员中心 - '.$_CFG['site_name']);
	$joinsql=" LEFT JOIN  ".table('jobs')." AS j  ON  i.jobs_id=j.id ";
	$smarty->assign('interview',get_invitation($offset, $perpage,$joinsql.$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('act',$act);
	$count[0]=count_interview($_SESSION['uid'],1);
	$count[1]=count_interview($_SESSION['uid'],2);
	$count[2]=$count[0]+$count[1];
	$smarty->assign('count',$count);
	$smarty->assign('resume_list',get_auditresume_list($_SESSION['uid']));
	$smarty->display('member_personal/personal_interview.htm');
}
elseif ($act=='set_interview')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择项目！",1);
	if ($_POST['set'])
	{
	set_invitation($yid,$_SESSION['uid'],2);
	showmsg("设置成功！",2);
	}
}
//职位收藏夹列表
elseif ($act=='favorites')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE f.personal_uid='{$_SESSION['uid']}' ";
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND f.addtime>".$settr_val;
	}
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_favorites')." AS f {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','职位收藏夹 - 个人会员中心 - '.$_CFG['site_name']);
	$smarty->assign('act',$act);
	$joinsql=" LEFT JOIN ".table('jobs')." as  j  ON f.jobs_id=j.id ";
	$smarty->assign('favorites',get_favorites($offset,$perpage,$joinsql.$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->display('member_personal/personal_favorites.htm');
}
elseif ($act=='del_favorites')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择项目！",1);
	$_POST['delete']?(!del_favorites($yid,$_SESSION['uid'])?showmsg("删除失败！",0):showmsg("删除成功！",2)):'';
}
//申请的职位列表
elseif ($act=='apply_jobs')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE a.personal_uid='{$_SESSION['uid']}' ";
	$resume_id =intval($_GET['resume_id']);
	if($resume_id>0)
	{
	$wheresql.=" AND  a.resume_id='{$resume_id}' ";	
	}
	$aetlook=intval($_GET['aetlook']);
	if($aetlook>0)
	{
	$wheresql.=" AND a.personal_look='{$aetlook}'";
	}	
	$perpage=10;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('personal_jobs_apply')." AS a {$wheresql} ";
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('title','已申请的职位 - 个人会员中心 - '.$_CFG['site_name']);
	$joinsql=" LEFT JOIN ".table('jobs')." AS j ON a.jobs_id=j.id ";
	$smarty->assign('jobs_apply',get_apply_jobs($offset,$perpage,$joinsql.$wheresql));
	$smarty->assign('act',$act);
	$applyjobs_num=get_now_applyjobs_num($_SESSION['uid']);
	$smarty->assign('count_apply',array($_CFG['apply_jobs_max'],$applyjobs_num,$_CFG['apply_jobs_max']-$applyjobs_num));
	$smarty->assign('page',$page->show(3));
	$count[0]=count_personal_jobs_apply($_SESSION['uid'],1);
	$count[1]=count_personal_jobs_apply($_SESSION['uid'],2);
	$count[2]=$count[0]+$count[1];
	$smarty->assign('count',$count);
	$smarty->assign('resume_list',get_auditresume_list($_SESSION['uid']));
	$smarty->display('member_personal/personal_apply_jobs.htm');
}
//删除-申请的职位列表
elseif ($act=='del_jobs_apply')
{
	$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:showmsg("你没有选择项目！",1);
	$delete =trim($_POST['delete']);
	$delete?(!del_jobs_apply($yid,$_SESSION['uid'])?showmsg("删除失败！",0):showmsg("删除成功！",2)):'';
}
unset($smarty);
?>