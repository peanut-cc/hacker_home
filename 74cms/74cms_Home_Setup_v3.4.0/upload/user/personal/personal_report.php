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
$smarty->assign('leftmenu',"index");
if ($act=='report')
{
	$smarty->assign('title','�ٱ���Ϣ - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('url',$_SERVER['HTTP_REFERER']);
	$smarty->display('member_personal/personal_report.htm');
}
//����ٱ���Ϣ
elseif ($act=='report_save')
{
	$link[0]['text'] = "������һҳ��";
	$link[0]['href'] = $_POST['url'];
	if (check_jobs_report($_SESSION['uid'],$_POST['jobs_id']))
	{
	showmsg("���Ѿ��ٱ�����ְλ��",1,$link);
	}
	$setsqlarr['content']=trim($_POST['content'])?trim($_POST['content']):showmsg('���������������',1);
	$setsqlarr['jobs_id']=$_POST['jobs_id']?intval($_POST['jobs_id']):showmsg('û��ְλID',1);
	$setsqlarr['jobs_name']=trim($_POST['jobs_name'])?trim($_POST['jobs_name']):showmsg('û��ְλ����',1);
	$setsqlarr['jobs_addtime']=intval($_POST['jobs_addtime']);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['addtime']=time();
	write_memberslog($_SESSION['uid'],2,7003,$_SESSION['username'],"�ٱ�ְλ({$_POST['jobs_id']})");
	!inserttable(table('report'),$setsqlarr)?showmsg("�ٱ�ʧ�ܣ�",0,$link):showmsg("�ٱ��ɹ�������Ա�����洦��",2,$link);
}
unset($smarty);
?>