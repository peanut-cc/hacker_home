<?php
 /*
 * 74cms ajax ��ϵ��ʽ
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(dirname(__FILE__)).'/include/plus.common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : '';
if($act == 'jobs_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"contact link_lan\">���˻�Ա�� <a href=\"".url_rewrite('QS_login')."\">��¼</a>  �鿴��ϵ��ʽ����������Ǹ��˻�Ա������ <a href=\"".$_CFG['site_dir']."user/user_reg.php\">���ע��</a> ��Ϊ���˻�Ա��</div>";
			}
		}
		elseif($_CFG['showjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<div class=\"contact link_lan\">��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_resume.php?act=resume_list\">[�鿴�ҵļ���]</a></div>";
				}
			}
			else
			{
			$show=false;
			$html="<div class=\"contact link_lan\">���˻�Ա�� <a href=\"".url_rewrite('QS_login')."\">��¼</a>  �鿴��ϵ��ʽ����������Ǹ��˻�Ա������ <a href=\"".$_CFG['site_dir']."user/user_reg.php\">���ע��</a> ��Ϊ���˻�Ա��</div>";
			}
		}
		if ($show)
		{
		$sql = "select * from ".table('jobs_contact')." where pid='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_job']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �ˣ���ҵ���ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>�� ϵ �� ����<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �� ������ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ���䣺��ҵ���ò����⹫��</li>";
			$address=$val['address_show']=='1'?"<li>��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ��ַ����ҵ���ò����⹫��</li>";
			$qq=$val['qq_show']=='1'?"<li>��ϵQ Q�� <img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=jobs_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵQ Q����ҵ���ò����⹫�� </li>";
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$qq.$ull;
			}
			else
			{
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�{$val['contact']}</li>":"<li>�� ϵ �ˣ���ҵ���ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>��ϵ�绰��{$val['telephone']}</li>":"<li>��ϵ�绰����ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺{$val['email']}</li>":"<li>��ϵ���䣺��ҵ���ò����⹫��</li>";
			$address=$val['address_show']=='1'?"<li>��ϵ��ַ��{$val['address']}</li>":"<li>��ϵ��ַ����ҵ���ò����⹫��</li>";
			$qq=$val['qq_show']=='1'?"<li>��ϵQ Q��{$val['qq']}</li>":"<li>��ϵQ Q����ҵ���ò����⹫��</li>";
			$ull.="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$qq.$ull;
			}
		exit($html);
		}
		else
		{		
		exit($html);
		}
	}
		
}
elseif($act == 'company_contact')
{
	$id=intval($_GET['id']);
	if ($id>0)
	{
		$show=false;
		if($_CFG['showjobcontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showjobcontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"contact link_lan\">���˻�Ա�� <a href=\"".url_rewrite('QS_login')."\">��¼</a>  �鿴��ϵ��ʽ����������Ǹ��˻�Ա������ <a href=\"".$_CFG['site_dir']."user/user_reg.php\">���ע��</a> ��Ϊ���˻�Ա��</div>";
			}
		}
		elseif($_CFG['showjobcontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='2')
			{
				$val=$db->getone("select uid from ".table('resume')." where uid='{$_SESSION['uid']}' LIMIT 1");
			 	if (!empty($val))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<div class=\"contact link_lan\">��û�з����������߼�����Ч������������ſ��Բ鿴��ϵ��ʽ��<a href=\"".get_member_url($_SESSION['utype'],true)."personal_resume.php?act=resume_list\">[�鿴�ҵļ���]</a></div>";
				}
			}
			else
			{
			$show=false;
			$html="<div class=\"contact link_lan\">���˻�Ա�� <a href=\"".url_rewrite('QS_login')."\">��¼</a>  �鿴��ϵ��ʽ����������Ǹ��˻�Ա������ <a href=\"".$_CFG['site_dir']."user/user_reg.php\">���ע��</a> ��Ϊ���˻�Ա��</div>";
			}
		}
		if ($show)
		{
		$sql = "select contact,contact_show,telephone,telephone_show,email,email_show,address,address_show,website FROM ".table('company_profile')." where id='{$id}' LIMIT 1";
		$val=$db->getone($sql);
			if ($_CFG['contact_img_com']=='2')
			{
			$token=md5($val['contact'].$id.$val['telephone']);
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �ˣ���ҵ���ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>�� ϵ �� ����<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>�� ϵ �� ������ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ���䣺��ҵ���ò����⹫��</li>";
			$address=$val['address_show']=='1'?"<li>��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>":"<li>��ϵ��ַ����ҵ���ò����⹫��</li>";
			$website.="<li>��˾��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=company_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";		
			$ull="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$website.$ull;
			}
			else
			{
			$ul="<ul>";
			$contact=$val['contact_show']=='1'?"<li>�� ϵ �ˣ�{$val['contact']}</li>":"<li>�� ϵ �ˣ���ҵ���ò����⹫��</li>";
			$telephone=$val['telephone_show']=='1'?"<li>��ϵ�绰��{$val['telephone']}</li>":"<li>��ϵ�绰����ҵ���ò����⹫��</li>";
			$email=$val['email_show']=='1'?"<li>��ϵ���䣺{$val['email']}</li>":"<li>��ϵ���䣺��ҵ���ò����⹫��</li>";
			$address=$val['address_show']=='1'?"<li>��ϵ��ַ��{$val['address']}</li>":"<li>��ϵ��ַ����ҵ���ò����⹫��</li>";
			$website="<li>��˾��ַ��{$val['website']}</li>";
			$ull.="</ul>";
			$html=$ul.$contact.$telephone.$email.$address.$website.$ull;
			}
			exit($html);
		}
		else
		{		
		exit($html);
		}
	}
}
elseif($act == 'resume_contact')
{
		$id=intval($_GET['id']);
		$show=false;
		if($_CFG['showresumecontact']=='0')
		{
		$show=true;
		}
		elseif($_CFG['showresumecontact']=='1')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
			{
			$show=true;
			}
			else
			{
			$show=false;
			$html="<div class=\"contact link_lan\">��ҵ��Ա�� <a href=\"".url_rewrite('QS_login')."\">��¼</a>  �鿴��ϵ��ʽ�������������ҵ��Ա������ <a href=\"".$_CFG['site_dir']."user/user_reg.php\">���ע��</a>��</div>";
			}
		}
		elseif($_CFG['showresumecontact']=='2')
		{
			if ($_SESSION['uid'] && $_SESSION['username'] && $_SESSION['utype']=='1')
			{
				$sql = "select did from ".table('company_down_resume')." WHERE company_uid = {$_SESSION['uid']} AND resume_id='{$id}' LIMIT 1";
				$info=$db->getone($sql);
			 	if (!empty($info))
				{
				$show=true;
				}
				else
				{
				$show=false;
				$html="<div align=\"center\"><img src=\"{$_CFG['site_template']}images/44.gif\"  border=\"0\" id=\"download\"/></div>";
				}
			}
			else
			{
			$show=false;
			$html="<div class=\"contact link_lan\">��ҵ��Ա�� <a href=\"".url_rewrite('QS_login')."\">��¼</a>  �鿴��ϵ��ʽ�������������ҵ��Ա������ <a href=\"".$_CFG['site_dir']."user/user_reg.php\">���ע��</a> </div>";
			}
		}
		if ($show)
		{
			$tb1=$db->getone("select fullname,telephone,email,qq,address,website from ".table('resume')." WHERE  id='{$id}'  LIMIT 1");
			$tb2=$db->getone("select fullname,telephone,email,qq,address,website from ".table('resume_tmp')." WHERE  id='{$id}'  LIMIT 1");		
			$val=!empty($tb1)?$tb1:$tb2;
			if ($_CFG['contact_img_resume']=='2')
			{
			$token=md5($val['fullname'].$id.$val['telephone']);
			$html="<ul>";
			$html.="<li>�� ϵ �ˣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=1&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="<li>��ϵ�绰��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=2&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="<li>��ϵ���䣺<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=3&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="<li>��ϵQ Q��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=4&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="<li>��ϵ��ַ��<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=5&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="<li>������ҳ/���ͣ�<img src=\"{$_CFG['site_dir']}plus/contact_img.php?act=resume_contact&type=6&id={$id}&token={$token}\"  border=\"0\" align=\"absmiddle\"/></li>";
			$html.="</ul>";
			$html.="<div align=\"center\"><br/><img src=\"{$_CFG['site_template']}images/64.gif\"  border=\"0\" id=\"invited\"/></div>";
			$html.="<div align=\"center\"><span class=\"add_resume_pool\">[��ӵ��˲ſ�]</span><br/><br/></div>";
			}
			else
			{
			$html="<ul>";
			$html.="<li>�� ϵ �ˣ�".$val['fullname']."</li>";
			$html.="<li>��ϵ�绰��".$val['telephone']."</li>";
			$html.="<li>��ϵ���䣺".$val['email']."</li>";
			$html.="<li>��ϵQ Q��".$val['qq']."</li>";
			$html.="<li>��ϵ��ַ��".$val['address']."</li>";
			$html.="<li>������ҳ/���ͣ�".$val['website']."</li>";
			$html.="</ul>";
			$html.="<div align=\"center\"><br/><img src=\"{$_CFG['site_template']}images/64.gif\"  border=\"0\" id=\"invited\"/></div>";
			$html.="<div align=\"center\"><span class=\"add_resume_pool\">[��ӵ��˲ſ�]</span><br/><br/></div>";
			}
			exit($html);
		exit($html);
		}
		else
		{		
		exit($html);
		}
}
 ?>