<?php
 /*
 * 74cms ��˲�ͨ��ԭ��
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : 'jobs_reason'; 
$id=intval($_GET['id']);
if($act=='jobs_reason'){
	$column="jobs_id";
}elseif($act=='resume_reason'){
	$column="resume_id";
}elseif($act=='company_reason'){
	$column="company_id";
}
if ($id)
{
	$result = $db->getone("SELECT * FROM ".table('audit_reason')." WHERE `{$column}`={$id} ORDER BY id DESC LIMIT 1");
	if(empty($result) && $column=='company_id'){exit('�����ύ��֤���ϣ���֤ͨ�����������Ϣ�Ŀ��Ŷȣ������ܶ������ͻ���Ŷ��');}
	exit($result['reason']);
}
?>