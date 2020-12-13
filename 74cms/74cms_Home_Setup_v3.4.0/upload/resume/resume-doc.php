<?php
 /*
 * 74cms 生成word
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
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$uid=intval($_GET['uid']);//简历所属会员的uid
$id=intval($_GET['resume_id']);
if ($_SESSION['uid']=='' || $_SESSION['username']=='')
{
	$resume_url=url_rewrite('QS_resumeshow',array('id'=>$id));
    header("Location:".url_rewrite('QS_login')."?url={$resume_url}");
	exit();
}
if(($_SESSION['utype']=='2' && $_SESSION['uid']==$uid) || $_SESSION['utype']=='1'){
	$flag=true;
}else{
	$flag=false;
}
if(!$flag) {showmsg('您没有权限！只有个人用户和企业用户可以转换简历',1);exit();}
$wheresql=" WHERE  id='{$id}'  AND uid='{$uid}' ";
$sql = "select * from ".table('resume').$wheresql." LIMIT  1";
$val=$db->getone($sql);
if ($val)
{
	$val['education_list']=get_this_education($val['uid'],$val['id']);
	$val['work_list']=get_this_work($val['uid'],$val['id']);
	$val['training_list']=get_this_training($val['uid'],$val['id']);
	$val['age']=date("Y")-$val['birthdate'];
	$val['tagcn']=preg_replace("/\d+/", '',$val['tag']);
	$val['tagcn']=preg_replace('/\,/','',$val['tagcn']);
	$val['tagcn']=preg_replace('/\|/','&nbsp;&nbsp;&nbsp;',$val['tagcn']);
 	if ($val['display_name']=="2")
	{
		$val['fullname']="N".str_pad($val['id'],7,"0",STR_PAD_LEFT);
		$val['fullname_']=$val['fullname'];		
	}
	elseif($val['display_name']=="3")
	{
		$val['fullname']=cut_str($val['fullname'],1,0,"**");
		$val['fullname_']=$val['fullname'];	
	}
	else
	{
		$val['fullname_']=$val['fullname'];
		$val['fullname']=$val['fullname'];
	}
	
	
	
	
	$htm='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title></title>
<style type="text/css">
<!--
body,td,th {
	font-size: 12px;
}
.STYLE1 {font-size: 36px}
-->
</style></head>
<body>
<table width="700" border="0" align="center" cellpadding="20" cellspacing="0">
  <tr>
    <td align="center"><span class="STYLE1">'.$val['fullname'].'的个人简历</span></td>
  </tr>
</table>
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
<tr>
    <td colspan="4" bgcolor="#E1EEF4">基本信息</td>
  </tr>
  <tr>
    <td width="110" align="right">真实姓名：</td>
    <td>'.$val['fullname'].'</td>
    <td width="100" align="right">性别：</td>
    <td>'.$val['sex_cn'].'</td>
  </tr>
  <tr>
    <td align="right">年龄：</td>
    <td>'.$val['age'].'岁</td>
    <td align="right">身高：</td>
    <td>'.$val['height'].'CM&nbsp;</td>
  </tr>
  <tr>
    <td align="right">婚姻状况：</td>
    <td>'.$val['marriage_cn'].'</td>
    <td align="right">户籍所在：</td>
    <td>'.$val['householdaddress'].'</td>
  </tr>
   <tr>
    <td align="right">最高学历：</td>
    <td>'.$val['education_cn'].'</td>
    <td align="right">工作经验：</td>
    <td>'.$val['experience_cn'].'</td>
  </tr>
  <tr>
    <td align="right">联系地址：</td>
    <td>'.$val['address'].'</td>
    <td align="right">个人主页：</td>
    <td>'.$val['website'].'</td>
  </tr>
</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="2" bgcolor="#E1EEF4">求职意向</td>
  </tr>
  <tr>
    <td width="110" align="right">最近工作过的职位： </td>
    <td>'.$val['recentjobs'].'</td>
  </tr>
  
  <tr>
    <td align="right"  >期望岗位性质：</td>
    <td>'.$val['nature_cn'].'</td>
  </tr>
  
  <tr>
    <td align="right"  >期望工作地： </td>
    <td>'.$val['district_cn'].'&nbsp;</td>
  </tr>
  <tr>
    <td align="right"  >期望月薪： </td>
    <td>'.$val['wage_cn'].'&nbsp;</td>
  </tr>
  <tr>
    <td align="right"  >期望从事的岗位： </td>
    <td>'.$val['intention_jobs'].'&nbsp;</td>
  </tr>
  <tr>
    <td align="right"  >期望从事的行业： </td>
    <td>'.$val['trade_cn'].'&nbsp;</td>
  </tr>
</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="2" bgcolor="#E1EEF4">个人标签及语言能力</td>
  </tr>
  <tr>
    <td width="110" align="right">个人标签： </td>
    <td>'.nl2br($val['tagcn']).'&nbsp;</td>
  </tr>
 </table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="2" bgcolor="#E1EEF4">技能特长</td>
  </tr>
  <tr>
    <td width="110" align="right">技能特长： </td>
    <td>'.nl2br($val['specialty']).'&nbsp;</td>
  </tr>
</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="4" bgcolor="#E1EEF4">教育经历</td>
  </tr>
  <tr>
    <td width="110" align="center" bgcolor="#F1F7FA">起止年月</td>
    <td align="center" bgcolor="#F1F7FA">学校名称</td>
    <td align="center" bgcolor="#F1F7FA">专业名称</td>
    <td align="center" bgcolor="#F1F7FA">获得学历</td>
  </tr>';
  if($val['education_list']){
	  foreach ($val['education_list'] as $eli)
	  {
	  $htm.='<tr>
		<td align="center"  >'.$eli['start'].'至'.$eli['endtime'].'&nbsp;</td>
		<td align="center">'.$eli['school'].'&nbsp;</td>
		<td align="center">'.$eli['speciality'].'&nbsp;</td>
		<td align="center">'.$eli['education_cn'].'&nbsp;</td>
	  </tr>';
	  }
  }else{
	 $htm.='<tr><td colspan="4" bgcolor="#FFFFFF">没有填写教育经历</td></tr>';
  }
$htm.='</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="4" bgcolor="#E1EEF4">工作经历</td>
  </tr>';
  if($val['work_list']){
	   foreach ($val['work_list'] as $wli)
	  {  $htm.='<tr>
		<td colspan="4" bgcolor="#FFFFFF">
		起止年月：'.$wli['start'].'至'.$wli['endtime'].'<br />
		企业名称：'.$wli['companyname'].'<br />
		从事职位：'.$wli['jobs'];
			if($wli['achievements']){
			  $htm.='<br />业绩表现：'.$wli['achievements'];
			}
			if($wli['companyprofile']){
			  $htm.='<br />公司介绍：'.$wli['companyprofile'];
			}
		
		  $htm.='</td>
	  </tr>';
	  }
  }else{
	 $htm.='<tr><td colspan="4" bgcolor="#FFFFFF">没有填写工作经历</td></tr>';
  }
$htm.='</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="4" bgcolor="#E1EEF4">培训经历</td>
  </tr>';
  if($val['training_list']){
	   foreach ($val['training_list'] as $tli)
	  {
	  $htm.=' <tr>
		<td colspan="4" bgcolor="#FFFFFF">
		起止日期：'.$tli['start'].'至'.$tli['endtime'].'<br />
		培训机构：'.$tli['agency'].'<br />
		培训课程：'.$tli['course'].'<br />
		培训描述：'.$tli['description'].'
		</td>
	  </tr>';
	  }
  }else{
	 $htm.='<tr><td colspan="4" bgcolor="#FFFFFF">没有填写培训经历</td></tr>';
  }
$htm.='</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="4" bgcolor="#E1EEF4">联系方式</td>
  </tr>
  <tr>
    <td colspan="4" bgcolor="#FFFFFF">';
if($_CFG['showresumecontact']=='1' || $_CFG['showresumecontact']=='0')
{
	$show=true;
}
elseif($_CFG['showresumecontact']=='2')//联系方式：会员下载后可见
{
	if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
	{
		$sql = "select did from ".table('company_down_resume')." WHERE company_uid = {$_SESSION['uid']} AND resume_id='{$id}' LIMIT 1";
		$info=$db->getone($sql);
		if (!empty($info))
		{
		$show=true;
		}
		else
		{
		$show=false;
		}
	}elseif($_SESSION['utype']=='2' && $_SESSION['uid']==$uid){
		$show=true;
	}else{
		$show=false;
	}
}
if($show){
	$contact='
	联系人：'.$val['fullname'].'<br />
	联系电话：'.$val['telephone'].'<br />
	联系邮箱：'.$val['email'].'<br />
	联系 Q Q：'.$val['qq'].'<br />
	联系地址：'.$val['address'].'<br />
	个人博客/主页：'.$val['website'].'<br />';
}else{
	$resume_url=$_CFG['site_domain'].url_rewrite('QS_resumeshow',array('id'=>$val['id']));
	$contact="<a href=\"{$resume_url}\" >下载</a>后才能查看联系方式";
}
$footer="</td>
  </tr>
</table>
<div align=\"center\"><br />
	<a title=\"{$_CFG['site_name']}\" href=\"{$_CFG['site_domain']}{$_CFG['site_dir']}\">{$_CFG['site_name']}</a>
</div>
</body>
</html>";
$htm=$htm.$contact.$footer;
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");   
header("Content-Type: application/doc"); 
header("Content-Disposition:attachment; filename={$val['fullname']}的个人简历.doc"); 
echo $htm;
}
else
{
 showmsg('简历不存在！',1);
 exit();
}
function get_this_education($uid,$pid)
{
	global $db;
	$sql = "SELECT * FROM ".table('resume_education')." WHERE uid='".intval($uid)."' AND pid='".intval($pid)."' ";
	return $db->getall($sql);
}
function get_this_work($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_work')." where uid=".intval($uid)." AND pid='".$pid."' " ;
	return $db->getall($sql);
}
function get_this_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where uid='".intval($uid)."' AND pid='".intval($pid)."'";
	return $db->getall($sql);
}
function get_user_setmealt($uid)
{
	global $db;
	$sql = "select * from ".table('members_setmeal')."  WHERE uid=".intval($uid)." AND  effective=1 LIMIT 1";
	return $db->getone($sql);
}
?>