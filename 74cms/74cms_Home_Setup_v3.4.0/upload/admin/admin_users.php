<?php
 /*
 * 74cms ����Ա�˻�
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
require_once(ADMIN_ROOT_PATH.'include/admin_users_fun.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
$smarty->assign('pageheader',"��վ����Ա");
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	if ($_SESSION['admin_purview']<>"all")
	{
		$wheresql=" WHERE admin_name='".$_SESSION['admin_name']."'";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('admin').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_admin_list($offset,$perpage,$wheresql);	
	$smarty->assign('list',$list);
	$smarty->assign('admin_purview',$_SESSION['admin_purview']);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel','list');	
	$smarty->display('users/admin_users_list.htm');
}
elseif($act == 'add_users')
{
	get_token();
	if ($_SESSION['admin_purview']<>"all")adminmsg("Ȩ�޲��㣡",1);
	$smarty->assign('navlabel','add');	
	$smarty->display('users/admin_users_add.htm');
}
elseif($act == 'add_users_save')
{
	check_token();
	if ($_SESSION['admin_purview']<>"all")adminmsg("Ȩ�޲��㣡",1);
	$setsqlarr['admin_name']=trim($_POST['admin_name'])?trim($_POST['admin_name']):adminmsg('����д�û�����',1);
	if (get_admin_one($setsqlarr['admin_name']))adminmsg('�û����Ѿ����ڣ�',1);
	$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):adminmsg('����дemail��',1);
	if (!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/",$setsqlarr['email']))adminmsg('email��ʽ����',1);
	$password=trim($_POST['password'])?trim($_POST['password']):adminmsg('����д����',1);
	if (strlen($password)<6)adminmsg('���벻������6λ��',1);
	if ($password<>trim($_POST['password1']))adminmsg('������������벻��ͬ��',1);
	$setsqlarr['rank']=trim($_POST['rank'])?trim($_POST['rank']):adminmsg('����дͷ��',1);
	$setsqlarr['add_time']=time();
	$setsqlarr['last_login_time']=0;
	$setsqlarr['last_login_ip']="��δ";
	$setsqlarr['pwd_hash']=randstr();
	$setsqlarr['pwd']=md5($password.$setsqlarr['pwd_hash'].$QS_pwdhash);		
	if (inserttable(table('admin'),$setsqlarr))
	{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] ="?act=";
		adminmsg('��ӳɹ���',2,$link);
	}
	else
	{
	adminmsg('���ʧ��',1);
	}	
}
elseif($act == 'del_users')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_users($id,$_SESSION['admin_purview']))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'edit_users')
{
	get_token();
	$id=intval($_GET['id']);
	$account=get_admin_account($id);
	if ($account['admin_name']==$_SESSION['admin_name'] || $_SESSION['admin_purview']=="all")
	{
	$smarty->assign('account',$account);
	$smarty->assign('admin_purview',$_SESSION['admin_purview']);
	$smarty->display('users/admin_users_edit.htm');
	}
	else
	{
	adminmsg("��������",1);
	}
}
elseif($act == 'edit_users_pwd')
{
	get_token();
	$id=intval($_GET['id']);
	$account=get_admin_account($id);
	if ($account['admin_name']==$_SESSION['admin_name'] || $_SESSION['admin_purview']=="all")
	{
	$smarty->assign('account',$account);
	$smarty->assign('admin_purview',$_SESSION['admin_purview']);
	$smarty->display('users/admin_users_edit_pwd.htm');
	}
	else
	{
	adminmsg("��������",1);
	}
}
elseif($act == 'edit_users_info_save' && $_SESSION['admin_purview']=="all")//��������Ա�ſ����޸�����
{
	check_token();
		$id=intval($_POST['id']);
		$account=get_admin_account($id);
		if ($account['purview']=="all")adminmsg("��������",1);//��������Ա�����ϲ����޸�
		$setsqlarr['admin_name']=trim($_POST['admin_name'])?trim($_POST['admin_name']):adminmsg('�û�������Ϊ�գ�',1);
		$setsqlarr['email']=trim($_POST['email'])?trim($_POST['email']):adminmsg('email����Ϊ�գ�',1);
		$setsqlarr['rank']=trim($_POST['rank'])?trim($_POST['rank']):adminmsg('ͷ�β���Ϊ�գ�',1);
			$sql = "select * from ".table('admin')." where admin_name = '".$$setsqlarr['admin_name']."' AND admin_id<>".$id;
			$ck_info=$db->getone($sql);
			if (!empty($ck_info))adminmsg("�û������ظ���",1);
		if (updatetable(table('admin'),$setsqlarr,' admin_id='.$id))
		{
			adminmsg("�޸ĳɹ���",2);
		 }
		 else
		{
			adminmsg("�޸�ʧ�ܣ�",0);
		 }
}
elseif($act == 'edit_users_pwd_save')
{
	check_token();
	$id=intval($_POST['id']);
	$account=get_admin_account($id);
	if ($account['purview']=="all" && $_SESSION['admin_purview']=="all")
	{
				if (strlen($_POST['password'])<6)adminmsg("���볤�Ȳ���С��6λ��",1);
				if ($_POST['password']<>$_POST['password1'])adminmsg("������������벻ͬ��",1);		
				$md5_pwd=md5($_POST['old_password'].$account['pwd_hash'].$QS_pwdhash);
				if ($md5_pwd<>$account['pwd'])adminmsg("�������������",1);
				$setsqlarr['pwd']=md5($_POST['password'].$account['pwd_hash'].$QS_pwdhash);
				if (updatetable(table('admin'),$setsqlarr,' admin_id='.$id))
				{
					adminmsg("�޸ĳɹ���",2);
				 }
				 else
				 {
					adminmsg("�޸�ʧ�ܣ�",0);
				 }
	}
	else
	{
				if ($_SESSION['admin_purview']=="all")
				{
					if (strlen($_POST['password'])<6)adminmsg("���볤�Ȳ���С��6λ��",1);
					$setsqlarr['pwd']=md5($_POST['password'].$account['pwd_hash'].$QS_pwdhash);
					if (!updatetable(table('admin'),$setsqlarr,' admin_id='.$id)) adminmsg("�޸�ʧ�ܣ�",0);
				}
				else
				{
					if (strlen($_POST['password'])<6)adminmsg("���볤�Ȳ���С��6λ��",1);
					if ($_POST['password']<>$_POST['password1'])adminmsg("������������벻ͬ��",1);		
					$md5_pwd=md5($_POST['old_password'].$account['pwd_hash'].$QS_pwdhash);
					if ($md5_pwd<>$account['pwd'])adminmsg("�������������",1);
					$setsqlarr['pwd']=md5($_POST['password'].$account['pwd_hash'].$QS_pwdhash);
					if (!updatetable(table('admin'),$setsqlarr,' admin_id='.$id)) adminmsg("�޸�ʧ�ܣ�",0);
				}
				 adminmsg("�޸ĳɹ���",2);
	}
}
elseif($act == 'loglist')
{
	get_token();
	$adminname=trim($_GET['adminname']);
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	if ($_SESSION['admin_purview']=="all")//��������Ա���Բ鿴�κι���Ա����־
	{
		$wheresql="";
	}
	else
	{
		$wheresql=" WHERE admin_name='".$_SESSION['admin_name']."'";
	}
	if (!empty($_GET['log_type']))
	{
		$wheresql=empty($wheresql)?" WHERE log_type= ".intval($_GET['log_type']):$wheresql." AND log_type=".intval($_GET['log_type']);
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('admin_log').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_admin_log($offset,$perpage,$wheresql);
	$smarty->assign('pageheader',"��¼��־");
	$smarty->assign('list',$list);//�б�
	$smarty->assign('perpage',$perpage);//ÿҳ��ʾ����POST
		if ($total_val>$perpage)
		{
		$smarty->assign('page',$page->show(3));//��ҳ��
		}
	$smarty->display('users/admin_users_log.htm');
}
elseif($act == 'users_set')
{
	get_token();
	$id=intval($_GET['id']);
	$account=get_admin_account($id);
	$smarty->assign('account',$account);
	$smarty->assign('admin_purview',$_SESSION['admin_purview']);
	$smarty->assign('admin_set',explode(',',$account['purview']));
	$smarty->display('users/admin_users_set.htm');
}
elseif($act == 'users_set_save')
{
	check_token();
	$id=intval($_POST['id']);
	if ($_SESSION['admin_purview']<>"all")adminmsg("Ȩ�޲��㣡",1);
	$setsqlarr['purview']=$_POST['purview'];
	$setsqlarr['purview']=implode(',',$setsqlarr['purview']);
		if (updatetable(table('admin'),$setsqlarr,' admin_id='.$id))
		{
			adminmsg("���óɹ���",2);
		 }
		 else
		{
			adminmsg("����ʧ�ܣ�",0);
		 }
}
?>