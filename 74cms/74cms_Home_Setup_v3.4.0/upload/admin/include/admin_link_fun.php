<?php
 /*
 * 74cms �������� �������� ���ݵ��ú���
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
function get_links($offset, $perpage, $get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query("SELECT l.*,c.categoryname FROM ".table('link')." AS l ".$get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row_arr[] = $row;
	}
	return $row_arr;	
}
function get_links_one($id)
{
	global $db;
	$sql = "select * from ".table('link')." where link_id=".intval($id)."";
	$link=$db->getone($sql);
	return $link;
}
function del_link($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('link')." WHERE link_id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_link_category()
{
	global $db;
	$sql = "select * from ".table('link_category')."";
	$info=$db->getall($sql);
	return $info;
}
function get_link_category_name($val)
{
	global $db;
	$sql = "select * from ".table('link_category')." where c_alias='".$val."'";
	$category=$db->getone($sql);
	return $category;
}
function del_category($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('link_category')." WHERE id IN (".$sqlin.")  AND c_sys<>1")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
?>