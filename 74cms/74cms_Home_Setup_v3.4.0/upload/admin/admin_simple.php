<?php
 /*
 * 74cms ΢��Ƹ
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
require_once(ADMIN_ROOT_PATH.'include/admin_simple_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('act',$act);
$smarty->assign('pageheader',"΢��Ƹ");
if($act == 'list')
{
	check_permissions($_SESSION['admin_purview'],"simple_list");	
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$orderbysql=" order BY `refreshtime` DESC";
	if ($key && $key_type>0)
	{
		
		if     ($key_type==1)$wheresql=" WHERE jobname like '%{$key}%'";
		if     ($key_type==2)$wheresql=" WHERE comname like '%{$key}%'";
		if     ($key_type==3)$wheresql=" WHERE tel ='{$key}'";
		if     ($key_type==4)$wheresql=" WHERE contact like '%{$key}%'";
		if     ($key_type==5)$wheresql=" WHERE email like '%{$key}%'";
		if     ($key_type==6)$wheresql=" WHERE qq ='{$key}'";
		if     ($key_type==7)$wheresql=" WHERE address like '%{$key}%'";
		$orderbysql="";
	}
	else
	{
		if (!empty($_GET['audit']))
		{
		$wheresql=" WHERE audit=".intval($_GET['audit']);
		}
		if (!empty($_GET['addtime']))
		{
			$settr=strtotime("-".intval($_GET['addtime'])." day");
			$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
		}
		if ($_GET['deadline']<>'')
		{
			$deadline=intval($_GET['deadline']);
			$time=time();			
			if ($deadline==0)
			{			
			$wheresql=empty($wheresql)?" WHERE deadline< {$time} AND deadline<>0 ":"{$wheresql} AND deadline< {$time} AND deadline<>0 ";
			}
			else
			{
			$settr=strtotime("+{$deadline} day");
			$wheresql=empty($wheresql)?" WHERE deadline<{$settr} AND deadline>{$time} ":"{$wheresql} AND  deadline<{$settr} AND deadline>{$time}";
			}			
		}
		if (!empty($_GET['refreshtime']))
		{
			$settr=strtotime("-".intval($_GET['refreshtime'])." day");
			$wheresql=empty($wheresql)?" WHERE refreshtime> ".$settr:$wheresql." AND refreshtime> ".$settr;
		}
	}
		if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_simple']=="1")
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND ";
			$wheresql.=" (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
		}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('simple').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_simple_list($offset,$perpage,$wheresql.$orderbysql);
	$smarty->assign('key',$key);
	$smarty->assign('total',$total_val);
	$smarty->assign('list',$list);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel','list');
	$smarty->display('simple/admin_simple.htm');
}
elseif($act == 'simple_del')
{
	check_permissions($_SESSION['admin_purview'],"simple_del");
	check_token();
	$id=$_REQUEST['id'];
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($num=simple_del($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'simple_refresh')
{
	check_permissions($_SESSION['admin_purview'],"simple_refresh");
	check_token();
	$id=$_REQUEST['id'];
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($num=simple_refresh($id))
	{
	adminmsg("ˢ�³ɹ�����ˢ�� {$num}�� ",2);
	}
	else
	{
	adminmsg("ˢ�³ɹ���",0);
	}
}
elseif($act == 'simple_audit')
{
	check_permissions($_SESSION['admin_purview'],"simple_audit");
	check_token();
	$id=$_REQUEST['id'];
	$audit=intval($_POST['audit']);
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($num=simple_audit($id,$audit))
	{
	adminmsg("���óɹ�����Ӱ�� {$num}�� ",2);
	}
	else
	{
	adminmsg("���óɹ���",0);
	}
}
elseif($act == 'simple_add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"simple_add");
	$smarty->assign('navlabel','add');
 	$smarty->display('simple/admin_simple_add.htm');
}
elseif($act == 'simple_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"simple_add");
	$setsqlarr['audit']=1;
	$setsqlarr['jobname']=trim($_POST['jobname'])?trim($_POST['jobname']):adminmsg('��û����дְλ���ƣ�',1);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['comname']=trim($_POST['comname'])?trim($_POST['comname']):adminmsg('��û����д��λ���ƣ�',1);
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):adminmsg('��û����д��ϵ�ˣ�',1);
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):adminmsg('��û����д��ϵ�绰��',1);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['qq']=trim($_POST['qq']);
	$setsqlarr['address']=trim($_POST['address']);
	$setsqlarr['detailed']=trim($_POST['detailed']);
	$setsqlarr['addtime']=time();
	$setsqlarr['refreshtime']=time();
	$setsqlarr['deadline']=0;
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$validity=intval($_POST['validity']);
	if ($validity>0)
	{
	$setsqlarr['deadline']=strtotime("{$validity} day");
	}
	$setsqlarr['pwd']=trim($_POST['pwd'])?trim($_POST['pwd']):adminmsg('��û����д�������룡',1);
	$setsqlarr['pwd_hash']=substr(md5(uniqid().mt_rand()),mt_rand(0,6),6);
	$setsqlarr['pwd']=md5(md5($setsqlarr['pwd']).$setsqlarr['pwd_hash'].$QS_pwdhash);
	$setsqlarr['addip']=$online_ip;
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['jobname'].$setsqlarr['comname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['jobname']} {$setsqlarr['comname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if(inserttable(table('simple'),$setsqlarr))
	{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = '?act=list';
		$link[1]['text'] = "�������";
		$link[1]['href'] = "?act=simple_add";
		adminmsg("��ӳɹ���",2,$link);
	}
	else
	{
		adminmsg("���ʧ�ܣ�",0);
	}	
}
elseif($act == 'simple_edit')
{
	get_token();
	$id=intval($_REQUEST['id']);
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	check_permissions($_SESSION['admin_purview'],"simple_edit");
	$sql = "select * from ".table('simple')." where id = '{$id}' LIMIT 1";
	$show=$db->getone($sql);
	$smarty->assign('show',$show);
 	$smarty->display('simple/admin_simple_edit.htm');
}
elseif($act == 'simple_edit_save')
{
	$id=intval($_POST['id']);
	if (empty($id))
	{
	adminmsg("��û��ѡ����Ŀ��",1);
	}
	if ($_POST['pwd'])
	{
		$info=$db->getone("select * from ".table('simple')." where id = '{$id}' LIMIT 1");
		$setsqlarr['pwd']=md5(md5($_POST['pwd']).$info['pwd_hash'].$QS_pwdhash);
	}
	$setsqlarr['jobname']=trim($_POST['jobname'])?trim($_POST['jobname']):adminmsg('��û����дְλ���ƣ�',1);
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['comname']=trim($_POST['comname'])?trim($_POST['comname']):adminmsg('��û����д��λ���ƣ�',1);
	$setsqlarr['contact']=trim($_POST['contact'])?trim($_POST['contact']):adminmsg('��û����д��ϵ�ˣ�',1);
	$setsqlarr['tel']=trim($_POST['tel'])?trim($_POST['tel']):adminmsg('��û����д��ϵ�绰��',1);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['qq']=trim($_POST['qq']);
	$setsqlarr['address']=trim($_POST['address']);
	$setsqlarr['detailed']=trim($_POST['detailed']);
	$setsqlarr['refreshtime']=time();
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$days=intval($_POST['days']);
	if ($days>0)
	{
	$time=$_POST['olddeadline']>time()?$_POST['olddeadline']:time();
	$setsqlarr['deadline']=strtotime("{$days} day",$time);
	}
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']=$setsqlarr['jobname'].$setsqlarr['comname'].$setsqlarr['address'].$setsqlarr['detailed'];
	$setsqlarr['key']="{$setsqlarr['jobname']} {$setsqlarr['comname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	if(updatetable(table('simple'),$setsqlarr," id='{$id}' "))
	{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = '?act=list';
		adminmsg("�޸ĳɹ���",2,$link);
	}
	else
	{
	adminmsg("�޸�ʧ�ܣ�",0);
	}
}
?>