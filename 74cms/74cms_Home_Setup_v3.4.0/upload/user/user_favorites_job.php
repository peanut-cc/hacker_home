<?php
 /*
 * 74cms ��ӵ��ղؼ�
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
if ($_SESSION['utype']!='2')
{
	exit("�����Ǹ��˻�Ա�����ղ�ְλ��");
}
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
$user=get_user_info($_SESSION['uid']);
if ($user['status']=="2") 
{
	$str="<a href=\"".get_member_url(2,true)."personal_user.php?act=user_status\">[�����ʺ�״̬]</a>";
	exit("�����˺Ŵ�����ͣ״̬��������Ϊ��������в�����".$str);
}
if ($act=="add")
{
	$id=isset($_GET['id'])?trim($_GET['id']):exit("������"); 
	if(add_favorites($id,$_SESSION['uid'])==0)
	{
	exit("���ʧ�ܣ��ղؼ����Ѿ����ڴ�ְλ");
	}
	else
	{
?>
<script type="text/javascript">
$("#add_ok .closed").click(function()
{
DialogClose();
});

function DialogClose()
{
	$("#FloatBg").hide();
	$("#FloatBox").hide();
}
</script>
<table width="100%" border="0" cellspacing="8" cellpadding="0" id="add_ok">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/13.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">��ӳɹ�!</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;"  class="dialog_closed">
	<a href="<?php echo get_member_url(2,true)?>personal_apply.php?act=favorites" >�鿴ְλ�ղؼ�</a><br />

	<a href="javascript:void(0)"  class="DialogClose">������</a>
	
	</div>
	</td>
  </tr>
</table>
<?php
}
}
?>