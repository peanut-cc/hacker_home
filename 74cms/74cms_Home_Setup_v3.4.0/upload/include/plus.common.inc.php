<?php
 /*
 * 74cms plus���������ļ�
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
if(!defined('IN_QISHI')) exit('Access Denied!');
define('QISHI_ROOT_PATH', dirname(dirname(__FILE__)).'/');
error_reporting(E_ERROR);
require_once(QISHI_ROOT_PATH.'data/config.php');
ini_set('session.save_handler', 'files');
session_save_path(QISHI_ROOT_PATH.'data/sessions/');
session_start();
header("Content-Type:text/html;charset=".QISHI_CHARSET);
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
require_once(QISHI_ROOT_PATH.'include/common.fun.php');
if (!empty($_GET))
{
$_GET  = addslashes_deep($_GET);
}
if (!empty($_POST))
{
$_POST = addslashes_deep($_POST);
}
$_COOKIE   = addslashes_deep($_COOKIE);
$_REQUEST  = addslashes_deep($_REQUEST);
PHP_VERSION > '5.1'?date_default_timezone_set("PRC"):'';
$timestamp = time();
$online_ip=getip();

$ip_address=convertip($online_ip);

$_CFG=get_cache('config');
$_PAGE=get_cache('page');
$_NAV=get_cache('nav');
$_CFG['version']=QISHI_VERSION;
$_CFG['web_logo']=$_CFG['web_logo']?$_CFG['web_logo']:'logo.gif';
$_CFG['upfiles_dir']=$_CFG['site_dir']."data/".$_CFG['updir_images']."/";
$_CFG['site_template']=$_CFG['site_dir'].'templates/'.$_CFG['template_dir'];
execution_crons();
?>