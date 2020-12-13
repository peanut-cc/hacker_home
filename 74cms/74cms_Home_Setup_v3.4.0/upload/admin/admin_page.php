<?php
 /*
 * 74cms ҳ�����
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
require_once(ADMIN_ROOT_PATH.'include/admin_page_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'page_list';
check_permissions($_SESSION['admin_purview'],"site_page");
$norewrite=array('QS_login');
$nocaching=array('QS_login','QS_jobslist','QS_jobscontrast','QS_officebuilding','QS_street','QS_jobtag','QS_resumelist','QS_resumetag','QS_simplelist','QS_helpsearch','QS_newssearch');
$smarty->assign('pageheader',"ҳ�����");
if($act == 'page_list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$total_sql="SELECT COUNT(*) AS num FROM ".table('page');
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_page($offset,$perpage,$wheresql.$oederbysql);
	$smarty->assign('list',$list);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel',"list");
	$smarty->display('page/admin_page.htm');
}
elseif($act == 'add_page')
{
	get_token();
	$smarty->assign('navlabel',"add");
	$smarty->display('page/admin_page_add.htm');
}
elseif($act == 'add_page_save')
{
	check_token();
    substr($_POST['alias'],0,3)=='QS_'?adminmsg('�������Ʋ����� QS_ ��ͷ��',1):'';
	if (ck_page_alias($_POST['alias']))
	{
	adminmsg("����ID ".$_POST['alias']." �Ѿ����ڣ���������д",1);
	exit();
	}
	if (ck_page_file($_POST['file']))
	{
	adminmsg("�ļ�·�� ".$_POST['file']." �Ѿ����ڣ���������д",1);
	exit();
	}
$setsqlarr['systemclass']=0;
$setsqlarr['pagetpye']=trim($_POST['pagetpye'])?trim($_POST['pagetpye']):1;
$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('����ID����Ϊ�գ�',1);
$setsqlarr['pname']=trim($_POST['pname'])?trim($_POST['pname']):adminmsg('��û����дҳ�����ƣ�',1);
$setsqlarr['tag']=trim($_POST['tag']);
$setsqlarr['url']=trim($_POST['url'])?trim($_POST['url']):0;
$setsqlarr['file']=trim($_POST['file'])?trim($_POST['file']):adminmsg('��û����д�ļ�·����',1);
$setsqlarr['tpl']=trim($_POST['tpl'])?trim($_POST['tpl']):adminmsg('��û����дģ��·����',1);
$setsqlarr['rewrite']=trim($_POST['rewrite']);
$setsqlarr['caching']=intval($_POST['caching']);
$setsqlarr['title']=trim($_POST['title']);
$setsqlarr['keywords']=trim($_POST['keywords']);
$setsqlarr['description']=trim($_POST['description']);
	if (inserttable(table('page'),$setsqlarr))
	{
	$link[0]['text'] = "�����б�";
	$link[0]['href'] ="?act=";	
		if ($_POST['mkdir']=="y" && $setsqlarr['html'])
		{
		ck_page_dir($setsqlarr['html']);
		}
	!copy_page($setsqlarr['file'],$setsqlarr['alias'])?adminmsg("�½���".$setsqlarr['file']."�ļ�ʧ�ܣ�����Ŀ¼Ȩ�޻����ֶ������ļ�",0):"";
	refresh_page_cache();
	refresh_nav_cache();
	adminmsg("��ӳɹ���",2,$link);
	}
	else
	{
	adminmsg("���ʧ�ܣ�",0);
	}
}
elseif($act == 'edit_page')
{
	get_token();
	$smarty->assign('list',get_page_one(intval($_GET['id'])));
	$smarty->display('page/admin_page_edit.htm');
}
elseif($act == 'edit_page_save')
{
	check_token();
	if ($_POST['systemclass']<>"1")//��ϵͳ����
	{
	$setsqlarr['pagetpye']=trim($_POST['pagetpye'])?trim($_POST['pagetpye']):1;
	$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('����ID����Ϊ�գ�',1);
	substr($_POST['alias'],0,3)=='QS_'?adminmsg('�������Ʋ����� QS_ ��ͷ��',1):'';
	}
$setsqlarr['pname']=trim($_POST['pname'])?trim($_POST['pname']):adminmsg('��û����дҳ�����ƣ�',1);
$setsqlarr['tag']=trim($_POST['tag']);
$setsqlarr['url']=trim($_POST['url'])?trim($_POST['url']):0;
$setsqlarr['file']=trim($_POST['file'])?trim($_POST['file']):adminmsg('��û����д�ļ�·����',1);
$setsqlarr['tpl']=trim($_POST['tpl'])?trim($_POST['tpl']):adminmsg('��û����дģ��·����',1);
$setsqlarr['rewrite']=trim($_POST['rewrite']);
$setsqlarr['caching']=intval($_POST['caching']);
$setsqlarr['title']=trim($_POST['title']);
$setsqlarr['keywords']=trim($_POST['keywords']);
$setsqlarr['description']=trim($_POST['description']);
	 if (in_array(trim($_POST['alias']),$nohtml) && $setsqlarr['url']=='2')
	 {
	 $setsqlarr['url']=0;
	 }
	 if (in_array(trim($_POST['alias']),$norewrite) && $setsqlarr['url']=='1')
	 {
	 $setsqlarr['url']=0;
	 }
	 if (in_array(trim($_POST['alias']),$nocaching))
	 {
	 $setsqlarr['caching']=0;
	 }
	if (ck_page_alias($_POST['alias'],$_POST['id']))
	{
	adminmsg("����ID ".$_POST['alias']." �Ѿ����ڣ���������д",1);
	exit();
	}
	if (ck_page_file($_POST['file'],$_POST['id']))
	{
	adminmsg("�ļ�·�� ".$_POST['file']." �Ѿ����ڣ���������д",1);
	exit();
	}
$wheresql=" id='".intval($_POST['id'])."'";
	if ($_POST['mkdir']=="y"  && $setsqlarr['html'])
	{
	ck_page_dir($setsqlarr['html']);
	}
	refresh_page_cache();
	refresh_nav_cache();
	if (updatetable(table('page'),$setsqlarr,$wheresql))
	{
	refresh_page_cache();
	adminmsg("�޸ĳɹ���",2);
	}
	else
	{
	adminmsg("�޸�ʧ�ܣ�",0);
	}
}
elseif($act == 'del_page')
{
	check_token();
	$id=$_REQUEST['id'];
	if (empty($id)) adminmsg("��ѡ����Ŀ��",0);
	if ($num=del_page($id))
	{
	refresh_page_cache();
	refresh_nav_cache();
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�".$num,1);
	}
}
elseif($act == 'set_page')
{
	check_token();
	$id =!empty($_POST['id'])?$_POST['id']:adminmsg("��û��ѡ��ҳ�棡",1);
	if ($_POST['set_url'])//����ҳ������
	{
		if (set_page_url($id,$_POST['url'],$norewrite))
		{
		refresh_page_cache();
		refresh_nav_cache();
		adminmsg("���óɹ���",2);		
		}
		else
		{
		adminmsg("����ʧ�ܣ�",0);
		}
	}
	if ($_POST['set_caching'])//����ҳ�滺��ʱ��
	{		
		if (set_page_caching($id,$_POST['caching'],$nocaching))
		{
		refresh_page_cache();
		adminmsg("���óɹ���",2);
		adminmsg("����ʧ�ܣ�",0);
		}
		else
		{
		adminmsg("����ʧ�ܣ�",0);;
		}
	}
}
?>