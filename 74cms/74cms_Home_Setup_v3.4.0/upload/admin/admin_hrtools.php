<?php
 /*
 * 74cms HR������
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
require_once(ADMIN_ROOT_PATH.'include/admin_hrtools_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
check_permissions($_SESSION['admin_purview'],"hrtools");
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'list';
$hrtools_updir="../data/hrtools/";
$hrtools_dir="data/hrtools/";
$smarty->assign('pageheader',"HR������");
if($act == 'list')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY h.h_order DESC,h_id DESC";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE h.h_filename like '%{$key}%'";
		$oederbysql="";
	}
	!empty($_GET['h_typeid'])? $wheresqlarr['h.h_typeid']=intval($_GET['h_typeid']):'';
	if (!empty($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	$joinsql=" LEFT JOIN  ".table('hrtools_category')." AS c ON h.h_typeid=c.c_id ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('hrtools')." AS h ".$joinsql.$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$hrtools = get_hrtools($offset, $perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('category',get_hrtools_category());
	$smarty->assign('hrtools',$hrtools);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('navlabel',"list");
	$smarty->display('hrtools/admin_hrtools.htm');
}
elseif($act == 'edit')
{
	get_token();
	$id = intval($_GET['id']);
	$sql = "select * from ".table('hrtools')." AS h LEFT JOIN ".table('hrtools_category')." AS c ON h.h_typeid=c.c_id where h.h_id='{$id}' LIMIT 1";
	$show=$db->getone($sql);
	$smarty->assign('show',$show);
	$smarty->assign('category',get_hrtools_category());
	$smarty->display('hrtools/admin_hrtools_edit.htm');
}
elseif($act == 'editsave')
{
	check_token();
	$setsqlarr['h_filename']=!empty($_POST['h_filename'])?trim($_POST['h_filename']):adminmsg('�ĵ����Ʋ���Ϊ�գ�',1);
	$setsqlarr['h_typeid']=intval($_POST['h_typeid'])>0?intval($_POST['h_typeid']):adminmsg('��ѡ����࣡',1);
	$setsqlarr['h_color']=trim($_POST['h_color']);
	$setsqlarr['h_strong']=intval($_POST['h_strong']);
	$setsqlarr['h_order']=intval($_POST['h_order']);
	if (empty($_FILES['upfile']['name']) && empty($_POST['url']))
	{
	adminmsg('���ϴ��ļ�������д�ļ�·����',1);
	}
	if ($_FILES['upfile']['name'])
		{
			$hrtools_updir=$hrtools_updir.date("Y/m/");
			make_dir($hrtools_updir);
			$setsqlarr['h_fileurl']=_asUpFiles($hrtools_updir,"upfile",3000,'doc/ppt/xls/rtf',true);
			if (empty($setsqlarr['h_fileurl']))
			{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['h_fileurl']=$hrtools_dir.date("Y/m/").$setsqlarr['h_fileurl'];
		}
		else
		{
			$setsqlarr['h_fileurl']=trim($_POST['url']);
		}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = '?';
	!updatetable(table('hrtools'),$setsqlarr," h_id=".intval($_POST['id'])."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'add')
{
	get_token();
	$smarty->assign('category',get_hrtools_category());
	$smarty->assign('navlabel',"add");
	$smarty->display('hrtools/admin_hrtools_add.htm');
}
elseif($act == 'addsave')
{	
	check_token();
	$setsqlarr['h_filename']=!empty($_POST['h_filename'])?trim($_POST['h_filename']):adminmsg('�ĵ����Ʋ���Ϊ�գ�',1);
	$setsqlarr['h_typeid']=intval($_POST['h_typeid'])>0?intval($_POST['h_typeid']):adminmsg('��ѡ����࣡',1);
	$setsqlarr['h_color']=trim($_POST['h_color']);
	$setsqlarr['h_strong']=intval($_POST['h_strong']);
	$setsqlarr['h_order']=intval($_POST['h_order']);
	if (empty($_FILES['upfile']['name']) && empty($_POST['url']))
	{
	adminmsg('���ϴ��ļ�������д�ļ�·����',1);
	}
	if ($_FILES['upfile']['name'])
		{
			$hrtools_updir=$hrtools_updir.date("Y/m/");
			make_dir($hrtools_updir);
			$setsqlarr['h_fileurl']=_asUpFiles($hrtools_updir,"upfile",3000,'doc/ppt/xls/rtf',true);
			if (empty($setsqlarr['h_fileurl']))
			{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['h_fileurl']=$hrtools_dir.date("Y/m/").$setsqlarr['h_fileurl'];
		}
		else
		{
			$setsqlarr['h_fileurl']=trim($_POST['url']);
		}
	$link[0]['text'] = "�������";
	$link[0]['href'] = "?act=add&h_typeid={$setsqlarr['h_typeid']}&h_typeid_cn={$_POST['h_typeid_cn']}";
	$link[1]['text'] = "�����б�";
	$link[1]['href'] = '?';
	!inserttable(table('hrtools'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
}
elseif($act == 'hrtools_del')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_hrtools($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
elseif($act == 'category')
{
	get_token();
	$smarty->assign('category',get_hrtools_category());
	$smarty->assign('navlabel',"category");
	$smarty->display('hrtools/admin_hrtools_category.htm');
}
elseif($act == 'category_add')
{
	get_token();
	$smarty->assign('navlabel',"category");
	$smarty->display('hrtools/admin_hrtools_category_add.htm');
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
				$setsqlarr['c_order']=intval($_POST['c_order'][$i]);	
				$setsqlarr['c_adminset']=0;		
				!inserttable(table('hrtools_category'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):"";
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
	$link[0]['text'] = "���ط������";
	$link[0]['href'] = '?act=category';
	$link[1]['text'] = "�������";
	$link[1]['href'] = "?act=category_add";
	adminmsg("��ӳɹ��������".$num."������",2,$link);
	}
}
elseif($act == 'edit_category')
{
	get_token();
	$id=intval($_GET['id']);
	$smarty->assign('category',get_hrtools_category_one($id));
	$smarty->assign('navlabel',"category");
	$smarty->display('hrtools/admin_hrtools_category_edit.htm');
}
elseif($act == 'edit_category_save')
{
	check_token();
	$id=intval($_POST['id']);	
	$setsqlarr['c_name']=!empty($_POST['c_name'])?trim($_POST['c_name']):adminmsg('����д�������ƣ�',1);
	$setsqlarr['c_order']=intval($_POST['c_order']);
	$link[0]['text'] = "�鿴�޸Ľ��";
	$link[0]['href'] = '?act=edit_category&id='.$id;
	$link[1]['text'] = "���ط������";
	$link[1]['href'] = '?act=category';
	!updatetable(table('hrtools_category'),$setsqlarr," c_id=".$id."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'del_category')
{
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_hrtools_category($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}

?>