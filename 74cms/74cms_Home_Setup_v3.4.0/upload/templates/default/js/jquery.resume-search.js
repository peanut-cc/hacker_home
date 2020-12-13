function allaround(templatedir,dir,jobslisturl,getkey,getarr)
{
	var  href="javascript:void(0);";
	get=getarr.split(",");
	var opthtm='';
	opthtm+='<div class="s1">';
	opthtm+='<div class="litit keysel">关键字：</div>';
	opthtm+='<div class="littxt">';
	opthtm+='<div class="keybox">';
	opthtm+='<div id="searcform" >';
	opthtm+='<div class="keyinputbox">';
	opthtm+='<input name="key" type="text" id="key" maxlength="25" value="'+getkey+'"/>';
	opthtm+='</div>';
	opthtm+='<input name="category" type="hidden" value="'+get[0]+'" />';
	opthtm+='<input name="subclass" type="hidden" value="'+get[1]+'" />';
	opthtm+='<input name="district" type="hidden" value="'+get[2]+'" />';
	opthtm+='<input name="sdistrict" type="hidden" value="'+get[3]+'" />';
	opthtm+='<input name="experience" type="hidden" value="'+get[4]+'" />';
	opthtm+='<input name="education" type="hidden" value="'+get[5]+'" />';
	opthtm+='<input name="sex" type="hidden" value="'+get[6]+'" />';
	opthtm+='<input name="photo" type="hidden" value="'+get[7]+'" />';
	opthtm+='<input name="talent" type="hidden" value="'+get[8]+'" />';
	opthtm+='<input name="inforow" type="hidden" value="'+get[9]+'" />';
	opthtm+='<input name="sort" type="hidden" value="" />';
	opthtm+='<input name="page" type="hidden" value="1" />';
	opthtm+='<div class="subinputbox"><input type="button" name=""  id="soall" value="搜全部" /></div>';
	if (get[0]+get[1]+get[2]+get[3]+get[4]+get[5]+get[6]+get[7]+get[8])
	{
	opthtm+='<div class="subinputbox1"><input type="button" name="" id="socat"  value="搜本类" /></div>';
	}
	opthtm+='</div>';
	opthtm+='</div>	';			
	opthtm+='<div class="keymore link_greenu"><a href="'+href+'" id="advopen">显示更多条件</a></div>';
	opthtm+='<div class="clear"></div>';
	opthtm+='</div>';
	opthtm+='<div class="clear"></div>';
	if (get[2]=='')
	{
		opthtm+='<div class="litit csel">地区：</div>';
		opthtm+='<div class="littxt">';
		opthtm+='<ul class="min">';
		var len=QS_city_parent.length;
		minlen=len>12?11:len;
		for(var i=0;i<minlen;i++)
		{
		arr    =QS_city_parent[i].split(",");
		opthtm+='<li><a href="'+href+'" id="district-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
		}
		if (len>12)
		{
			for(var i=minlen;i<len;i++)
			{
			arr    =QS_city_parent[i].split(",");
			opthtm+='<li class="hide"><a href="'+href+'" id="district-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
			}
			opthtm+='<li class="more"><a href="'+href+'">更多</a></li>';
		}
		opthtm+='</ul>';
		opthtm+='<div class="clear"></div>';
		opthtm+='</div>';
		opthtm+='<div class="clear"></div>';
	}
	else
	{
		var districtname='';
		for(var i=0;i<QS_city_parent.length;i++)
		{
			arr    =QS_city_parent[i].split(",");
			if (arr[0]==get[2])districtname=arr[1];
		}
		opthtm+='<div class="litit csel">地区：</div>';
		opthtm+='<div class="littxt">';
		opthtm+='<ul >';
		if (districtname)
		{
			sdistrictstr=QS_city[get[2]];
			var b="";
			if (sdistrictstr)
			{
				b="<span>▼</span>";
			}
		opthtm+='<li><span  id="district-'+get[2]+'">'+districtname+'</span>'+b+'</li>';
		}
		else
		{
			alert('参数错误');
		}
		opthtm+='</ul>';
		opthtm+='<div class="clear"></div>';
		opthtm+='</div>';	
		opthtm+='<div class="clear"></div>';
		sdistrictstr=QS_city[get[2]];
		if (sdistrictstr)//如果有子类
		{
				if (get[3]=='')
				{
					arrsubclass=sdistrictstr.split("|");
					var len=arrsubclass.length;
					if (len>0)
					{
							opthtm+='<div class="litit csel">地区子类：</div>';
							opthtm+='<div class="littxt">';
							opthtm+='<ul >';		
							minlen=len>12?11:len;
							for(var i=0;i<minlen;i++)
							{
							arr    =arrsubclass[i].split(",");
							opthtm+='<li><a href="'+href+'" id="sdistrict-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
							}
							if (len>12)
							{
								for(var i=minlen;i<len;i++)
								{
								arr    =arrsubclass[i].split(",");
								opthtm+='<li class="hide"><a href="'+href+'" id="sdistrict-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
								}
								opthtm+='<li class="more"><a href="'+href+'">更多</a></li>';
							}
							opthtm+='</ul>';
							opthtm+='<div class="clear"></div>';
							opthtm+='</div>';	
							opthtm+='<div class="clear"></div>';
					}
				}
				else
				{
					arrsubclass=sdistrictstr.split("|");
					for(var i=0;i<arrsubclass.length;i++)
					{
						arr    =arrsubclass[i].split(",");
						if (arr[0]==get[3])subclassname=arr[1];
					}
					if(subclassname)
					{
						opthtm+='<div class="litit csel">地区子类：</div>';
						opthtm+='<div class="littxt">';
						opthtm+='<ul >';
						opthtm+='<li><span  id="sdistrict-'+get[3]+'">'+subclassname+'</span></li>';
						opthtm+='</ul>';
						opthtm+='<div class="clear"></div>';
						opthtm+='</div>';	
						opthtm+='<div class="clear"></div>';
					}
				}
		}
	}
	if (get[0]=='')
	{
		opthtm+='<div class="litit csel">职位：</div>';
		opthtm+='<div class="littxt">';
		opthtm+='<ul >';
		var len=QS_jobs_parent.length;
		minlen=len>12?11:len;
		for(var i=0;i<minlen;i++)
		{
		arr    =QS_jobs_parent[i].split(",");
		opthtm+='<li><a href="'+href+'" id="category-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
		}
		if (len>12)
		{
			for(var i=minlen;i<len;i++)
			{
			arr    =QS_jobs_parent[i].split(",");
			opthtm+='<li class="hide"><a href="'+href+'" id="category-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
			}
			opthtm+='<li class="more"><a href="'+href+'">更多</a></li>';
		}
		opthtm+='</ul>';
		opthtm+='<div class="clear"></div>';
		opthtm+='</div>';	
		opthtm+='<div class="clear"></div>';
	}
	else
	{
		var categoryname='';
		for(var i=0;i<QS_jobs_parent.length;i++)
		{
			arr    =QS_jobs_parent[i].split(",");
			if (arr[0]==get[0])categoryname=arr[1];
		}
		opthtm+='<div class="litit csel">职位：</div>';
		opthtm+='<div class="littxt">';
		opthtm+='<ul >';
		if (categoryname)
		{
			subclassstr=QS_jobs[get[0]];
			var b="";
			if (subclassstr)
			{
				b="<span>▼</span>";
			}
		opthtm+='<li><span  id="category-'+get[0]+'">'+categoryname+'</span>'+b+'</li>';
		}
		else
		{
			alert('参数错误');
		}
		opthtm+='</ul>';
		opthtm+='<div class="clear"></div>';
		opthtm+='</div>';	
		opthtm+='<div class="clear"></div>';
		subclassstr=QS_jobs[get[0]];
		if (subclassstr)//如果有子类
		{
				if (get[1]=='')
				{
					arrsubclass=subclassstr.split("|");
					var len=arrsubclass.length;
					if (len>0)
					{
							opthtm+='<div class="litit csel">职位子类：</div>';
							opthtm+='<div class="littxt">';
							opthtm+='<ul >';		
							minlen=len>12?11:len;
							for(var i=0;i<minlen;i++)
							{
							arr    =arrsubclass[i].split(",");
							opthtm+='<li><a href="'+href+'" id="subclass-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
							}
							if (len>12)
							{
								for(var i=minlen;i<len;i++)
								{
								arr    =arrsubclass[i].split(",");
								opthtm+='<li class="hide"><a href="'+href+'" id="subclass-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
								}
								opthtm+='<li class="more"><a href="'+href+'">更多</a></li>';
							}
							opthtm+='</ul>';
							opthtm+='<div class="clear"></div>';
							opthtm+='</div>';	
							opthtm+='<div class="clear"></div>';
					}
				}
				else
				{
					var subclassname='';
					arrsubclass=subclassstr.split("|");
					for(var i=0;i<arrsubclass.length;i++)
					{
						arr    =arrsubclass[i].split(",");
						if (arr[0]==get[1])subclassname=arr[1];
					}
					if(subclassname)
					{
						opthtm+='<div class="litit csel">职位子类：</div>';
						opthtm+='<div class="littxt">';
						opthtm+='<ul >';
						opthtm+='<li><span  id="subclass-'+get[1]+'">'+subclassname+'</span></li>';
						opthtm+='</ul>';
						opthtm+='<div class="clear"></div>';
						opthtm+='</div>';	
						opthtm+='<div class="clear"></div>';
					}
				}
		}
	}
	opthtm+='<div class="litit csel">工作经验：</div>';
	opthtm+='<div class="littxt min">';
	opthtm+='<ul class="min">';	
	if (get[4]=='')
	{
		for(var i=0;i<QS_experience.length;i++)
		{
		arr    =QS_experience[i].split(",");
		opthtm+='<li><a href="'+href+'" id="experience-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
		}
	}
	else
	{
		var name='';
		for(var i=0;i<QS_experience.length;i++)
		{
			arr    =QS_experience[i].split(",");
			if (arr[0]==get[4]) name=arr[1];
		}
		if (name)
		{
		opthtm+='<li><span  id="experience-'+get[4]+'">'+name+'</span></li>';	
		}
		else
		{
			alert('参数错误');
		}
	}
	opthtm+='</ul>';
	opthtm+='<div class="clear"></div>';
	opthtm+='</div>';
	opthtm+='<div class="clear"></div>';	  
	opthtm+='<div class="advbox" id="advbox">';
	opthtm+='<div class="litit csel">学历：</div>';
	opthtm+='<div class="littxt">';
	opthtm+='<ul >';
	if (get[5]=='')
	{
		for(var i=0;i<QS_education.length;i++)
		{
		arr    =QS_education[i].split(",");
		opthtm+='<li><a href="'+href+'" id="education-'+arr[0]+'"  class="opt">'+arr[1]+'</a></li>';
		}
	}
	else
	{
		var name='';
		for(var i=0;i<QS_education.length;i++)
		{
			arr    =QS_education[i].split(",");
			if (arr[0]==get[5]) name=arr[1];
		}
		if (name)
		{
		opthtm+='<li><span  id="education-'+get[5]+'">'+name+'</span></li>';	
		}
		else
		{
			alert('参数错误');
		}
	}
	opthtm+='</ul>';
	opthtm+='<div class="clear"></div>';
	opthtm+='</div>';
	opthtm+='<div class="clear"></div>';
	opthtm+='<div class="litit csel">性别：</div>';
	opthtm+='<div class="littxt min">';
	opthtm+='<ul class="min">';
	if (get[6]=='')
	{
	opthtm+='<li><a href="'+href+'" id="sex-1"  class="opt">男</a></li>';
	opthtm+='<li><a href="'+href+'" id="sex-2"  class="opt">女</a></li>';
	}
	else
	{
		var name=get[6]=="1"?"男":"女";
	opthtm+='<li><span  id="sex-'+get[6]+'">'+name+'</span></li>';
	}
	opthtm+='</ul>';
	opthtm+='<div class="clear"></div>';
	opthtm+='</div>';
	opthtm+='<div class="clear"></div>';
	opthtm+='<div class="litit csel">照片：</div>';
	opthtm+='<div class="littxt min">';
	opthtm+='<ul class="min">';
	if (get[7]=='')
	{
	opthtm+='<li><a href="'+href+'" id="photo-1"  class="opt">有</a></li>';
	opthtm+='<li><a href="'+href+'" id="photo-0"  class="opt">无</a></li>';
	}
	else
	{
		var name=get[7]=="1"?"有":"无";
	opthtm+='<li><span  id="photo-'+get[7]+'">'+name+'</span></li>';
	}
	opthtm+='</ul>';
	opthtm+='<div class="clear"></div>';
	opthtm+='</div>';
	opthtm+='<div class="clear"></div>';
	opthtm+='<div class="litit csel">等级：</div>';
	opthtm+='<div class="littxt min">';
	opthtm+='<ul class="min">';
	if (get[8]=='')
	{
	opthtm+='<li><a href="'+href+'" id="talent-1"  class="opt">普通</a></li>';
	opthtm+='<li><a href="'+href+'" id="talent-2"  class="opt">高级</a></li>';
	}
	else
	{
		var name=get[8]=="1"?"普通":"高级";
	opthtm+='<li><span  id="talent-'+get[8]+'">'+name+'</span></li>';
	}
	opthtm+='</ul>';
	opthtm+='<div class="clear"></div>';
	opthtm+='</div>';
	opthtm+='<div class="clear"></div>';
	opthtm+='</div>';
	opthtm+='<div class="bottomheight"></div>';
	opthtm+='<div class="myselbox" id="myselbox"><div class="left">已选条件：</div><div class="optcentet"></div><div class="right"><div class="clearoptall"><a  href="'+href+'" class="clearall">清除所有</a></div></div><div class="clear"></div>';
	opthtm+='</div>';
	$("#resumesearchbox").html(opthtm);
	//打开更多选项
	$(".more a").click(function ()
	{
		if ($(this).parent().prev().css('display')=='none')
		{
			$(this).parent().prevAll('.hide').css("display",'block');
			$(this).html('收起').blur();
		}
		else
		{
			$(this).parent().prevAll('.hide').css("display",'none');
			$(this).html('更多').blur();
		}
	
	});	
	//显示已选
	 var patrn=/^(请输入：技能，)/i; 
		if (patrn.exec(getkey))
		{
			getkey='';
		}
	var selopt=getkey+get[0]+get[1]+get[2]+get[3]+get[4]+get[5]+get[6]+get[7]+get[8];
	if (selopt!='')
	{
		selbox=$("#myselbox .optcentet");
		if (getkey)	{
		selbox.append('<a href="'+href+'" class="clearopt" id="key-'+getkey+'" title="点击取消"><u>关键字:</u>'+getkey+'</a>');
		}
		if (get[2])	{
		var optval=$('#district-'+get[2]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="district-'+get[2]+'" title="点击取消"><u>地区:</u>'+optval+'</a>');
		}
		if (get[3])	{
		var optval=$('#sdistrict-'+get[3]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="sdistrict-'+get[3]+'" title="点击取消"><u>地区子类:</u>'+optval+'</a>');
		}
		if (get[0])	{
		var optval=$('#category-'+get[0]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="category-'+get[0]+'" title="点击取消"><u>职位:</u>'+optval+'</a>');
		}
		if (get[1])	{
		var optval=$('#subclass-'+get[1]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="subclass-'+get[1]+'" title="点击取消"><u>职位子类:</u>'+optval+'</a>');
		}
		if (get[4])	{
		var optval=$('#experience-'+get[4]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="experience-'+get[4]+'" title="点击取消"><u>工作经验:</u>'+optval+'</a>');
		}
		if (get[5])	{
		var optval=$('#education-'+get[5]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="education-'+get[5]+'" title="点击取消"><u>学历:</u>'+optval+'</a>');
		}
		if (get[6])	{
		var optval=$('#sex-'+get[6]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="sex-'+get[6]+'" title="点击取消"><u>性别:</u>'+optval+'</a>');
		}
		if (get[7])	{
		var optval=$('#photo-'+get[7]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="photo-'+get[7]+'" title="点击取消"><u>照片:</u>'+optval+'</a>');
		}
		if (get[8])	{
		var optval=$('#talent-'+get[8]).html();
		selbox.append('<a href="'+href+'" class="clearopt" id="talent-'+get[8]+'" title="点击取消"><u>等级:</u>'+optval+'</a>');
		}
		selbox.append('<div class="clear"></div>');
		$("#jobsearchbox").css('padding-bottom',0);
		$("#myselbox").show();
		//删除单个
		$(".clearopt").click(function () {
		var patrn=/^(请输入：技能，)/i; 
	 	var key=$("#key").val();
		if (patrn.exec(key)) $("#key").val('');
			var opt=$(this).attr('id');
			opt=opt.split("-");
		$("#searcform input[name="+opt[0]+"]").val('');
		if (opt[0]=="category") $("#searcform input[name='subclass']").val('');//取消大类，同时取消子类
		if (opt[0]=="district") $("#searcform input[name='sdistrict']").val('');//取消大类，同时取消子类
		flag=false;
		setTimeout(function() {
		search_location();
		}, 1);
		});
		//删除所有
		$(".clearall").click(function () {
		$("#searcform input[type='hidden']").val('');
		$("#searcform input[name='key']").val('');
		setTimeout(function() {
		search_location();
		}, 1);
		});	
	}
	//是否显示更多条件
	if (get[5]+get[6]+get[7]+get[8])
	{
		$("#advbox").show();
	}
	//更多条件
	if($("#advbox").css('display')=='none')
	{
		$(".keymore").css('background-position','40px -64px');
		$(".keymore a").html("显示更多条件");
	}
	else
	{
		$(".keymore").css('background-position','40px -98px');
		$(".keymore a").html("隐藏更多条件");
	}
	//打开更多条件
	$("#advopen").click(function () {
	if($("#advbox").css('display')=='none')
	{
		$(".keymore").css('background-position','40px -98px');
		$(".keymore a").html("隐藏更多条件");
	}
	else
	{
		$(".keymore").css('background-position','40px -64px');
		$(".keymore a").html("显示更多条件");
	}
	$("#advbox").slideToggle(80);
	});	
	//
	var flag=true;
	//从项目选择
	$(".opt").click(function (){
	var patrn=/^(请输入：技能，)/i; 
	 	var key=$("#key").val();
		if (patrn.exec(key)) $("#key").val('');
	var opt=$(this).attr('id');
	    opt=opt.split("-");
	$("#searcform input[name="+opt[0]+"]").val(opt[1]);
	flag=false;
	setTimeout(function() {
	search_location();
	}, 1);
	});
	//搜本类
	$("#socat").click(function () {
		//搜本类
	 	flag=false;
		setTimeout(function() {
		search_location();
		}, 1);
	});
	

	
	$("#soall").click( function () {
		if (flag)//搜索全部，清除选择
		{			
			$("#searcform input[type='hidden']").val('');
		}
		var patrn=/^(请输入：技能，)/i; 
	 	var key=$("#key").val();
		if (patrn.exec(key))
		{
			$("#key").val('');
		}
		search_location();
	});
	
	$("#key").focus(function(){
		var patrn=/^(请输入：技能，)/i; 
	 	var key=$("#key").val();
		if (patrn.exec(key))
		{
		$("#key").css('color','').val('');
		}  
	});
	$('#searcform input[name=key]').keydown(function(e){
	if(e.keyCode==13){
   search_location()
	}
	});
	function search_location()
	{
		$("body").append('<div id="pageloadingbox">页面加载中....</div><div id="pageloadingbg"></div>');
		$("#pageloadingbg").css("opacity", 0.5);
		var key=$("#searcform input[name=key]").val();
		var category=$("#searcform input[name=category]").val();
		var subclass=$("#searcform input[name=subclass]").val();
		var district=$("#searcform input[name=district]").val();
		var sdistrict=$("#searcform input[name=sdistrict]").val();
		var experience=$("#searcform input[name=experience]").val();
		var education=$("#searcform input[name=education]").val();
		var sex=$("#searcform input[name=sex]").val();
		var photo=$("#searcform input[name=photo]").val();
		var talent=$("#searcform input[name=talent]").val();
		var inforow=$("#searcform input[name=inforow]").val();
		var sort_1=$("#searcform input[name=sort]").val();
		var page=$("#searcform input[name=page]").val();
		$.get(dir+"plus/ajax_search_location.php", {"act":"QS_resumelist","key":key,"category":category,"subclass":subclass,"district":district,"sdistrict":sdistrict,"experience":experience,"education":education,"sex":sex,"photo":photo,"talent":talent,"inforow":inforow,"sort":sort_1,"page":page},
			function (data,textStatus)
			 {
				 window.location.href=data;
			 },"text"
		);
	}
}