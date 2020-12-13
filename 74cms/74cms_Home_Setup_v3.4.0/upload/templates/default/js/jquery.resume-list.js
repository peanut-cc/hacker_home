function resumelist()
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
	$("input[name='selectall']").unbind().click(function(){$("#formresumelist :checkbox").attr('checked',$(this).attr('checked'))});
	//点击选择后重新加样式
	$("#formresumelist input[type='checkbox']").click(function(){setlistbg();});
}
function recommend_resume(ajaxurl)
{
	$(".recommend").unbind().click(function()
	{
		dialog("推荐给好友","url:get?"+ajaxurl+"plus/ajax_recommend.php?resume="+$(this).attr("url")+"&act=recommendresume","400px","auto","");
	});
}
function favorites(ajaxurl)
{
	$(".add_favorites").unbind().click(function(){	
	var url=ajaxurl+"user/user_favorites_resume.php?id="+$(this).attr("id")+"&act=add";
	dialog("添加到人才库","url:get?"+url,"500px","auto","");	
	});	
	
	$(".allfavorites").click(function()
	{
		var sltlength=$("#formresumelist input:checked").length;
		if ($("#formresumelist .list input:checked").length==0)
			{
			dialog("系统提示","text:请选择简历","300px","auto");
			}
			else
			{
				var jidArr=new Array();
				 $("#formresumelist .list :checkbox[checked]").each(function(index){jidArr[index]=$(this).val();});
				dialog("加入收藏","url:get?"+ajaxurl+"user/user_favorites_resume.php?id="+jidArr.join("-")+"&act=add","500px","auto","");
			}
	});
}