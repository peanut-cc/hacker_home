<?php
 /*
 * 74cms ����Ϣ
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
if (empty($_SESSION['uid']) || empty($_SESSION['username']))
{
	$captcha=get_cache('captcha');
	$smarty->assign('verify_userlogin',$captcha['verify_userlogin']);
	$smarty->display('plus/ajax_login.htm');
	exit();
}
if ($act=="add")
{
	$tuid=intval($_GET['tuid']);
	if ($tuid==$_SESSION['uid'])
	{
	exit("<div align=\"center\">�����ܸ��Լ�����Ϣ��</div>");
	}
	if ($tuid)
	{
	$username=$db->getone("SELECT username FROM ".table('members')." WHERE uid ='{$tuid}' LIMIT 1");
	$username=$username['username'];
	}
?>
<script type="text/javascript">
$(document).ready(function()
{
	$("#toname,#msg").focus(function(){
	$('#pmserr').hide();
	});
	$("#pms_post_sbt").click(function(){
	var toname=$('#toname').val();
	var msg=$('#msg').val();
	var t=true;
	if (toname=='')
	{
	$('#pmserr').show().html('����д�ռ��ˣ�');
	t=false;
	}
	if (msg.length>100 || msg.length<2)
	{
	$('#pmserr').show().html('���ݱ�������2-100���ַ�֮�䣡');
	t=false;
	}
	if (t)
	{
			$.get("<?php echo $_CFG['site_dir'] ?>user/user_pms.php", { "toname": $('#toname').val(),"msg":$('#msg').val(),"act":"add_save"},
			function (data,textStatus)
			 {			
				if (data=='OK')
				{
				$('#Formadd').html("<div align=\"center\">����Ϣ���ͳɹ���</div>");
				}
				else
				{
				$('#pmserr').show().html(data);
				}
			 },'text'
			);
	}

});	
});
</script>
<div id="Formadd">
<table width="100%" border="0" cellpadding="3" cellspacing="0">
  <tr>
    <td width="50" align="right">�ռ��ˣ�</td>
    <td>
	<?php
	if (!empty($username))
	{
	echo $username;
	echo "<input name=\"toname\" type=\"hidden\" id=\"toname\" value=\"{$username}\" />";
	}
	else
	{
	echo '<input name="toname" type="text" class="input_text_400" id="toname" style="width:280px;"/>';
	}
	?>
</td>
  </tr>
  <tr>
    <td align="right">���ݣ�</td>
    <td>
	<textarea name="msg"  id="msg" style="width:280px; height:80px; font-size:12px; line-height:180%; margin-bottom:6px;" ></textarea>
	</td>
  </tr>

  <tr>
    <td>&nbsp;</td>
    <td>
	<div style=" height:25px; line-height:25px; color: #FF0000; margin-bottom:15px;display:none" id="pmserr"></div>
	<input type="button" name="Submit" value="����"  class="but80" id="pms_post_sbt" />
	</td>
  </tr>
</table>
</div>
<?php
}
elseif($act=="add_save")
{
	$setsqlarr['msgtype']=2;
	$setsqlarr['msgfrom']=trim($_SESSION['username']);
	$setsqlarr['msgfromuid']=intval($_SESSION['uid']);
	$toname=trim($_GET['toname']);
	$setsqlarr['message']=trim($_GET['msg']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$toname=utf8_to_gbk($toname);
	$setsqlarr['message']=utf8_to_gbk($setsqlarr['message']);
	}
	$msgtouser= $db->getone("select * from ".table('members')." where username = '{$toname}' LIMIT 1");
	if (empty($msgtouser))
	{
	exit('�ռ��˲����ڣ�');
	}
	elseif ($msgtouser['uid']==$_SESSION['uid'])
	{
	exit("�����ܸ��Լ�����Ϣ��");
	}
	elseif ($_SESSION['utype']=='1' && $msgtouser['utype']=='2')
	{
		$sql = "select did from ".table('company_down_resume')." WHERE company_uid = '{$_SESSION['uid']}' AND resume_uid='{$msgtouser['uid']}' LIMIT 1";
		$info=$db->getone($sql);
		if (empty($info))
		{
		exit("��û�����ع���Ա<strong>{$msgtouser['username']}</strong>�����ļ��������ع�������ſ��Ը�TA������Ϣ��");
		}
	}
	$setsqlarr['msgtouid']=$msgtouser['uid'];
	$setsqlarr['msgtoname']=$msgtouser['username'];
	$setsqlarr['dateline']=time();
	$setsqlarr['new']=1;
	$setsqlarr['replytime']=$setsqlarr['dateline'];
	$setsqlarr['replyuid']=$setsqlarr['msgfromuid']; 
	inserttable(table('pms'),$setsqlarr);
	exit('OK');
}
?>
