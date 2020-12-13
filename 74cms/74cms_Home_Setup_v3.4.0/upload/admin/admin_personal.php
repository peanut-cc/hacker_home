<?php
 /*
 * 74cms ����
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
require_once(ADMIN_ROOT_PATH.'include/admin_personal_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
if($act == 'list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"resume_show");
	$tabletype=intval($_GET['tabletype']);
	$audit=intval($_GET['audit']);
	if (empty($tabletype))
	{
		$tabletype=1;
		$_GET['tabletype']=1;
	}
	if ($tabletype==1)
	{
	$tablename="resume";
	$audit="";
	}
	else
	{
	$tablename="resume_tmp";
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY refreshtime DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE fullname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE id='".intval($key)."'";
		elseif ($key_type===3)$wheresql=" WHERE uid='".intval($key)."'";	
		elseif ($key_type===4)$wheresql=" WHERE telephone like '%{$key}%'";	
		elseif ($key_type===5)$wheresql=" WHERE qq like '%{$key}%'";
		elseif ($key_type===6)$wheresql=" WHERE address like '%{$key}%'";
		$oederbysql="";
		$tablename="all";
	}
	else
	{
		$photo_audit=intval($_GET['photo_audit']);
		!empty($audit)? $wheresqlarr['audit']=$audit:'';
		!empty($_GET['talent'])? $wheresqlarr['talent']=intval($_GET['talent']):'';
		if ($photo_audit>0)
		{
			$wheresqlarr['photo_audit']=$photo_audit;
			$oederbysql="";
		}
		if ($_GET['photo']<>'')
		{
		$wheresqlarr['photo']=intval($_GET['photo']);
		$oederbysql=" order BY addtime DESC ";
		}
		if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);	
		if (!empty($_GET['addtimesettr']))
		{
			$settr=strtotime("-".intval($_GET['addtimesettr'])." day");
			$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
			$oederbysql=" order BY addtime DESC ";
		}
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql=empty($wheresql)?" WHERE refreshtime> ".$settr:$wheresql." AND refreshtime> ".$settr;
		}
		if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_resume']=="1")
		{
				$wheresql.=empty($wheresql)?" WHERE ":" AND ";
				$wheresql.=" (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
		}
	}
	if ($tablename=="all")
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table('resume').$wheresql." UNION ALL SELECT COUNT(*) AS num FROM ".table('resume_tmp').$wheresql;
	}
	else
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table($tablename).$wheresql;
	}
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	if ($tablename=="all")
	{
	$getsql="SELECT * FROM ".table('resume').$wheresql." UNION ALL SELECT * FROM ".table('resume_tmp').$wheresql;
	}
	else
	{
	$getsql="SELECT * FROM ".table($tablename)." ".$wheresql.$oederbysql;
	}
	$resumelist = get_resume_list($offset,$perpage,$getsql);
	$total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume')."");
	$total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')."");
	if ($tabletype===2)
	{
	$total[2]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')." WHERE audit=1 ");
	$total[3]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')." WHERE audit=2 ");
	$total[4]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('resume_tmp')." WHERE audit=3 ");
	}
	$smarty->assign('total',$total);
	$smarty->assign('pageheader',"�����б�");
	$smarty->assign('resumelist',$resumelist);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('total_val',$total_val);
	$smarty->display('personal/admin_personal_resume.htm');
}
elseif($act == 'perform')
{
		check_token();
		$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ�������",1);
		if (!empty($_REQUEST['delete']))
		{
			check_permissions($_SESSION['admin_purview'],"resume_del");
			if ($n=del_resume($id))
			{
			adminmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
			}
			else
			{
			adminmsg("ɾ��ʧ�ܣ�",0);
			}
		}
				if (!empty($_POST['set_audit']))
		{
			check_permissions($_SESSION['admin_purview'],"resume_audit");
			$audit=$_POST['audit'];
			$pms_notice=intval($_POST['pms_notice']);
			$reason=trim($_POST['reason']);
			!edit_resume_audit($id,$audit,$reason,$pms_notice)?adminmsg("����ʧ�ܣ�",0):adminmsg("���óɹ���",2,$link);
		}
		
		if (!empty($_POST['set_talent']))
		{
		check_permissions($_SESSION['admin_purview'],"resume_talent");
		$talent=$_POST['talent'];
		!edit_resume_talent($id,$talent)?adminmsg("����ʧ�ܣ�",0):adminmsg("���óɹ���",2,$link);
		}
		if (!empty($_POST['set_photoaudit']))
		{
		check_permissions($_SESSION['admin_purview'],"resume_photo_audit");
		$photoaudit=$_POST['photoaudit'];
		!edit_resume_photoaudit($id,$photoaudit)?adminmsg("����ʧ�ܣ�",0):adminmsg("���óɹ���",2,$link);
		}
		elseif (!empty($_GET['refresh']))
		{
			if($n=refresh_resume($id))
			{
			adminmsg("ˢ�³ɹ�����Ӧ���� {$n}",2);
			}
			else
			{
			adminmsg("ˢ��ʧ�ܣ�",0);
			}
		}	
}
elseif($act == 'members_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"per_user_show");
		require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE  m.utype=2 ";
	$oederbysql=" order BY m.uid DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.=" AND m.username like '{$key}%'";
		elseif ($key_type===2)$wheresql.=" AND m.uid='".intval($key)."'";
		elseif ($key_type===3)$wheresql.=" AND m.email like '{$key}%'";
		elseif ($key_type===4)$wheresql.=" AND m.mobile like '{$key}%'";
		$oederbysql="";
	}
	else
	{	
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql.=" AND m.reg_time> ".$settr;
		}
		if (!empty($_GET['verification']))
		{
			if ($_GET['verification']=="1")
			{
			$wheresql.=" AND m.email_audit = 1";
			}
			elseif ($_GET['verification']=="2")
			{
			$wheresql.=" AND m.email_audit = 0";
			}
			elseif ($_GET['verification']=="3")
			{
			$wheresql.=" AND m.mobile_audit = 1";
			}
			elseif ($_GET['verification']=="4")
			{
			$wheresql.=" AND m.mobile_audit = 0";
			}
		}
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members')." as m ".$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$member = get_member_list($offset,$perpage,$wheresql.$oederbysql);
	$smarty->assign('pageheader',"���˻�Ա");
	$smarty->assign('member',$member);
	$smarty->assign('page',$page->show(3));
	$smarty->display('personal/admin_personal_user_list.htm');
}
elseif($act == 'delete_user')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"per_user_del");
	$tuid =!empty($_POST['tuid'])?$_POST['tuid']:adminmsg("��û��ѡ���Ա��",1);
	if ($_POST['delete'])
	{
		if ($_POST['delete_user']=='yes' && !delete_member($tuid))
		{
			adminmsg("ɾ����Աʧ�ܣ�",0);
		}
		if ($_POST['delete_resume']=='yes' && !del_resume_for_uid($tuid))
		{
			adminmsg("ɾ������ʧ�ܣ�",0);
		}
		adminmsg("ɾ���ɹ���",2);
	}
}
elseif($act == 'user_edit')
{	
	get_token();
	check_permissions($_SESSION['admin_purview'],"per_user_edit");
	$smarty->assign('pageheader',"���˻�Ա");
	$smarty->assign('user',get_member_one($_GET['tuid']));
	$smarty->assign('resume',get_resume_uid($_GET['tuid']));
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->display('personal/admin_personal_user_edit.htm');
}
elseif($act == 'set_account_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"per_user_edit");
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	$setsqlarr['username']=trim($_POST['username']);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['email_audit']=intval($_POST['email_audit']);
	$setsqlarr['mobile']=trim($_POST['mobile']);
	$setsqlarr['mobile_audit']=intval($_POST['mobile_audit']);
	if ($_POST['qq_openid']=="1")
	{
	$setsqlarr['qq_openid']='';
	}
	$thisuid=intval($_POST['thisuid']);	
	if (strlen($setsqlarr['username'])<3) adminmsg('�û�������Ϊ3λ���ϣ�',1);
	$getusername=get_user_inusername($setsqlarr['username']);
	if (!empty($getusername)  && $getusername['uid']<>$thisuid)
	{
	adminmsg("�û��� {$setsqlarr['username']}  �Ѿ����ڣ�",1);
	}
	if (empty($setsqlarr['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$setsqlarr['email']))
	{
	adminmsg('���������ʽ����',1);
	}
	$getemail=get_user_inemail($setsqlarr['email']);
	if (!empty($getemail)  && $getemail['uid']<>$thisuid)
	{
	adminmsg("Email  {$setsqlarr['email']}  �Ѿ����ڣ�",1);
	}
	if (!empty($setsqlarr['mobile']) && !preg_match("/^(13|15|18)\d{9}$/",$setsqlarr['mobile']))
	{
	adminmsg('�ֻ��������',1);
	}
	$getmobile=get_user_inmobile($setsqlarr['mobile']);
	if (!empty($setsqlarr['mobile']) && !empty($getmobile)  && $getmobile['uid']<>$thisuid)
	{
	adminmsg("�ֻ��� {$setsqlarr['mobile']}  �Ѿ����ڣ�",1);
	}
	if (updatetable(table('members'),$setsqlarr," uid=".$thisuid.""))
	{
		$u['email']=$setsqlarr['email'];
		updatetable(table('resume'),$u," uid={$thisuid}");
		updatetable(table('resume_tmp'),$u," uid={$thisuid}");
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�޸ĳɹ���',2,$link);
	}
	else
	{
	adminmsg('�޸�ʧ�ܣ�',1);
	}
}
elseif($act == 'userpass_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"per_user_edit");
	if (strlen(trim($_POST['password']))<6) adminmsg('���������Ϊ6λ���ϣ�',1);
	$user_info=get_member_one($_POST['memberuid']);
	$pwd_hash=$user_info['pwd_hash'];
	$md5password=md5(md5(trim($_POST['password'])).$pwd_hash.$QS_pwdhash);	
		if ($db->query( "UPDATE ".table('members')." SET password = '{$md5password}'  WHERE uid='{$user_info['uid']}' LIMIT 1"))
		{
 		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $_POST['url'];
		$member=get_member_one($user_info['uid']);
		write_memberslog($member['uid'],1,1004,$member['username'],"����Ա�ں�̨�޸ĵ�¼����");
		adminmsg('�����ɹ���',2,$link);
		}
		else
		{
		adminmsg('����ʧ�ܣ�',1);
		}
}
elseif($act == 'members_add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"per_user_add");
	$smarty->assign('pageheader',"���˻�Ա");
	$smarty->display('personal/admin_personal_user_add.htm');
}
elseif($act == 'members_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"per_user_add");	
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	if (strlen(trim($_POST['username']))<3) adminmsg('�û�������Ϊ3λ���ϣ�',1);
	if (strlen(trim($_POST['password']))<6) adminmsg('�������Ϊ6λ���ϣ�',1);
	$sql['username'] = !empty($_POST['username']) ? trim($_POST['username']):adminmsg('����д�û�����',1);
	$sql['password'] = !empty($_POST['password']) ? trim($_POST['password']):adminmsg('����д���룡',1);	
	if ($sql['password']<>trim($_POST['password1']))
	{
	adminmsg('������������벻��ͬ��',1);
	}
	$sql['utype'] = !empty($_POST['member_type']) ? intval($_POST['member_type']):adminmsg('��û��ѡ��ע�����ͣ�',1);
	if (empty($_POST['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$_POST['email']))
	{
	adminmsg('���������ʽ����',1);
	}
	$sql['email']= trim($_POST['email']);
	if (get_user_inusername($sql['username']))
	{
	adminmsg('���û����Ѿ���ʹ�ã�',1);
	}
	if (get_user_inemail($sql['email']))
	{
	adminmsg('�� Email �Ѿ���ע�ᣡ',1);
	}
 	$sql['pwd_hash'] = randstr();
	$sql['password'] = md5(md5($sql['password']).$sql['pwd_hash'].$QS_pwdhash);
	$sql['reg_time']=time();
	$sql['reg_ip']=$online_ip;
	$insert_id=inserttable(table('members'),$sql,true);
	if ($insert_id)
	{
		write_memberslog($insert_id,1,1000,$sql['username'],"����Ա�ں�̨������Ա");
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = "?act=members_list";
		adminmsg('��ӳɹ���',2,$link);
	}	
}
elseif($act == 'resume_show')
{
	check_permissions($_SESSION['admin_purview'],"resume_show");
	$id =!empty($_REQUEST['id'])?intval($_REQUEST['id']):adminmsg("��û��ѡ�������",1);
	$uid =intval($_REQUEST['uid']);
	$smarty->assign('pageheader',"�鿴����");
	$resume=get_resume_basic($uid,$id);
	if (empty($resume))
	{
	$link[0]['text'] = "���ؼ����б�";
	$link[0]['href'] = '?act=list';
	adminmsg('���������ڻ��Ѿ���ɾ����',1,$link);
	}
	$smarty->assign('random',mt_rand());
	$smarty->assign('time',time());
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('resume',$resume);
	$smarty->assign('resume_education',get_resume_education($uid,$id));
	$smarty->assign('resume_work',get_resume_work($uid,$id));
	$smarty->assign('resume_training',get_resume_training($uid,$id));
	$smarty->assign('resumeaudit',get_resumeaudit_one($id));
	$smarty->display('personal/admin_personal_resume_show.htm');
}
elseif($act == 'del_auditreason')
{	
	check_permissions($_SESSION['admin_purview'],"resume_audit");
	$id =!empty($_REQUEST['a_id'])?$_REQUEST['a_id']:adminmsg("��û��ѡ����־��",1);
$n=reasonaudit_del($id);
	if ($n>0)
	{
	adminmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
 elseif($act == 'management')
{	
	$id=intval($_GET['id']);
	$u=get_user($id);
	if (!empty($u))
	{
		unset($_SESSION['uid']);
		unset($_SESSION['username']);
		unset($_SESSION['utype']);
		unset($_SESSION['uqqid']);
		setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		unset($_SESSION['activate_username']);
		unset($_SESSION['activate_email']);
		
		$_SESSION['uid']=$u['uid'];
		$_SESSION['username']=$u['username'];
		$_SESSION['utype']=$u['utype'];
		$_SESSION['uqqid']="1";
		setcookie('QS[uid]',$u['uid'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[username]',$u['username'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[password]',$u['password'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[utype]',$u['utype'], 0,$QS_cookiepath,$QS_cookiedomain);
		header("Location:".get_member_url($u['utype']));
	}	
} 
?>