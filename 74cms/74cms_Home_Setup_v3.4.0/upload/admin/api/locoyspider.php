<?php
 /*
 * 74cms ��ͷ�ɼ�
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once('../../data/config.php');
require_once('../include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_locoyspider_fun.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'set';
$locoyspider=get_cache('locoyspider');
if ($locoyspider['open']<>"1")
{
exit("������վ��̨������ͷ�ɼ�");
}
elseif($act=="news")
{
	require_once(ADMIN_ROOT_PATH.'include/admin_article_fun.php');
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('���±��ⲻ��Ϊ�գ�',1);
	if (ck_article_title($setsqlarr['title']))
	{
	exit("���ʧ�ܣ����ű������ظ�");
	}
	$setsqlarr['type_id']=trim($_POST['type_id'])?trim($_POST['type_id']):exit('�����������಻��Ϊ�գ�');
	$setsqlarr['parentid']=get_article_parentid($setsqlarr['type_id']);
	$setsqlarr['content']=trim($_POST['content'])?trim($_POST['content']):exit('�������ݲ���Ϊ�գ�');
	$setsqlarr['tit_color']=intval($_POST['tit_color']);
	$setsqlarr['tit_b']=intval($_POST['tit_b']);
	$setsqlarr['author']=trim($_POST['author']);
	$setsqlarr['source']=trim($_POST['source']);
		//�ж��Ƿ����ã���������ϵͳĬ��
		if ($_POST['focos']=="")
		{
		$setsqlarr['focos']=$locoyspider['article_focos'];
		}
		else
		{
		$setsqlarr['focos']=intval($_POST['focos']);
		}
		//�ж��Ƿ����ã���������ϵͳĬ��
		if ($_POST['is_display']=="")
		{
		$setsqlarr['is_display']=$locoyspider['article_display'];
		}
		else
		{
		$setsqlarr['is_display']=intval($_POST['is_display']);
		}
	$setsqlarr['is_url']=trim($_POST['is_url'])==""? "http://":trim($_POST['is_url']);
	$setsqlarr['seo_keywords']=trim($_POST['seo_keywords']);
	$setsqlarr['seo_description']=trim($_POST['seo_description']);
	$setsqlarr['article_order']=trim($_POST['article_order']);
	$setsqlarr['click']=intval($_POST['click']);
	$setsqlarr['Small_img']=trim($_POST['Small_img']);
	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['robot']=1;
		if (inserttable(table('article'),$setsqlarr))
		{
		exit("��ӳɹ�");
		}
		else
		{
		exit("���ʧ��");
		}
		exit();
}
elseif($act=="jobs")
{
$companyname=isset($_POST['companyname'])?trim($_POST['companyname']):exit('��˾���Ʋ���Ϊ�գ�');
$companyinfo=get_companyinfo($companyname);
	if ($companyinfo)
	{
		locoyspider_addjobs($companyinfo);
	}
	else
	{
		if (locoyspider_addcompany($companyname))
		{
		$companyinfo=get_companyinfo($companyname);
		locoyspider_addjobs($companyinfo);
		}
		else
		{
		exit("���ʧ��");
		}
	} 
}
?>