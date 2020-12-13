<?php
 /*
 * 74cms ������
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
require_once(ADMIN_ROOT_PATH.'include/admin_ad_fun.php');
require_once(ADMIN_ROOT_PATH.'include/upload.php');
$ads_updir="../data/comads/";
$ads_dir=$_CFG['site_dir']."data/comads/";
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'list';
if($act == 'list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ad_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE a.title like '%{$key}%'";
	}
	else
	{
		$category_id=isset($_GET['category_id'])?intval($_GET['category_id']):"";
		if ($category_id>0)
		{
		$wheresql=empty($wheresql)?" WHERE a.category_id= ".$category_id:$wheresql." AND a.category_id= ".$category_id;
		}
		$settr=$_GET['settr'];
		if ($settr<>"")
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND  ";
			$days=intval($settr);
			$settr=strtotime($days." day");
			if ($days===0)
			{
			$wheresql.=" a.deadline< ".time()." AND a.deadline>0 ";
			}
			else
			{
			$wheresql.=" a.deadline< ".$settr." AND  a.deadline>".time()." ";
			}		
		}
		$is_display=isset($_GET['is_display'])?$_GET['is_display']:"";
		if ($is_display<>'')
		{
		$is_display=intval($is_display);
		$wheresql=empty($wheresql)?" WHERE a.is_display= ".$is_display:$wheresql." AND a.is_display= ".$is_display;
		}
	}
		if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_ad']=="1")
		{
			$wheresql.=empty($wheresql)?" WHERE ":" AND ";
			$wheresql.=" (a.subsite_id=0 OR a.subsite_id=".intval($_CFG['subsite_id']).") ";
		}
	$joinsql=" LEFT JOIN  ".table('ad_category')." AS c ON  a.category_id=c.id ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('ad')." AS a " .$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$smarty->assign('list',get_ad_list($offset,$perpage,$joinsql.$wheresql));
	$smarty->assign('ad_category',get_ad_category());
	$smarty->assign('page',$page->show(3));
	$smarty->assign('total',$total_val);
	$smarty->assign('pageheader',"������");	
	$smarty->display('ads/admin_ad_list.htm');
}
//��ӹ��
elseif($act == 'ad_add')
{
	check_permissions($_SESSION['admin_purview'],"ad_add");
	$smarty->assign('datefm',convert_datefm(time(),1));
	$smarty->assign('ad_category',get_ad_category());
 	$smarty->assign('pageheader',"������");
	get_token();
	$smarty->display('ads/admin_ad_add.htm');
}
//������ӹ��
elseif($act == 'ad_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"ad_add");
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('��û����д���⣡',1);
	$setsqlarr['is_display']=trim($_POST['is_display'])?trim($_POST['is_display']):0;
	$setsqlarr['category_id']=trim($_POST['category_id'])?trim($_POST['category_id']):adminmsg('��û����д�����࣡',1);
	$setsqlarr['type_id']=trim($_POST['type_id'])?trim($_POST['type_id']):adminmsg('��û����д������ͣ�',1);
	$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('�������󣬵���ID�����ڣ�',1);
	$setsqlarr['show_order']=intval($_POST['show_order']);
	$setsqlarr['note']=trim($_POST['note']);	
		if ($_POST['starttime']=="")
		{
		$setsqlarr['starttime']=0;
		}
		else
		{
		$setsqlarr['starttime']=intval(convert_datefm($_POST['starttime'],2));
		}
		if ($_POST['deadline']=="")
		{
		$setsqlarr['deadline']=0;
		}
		else
		{
		$setsqlarr['deadline']=intval(convert_datefm($_POST['deadline'],2));
		}
	//����
	if ($setsqlarr['type_id']=="1")
	{
	$setsqlarr['text_content']=trim($_POST['text_content'])?trim($_POST['text_content']):adminmsg('��û����д�������ݣ�',1);
	$setsqlarr['text_url']=trim($_POST['text_url']);
	$setsqlarr['text_color']=trim($_POST['tit_color']);
	}
	//ͼƬ
	elseif ($setsqlarr['type_id']=="2")
	{
		if (empty($_FILES['img_file']['name']) && empty($_POST['img_path']))
		{
		adminmsg('���ϴ�ͼƬ������дͼƬ·����',1);
		}
		if ($_FILES['img_file']['name'])
		{
			$ads_updir=$ads_updir.date("Y/m/d/");
			make_dir($ads_updir);
			$setsqlarr['img_path']=_asUpFiles($ads_updir,"img_file",1000,'gif/jpg/bmp/png',true);
			if (empty($setsqlarr['img_path']))
			{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['img_path']=$ads_dir.date("Y/m/d/").$setsqlarr['img_path'];
		}
		else
		{
			$setsqlarr['img_path']=trim($_POST['img_path']);
		}
	$setsqlarr['img_url']=trim($_POST['img_url']);
	$setsqlarr['img_explain']=trim($_POST['img_explain']);
	$setsqlarr['img_uid']=intval($_POST['img_uid']);
	}
	//����
	elseif ($setsqlarr['type_id']=="3")
	{
	$setsqlarr['code_content']=trim($_POST['code_content'])?trim($_POST['code_content']):adminmsg('��û����д���룡',1);
	}
	//FLASH
	elseif ($setsqlarr['type_id']=="4")
	{
	$setsqlarr['flash_width']=!empty($_POST['flash_width'])?intval($_POST['flash_width']):adminmsg('��û����дflash��ȣ�',1);
	$setsqlarr['flash_height']=!empty($_POST['flash_height'])?intval($_POST['flash_height']):adminmsg('��û����дflash�߶ȣ�',1);
		if (empty($_FILES['flash_file']['name']) && empty($_POST['flash_path']))
			{
			adminmsg('���ϴ�FLASH������дFLASH·����',1);
			}
			if ($_FILES['flash_file']['name'])
			{
				$ads_updir=$ads_updir.date("Y/m/d/");
				make_dir($ads_updir);
				$setsqlarr['flash_path']=_asUpFiles($ads_updir,"flash_file",1000,'swf/SWF',true);
				if (empty($setsqlarr['flash_path']))
				{
				adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
				}
				$setsqlarr['flash_path']=$ads_dir.date("Y/m/d/").$setsqlarr['flash_path'];
			}
			else
			{
				$setsqlarr['flash_path']=trim($_POST['flash_path']);
			}
	}
	//����
	elseif ($setsqlarr['type_id']=="5")
	{
	$setsqlarr['floating_type']=$_POST['floating_type']?trim($_POST['floating_type']):1;	
	$setsqlarr['floating_url']=trim($_POST['floating_url']);
	$setsqlarr['floating_width']=$_POST['floating_width']?intval($_POST['floating_width']):adminmsg('��û����д��ȣ�',1);
	$setsqlarr['floating_height']=$_POST['floating_height']?intval($_POST['floating_height']):adminmsg('��û����д�߶ȣ�',1);
	$setsqlarr['floating_left']=$_POST['floating_left']<>""?intval($_POST['floating_left']):"";
	$setsqlarr['floating_right']=$_POST['floating_right']<>""?intval($_POST['floating_right']):"";
	if ($setsqlarr['floating_left']==="" && $setsqlarr['floating_right']==="") adminmsg('��߾���ұ߾�������дһ�',1);
	$setsqlarr['floating_top']=$_POST['floating_top']?intval($_POST['floating_top']):0;
		if (empty($_FILES['floating_file']['name']) && empty($_POST['floating_path']))
		{
		adminmsg('���ϴ��ļ�������д·����',1);
		}
		if ($_FILES['floating_file']['name'])
		{
			if ($setsqlarr['floating_type']==1)
			{
			$filetype="gif/jpg/bmp/png";
			}
			else
			{
			$filetype="swf";
			}
			$ads_updir=$ads_updir.date("Y/m/d/");
			make_dir($ads_updir);
			$setsqlarr['floating_path']=_asUpFiles($ads_updir,"floating_file",1000,$filetype,true);
			if (empty($setsqlarr['floating_path']))
			{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['floating_path']=$ads_dir.date("Y/m/d/").$setsqlarr['floating_path'];
		}
		else
		{
			$setsqlarr['floating_path']=trim($_POST['floating_path']);
		}
	}
	//��Ƶ
	elseif ($setsqlarr['type_id']=="6")
	{
	$setsqlarr['video_width']=$_POST['video_width']?intval($_POST['video_width']):adminmsg('��û����д��ȣ�',1);
	$setsqlarr['video_height']=$_POST['video_height']?intval($_POST['video_height']):adminmsg('��û����д�߶ȣ�',1);
		if (empty($_FILES['video_file']['name']) && empty($_POST['video_path']))
		{
		adminmsg('���ϴ��ļ�������д·����',1);
		}
		if ($_FILES['video_file']['name'])
		{
			$ads_updir=$ads_updir.date("Y/m/d/");
			make_dir($ads_updir);
			$setsqlarr['video_path']=_asUpFiles($ads_updir,"video_file",5000,"swf/flv/f4v",true);
			if (empty($setsqlarr['video_path']))
			{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['video_path']=$ads_dir.date("Y/m/d/").$setsqlarr['video_path'];
		}
		else
		{
			$setsqlarr['video_path']=trim($_POST['video_path']);
		}
	}
	$setsqlarr['addtime']=$timestamp;
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$link[0]['text'] = "�������";
	$link[0]['href'] ="?act=ad_add&category_id=".$_POST['category_id']."&type_id=".$_POST['type_id']."&alias=".$_POST['alias'];
	$link[1]['text'] = "���ع���б�";
	$link[1]['href'] ="?act=";
	!inserttable(table('ad'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
}
//�޸Ĺ��
elseif($act == 'edit_ad')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ad_edit");
	$id=!empty($_GET['id'])?intval($_GET['id']):adminmsg('û�й��id��',1);
	$ad=get_ad_one($id);
	$smarty->assign('ad',$ad);
	$smarty->assign('ad_category',get_ad_category());//���λ�����б�
	$smarty->assign('url',$_SERVER['HTTP_REFERER']);
 	$smarty->assign('pageheader',"������");
	$smarty->display('ads/admin_ad_edit.htm');
	 
}
//����:�޸Ĺ��
elseif($act == 'ad_edit_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"ad_edit");
	$setsqlarr['title']=trim($_POST['title'])?trim($_POST['title']):adminmsg('��û����д���⣡',1);
	$setsqlarr['is_display']=trim($_POST['is_display'])?trim($_POST['is_display']):0;
	$setsqlarr['category_id']=trim($_POST['category_id'])?trim($_POST['category_id']):adminmsg('��û����д�����࣡',1);
	$setsqlarr['type_id']=trim($_POST['type_id'])?trim($_POST['type_id']):adminmsg('��û����д������ͣ�',1);
	$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('�������󣬵���ID�����ڣ�',1);
	$setsqlarr['show_order']=intval($_POST['show_order']);
	$setsqlarr['note']=trim($_POST['note']);	
		if ($_POST['starttime']=="")
		{
		$setsqlarr['starttime']=0;
		}
		else
		{
		$setsqlarr['starttime']=intval(convert_datefm($_POST['starttime'],2));
		}
		if ($_POST['deadline']=="")
		{
		$setsqlarr['deadline']=0;
		}
		else
		{
		$setsqlarr['deadline']=intval(convert_datefm($_POST['deadline'],2));
		}
	//����
	if ($setsqlarr['type_id']=="1")
	{
	$setsqlarr['text_content']=trim($_POST['text_content'])?trim($_POST['text_content']):adminmsg('��û����д�������ݣ�',1);
	$setsqlarr['text_url']=trim($_POST['text_url']);
	$setsqlarr['text_color']=trim($_POST['tit_color']);
	}
	//ͼƬ
	elseif ($setsqlarr['type_id']=="2")
	{
		if (empty($_FILES['img_file']['name']) && empty($_POST['img_path']))
		{
		adminmsg('���ϴ�ͼƬ������дͼƬ·����',1);
		}
		if ($_FILES['img_file']['name'])
		{
			$ads_updir=$ads_updir.date("Y/m/d/");
			make_dir($ads_updir);
			$setsqlarr['img_path']=_asUpFiles($ads_updir,"img_file",1000,'gif/jpg/bmp/png',true);
			if (empty($setsqlarr['img_path']))
			{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['img_path']=$ads_dir.date("Y/m/d/").$setsqlarr['img_path'];
		}
		else
		{
			$setsqlarr['img_path']=trim($_POST['img_path']);
		}
	$setsqlarr['img_url']=trim($_POST['img_url']);
	$setsqlarr['img_explain']=trim($_POST['img_explain']);
	$setsqlarr['img_uid']=intval($_POST['img_uid']);
	}
	//����
	elseif ($setsqlarr['type_id']=="3")
	{
	$setsqlarr['code_content']=trim($_POST['code_content'])?trim($_POST['code_content']):adminmsg('��û����д���룡',1);
	}
	//FLASH
	elseif ($setsqlarr['type_id']=="4")
	{
	$setsqlarr['flash_width']=!empty($_POST['flash_width'])?intval($_POST['flash_width']):adminmsg('��û����дflash��ȣ�',1);
	$setsqlarr['flash_height']=!empty($_POST['flash_height'])?intval($_POST['flash_height']):adminmsg('��û����дflash�߶ȣ�',1);
		if (empty($_FILES['flash_file']['name']) && empty($_POST['flash_path']))
			{
			adminmsg('���ϴ�FLASH������дFLASH·����',1);
			}
			if ($_FILES['flash_file']['name'])
			{
				$ads_updir=$ads_updir.date("Y/m/d/");
				make_dir($ads_updir);
				$setsqlarr['flash_path']=_asUpFiles($ads_updir,"flash_file",1000,'swf/SWF',true);
				if (empty($setsqlarr['flash_path']))
				{
				adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
				}
				$setsqlarr['flash_path']=$ads_dir.date("Y/m/d/").$setsqlarr['flash_path'];
			}
			else
			{
				$setsqlarr['flash_path']=trim($_POST['flash_path']);
			}
	}
	//����
	elseif ($setsqlarr['type_id']=="5")
	{
	$setsqlarr['floating_type']=$_POST['floating_type']?trim($_POST['floating_type']):1;	
	$setsqlarr['floating_url']=trim($_POST['floating_url']);
	$setsqlarr['floating_width']=$_POST['floating_width']?intval($_POST['floating_width']):adminmsg('��û����д��ȣ�',1);
	$setsqlarr['floating_height']=$_POST['floating_height']?intval($_POST['floating_height']):adminmsg('��û����д�߶ȣ�',1);
	$setsqlarr['floating_left']=$_POST['floating_left']<>""?intval($_POST['floating_left']):"";
	$setsqlarr['floating_right']=$_POST['floating_right']<>""?intval($_POST['floating_right']):"";
	if ($setsqlarr['floating_left']==="" && $setsqlarr['floating_right']==="") adminmsg('��߾���ұ߾�������дһ�',1);
	$setsqlarr['floating_top']=$_POST['floating_top']?intval($_POST['floating_top']):0;
		if (empty($_FILES['floating_file']['name']) && empty($_POST['floating_path']))
		{
		adminmsg('���ϴ��ļ�������д·����',1);
		}
		if ($_FILES['floating_file']['name'])
		{
			if ($setsqlarr['floating_type']==1)
			{
			$filetype="gif/jpg/bmp/png";
			}
			else
			{
			$filetype="swf";
			}
			$ads_updir=$ads_updir.date("Y/m/d/");
			make_dir($ads_updir);
			$setsqlarr['floating_path']=_asUpFiles($ads_updir,"floating_file",1000,$filetype,true);
			if (empty($setsqlarr['floating_path']))
			{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['floating_path']=$ads_dir.date("Y/m/d/").$setsqlarr['floating_path'];
		}
		else
		{
			$setsqlarr['floating_path']=trim($_POST['floating_path']);
		}
	}
	//��Ƶ
	elseif ($setsqlarr['type_id']=="6")
	{
	$setsqlarr['video_width']=$_POST['video_width']?intval($_POST['video_width']):adminmsg('��û����д��ȣ�',1);
	$setsqlarr['video_height']=$_POST['video_height']?intval($_POST['video_height']):adminmsg('��û����д�߶ȣ�',1);
		if (empty($_FILES['video_file']['name']) && empty($_POST['video_path']))
		{
		adminmsg('���ϴ��ļ�������д·����',1);
		}
		if ($_FILES['video_file']['name'])
		{
			$ads_updir=$ads_updir.date("Y/m/d/");
			make_dir($ads_updir);
			$setsqlarr['video_path']=_asUpFiles($ads_updir,"video_file",5000,"swf/flv/f4v",true);
			if (empty($setsqlarr['video_path']))
			{
			adminmsg('�ϴ��ļ�ʧ�ܣ�',1);
			}
			$setsqlarr['video_path']=$ads_dir.date("Y/m/d/").$setsqlarr['video_path'];
		}
		else
		{
			$setsqlarr['video_path']=trim($_POST['video_path']);
		}
	}
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['addtime']=$timestamp;
	$link[0]['text'] = "�����б�";
	$link[0]['href'] =trim($_POST['url']);
	$wheresql=" id='".intval($_POST['id'])."' "; 
	!updatetable(table('ad'),$setsqlarr,$wheresql)?adminmsg("�޸�ʧ�ܣ�",0):adminmsg("�޸ĳɹ���",2,$link);
}
//ɾ�����
elseif($act=='del_ad')
{
	$id=$_REQUEST['id'];
	check_token();
	if (empty($id)) adminmsg("��ѡ����Ŀ��",0);
	if ($num=del_ad($id))
	{
	adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�".$num,1);
	}
}
//���λ����
elseif($act=='ad_category')
{
	check_permissions($_SESSION['admin_purview'],"ad_category");
	$smarty->assign('act',$act);//��ǩID
	$smarty->assign('list',get_ad_category());
	$smarty->assign('pageheader',"������");
	get_token();
	$smarty->display('ads/admin_ad_category.htm');
}
//��ӹ��λ
elseif($act=='ad_category_add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ad_category");
	$smarty->assign('pageheader',"��ӹ��λ");
	$smarty->display('ads/admin_ad_category_add.htm');
}
//������ӹ��λ
elseif($act=='ad_category_add_save')
{
	check_permissions($_SESSION['admin_purview'],"ad_category");
	check_token();
	$link[0]['text'] = "������һҳ";
	$link[0]['href'] ="?act=ad_category";
	$setsqlarr['categoryname']=$_POST['categoryname']?trim($_POST['categoryname']):adminmsg('��û�й��λ���ƣ�',1);
	$setsqlarr['alias']=$_POST['alias']?trim($_POST['alias']):adminmsg('��û����д�������ƣ�',1);
	substr($setsqlarr['alias'],0,3)=='QS_'?adminmsg('�Զ�����λ�������Ʋ����� QS_ ��ͷ��',1):'';
	ck_category_alias($setsqlarr['alias'])?adminmsg('���������Ѿ����ڣ��뻻һ���������ƣ�',1):'';
	$setsqlarr['type_id']=$_POST['type_id']?intval($_POST['type_id']):adminmsg('��û��ѡ�������ͣ�',1);
	!inserttable(table('ad_category'),$setsqlarr)?adminmsg("���ʧ�ܣ�",0):adminmsg("��ӳɹ���",2,$link);
}
//�޸Ĺ��λ
elseif($act=='edit_ad_category')
{
	check_permissions($_SESSION['admin_purview'],"ad_category");
	$smarty->assign('ad_category',get_ad_category_one($_GET['id']));
	$smarty->assign('pageheader',"������");
	get_token();
	$smarty->display('ads/admin_ad_category_edit.htm');
}
//���� �޸ĵĹ��λ
elseif($act=='ad_category_edit_save')
{
	check_permissions($_SESSION['admin_purview'],"ad_category");
	check_token();
	$link[0]['text'] = "���ع��λ�б�";
	$link[0]['href'] ="?act=ad_category";
	$setsqlarr['categoryname']=trim($_POST['categoryname'])?trim($_POST['categoryname']):adminmsg('��û�й��λ���ƣ�',1);
	$setsqlarr['alias']=trim($_POST['alias'])?trim($_POST['alias']):adminmsg('��û����д�������ƣ�',1);
	substr($setsqlarr['alias'],0,3)=='QS_'?adminmsg('�Զ�����λ�������Ʋ����� QS_ ��ͷ��',1):'';
	ck_category_alias($setsqlarr['alias'],$_POST['id'])?adminmsg('���������Ѿ����ڣ��뻻һ���������ƣ�',1):'';
	$setsqlarr['type_id']=trim($_POST['type_id'])?trim($_POST['type_id']):adminmsg('��û��ѡ�������ͣ�',1);
	$wheresql=" id='".intval($_POST['id'])."' AND admin_set<>'1'";
		if (updatetable(table('ad_category'),$setsqlarr,$wheresql))
		{
			$adaliasarr['alias']=$setsqlarr['alias'];//ͬʱ�޸Ĵ˷��������й���alias
			$wheresql=" category_id='".intval($_POST['id'])."'";
			updatetable(table('ad'),$adaliasarr,$wheresql);
		adminmsg("�޸ĳɹ���",2,$link);
		}
		else
		{
		adminmsg("�޸�ʧ�ܣ�",0);
		}
}
//ɾ�����λ
elseif($act=='del_ad_category')
{
	check_permissions($_SESSION['admin_purview'],"ad_category");
	check_token();
	$id=!empty($_GET['id'])?$_GET['id']:adminmsg("��û��ѡ����λ��",1);
		if ($id)
		{
			!del_ad_category($id)?adminmsg("ɾ��ʧ�ܣ�",0):adminmsg("ɾ���ɹ���",2);
		}
}
?>