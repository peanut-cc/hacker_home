<?php
 /*
 * 74cms �������� ��վ���� ���ݵ��ú���
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
//��ȡ��������
function get_nav($typeid=NULL)
{
	global $db;
	if ($typeid) $wheresql=" WHERE typeid='".intval($typeid)."' ";
	$result = $db->query("SELECT * FROM ".table('navigation').$wheresql." order BY display desc,navigationorder desc,id asc");
	while($row = $db->fetch_array($result))
	{
	$category=get_nav_cat_one($row['alias']);
	$row['categoryname']=$category['categoryname'];
	$row_arr[] = $row;
	}
	return $row_arr;
}
//��ȡ������������
function get_nav_one($id)
{
	global $db;
	$id=intval($id);
	$sql = "select * from ".table('navigation')." where id=".$id;
	$category_one=$db->getone($sql);
	if ($category_one['systemclass']=="1")
	{
	$category_one['url']=url_rewrite($category_one['module'],$category_one['module_page']);
	}
	$category_one['url_str']=cut_str($category_one['url'],12,0);
	return $category_one;
}
//ɾ��������Ŀ
function del_navigation($del_id)
{
	global $db;
	if (!is_array($del_id))$del_id=array($del_id);
	$sqlin=implode(",",$del_id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('navigation')." WHERE id IN (".$sqlin.")")) return false;
	return true;
	}
	return false;
}
//��ȡ���е�������
function get_nav_cat()
{
	global $db;
	$sql = "select * from ".table('navigation_category');
	$list=$db->getall($sql);
	return $list;
}
//��ȡ������������
function get_nav_cat_one($alias)
{
	global $db;
	$sql = "select * from ".table('navigation_category')." where alias='".trim($alias)."'";
	return $db->getone($sql);
}
function del_nav_cat($id)
{
	global $db;
	if (!$db->query("Delete from ".table('navigation_category')." WHERE id=".intval($id)." AND admin_set<>1")) return false;
	return true;
}
?>