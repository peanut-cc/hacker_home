<?php
 /*
 * 74cms ����word
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
unset($dbhost,$dbuser,$dbpass,$dbname);
$uid=intval($_GET['uid']);//����������Ա��uid
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
if(!$flag) {showmsg('��û��Ȩ�ޣ�ֻ�и����û�����ҵ�û�����ת������',1);exit();}
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
    <td align="center"><span class="STYLE1">'.$val['fullname'].'�ĸ��˼���</span></td>
  </tr>
</table>
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
<tr>
    <td colspan="4" bgcolor="#E1EEF4">������Ϣ</td>
  </tr>
  <tr>
    <td width="110" align="right">��ʵ������</td>
    <td>'.$val['fullname'].'</td>
    <td width="100" align="right">�Ա�</td>
    <td>'.$val['sex_cn'].'</td>
  </tr>
  <tr>
    <td align="right">���䣺</td>
    <td>'.$val['age'].'��</td>
    <td align="right">��ߣ�</td>
    <td>'.$val['height'].'CM&nbsp;</td>
  </tr>
  <tr>
    <td align="right">����״����</td>
    <td>'.$val['marriage_cn'].'</td>
    <td align="right">�������ڣ�</td>
    <td>'.$val['householdaddress'].'</td>
  </tr>
   <tr>
    <td align="right">���ѧ����</td>
    <td>'.$val['education_cn'].'</td>
    <td align="right">�������飺</td>
    <td>'.$val['experience_cn'].'</td>
  </tr>
  <tr>
    <td align="right">��ϵ��ַ��</td>
    <td>'.$val['address'].'</td>
    <td align="right">������ҳ��</td>
    <td>'.$val['website'].'</td>
  </tr>
</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="2" bgcolor="#E1EEF4">��ְ����</td>
  </tr>
  <tr>
    <td width="110" align="right">�����������ְλ�� </td>
    <td>'.$val['recentjobs'].'</td>
  </tr>
  
  <tr>
    <td align="right"  >������λ���ʣ�</td>
    <td>'.$val['nature_cn'].'</td>
  </tr>
  
  <tr>
    <td align="right"  >���������أ� </td>
    <td>'.$val['district_cn'].'&nbsp;</td>
  </tr>
  <tr>
    <td align="right"  >������н�� </td>
    <td>'.$val['wage_cn'].'&nbsp;</td>
  </tr>
  <tr>
    <td align="right"  >�������µĸ�λ�� </td>
    <td>'.$val['intention_jobs'].'&nbsp;</td>
  </tr>
  <tr>
    <td align="right"  >�������µ���ҵ�� </td>
    <td>'.$val['trade_cn'].'&nbsp;</td>
  </tr>
</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="2" bgcolor="#E1EEF4">���˱�ǩ����������</td>
  </tr>
  <tr>
    <td width="110" align="right">���˱�ǩ�� </td>
    <td>'.nl2br($val['tagcn']).'&nbsp;</td>
  </tr>
 </table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="2" bgcolor="#E1EEF4">�����س�</td>
  </tr>
  <tr>
    <td width="110" align="right">�����س��� </td>
    <td>'.nl2br($val['specialty']).'&nbsp;</td>
  </tr>
</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="4" bgcolor="#E1EEF4">��������</td>
  </tr>
  <tr>
    <td width="110" align="center" bgcolor="#F1F7FA">��ֹ����</td>
    <td align="center" bgcolor="#F1F7FA">ѧУ����</td>
    <td align="center" bgcolor="#F1F7FA">רҵ����</td>
    <td align="center" bgcolor="#F1F7FA">���ѧ��</td>
  </tr>';
  if($val['education_list']){
	  foreach ($val['education_list'] as $eli)
	  {
	  $htm.='<tr>
		<td align="center"  >'.$eli['start'].'��'.$eli['endtime'].'&nbsp;</td>
		<td align="center">'.$eli['school'].'&nbsp;</td>
		<td align="center">'.$eli['speciality'].'&nbsp;</td>
		<td align="center">'.$eli['education_cn'].'&nbsp;</td>
	  </tr>';
	  }
  }else{
	 $htm.='<tr><td colspan="4" bgcolor="#FFFFFF">û����д��������</td></tr>';
  }
$htm.='</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="4" bgcolor="#E1EEF4">��������</td>
  </tr>';
  if($val['work_list']){
	   foreach ($val['work_list'] as $wli)
	  {  $htm.='<tr>
		<td colspan="4" bgcolor="#FFFFFF">
		��ֹ���£�'.$wli['start'].'��'.$wli['endtime'].'<br />
		��ҵ���ƣ�'.$wli['companyname'].'<br />
		����ְλ��'.$wli['jobs'];
			if($wli['achievements']){
			  $htm.='<br />ҵ�����֣�'.$wli['achievements'];
			}
			if($wli['companyprofile']){
			  $htm.='<br />��˾���ܣ�'.$wli['companyprofile'];
			}
		
		  $htm.='</td>
	  </tr>';
	  }
  }else{
	 $htm.='<tr><td colspan="4" bgcolor="#FFFFFF">û����д��������</td></tr>';
  }
$htm.='</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="4" bgcolor="#E1EEF4">��ѵ����</td>
  </tr>';
  if($val['training_list']){
	   foreach ($val['training_list'] as $tli)
	  {
	  $htm.=' <tr>
		<td colspan="4" bgcolor="#FFFFFF">
		��ֹ���ڣ�'.$tli['start'].'��'.$tli['endtime'].'<br />
		��ѵ������'.$tli['agency'].'<br />
		��ѵ�γ̣�'.$tli['course'].'<br />
		��ѵ������'.$tli['description'].'
		</td>
	  </tr>';
	  }
  }else{
	 $htm.='<tr><td colspan="4" bgcolor="#FFFFFF">û����д��ѵ����</td></tr>';
  }
$htm.='</table>
<br />
<br />
<table width="700" border="1" align="center" cellpadding="5" cellspacing="0" bordercolor="#333333" >
  <tr>
    <td colspan="4" bgcolor="#E1EEF4">��ϵ��ʽ</td>
  </tr>
  <tr>
    <td colspan="4" bgcolor="#FFFFFF">';
if($_CFG['showresumecontact']=='1' || $_CFG['showresumecontact']=='0')
{
	$show=true;
}
elseif($_CFG['showresumecontact']=='2')//��ϵ��ʽ����Ա���غ�ɼ�
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
	��ϵ�ˣ�'.$val['fullname'].'<br />
	��ϵ�绰��'.$val['telephone'].'<br />
	��ϵ���䣺'.$val['email'].'<br />
	��ϵ Q Q��'.$val['qq'].'<br />
	��ϵ��ַ��'.$val['address'].'<br />
	���˲���/��ҳ��'.$val['website'].'<br />';
}else{
	$resume_url=$_CFG['site_domain'].url_rewrite('QS_resumeshow',array('id'=>$val['id']));
	$contact="<a href=\"{$resume_url}\" >����</a>����ܲ鿴��ϵ��ʽ";
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
header("Content-Disposition:attachment; filename={$val['fullname']}�ĸ��˼���.doc"); 
echo $htm;
}
else
{
 showmsg('���������ڣ�',1);
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