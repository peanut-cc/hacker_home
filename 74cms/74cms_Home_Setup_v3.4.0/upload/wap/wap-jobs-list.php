<?php
 /*
 * 74cms WAP
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/fun_wap.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$smarty->cache = false;
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$page = empty($_GET['page'])?1:intval($_GET['page']);
$sdistrict = empty($_GET['sdistrict'])?"":intval($_GET['sdistrict']);
$subclass = empty($_GET['subclass'])?"":intval($_GET['subclass']);
$key = empty($_GET['key'])?"":$_GET['key'];
$jobstable=table('jobs_search_rtime');
if ($sdistrict)
{
	$wheresql.=" AND sdistrict=".$sdistrict." ";
}
if ($subclass)
{
	$wheresql.=" AND subclass=".$subclass." ";
}
$orderbysql=" ORDER BY refreshtime desc";
if (!empty($key))
{
	$key=trim($key);
	$akey=explode(' ',$key);
	if (count($akey)>1)
	{
	$akey=array_filter($akey);
	$akey=array_slice($akey,0,2);
	$akey=array_map("fulltextpad",$akey);
	$ykey='+'.implode(' +',$akey);
	$mode=' IN BOOLEAN MODE';
	}
	else
	{
	$ykey=fulltextpad($key);
	$mode=' ';
	}
	$wheresql.=" AND  MATCH (`key`) AGAINST ('{$ykey}'{$mode}) ";
	$orderbysql="";
	$jobstable=table('jobs_search_key');
}
if (!empty($wheresql))
{
$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
}

	$perpage = 5;
	$count  = 0;
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	$theurl = "wap-jobs-list.php?sdistrict=".$sdistrict."&amp;subclass=".$subclass."&amp;key=".$key;
	$start = ($page-1)*$perpage;
	$total_sql="SELECT COUNT(*) AS num FROM {$jobstable} {$wheresql}";
	$count=$db->get_total($total_sql);
	$limit=" LIMIT {$start},{$perpage}";
	$idresult = $db->query("SELECT id FROM {$jobstable} ".$wheresql.$orderbysql.$limit);
	while($row = $db->fetch_array($idresult))
	{
	$id[]=$row['id'];
	}
	if (!empty($id))
	{
		$wheresql=" WHERE id IN (".implode(',',$id).") ";
		$jobs = $db->getall("SELECT * FROM ".table('jobs').$wheresql.$orderbysql);	
	}
	else
	{
		$jobs=array();
	}
	$smarty->assign('jobs',$jobs);
	$smarty->assign('pagehtml',wapmulti($count, $perpage, $page, $theurl));
	$smarty->display("wap/wap-jobs-list.htm");
?>