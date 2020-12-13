<?php
 /*
 * 74cms 邀请面试
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
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'invited';
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
	exit("必须是企业会员才可以邀请面试！");
}
		require_once(QISHI_ROOT_PATH.'include/fun_company.php');
		$user=get_user_info($_SESSION['uid']);
		if ($user['status']=="2") 
		{
			$str="<a href=\"".get_member_url(1,true)."company_user.php?act=user_status\">[设置帐号状态]</a>";
			exit("您的账号处于暂停状态，请先设为正常后进行操作！".$str);
		}
$id=isset($_GET['id'])?intval($_GET['id']):exit("err");
$user_jobs=get_auditjobs($_SESSION['uid']);
if (count($user_jobs)==0)
{
	exit("邀请失败，你没有发布招聘信息或者信息没有审核通过！");
}
$setmeal=get_user_setmeal($_SESSION['uid']);
$resume=$db->getone("select * from ".table('resume')." WHERE id ='{$id}'  LIMIT 1");
if ($_CFG['operation_mode']=="2")
{
 			if (empty($setmeal) || ($setmeal['endtime']<time() && $setmeal['endtime']<>"0"))
			{
				$str="<a href=\"".get_member_url(1,true)."company_service.php?act=setmeal_list\">[申请服务]</a>";
				exit("您的服务已到期。您可以 {$str}");
			}
			elseif ($resume['talent']=='2' && $setmeal['interview_senior']<=0)
			{
				$str="<a href=\"".get_member_url(1,true)."company_service.php?act=setmeal_list\">[申请服务]</a>";
				exit("你邀请高级人才面试次数已经超出了限制。您可以{$str}");
			}
			elseif ($resume['talent']=='1' && $setmeal['interview_ordinary']<=0)
			{
				$str="<a href=\"".get_member_url(1,true)."company_service.php?act=setmeal_list\">[申请服务]</a>";
				exit("你邀请面试次数已经超出了限制。您可以{$str}");
			}
}
if ($act=="invited")
{			
	if ($_CFG['operation_mode']=="2")
	{
		if ($resume['talent']=='2')
		{	
			$tip="提示：您还可以邀请<span> {$setmeal['interview_senior']}</span> 次高级人才面试";
		}
		else
		{	
			$tip="提示：您还可以邀请<span> {$setmeal['interview_ordinary']}</span> 次普通人才面试";
		}
	}
	elseif($_CFG['operation_mode']=="1")
	{
				$mypoints=get_user_points($_SESSION['uid']);
				$points_rule=get_cache('points_rule');
				$points=$resume['talent']=='2'?$points_rule['interview_invite_advanced']['value']:$points_rule['interview_invite']['value'];
				if  ($mypoints<$points)
				{
					$str="<a href=\"".get_member_url(1,true)."company_service.php?act=order_add\">[充值{$_CFG['points_byname']}]</a>&nbsp;&nbsp;&nbsp;&nbsp;";
 					exit("你的 {$_CFG['points_byname']} 不足，请充值后下载。".$str);
				}
				$tip="邀请面试将扣除<span> {$points} </span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}，您目前共有<span> {$mypoints}</span>{$_CFG['points_quantifier']}{$_CFG['points_byname']}";
	}
?>
<script type="text/javascript">
$(".but100").hover(function(){$(this).addClass("but100_hover")},function(){$(this).removeClass("but100_hover")});
$("#but_invited").click(function() 
{
		var id="<?php echo $id?>";
		if ($("#ajax_invited_table :radio[checked]").length==0)
		{
		alert("请选择面试职位！");
		}
		else
		{
			$("#but_invited").val("处理中...");
			$("#but_invited").attr("disabled","disabled");
			var tsTimeStamp= new Date().getTime();
			$("#ajax_download_r").attr("disabled","disabled");
 			 var pms_notice=$("#pms_notice").attr("checked");
			 if(pms_notice) pms_notice=1;else pms_notice=0;
			$.get("<?php echo $_CFG['site_dir'] ?>user/user_invited.php", {"jobs_id": $("#ajax_invited_table :radio[checked]").val(),"id":id,"notes":$("#notes").val(),"pms_notice":pms_notice,"time":tsTimeStamp,"act":"invited_save"},
 			function (data,textStatus)
			 {
				if (data=="ok")
				{
				$(".ajax_invited_tip").hide();
				$("#ajax_invited_table").hide();
				$("#invited_ok").show();				 
						$("#invited_ok .closed").click(function(){
							DialogClose();
						});
				}
				else if (data=="repeat")
				{
				$(".ajax_invited_tip").show();
				$("#ajax_invited_table").show();
				$("#invited_ok").hide();
				alert("此岗位已经邀请过此人了，不能重复邀请!");
				}
				else
				{
				$(".ajax_invited_tip").show();
				$("#ajax_invited_table").show();
				$("#invited_ok").hide();
				alert(data);
				}
				$("#but_invited").val("提交");
				$("#but_invited").attr("disabled","");
			 })
		}	 
});
function DialogClose()
{
	$("#FloatBg").hide();
	$("#FloatBox").hide();
}
</script>
<div class="ajax_invited_tip"><?php echo $tip?></div>

<table width="100%" border="0" cellspacing="5" cellpadding="2" id="ajax_invited_table">
  <tr>
    <td width="100" align="right">面试职位：</td>
    <td>
	<?php 
	foreach ($user_jobs as $list)
	{
	?>
	<label> <input name="jobs_id" type="radio"  id="jobs_id" value="<?php echo $list['id']?>" /><?php echo $list['jobs_name']?></label><br />
	<?php
	}
	?>
	</td>
  </tr>
  <tr>
    <td width="100" align="right">其他描述：</td>
    <td>
	<textarea name="notes" id="notes" style="width:300px; height:80px;"></textarea>
          <br />
        其他说明填写：面试携带证件，联系人，乘车路线等...<br />
	
	</td>
  </tr>
		
 <tr>
    <td align="right" >站内信通知对方：</td>
    <td>
		  <label><input type="checkbox" name="pms_notice" id="pms_notice" value="1"  checked="checked"/>
		  站内信通知
		   </label>
	</td>
  </tr>
   <tr>
    <td align="right">&nbsp;</td>
    <td><input type="button" name="Submit2"    class="but100" value="提交"  id="but_invited"/></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td><a href="<?php echo get_member_url(1,true)?>company_recruitment.php?act=interview_lsit" target="_blank">查看我发起的面试邀请</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo get_member_url(1)?>" >进入会员中心</a></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="8" cellpadding="0" id="invited_ok"  style="display:none">
  <tr>
    <td width="80" height="120" align="right" valign="top"><img src="<?php echo  $_CFG['site_template']?>images/13.gif" /></td>
    <td>
	<strong style=" font-size:14px ; color:#0066CC;">邀请成功!</strong>
	<div style="border-top:1px #CCCCCC solid; line-height:180%; margin-top:10px; padding-top:10px; height:100px;" >
	<a href="<?php echo get_member_url(1,true)?>company_recruitment.php?act=interview_lsit" >查看我发起的面试邀请</a><br />

	<a href="javascript:void(0)"  class="DialogClose">邀请完成</a>
	
	</div>
	</td>
  </tr>
</table>
<?php
}
 elseif ($act=="invited_save")
{
	$jobs_id=isset($_GET['jobs_id'])?intval($_GET['jobs_id']):exit("err");
	$notes=isset($_GET['notes'])?trim($_GET['notes']):"";
	$pms_notice=intval($_GET['pms_notice']);
	if (check_interview($id,$jobs_id,$_SESSION['uid']))
	{
	exit("repeat");
	}
	$jobs=get_jobs_one($jobs_id);
	$addarr['resume_id']=$resume['id'];
	$addarr['resume_addtime']=$resume['addtime'];
	if ($resume['display_name']=="2")
	{
	$addarr['resume_name']="N".str_pad($resume['id'],7,"0",STR_PAD_LEFT);	
	}
	elseif ($resume['display_name']=="3")
	{
	$addarr['resume_name']=cut_str($resume['fullname'],1,0,"**");
	}
	else
	{
	$addarr['resume_name']=$resume['fullname'];
	}
	$addarr['resume_uid']=$resume['uid'];
	$addarr['company_id']=$jobs['company_id'];
	$addarr['company_addtime']=$jobs['company_addtime'];
	$addarr['company_name']=$jobs['companyname'];
	$addarr['company_uid']=$_SESSION['uid'];
	$addarr['jobs_id']=$jobs['id'];
	$addarr['jobs_name']=$jobs['jobs_name'];
	$addarr['jobs_addtime']=$jobs['addtime'];	
	$addarr['notes']= $notes;
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
		$addarr['notes']=utf8_to_gbk($addarr['notes']);
	}
	$addarr['personal_look']= 1;
	$addarr['interview_addtime']=time();
	$user=get_user_info($resume['uid']);
	$resume_user=get_user_info($resume['uid']);
	if ($_CFG['operation_mode']=="2")
	{
		inserttable(table('company_interview'),$addarr);
		if ($resume['talent']=='2')
		{
			action_user_setmeal($_SESSION['uid'],"interview_senior");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"邀请了 {$resume_user['username']} 面试，还可以邀请高级人才 {$setmeal['interview_senior']} 次");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"邀请了 {$resume_user['username']} 面试");
		}
		else
		{				 
			action_user_setmeal($_SESSION['uid'],"interview_ordinary");
			$setmeal=get_user_setmeal($_SESSION['uid']);
			write_memberslog($_SESSION['uid'],1,9002,$_SESSION['username'],"邀请了 {$resume_user['username']} 面试，还可以邀请普通人才 {$setmeal['interview_ordinary']} 次");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"邀请了 {$resume_user['username']} 面试");				
		}			
	}		 
	elseif($_CFG['operation_mode']=="1")
	{
		$mypoints=get_user_points($_SESSION['uid']);
		$points_rule=get_cache('points_rule');
		$points=$resume['talent']=='2'?$points_rule['interview_invite_advanced']['value']:$points_rule['interview_invite']['value'];
		$ptype=$resumeshow['talent']=='2'?$points_rule['interview_invite_advanced']['type']:$points_rule['interview_invite']['type'];
		if  ($mypoints<$points)
		{
			exit("err");
		}
		inserttable(table('company_interview'),$addarr);
		if ($points>0)
		{
			report_deal($_SESSION['uid'],$ptype,$points);
			$user_points=get_user_points($_SESSION['uid']);
			$operator=$ptype=="1"?"+":"-";
			write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username'],"邀请 {$resume_user['username']} 面试({$operator}{$points}),(剩余:{$user_points})");
			write_memberslog($_SESSION['uid'],1,6001,$_SESSION['username'],"邀请 {$resume_user['username']} 面试");
		}		
	}
	$mailconfig=get_cache('mailconfig');
	$sms=get_cache('sms_config');
	if ($mailconfig['set_invite']=="1" && $resume['email_notify']=='1' && $resume_user['email_audit']=="1")
	{
		dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_mail.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&email={$resume_user['email']}");				
	}
	//sms
	if ($sms['open']=="1"  && $sms['set_invite']=="1"  && $resume_user['mobile_audit']=="1")
	{
		dfopen("{$_CFG['site_domain']}{$_CFG['site_dir']}plus/asyn_sms.php?uid={$_SESSION['uid']}&key=".asyn_userkey($_SESSION['uid'])."&act=set_invite&companyname={$jobs['companyname']}&mobile={$resume_user['mobile']}");		
	}
	//站内信
	if($pms_notice=='1'){
		$user=$db->getone("select username from ".table('members')." where uid ={$resume['uid']} limit 1");
		$jobs_url=url_rewrite('QS_jobsshow',array('id'=>$jobs['id']));
		$company_url=url_rewrite('QS_companyshow',array('id'=>$jobs['company_id']));
		$message=$jobs['companyname']."邀请您参加公司面试，面试职位：<a href=\"{$jobs_url}\" target=\"_blank\"> {$jobs['jobs_name']} </a>，<a href=\"{$company_url}\" target=\"_blank\">点击查看公司详情</a>";
		write_pmsnotice($resume['uid'],$user['username'],$message);
	}
	exit("ok");
}
 
?>