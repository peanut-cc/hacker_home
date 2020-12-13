<?php
 /*
 * 74cms  延期职位
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
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
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
	exit("必须是企业会员才可以延期职位！");
}
		require_once(QISHI_ROOT_PATH.'include/fun_company.php');
		$user=get_user_info($_SESSION['uid']);
		if ($user['status']=="2") 
		{
			$str="<a href=\"".get_member_url(1,true)."company_user.php?act=user_status\">[设置帐号状态]</a>";
			exit("您的账号处于暂停状态，请先设为正常后进行操作！".$str);
		}
$id=!empty($_GET['id'])?intval($_GET['id']):exit("出错了");
$jobs=get_jobs_one(intval($_GET['id']),$_SESSION['uid']);
if ($_CFG['operation_mode']=="2")
{
	$setmeal=get_user_setmeal($_SESSION['uid']);
	$tip="提示：职位：<span>{$jobs['jobs_name']}</span>,截止日期：<span>".date('Y-m-d',$jobs['deadline']).'</span>';
	if (empty($setmeal) || ($setmeal['endtime']<time() && $setmeal['endtime']<>'0'))
	{
		$str="<a href=\"".get_member_url(1,true)."company_service.php?act=setmeal_list\">[申请服务]</a>";
		exit("您的服务已到期,不能延期职位。您可以 {$str}");
	}
}
elseif($_CFG['operation_mode']=="1")
{
			$points_rule=get_cache('points_rule');
			$day_points=$points_rule['jobs_daily']['value'];
			$mypoints=get_user_points($_SESSION['uid']);
			$tip="提示：延期职位，每天消耗<span> {$day_points}</span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}，您目前共有<span> {$mypoints}</span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}";
}

if ($act=="delay")
{
?>
<script type="text/javascript">
$(".but100").hover(function(){$(this).addClass("but100_hover")},function(){$(this).removeClass("but100_hover")});
 	var	operation_mode="<?php echo $_CFG['operation_mode'];?>";
	if(operation_mode=='1'){
		$("#days").keyup(function(){	
			if((/^(\+|-)?\d+$/.test($(this).val())))
			{
				var days_points="<?php echo $day_points;?>";
				var user_points="<?php echo $mypoints;?>";
				var total_points=days_points*$(this).val();
				var points_quantifier="<?php echo $_CFG['points_quantifier'];?>";
				var points_byname="<?php echo $_CFG['points_byname'];?>";
				var tip="提示：您的延期天数<span>"+$(this).val()+"天</span>,需要扣除："+"<span>"+total_points+"</span>"+points_quantifier+points_byname+"，您目前共有<span>"+user_points+"</span>"+points_quantifier+points_byname;
				$(".ajax_delay_tip").html(tip)
			}else{
				var tip="<?php echo $tip;?>";
				$(".ajax_delay_tip").html(tip)
			}
		});	
	}
 $("#ajax_delay_r").click(function() {
		var id="<?php echo $id?>";
		var days=$("#days").val();
		var olddeadline=$("#olddeadline").val();
		var tsTimeStamp= new Date().getTime();
			$("#ajax_delay_r").val("处理中...");
			$("#ajax_delay_r").attr("disabled","disabled");
		$.get("<?php echo $_CFG['site_dir'] ?>user/user_delay_jobs.php", { "id":id,"days":days,"olddeadline":olddeadline,"time":tsTimeStamp,"act":"delay_save"},
	 	function (data,textStatus)
	 	 {
			if (data=="ok")
			{
			$(".ajax_delay_tip").hide();
			$("#ajax_delay_table").hide();
			$("#delay_ok").show();			 
					$("#delay_ok .closed").click(function(){
						DialogClose();
					});
			}
			else
			{
				$(".ajax_delay_tip").html(data);
				$("#ajax_delay_table").hide();
			}
			$("#ajax_delay_r").val("延期职位");
			$("#ajax_delay_r").attr("disabled","");
	 	 })
});
function DialogClose()
{
	$("#FloatBg").hide();
	$("#FloatBox").hide();
}
</script>
<div class="ajax_delay_tip"><?php echo $tip?></div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" id="ajax_delay_table">
  <tr>
    <td align="right">延期天数：</td><td align="left"><input type="text" name="days"  id="days" class="input_text_150" maxlength="3" /></td>
	<input type="hidden" name="olddeadline"   value="<?php echo $jobs['deadline']; ?>" id="olddeadline" />
  </tr>
  <tr>
    <td align="center" colspan="2"><input type="button" name="Submit"  id="ajax_delay_r" class="but100" value="延期职位" /></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="8" cellpadding="0" id="delay_ok"  style="display:none">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/13.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">延期成功!</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;"  class="dialog_closed">
	<a href="<?php echo get_member_url(1,true)?>company_jobs.php?act=jobs" >查看已延期的职位</a><br />
	</div>
	</td>
  </tr>
</table>
<?php
}
elseif ($act=="delay_save")
{
	$id=intval($_GET['id'])?intval($_GET['id']):exit("出错了");
	$days=intval($_GET['days'])?intval($_GET['days']):exit("出错了");
	$olddeadline=intval($_GET['olddeadline'])?intval($_GET['olddeadline']):exit("出错了");
	if ($_CFG['operation_mode']=="2")
	{	
		$setmeal=get_user_setmeal($_SESSION['uid']);
		if (empty($setmeal) || ($setmeal['endtime']<time() && $setmeal['endtime']<>'0'))
		{
			$str="<a href=\"".get_member_url(1,true)."company_service.php?act=setmeal_list\">[申请服务]</a>";
			exit("您的服务已到期,不能延期职位。您可以 {$str}");
		}
		if (delay_jobs($id,$_SESSION['uid'],$days,$olddeadline)){
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"延长职位({$jobs['jobs_name']}) 有效期为{$days}天，职位ID:{$id}");//记录套餐操作
			write_memberslog($_SESSION['uid'],1,2007,$_SESSION['username'],"延长职位({$jobs['jobs_name']}) 有效期为{$days}天");//记录套餐操作
			exit('ok');
		}
	}
	elseif($_CFG['operation_mode']=="1")
	{
				$points_rule=get_cache('points_rule');
				$day_points=$points_rule['jobs_daily']['value'];
				$ptype=$points_rule['jobs_daily']['type'];
				$mypoints=get_user_points($_SESSION['uid']);
				if ($points_rule['jobs_daily']['type']=="2" && $points_rule['jobs_daily']['value']>0){
						$points=$day_points*$days;
				}
				if  ($mypoints<$points){
					$str="<a href=\"".get_member_url(1,true)."company_service.php?act=order_add\">[充值{$_CFG['points_byname']}]</a>";
				exit("你的".$_CFG['points_byname']."不足，请".$str);
				}
				if (delay_jobs($id,$_SESSION['uid'],$days,$olddeadline))
				{
					if ($points>0)
					{
						report_deal($_SESSION['uid'],$ptype,$points);
						$user_points=get_user_points($_SESSION['uid']);
						$operator=$ptype=="1"?"+":"-";
						write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"延长职位({$jobs['jobs_name']})有效期为{$days}天，({$operator}{$points})，(剩余:{$user_points})");
						write_memberslog($_SESSION['uid'],1,2007,$_SESSION['username'],"延长职位({$jobs['jobs_name']}) 有效期为{$days}天");//记录套餐操作
					}
					exit("ok");
				}
	}
}
?>