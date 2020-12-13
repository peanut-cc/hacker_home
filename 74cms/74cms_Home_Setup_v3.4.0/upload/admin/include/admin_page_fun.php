<?php
 /*
 * 74cms �������� ģ������
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
function get_page($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$sql = "select * from ".table('page')." ".$get_sql.$limit;
	$val=$db->getall($sql);
	return $val;
}
//��ȡ����ģ��
function get_page_one($id)
{
	global $db,$_CFG;
	$sql = "select * from ".table('page')." WHERE id='".intval($id)."'";
	$info=$db->getone($sql);
	return $info;
}
//���������Ƿ��ظ�
function ck_page_alias($alias,$noid=NULL){
global $db;
	if ($noid)
	{
	$wheresql=" AND id<>'".intval($noid)."'";
	}
$sql = "select id from ".table('page')." WHERE alias='".$alias."'".$wheresql;
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
function ck_page_file($file,$noid=NULL)
{
	global $db;
		if ($noid)
		{
		$wheresql=" AND id<>'".intval($noid)."'";
		}
	$sql = "select id from ".table('page')." WHERE file='".$file."'".$wheresql;
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
function del_page($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('page')." WHERE id IN (".$sqlin.") AND systemclass<>1 ")) return false;
	$return=$return+$db->affected_rows();
	}
	return $return;
}
function copy_page($filedir,$as)
{
	$newfile = QISHI_ROOT_PATH.$filedir; 
	$fp = fopen(QISHI_ROOT_PATH.'data/filetpl.php', 'r');
	$content = fread($fp, filesize(QISHI_ROOT_PATH.'data/filetpl.php'));
	$content = trim($content);
	$content = substr($content, -2) == '?>' ? substr($content, 0, -2) : $content;
	fclose($fp);
	$content = str_replace('($dir)',QISHI_ROOT_PATH,$content);
	$content = str_replace('($as)',$as,$content);
	$content .= '?>';
	$fp = @fopen($newfile, 'w');
	@fwrite($fp, trim($content));
	@fclose($fp);
	return true;
}
function ck_page_dir($dir)
{
$dir="../".$dir;
$s=strpos($dir,'(');
if ($s)
{
$dir=dirname(substr($dir,0,$s))."/";
}
else
{
	if (strrchr($dir,'/')!='/')
	{
		$info=pathinfo($dir);
		if($info['extension'])
		{
		$dir=$info['dirname']."/";
		}
		else
		{
		$dir=$dir."/";
		}
	}
}
if (!file_exists($dir))
 {
 make_dir($dir);
 }
return true;
}
//����ҳ��URL
function set_page_url($pid,$url,$norewrite)
{
	global $db;
	if (!is_array($pid))return false;
	$sqlin=implode(",",$pid);
	$noarray=array();
	if ($url=="1")
	{
	$noarray=$norewrite;
	}
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$wheresql='';
		if (!empty($noarray))
		{
			foreach ($noarray as $s)
			{
			$wheresql.=" AND alias<>'".$s."' ";
			}
		}
		if (!$db->query("update  ".table('page')." SET url='".intval($url)."'  WHERE id IN (".$sqlin.")".$wheresql)) return false;
	return true;
	}
	return false;
}
//����ҳ�滺��
function set_page_caching($pid,$caching,$nocaching)
{
	global $db;
	if (!is_array($pid))return false;
	$sqlin=implode(",",$pid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$wheresql='';
		foreach ($nocaching as $s)
		{
		$wheresql.=" AND alias<>'{$s}' ";
		}
	if (!$db->query("update  ".table('page')." SET caching='".intval($caching)."'  WHERE id IN (".$sqlin.")".$wheresql)) return false;
	return true;
	}
	return false;
}
//��ʽ��·��
function formatting_dir($dir)
{
$path="../".$dir;
if(strrchr($path,'/')=='/')
		{
		return $dir;
		}
		else
		{
			$info=pathinfo($path);
			if($info['extension'])
			{
			return $dir;
			}
			else
			{
			return $dir."/";
			}
		}
}
?>