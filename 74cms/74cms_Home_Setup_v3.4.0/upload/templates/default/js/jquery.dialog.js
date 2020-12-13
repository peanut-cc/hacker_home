var dialogFirst=true;
function dialog(title,content,width,height,cssName){
if(dialogFirst==true){
  var temp_float=new String;
  temp_float="<div class=\"FloatBg\"  style=\"height:"+$(document).height()+"px;width:"+$(document).width()+"px;filter:alpha(opacity=0);opacity:0;\"></div>";
  temp_float+="<div class=\"FloatBox\">";
  temp_float+="<div class=\"Box\">";
  temp_float+="<div class=\"title\"><h4></h4><span class=\"DialogClose\" title=\"¹Ø±Õ\"></span></div>";
  temp_float+="<div class=\"content link_lan\"><div class=\"wait\"></div></div>";
  temp_float+="</div></div>";
  $("body").append(temp_float);
  $(".FloatBox").css("width","200");
  dialogFirst=false;
}
$('.DialogClose').live('click', function() {
	$(".FloatBg").hide();
	$(".FloatBox").hide();
});
$(".FloatBox .title h4").html(title);
contentType=content.substring(0,content.indexOf(":"));
content=content.substring(content.indexOf(":")+1,content.length);
switch(contentType){
  case "url":
  var content_array=content.split("?");
  $.ajax({
    type:content_array[0],
    url:content_array[1],
    data:content_array[2],
	error:function(){
	  $(".FloatBox .content").html("error...");
	},
    success:function(html){
   $(".FloatBox .content").html(html);
    }
  });
  break;
  case "text":
  $(".FloatBox .content").html(content);
  break;
  case "id":
  $(".FloatBox .content").html($("#"+content+"").html());
  break;
  case "iframe":
  $(".FloatBox .content").html("<iframe src=\""+content+"\" width=\"100%\" height=\""+(parseInt(height)-30)+"px"+"\" scrolling=\"auto\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\"></iframe>");
}

$(".FloatBg").show().css("opacity", 0.1);
if (cssName)
{
	$(".FloatBox").attr("class",cssName);
}
$(".FloatBox").css({display:"block",left:(($(document).width())/2-(parseInt(width)/2))+"px",top:($(document).scrollTop()+120)+"px",width:width,height:height});
$(".FloatBox .title .DialogClose").hover(function(){$(this).addClass("spanhover")},function(){$(this).removeClass("spanhover")});
}