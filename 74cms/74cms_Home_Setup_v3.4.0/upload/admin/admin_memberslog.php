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
require_once(ADMIN_ROOT_PATH.'include/admin_memberslog_fun.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
check_permissions($_SESSION['admin_purview'],"memberslog");
$smarty->assign('pageheader',"��Ա��־");
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql="";
	$oederbysql=" order BY log_addtime DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE log_username like '%{$key}%'";
		if     ($key_type===2)$wheresql=" WHERE log_uid =".intval($key);
		$oederbysql="";
	}
	if (isset($_GET['log_utype']) && !empty($_GET['log_utype']))
	{
		$wheresql=" WHERE log_utype='".intval($_GET['log_utype'])."'";
	}
	if (isset($_GET['settr']) && !empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE log_addtime> ".$settr:$wheresql." AND log_addtime> ".$settr;
	}
	if (isset($_GET['log_type']) && !empty($_GET['log_type']))
	{
		$log_type=intval($_GET['log_type']);
		$wheresql=empty($wheresql)?" WHERE log_type= ".$log_type:$wheresql." AND log_type= ".$log_type;
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_memberslog_list($offset,$perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$smarty->assign('total',$total_val);
	$smarty->assign('page',$page->show(3));
	$smarty->display('memberslog/admin_memberslog_list.htm');
}
elseif($act == 'del_memberslog')
{
	check_token();
	$id=$_REQUEST['id'];
	$dnum=del_memberslog($id);
	if ($dnum>0)
	{
	adminmsg("ɾ���ɹ�����ɾ��".$dnum."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'pidel_memberslog')
{
	get_token();
	$smarty->assign('pageheader',"ɾ����Ա��־");
	$smarty->display('memberslog/admin_memberslogdel.htm');
}
elseif($act == 'pidel_memberslog_del')
{
	check_token();
	$log_type=$_POST['log_type'];
	if(empty($log_type))	adminmsg('��ѡ��������ͣ�',1);
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
	if($starttime>$endtime) adminmsg('��ʼʱ�䲻�ܴ��ڽ���ʱ�䣡',1);
	$link[0]['text'] = "������־�б�";
	$link[0]['href'] = '?act=list';
	$link[1]['text'] = "����ɾ��";
	$link[1]['href'] = '?act=pidel_memberslog';
	$dnum=pidel_memberslog($log_type,$starttime,$endtime);
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