<?php
 /*
 * 74cms �������� ����������
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
 //��ȡ����б�
function get_ad_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$info = $db->getall("SELECT a.*,c.categoryname FROM ".table('ad')." AS a ".$get_sql." order BY a.show_order DESC,a.id DESC ".$limit);
	return $info;
}
//��ȡ���(����)
function get_ad_one($val)
{
	global $db;
	$sql = "select * from ".table('ad')." where id=".intval($val). " LIMIT 1";
	$arr=$db->getone($sql);
	$arr['starttime']=$arr['starttime']=="0"?'':convert_datefm($arr['starttime'],1);
	$arr['deadline']=$arr['deadline']=="0"?'':convert_datefm($arr['deadline'],1);
	return $arr;
}

//��ȡ���λ
function get_ad_category($type=NULL)
{
	global $db;
	if ($type) $wheresql=" where  type_id=".intval($type).""; 
	$sql = "select * from ".table('ad_category').$wheresql." order BY id asc";
	$info = $db->getall($sql);
	return $info;
}
//��ȡ���λ(����)
function get_ad_category_one($id)
{
	global $db;
	$sql = "select * from ".table('ad_category')." where id=".intval($id);
	$category_one=$db->getone($sql);
	return $category_one;
}
function del_ad($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('ad')." WHERE id IN (".$sqlin.") ")) return false;
	$return=$return+$db->affected_rows();
	}
	return $return;
}
function del_ad_category($id)
{
global $db;
if (!$db->query("Delete from ".table('ad_category')." WHERE id  = ".intval($id)." AND admin_set<>'1'")) return false; 
return true;
}
function ck_category_alias($alias,$noid=NULL){
global $db;
	if ($noid)
	{
	$wheresql=" AND id<>'".intval($noid)."'";
	}
$sql = "select id from ".table('ad_category')." WHERE alias='".$alias."'".$wheresql;
$info=$db->getone($sql);
 if ($info)
 {
 return true;
 }
 else
 {
 return false;
 }
}
?>