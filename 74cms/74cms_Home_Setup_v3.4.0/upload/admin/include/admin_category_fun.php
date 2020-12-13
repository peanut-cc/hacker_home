<?php
 /*
 * 74cms �������� ���÷��� ���ݵ��ú���
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
function get_category_group()
{
	global $db;
	$sql = "select * from ".table('category_group');
	return $db->getall($sql);
}
function get_category_group_one($alias)
{
	global $db;
	$sql = "select * from ".table('category_group')." WHERE g_alias='".$alias."'";
	return $db->getone($sql);
}
function del_group($alias)
{
	global $db;
	if(!is_array($alias)) $alias=array($alias);
	$return=0;
	foreach($alias as $a)
	{
			if (!$db->query("Delete from ".table('category_group')." WHERE g_alias ='".trim($a)."' AND g_sys<>1")) return false;
			$return=$return+$db->affected_rows();
			if (!$db->query("Delete from ".table('category')." WHERE c_alias ='".trim($a)."' ")) return false;
			$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_category($alias)
{
	global $db;
	$sql = "select * from ".table('category')." WHERE c_alias='".$alias."'  ORDER BY c_order DESC,c_id ASC";
	return $db->getall($sql);
}
function get_category_one($id)
{
	global $db;
	$sql = "select * from ".table('category')." WHERE c_id=".intval($id)." LIMIT 1";
	return $db->getone($sql);
}
function del_category($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('category')." WHERE c_id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
function get_category_district($pid='0')
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('category_district')." where parentid={$pid}  order BY category_order desc,id asc";
	return   $db->getall($sql);
}
function get_category_district_one($id)
{
	global $db;
	$sql = "select * from ".table('category_district')." WHERE id=".intval($id)." LIMIT 1";
	return $db->getone($sql);
}
function del_district($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('category_district')." WHERE id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("Delete from ".table('category_district')." WHERE parentid IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
//-----------ְλ
function get_category_jobs()
{
	global $db;
	$sql = "select * from ".table('category_jobs')." where parentid=0  order BY category_order desc,id asc";
	$result = $db->query($sql);
	while($row = $db->fetch_array($result))
	{		
		$sql = "select * from ".table('category_jobs')." where parentid=".$row['id']."  order BY category_order desc,id asc";
		$sub=$db->getall($sql);
		$row['sub']=$sub;
		$category[]=$row;
	}
	return $category;
}
function get_category_jobs_one($id)
{
	global $db;
	$sql = "select * from ".table('category_jobs')." WHERE id=".intval($id)." LIMIT 1";
	return $db->getone($sql);
}
function del_jobs_category($id)
{
	global $db;
	if(!is_array($id)) $id=array($id);
	$return=0;
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("Delete from ".table('category_jobs')." WHERE id IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("Delete from ".table('category_jobs')." WHERE parentid IN (".$sqlin.") ")) return false;
		$return=$return+$db->affected_rows();
	}
	return $return;
}
 
function getfirstchar($str)
{
        $fchar=ord($str{0});   
        if($fchar>=ord("A") and $fchar<=ord("z") )return strtoupper($str{0}); 
		$s=$str;
		if (strcasecmp(QISHI_DBCHARSET,"GBK")!=0)
		{
		$s=iconv(QISHI_DBCHARSET,"GBK//IGNORE",$str);
		}
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319 and $asc<=-20284)return "a ".$str;
        if($asc>=-20283 and $asc<=-19776)return "b ".$str;
        if($asc>=-19775 and $asc<=-19219)return "c ".$str;
        if($asc>=-19218 and $asc<=-18711)return "d ".$str;
        if($asc>=-18710 and $asc<=-18527)return "e ".$str;
        if($asc>=-18526 and $asc<=-18240)return "f ".$str;
        if($asc>=-18239 and $asc<=-17923)return "g ".$str;
        if($asc>=-17922 and $asc<=-17418)return "h ".$str;
        if($asc>=-17417 and $asc<=-16475)return "j ".$str;
        if($asc>=-16474 and $asc<=-16213)return "k ".$str;
        if($asc>=-16212 and $asc<=-15641)return "l ".$str;
        if($asc>=-15640 and $asc<=-15166)return "m ".$str;
        if($asc>=-15165 and $asc<=-14923)return "n ".$str;
        if($asc>=-14922 and $asc<=-14915)return "o ".$str;
        if($asc>=-14914 and $asc<=-14631)return "p ".$str;
        if($asc>=-14630 and $asc<=-14150)return "q ".$str;
        if($asc>=-14149 and $asc<=-14091)return "r ".$str;
        if($asc>=-14090 and $asc<=-13319)return "s ".$str;
        if($asc>=-13318 and $asc<=-12839)return "t ".$str;
        if($asc>=-12838 and $asc<=-12557)return "w ".$str;
        if($asc>=-12556 and $asc<=-11848)return "x ".$str;
        if($asc>=-11847 and $asc<=-11056)return "y ".$str;
        if($asc>=-11055 and $asc<=-10247)return "z ".$str;
        return NULL; 
}
?>