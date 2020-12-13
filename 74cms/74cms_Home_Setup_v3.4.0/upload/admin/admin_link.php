<?php
 /*
 * 74cms ��������
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
require_once(ADMIN_ROOT_PATH.'include/admin_link_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
$upfiles_dir="../data/link/";
$files_dir=$_CFG['site_dir']."data/link/";
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$smarty->assign('pageheader',"��������");
if($act == 'list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"link_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY l.show_order DESC";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE l.link_name like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE l.link_url like '%{$key}%'";
	}
	else
	{
	!empty($_GET['alias'])? $wheresqlarr['l.alias']=trim($_GET['alias']):'';
	!empty($_GET['type_id'])? $wheresqlarr['l.type_id']=intval($_GET['type_id']):'';
	if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	}
		if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_links']=="1")
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND ";
			$wheresql.=" (l.subsite_id=0 OR l.subsite_id=".intval($_CFG['subsite_id']).") ";
		}
	$joinsql=" LEFT JOIN ".table('link_category')." AS c ON l.alias=c.c_alias  ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('link')." AS l ".$joinsql.$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$link = get_links($offset, $perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('link',$link);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('upfiles_dir',$upfiles_dir);
	$smarty->assign('get_link_category',get_link_category());
	$smarty->assign('navlabel',"list");
	$smarty->display('link/admin_link.htm');
}
elseif($act == 'del_link')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"link_del");
	$id=$_REQUEST['id'];
	if ($num=del_link($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act =='add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"link_add");
	$id = !empty($_GET['id']) ? trim($_GET['id']) : '';
	$smarty->assign('cat',get_link_category());
 	$smarty->assign('navlabel',"add");	
	$smarty->display('link/admin_link_add.htm');
}
elseif($act =='addsave')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"link_add");
	$setsqlarr['link_name']=$_POST['link_name']?trim($_POST['link_name']):adminmsg('�������Ʋ���Ϊ�գ�',1);
	$setsqlarr['link_url']=$_POST['link_url'];
	$setsqlarr['alias']=$_POST['alias'];
	$setsqlarr['show_order'] =intval($_POST['show_order']);
	$setsqlarr['display'] =intval($_POST['display']);
	$setsqlarr['type_id'] =1;
	$setsqlarr['Notes'] =trim($_POST['Notes']);	
	if ( $_FILES['logo']['name'])
	{
		$setsqlarr['link_logo']=_asUpFiles($upfiles_dir, "logo", 1024*2, 'jpg/gif/png',true);
		if (empty($setsqlarr['link_logo']))
		{
		adminmsg('�ϴ�ͼƬ����',1);
		}
		else
		{
		$setsqlarr['link_logo']=$files_dir.$setsqlarr['link_logo'];
		}
	}
	else
	{
		$setsqlarr['link_logo']=trim($_POST['link_logo']);
	}
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$link[0]['text'] = "�����������";
	$link[0]['href'] = '?act=add';
	$link[1]['text'] = "�������������б�";
	$link[1]['href'] = '?';
	!inserttable(table('link'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
}
elseif($act =='edit')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"link_edit");
	$id =$_GET['id'];
	$smarty->assign('upfiles_dir',$upfiles_dir);
	$smarty->assign('link',get_links_one($id));
	$smarty->assign('cat',get_link_category());
	$smarty->assign('url',$_SERVER['HTTP_REFERER']);
 	$smarty->display('link/admin_link_edit.htm');
}
elseif($act =='editsave')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"link_edit");
	$setsqlarr['link_name']=$_POST['link_name']?trim($_POST['link_name']):adminmsg('�������Ʋ���Ϊ�գ�',1);
	$setsqlarr['link_url']=$_POST['link_url'];
	$setsqlarr['alias']=$_POST['alias'];
	$setsqlarr['show_order'] =intval($_POST['show_order']);
	$setsqlarr['display'] =intval($_POST['display']);
	$setsqlarr['Notes'] =trim($_POST['Notes']);
	if ( $_FILES['logo']['name'])
	{
		$setsqlarr['link_logo']=_asUpFiles($upfiles_dir, "logo", 1024*2, 'jpg/gif/png',true);
		if (empty($setsqlarr['link_logo']))
		{
		adminmsg('�ϴ�ͼƬ����',1);
		}
		else
		{
		$setsqlarr['link_logo']=$files_dir.$setsqlarr['link_logo'];
		}
	}
	else
	{
		$setsqlarr['link_logo']=trim($_POST['link_logo']);
	}
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] = $_POST['url'];
	!updatetable(table('link'),$setsqlarr," link_id =".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'category')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"link_category");
	$smarty->assign('link',get_link_category());
	$smarty->assign('navlabel',"category");
	$smarty->display('link/admin_link_category.htm');
}
elseif($act == 'category_add')
{	
	get_token();
	check_permissions($_SESSION['admin_purview'],"link_category");
	$smarty->assign('navlabel',"category");
	$smarty->display('link/admin_link_category_add.htm');
}
elseif($act == 'add_category_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"link_category");	
	$setsqlarr['categoryname']=$_POST['categoryname']?trim($_POST['categoryname']):adminmsg('��û����д�������ƣ�',1);
	$setsqlarr['c_alias']=$_POST['c_alias']?trim($_POST['c_alias']):adminmsg('��û����������ƣ�',1);
	substr($setsqlarr['c_alias'],0,3)=='QS_'?adminmsg('�������Ʋ����� QS_ ��ͷ��',1):'';
	$category=get_link_category_name($setsqlarr['c_alias']);
	if ($category)
	{
	adminmsg("�������Ѿ����ڣ�",0);
	}
	else
	{
	$link[0]['text'] = "���ط������";
	$link[0]['href'] = '?act=category';
	$link[1]['text'] = "������ӷ���";
	$link[1]['href'] = "?act=category_add";
	!inserttable(table('link_category'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
	}	
}
elseif($act == 'category_edit')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"link_category");
	$smarty->assign('navlabel',"category");
	$smarty->assign('category',get_link_category_name($_GET['alias']));
	$smarty->display('link/admin_link_category_edit.htm');
}
elseif($act == 'edit_category_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"link_category");	
	$setsqlarr['categoryname']=$_POST['categoryname']?trim($_POST['categoryname']):adminmsg('��û����д�������ƣ�',1);
	$setsqlarr['c_alias']=$_POST['c_alias']?trim($_POST['c_alias']):adminmsg('��û����������ƣ�',1);
	substr($setsqlarr['c_alias'],0,3)=='QS_'?adminmsg('�������Ʋ����� QS_ ��ͷ��',1):'';
	$category=get_link_category_name($setsqlarr['c_alias']);
	if ($category && $category['id']<>$_POST['id'])
	{
	adminmsg("�������Ѿ����ڣ�",0);
	}
	else
	{
	$link[0]['text'] = "���ط������";
	$link[0]['href'] = '?act=category';
	!updatetable(table('link_category'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
	}	
}
elseif($act == 'del_category')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"link_category");
	$id=$_REQUEST['id'];
	if ($num=del_category($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'link_set')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"link_set");
	$smarty->assign('config',$_CFG);
	$smarty->assign('text',get_cache('text'));
	$smarty->assign('navlabel',"link_set");
	$smarty->display('link/admin_link_set.htm');
}
elseif($act == 'link_set_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"mb_set");
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('config')." SET value='$v' WHERE name='$k'")?adminmsg('��������ʧ��', 1):"";
	}
	foreach($_POST as $k => $v)
	{
	!$db->query("UPDATE ".table('text')." SET value='$v' WHERE name='$k'")?adminmsg('��������ʧ��', 1):"";
	}
	refresh_cache('config');
	refresh_cache('text');
	adminmsg("����ɹ���",2);
}
?>