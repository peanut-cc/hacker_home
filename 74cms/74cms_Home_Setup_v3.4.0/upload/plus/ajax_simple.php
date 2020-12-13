<?php
 /*
 * 74cms ajax ΢��Ƹ
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'add';
if ($_CFG['simple_open']=="0")
{
exit('����Ա�ѹر�΢��Ƹģ�飬���Ժ����ԡ�');
}
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if ($act=="add")
{	
	$smarty->cache =true;
	$smarty->cache_lifetime=60*60*72;
	$smarty->assign('ip',$online_ip);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_simple',$captcha['verify_simple']);
	$smarty->display('simple/simple-add.htm');
	exit();
}
elseif ($act=="addsave")
{	
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_simple']=='1' && empty($postcaptcha))
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_simple']=='1' &&  strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	$setsqlarr['audit']=intval($_CFG['simple_add_audit']);
	$setsqlarr['jobname']=trim($_POST['jobname'])?trim($_POST['jobname']):showmsg('��û����дְλ���ƣ�',1);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['comname']=trim($_POST['comname'])?trim($_POST['comname']):showmsg('��û����д��λ���ƣ�',1);
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):showmsg('��û����д��ϵ�绰��',1);
	if(preg_match("/^\d*$/",$setsqlarr['tel']))
	{
		if ($captcha['simple_tel_repeat']=='0')
		{
			$sql = "select id from ".table('simple')." where tel = '{$setsqlarr['tel']}' LIMIT 1";
			$info=$db->getone($sql);
			if (!empty($info))
			{
			showmsg('�绰�����Ѿ����ڣ�',1);
			}
		}
	}
	else
	{
	showmsg('�绰�����ʽ����',1);
	}
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['qq']=trim($_POST['qq']);
	$setsqlarr['address']=trim($_POST['address']);
	$setsqlarr['detailed']=trim($_POST['detailed']);
	$setsqlarr['addtime']=time();
	$setsqlarr['refreshtime']=time();
	$setsqlarr['deadline']=0;
	$validity=intval($_POST['validity']);
	if ($validity>0)
	{
	$setsqlarr['deadline']=strtotime("{$validity} day");
	}
	$setsqlarr['pwd']=trim($_POST['pwd'])?trim($_POST['pwd']):showmsg('��û����д�������룡',1);
	$setsqlarr['pwd_hash']=substr(md5(uniqid().mt_rand()),mt_rand(0,6),6);
	$setsqlarr['pwd']=md5(md5($setsqlarr['pwd']).$setsqlarr['pwd_hash'].$QS_pwdhash);
	$setsqlarr['addip']=$online_ip;
	$setsqlarr['subsite_id']=intval($_CFG['subsite_id']);
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['jobname'].$setsqlarr['comname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['jobname']} {$setsqlarr['comname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$link[0]['text'] = "����΢��Ƹ�б�";
	$link[0]['href'] =url_rewrite('QS_simplelist');
	if(inserttable(table('simple'),$setsqlarr))
	{
		if ($setsqlarr['audit']<>1)
		{
		$str="����ȴ�����Ա���";
		}
		showmsg("��ӳɹ�{$str}��",2,$link);
	}
	else
	{
	showmsg("���ʧ�ܣ�",0);
	}
}
elseif ($act=="delsimple")
{
	$smarty->cache =false;
	$smarty->assign('id',intval($_GET['id']));
	$smarty->display('simple/simple-del.htm');
	exit();
}
elseif ($act=="exe_delsimple")
{
		$pwd=$_POST['pwd'];
		$id=intval($_POST['id']);
		$sql = "select * from ".table('simple')." where id = '{$id}' LIMIT 1";
		$info=$db->getone($sql);
		$thispwd=md5(md5($pwd).$info['pwd_hash'].$QS_pwdhash);
		if ($thispwd==$info['pwd'])
		{
		$db->query("Delete from ".table('simple')." WHERE id = '{$id}'");
		$link[0]['text'] = "����΢��Ƹ�б�";
		$link[0]['href'] =url_rewrite('QS_simplelist');
		showmsg("ɾ���ɹ���",2,$link);
		}
}
elseif ($act=="refreshsimple")
{
	$smarty->cache =false;
	$smarty->assign('id',intval($_GET['id']));
	$smarty->display('simple/simple-refresh.htm');
	exit();
}
elseif ($act=="exe_refreshsimple")
{
		$pwd=$_POST['pwd'];
		$id=intval($_POST['id']);
		$sql = "select * from ".table('simple')." where id = '{$id}' LIMIT 1";
		$info=$db->getone($sql);
		$thispwd=md5(md5($pwd).$info['pwd_hash'].$QS_pwdhash);
		if ($thispwd==$info['pwd'])
		{
		$db->query("update ".table('simple')."  SET refreshtime='".time()."' WHERE id = '{$id}'");
		$link[0]['text'] = "����΢��Ƹ�б�";
		$link[0]['href'] =url_rewrite('QS_simplelist');
		showmsg("ˢ�³ɹ���",2,$link);
		}
}
elseif ($act=="editsimple")
{
	$id=intval($_GET['id']);
	$smarty->cache =false;
	$info=$db->getone("select * from ".table('simple')." where id = '{$id}' LIMIT 1");
	$smarty->assign('info',$info);
	$smarty->assign('id',$id);
	$captcha=get_cache('captcha');
	$smarty->assign('verify_simple',$captcha['verify_simple']);
	$smarty->display('simple/simple-edit.htm');
	exit();
}
elseif ($act=="editsave")
{	
	$captcha=get_cache('captcha');
	$postcaptcha = trim($_POST['postcaptcha']);
	if($captcha['verify_simple']=='1' && empty($postcaptcha))
	{
		showmsg("����д��֤��",1);
 	}
	if ($captcha['verify_simple']=='1' &&  strcasecmp($_SESSION['imageCaptcha_content'],$postcaptcha)!=0)
	{
		showmsg("��֤�����",1);
	}
	$id=intval($_POST['id']);
	$pwd=trim($_POST['pwd']);
	$info=$db->getone("select * from ".table('simple')." where id = '{$id}' LIMIT 1");
	$thispwd=md5(md5($pwd).$info['pwd_hash'].$QS_pwdhash);
	if ($thispwd!=$info['pwd'])
	{
		showmsg("�����������",1);
	}
	if ($_CFG['simple_edit_audit']!="-1")
	{
	$setsqlarr['audit']=intval($_CFG['simple_edit_audit']);
	}
	$setsqlarr['jobname']=trim($_POST['jobname'])?trim($_POST['jobname']):showmsg('��û����дְλ���ƣ�',1);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['comname']=trim($_POST['comname'])?trim($_POST['comname']):showmsg('��û����д��λ���ƣ�',1);
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):showmsg('��û����д��ϵ�ˣ�',1);
	if ($_CFG['simple_tel_edit']=="1")
	{
		$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):showmsg('��û����д��ϵ�绰��',1);
		if(preg_match("/^\d*$/",$setsqlarr['tel']))
		{
			if ($captcha['simple_tel_repeat']=='0')
			{
				$sql = "select id from ".table('simple')." where tel = '{$setsqlarr['tel']}' AND id<>'{$id}' LIMIT 1";
				$info=$db->getone($sql);
				if (!empty($info))
				{
				showmsg('�绰�����Ѿ����ڣ�',1);
				}
			}
		}	
	}
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['qq']=trim($_POST['qq']);
	$setsqlarr['address']=trim($_POST['address']);
	$setsqlarr['detailed']=trim($_POST['detailed']);
	$setsqlarr['refreshtime']=time();
	$days=intval($_POST['days']);
	if ($days>0)
	{
	$time=$info['deadline']>time()?$info['deadline']:time();
	$setsqlarr['deadline']=strtotime("{$days} day",$time);
	}
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['jobname'].$setsqlarr['comname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['jobname']} {$setsqlarr['comname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$link[0]['text'] = "����΢��Ƹ�б�";
	$link[0]['href'] =url_rewrite('QS_simplelist');
	if(updatetable(table('simple'),$setsqlarr," id='{$id}' "))
	{
		if ($_CFG['simple_edit_audit']>1)
		{
		$str="����ȴ�����Ա���";
		}
		showmsg("�޸ĳɹ�{$str}��",2,$link);
	}
	else
	{
	showmsg("�޸�ʧ�ܣ�",0);
	}
}
elseif($act =='check_tel')
{
	$tel=$_GET['tel'];
	$id=intval($_GET['id']);
	if(preg_match("/^\d*$/",$tel))
	{
		if ($id>0)
		{
		$wheresql=" AND id<>'{$id}'";
		}
		$sql = "select id from ".table('simple')." where tel = '{$tel}' {$wheresql} LIMIT 1";
		$info=$db->getone($sql);
		if (!empty($info))
		{
		exit('false');
		}
		else
		{
		exit('true');
		}		
	}
	else
	{
	exit('false');
	} 
}
elseif($act =='check_pwd')
{
	$pwd=$_GET['pwd'];
	$id=intval($_GET['id']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$pwd=utf8_to_gbk($pwd);
	}
		$sql = "select * from ".table('simple')." where id = '{$id}' LIMIT 1";
		$info=$db->getone($sql);
		$thispwd=md5(md5($pwd).$info['pwd_hash'].$QS_pwdhash);
		if ($thispwd==$info['pwd'])
		{		
		exit('true');
		}
		else
		{
		exit('false');
		}
}
?>