<?php
 /*
 * 74cms Ͷ���뽨��
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
require_once(ADMIN_ROOT_PATH.'include/admin_feedback_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'suggest_list';
if($act == 'suggest_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"suggest_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	!empty($_GET['infotype'])? $wheresqlarr['infotype']=intval($_GET['infotype']):'';
	!empty($_GET['usertype'])? $wheresqlarr['usertype']=intval($_GET['usertype']):'';
	!empty($_GET['replyinfo'])? $wheresqlarr['replyinfo']=intval($_GET['replyinfo']):'';
		if (is_array($wheresqlarr))
		{
		$where_set=' WHERE';
			foreach ($wheresqlarr as $key => $value)
			{
			$wheresql .=$where_set. $comma.'`'.$key.'`'.'=\''.$value.'\'';
			$comma = ' AND ';
			$where_set='';
			}
		}
	$key=trim($_POST['key']);
	!empty($key)?($wheresql=" WHERE username like '%{$key}%'"):'';
	$total_sql="SELECT COUNT(*) AS num FROM ".table('feedback').$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_feedback_list($offset,$perpage,$wheresql);
	$smarty->assign('pageheader',"����ͽ���");
	$smarty->assign('infotype',$_GET['infotype']);//��Ƭ�˲�
	$smarty->assign('usertype',$_GET['usertype']);//��Ƭ���״̬
	$smarty->assign('replyinfo',$_GET['replyinfo']);//�Ƿ��ѻظ�
	$smarty->assign('key',$key);
	$smarty->assign('perpage',$perpage);
	$smarty->assign('list',$list);//�б�
	if ($total_val>$perpage)
	{
	$smarty->assign('page',$page->show(3));//��ҳ��
	}
	$smarty->display('feedback/admin_feedback_suggest_list.htm');
}
elseif($act == 'del_feedback')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"suggest_del");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ŀ��",1);
	if ($num=del_feedback($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'reply_feedback')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"suggest_reply");
	$id =!empty($_GET['id'])?intval($_GET['id']):adminmsg("��û��ѡ����Ŀ��",1);
	$smarty->assign('pageheader',"����ͽ���");
	$smarty->assign('feedback',get_feedback_one($id));
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->display('feedback/admin_feedback_suggest_reply.htm');
}
elseif($act == 'reply_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"suggest_reply");
	$setsqlarr['feedbacktime']=$timestamp;
	$setsqlarr['reply']=trim($_POST['reply'])?trim($_POST['reply']):adminmsg('��û����д�ظ����ݣ�',1);
	$setsqlarr['replyinfo']=2;
	$wheresql=" id='".intval($_POST['feedbackid'])."' ";
	if (!updatetable(table('feedback'), $setsqlarr,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg("�����ɹ���",2,$link);
}
elseif($act == 'report_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"report_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY r.id DESC ";
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE r.addtime> ".$settr:$wheresql." AND r.addtime> ".$settr;
	}
	$joinsql=" LEFT JOIN ".table('members')." AS m ON r.uid=m.uid  ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('report')." AS r ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$list = get_report_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"�ٱ���Ϣ");
	$smarty->assign('list',$list);
	$smarty->assign('page',$page->show(3));
	$smarty->display('feedback/admin_report_list.htm');
}
elseif($act == 'del_report')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"report_del");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ŀ��",1);
	$id=$_REQUEST['id'];
	if ($num=del_report($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
?>