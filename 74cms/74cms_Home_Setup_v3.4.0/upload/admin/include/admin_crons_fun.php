<?php
 /*
 * 74cms �ƻ�����
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
function get_crons($offset, $perpage, $get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT * FROM ".table('crons')." ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
		switch ($row['weekday'])
		{
		case "-1":
		  $row['weekdaycn']="";
		  break;
		case 0:
		  $row['weekdaycn']="ÿ����";
		  break;
		case 1:
		   $row['weekdaycn']="ÿ��һ";
		  break;
		case 2:
		   $row['weekdaycn']="ÿ�ܶ�";
		  break;
		case 3:
		   $row['weekdaycn']="ÿ����";
		  break;
		case 4:
		   $row['weekdaycn']="ÿ����";
		  break;
		case 5:
		   $row['weekdaycn']="ÿ����";
		  break;
		case 6:
		   $row['weekdaycn']="ÿ����";
		  break;
		default:
		 $row['weekdaycn']="";
		}
	
	$row_arr[] = $row;
	}
	return $row_arr;	
}
function del_crons($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('crons')." WHERE  cronid  IN (".$sqlin.") AND admin_set<>1 ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_crons_one($id)
{
	global $db;
	$sql = "select * from ".table('crons')." where cronid=".intval($id)."";
	return $db->getone($sql);
}
?>