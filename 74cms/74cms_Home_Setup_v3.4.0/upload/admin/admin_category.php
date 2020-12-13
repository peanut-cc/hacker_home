<?php
 /*
 * 74cms ����
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
require_once(ADMIN_ROOT_PATH.'include/admin_category_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'district';
check_permissions($_SESSION['admin_purview'],"site_category");
$smarty->assign('pageheader',"�������");
if($act == 'grouplist')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('group',get_category_group());
	$smarty->display('category/admin_category_group.htm');
}
elseif($act == 'add_group')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->display('category/admin_category_group_add.htm');
}
elseif($act == 'add_group_save')
{
	check_token();
	$setsqlarr['g_name']=!empty($_POST['g_name']) ?trim($_POST['g_name']) : adminmsg("����д������",1);
	$setsqlarr['g_alias']=!empty($_POST['g_alias']) ?trim($_POST['g_alias']) : adminmsg("����д������",1);
	$info=get_category_group_one($setsqlarr['g_alias']);
	if (empty($info))
	{
		if (stripos($setsqlarr['g_alias'],"qs_")===0)
		{
			adminmsg("�����������á�qs_����ͨ",0);
		}
		else
		{
			$link[0]['text'] = "�������б�";
			$link[0]['href'] = '?act=grouplist';
			$link[1]['text'] = "������ӷ�����";
			$link[1]['href'] = "?act=add_group";
			inserttable(table('category_group'),$setsqlarr)?adminmsg("��ӳɹ���",2,$link):adminmsg("���ʧ�ܣ�",0);			
		}
	}
	else
	{
	 adminmsg("���ʧ��,���������ظ�",0);
	}
}
elseif($act == 'edit_group')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('group',get_category_group_one($_GET['alias']));
	$smarty->display('category/admin_category_group_edit.htm');
}
elseif($act == 'edit_group_save')
{
	check_token();
	$setsqlarr['g_name']=!empty($_POST['g_name']) ?trim($_POST['g_name']) : adminmsg("����д������",1);
	$setsqlarr['g_alias']=!empty($_POST['g_alias']) ?trim($_POST['g_alias']) : adminmsg("����д������",1);
	$info=get_category_group_one($setsqlarr['g_alias']);
	if (empty($info) || $info['g_id']==intval($_POST['g_id']))
	{
		if (stripos($setsqlarr['g_alias'],"qs_")===0)
		{
			adminmsg("�����������á�qs_����ͨ",0);
		}
		else
		{
			$link[0]['text'] = "�������б�";
			$link[0]['href'] = '?act=grouplist';
			$link[1]['text'] = "�鿴�޸Ľ��";
			$link[1]['href'] = "?act=edit_group&alias=".$setsqlarr['g_alias'];
			updatetable(table('category_group'),$setsqlarr," g_id=".intval($_POST['g_id']))?'':adminmsg("�޸�ʧ�ܣ�",0);
			//ͬʱ�޸ķ������µķ������
			$catarr['c_alias']=$setsqlarr['g_alias'];
			updatetable(table('category'),$catarr," c_alias='".$_POST['old_g_alias']."'")?'':adminmsg("�޸�ʧ�ܣ�",0);
			adminmsg("�޸ĳɹ���",2,$link);						
		}
	}
	else
	{
	 adminmsg("���ʧ��,���������ظ�",0);
	}
}
elseif($act == 'del_group')
{
	check_token();
	$alias=$_REQUEST['alias'];
	if ($num=del_group($alias))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'show_category')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('group',get_category_group_one($_GET['alias']));
	$smarty->assign('category',get_category($_GET['alias']));	
	$smarty->display('category/admin_category_list.htm');
}
elseif($act == 'category_save')
{
	check_token();
	if (is_array($_POST['c_id']) && count($_POST['c_id'])>0)
	{
		for ($i =0; $i <count($_POST['c_id']);$i++){
			if (!empty($_POST['c_name'][$i]))
			{	
				$setsqlarr['c_name']=trim($_POST['c_name'][$i]);
				$setsqlarr['c_order']=intval($_POST['c_order'][$i]);
				$setsqlarr['c_index']=getfirstchar($setsqlarr['c_name']);
				!updatetable(table('category'),$setsqlarr," c_id=".intval($_POST['c_id'][$i]))?adminmsg("���ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}

	}
	refresh_category_cache();
	makejs_classify();
 	adminmsg("�޸���ɣ�",2);
}
elseif($act == 'add_category')
{
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('group',get_category_group_one($_GET['alias']));
	$smarty->display('category/admin_category_add.htm');
}
elseif($act == 'add_category_save')
{
	check_token();
	$num=0;
	if (is_array($_POST['c_name']) && count($_POST['c_name'])>0)
	{
		for ($i =0; $i <count($_POST['c_name']);$i++){
			if (!empty($_POST['c_name'][$i]))
			{		
				$setsqlarr['c_name']=trim($_POST['c_name'][$i]);
				$setsqlarr['c_alias']=trim($_POST['c_alias'][$i]);
				$setsqlarr['c_order']=intval($_POST['c_order'][$i]);
				$setsqlarr['c_index']=getfirstchar($setsqlarr['c_name']);
				$setsqlarr['c_note']=trim($_POST['c_note'][$i]);				
				!inserttable(table('category'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}

	}
	if ($num==0)
	{
	adminmsg("���ʧ��,���ݲ�����",1);
	}
	else
	{
	$link[0]['text'] = "���ط����б�";
	$link[0]['href'] = "?act=show_category&alias=".$setsqlarr['c_alias'];
	$link[1]['text'] = "������ӷ���";
	$link[1]['href'] = "?act=add_category&alias=".$setsqlarr['c_alias'];
	refresh_category_cache();
	makejs_classify();
 	adminmsg("��ӳɹ��������".$num."������",2,$link);
	}
}
elseif($act == 'edit_category')
{	
	get_token();
	$smarty->assign('navlabel',"group");
	$smarty->assign('category',get_category_one($_GET['id']));
	$smarty->display('category/admin_category_edit.htm');
}
elseif($act == 'edit_category_save')
{
	check_token();
	$setsqlarr['c_name']=!empty($_POST['c_name']) ?trim($_POST['c_name']) : adminmsg("����д����",1);
	$setsqlarr['c_order']=intval($_POST['c_order']);
	$setsqlarr['c_parentid']=intval($_POST['c_parentid']);
	$setsqlarr['c_index']=getfirstchar($setsqlarr['c_name']);
	$setsqlarr['c_note']=trim($_POST['c_note']);				
	!updatetable(table('category'),$setsqlarr," c_id=".intval($_POST['c_id']))?adminmsg("����ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=show_category&alias='.$_POST['c_alias'];
	$link[1]['text'] = "�鿴�޸Ľ��";
	$link[1]['href'] = "?act=edit_category&id=".intval($_POST['c_id']);
	refresh_category_cache();
	makejs_classify();
 	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'del_category')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_category($id))
	{
	refresh_category_cache();
	makejs_classify();
 	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
//����--------------
elseif($act == 'district')
{
	get_token();
	$smarty->assign('navlabel',"district");
	$smarty->assign('district',get_category_district());
	$smarty->display('category/admin_category_district.htm');
}
elseif($act == 'district_all_save')
{
	check_token();
	if (is_array($_POST['save_id']) && count($_POST['save_id'])>0)
	{
		foreach($_POST['save_id'] as $k=>$v)
		{
		 
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$k]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$k]);
				!updatetable(table('category_district'),$setsqlarr," id=".intval($_POST['save_id'][$k]))?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
 
		}
	}
	//���������
	if (is_array($_POST['add_pid']) && count($_POST['add_pid'])>0)
	{
		for ($i =0; $i <count($_POST['add_pid']);$i++){
			if (!empty($_POST['add_categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['add_categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['add_category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['add_pid'][$i]);	
				!inserttable(table('category_district'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	makejs_classify();
 	adminmsg("����ɹ���",2);
}
elseif($act == 'del_district')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_district($id))
	{
	makejs_classify();
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_district')
{
	get_token();
	$smarty->assign('navlabel',"district");
	$smarty->assign('district',get_category_district_one($_GET['id']));
	$smarty->display('category/admin_category_district_edit.htm');
}
elseif($act == 'edit_district_save')
{
	check_token();
	$setsqlarr['categoryname']=!empty($_POST['categoryname']) ?trim($_POST['categoryname']) : adminmsg("����д����",1);
	$setsqlarr['category_order']=intval($_POST['category_order']);
	$setsqlarr['parentid']=intval($_POST['parentid']);				
	!updatetable(table('category_district'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=district';
	makejs_classify();
 	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'add_district')
{
	get_token();
	$smarty->assign('navlabel',"district");
	$smarty->display('category/admin_category_district_add.htm');
}
elseif($act == 'add_district_save')
{
	check_token();
	//���������
	if (is_array($_POST['categoryname']) && count($_POST['categoryname'])>0)
	{
		for ($i =0; $i <count($_POST['categoryname']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['parentid'][$i]);	
				!inserttable(table('category_district'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=district';
	makejs_classify();
 	adminmsg("��ӳɹ������������{$num}������",2,$link);	
}
///////---------------ְλ����
elseif($act == 'jobs')
{
	get_token();
	$smarty->assign('navlabel',"jobs");
	$smarty->assign('district',get_category_jobs());
	$smarty->display('category/admin_category_jobs.htm');
}
elseif($act == 'jobs_all_save')
{
	check_token();
	if (is_array($_POST['save_id']) && count($_POST['save_id'])>0)
	{
		for ($i =0; $i <count($_POST['save_id']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);				
				!updatetable(table('category_jobs'),$setsqlarr," id=".intval($_POST['save_id'][$i]))?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}
		}
	}
	//���������
	if (is_array($_POST['add_pid']) && count($_POST['add_pid'])>0)
	{
		for ($i =0; $i <count($_POST['add_pid']);$i++){
			if (!empty($_POST['add_categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['add_categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['add_category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['add_pid'][$i]);	
				!inserttable(table('category_jobs'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	makejs_classify();
 	adminmsg("����ɹ���",2);
}
elseif($act == 'del_jobs_category')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_jobs_category($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_jobs_category')
{
	get_token();
	$smarty->assign('navlabel',"jobs");
	$smarty->assign('category',get_category_jobs_one($_GET['id']));
	$smarty->display('category/admin_category_jobs_edit.htm');
}
elseif($act == 'edit_jobs_category_save')
{
	check_token();
	$setsqlarr['categoryname']=!empty($_POST['categoryname']) ?trim($_POST['categoryname']) : adminmsg("����д����",1);
	$setsqlarr['category_order']=intval($_POST['category_order']);
	$setsqlarr['parentid']=intval($_POST['parentid']);				
	!updatetable(table('category_jobs'),$setsqlarr," id=".intval($_POST['id']))?adminmsg("�޸�ʧ�ܣ�",0):"";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=jobs';
	makejs_classify();
 	adminmsg("����ɹ���",2,$link);
}
elseif($act == 'add_category_jobs')
{
	get_token();
	$smarty->assign('navlabel',"jobs");
	$smarty->display('category/admin_category_jobs_add.htm');
}
elseif($act == 'add_category_jobs_save')
{
	check_token();
	//���������
	if (is_array($_POST['categoryname']) && count($_POST['categoryname'])>0)
	{
		for ($i =0; $i <count($_POST['categoryname']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{	
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);
				$setsqlarr['parentid']=intval($_POST['parentid'][$i]);	
				!inserttable(table('category_jobs'),$setsqlarr)?adminmsg("����ʧ�ܣ�",0):"";
				$num=$num+$db->affected_rows();
			}

		}
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?act=jobs';
	makejs_classify();
 	adminmsg("��ӳɹ������������".$num."������",2,$link);	
}


?>