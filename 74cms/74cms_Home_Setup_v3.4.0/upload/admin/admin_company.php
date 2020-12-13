<?php
 /*
 * 74cms ��ҵ�û����
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
require_once(ADMIN_ROOT_PATH.'include/admin_company_fun.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'jobs';
if($act == 'jobs')
{
	check_permissions($_SESSION['admin_purview'],"jobs_show");
	$audit=intval($_GET['audit']);
	$deadline=intval($_GET['deadline']);
	$jobtype=intval($_GET['jobtype']);
	if (empty($jobtype))
	{
		$jobtype=1;
		$_GET['jobtype']=1;
	}
	if ($jobtype==1)
	{
	$tablename="jobs";
	$audit="";
	$deadline=$deadline>2?$deadline:'';
	}
	else
	{
	$tablename="jobs_tmp";
	}
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY id DESC ";
	$wheresqlarr=array();
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if (!empty($key) && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE jobs_name like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE companyname like '%{$key}%'";
		elseif ($key_type===3 && intval($key)>0)$wheresql=" WHERE id =".intval($key);
		elseif ($key_type===4 && intval($key)>0)$wheresql=" WHERE company_id =".intval($key);
		elseif ($key_type===5 && intval($key)>0)$wheresql=" WHERE uid =".intval($key);
		$oederbysql="";
		$tablename="all";
	}
	else
	{
			if ($audit>0)
			{
			$wheresqlarr['audit']=$audit;
			}
			if(isset($_GET['emergency']) && $_GET['emergency']<>'')
			{
			$wheresqlarr['emergency']=intval($_GET['emergency']);
			$oederbysql=" order BY refreshtime DESC";
			}
			if(isset($_GET['recommend']) && $_GET['recommend']<>'')
			{
			$wheresqlarr['recommend']=intval($_GET['recommend']);
			$oederbysql=" order BY refreshtime DESC";
			}
			if (!empty($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
			if (!empty($_GET['settr']))
			{
				$settr=strtotime("-".intval($_GET['settr'])." day");
				$wheresql=empty($wheresql)?" WHERE refreshtime> ".$settr:$wheresql." AND refreshtime> ".$settr;
				$oederbysql=" order BY refreshtime DESC ";
			}
			if (!empty($_GET['addsettr']))
			{
				$settr=strtotime("-".intval($_GET['addsettr'])." day");
				$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
				$oederbysql=" order BY addtime DESC ";
			}
			if($deadline==1)
			{
			$wheresql=empty($wheresql)?" WHERE deadline< ".time():$wheresql." AND deadline> ".time();
			$oederbysql=" order BY deadline DESC ";
			}
			elseif($deadline==2)
			{			
			$wheresql=empty($wheresql)?" WHERE deadline>  ".time():$wheresql." AND deadline> ".time();
			$oederbysql=" order BY deadline DESC ";
			}
			elseif($deadline>2)
			{
			$settr=strtotime("+{$deadline} day");
			$wheresql=empty($wheresql)?" WHERE deadline< {$settr}":$wheresql." AND deadline<{$settr} ";
			$oederbysql=" order BY deadline DESC ";
			}
			if (!empty($_GET['promote']))
			{
				$promote=intval($_GET['promote']);
				if ($promote==-1)
				{
				$psql="recommend=0 AND emergency=0 AND stick=0 AND highlight=''";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				elseif ($promote==1)
				{
				$psql="recommend=1";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				elseif ($promote==2)
				{
				$psql="emergency=1";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				elseif ($promote==3)
				{
				$psql="stick=1";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				}
				elseif ($promote==4)
				{
				$psql="highlight<>'' ";
				$wheresql=empty($wheresql)?" WHERE {$psql} ":"{$wheresql} AND {$psql} ";
				} 
				$oederbysql="";
			}
		 
	}
	if ($tablename=="all")
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table('jobs').$wheresql." UNION ALL SELECT COUNT(*) AS num FROM ".table('jobs_tmp').$wheresql;
	}
	else
	{
	$total_sql="SELECT COUNT(*) AS num FROM ".table($tablename).$wheresql;
	}
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	if ($tablename=="all")
	{
	$getsql="SELECT * FROM ".table('jobs').$wheresql." UNION ALL SELECT * FROM ".table('jobs_tmp').$wheresql;
	}
	else
	{
	$getsql="SELECT * FROM ".table($tablename)." ".$wheresql.$oederbysql;
	}
	$total[0]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs')."");
	$total[1]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')."");
	if ($jobtype==2)
	{
	$total[2]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE audit=1 ");
	$total[3]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE audit=2 ");
	$total[4]=$db->get_total("SELECT COUNT(*) AS num FROM ".table('jobs_tmp')." WHERE audit=3 ");
	}
	$jobs = get_jobs($offset,$perpage,$getsql);
	$smarty->assign('pageheader',"ְλ����");
	$smarty->assign('jobs',$jobs);
	$smarty->assign('now',time());
	$smarty->assign('total',$total);
	$smarty->assign('page',$page->show(3));
	$smarty->assign('totaljob',$total_val);
	$smarty->assign('cat',get_promotion_cat(1));
	get_token();
	$smarty->display('company/admin_company_jobs.htm');
}
elseif($act == 'jobs_perform')
{
		check_token();
		$yid =!empty($_REQUEST['y_id'])?$_REQUEST['y_id']:adminmsg("��û��ѡ��ְλ��",1);
		if (!empty($_REQUEST['delete']))
		{
			check_permissions($_SESSION['admin_purview'],"jobs_del");
			$num=del_jobs($yid);
			if ($num>0)
			{
			adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);
			}
			else
			{
			adminmsg("ɾ��ʧ�ܣ�",0);
			}
		}
		elseif (!empty($_POST['set_audit']))
		{
			check_permissions($_SESSION['admin_purview'],"jobs_audit");
			$audit=intval($_POST['audit']);
			$pms_notice=intval($_POST['pms_notice']);
			$reason=trim($_POST['reason']);
			if ($n=edit_jobs_audit($yid,$audit,$reason,$pms_notice))
			{
			adminmsg("��˳ɹ�����Ӧ���� {$n}",2);			
			}
			else
			{
			adminmsg("��˳ɹ�����Ӧ���� 0",1);
			}
		}
		elseif (!empty($_GET['refresh']))
		{
			if($n=refresh_jobs($yid))
			{
			adminmsg("ˢ�³ɹ�����Ӧ���� {$n}",2);
			}
			else
			{
			adminmsg("ˢ��ʧ�ܣ�",0);
			}
		}
		elseif (!empty($_POST['set_delay']))
		{
			$days=intval($_POST['days']);
			if (empty($days))
			{
			adminmsg("����дҪ�ӳ���������",0);
			}
			if($n=delay_jobs($yid,$days))
			{
			distribution_jobs($yid);
			adminmsg("�ӳ���Ч�ڳɹ�����Ӧ���� {$n}",2);
			}
			else
			{
			adminmsg("����ʧ�ܣ�",0);
			}
		}
}
elseif($act == 'edit_jobs')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"jobs_edit");
	$id =!empty($_REQUEST['id'])?intval($_REQUEST['id']):adminmsg("��û��ѡ��ְλ��",1);
	$smarty->assign('pageheader',"ְλ����");
	$jobs=get_jobs_one($id);
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('jobs',$jobs);
	$smarty->assign('jobsaudit',get_jobsaudit_one($id));
 	$smarty->display('company/admin_company_jobs_edit.htm');
}
elseif ($act=='editjobs_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"jobs_edit");
	$id=intval($_POST['id']);
	$company_id=intval($_POST['company_id']);
    $company_profile=get_company_one_id($company_id);
	$setsqlarr['subsite_id']=intval($_POST['subsite_id']);
	$setsqlarr['display']=intval($_POST['display']);
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['sex']=intval($_POST['sex']);
	$setsqlarr['sex_cn']=trim($_POST['sex_cn']);
	$setsqlarr['nature']=intval($_POST['nature']);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn']);	
	$setsqlarr['jobs_name']=trim($_POST['jobs_name'])?trim($_POST['jobs_name']):adminmsg('��û����дְλ���ƣ�',1);
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):adminmsg('��û����дְλ������',1);	
	$setsqlarr['amount']=intval($_POST['amount']);
	$setsqlarr['category']=intval($_POST['category']);
	$setsqlarr['subclass']=intval($_POST['subclass']);
	$setsqlarr['category_cn']=trim($_POST['category_cn']);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['district_cn']=trim($_POST['district_cn']);
	$setsqlarr['education']=intval($_POST['education']);
	$setsqlarr['education_cn']=trim($_POST['education_cn']);
	$setsqlarr['experience']=intval($_POST['experience']);
	$setsqlarr['experience_cn']=trim($_POST['experience_cn']);
	$setsqlarr['wage']=intval($_POST['wage']);
	$setsqlarr['wage_cn']=trim($_POST['wage_cn']);
	$setsqlarr['graduate']=intval($_POST['graduate']);
	$setsqlarr['key']=$setsqlarr['jobs_name'].$company_profile['companyname'].$setsqlarr['category_cn'].$setsqlarr['district_cn'].$setsqlarr['contents'];
	require_once(QISHI_ROOT_PATH.'include/splitword.class.php');
	$sp = new SPWord();
	$setsqlarr['key']="{$setsqlarr['jobs_name']} {$company_profile['companyname']} ".$sp->extracttag($setsqlarr['key']);
	$setsqlarr['key']=$sp->pad($setsqlarr['key']);
	$days=intval($_POST['days']);
	if ($days>0 && (intval($_POST['olddeadline'])-time())>0) $setsqlarr['deadline']=intval($_POST['olddeadline'])+($days*(60*60*24));
	if ($days>0 && (intval($_POST['olddeadline'])-time())<0) $setsqlarr['deadline']=strtotime("".$days." day");
	$setsqlarr_contact['contact']=trim($_POST['contact']);
	$setsqlarr_contact['qq']=trim($_POST['qq']);
	$setsqlarr_contact['telephone']=trim($_POST['telephone']);
	$setsqlarr_contact['address']=trim($_POST['address']);
	$setsqlarr_contact['email']=trim($_POST['email']);
	$setsqlarr_contact['notify']=trim($_POST['notify']);
		$setsqlarr_contact['contact_show']=intval($_POST['contact_show']);
	$setsqlarr_contact['email_show']=intval($_POST['email_show']);
	$setsqlarr_contact['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr_contact['address_show']=intval($_POST['address_show']);
	$setsqlarr_contact['qq_show']=intval($_POST['qq_show']);
	
	$wheresql=" id='".$id."' ";
	$tb1=$db->getone("select * from ".table('jobs')." where id='{$id}' LIMIT 1");
	if (!empty($tb1))
	{
		if (!updatetable(table('jobs'),$setsqlarr,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	}
	else
	{
		if (!updatetable(table('jobs_tmp'),$setsqlarr,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	}
	$wheresql=" pid=".$id;
	if (!updatetable(table('jobs_contact'),$setsqlarr_contact,$wheresql)) adminmsg("����ʧ�ܣ�",0);
	//
	$searchtab['subsite_id']=$setsqlarr['subsite_id'];
	$searchtab['nature']=$setsqlarr['nature'];
	$searchtab['sex']=$setsqlarr['sex'];
	$searchtab['category']=$setsqlarr['category'];
	$searchtab['subclass']=$setsqlarr['subclass'];
	$searchtab['trade']=$setsqlarr['trade'];
	$searchtab['district']=$setsqlarr['district'];
	$searchtab['sdistrict']=$setsqlarr['sdistrict'];
	$searchtab['education']=$setsqlarr['education'];
	$searchtab['experience']=$setsqlarr['experience'];
	$searchtab['wage']=$setsqlarr['wage'];
	//
	updatetable(table('jobs_search_wage'),$searchtab," id='{$id}'");
	updatetable(table('jobs_search_rtime'),$searchtab," id='{$id}'");
	updatetable(table('jobs_search_stickrtime'),$searchtab," id='{$id}'");
	updatetable(table('jobs_search_hot'),$searchtab," id='{$id}'");
	updatetable(table('jobs_search_scale'),$searchtab," id='{$id}'");
	$searchtab['key']=$setsqlarr['key'];
	$searchtab['likekey']=$setsqlarr['jobs_name'].','.$company_profile['companyname'];
	updatetable(table('jobs_search_key'),$searchtab," id='{$id}' ");	
	unset($setsqlarr_contact,$setsqlarr);
	distribution_jobs($id);
	$link[0]['text'] = "����ְλ�б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg("�޸ĳɹ���",2,$link);
}
elseif($act == 'company_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_show");
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY c.id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		
		if     ($key_type===1)$wheresql=" WHERE c.companyname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE c.id ='".intval($key)."'";
		elseif ($key_type===3)$wheresql=" WHERE m.username like '{$key}%'";
		elseif ($key_type===4)$wheresql=" WHERE c.uid ='".intval($key)."'";
		elseif ($key_type===5)$wheresql=" WHERE c.address  like '%{$key}%'";
		elseif ($key_type===6)$wheresql=" WHERE c.telephone  like '{$key}%'";		
		$oederbysql="";
	}
	$_GET['audit']<>""? $wheresqlarr['c.audit']=intval($_GET['audit']):'';
	$_GET['yellowpages']<>""? $wheresqlarr['c.yellowpages']=intval($_GET['yellowpages']):'';
	if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
	if (!empty($_GET['settr']))
	{
		$settr=strtotime("-".intval($_GET['settr'])." day");
		$wheresql=empty($wheresql)?" WHERE addtime> ".$settr:$wheresql." AND addtime> ".$settr;
	}
	$operation_mode=$_CFG['operation_mode'];
	if($operation_mode=='1'){
		$joinsql=" LEFT JOIN ".table('members')." AS m ON c.uid=m.uid  LEFT JOIN ".table('members_points')." AS p ON c.uid=p.uid";
	}else{
		$joinsql=" LEFT JOIN ".table('members')." AS m ON c.uid=m.uid  LEFT JOIN ".table('members_setmeal')." AS p ON c.uid=p.uid";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('company_profile')." AS c".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$clist = get_company($offset,$perpage,$joinsql.$wheresql.$oederbysql,$operation_mode);
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->assign('clist',$clist);
	$smarty->assign('certificate_dir',$certificate_dir);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_list.htm');
}

elseif($act == 'company_perform')
{
	check_token();
	$u_id =!empty($_POST['y_id'])?$_POST['y_id']:adminmsg("��û��ѡ����ҵ��",1);
	if ($_POST['delete'])
	{
		check_permissions($_SESSION['admin_purview'],"com_del");
		if ($_POST['delete_company']=='yes')
		{
		!del_company($u_id)?adminmsg("ɾ����ҵ����ʧ�ܣ�",0):"";
		}
		if ($_POST['delete_jobs']=='yes')
		{
		!del_company_alljobs($u_id)?adminmsg("ɾ��ְλʧ�ܣ�",0):"";
		}
		if ($_POST['delete_jobs']<>'yes' && $_POST['delete_company']<>'yes')
		{
		adminmsg("δѡ��ɾ�����ͣ�",1);
		}
		adminmsg("ɾ���ɹ���",2);
	}
	if (trim($_POST['set_audit']))
	{
		check_permissions($_SESSION['admin_purview'],"com_audit");
		$audit=$_POST['audit'];
		$pms_notice=intval($_POST['pms_notice']);
		$reason=trim($_POST['reason']);
		!edit_company_audit($u_id,intval($audit),$reason,$pms_notice)?adminmsg("����ʧ�ܣ�",0):adminmsg("���óɹ���",2);
	}
	elseif (!empty($_POST['set_refresh']))
		{
			if (empty($_POST['refresh_jobs']))
			{
			$refresjobs=false;
			}
			else
			{
			$refresjobs=true;
			}
			if($n=refresh_company($u_id,$refresjobs))
			{
			adminmsg("ˢ�³ɹ�����Ӧ���� {$n} ��",2);
			}
			else
			{
			adminmsg("ˢ��ʧ�ܣ�",0);
			}
		}
}
elseif($act == 'edit_company_profile')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_edit");
	$yid =!empty($_REQUEST['id'])?intval($_REQUEST['id']):adminmsg("��û��ѡ����ҵ��",1);
	$smarty->assign('pageheader',"��ҵ����");
	$company_profile=get_company_one_id($yid);
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('comaudit',get_comaudit_one($yid));

	$smarty->assign('company_profile',$company_profile);
	$smarty->assign('certificate_dir',$certificate_dir);//Ӫҵִ��·��
	$smarty->display('company/admin_company_profile_edit.htm');
}
elseif ($act=='company_profile_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_edit");
	$setsqlarr=array();
	$contents=array();
	$id=intval($_POST['id']);
	$setsqlarr['audit']=intval($_POST['audit']);
	$setsqlarr['companyname']=trim($_POST['companyname'])?trim($_POST['companyname']):adminmsg('��û��������ҵ���ƣ�',1);
	$setsqlarr['nature']=trim($_POST['nature'])?trim($_POST['nature']):adminmsg('��ѡ����ҵ���ʣ�',1);
	$setsqlarr['nature_cn']=trim($_POST['nature_cn'])?trim($_POST['nature_cn']):adminmsg('��ѡ����ҵ���ʣ�',1);
	$setsqlarr['trade']=trim($_POST['trade'])?trim($_POST['trade']):adminmsg('��ѡ��������ҵ��',1);
	$setsqlarr['trade_cn']=trim($_POST['trade_cn'])?trim($_POST['trade_cn']):adminmsg('��ѡ��������ҵ��',1);
	$setsqlarr['district_cn']=trim($_POST['district_cn'])?trim($_POST['district_cn']):adminmsg('��ѡ������������',1);
	$setsqlarr['district']=intval($_POST['district']);
	$setsqlarr['sdistrict']=intval($_POST['sdistrict']);
	$setsqlarr['street']=intval($_POST['street']);
	$setsqlarr['street_cn']=trim($_POST['street_cn']);
	$setsqlarr['officebuilding']=intval($_POST['officebuilding']);
	$setsqlarr['officebuilding_cn']=trim($_POST['officebuilding_cn']);
	$setsqlarr['scale']=trim($_POST['scale'])?trim($_POST['scale']):adminmsg('��ѡ��˾��ģ��',1);
	$setsqlarr['scale_cn']=trim($_POST['scale_cn'])?trim($_POST['scale_cn']):adminmsg('��ѡ��˾��ģ��',1);	
	$setsqlarr['registered']=trim($_POST['registered']);
	$setsqlarr['currency']=trim($_POST['currency']);
	$setsqlarr['address']=trim($_POST['address']);
	$setsqlarr['contact']=trim($_POST['contact']);
	$setsqlarr['telephone']=trim($_POST['telephone']);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['yellowpages']=intval($_POST['yellowpages']);
	$setsqlarr['website']=trim($_POST['website']);
	$setsqlarr['contents']=trim($_POST['contents'])?trim($_POST['contents']):adminmsg('����д��˾��飡',1);
		$setsqlarr['contact_show']=intval($_POST['contact_show']);
	$setsqlarr['email_show']=intval($_POST['email_show']);
	$setsqlarr['telephone_show']=intval($_POST['telephone_show']);
	$setsqlarr['address_show']=intval($_POST['address_show']);
		
	$wheresql=" id='{$id}' ";
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
		if (updatetable(table('company_profile'),$setsqlarr,$wheresql))
		{
				$jobarr['companyname']=$setsqlarr['companyname'];
				$jobarr['trade']=$setsqlarr['trade'];
				$jobarr['trade_cn']=$setsqlarr['trade_cn'];
				$jobarr['scale']=$setsqlarr['scale'];
				$jobarr['scale_cn']=$setsqlarr['scale_cn'];
				$jobarr['street']=$setsqlarr['street'];
				$jobarr['street_cn']=$setsqlarr['street_cn'];
				$jobarr['officebuilding']=$setsqlarr['officebuilding'];
				$jobarr['officebuilding_cn']=$setsqlarr['officebuilding_cn'];
				if (!updatetable(table('jobs'),$jobarr," uid=".intval($_POST['cuid'])."")) adminmsg('�޸�ְλ���ֳ���',0);
				if (!updatetable(table('jobs_tmp'),$jobarr," uid=".intval($_POST['cuid'])."")) adminmsg('�޸�ְλ���ֳ���',0);
				// if (!updatetable(table('jobfair_exhibitors'),array('companyname'=>$jobarr['companyname'])," uid=".intval($_POST['cuid'])."")) adminmsg('�޸Ĺ�˾���Ƴ���',0);
				$soarray['trade']=$jobarr['trade'];
				$soarray['scale']=$jobarr['scale'];
				$soarray['street']=$setsqlarr['street'];
				$soarray['officebuilding']=$setsqlarr['officebuilding'];
				updatetable(table('jobs_search_scale'),$soarray," uid=".intval($_POST['cuid'])."");
				updatetable(table('jobs_search_wage'),$soarray," uid=".intval($_POST['cuid'])."");
				updatetable(table('jobs_search_rtime'),$soarray," uid=".intval($_POST['cuid'])."");
				updatetable(table('jobs_search_stickrtime'),$soarray," uid=".intval($_POST['cuid'])."");
				updatetable(table('jobs_search_hot'),$soarray," uid=".intval($_POST['cuid'])."");
				updatetable(table('jobs_search_key'),$soarray," uid=".intval($_POST['cuid'])."");
				unset($setsqlarr);
				adminmsg("����ɹ���",2,$link);
		}
		else
		{
		unset($setsqlarr);
		adminmsg("����ʧ�ܣ�",0);
		}
}
elseif($act == 'order_list')
{	
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_show");
		require_once(QISHI_ROOT_PATH.'include/page.class.php');
		require_once(ADMIN_ROOT_PATH.'include/admin_pay_fun.php');
	$wheresql=" WHERE o.utype=1 ";
	$oederbysql=" order BY o.addtime DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql=" WHERE o.utype=1 AND c.companyname like '%{$key}%'";
		elseif ($key_type===2)$wheresql=" WHERE o.utype=1 AND m.username = '{$key}'";
		elseif ($key_type===3)$wheresql=" WHERE o.utype=1 AND o.oid ='".trim($key)."'";
		$oederbysql="";
	}
	else
	{	
		$wheresqlarr['o.utype']='1';
		!empty($_GET['is_paid'])? $wheresqlarr['o.is_paid']=intval($_GET['is_paid']):'';
		!empty($_GET['typename'])?$wheresqlarr['o.payment_name']=trim($_GET['typename']):'';
		if (is_array($wheresqlarr)) $wheresql=wheresql($wheresqlarr);
		
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql.=empty($wheresql)?" WHERE ": " AND ";
			$wheresql.="o.addtime> ".$settr;
		}
	}
	$joinsql=" left JOIN ".table('members')." as m ON o.uid=m.uid LEFT JOIN  ".table('company_profile')." as c ON o.uid=c.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('order')." as o ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$orderlist = get_order_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��������");
	$smarty->assign('payment_list',get_payment(2));
	$smarty->assign('orderlist',$orderlist);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_order_list.htm');
}
elseif($act == 'show_order')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_show");
	$smarty->assign('pageheader',"��������");
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('payment',get_order_one($_GET['id']));
	$smarty->display('company/admin_order_show.htm');
}
elseif($act == 'order_notes_save')
{
	check_token();
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	!$db->query("UPDATE ".table('order')." SET  notes='".$_POST['notes']."' WHERE id='".intval($_GET['id'])."'")?adminmsg('����ʧ��',1):adminmsg("�����ɹ���",2,$link);
}
//���ó�ֵ��¼���տͨ��
elseif($act == 'order_set')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"ord_set");
	$smarty->assign('pageheader',"��������");
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->assign('payment',get_order_one($_GET['id']));
	$smarty->display('company/admin_order_set.htm');
}
elseif($act == 'order_set_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"ord_set");
		if (order_paid(trim($_POST['oid'])))
		{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $_POST['url'];
		!$db->query("UPDATE ".table('order')." SET notes='".$_POST['notes']."' WHERE id=".intval($_GET['id'])."  LIMIT 1 ")?adminmsg('����ʧ��',1):adminmsg("�����ɹ���",2,$link);
		}
		else
		{
		adminmsg('����ʧ��',1);
		}
}
//ȡ����Ա��ֵ����
elseif($act == 'order_del')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"ord_del");
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ����Ŀ��",1);
	if (del_order($id))
	{
	adminmsg("ȡ���ɹ���",2,$link);
	}
	else
	{
	adminmsg("ȡ��ʧ�ܣ�",1);
	}
}
elseif($act == 'meal_members')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE a.effective=1 ";
	$oederbysql=" order BY a.uid DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.=" AND b.username = '{$key}'";
		elseif ($key_type===2)$wheresql.=" AND b.uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND b.email = '{$key}'";
		elseif ($key_type===4)$wheresql.=" AND b.mobile like '{$key}%'";
		elseif ($key_type===5)$wheresql.=" AND c.companyname like '{$key}%'";
		$oederbysql="";
	}
	else
	{	
		if (!empty($_GET['setmeal_id']))
		{
			$setmeal_id=intval($_GET['setmeal_id']);
			$wheresql.=" AND a.setmeal_id=".$setmeal_id;
		}
		if (!empty($_GET['settr']))
		{
			$settr=intval($_GET['settr']);
			if ($settr==-1)
			{
			$wheresql.=" AND a.endtime<".time()." AND a.endtime>0 ";
			}
			else
			{
			$settr=strtotime("{$settr} day");
			$wheresql.="  AND a.endtime>".time()." AND a.endtime< {$settr}";
			}			
		}
	}
	$joinsql=" LEFT JOIN ".table('members')." as b ON a.uid=b.uid  LEFT JOIN ".table('company_profile')." as c ON a.uid=c.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_setmeal')." as a ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$member = get_meal_members_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->assign('navlabel','meal_members');
	$smarty->assign('member',$member);
	$smarty->assign('setmeal',get_setmeal());	
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_meal_members.htm');
}
elseif($act == 'meal_log')
{
	get_token();
	require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$oederbysql=" order BY a.log_id DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	$operation_mode=$_CFG['operation_mode'];
	$wheresql=" WHERE a.log_mode={$operation_mode} AND a.log_utype=1";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.="  AND a.log_username = '{$key}'";
		elseif ($key_type===2)$wheresql.="  AND a.log_uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND c.companyname like '{$key}%'";
		$oederbysql=" order BY a.log_id DESC ";
	}
	else
	{	
		if (!empty($_GET['log_type']))
		{
			$log_type=intval($_GET['log_type']);
			$wheresql.=" AND  a.log_type=".$log_type;
		}
		if (!empty($_GET['settr']))
		{
			$settr=intval($_GET['settr']);
			$settr=strtotime("-{$settr} day");
			$wheresql.=" AND a.log_addtime> ".$settr;
		}
		if (!empty($_GET['is_money']))
		{
			$is_money=intval($_GET['is_money']);
			$wheresql.= " AND a.log_ismoney={$is_money}";
		}
	}
	if($operation_mode=='1'){
		$joinsql=" LEFT JOIN ".table('members_points')." as b ON a.log_uid=b.uid  LEFT JOIN ".table('company_profile')." as c ON a.log_uid=c.uid ";
	}else{
		$joinsql=" LEFT JOIN ".table('members_setmeal')." as b ON a.log_uid=b.uid  LEFT JOIN ".table('company_profile')." as c ON a.log_uid=c.uid ";
	}
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members_charge_log')." as a ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$meallog = get_meal_members_log($offset,$perpage,$joinsql.$wheresql.$oederbysql,$operation_mode);
	$smarty->assign('pageheader','��ҵ����');
	$smarty->assign('navlabel','meal_log');
	$smarty->assign('meallog',$meallog);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_meal_log.htm');
}
elseif($act == 'meal_log_pie')
{
	require_once(ADMIN_ROOT_PATH.'include/admin_flash_statement_fun.php');
	$pie_type=!empty($_GET['pie_type'])?intval($_GET['pie_type']):1;
	meal_log_pie($pie_type,1);	
	$smarty->assign('pageheader',"��ҵ����");
	$smarty->assign('navlabel','meal_log_pie');
	$smarty->display('company/admin_company_meal_log_pie.htm');
}
elseif($act == 'meallog_del')
{
	check_permissions($_SESSION['admin_purview'],"meallog_del");
	check_token();
	$id =!empty($_REQUEST['id'])?$_REQUEST['id']:adminmsg("��û��ѡ���¼��",1);
	$num=del_meal_log($id);
	if ($num>0){adminmsg("ɾ���ɹ�����ɾ��".$num."��",2);}else{adminmsg("ɾ��ʧ�ܣ�",0);}
}


elseif($act == 'meal_delay')
{
			$tuid =!empty($_REQUEST['tuid'])?$_REQUEST['tuid']:adminmsg("��û��ѡ���Ա��",1);
			$days=intval($_POST['days']);
			if (empty($days))
			{
			adminmsg("����дҪ�ӳ���������",0);
			}
			if($n=delay_meal($tuid,$days))
			{
			distribution_jobs_uid($tuid);
			adminmsg("�ӳ���Ч�ڳɹ�����Ӧ���� {$n}",2);
			}
			else
			{
			adminmsg("����ʧ�ܣ�",0);
			}
}
elseif($act == 'members_list')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_user_show");
		require_once(QISHI_ROOT_PATH.'include/page.class.php');
	$wheresql=" WHERE m.utype=1 ";
	$oederbysql=" order BY m.uid DESC ";
	$key=isset($_GET['key'])?trim($_GET['key']):"";
	$key_type=isset($_GET['key_type'])?intval($_GET['key_type']):"";
	if ($key && $key_type>0)
	{
		if     ($key_type===1)$wheresql.=" AND m.username = '{$key}'";
		elseif ($key_type===2)$wheresql.=" AND m.uid = '".intval($key)."' ";
		elseif ($key_type===3)$wheresql.=" AND m.email = '{$key}'";
		elseif ($key_type===4)$wheresql.=" AND m.mobile like '{$key}%'";
		elseif ($key_type===5)$wheresql.=" AND c.companyname like '%{$key}%'";
		$oederbysql="";
	}
	else
	{	
		if (!empty($_GET['settr']))
		{
			$settr=strtotime("-".intval($_GET['settr'])." day");
			$wheresql.=" AND m.reg_time> ".$settr;
		}
		if (!empty($_GET['verification']))
		{
			if ($_GET['verification']=="1")
			{
			$wheresql.=" AND m.email_audit = 1";
			}
			elseif ($_GET['verification']=="2")
			{
			$wheresql.=" AND m.email_audit = 0";
			}
			elseif ($_GET['verification']=="3")
			{
			$wheresql.=" AND m.mobile_audit = 1";
			}
			elseif ($_GET['verification']=="4")
			{
			$wheresql.=" AND m.mobile_audit = 0";
			}
		}
	}
	$joinsql=" LEFT JOIN ".table('company_profile')." as c ON m.uid=c.uid ";
	$total_sql="SELECT COUNT(*) AS num FROM ".table('members')." as m ".$joinsql.$wheresql;
	$total_val=$db->get_total($total_sql);
	$page = new page(array('total'=>$total_val, 'perpage'=>$perpage));
	$currenpage=$page->nowindex;
	$offset=($currenpage-1)*$perpage;
	$member = get_member_list($offset,$perpage,$joinsql.$wheresql.$oederbysql);
	$smarty->assign('pageheader',"��ҵ��Ա");
	$smarty->assign('member',$member);
	$smarty->assign('page',$page->show(3));
	$smarty->display('company/admin_company_user_list.htm');
}
elseif($act == 'delete_user')
{	
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_del");
	$tuid =!empty($_REQUEST['tuid'])?$_REQUEST['tuid']:adminmsg("��û��ѡ���Ա��",1);
	if ($_POST['delete'])
	{
		if (!empty($_POST['delete_user']))
		{
		!delete_company_user($tuid)?adminmsg("ɾ����Աʧ�ܣ�",0):"";
		}
		if (!empty($_POST['delete_company']))
		{
		!del_company($tuid)?adminmsg("ɾ����ҵ����ʧ�ܣ�",0):"";
		}
		if (!empty($_POST['delete_jobs']))
		{
		!del_company_alljobs($tuid)?adminmsg("ɾ��ְλʧ�ܣ�",0):"";
		}
	adminmsg("ɾ���ɹ���",2);
	}
}
elseif($act == 'members_add')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_user_add");
	$smarty->assign('pageheader',"��ҵ��Ա");
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('points',get_cache('points_rule'));
	$smarty->display('company/admin_company_user_add.htm');
}
elseif($act == 'members_add_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_add");
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	if (strlen(trim($_POST['username']))<3) adminmsg('�û�������Ϊ3λ���ϣ�',1);
	if (strlen(trim($_POST['password']))<6) adminmsg('�������Ϊ6λ���ϣ�',1);
	$sql['username'] = !empty($_POST['username']) ? trim($_POST['username']):adminmsg('����д�û�����',1);
	$sql['password'] = !empty($_POST['password']) ? trim($_POST['password']):adminmsg('����д���룡',1);	
	if ($sql['password']<>trim($_POST['password1']))
	{
	adminmsg('������������벻��ͬ��',1);
	}
	$sql['utype'] = !empty($_POST['member_type']) ? intval($_POST['member_type']):adminmsg('��û��ѡ��ע�����ͣ�',1);
	if (empty($_POST['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$_POST['email']))
	{
	adminmsg('���������ʽ����',1);
	}
	$sql['email']= trim($_POST['email']);
	if (get_user_inusername($sql['username']))
	{
	adminmsg('���û����Ѿ���ʹ�ã�',1);
	}
	if (get_user_inemail($sql['email']))
	{
	adminmsg('�� Email �Ѿ���ע�ᣡ',1);
	}
 	$sql['pwd_hash'] = randstr();
	$sql['password'] = md5(md5($sql['password']).$sql['pwd_hash'].$QS_pwdhash);
	$sql['reg_time']=time();
	$sql['reg_ip']=$online_ip;
	$insert_id=inserttable(table('members'),$sql,true);
			if($sql['utype']=="1")
			{
			$db->query("INSERT INTO ".table('members_points')." (uid) VALUES ('{$insert_id}')");
			$db->query("INSERT INTO ".table('members_setmeal')." (uid) VALUES ('{$insert_id}')");
				if(intval($_POST['is_money']) && $_POST['log_amount']){
					$amount=round($_POST['log_amount'],2);
					$ismoney=2;
				}else{
					$amount='0.00';
					$ismoney=1;
				}
				$regpoints_num=intval($_POST['regpoints_num']);
				if ($_POST['regpoints']=="y")
				{
				write_memberslog($insert_id,1,9001,$sql['username'],"<span style=color:#FF6600>ע���Աϵͳ�Զ�����!(+{$regpoints_num})</span>");
						//��Ա���ֱ����¼������Ա��̨�޸Ļ�Ա�Ļ��֡�3��ʾ������Ա��̨�޸�
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨�����ҵ��Ա������(+{$regpoints_num})���֣���ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,$ismoney,1,1);
					
				report_deal($insert_id,1,$regpoints_num);
				}
				$reg_service=intval($_POST['reg_service']);
				if ($reg_service>0)
				{
				$service=get_setmeal_one($reg_service);
				write_memberslog($insert_id,1,9002,$sql['username'],"��ͨ����({$service['setmeal_name']})");
				set_members_setmeal($insert_id,$reg_service);
						//��Ա���ֱ����¼������Ա��̨�޸Ļ�Ա�Ļ��֡�3��ʾ������Ա��̨�޸�
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨�����ҵ��Ա����ͨ����({$service['setmeal_name']})����ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,$ismoney,2,1);
					
				}
				if(intval($_POST['is_money']) && $_POST['log_amount'] && !$notes){
				$notes="�����ˣ�{$_SESSION['admin_name']},˵������̨�����ҵ��Ա��δ���ͻ��֣�δ��ͨ�ײͣ���ȡ���ã�{$amount}Ԫ";
				write_setmeallog($insert_id,$sql['username'],$notes,4,$amount,2,2,1);
				}			
			}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = "?act=members_list";
	$link[1]['text'] = "�������";
	$link[1]['href'] = "?act=members_add";
	adminmsg('��ӳɹ���',2,$link);
}
elseif($act == 'user_edit')
{
	get_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	$company_user=get_user($_GET['tuid']);
	$smarty->assign('pageheader',"��ҵ��Ա");
	$company_profile=get_company_one_uid($company_user['uid']);
	$company_user['tpl']=$company_profile['tpl'];
	$smarty->assign('company_user',$company_user);
	$smarty->assign('userpoints',get_user_points($company_user['uid']));
	$smarty->assign('setmeal',get_user_setmeal($company_user['uid']));
	$smarty->assign('givesetmeal',get_setmeal(false));
	$smarty->assign('url',$_SERVER["HTTP_REFERER"]);
	$smarty->display('company/admin_company_user_edit.htm');
}
elseif($act == 'set_account_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	$setsqlarr['username']=trim($_POST['username']);
	$setsqlarr['email']=trim($_POST['email']);
	$setsqlarr['email_audit']=intval($_POST['email_audit']);
	$setsqlarr['mobile']=trim($_POST['mobile']);
	$setsqlarr['mobile_audit']=intval($_POST['mobile_audit']);
	if ($_POST['qq_openid']=="1")
	{
	$setsqlarr['qq_openid']='';
	}
	$thisuid=intval($_POST['company_uid']);	
	if (strlen($setsqlarr['username'])<3) adminmsg('�û�������Ϊ3λ���ϣ�',1);
	$getusername=get_user_inusername($setsqlarr['username']);
	if (!empty($getusername)  && $getusername['uid']<>$thisuid)
	{
	adminmsg("�û��� {$setsqlarr['username']}  �Ѿ����ڣ�",1);
	}
	if (empty($setsqlarr['email']) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$setsqlarr['email']))
	{
	adminmsg('���������ʽ����',1);
	}
	$getemail=get_user_inemail($setsqlarr['email']);
	if (!empty($getemail)  && $getemail['uid']<>$thisuid)
	{
	adminmsg("Email  {$setsqlarr['email']}  �Ѿ����ڣ�",1);
	}
	if (!empty($setsqlarr['mobile']) && !preg_match("/^(13|15|18)\d{9}$/",$setsqlarr['mobile']))
	{
	adminmsg('�ֻ��������',1);
	}
	$getmobile=get_user_inmobile($setsqlarr['mobile']);
	if (!empty($setsqlarr['mobile']) && !empty($getmobile)  && $getmobile['uid']<>$thisuid)
	{
	adminmsg("�ֻ��� {$setsqlarr['mobile']}  �Ѿ����ڣ�",1);
	}
	if ($_POST['tpl'])
	{
		$tplarr['tpl']=trim($_POST['tpl']);
		updatetable(table('company_profile'),$tplarr," uid='{$thisuid}'");
		updatetable(table('jobs'),$tplarr," uid='{$thisuid}'");
		updatetable(table('jobs_tmp'),$tplarr," uid='{$thisuid}'");
		unset($tplarr);
	}
	if (updatetable(table('members'),$setsqlarr," uid=".$thisuid.""))
	{
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�޸ĳɹ���',2,$link);
	}
	else
	{
	adminmsg('�޸�ʧ�ܣ�',1);
	}
}
elseif($act == 'userpoints_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	if (intval($_POST['points'])<1) adminmsg('��������֣�',1);
	if (trim($_POST['points_notes'])=='') adminmsg('����д���ֲ���˵����',1);
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	$user=get_user($_POST['company_uid']);
	$points_type=intval($_POST['points_type']);	
	$t=$points_type==1?"+":"-";
	report_deal($user['uid'],$points_type,intval($_POST['points']));
	$points=get_user_points($user['uid']);
	write_memberslog(intval($_POST['company_uid']),1,9001,$user['username']," ����Ա��������({$t}{$_POST['points']})��(ʣ��:{$points})����ע��".$_POST['points_notes']);
		//��Ա���ֱ����¼������Ա��̨�޸Ļ�Ա�Ļ��֡�3��ʾ������Ա��̨�޸�
		$user=get_user($_POST['company_uid']);
		if(intval($_POST['is_money']) && $_POST['log_amount']){
			$amount=round($_POST['log_amount'],2);
			$ismoney=2;
		}else{
			$amount='0.00';
			$ismoney=1;
		}
		$notes="�����ˣ�{$_SESSION['admin_name']},˵�����޸Ļ�Ա {$user['username']} ���� ({$t}{$_POST['points']})����ȡ���ֽ�{$amount} Ԫ����ע��{$_POST['points_notes']}";
		write_setmeallog($_POST['company_uid'],$user['username'],$notes,3,$amount,$ismoney,1,1);
			
	adminmsg('����ɹ���',2);
}
elseif($act == 'set_setmeal_save')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
    if (intval($_POST['reg_service'])>0)
	{
		if (set_members_setmeal($_POST['company_uid'],$_POST['reg_service']))
		{
		$link[0]['text'] = "�����б�";
		$link[0]['href'] = $_POST['url'];
		//��Ա�ײͱ����¼������Ա��̨�޸Ļ�Ա�ײͣ����¿�ͨ�ײ͡�3��ʾ������Ա��̨�޸�
		$user=get_user($_POST['company_uid']);
		if(intval($_POST['is_money']) && $_POST['log_amount']){
			$amount=round($_POST['log_amount'],2);
			$ismoney=2;
		}else{
			$amount='0.00';
			$ismoney=1;
		}
		$notes="�����ˣ�{$_SESSION['admin_name']},˵����Ϊ��Ա {$user['username']} ���¿�ͨ������ȡ�����{$amount}Ԫ������ID��{$_POST['reg_service']}��";
		write_setmeallog($_POST['company_uid'],$user['username'],$notes,4,$amount,$ismoney,2,1);
			
		adminmsg('�����ɹ���',2,$link);
		}
		else
		{
		adminmsg('����ʧ�ܣ�',1);
		}
	}
	else
	{
	adminmsg('��ѡ������ײͣ�',1);
	}	
}
elseif($act == 'edit_setmeal_save')
{
	check_token();
    check_permissions($_SESSION['admin_purview'],"com_user_edit");
	$setsqlarr['jobs_ordinary']=$_POST['jobs_ordinary'];
	$setsqlarr['download_resume_ordinary']=$_POST['download_resume_ordinary'];
	$setsqlarr['download_resume_senior']=$_POST['download_resume_senior'];
	$setsqlarr['interview_ordinary']=$_POST['interview_ordinary'];
	$setsqlarr['interview_senior']=$_POST['interview_senior'];
	$setsqlarr['talent_pool']=$_POST['talent_pool'];
	$setsqlarr['recommend_num']=intval($_POST['recommend_num']);
	$setsqlarr['recommend_days']=intval($_POST['recommend_days']);
	$setsqlarr['stick_num']=intval($_POST['stick_num']);
	$setsqlarr['stick_days']=intval($_POST['stick_days']);
	$setsqlarr['emergency_num']=intval($_POST['emergency_num']);
	$setsqlarr['emergency_days']=intval($_POST['emergency_days']);
	$setsqlarr['highlight_num']=intval($_POST['highlight_num']);
	$setsqlarr['highlight_days']=intval($_POST['highlight_days']);
	$setsqlarr['change_templates']=intval($_POST['change_templates']);
	$setsqlarr['map_open']=intval($_POST['map_open']);

	$setsqlarr['added']=$_POST['added'];
	if ($_POST['setendtime']<>"")
	{
		$setendtime=convert_datefm($_POST['setendtime'],2);
		if ($setendtime=='')
		{
		adminmsg('���ڸ�ʽ����',0);	
		}
		else
		{
		$setsqlarr['endtime']=$setendtime;
		}
	}
	else
	{
	$setsqlarr['endtime']=0;
	}
	if ($_POST['days']<>"")
	{
			if (intval($_POST['days'])<>0)
			{
				$oldendtime=intval($_POST['oldendtime']);
				$setsqlarr['endtime']=strtotime("".intval($_POST['days'])." days",$oldendtime==0?time():$oldendtime);
			}
			if (intval($_POST['days'])=="0")
			{
				$setsqlarr['endtime']=0;
			}
	}
	$setmealtime=$setsqlarr['endtime'];
	$company_uid=intval($_POST['company_uid']);
	if ($company_uid)
	{
			$setmeal=get_user_setmeal($company_uid);
			if (!updatetable(table('members_setmeal'),$setsqlarr," uid=".$company_uid."")) adminmsg('�޸ĳ���',0);
		//��Ա�ײͱ����¼������Ա��̨�޸Ļ�Ա�ײͣ��޸Ļ�Ա��3��ʾ������Ա��̨�޸�
			$setmeal['endtime']=date('Y-m-d',$setmeal['endtime']);
			$setsqlarr['endtime']=date('Y-m-d',$setsqlarr['endtime']);
			$setsqlarr['log_amount']=round($_POST['log_amount']);
			$notes=edit_setmeal_notes($setsqlarr,$setmeal);
			if($notes){
				$user=get_user($_POST['company_uid']);
				$ismoney=round($_POST['log_amount'])?2:1;
				write_setmeallog($company_uid,$user['username'],$notes,3,$setsqlarr['log_amount'],$ismoney,2,1);
			}

			if ($setsqlarr['endtime']<>"")
			{
				$setmeal_deadline['setmeal_deadline']=$setmealtime;
				if (!updatetable(table('jobs'),$setmeal_deadline," uid='{$company_uid}' AND add_mode='2' "))adminmsg('�޸ĳ���',0);
				if (!updatetable(table('jobs_tmp'),$setmeal_deadline," uid='{$company_uid}' AND add_mode='2' "))adminmsg('�޸ĳ���',0);
				distribution_jobs_uid($company_uid);
			}
	}
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�����ɹ���',2,$link);
}
elseif($act == 'userpass_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	if (strlen(trim($_POST['password']))<6) adminmsg('���������Ϊ6λ���ϣ�',1);
	require_once(ADMIN_ROOT_PATH.'include/admin_user_fun.php');
	$user_info=get_user_inusername($_POST['username']);
	$pwd_hash=$user_info['pwd_hash'];
	$md5password=md5(md5(trim($_POST['password'])).$pwd_hash.$QS_pwdhash);	
	if ($db->query( "UPDATE ".table('members')." SET password = '$md5password'  WHERE uid='".$user_info['uid']."'"))
	{
 	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�����ɹ���',2,$link);
	}
	else
	{
	adminmsg('����ʧ�ܣ�',1);
	}
}
elseif($act == 'userstatus_edit')
{
	check_token();
	check_permissions($_SESSION['admin_purview'],"com_user_edit");
	if ($db->query( "UPDATE ".table('members')." SET status = '".intval($_POST['status'])."'  WHERE uid='".intval($_POST['userstatus_uid'])."'"))
	{
	$link[0]['text'] = "�����б�";
	$link[0]['href'] = $_POST['url'];
	adminmsg('�����ɹ���',2,$link);
	}
	else
	{
	adminmsg('����ʧ�ܣ�',1);
	}
}
 elseif($act == 'del_auditreason')
{	
	//check_token();
	check_permissions($_SESSION['admin_purview'],"jobs_audit");//�õ���ְλ��˵�Ȩ��
	$id =!empty($_REQUEST['a_id'])?$_REQUEST['a_id']:adminmsg("��û��ѡ����־��",1);
	$n=reasonaudit_del($id);
	if ($n>0)
	{
	adminmsg("ɾ���ɹ�����ɾ�� {$n} ��",2);
	}
	else
	{
	adminmsg("ɾ��ʧ�ܣ�",0);
	}
}
  elseif($act == 'management')
{	
	$id=intval($_GET['id']);
	$u=get_user($id);
	if (!empty($u))
	{
		unset($_SESSION['uid']);
		unset($_SESSION['username']);
		unset($_SESSION['utype']);
		unset($_SESSION['uqqid']);
		setcookie("QS[uid]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[username]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[password]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		setcookie("QS[utype]","",time() - 3600,$QS_cookiepath, $QS_cookiedomain);
		unset($_SESSION['activate_username']);
		unset($_SESSION['activate_email']);
		
		$_SESSION['uid']=$u['uid'];
		$_SESSION['username']=$u['username'];
		$_SESSION['utype']=$u['utype'];
		$_SESSION['uqqid']="1";
		setcookie('QS[uid]',$u['uid'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[username]',$u['username'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[password]',$u['password'],0,$QS_cookiepath,$QS_cookiedomain);
		setcookie('QS[utype]',$u['utype'], 0,$QS_cookiepath,$QS_cookiedomain);
		header("Location:".get_member_url($u['utype']));
	}	
} 
?>