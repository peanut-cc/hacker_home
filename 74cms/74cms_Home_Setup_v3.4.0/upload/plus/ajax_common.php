<?php
 /*
 * 74cms ajax
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
if ($act=="countinfo")
{
	$total="SELECT COUNT(*) AS num FROM ".table('company_profile');
	$total_company=$db->get_total($total);
	$total="SELECT COUNT(*) AS num FROM ".table('jobs');
	$total_jobs=$db->get_total($total);
	$total="SELECT COUNT(*) AS num FROM ".table('resume');
	$total_resume=$db->get_total($total);
	$total="SELECT COUNT(*) AS num FROM ".table('members');
	$total_members=$db->get_total($total);
		$tpl='../templates/'.$_CFG['template_dir']."plus/count.htm";
		$contents=file_get_contents($tpl);
		$contents=str_replace('{#$total_company#}',$total_company,$contents);
		$contents=str_replace('{#$total_jobs#}',$total_jobs,$contents);
		$contents=str_replace('{#$total_resume#}',$total_resume,$contents);
		$contents=str_replace('{#$total_members#}',$total_members,$contents);
		$contents=str_replace('{#$site_name#}',$_CFG['site_name'],$contents);
		$contents=str_replace('{#$user_url#}',url_rewrite('QS_login'),$contents);
		$contents=str_replace('{#$site_template#}',$_CFG['site_template'],$contents);
		$contents = ereg_replace("\t","",$contents); 
		$contents = ereg_replace("\r\n","",$contents); 
		$contents = ereg_replace("\r","",$contents); 
		$contents = ereg_replace("\n","",$contents);
		exit($contents);
}
elseif ($act=="dynamic")
{
		$result = $db->query("SELECT id,jobs_name,addtime,companyname,company_id,company_addtime FROM ".table('jobs')." ORDER BY id DESC LIMIT 3");
		while($row = $db->fetch_array($result))
		{
		$html[]="<li><a href=".url_rewrite('QS_companyshow',array('id'=>$row['company_id']))." target=\"_blank\">".cut_str($row['companyname'],5,0,"...")."</a> 发布了职位:<a href=".url_rewrite('QS_jobsshow',array('id'=>$row['id']))." target=\"_blank\">".$row['jobs_name']."</a></li>";
		}
		$result= $db->query("SELECT id,display_name,fullname,addtime FROM ".table('resume')."  ORDER BY id DESC LIMIT 3");
		while($row = $db->fetch_array($result))
		{
		if ($row['display_name']=="2")
		{
		$row['fullname']="N".str_pad($row['id'],7,"0",STR_PAD_LEFT);
		}
		elseif ($row['display_name']=="3")
		{
		$row['fullname']=cut_str($row['fullname'],1,0,"**");
		}
		$html[]="<li><a href=".url_rewrite('QS_resumeshow',array('id'=>$row['id']))." target=\"_blank\">".$row['fullname']." </a> 发布了简历</li>";
		}
		if (is_array($html) && shuffle($html))
		{
 		exit("<ul>".implode("",$html)."</ul>");
		}		
}
elseif ($act=="company_down_resume")
{
	$result = $db->query("SELECT a.down_addtime,b.companyname,b.id AS bid,b.addtime AS baddtime,c.id AS cid,c.display_name,c.addtime AS caddtime,c.fullname FROM ".table('company_down_resume')." AS a LEFT JOIN  ".table('company_profile')." AS b ON a.company_uid=b.uid  LEFT JOIN ".table('resume')." AS c ON a.resume_id=c.id ORDER BY a.down_addtime DESC  LIMIT 30");
	$html=array();
	while($row = $db->fetch_array($result))
	{
		if ($row['companyname'] && $row['fullname'] && empty($html[$row['bid']]))
		{
		$row['time']=daterange(time(),$row['down_addtime'],'Y-m-d',"#FF3300");
		if ($row['display_name']=="2")
		{
		$row['fullname']="N".str_pad($row['cid'],7,"0",STR_PAD_LEFT);
		}
		elseif ($row['display_name']=="3")
		{
		$row['fullname']=cut_str($row['fullname'],1,0,"**");
		}
		$html[$row['bid']]="<li><a href=".url_rewrite('QS_companyshow',array('id'=>$row['bid']))." target=\"_blank\">".$row['companyname']." </a> 下载了 <a href=".url_rewrite('QS_resumeshow',array('id'=>$row['cid']))." target=\"_blank\">".$row['fullname']." </a>的个人简历<span>{$row['time']}</span></li>";
		}
	}
	exit(implode("",$html));
}
elseif($act=="hotword")
{
	if (empty($_GET['query']))
	{
	exit();
	}
	$gbk_query=trim($_GET['query']);
	if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$gbk_query=utf8_to_gbk($gbk_query);
	}
	$sql="SELECT * FROM ".table('hotword')." WHERE w_word like '%{$gbk_query}%' ORDER BY `w_hot` DESC LIMIT 0 , 10";
	$result = $db->query($sql);
	while($row = $db->fetch_array($result))
	{
		$list[]="'".$row['w_word']."'";
	}
	if ($list)
	{
	$liststr=implode(',',$list);
	$str="{";
	$str.="query:'{$gbk_query}',";
	$str.="suggestions:[{$liststr}]";
	$str.="}";
	exit($str);
	}
}
elseif($act=="joblisttip")
{
	$uid=intval($_GET['uid']);
	$wheresql='';
	if ($_CFG['subsite']=="1"  && $_CFG['subsite_filter_jobs']=="1")
	{
		$wheresql.=" AND (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
	}	
	$result = $db->query("SELECT * FROM ".table('jobs')." WHERE uid='{$uid}' {$wheresql} ORDER BY refreshtime desc limit 6");
	$i=1;
	while($row = $db->fetch_array($result))
	{
				$row['jobs_name']=cut_str($row['jobs_name'],8,0,"");
				$row['refreshtime_cn']=daterange(time(),$row['refreshtime'],'Y-m-d',"#FF3300"); 
				if (!empty($row['highlight']))
				{
				$row['jobs_name']="<span style=\"color:{$row['highlight']}\">{$row['jobs_name']}</span>";
				} 
				$row['companyname_']=$row['companyname'];
				$row['companyname']=cut_str($row['companyname'],15,0,"...");
				$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['id']));
				$row['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['company_id']));
				if ($i>5)
				{
				$html.="<li class=\"more\"><a href=\"{$row['company_url']}\" target=\"_blank\">更多...</span></li>";
				break;
				}
				$html.="<li><a href=\"{$row['jobs_url']}\" target=\"_blank\">{$row['jobs_name']}</a>&nbsp;&nbsp;<span>({$row['refreshtime_cn']})</span></li>";
				$i++;				
	}
	exit($html);
}
elseif($act=="ajaxcomlist")
{
	$categoryid=intval($_GET['categoryid']);
	$tradeid=intval($_GET['tradeid']);
	$showtype=trim($_GET['showtype']);
	$limit=intval($_GET['comrow'])>0?intval($_GET['comrow']):10;
	$jobrow=intval($_GET['jobrow'])>0?intval($_GET['jobrow']):3;
	$companynamelen=intval($_GET['companynamelen'])>0?intval($_GET['companynamelen']):12;
	$jobslen=intval($_GET['jobslen'])>0?intval($_GET['jobslen']):4;	
	$jobstable=table('jobs_search_rtime');
	$wheresql='';
	$ordersql='  ORDER BY refreshtime desc ';
	$limitsql=" LIMIT ".$limit*15;
	if ($_CFG['subsite']=='1'  && $_CFG['subsite_filter_jobs']=='1')
	{
		$wheresql.=" AND (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
	}
	if ($showtype=="category")
	{
		if ($categoryid>0)
		{
		$wheresql.=" AND category='{$categoryid}' ";
		}
	}
	elseif($showtype=="trade")
	{
		$wheresql.=" AND trade='{$tradeid}' ";
	}
	if (!empty($wheresql))
	{
	$wheresql=" WHERE ".ltrim(ltrim($wheresql),'AND');
	}
	$result = $db->query("SELECT id,uid FROM ".$jobstable.$wheresql.$ordersql.$limitsql);
	$uidarr= array();
	while($row = $db->fetch_array($result))
	{
		if (count($uidarr)>=$limit) break;
		$uidarr[$row['uid']]=$row['uid'];
	}
	if (!empty($uidarr))
	{
		$uidarr= implode(",",$uidarr);
		$wheresql=$wheresql?$wheresql." AND uid IN ({$uidarr}) ":" WHERE uid IN ({$uidarr}) ";
		$sql="SELECT company_id,companyname,company_addtime,refreshtime,id,jobs_name,addtime,uid,click,highlight,highlight,setmeal_id,setmeal_name FROM ".table('jobs').$wheresql.$ordersql;
		$countuid=array();
		$result = $db->query($sql);
		while($row = $db->fetch_array($result))
		{		
			$countuid[$row['uid']][]=$row['uid'];
			if (count($countuid[$row['uid']])>$jobrow)continue;
			$companyarray[$row['uid']]['companyname_']=$row['companyname'];
			$companyarray[$row['uid']]['companyname']=cut_str($row['companyname'],$companynamelen,0,'');
			$companyarray[$row['uid']]['company_url']=url_rewrite('QS_companyshow',array('id'=>$row['company_id']));
			$companyarray[$row['uid']]['company_addtime']=$row['company_addtime'];
			$companyarray[$row['uid']]['refreshtime']=$companyarray[$row['uid']]['refreshtime']>$row['refreshtime']?$companyarray[$row['uid']]['refreshtime']:$row['refreshtime'];
			$companyarray[$row['uid']]['refreshtime_cn']=daterange(time(),$companyarray[$row['uid']]['refreshtime'],'m-d',"#FF3300");
			$companyarray[$row['uid']]['setmeal_id']=$row['setmeal_id'];
			$companyarray[$row['uid']]['setmeal_name']=$row['setmeal_name'];
			$companyarray[$row['uid']]['uid']=$row['uid'];
			$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_addtime']=$row['addtime'];
			$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_refreshtime']=$row['refreshtime'];
			$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_click']=$row['click'];
			$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_name']=cut_str($row['jobs_name'],$jobslen,0,'');
				if (!empty($row['highlight']))
				{
				$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_name']="<span style=\"color:{$row['highlight']}\">{$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_name']}</span>";
				}
			$companyarray[$row['uid']]['jobs'][$row['id']]['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['id']));
		}
	}
	if (!empty($companyarray))
	{
		$html='';
		foreach($companyarray as $li)
		{
		$html.="<div class=\"jobboxlist link_lan\">";
		$html.="<span class=\"comtip\" id=\"{$li['uid']}\"><a href=\"{$li['company_url']}\" target=\"_blank\">{$li['companyname']}</a></span><span style=\"color: #666666\">({$li['refreshtime_cn']})</span>";
		if ($_CFG['operation_mode']=="2" && $li['setmeal_id']>1)
		{
		$html.=" <img src=\"{$_CFG['site_dir']}data/setmealimg/{$li['setmeal_id']}.gif\" border=\"0\" align=\"absmiddle\"  title=\"{$li['setmeal_name']}\" class=\"vtip\" />";
		}
		$html.="<br />";
		$html.="<span style=\"color:#009900\">聘: </span><span class=\"link_bk\">";
		if (!empty($li['jobs']))
		{
			foreach($li['jobs'] as $jobsli)
			{
			$html.="<a href=\"{$jobsli['jobs_url']}\" target=\"_blank\">{$jobsli['jobs_name']}</a>&nbsp;&nbsp;";
			}
		}
		$html.="</span>";
 		$html.="</div>";
		}
	}
	$html.="<script type=\"text/javascript\">$.joblisttip(\".comtip\",\"{$_CFG['site_dir']}plus/ajax_common.php?act=joblisttip\",\"载入中...\",\"comvtipshow\");</script>";
	exit($html);
}
?>