<?php
 /*
 * 74cms ��ϵ��ʽͼ�λ�
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = trim($_GET['act']);
$type =intval($_GET['type']);
$token=trim($_GET['token']);
$id=intval($_GET['id']);
if($act == 'jobs_contact')
{
			$sql = "select * from ".table('jobs_contact')." where pid='{$id}' LIMIT 1";
			$val=$db->getone($sql);
			$tmd5=md5($val['contact'].$id.$val['telephone']);
			if ($tmd5<>$token)
			{
			exit();
			}
			switch ($type)
			{
			case 1:
			  $t=$val['contact'];
			  break;  
			case 2:
			   $t=$val['telephone'];
			  break;
			  case 3:
			   $t=$val['email'];
			  break;
			  case 4:
			   $t=$val['address'];
			  break;
			  case 5:
			   $t=$val['qq'];
			  break;
			}
}
elseif($act == 'company_contact')
{
			$sql = "select contact,telephone,email,address,website FROM ".table('company_profile')." where id=".intval($id)." LIMIT 1";
			$val=$db->getone($sql);
			$tmd5=md5($val['contact'].$id.$val['telephone']);
			if ($tmd5<>$token)
			{
			exit();
			}
			switch ($type)
			{
			case 1:
			  $t=$val['contact'];
			  break;  
			case 2:
			   $t=$val['telephone'];
			  break;
			  case 3:
			   $t=$val['email'];
			  break;
			  case 4:
			   $t=$val['address'];
			  break;
			  case 5:
			   $t=$val['website'];
			  break;
			}
}
//������ϵ��ʽ
elseif($act == 'resume_contact')
{
		$tb1=$db->getone("select fullname,telephone,email,qq,address,website from ".table('resume')." WHERE  id='{$id}'  LIMIT 1");
		$tb2=$db->getone("select fullname,telephone,email,qq,address,website from ".table('resume_tmp')." WHERE  id='{$id}'  LIMIT 1");		
		$val=!empty($tb1)?$tb1:$tb2;
		$tmd5=md5($val['fullname'].$id.$val['telephone']);
			if ($tmd5<>$token)
			{
			exit();
			}	
		switch ($type)
			{
			case 1:
			  $t=$val['fullname'];
			  break; 
			case 2:
			  $t=$val['telephone'];
			  break;  
			case 3:
			   $t=$val['email'];
			  break;
			  case 4:
			   $t=$val['qq'];
			  break;
			  case 5:
			   $t=$val['address'];
			  break;
			  case 6:
			   $t=$val['website'];
			  break;
			}
}
 header("Content-type: image/gif");
$w=30+(strlen($t)*8);
$h=20;
$im = imagecreate($w,$h);
$white = imagecolorallocate($im, 255,255,255);
$black = imagecolorallocate($im, 0,0,0);
if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$t=gbk_to_utf8($t);
	}
$ttf=QISHI_ROOT_PATH."data/contactimgfont/cn.ttc";
imagettftext($im, 9, 0, 10, 15, $black, $ttf,$t);
imagegif($im);
?> 
