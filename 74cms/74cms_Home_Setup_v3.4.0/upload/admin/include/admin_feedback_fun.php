<?php
 /*
 * 74cms Ͷ���뽨����غ���
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
function get_feedback_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$sql = "select * from ".table('feedback')." ".$get_sql.$limit;
	$val=$db->getall($sql);
	return $val;
}
function get_feedback_one($id)
{
	global $db;
	$sql = "select * from ".table('feedback')." where id=".intval($id);
	$val=$db->getone($sql);
	return $val;
}
function del_feedback($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('feedback')." WHERE id IN (".$sqlin.")")) return false;
	$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_report_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT r.*,m.username FROM ".table('report')." AS r ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['jobs_id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function del_report($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$del_id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('report')." WHERE id IN (".$sqlin.")")) return false;
	$return=$return+$db->affected_rows();
	}
	return $return;
}
?>