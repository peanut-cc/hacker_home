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
$smarty->assign('leftmenu',"user");
if ($act=='userprofile')
{
	$smarty->assign('user',$user);
	$smarty->assign('title','�������� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('userprofile',get_userprofile($_SESSION['uid']));	
	$smarty->display('member_personal/personal_userprofile.htm');
}
elseif ($act=='userprofile_save')
{
	$setsqlarr['realname']=trim($_POST['realname'])?trim($_POST['realname']):showmsg('����д��ʵ������',1);
	$setsqlarr['sex']=trim($_POST['sex']);
	$setsqlarr['birthday']=trim($_POST['birthday']);
	$setsqlarr['addresses']=trim($_POST['addresses'])?trim($_POST['addresses']):showmsg('����дͨѶ��ַ',1);
	$setsqlarr['phone']=trim($_POST['phone']);
	$setsqlarr['qq']=trim($_POST['qq']);
	$setsqlarr['msn']=trim($_POST['msn']);
	$setsqlarr['profile']=trim($_POST['profile']);
	if (get_userprofile($_SESSION['uid']))
	{
	$wheresql=" uid='".intval($_SESSION['uid'])."'";
	write_memberslog($_SESSION['uid'],2,1005,$_SESSION['username'],"�޸��˸�������");
	!updatetable(table('members_info'),$setsqlarr,$wheresql)?showmsg("�޸�ʧ�ܣ�",0):showmsg("�޸ĳɹ���",2);
	}
	else
	{
	$setsqlarr['uid']=intval($_SESSION['uid']);
	write_memberslog($_SESSION['uid'],2,1005,$_SESSION['username'],"�޸��˸�������");
	!inserttable(table('members_info'),$setsqlarr)?showmsg("�޸�ʧ�ܣ�",0):showmsg("�޸ĳɹ���",2);
	}
}
//ͷ��
elseif ($act=='avatars')
{
	$smarty->assign('title','����ͷ�� - ��Ա���� - '.$_CFG['site_name']);
	$smarty->assign('user',$user);
	$smarty->assign('rand',rand(1,100));
	$smarty->display('member_personal/personal_avatars.htm');
}
elseif ($act=='avatars_save')
{
	require_once(QISHI_ROOT_PATH.'include/upload.php');
	!$_FILES['avatars']['name']?showmsg('���ϴ�ͼƬ��',1):"";
	$up_dir_100="../../data/avatar/100/";
	$up_dir_48="../../data/avatar/48/";
	make_dir($up_dir_100.date("Y/m/d/"));
	make_dir($up_dir_48.date("Y/m/d/"));
	$setsqlarr['avatars']=_asUpFiles($up_dir_100.date("Y/m/d/"),"avatars",500,'gif/jpg/bmp/png',$_SESSION['uid']);
	$setsqlarr['avatars']=date("Y/m/d/").$setsqlarr['avatars'];
	if ($setsqlarr['avatars'])
	{
	makethumb($up_dir_100.$setsqlarr['avatars'],$up_dir_100.date("Y/m/d/"),100,100);
	makethumb($up_dir_100.$setsqlarr['avatars'],$up_dir_48.date("Y/m/d/"),48,48);
	$wheresql=" uid='".$_SESSION['uid']."'";
	write_memberslog($_SESSION['uid'],2,1006 ,$_SESSION['username'],"�޸��˸���ͷ��");
	updatetable(table('members'),$setsqlarr,$wheresql)?showmsg('����ɹ���',2):showmsg('����ʧ�ܣ�',1);
	}
	else
	{
	showmsg('����ʧ�ܣ�',1);
	}
}
//�޸�����
elseif ($act=='password_edit')
{
	$smarty->assign('title','�޸����� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_password.htm');
}
//�����޸�����
elseif ($act=='save_password')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	$arr['username']=$_SESSION['username'];
	$arr['oldpassword']=trim($_POST['oldpassword'])?trim($_POST['oldpassword']):showmsg('����������룡',1);
	$arr['password']=trim($_POST['password'])?trim($_POST['password']):showmsg('�����������룡',1);
	if ($arr['password']!=trim($_POST['password1'])) showmsg('�����������벻��ͬ�����������룡',1);
	$info=edit_password($arr);
	if ($info==-1) showmsg('����������������������룡',1);
	if ($info==$_SESSION['username']){
			//�����ʼ�
			$mailconfig=get_cache('mailconfig');
			if ($mailconfig['set_editpwd']=="1" && $user['email_audit']=="1")
			{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_editpwd&newpassword=".$arr['password']);
			}
			//�ʼ��������
			//sms
			$sms=get_cache('sms_config');
			if ($sms['open']=="1" && $sms['set_editpwd']=="1"  && $user['mobile_audit']=="1")
			{
			dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$_SESSION['uid']."&key=".asyn_userkey($_SESSION['uid'])."&act=set_editpwd&newpassword=".$arr['password']);
			}
 	 write_memberslog($_SESSION['uid'],2,1004 ,$_SESSION['username'],"�޸�����");
	 showmsg('�����޸ĳɹ���',2);
	 }
}
//��Ա״̬
elseif ($act=='user_status')
{
	$smarty->assign('user_status',$user['status']);
	$smarty->assign('title','�˺�״̬ - ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_user_status.htm');
}
//�����Ա״̬
elseif ($act=='user_status_save')
{
	!set_user_status($_POST['status'],$_SESSION['uid'])?showmsg('����ʧ�ܣ�',1):showmsg('����ɹ�',2);
}
elseif ($act=='user_email')
{
	$smarty->assign('user',$user);
	$smarty->assign('re_audit',$_GET['re_audit']);
	$smarty->assign('title','��֤���� - ���˻�Ա���� - '.$_CFG['site_name']);
	$_SESSION['send_key']=mt_rand(100000, 999999);
	$smarty->assign('send_key',$_SESSION['send_key']);
	$smarty->display('member_personal/personal_user_email.htm');
}
elseif ($act=='user_mobile')
{
	$smarty->assign('user',$user);
	$smarty->assign('re_audit',$_GET['re_audit']);
	$smarty->assign('title','�ֻ���֤ - ���˻�Ա���� - '.$_CFG['site_name']);
	$_SESSION['send_key']=mt_rand(100000, 999999);
	$smarty->assign('send_key',$_SESSION['send_key']);
	$smarty->display('member_personal/personal_user_mobile.htm');
}
elseif ($act=='feedback')
{
	$smarty->assign('title','�û����� - ���˻�Ա���� - '.$_CFG['site_name']);
	$smarty->assign('feedback',get_feedback($_SESSION['uid']));
	$smarty->display('member_personal/personal_feedback.htm');
}
//�����û�����
elseif ($act=='feedback_save')
{
	$get_feedback=get_feedback($_SESSION['uid']);
	if (count($get_feedback)>=5) 
	{
	showmsg('������Ϣ���ܳ���5����',1);
	exit();
	}
	$setsqlarr['infotype']=intval($_POST['infotype']);
	$setsqlarr['feedback']=trim($_POST['feedback'])?trim($_POST['feedback']):showmsg('����д���ݣ�',1);
	$setsqlarr['uid']=$_SESSION['uid'];
	$setsqlarr['usertype']=$_SESSION['utype'];
	$setsqlarr['username']=$_SESSION['username'];
	$setsqlarr['addtime']=$timestamp;
	write_memberslog($_SESSION['uid'],2,7001,$_SESSION['username'],"��ӷ�����Ϣ");
	!inserttable(table('feedback'),$setsqlarr)?showmsg("���ʧ�ܣ�",0):showmsg("��ӳɹ�����ȴ�����Ա�ظ���",2);
}
//ɾ���û�����
elseif ($act=='del_feedback')
{
	$id=intval($_GET['id']);
	del_feedback($id,$_SESSION['uid'])?showmsg('ɾ���ɹ���',2):showmsg('ɾ��ʧ�ܣ�',1);
}
elseif ($act=='pm')
{
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$perpage=10;
	$uid=intval($_SESSION['uid']);
	$new=intval($_GET['new']);
	$msgtype=intval($_GET['msgtype']);
	$wheresql=" WHERE (p.msgfromuid='{$uid}' OR p.msgtouid='{$uid}') ";
	$joinsql=" LEFT JOIN  ".table('members')." AS i  ON  p.msgfromuid=i.uid ";
	$orderby=" order by p.pmid desc";
	if ($new>0)
	{
	$wheresql.=" AND p.`new`='{$new}' AND p.`replyuid`<>'{$uid}' ";
	}
	if ($msgtype>0)
	{
	$wheresql.=" AND p.msgtype='{$msgtype}' ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('pms').' AS p '.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$sql="SELECT p.*,i.avatars FROM ".table('pms').' AS p'.$joinsql.$wheresql.$orderby;
	$smarty->assign('pms',get_pms($offset,$perpage,$sql));
	$smarty->assign('total1',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1' AND `replyuid`<>'{$uid}'"));
	$smarty->assign('total2',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `msgtype`='1'"));
	$smarty->assign('total3',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `msgtype`='2'"));
	$smarty->assign('title','����Ϣ - ��Ա���� - '.$_CFG['site_name']);	
	$smarty->assign('page',$page->show(3));
	$smarty->assign('uid',$uid);
	$smarty->display('member_personal/personal_user_pm.htm');
}
elseif ($act=='pm_show')
{
	$uid=intval($_SESSION['uid']);
	$pmid=$_GET['pmid'];
	$show=get_pms_one($_GET['pmid'],$uid);
	if (empty($show))
	{
	exit();
	}
	$smarty->assign('show',$show);
	if ($show['msgtype']!='1')//��ϵͳ��Ϣ
	{
	$smarty->assign('reply',get_pms_reply($show['pmid']));
	}
	$db->query("UPDATE ".table('pms')." SET `new`='2' WHERE pmid='{$show['pmid']}' AND replyuid<>'{$uid}'");	
	$db->query("UPDATE ".table('pms_reply')." SET `new`='2' WHERE pmid='{$show['pmid']}' AND replyuid<>'{$uid}'");
	$total1=$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `new`='1' AND `replyuid`<>'{$uid}'");
	$smarty->assign('total1',$total1);
	setcookie('QS[pmscount]',$total1,0,$QS_cookiepath,$QS_cookiedomain);
	$smarty->assign('total2',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `msgtype`='1'"));
	$smarty->assign('total3',$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$uid}' OR msgtouid='{$uid}') AND `msgtype`='2'"));
 	$smarty->assign('title','��Ϣ��¼ - ��Ա���� - '.$_CFG['site_name']); 
	$smarty->assign('uid',$uid);
	$smarty->display('member_personal/personal_user_pm_show.htm');
	//ͳ����Ϣ
	$pmscount=$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$_SESSION['uid']}' OR msgtouid='{$_SESSION['uid']}') AND `new`='1' AND `replyuid`<>'{$_SESSION['uid']}'");
	setcookie('QS[pmscount]',$pmscount, $expire,$QS_cookiepath,$QS_cookiedomain);
}
elseif ($act=='pm_reply_save')
{
	$setsqlarr['pmid']=intval($_POST['pmid']);
	$setsqlarr['replyuid']=intval($_SESSION['uid']);
	$setsqlarr['replyusername']=trim($_SESSION['username']);
	$setsqlarr['new']=1;
	$setsqlarr['replytime']=time();
	$setsqlarr['replytext']=trim($_POST['replytext']);
	inserttable(table('pms_reply'),$setsqlarr);
	$pms['new']=1;
	$pms['replytime']=$setsqlarr['replytime'];
	$pms['replyuid']=$setsqlarr['replyuid'];
	updatetable(table('pms'),$pms," pmid='{$setsqlarr['pmid']}'");
	showmsg("�ظ��ɹ���",2);
}
elseif ($act=='pm_del')
{
	$pmid=intval($_GET['pmid']);
	$uid=intval($_SESSION['uid']);
	$pms= $db->getone("select * from ".table('pms')." where pmid = '{$pmid}' AND (msgfromuid='{$uid}' OR msgtouid='{$uid}') LIMIT 1");
	if (!empty($pms))
	{
	$db->query("Delete from ".table('pms')." WHERE pmid='{$pms['pmid']}'");
	$db->query("Delete from ".table('pms_reply')." WHERE pmid='{$pms['pmid']}'");
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=pm&msgtype={$_GET['msgtype']}&new={$_GET['new']}";
	//ͳ����Ϣ
	$pmscount=$db->get_total("SELECT COUNT(*) AS num FROM ".table('pms')." WHERE (msgfromuid='{$_SESSION['uid']}' OR msgtouid='{$_SESSION['uid']}') AND `new`='1' AND `replyuid`<>'{$_SESSION['uid']}'");
	setcookie('QS[pmscount]',$pmscount, $expire,$QS_cookiepath,$QS_cookiedomain);
	showmsg("�����ɹ���",2,$link);
}
elseif ($act=='buddy')
{
	$perpage=30;
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$smarty->assign('title','�����б� - ��Ա���� - '.$_CFG['site_name']);
	$uid=intval($_SESSION['uid']);
	$wheresql=" WHERE b.uid='{$uid}' ";
	$joinsql=" LEFT JOIN  ".table('members')." AS m  ON  b.tuid=m.uid ";
	$orderby=" ORDER BY b.id DESC";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_buddy').' AS b '.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$sql="SELECT b.*,m.username,m.avatars FROM ".table('members_buddy').' AS b '.$joinsql.$wheresql.$orderby;
	$smarty->assign('buddy',get_buddy($offset,$perpage,$sql));
	$smarty->assign('page',$page->show(3));
	$smarty->display('member_personal/personal_user_buddy.htm');
}
elseif ($act=='buddy_del')
{
	$id=intval($_GET['id']);
	$uid=intval($_SESSION['uid']);
	$db->query("Delete from ".table('members_buddy')." WHERE id='{$id}' and uid='{$uid}'");
	showmsg("�����ɹ���",2);
}
elseif ($act=='del_qq_binding')
{
	$db->query("UPDATE ".table('members')." SET qq_openid = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	showmsg('�����ɹ���',2);
}
elseif ($act=='del_sina_binding')
{
	$db->query("UPDATE ".table('members')." SET sina_access_token = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	showmsg('�����ɹ���',2);
}
elseif ($act=='del_taobao_binding')
{
	$db->query("UPDATE ".table('members')." SET taobao_access_token = ''  WHERE uid='{$_SESSION[uid]}' LIMIT 1");
	showmsg('�����ɹ���',2);
}

//��Ա��¼��־
elseif ($act=='login_log')
{
	require_once(QISHI_ROOT_PATH.'include/fun_user.php');
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE log_uid='{$_SESSION['uid']}' AND log_type='1001' ";
	$settr=intval($_GET['settr']);
	if($settr>0)
	{
	$settr_val=strtotime("-".$settr." day");
	$wheresql.=" AND log_addtime >".$settr_val;
	}
	$perpage=15;
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_log').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('loginlog',get_user_loginlog($offset, $perpage,$wheresql));
	$smarty->assign('page',$page->show(3));
	$smarty->assign('title','��Ա��¼��־ - ��ҵ��Ա���� - '.$_CFG['site_name']);
	$smarty->display('member_personal/personal_user_loginlog.htm');
}


unset($smarty);
?>