function jobslist()
{
	var key=$('#key').val();
	if (key)
	{
		key_arr=key.split(" ");
		for (x in key_arr)
		{
		if (key_arr[x]) $('.striking').highlight(key_arr[x]);
		}
	}
	$(".list:odd").css("background-color","#F4F5FB");
	$(".titsub").hover(	function()	{$(this).addClass("titsub_h");},function(){$(this).removeClass("titsub_h");});
	function setlistbg()
	{
			$(".li_left_check input[type='checkbox']").each(function(i){
				if ($(this).attr("checked"))
				{
					$(this).parent().parent().addClass("seclect");
				}
				else
				{
					$(this).parent().parent().removeClass("seclect");
				}
			}); 
	 }
 	//全选反选
	$("input[name='selectall']").unbind().click(function(){$("#formjobslist :checkbox").attr('checked',$(this).attr('checked'))});
	//点击选择后重新加样式
	$("#formjobslist input[type='checkbox']").click(function(){setlistbg();});
	//提醒
	$(function(){
	var _wrap=$('#jobs_list_tip ul');
	var _interval=3000;
	var _moving;
	_wrap.hover(function(){
	clearInterval(_moving);
	},function(){
	_moving=setInterval(function(){
	var _field=_wrap.find('li:first');
	var _h=_field.height();
	_field.animate({marginTop:-_h+'px'},300,function(){
	_field.css('marginTop',0).appendTo(_wrap);
	})
	},_interval)
	}).trigger('mouseleave');
	});
}
//对比职位
function jobscontrast(dir)
{
	$(".contrast").click(function()
	{
			var sltlength=$("#formjobslist .list input:checked").length;
			if (sltlength>4 || sltlength<2)
			{
			dialog("系统提示","text:请选择 2-4个职位进行对比","300px","auto");
			}
			else
			{
			var jidArr=new Array();
			 $("#formjobslist .list :checkbox[checked]").each(function(index){jidArr[index]=$(this).val();});
			 
			 $("body").append('<div id="pageloadingbox">页面加载中....</div><div id="pageloadingbg"></div>');
			$("#pageloadingbg").css("opacity", 0.5);
			var id=jidArr.join("-");
			$.get(dir+"plus/ajax_search_location.php", {"act":"QS_jobscontrast","id":id},
			function (data,textStatus)
			 {
				 window.location.href=data;
			 },"text"
		);
			}
	});
}
 //申请
function apply_jobs(ajaxurl)
{
	$(".deliver").click(function()
	{
			var sltlength=$("#formjobslist input:checked").length;
			if ($("#formjobslist .list input:checked").length==0)
			{
			dialog("系统提示","text:请选择职位","300px","auto");
			}
			else
			{
				var jidArr=new Array();
				 $("#formjobslist .list :checkbox[checked]").each(function(index){jidArr[index]=$(this).val();});
				dialog("投简历","url:get?"+ajaxurl+"user/user_apply_jobs.php?id="+jidArr.join("-")+"&act=app","500px","auto","");
			}
	});
	//单个申请职位
	$(".app_jobs").unbind().click(function(){
	dialog("投简历","url:get?"+ajaxurl+"user/user_apply_jobs.php?id="+$(this).attr("id")+"&act=app","500px","auto","");
	});
}
function recommend_jobs(ajaxurl)
{
	$(".recommend").unbind().click(function()
	{
 		dialog("推荐给好友","url:get?"+ajaxurl+"plus/ajax_recommend.php?job="+$(this).attr("url")+"&act=recommendjobs","400px","auto","");
	});
}
function favorites(ajaxurl)
{
	$(".add_favorites").unbind().click(function(){
	dialog("加入收藏","url:get?"+ajaxurl+"user/user_favorites_job.php?id="+$(this).attr("id")+"&act=add","500px","auto","");
	});
	$(".collect").click(function()
	{
		var sltlength=$("#formjobslist input:checked").length;
		if ($("#formjobslist .list input:checked").length==0)
			{
			dialog("系统提示","text:请选择职位","300px","auto");
			}
			else
			{
				var jidArr=new Array();
				 $("#formjobslist .list :checkbox[checked]").each(function(index){jidArr[index]=$(this).val();});
				dialog("加入收藏","url:get?"+ajaxurl+"user/user_favorites_job.php?id="+jidArr.join("-")+"&act=add","500px","auto","");
			}
	});
}