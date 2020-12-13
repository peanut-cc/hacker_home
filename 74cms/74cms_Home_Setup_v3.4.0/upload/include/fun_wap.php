<?php
 /*
 * 74cms WAP
 * ============================================================================
 * 版权所有: 骑士网络，并保留所有权利。
 * 网站地址: http://www.74cms.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
*/
 if(!defined('IN_QISHI'))
 {
 	die('Access Denied!');
 }
function browser()
{
	switch(TRUE)
	{
	// Apple/iPhone browser renders as mobile
	case (preg_match('/(apple|iphone|ipod)/i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/mobile/i', $_SERVER['HTTP_USER_AGENT'])):
	$browser = "mobile";
	break; 
	// Other mobile browsers render as mobile
	case (preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|
	treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])):
	$browser = "mobile";
	break; 
	// Wap browser
	case (((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'text/vnd.wap.wml') > 0) || (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0)) || ((isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])))):
	$browser = "mobile";
	break; 
	// Shortend user agents
	case (in_array(strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,3)),array('lg '=>'lg ','lg-'=>'lg-','lg_'=>'lg_','lge'=>'lge'))); 
	$browser = "mobile";
	break; 
	// More shortend user agents
	case (in_array(strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4)),array('acs-'=>'acs-','amoi'=>'amoi','doco'=>'doco','eric'=>'eric','huaw'=>'huaw','lct_'=>'lct_','leno'=>'leno','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','nec-'=>'nec-','phil'=>'phil','sams'=>'sams','sch-'=>'sch-','shar'=>'shar','sie-'=>'sie-','wap_'=>'wap_','zte-'=>'zte-')));
	$browser = "mobile";
	break; 
	// Render mobile site for mobile search engines
	case (preg_match('/Googlebot-Mobile/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/YahooSeeker\/M1A1-R2D2/i', $_SERVER['HTTP_USER_AGENT'])):
	$browser = "mobile";
	break;
	}
	return $browser;
}
function company_one($id)
{
	global $db;
	$wheresql=" WHERE id=".intval($id);
	$sql = "select * from ".table('company_profile').$wheresql." LIMIT 1";
	$val=$db->getone($sql);
	return $val;
}
function jobs_one($id)
{
	global $db;
	$id=intval($id);
	$db->query("update ".table('jobs')." set click=click+1 WHERE id='{$id}'  LIMIT 1");
	$wheresql=" WHERE id='{$id}'";
	$sql = "select * from ".table('jobs').$wheresql." LIMIT 1";
	$val=$db->getone($sql);
	$val['amount']=$val['amount']=="0"?'若干':$val['amount'];
	$profile=company_one($val['company_id']);
	$val['company']=$profile;
	$sql = "select * from ".table('jobs_contact')." where pid='{$id}' LIMIT 1";
	$contact=$db->getone($sql);
	$val['contact']=$contact;
	return $val;
}
function WapShowMsg($msg_detail, $msg_type = 0, $links = array())
{
	global $smarty;
    if (count($links) == 0)
    {
        $links[0]['text'] = '返回上一页';
        $links[0]['href'] = 'javascript:history.go(-1)';
    }
   $smarty->assign('ur_here',     '系统提示');
   $smarty->assign('msg_type',    $msg_type);
   $smarty->assign('msg_detail',  $msg_detail);
   $smarty->assign('links',       $links);
   $smarty->assign('default_url', $links[0]['href']);
   $smarty->display('wap/wap-showmsg.htm');
	exit();
}
function wapmulti($num, $perpage, $curpage, $mpurl)
{
	$lang['home_page']="首页";
	$lang['last_page']="上一页";
	$lang['next_page']="下一页";
	$lang['end_page']="尾页";
	$lang['page']="页";
	$lang['turn_page']="翻页";
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
	if($num > $perpage) {
		$page = 5;
		$offset = 2;

		$realpages = @ceil($num / $perpage);
		$pages = $realpages;

		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$mpurl.'page=1">'.$lang['home_page'].'</a>' : '').
			($curpage > 1 ? ' <a href="'.$mpurl.'page='.($curpage - 1).'">'.$lang['last_page'].'</a>' : '');

		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? ' '.$i : ' <a href="'.$mpurl.'page='.$i.'">'.$i.'</a>';
		}

		$multipage .= ($curpage < $pages ? ' <a href="'.$mpurl.'page='.($curpage + 1).'">'.$lang['next_page'].'</a>' : '').
			($to < $pages ? ' <a href="'.$mpurl.'page='.$pages.'">'.$lang['end_page'].'</a>' : '');

		$multipage .= $realpages > $page ?
			'<br />'.$curpage.'/'.$realpages.$lang['page']: '';

	}
	return $multipage;
}
?>