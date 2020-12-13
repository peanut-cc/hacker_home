<?php
 /*
 * 74cms ������Ŀ����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../data/config.php');
require_once(dirname(__FILE__).'/include/admin_common.inc.php');
require_once(ADMIN_ROOT_PATH.'include/admin_nav_fun.php');
require_once(ADMIN_ROOT_PATH.'include/admin_page_fun.php');
check_permissions($_SESSION['admin_purview'],"site_navigation");
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('pageheader',"����������");
if($act == 'list')
{
	get_token();
	$smarty->assign('navlabel',"list");
	$smarty->assign('list',get_nav());
	$smarty->display('nav/admin_nav.htm');
}
elseif($act == 'site_navigation_all_save')
{
	check_token();
	$id=$_POST['id'];
	$title=$_POST['title'];
	$navigationorder=$_POST['navigationorder'];
	$id_num=count($id);
		for($i=0;$i<$id_num;$i++)
		{
		$sql="update ".table('navigation')." set title='".$title[$i]."',navigationorder='".intval($navigationorder[$i])."'  where id='".intval($id[$i])."' LIMIT 1";
		$db->query($sql);
		}
	refresh_nav_cache();
	$smarty->clear_all_cache();
	adminmsg("�޸ĳɹ���",2);
}
elseif($act == 'site_navigation_add')
{
	get_token();
	$smarty->assign('navlabel',"add");
	$smarty->assign('category',get_nav_cat());
	$smarty->assign('syspage',get_page(0,300," WHERE pagetpye=1 or pagetpye=2"));
	$smarty->display('nav/admin_nav_add.htm');
}
elseif($act == 'site_navigation_add_save')
{
	check_token();
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('��û����д��Ŀ���ƣ�',1);
	$setsqlarr['urltype']=intval($_POST['urltype']);
		if ($setsqlarr['urltype']=="1")
		{
		$setsqlarr['url']=trim($_POST['url'])?trim($_POST['url']):adminmsg('��û����д���ӵ�ַ��',1);
		}
		else
		{
		$setsqlarr['pagealias']=trim($_POST['pagealias'])?trim($_POST['pagealias']):adminmsg('ҳ���������ʧ��',1);
		}
	$setsqlarr['list_id']=trim($_POST['list_id']);
	$setsqlarr['target']=trim($_POST['target'])?trim($_POST['target']):adminmsg('��û����д�򿪷�ʽ��',1);
	$setsqlarr['navigationorder']=intval($_POST['navigationorder']);
	$setsqlarr['display']=$_POST['display'];
	$setsqlarr['color']=$_POST['tit_color'];
	$setsqlarr['alias']=trim($_POST['alias']);
	$setsqlarr['tag']=trim($_POST['tag']);
	if(inserttable(table('navigation'),$setsqlarr))
	{
	$link[0]['text'] = "�����б�";
	$link[0]['href'] ="?act=list";
	refresh_nav_cache();
	$smarty->clear_all_cache();
	adminmsg("��ӳɹ���",2,$link);
	}
	else
	{
	adminmsg("���ʧ�ܣ�",0);
	}
}
elseif($act == 'del_navigation')
{
	check_token();
	$id=$_GET['id'];
	if (del_navigation($id))
	{
	refresh_nav_cache();
	$smarty->clear_all_cache();
	$link[0]['text'] = "�����б�";
	$link[0]['href'] ="?act=";
	adminmsg("ɾ���ɹ���",2,$link);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'site_navigation_edit')
{
	get_token();
	$id=intval($_GET['id']);
	$smarty->assign('show',get_nav_one($id));
	$smarty->assign('category',get_nav_cat());
	$smarty->assign('syspage',get_page(0,300," WHERE pagetpye=1 or pagetpye=2"));
	$smarty->display('nav/admin_nav_edit.htm');
}
elseif($act == 'site_navigation_edit_save')
{
	check_token();
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('��û����д��Ŀ���ƣ�',1);
	$setsqlarr['urltype']=intval($_POST['urltype']);
		if ($setsqlarr['urltype']=="1")
		{
		$setsqlarr['url']=trim($_POST['url'])?trim($_POST['url']):adminmsg('��û����д���ӵ�ַ��',1);
		}
		else
		{
		$setsqlarr['pagealias']=trim($_POST['pagealias'])?trim($_POST['pagealias']):adminmsg('ҳ���������ʧ��',1);
		}
		//exit($setsqlarr['pagealias']);
	$setsqlarr['list_id']=trim($_POST['list_id']);
	$setsqlarr['target']=trim($_POST['target'])?trim($_POST['target']):adminmsg('��û����д�򿪷�ʽ��',1);
	$setsqlarr['navigationorder']=intval($_POST['navigationorder']);
	$setsqlarr['display']=$_POST['display'];
	$setsqlarr['color']=$_POST['tit_color'];
	$setsqlarr['alias']=trim($_POST['alias']);
	$setsqlarr['tag']=trim($_POST['tag']);
	$wheresql=" id='".intval($_POST['id'])."'";
	if(updatetable(table('navigation'),$setsqlarr,$wheresql))
	{
	refresh_nav_cache();
	$smarty->clear_all_cache();
	$link[0]['text'] = "�����б�";
	$link[0]['href'] ="?act=list";
	adminmsg("�޸ĳɹ���",2,$link);
	}
	else
	{
	adminmsg("�޸�ʧ�ܣ�",0);
	}
}
elseif($act == 'site_navigation_category')
{
	get_token();
	$smarty->assign('navlabel',"category");
	$smarty->assign('list',get_nav_cat());
	$smarty->display('nav/admin_nav_category.htm');
}
elseif($act == 'site_navigation_category_add')
{
	get_token();
	$smarty->assign('navlabel',"category");
	$smarty->display('nav/admin_nav_category_add.htm');
}
elseif($act == 'site_navigation_category_add_save')
{
	check_token();
	$setsqlarr['categoryname']=trim($_POST['categoryname'])?trim($_POST['categoryname']):adminmsg('��û����д���ƣ�',1);
	$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('��û����д��������',1);
		if (stripos($setsqlarr['alias'],"qs_")===0)
		{
			adminmsg("�����������á�qs_����ͨ",0);
		}
		else
		{
			$info=get_nav_cat_one($setsqlarr['alias']);
			if (empty($info))
			{
			$link[0]['text'] = "�����б�";
			$link[0]['href'] ="?act=site_navigation_category";
			inserttable(table('navigation_category'),$setsqlarr)?adminmsg("��ӳɹ���",2,$link):adminmsg("���ʧ�ܣ�",0);	
			}
			else
			{
			adminmsg("������".$setsqlarr['alias']."�Ѿ����ڣ�",0);
			}					
		}
		
}
elseif($act == 'site_navigation_category_del')
{
	check_token();
	if (del_nav_cat(intval($_GET['id'])))
	{
	adminmsg("ɾ���ɹ���",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'site_navigation_category_edit')
{
		get_token();
	$smarty->assign('navlabel',"category");
	$alias=trim($_GET['alias']);
	$smarty->assign('list',get_nav_cat_one($alias));
	$smarty->display('nav/admin_nav_category_edit.htm');
}
elseif($act == 'site_navigation_category_edit_save')
{
	check_token();
	$setsqlarr['categoryname']=trim($_POST['categoryname'])?trim($_POST['categoryname']):adminmsg('��û����д���ƣ�',1);
	$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('��û����д��������',1);
	if (stripos($setsqlarr['alias'],"qs_")===0)
		{
			adminmsg("�����������á�qs_����ͨ",0);
		}
		else
		{
			$info=get_nav_cat_one($setsqlarr['alias']);
			if (empty($info) || $info['alias']==$setsqlarr['alias'])
			{
			$link[0]['text'] = "�����б�";
			$link[0]['href'] ="?act=site_navigation_category";
			$wheresql=" id='".intval($_POST['id'])."'";
			!updatetable(table('navigation_category'),$setsqlarr,$wheresql)?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
			}
			else
			{
			adminmsg("������".$setsqlarr['alias']."�Ѿ����ڣ�",0);
			}					
		}
}
?>