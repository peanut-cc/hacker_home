<?php
 /*
 * 74cms 
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
if(!defined('IN_QISHI'))
{
 	die('Access Denied!');
}
function get_syslog_list($offset,$perpage,$sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('syslog')." ".$sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['l_page']=urldecode($row['l_page']);
	$row_arr[] = $row;
	}
	return $row_arr;
}
function del_syslog($id)
{
	global $db;
	$delnum=0;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$db->query("Delete from ".table('syslog')." WHERE l_id IN (".$sqlin.")");
		$delnum=$db->affected_rows();
	}
	return $delnum;
}
function pidel_syslog($l_type,$starttime,$endtime)
{
	global $db;
	$delnum=0;
	$starttime=intval($starttime);
	$endtime=intval($endtime);
	if (!is_array($l_type)) $l_type=array($l_type);
	$sqlin=implode(",",$l_type);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin) && $starttime && $endtime)
	{
		$db->query("Delete from ".table('syslog')." WHERE l_time>{$starttime} and  l_time<{$endtime} and l_type IN (".$sqlin.")");
		$delnum=$db->affected_rows();
	}
	return $delnum;
}
?>