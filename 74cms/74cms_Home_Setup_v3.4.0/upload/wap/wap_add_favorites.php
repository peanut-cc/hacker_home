<?php
 /*
 * 74cms 添加到收藏夹
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'add';
//未登录
if ($_SESSION['uid']=='' || $_SESSION['username']=='')
{
?>
<script type="text/javascript">
$(".but80").hover(function(){$(this).addClass("but80_hover")},function(){$(this).removeClass("but80_hover")});
//验证
$("#ajax_login").click(function() {
	if ($("#username").val()=="")
	{
	$(".ajax_login_err").show();
	$(".ajax_login_err").html("请填写用户名！");
	return false;
	}
	else if($("#password").val()=="")
	{
	$(".ajax_login_err").show();
	$(".ajax_login_err").html("请填写密码！");
	return false;
	}
	else
	{
		$("#ajax_login").hide();
		$("#ajax_waiting").show();
		var tsTimeStamp= new Date().getTime();
		 if($("#expire").attr("checked")==true)
		 {
		 var expire=$("#expire").val();
		 }
		 else
		 {
		 var expire="";
		 }
		 //alert(expire);
		$.post("<?php echo $_CFG['site_dir'] ?>plus/ajax_user.php", { "username": $("#username").val(),"password": $("#password").val(),"expire":expire,"url":window.location.href,"time":tsTimeStamp,"act":"do_login"},
	 	function (data,textStatus)
	 	 {
			if (data=="err")
			{
			$("#ajax_waiting").hide();
			$("#ajax_login").show();
			$("#password").attr("value","");
			$(".ajax_login_err").show();
			$(".ajax_login_err").html("用户名或者密码错误");
			}
			else
			{
				$("body").append(data);
				//alert(data);
			}
	 	 })
	}
});
</script>
<div class="ajax_login_tit">快速登录</div>
<div class="ajax_login_err"></div>
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" >
    <tr><td width="50" align="right">用户名：</td>
    <td>
      <input name="username" type="text"  class="ajax_login_input" id="username"   maxlength="30" />    </td>
  </tr>
  <tr>
    <td align="right">密&nbsp;&nbsp;&nbsp;&nbsp;码：</td>
    <td>
      <input name="password" type="password"  class="ajax_login_input" id="password"  maxlength="20"/>    </td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td><label><input type="checkbox" name="expire" id="expire" value="7" />
      一周内自动登录</label></td>
  </tr>
  </table>
    <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" >  
  <tr>
    <td align="right" width="50">&nbsp;</td>
    <td height="50" valign="top">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100"><input type="button" name="Submit"  id="ajax_login" class="but80" value="登录" />
		<input type="button" name="Submit"  id="ajax_waiting" class="but80" value="登录中"  style="display:none"/>
		</td>
        <td class="link_bk"><a href="<?php echo $_CFG['site_dir'] ?>user/getpass.php">忘记密码？</a></td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td align="right" style="border-top:1px  #E8E8E8 solid">&nbsp;</td>
    <td height="30" valign="bottom" class="link_lan" style="border-top:1px  #E8E8E8 solid">还没有账号？<a href="<?php echo $_CFG['site_dir'] ?>user/user_reg.php">免费注册</a></td>
  </tr>
</table>
<?php
exit();
}
elseif ($_SESSION['utype']!='2')
{
	exit("必须是个人会员可以申请职位！");
}
require_once(QISHI_ROOT_PATH.'include/fun_personal.php');
$user=get_user_info($_SESSION['uid']);
if ($user['status']=="2") 
{
	$str="<a href=\"".get_member_url(2,true)."?act=user_status\">[设置帐号状态]</a>";
	exit("您的账号处于暂停状态，请先设为正常后进行操作！".$str);
}
if ($act=="add")
{
	$id=isset($_GET['id'])?trim($_GET['id']):exit("出错了"); 
	if(add_favorites($id,$_SESSION['uid'])==0)
	{
	exit("添加失败，收藏夹中已经存在此职位");
	}
	else
	{
?>
<script type="text/javascript">
$("#add_ok .closed").click(function(){
$("#floatBoxBg").hide();
$("#floatBox").hide(); 
});
</script>
<table width="100%" border="0" cellspacing="8" cellpadding="0" id="add_ok">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/13.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">添加成功!</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;"  class="dialog_closed">
	<a href="<?php echo get_member_url(2,true)?>?act=favorites" >查看职位收藏夹</a><br />

	<a href="javascript:void(0)"  class="DialogClose">添加完成</a>
	
	</div>
	</td>
  </tr>
</table>
<?php
}
}
?>