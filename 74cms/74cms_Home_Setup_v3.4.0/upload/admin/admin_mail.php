<?php
 /*
 * 74cms �ʼ�����
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
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'email_set';
check_permissions($_SESSION['admin_purview'],"site_mail");
$smarty->assign('pageheader',"�ʼ�����");
if($act == 'email_set')
{
	get_token();
	$mailconfig=get_cache('mailconfig');
	$mailconfig['smtpservers']=explode('|-_-|',$mailconfig['smtpservers']);
	$mailconfig['smtpusername']=explode('|-_-|',$mailconfig['smtpusername']);
	$mailconfig['smtppassword']=explode('|-_-|',$mailconfig['smtppassword']);
	$mailconfig['smtpfrom']=explode('|-_-|',$mailconfig['smtpfrom']);
	$mailconfig['smtpport']=explode('|-_-|',$mailconfig['smtpport']);
	for ($i=0; $i<count($mailconfig['smtpservers']); $i++)
	{
	$mailconfigli[]=array('smtpservers'=>$mailconfig['smtpservers'][$i],'smtpusername'=>$mailconfig['smtpusername'][$i],'smtppassword'=>$mailconfig['smtppassword'][$i],'smtpfrom'=>$mailconfig['smtpfrom'][$i],'smtpport'=>$mailconfig['smtpport'][$i]);
	}
	$smarty->assign('mailconfig',$mailconfig);
	$smarty->assign('mailconfigli',$mailconfigli);
	$smarty->assign('navlabel','set');
	$smarty->display('mail/admin_mail_set.htm');
}
elseif($act == 'email_set_save')
{
	check_token();
	header("Cache-control: private");
	if (intval($_POST['method'])=="1")
	{
		for ($i=0; $i<count($_POST['smtpservers']); $i++)
		{
			 if (empty($_POST['smtpservers'][$i]) || empty($_POST['smtpusername'][$i]) || empty($_POST['smtppassword'][$i]) || empty($_POST['smtpfrom'][$i]) || empty($_POST['smtpport'][$i]))
			{
			adminmsg('����д�����ϲ�����!',1);
			}
		}
		$_POST['smtpservers']=implode('|-_-|',$_POST['smtpservers']);
		$_POST['smtpusername']=implode('|-_-|',$_POST['smtpusername']);
		$_POST['smtppassword']=implode('|-_-|',$_POST['smtppassword']);
		$_POST['smtpfrom']=implode('|-_-|',$_POST['smtpfrom']);
		$_POST['smtpport']=implode('|-_-|',$_POST['smtpport']);
	}
	foreach($_POST as $k => $v){
	!$db->query("UPDATE ".table('mailconfig')." SET value='$v' WHERE name='$k'")?adminmsg('����վ������ʧ��', 1):"";
	}
	refresh_cache('mailconfig');
	adminmsg("����ɹ���",2);
}
if($act == 'testing')
{
	get_token();
	$smarty->assign('navlabel','testing');
	$smarty->display('mail/admin_mail_testing.htm');
}
elseif($act == 'email_testing')
{
	check_token();
	$mailconfig=get_cache('mailconfig');
	$txt="���ã�����һ�����ʼ����������õĲ����ʼ����յ����ʼ�����ζ�������ʼ�������������ȷ�������Խ��������ʼ����͵Ĳ����ˣ�";
	$check_smtp=trim($_POST['check_smtp'])?trim($_POST['check_smtp']):adminmsg('�ռ��˵�ַ������д', 1);
	if (!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/",$check_smtp))adminmsg('email��ʽ����',1);
	if (smtp_mail($check_smtp,"��ʿCMS�����ʼ�",$txt))
	{
	adminmsg('�����ʼ����ͳɹ���',2);
	}
	else
	{
	adminmsg('�����ʼ�����ʧ�ܣ�',1);
	}
}
elseif($act == 'email_set_templates')
{
	get_token();
	$smarty->assign('navlabel','templates');
	$smarty->assign('mailconfig',get_cache('mailconfig'));
	$smarty->display('mail/admin_mail_templates.htm');
}
elseif($act == 'rule')
{
	get_token();
	$smarty->assign('navlabel','rule');
	$smarty->assign('mailconfig',get_cache('mailconfig'));
	$smarty->display('mail/admin_mail_rule.htm');
}
elseif($act == 'email_rule_save')
{
	check_token();
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('mailconfig')." SET value='$v' WHERE name='$k'")?adminmsg('����վ������ʧ��', 1):"";
	}
	refresh_cache('mailconfig');
	adminmsg("����ɹ���",2);
}
elseif($act == 'mail_templates_edit')
{
	get_token();
	$templates_name=trim($_GET['templates_name']);
	$label=array();
	$label[]=array('{sitename}','��վ����');
	$label[]=array('{sitedomain}','��վ����');
	//���ɱ�ǩ
	if ($templates_name=='set_reg')
	{
	$label[]=array('{username}','�û���');
	$label[]=array('{password}','����');
	}
	elseif ($templates_name=='set_applyjobs')
	{
	$label[]=array('{personalfullname}','������');
	$label[]=array('{jobsname}','����ְλ����');
	}
	elseif ($templates_name=='set_invite')
	{
	$label[]=array('{companyname}','���뷽(��˾����)');
	}
	elseif ($templates_name=='set_order')
	{
	$label[]=array('{paymenttpye}','���ʽ');
	$label[]=array('{amount}','���');
	$label[]=array('{oid}','������');
	}
	elseif ($templates_name=='set_editpwd')
	{
	$label[]=array('{newpassword}','������');
	}
	elseif ($templates_name=='set_jobsallow' || $templates_name=='set_jobsnotallow')
	{
	$label[]=array('{jobsname}','ְλ����');
	}
	//-end
	if ($templates_name)
	{
		$sql = "select * from ".table('mail_templates')." where name='".$templates_name."'";
	$info=$db->getone($sql);
		$sql = "select * from ".table('mail_templates')." where name='".$templates_name."_title'";
	$title=$db->getone($sql);
	}
	$info['thisname']=trim($_GET['thisname']);
	$smarty->assign('info',$info);
	$smarty->assign('title',$title);
 	$smarty->assign('label',$label);
	$smarty->assign('navlabel','templates');
	$smarty->display('mail/admin_mail_templates_edit.htm');
}
elseif($act == 'templates_save')
{
	check_token();
	$templates_value=trim($_POST['templates_value']);
	$templates_name=trim($_POST['templates_name']);
	$title=trim($_POST['title']);
	!$db->query("UPDATE ".table('mail_templates')." SET value='".$templates_value."' WHERE name='".$templates_name."'")?adminmsg('����ʧ��', 1):"";
	!$db->query("UPDATE ".table('mail_templates')." SET value='".$title."' WHERE name='".$templates_name."_title'")?adminmsg('����ʧ��', 1):"";
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] ="?act=email_set_templates";
	refresh_cache('mail_templates');
	adminmsg("����ɹ���",2,$link);
}
 elseif($act == 'send')
{
	get_token();
	$smarty->assign('pageheader',"�ʼ�Ӫ��");
	
	require_once(dirname(__FILE__).'/include/admin_mailqueue_fun.php');
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$uid=intval($_GET['uid']);
	$email=trim($_GET['email']);
	
	$wheresql=' WHERE m_uid='.$uid.' ORDER BY m_id DESC ';
	$total_sql="SELECT COUNT(*) AS num FROM ".table('mailqueue').$wheresql;
	$perpage=10;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$maillog = get_mailqueue($offset,$perpage,$wheresql);
	
	$url=trim($_REQUEST['url']);
	if (empty($url))
	{
	$url="?act=send&email={$email}&uid={$uid}";
	}
	$smarty->assign('url',$url);
	$smarty->assign('maillog',$maillog);
	$smarty->assign('page',$page->show(3));
	$smarty->display('mail/admin_mail_send.htm');
}
elseif($act == 'email_send')
{
	check_token();
	$uid=intval($_POST['uid']);
	$url=trim($_REQUEST['url']);
	if (!$uid)
	{
	adminmsg('�û�UID����',0);
	}
	$setsqlarr['m_mail']=trim($_POST['email'])?trim($_POST['email']):adminmsg('�ʼ���ַ������д��',1);
	if (!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$setsqlarr['m_mail'])) 
    {
	adminmsg('�����ʽ����',1);
    }
	$setsqlarr['m_subject']=trim($_POST['subject'])?trim($_POST['subject']):adminmsg('�ʼ����������д��',1);	
	$setsqlarr['m_body']=trim($_POST['body'])?trim($_POST['body']):adminmsg('�ʼ����ݱ�����д��',1);
	$setsqlarr['m_addtime']=time();
	$setsqlarr['m_uid']=$uid;
	if(smtp_mail($setsqlarr['m_mail'],$setsqlarr['m_subject'],$setsqlarr['m_body'])){
		$setsqlarr['m_sendtime']=time();
		$setsqlarr['m_type']=1;//���ͳɹ�
		inserttable(table('mailqueue'),$setsqlarr);
		unset($setsqlarr);
		$link[0]['text'] = "������һҳ";
		$link[0]['href'] = "{$url}";
		adminmsg("���ͳɹ���",2,$link);
	}
	else
	{
		$setsqlarr['m_sendtime']=time();
		$setsqlarr['m_type']=2;//����ʧ��
		inserttable(table('mailqueue'),$setsqlarr);
		unset($setsqlarr);
		$link[0]['text'] = "������һҳ";
		$link[0]['href'] = "{$url}";
		adminmsg("����ʧ�ܣ�����δ֪��",0,$link);
	}
}
elseif ($act=='again_send')
{
	$id=intval($_GET['id']);
	if (empty($id))
	{
	adminmsg("��ѡ��Ҫ���͵���Ŀ��",1);
	}
	$result = $db->getone("SELECT * FROM ".table('mailqueue')." WHERE  m_id = {$id} limit 1");
	$wheresql=" m_id={$id} ";
	if(smtp_mail($result['m_mail'],$result['m_subject'],$result['m_body'])){
		$setsqlarr['m_sendtime']=time();
		$setsqlarr['m_type']=1;//���ͳɹ�
		!updatetable(table('mailqueue'),$setsqlarr,$wheresql);
		adminmsg('���ͳɹ�',2);
	}else{
		$setsqlarr['m_sendtime']=time();
		$setsqlarr['m_type']=2;
		!updatetable(table('mailqueue'),$setsqlarr,$wheresql);
		adminmsg('����ʧ��',0);
	}
		
}
elseif ($act=='del')
{
	$id=$_POST['id'];
	if (empty($id))
	{
	adminmsg("��ѡ����Ŀ��",1);
	}
	if(!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	$db->query("Delete from ".table('mailqueue')." WHERE m_id IN ({$sqlin}) ");
	adminmsg("ɾ���ɹ�",2);
	}
}

?>