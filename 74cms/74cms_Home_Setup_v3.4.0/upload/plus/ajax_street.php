<?php
 /*
 * 74cms ajax ��·����
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
$listtype=trim($_GET['listtype']);
if($act == 'alphabet')
{
	$alphabet=trim($_GET['x']);	
	if (!empty($alphabet))
	{
	$result = $db->query("select * from ".table('category')." where c_alias='QS_street' AND c_index='{$alphabet}' ");
	while($row = $db->fetch_array($result))
	{
		if ($listtype=="li")
		{
		$htm.="<li  title=\"{$row['c_name']}\" id=\"{$row['c_id']}\">{$row['c_name']}</li>";
		}
		else
		{
		$_GET['streetid']=$row['c_id'];
		$url=url_rewrite('QS_street',$_GET);
		$htm.="<li><a href=\"{$url}\" title=\"{$row['c_note']}\" class=\"vtip\">{$row['c_name']}</a><span>{$row['stat_jobs']}</span></li>";
		
		}
	}
	if (empty($htm))
	{
	$htm="<span class=\"noinfo\">û���ҵ�����ĸΪ��<span>{$alphabet}</span>  �ĵ�·��</span>";
	}
	exit($htm);
	}
}
elseif($act == 'key')
{
	$key=trim($_GET['key']);
	if (!empty($key))
	{
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0) $key=utf8_to_gbk($key);
	$result = $db->query("select * from ".table('category')." where c_alias='QS_street' AND c_name LIKE '%{$key}%' ");
	while($row = $db->fetch_array($result))
	{
		if ($listtype=="li")
		{
		$htm.="<li  title=\"{$row['c_name']}\" id=\"{$row['c_id']}\">{$row['c_name']}</li>";
		}
		else
		{
		$_GET['streetid']=$row['c_id'];
		$url=url_rewrite('QS_street',$_GET);
		$htm.="<li><a href=\"{$url}\" title=\"{$row['c_note']}\" class=\"vtip\">{$row['c_name']}</a><span>{$row['stat_jobs']}</span></li>";
		};
	}
	if (empty($htm))
	{
	$htm="<span class=\"noinfo\">û���ҵ��ؼ��֣� <span>{$key}</span> ��ص�·��</span>";
	}
	exit($htm);
	}
}
?>