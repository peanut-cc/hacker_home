<?php
 /*
 * 74cms ����������ҳ
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
require_once(ADMIN_ROOT_PATH.'include/admin_flash_statement_fun.php');
$act=!empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
if($act=='')
{
	$smarty->display('sys/admin_index.htm');
}
elseif($act=='top')
{
	$admininfo=get_admin_one($_SESSION['admin_name']);
	$smarty->assign('admin_rank', $admininfo['rank']);
	$smarty->assign('admin_name', $_SESSION['admin_name']);
	$smarty->display('sys/admin_top.htm');
}
elseif($act=='left')
{
	$smarty->display('sys/admin_left.htm');
}
elseif($act == 'main')
{
	get_userreg_30_days();
	$install_warning=file_exists('../install')?"����û��ɾ�� install �ļ��У����ڰ�ȫ�Ŀ��ǣ����ǽ�����ɾ�� install �ļ��С�":null;
	$update_warning=file_exists('../update')?"����û��ɾ�� update �ļ��У����ڰ�ȫ�Ŀ��ǣ����ǽ�����ɾ�� update �ļ��С�":null;
	$admindir_warning=substr(ADMIN_ROOT_PATH,-7)=='/admin/'?"������վ��������Ŀ¼Ϊ ./admin �����ڰ�ȫ�Ŀ��ǣ����ǽ������޸�Ŀ¼����":null;
	$admin_register_globals=ini_get('register_globals')?'����php.ini��register_globalsΪOn��ǿ�ҽ�������ΪOff�����򽫻�������صİ�ȫ���������ݴ��ң�':null;
	$system_info = array();
	$system_info['version'] = QISHI_VERSION;
	$system_info['release'] = QISHI_RELEASE;
	$system_info['os'] = PHP_OS;
	$system_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
	$system_info['php_ver'] = PHP_VERSION;
	$system_info['mysql_ver'] = $db->dbversion();
	$system_info['max_filesize'] = ini_get('upload_max_filesize');
	$smarty->assign('site_domain',$_SERVER['SERVER_NAME']);
	$smarty->assign('system_info',$system_info);
	$smarty->assign('random',mt_rand());
	$smarty->assign('install_warning',$install_warning);
	$smarty->assign('update_warning',$update_warning);
	$smarty->assign('admindir_warning',$admindir_warning);
	$smarty->assign('admin_register_globals',$admin_register_globals);
	$smarty->assign('pageheader',"74CMS �������� - ��̨������ҳ");
	$smarty->display('sys/admin_main.htm');
}
?>