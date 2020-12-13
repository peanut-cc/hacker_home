<?php
 /*
 * 74cms 投诉与建议相关函数
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
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