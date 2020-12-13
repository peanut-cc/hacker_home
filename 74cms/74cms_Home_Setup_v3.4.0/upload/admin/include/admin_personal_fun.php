<?php
 /*
 * 74cms 管理中心 个人用户相关函数
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
 //******************************简历部分**********************************
function get_resume_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$limit=" LIMIT ".$offset.','.$perpage;
	$result = $db->query($get_sql.$limit);
	while($row = $db->fetch_array($result))
	{
	$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['id']));
	$row_arr[] = $row;
	}
	return $row_arr;
}
function distribution_resume($id)
{
	global $db;
	if (!is_array($id))$id=array($id);
	$time=time();
	foreach($id as $v)
	{
		$v=intval($v);
		$t1=$db->getone("select * from ".table('resume')." where id='{$v}' LIMIT 1");
		$t2=$db->getone("select * from ".table('resume_tmp')." where id='{$v}' LIMIT 1");
		if ((empty($t1) && empty($t2)) || (!empty($t1) && !empty($t2)))
		{
		continue;
		}
		else
		{
				$j=!empty($t1)?$t1:$t2;
				if (!empty($t1) &&  $j['audit']=="1" && $j['user_status']=="1" && $j['complete']=="1")
				{
					continue;
				}
				elseif (!empty($t2))
				{
						if ($j['audit']!="1" || $j['display']!="1" || $j['user_status']!="1"  || $j['complete']!="1")
						{
						continue;
						}
				}
				//检测完毕	
				if (!empty($t1))
				{
					$db->query("Delete from ".table('resume')." WHERE id='{$v}' LIMIT 1");
					$db->query("Delete from ".table('resume_tmp')." WHERE id='{$v}' LIMIT 1");
					if (inserttable(table('resume_tmp'),$j))
					{
						$db->query("Delete from ".table('resume_search_rtime')." WHERE id='{$v}' LIMIT 1");
						$db->query("Delete from ".table('resume_search_key')." WHERE id='{$v}' LIMIT 1");
						$db->query("Delete from ".table('resume_search_tag')." WHERE id='{$v}' LIMIT 1");
					}
				}
				else
				{
					$db->query("Delete from ".table('resume')." WHERE id='{$v}' LIMIT 1");
					$db->query("Delete from ".table('resume_tmp')." WHERE id='{$v}' LIMIT 1");
					if (inserttable(table('resume'),$j))
					{
						$searchtab['id']=$j['id'];
						$searchtab['display']=$j['display'];
						$searchtab['uid']=$j['uid'];
						$searchtab['subsite_id']=$j['subsite_id'];
						$searchtab['sex']=$j['sex'];
						$searchtab['nature']=$j['nature'];
						$searchtab['marriage']=$j['marriage'];
						$searchtab['experience']=$j['experience'];
						$searchtab['district']=$j['district'];
						$searchtab['sdistrict']=$j['sdistrict'];
						$searchtab['wage']=$j['wage'];
						$searchtab['education']=$j['education'];
						$searchtab['photo']=$j['photo'];
						$searchtab['refreshtime']=$j['refreshtime'];
						$searchtab['talent']=$j['talent'];
						inserttable(table('resume_search_rtime'),$searchtab);
						$searchtab['key']=$j['key'];
						$searchtab['likekey']=$j['intention_jobs'].','.$j['recentjobs'].','.$j['specialty'].','.$j['fullname'];
						inserttable(table('resume_search_key'),$searchtab);
						unset($searchtab);
						$tag=explode('|',$j['tag']);
						$tagindex=1;
						$tagsql['tag1']=$tagsql['tag2']=$tagsql['tag3']=$tagsql['tag4']=$tagsql['tag5']=0;
						if (!empty($tag) && is_array($tag))
						{
							foreach($tag as $v)
							{
							$vid=explode(',',$v);
							$tagsql['tag'.$tagindex]=intval($vid[0]);
							$tagindex++;
							}
						}
						$tagsql['id']=$j['id'];
						$tagsql['uid']=$j['uid'];
						$tagsql['subsite_id']=$j['subsite_id'];
						$tagsql['experience']=$j['experience'];
						$tagsql['district']=$j['district'];
						$tagsql['sdistrict']=$j['sdistrict'];
						$tagsql['education']=$j['education'];
						inserttable(table('resume_search_tag'),$tagsql);
					}
				}		
		}
	}
}
function del_resume($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	$return=0;
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("Delete from ".table('resume')." WHERE id IN ({$sqlin})")) return false;
	$return=$return+$db->affected_rows();
	if (!$db->query("Delete from ".table('resume_tmp')." WHERE id IN ({$sqlin})")) return false;
	$return=$return+$db->affected_rows();
	if (!$db->query("Delete from ".table('resume_jobs')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_education')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_training')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_work')." WHERE pid IN ({$sqlin}) ")) return false;
	if (!$db->query("Delete from ".table('resume_search_rtime')." WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("Delete from ".table('resume_search_key')." WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("Delete from ".table('resume_search_tag')." WHERE id IN ({$sqlin})")) return false;
	return $return;
	}
	return $return;
}
function del_resume_for_uid($uid)
{
	global $db;
	if (!is_array($uid)) $uid=array($uid);
	$sqlin=implode(",",$uid);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		$result = $db->query("SELECT id FROM ".table('resume')." WHERE uid IN (".$sqlin.")");
		while($row = $db->fetch_array($result))
		{
		$rid[]=$row['id'];
		}
		$result = $db->query("SELECT id FROM ".table('resume_tmp')." WHERE uid IN (".$sqlin.")");
		while($row = $db->fetch_array($result))
		{
		$rid[]=$row['id'];
		}
		if (empty($rid))
		{
		return true;
		}
		else
		{
		return del_resume($rid);
		}		
	}
}
function edit_resume_audit($id,$audit,$reason,$pms_notice)
{
	global $db,$_CFG;
	$audit=intval($audit);
	if (!is_array($id))  $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('resume')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		if (!$db->query("update  ".table('resume_tmp')." SET audit='{$audit}'  WHERE id IN ({$sqlin}) ")) return false;
		distribution_resume($id);
		//发送站内信
		if ($pms_notice=='1')
		{
				$result = $db->query("SELECT  fullname,title,uid  FROM ".table('resume')." WHERE id IN ({$sqlin})  UNION ALL  SELECT fullname,title,uid FROM ".table('resume_tmp')." WHERE id IN ({$sqlin})");
				$reason=$reason==''?'原因：未知':'原因：'.$reason;
				while($list = $db->fetch_array($result))
				{
					$user_info=get_user($list['uid']);
					$setsqlarr['message']=$audit=='1'?"您创建的简历：{$list['title']},真实姓名：{$list['fullname']},成功通过网站管理员审核！":"您创建的简历：{$list['title']},真实姓名：{$list['fullname']},未通过网站管理员审核,{$reason}";
					$setsqlarr['msgtype']=1;
					$setsqlarr['msgtouid']=$user_info['uid'];
					$setsqlarr['msgtoname']=$user_info['username'];
					$setsqlarr['dateline']=time();
					$setsqlarr['replytime']=time();
					$setsqlarr['new']=1;
					inserttable(table('pms'),$setsqlarr);
				 }
		}
		//审核未通过增加原因
		if($audit=='3'){
			foreach($id as $list){
				$auditsqlarr['resume_id']=$list;
				$auditsqlarr['reason']=$reason;
				$auditsqlarr['addtime']=time();
				inserttable(table('audit_reason'),$auditsqlarr);
			}
		}
			
			//发送邮件
				$mailconfig=get_cache('mailconfig');//获取邮件规则
				$sms=get_cache('sms_config');
				if ($audit=="1" && $mailconfig['set_resumeallow']=="1")//审核通过
				{
						$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin})  UNION ALL  SELECT * FROM ".table('resume_tmp')." WHERE id IN ({$sqlin})");
						while($list = $db->fetch_array($result))
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumeallow");
						}
				}
				if ($audit=="3" && $mailconfig['set_resumenotallow']=="1")//审核未通过
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin})  UNION ALL  SELECT * FROM ".table('resume_tmp')." WHERE id IN ({$sqlin})");
						while($list = $db->fetch_array($result))
						{
						dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_mail.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumenotallow");
						}
				}
				//sms		
				if ($audit=="1" && $sms['open']=="1" && $sms['set_resumeallow']=="1" )
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin})  UNION ALL  SELECT * FROM ".table('resume_tmp')." WHERE id IN ({$sqlin}) ");
						while($list = $db->fetch_array($result))
						{
							$user_info=get_user($list['uid']);
							if ($user_info['mobile_audit']=="1")
							{
							dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumeallow");
							}
						}
				}
				//sms
				if ($audit=="3" && $sms['open']=="1" && $sms['set_resumenotallow']=="1" )//认证未通过
				{
					$result = $db->query("SELECT * FROM ".table('resume')." WHERE id IN ({$sqlin})  UNION ALL  SELECT * FROM ".table('resume_tmp')." WHERE id IN ({$sqlin})");
						while($list = $db->fetch_array($result))
						{
							$user_info=get_user($list['uid']);
							if ($user_info['mobile_audit']=="1")
							{
							dfopen($_CFG['site_domain'].$_CFG['site_dir']."plus/asyn_sms.php?uid=".$list['uid']."&key=".asyn_userkey($list['uid'])."&act=set_resumenotallow");
							}
						}
				}
				//sms
			//发送邮件
	return true;
	}
	return false;
}
//修改照片审核状态
function edit_resume_photoaudit($id,$audit)
{
	global $db;
	$audit=intval($audit);
	if (!is_array($id)) $id=array($id);
	if (!empty($id))
	{
		foreach($id as $i)
		{
			$i=intval($i);
			$tb1=$db->getone("select photo_img,photo_audit,photo_display from ".table('resume')." WHERE id='{$i}' LIMIT  1");
			if (!empty($tb1))
			{
				if ($tb1['photo_img'] && $audit=="1" && $tb1['photo_display']=="1")
				{
				$photo=1;
				}
				else
				{
				$photo=0;
				}	
				$db->query("update  ".table('resume')." SET photo_audit='{$audit}',photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
				$db->query("update  ".table('resume_search_rtime')." SET photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
				$db->query("update  ".table('resume_search_key')." SET photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");				
			}
			else
			{	
				$tb2=$db->getone("select photo_img,photo_audit,photo_display from ".table('resume_tmp')." WHERE id='{$i}' LIMIT  1");	
				if ($tb2['photo_img'] && $audit=="1"  && $tb2['photo_display']=="1")
				{
				$photo=1;
				}
				else
				{
				$photo=0;
				}			
				$db->query("update  ".table('resume_tmp')." SET photo_audit='{$audit}',photo='{$photo}' WHERE id='{$i}' LIMIT 1 ");
			}
		}
	}
	return true;
}
//修改人才等级
function edit_resume_talent($id,$talent)
{
	global $db;
	$talent=intval($talent);
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
	if (!$db->query("update  ".table('resume')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("update  ".table('resume_tmp')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("update  ".table('resume_search_rtime')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
	if (!$db->query("update  ".table('resume_search_key')." SET talent={$talent}  WHERE id IN ({$sqlin})")) return false;
	return true;
	}
	return false;
}
//从UID获取所有简历
function get_resume_uid($uid)
{
	global $db;
	$uid=intval($uid);
	$result = $db->query("select * FROM ".table('resume')." where uid='{$uid}'   UNION ALL select * FROM ".table('resume_tmp')." where uid='{$uid}' ");
	while($row = $db->fetch_array($result))
	{ 
	$row['resume_url']=url_rewrite('QS_resumeshow',array('id'=>$row['id']));
	$row_arr[] = $row;
	}
	return $row_arr;	
}
function refresh_resume($id)
{
	global $db;
	$return=0;
	if (!is_array($id))$id=array($id);
	$sqlin=implode(",",$id);
	if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	{
		if (!$db->query("update  ".table('resume')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("update  ".table('resume_tmp')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
		$return=$return+$db->affected_rows();
		if (!$db->query("update  ".table('resume_search_rtime')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
		if (!$db->query("update  ".table('resume_search_key')." SET refreshtime='".time()."'  WHERE id IN (".$sqlin.")")) return false;
	}
	return $return;
}
//**************************个人会员列表
function get_member_list($offset,$perpage,$get_sql= '')
{
	global $db;
	$row_arr = array();
	$limit=" LIMIT ".$offset.','.$perpage;	
	$result = $db->query("SELECT * FROM ".table('members')." as m ".$get_sql.$limit);
		while($row = $db->fetch_array($result))
		{
		$row_arr[] = $row;
		}
	return $row_arr;
}
function delete_member($uid)
{
	global $db;
	if (!is_array($uid)) $uid=array($uid);
	$sqlin=implode(",",$uid);
		if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
		{
 		if (!$db->query("Delete from ".table('members')." WHERE uid IN (".$sqlin.")")) return false;
		if (!$db->query("Delete from ".table('members_info')." WHERE uid IN (".$sqlin.")")) return false;
		return true;
		}
	return false;
}
function get_member_one($memberuid)
{
	global $db;
	$sql = "select * from ".table('members')." where uid=".intval($memberuid)." LIMIT 1";
	$val=$db->getone($sql);
	return $val;
}
function get_user($uid)
{
	global $db;
	$uid=intval($uid);
	$sql = "select * from ".table('members')." where uid = '{$uid}' LIMIT 1";
	return $db->getone($sql);
}
//获取简历的审核日志
function get_resumeaudit_one($resume_id){
	global $db;
	$sql = "select * from ".table('audit_reason')."  WHERE resume_id='".intval($resume_id)."' ORDER BY id DESC";
	return $db->getall($sql);
}
//获取简历基本信息
function get_resume_basic($uid,$id)
{
	global $db;
	$id=intval($id);
	$uid=intval($uid);
	$info=$db->getone("select * from ".table('resume')." where id='{$id}'  AND uid='{$uid}' LIMIT 1 ");
	if (empty($info))
	{
	$info=$db->getone("select * from ".table('resume_tmp')." where id='{$id}'  AND uid='{$uid}' LIMIT 1 ");
	}
	if (empty($info))
	{
	return false;
	}
	else
	{
	$info['age']=date("Y")-$info['birthdate'];
	$info['number']="N".str_pad($info['id'],7,"0",STR_PAD_LEFT);
	$info['lastname']=cut_str($info['fullname'],1,0,"**");
	$info['tagcn']=preg_replace("/\d+/", '',$info['tag']);
	$info['tagcn']=preg_replace('/\,/','',$info['tagcn']);
	$info['tagcn']=preg_replace('/\|/','&nbsp;&nbsp;&nbsp;',$info['tagcn']);
	return $info;
	}
}
//获取教育经历列表
function get_resume_education($uid,$pid)
{
	global $db;
	if (intval($uid)!=$uid) return false;
	$sql = "SELECT * FROM ".table('resume_education')." WHERE  pid='".intval($pid)."' AND uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//获取：工作经历
function get_resume_work($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_work')." where pid='".$pid."' AND uid=".intval($uid)."" ;
	return $db->getall($sql);
}
//获取：培训经历列表
function get_resume_training($uid,$pid)
{
	global $db;
	$sql = "select * from ".table('resume_training')." where pid='".intval($pid)."' AND  uid='".intval($uid)."' ";
	return $db->getall($sql);
}
//获取意向职位
function get_resume_jobs($pid)
{
	global $db;
	$pid=intval($pid);
	$sql = "select * from ".table('resume_jobs')." where pid='{$pid}'  LIMIT 20" ;
	return $db->getall($sql);
}
function reasonaudit_del($id)
{
	global $db;
	if (!is_array($id)) $id=array($id);
	$sqlin=implode(",",$id);
	if (!preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin)) return false;
	if (!$db->query("Delete from ".table('audit_reason')." WHERE id IN ({$sqlin})")) return false;
	return $db->affected_rows();
}
 ?>