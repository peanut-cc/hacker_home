<?php
 /*
 * 74cms ��ʼ��smarty����
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
if(!defined('IN_QISHI'))
{
die('Access Denied!');
}
include_once(QISHI_ROOT_PATH.'include/template_lite/class.template.php');
$smarty = new Template_Lite; 
$smarty -> compile_dir =  QISHI_ROOT_PATH.'temp/templates_c';
$smarty -> template_dir = ADMIN_ROOT_PATH."templates/default/";
$smarty -> cache_dir = QISHI_ROOT_PATH.'temp/caches';
$smarty -> reserved_template_varname = "smarty";
$smarty -> cache = false;
$smarty -> left_delimiter = "{#";
$smarty -> right_delimiter = "#}";
$smarty -> force_compile = false;
$smarty -> assign('QISHI',$_CFG);
$smarty -> assign('QISHI_VERSION', QISHI_VERSION.".".QISHI_RELEASE);
?>