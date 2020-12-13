<?php
function tpl_modifier_qishi_categoryname($string)
{
		global $db;
		$val=explode(",",$string);
		$type=trim($val[0]);
		$id=intval($val[1]);
		$len=intval($val[2]);
		if ($type=="QS_jobs")
		{
			$cat=$db->getone("select categoryname from ".table('category_jobs')." WHERE  id='{$id}' LIMIT  1");
			if ($len>0) $cat['categoryname']=cut_str($cat['categoryname'],$len,0,'');
			return $cat['categoryname'];
		}
 		elseif ($type=="QS_district")
		{
			$cat=$db->getone("select categoryname from ".table('category_district')." WHERE  id='{$id}' LIMIT  1");
			if ($len>0) $cat['categoryname']=cut_str($cat['categoryname'],$len,0,'');
			return $cat['categoryname'];
		}
		elseif ($type=="QS_officebuilding" || $type=="QS_street")
		{
			$cat=$db->getone("select c_name from ".table('category')." WHERE  c_id='{$id}' LIMIT  1");
			$cat['categoryname']=$cat['c_name'];
			if ($len>0) $cat['categoryname']=cut_str($cat['categoryname'],$len,0,'');
			return $cat['categoryname'];
		}
		else
		{
			$_CAT=get_cache('category');
			if ($len>0) $_CAT[$type][$id]['categoryname']=cut_str($_CAT[$type][$id]['categoryname'],$len,0,'');
			return $_CAT[$type][$id]['categoryname'];
		}		 
}
?>