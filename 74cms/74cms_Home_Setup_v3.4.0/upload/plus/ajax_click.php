<?php
 /*
 * 74cms ajax �������
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
if($act == 'news_click')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$sql="update ".table('article')." set click=click+1 WHERE id='{$id}'  LIMIT 1";
		$db->query($sql);
		$sql = "select click from ".table('article')." where id='{$id}'  LIMIT 1";
		$val=$db->getone($sql);
		exit($val['click']);
	}
}
elseif($act == 'company_news_click')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$sql="update ".table('company_news')." set click=click+1 WHERE id='{$id}'  LIMIT 1";
		$db->query($sql);
		$sql = "select click from ".table('company_news')." where id='{$id}'  LIMIT 1";
		$val=$db->getone($sql);
		exit($val['click']);
	}
}
elseif($act == 'notice_click')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$sql="update ".table('notice')." set click=click+1 WHERE id='{$id}'  LIMIT 1";
		$db->query($sql);
		$sql = "select click from ".table('notice')." where id='{$id}'  LIMIT 1";
		$val=$db->getone($sql);
		exit($val['click']);
	}
}
elseif($act == 'jobs_click')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$db->query("update ".table('jobs')." set click=click+1 WHERE id='{$id}'  LIMIT 1");
		$db->query("update ".table('jobs_search_hot')." set click=click+1 WHERE id='{$id}'  LIMIT 1");
		$sql = "select click from ".table('jobs_search_hot')." where id='{$id}'  LIMIT 1";
		$val=$db->getone($sql);
		exit($val['click']);
	}
}
elseif($act == 'resume_click')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$db->query("update ".table('resume')." set click=click+1 WHERE id='{$id}'  LIMIT 1");
		$db->query("update ".table('resume_tmp')." set click=click+1 WHERE id='{$id}'  LIMIT 1");
		$tb1=$db->getone("select click from ".table('resume')." where id='{$id}'  LIMIT 1");
		$tb2=$db->getone("select click from ".table('resume_tmp')." where id='{$id}'  LIMIT 1");
		$val=!empty($tb1)?$tb1:$tb2;
		exit($val['click']);
	}
}
elseif($act == 'company_click')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$sql="update ".table('company_profile')." set click=click+1 WHERE id='{$id}'  LIMIT 1";
		$db->query($sql);
		$sql = "select click from ".table('company_profile')." where id='{$id}'  LIMIT 1";
		$val=$db->getone($sql);
		exit($val['click']);
	}
}
elseif($act == 'jobfair_click')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$sql="update ".table('jobfair')." set click=click+1 WHERE id='{$id}'  LIMIT 1";
		$db->query($sql);
		$sql = "select click from ".table('jobfair')." where id='{$id}'  LIMIT 1";
		$val=$db->getone($sql);
		exit($val['click']);
	}
}
elseif($act == 'simple_click')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$sql="update ".table('simple')." set click=click+1 WHERE id='{$id}'  LIMIT 1";
		$db->query($sql);
		$sql = "select click from ".table('simple')." where id='{$id}'  LIMIT 1";
		$val=$db->getone($sql);
		exit($val['click']);
	}
}
 ?>