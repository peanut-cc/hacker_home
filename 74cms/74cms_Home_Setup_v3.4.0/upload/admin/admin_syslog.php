<?php
 /*
 * 74cms ϵͳ��־
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
require_once(ADMIN_ROOT_PATH.'include/admin_syslog_fun.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
check_permissions($_SESSION['admin_purview'],"syslog");
$smarty->assign('pageheader',"ϵͳ��־");
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql="";
	$oederbysql=" order BY l_id DESC ";
	if (isset($_GET['l_type']) && !empty($_GET['l_type']))
	{
		$wheresql=" WHERE l_type='".intval($_GET['l_type'])."'";
	}
	if (isset($_GET['settr']) && !empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE l_time> ".$settr:$wheresql." AND l_time> ".$settr;
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('syslog').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_syslog_list($offset,$perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$smarty->assign('page',$page->show(3));
	$smarty->display('syslog/admin_syslog_list.htm');
}
elseif($act == 'del_syslog')
{
	check_token();
	$id=$_REQUEST['id'];
	$dnum=del_syslog($id);
	if ($dnum>0)
	{
	adminmsg("ɾ���ɹ�����ɾ��".$dnum."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'pidel_syslog')
{
	get_token();
	$smarty->assign('pageheader',"ɾ��ϵͳ������־");
	$smarty->display('syslog/admin_syslog_del.htm');
}
elseif($act == 'pidel_syslog_del')
{
	check_token();
	$l_type=$_POST['l_type'];
	if(empty($l_type))	adminmsg('��ѡ��������ͣ�',1);
	$starttime=intval(convert_datefm($_POST['starttime'],2));
	if (empty($starttime))
	{
	adminmsg('����д��ʼʱ�䣡',1);
	}	
	$endtime=intval(convert_datefm($_POST['endtime'],2));
	if (empty($endtime))
	{
	adminmsg('����д����ʱ�䣡',1);
	}	
	if($starttime >$endtime) adminmsg('��ʼʱ�䲻�ܴ��ڽ���ʱ�䣡',1);
	$link[0]['text'] = "������־�б�";
	$link[0]['href'] = '?act=list';
	$link[1]['text'] = "����ɾ��";
	$link[1]['href'] = '?act=pidel_syslog';
	$dnum=pidel_syslog($l_type,$starttime,$endtime);
	if ($dnum>0)
	{
	adminmsg("ɾ���ɹ�����ɾ��".$dnum."��",2,$link);
	}
	else
	{
	adminmsg("�����ڶ�û����־��ɾ��ʧ��,���飡",0,$link);
	}
}
?>