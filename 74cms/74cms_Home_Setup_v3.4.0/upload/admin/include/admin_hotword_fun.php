<?php
 /*
 * 74cms �������� �ؼ���
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
function get_hotword($offset, $perpage, $wheresql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;
	return $db->getall("SELECT * FROM ".table('hotword').$wheresql.$limit);
}
function get_hotword_one($id)
{
	global $db;
	$sql = "select * from ".table('hotword')." where w_id=".intval($id)." LIMIT 1";
	return $db->getone($sql);
}
function get_hotword_obtainword($word)
{
	global $db;
	$sql = "select * from ".table('hotword')." where w_word='".trim($word)."'  LIMIT 1";
	return $db->getone($sql);
}
function del_hottype($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('hotword')." WHERE w_id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
?>