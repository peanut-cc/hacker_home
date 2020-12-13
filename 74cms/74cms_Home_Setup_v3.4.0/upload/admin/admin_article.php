<?php
 /*
 * 74cms ����ҳ��
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
require_once(ADMIN_ROOT_PATH.'include/admin_article_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'newslist';
$smarty->assign('act',$act);
if($act == 'newslist')
{
	check_permissions($_SESSION['admin_purview'],"article_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$oederbysql=" order BY a.article_order DESC,a.id DESC";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE a.title like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE a.id =".intval($key);
	}	
	!empty($_GET['parentid'])? $wheresqlarr['a.parentid']=intval($_GET['parentid']):'';
	!empty($_GET['type_id'])? $wheresqlarr['a.type_id']=intval($_GET['type_id']):'';
	!empty($_GET['focos'])?$wheresqlarr['a.focos']=intval($_GET['focos']):'';	
	if (!empty($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE a.addtime> ".$settr:$wheresql." AND a.addtime> ".$settr;
		$oederbysql=" order BY a.addtime DESC";
	}
		if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_news']=="1")
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND ";
			$wheresql.=" (a.subsite_id=0 OR a.subsite_id=".intval($_CFG['subsite_id']).") ";
		}
	$joinsql=" LEFT JOIN ".table('article_category')." AS c ON a.type_id=c.id  LEFT JOIN ".table('article_property')." AS p ON a.focos=p.id ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('article')." AS a ".$joinsql.$wheresql;
	$page = new page(array('total'=>$db->get_total($total_sql), 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$article = get_news($offset, $perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('article',$article);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('pageheader',"������Ѷ");
	get_token();
	$smarty->display('article/admin_article.htm');
}
elseif($act =='migrate_article')
{
	$id=$_REQUEST['id'];
	if (empty($id)) adminmsg("��ѡ����Ŀ��",1);
	check_token();
	check_permissions($_SESSION['admin_purview'],"article_del");
	if (del_news($id))
	{
	adminmsg("ɾ���ɹ���",2);
	}
}
elseif($act == 'news_add')
{
	check_permissions($_SESSION['admin_purview'],"article_add");
	$smarty->assign('article_category',get_article_category());
	$smarty->assign('author',$_SESSION['admin_name']);
	$smarty->assign('pageheader',"������Ѷ");
 	get_token();
	$smarty->display('article/admin_article_add.htm');
}
elseif($act == 'addsave')
{
	check_permissions($_SESSION['admin_purview'],"article_add");
	check_token();
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('��û����д���⣡',1);
	$setsqlarr['type_id']=!empty($_POST['type_id'])?intval($_POST['type_id']):adminmsg('��û��ѡ����࣡',1);
	$setsqlarr['content']=!empty($_POST['content'])?$_POST['content']:adminmsg('��û�����ݣ�',1);
	$setsqlarr['tit_color']=trim($_POST['tit_color']);
	$setsqlarr['tit_b']=intval($_POST['tit_b']);
	$setsqlarr['author']=trim($_POST['author']);
	$setsqlarr['source']=trim($_POST['source']);
	$setsqlarr['focos']=intval($_POST['focos']);
	$setsqlarr['is_display']=intval($_POST['is_display']);
	$setsqlarr['is_url']=trim($_POST['is_url']);
	$setsqlarr['seo_keywords']=$_POST['seo_keywords'];
	$setsqlarr['seo_description']=$_POST['seo_description'];
	$setsqlarr['article_order']=intval($_POST['article_order']);
	if($_FILES['Small_img']['name'])
	{
		$upfiles_dir.=date("Y/m/d/");
		make_dir($upfiles_dir);
		$Small_img=_asUpFiles($upfiles_dir, "Small_img", 1024*2, 'jpg/gif/png',true);
		$makefile=$upfiles_dir.$Small_img;
		make_dir($thumb_dir.date("Y/m/d/"));
		makethumb($makefile,$thumb_dir.date("Y/m/d/"),$thumbwidth,$thumbheight);
		$setsqlarr['Small_img']=date("Y/m/d/").$Small_img;
	}
	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['parentid']=get_article_parentid($setsqlarr['type_id']);
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$link[0]['text'] = "�����������";
	$link[0]['href'] = '?act=news_add&type_id_cn='.trim($_POST['type_id_cn'])."&type_id=".$_POST['type_id'];
	$link[1]['text'] = "���������б�";
	$link[1]['href'] = '?act=newslist';
	!inserttable(table('article'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
}
elseif($act == 'article_edit')
{
	check_permissions($_SESSION['admin_purview'],"article_edit");
	$id=intval($_GET['id']);
	$sql = "select * from ".table('article')." where id=".intval($id)." LIMIT 1";
	$edit_article=$db->getone($sql);
	$smarty->assign('edit_article',$edit_article); 
	$smarty->assign('upfiles_dir',$upfiles_dir); 
	$smarty->assign('thumb_dir',$thumb_dir); 
	$smarty->assign('article_category',get_article_category());
 	$smarty->assign('pageheader',"������Ѷ");
	get_token();
	$smarty->display('article/admin_article_edit.htm');
}
elseif($act == 'editsave')
{
	check_permissions($_SESSION['admin_purview'],"article_edit");
	check_token();
	$id=intval($_POST['id']);
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('��û����д���⣡',1);
	$setsqlarr['type_id']=trim($_POST['type_id'])?intval($_POST['type_id']):0;
	$setsqlarr['content']=!empty($_POST['content'])?$_POST['content']:adminmsg('��û�����ݣ�',1);
	$setsqlarr['tit_color']=trim($_POST['tit_color']);
	$setsqlarr['tit_b']=intval($_POST['tit_b']);
	$setsqlarr['author']=trim($_POST['author']);
	$setsqlarr['source']=trim($_POST['source']);
	$setsqlarr['focos']=intval($_POST['focos']);
	$setsqlarr['is_display']=intval($_POST['is_display']);
	$setsqlarr['is_url']=trim($_POST['is_url']);
	$setsqlarr['seo_keywords']=$_POST['seo_keywords'];
	$setsqlarr['seo_description']=$_POST['seo_description'];
	$setsqlarr['article_order']=intval($_POST['article_order']);
	if($_FILES['Small_img']['name'])
	{
		$upfiles_dir.=date("Y/m/d/");
		make_dir($upfiles_dir);
		$Small_img=_asUpFiles($upfiles_dir, "Small_img", 1024*2, 'jpg/gif/png',true);
		$makefile=$upfiles_dir.$Small_img;
		make_dir($thumb_dir.date("Y/m/d/"));
		makethumb($makefile,$thumb_dir.date("Y/m/d/"),$thumbwidth,$thumbheight);
		$setsqlarr['Small_img']=date("Y/m/d/").$Small_img;
	}
	$setsqlarr['parentid']=get_article_parentid($setsqlarr['type_id']);
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$link[0]['text'] = "���������б�";
	$link[0]['href'] = '?act=newslist';
	$link[1]['text'] = "�鿴���޸�����";
	$link[1]['href'] = "?act=article_edit&id=".$id;
	!updatetable(table('article'),$setsqlarr," id=".$id."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'del_img')
{
	check_token();
	$id=intval($_GET['id']);
	$img=$_GET['img'];
	$img=str_replace("../","***",$img);
	$sql="update ".table('article')." set Small_img='' where id=".$id." LIMIT 1";
	$db->query($sql);
	@unlink($upfiles_dir.$img);
	@unlink($thumb_dir.$img);
	adminmsg("ɾ������ͼ�ɹ���",2);
}
elseif($act == 'property'){
	check_permissions($_SESSION['admin_purview'],"article_property");
	$smarty->assign('pageheader',"������Ѷ");
	get_token();
	$smarty->display('article/admin_article_property.htm');
}
elseif($act == 'property_add')
{
	check_permissions($_SESSION['admin_purview'],"article_property");
	$smarty->assign('pageheader',"������Ѷ");
	get_token();
	$smarty->display('article/admin_article_property_add.htm');
}
elseif($act == 'add_property_save')
{
	check_permissions($_SESSION['admin_purview'],"article_property");
	check_token();
	$num=0;
	if (is_array($_POST['categoryname']) && count($_POST['categoryname'])>0)
	{
		for ($i =0; $i <count($_POST['categoryname']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{		
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);				
				!inserttable(table('article_property'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):"";
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
	$link[0]['text'] = "�������Թ���ҳ��";
	$link[0]['href'] = '?act=property';
	$link[1]['text'] = "�����������";
	$link[1]['href'] = "?act=property_add";
	adminmsg("��ӳɹ��������".$num."������",2,$link);
	}
}
elseif($act == 'del_property')
{
	check_permissions($_SESSION['admin_purview'],"article_property");
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_property($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."������",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_property')
{	
	check_permissions($_SESSION['admin_purview'],"article_property");
	$id=intval($_GET['id']);
	$smarty->assign('property',get_article_property_one($id));
	$smarty->assign('pageheader',"������Ѷ");
	get_token();
	$smarty->display('article/admin_article_property_edit.htm');
}
elseif($act == 'edit_property_save')
{
	check_permissions($_SESSION['admin_purview'],"article_property");
	check_token();
	$id=intval($_POST['id']);
	$setsqlarr['categoryname']=trim($_POST['categoryname'])?trim($_POST['categoryname']):adminmsg('����д�������ƣ�',1);
	$setsqlarr['category_order']=intval($_POST['category_order']);	
	$link[0]['text'] = "�鿴�޸Ľ��";
	$link[0]['href'] = '?act=edit_property&id='.$id;
	$link[1]['text'] = "�������Թ���";
	$link[1]['href'] = '?act=property';
	!updatetable(table('article_property'),$setsqlarr," id=".$id."")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'category')
{
	check_permissions($_SESSION['admin_purview'],"article_category");
	$smarty->assign('pageheader',"������Ѷ");
	get_token();
	$smarty->display('article/admin_article_category.htm');
}
elseif($act == 'category_add')
{
	check_permissions($_SESSION['admin_purview'],"article_category");
	$parentid = !empty($_GET['parentid']) ? intval($_GET['parentid']) : '0';
	$smarty->assign('pageheader',"������Ѷ");
	get_token();
	$smarty->display('article/admin_article_category_add.htm');
}
elseif($act == 'add_category_save')
{
	check_permissions($_SESSION['admin_purview'],"article_category");
	check_token();
	$num=0;
	if (is_array($_POST['categoryname']) && count($_POST['categoryname'])>0)
	{
		for ($i =0; $i <count($_POST['categoryname']);$i++){
			if (!empty($_POST['categoryname'][$i]))
			{		
				$setsqlarr['categoryname']=trim($_POST['categoryname'][$i]);
				$setsqlarr['parentid']=intval($_POST['parentid'][$i]);
				$setsqlarr['category_order']=intval($_POST['category_order'][$i]);	
				$setsqlarr['title']=$_POST['title'][$i];
				$setsqlarr['description']=$_POST['description'][$i];
				$setsqlarr['keywords']=$_POST['keywords'][$i];
				!inserttable(table('article_category'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):"";
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
	$link[1]['text'] = "������ӷ���";
	$link[1]['href'] = "?act=category_add";
	adminmsg("��ӳɹ��������".$num."������",2,$link);
	}
}
elseif($act == 'del_category')
{
	check_permissions($_SESSION['admin_purview'],"article_category");
	check_token();
	$id=$_REQUEST['id'];
	if ($num=del_category($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."������",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",1);
	}
}
elseif($act == 'edit_category')
{	
	check_permissions($_SESSION['admin_purview'],"article_category");
	$id=intval($_GET['id']);
	$smarty->assign('category',get_article_category_one($id));
	$smarty->assign('pageheader',"������Ѷ");
	get_token();
	$smarty->display('article/admin_article_category_edit.htm');
}
elseif($act == 'edit_category_save')
{
	check_permissions($_SESSION['admin_purview'],"article_category");
	check_token();
	$id=intval($_POST['id']);
	$setsqlarr['parentid']=trim($_POST['parentid'])?intval($_POST['parentid']):0;
	$setsqlarr['categoryname']=trim($_POST['categoryname'])?trim($_POST['categoryname']):adminmsg('����д�������ƣ�',1);
	$setsqlarr['category_order']=!empty($_POST['category_order'])?intval($_POST['category_order']):0;	
	$setsqlarr['title']=$_POST['title'];
    $setsqlarr['description']=$_POST['description'];
	$setsqlarr['keywords']=$_POST['keywords'];
	$link[0]['text'] = "�鿴�޸Ľ��";
	$link[0]['href'] = '?act=edit_category&id='.$id;
	$link[1]['text'] = "���ط������";
	$link[1]['href'] = '?act=category';
	!updatetable(table('article_category'),$setsqlarr," id='".$id."'")?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
?>