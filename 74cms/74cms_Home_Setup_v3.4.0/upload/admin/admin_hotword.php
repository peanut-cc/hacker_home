<?php
 /*
 * 74cms ���Źؼ���
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
require_once(ADMIN_ROOT_PATH.'include/admin_hotword_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('act',$act);
check_permissions($_SESSION['admin_purview'],"hotword");
$smarty->assign('pageheader',"���Źؼ���");
if($act == 'list')
{	
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY w_hot DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	if ($key)
	{
		$wheresql=" WHERE w_word like '%{$key}%'";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('hotword')." ".$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql),'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$hotword = get_hotword($offset, $perpage,$wheresql.$oederbysql);	
	$smarty->assign('hotword',$hotword);
	$smarty->assign('navlabel',"list");	
	$smarty->assign('page',$page->show(3));	
	$smarty->display('hotword/admin_hotword_list.htm');
}
elseif($act == 'add')
{
	get_token();
	$smarty->assign('navlabel',"add");	
	$smarty->display('hotword/admin_hotword_add.htm');
}
elseif($act == 'addsave')
{
	check_token();
	$setsqlarr['w_word']=trim($_POST['w_word'])?trim($_POST['w_word']):adminmsg('�ؼ��ʱ�����д��',1);
	$setsqlarr['w_hot']=intval($_POST['w_hot']);
	if (get_hotword_obtainword($setsqlarr['w_word']))
	{
	adminmsg("�ؼ����Ѿ����ڣ�",0);
	}
	$link[0]['text'] = "�������";
	$link[0]['href'] = '?act=add&w_type='.$setsqlarr['w_type'];
	$link[1]['text'] = "�����б�";
	$link[1]['href'] = '?';
	!inserttable(table('hotword'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
}
elseif($act == 'edit')
{
	get_token();
	$smarty->assign('hotword',get_hotword_one($_GET['id']));
	$smarty->display('hotword/admin_hotword_edit.htm');
}
elseif($act == 'editsave')
{
	check_token();
	$id = !empty($_POST['id']) ? intval($_POST['id']) : adminmsg('��������',1);
	$setsqlarr['w_word']=trim($_POST['w_word'])?trim($_POST['w_word']):adminmsg('�ؼ��ʱ�����д��',1);
	$setsqlarr['w_hot']=intval($_POST['w_hot']);
	$word=get_hotword_obtainword($setsqlarr['w_word']);
	if ($word['w_id'] && $word['w_id']<>$id)
	{
	adminmsg("�ؼ����Ѿ����ڣ�",0);
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?';
 	!updatetable(table('hotword'),$setsqlarr," w_id=".$id."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'hottype_del')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_hottype($id))
	{
	adminmsg("ɾ���ɹ�����ɾ�� {$num} ��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
?>