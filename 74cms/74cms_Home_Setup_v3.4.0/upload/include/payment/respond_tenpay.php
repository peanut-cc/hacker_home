<?php 
 /*
 * 74cms ֧����Ӧҳ��
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
define('IN_QISHI', true);
require_once(dirname(__FILE__).'/../common.inc.php');
require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname);
$funtype=array('1'=>'include/fun_company.php',4=>'include/fun_train.php',3=>'include/fun_hunter.php');
require_once(QISHI_ROOT_PATH.$funtype[$_SESSION['utype']]);
require_once(QISHI_ROOT_PATH."include/payment/tenpay.php");
	if (respond())
	{
		$orderurl=array('1'=>'company_service.php?act=order_list',4=>'train_service.php?act=order_list',3=>'hunter_service.php?act=order_list');
		$link[0]['text'] = "�鿴����";
		$link[0]['href'] = get_member_url($_SESSION['utype'],true).$orderurl[$_SESSION['utype']];
		$link[1]['text'] = "��Ա����";
		$link[1]['href'] = url_rewrite('QS_login');		
		$link[2]['text'] = "��վ��ҳ";
		$link[2]['href'] = $_CFG['site_dir'];
		showmsg("����ɹ���",2,$link,false);
	}
	else
	{
		$link[0]['text'] = "��Ա����";
		$link[0]['href'] = get_member_url($_SESSION['utype']);
		showmsg("����ʧ�ܣ�����ϵ��վ����Ա",0,$link);
	}
?>
