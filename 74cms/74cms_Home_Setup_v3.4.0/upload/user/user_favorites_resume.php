<?php
 /*
 * 74cms ��ӵ��˲ſ�
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
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'add';
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
if ($_SESSION['uid']=='' || $_SESSION['username']=='')
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
if ($_SESSION['utype']!='1')
{
	exit("��������ҵ��Ա�ſ���ʹ���˲ſ⣡");
}
require_once(QISHI_ROOT_PATH.'include/fun_company.php');
$user=get_user_info($_SESSION['uid']);
if ($user['status']=="2") 
{
	$str="<a href=\"".get_member_url(2,true)."company_user.php?act=user_status\">[�����ʺ�״̬]</a>";
	exit("�����˺Ŵ�����ͣ״̬��������Ϊ��������в�����".$str);
}
if ($act=="add")
{
	$id=isset($_GET['id'])?$_GET['id']:exit("������"); 
	$add_return=add_favorites($id,$_SESSION['uid']);
	if ($add_return==="full")
	{
	exit("���ʧ�ܣ��˲ſ������Ѿ������������!");
	}
	elseif ($add_return=="0")
	{
	exit("���ʧ�ܣ��˲ſ����Ѿ����ڣ�");
	}
	else
	{
?>
<table width="100%" border="0" cellspacing="8" cellpadding="0" id="add_ok">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/13.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">��ӳɹ�!����� <?php echo $add_return?>�ݼ���</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;">
	<a href="<?php echo get_member_url(1,true)?>company_recruitment.php?act=favorites_list" >�鿴�˲ſ�</a><br />

	<a href="javascript:void(0)"  class="DialogClose">������</a>
	
	</div>
	</td>
  </tr>
</table>
<?php
}
}
?>