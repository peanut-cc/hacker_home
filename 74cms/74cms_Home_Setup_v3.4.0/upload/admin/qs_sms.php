<?php
 /*
 * 74cms ���ŷ���
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
require_once(dirname(__FILE__).'/include/crm_common.inc.php');
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'send';
check_permissions($_SESSION['crm_admin_purview'],"send_sms");
$smarty->assign('pageheader',"����Ӫ��");
if($act == 'send')
{
	get_token();
	//$smarty->assign('navlabel','testing');
	$url=trim($_REQUEST['url']);
	if (empty($url))
	{
	$url="?act=send";
	}
	$smarty->assign('url',$url);
	$smarty->display('sms/crm_sms_send.htm');
}
elseif($act == 'sms_send')
{
	$txt=trim($_POST['txt']);
	$mobile=trim($_POST['mobile']);
	$url=trim($_REQUEST['url']);
	if (empty($txt))
	{
	crmmsg('�������ݲ���Ϊ�գ�',0);
	}
	if (empty($mobile))
	{
	crmmsg('�ֻ�����Ϊ�գ�',0);
	}
	if (!preg_match("/^(13|15|18|14)\d{9}$/",$mobile))
	{
		$link[0]['text'] = "������һҳ";
		$link[0]['href'] = "{$url}";
		crmmsg("����ʧ�ܣ�<strong>{$mobile}</strong> ���Ǳ�׼���ֻ��Ÿ�ʽ",1,$link);
		
	}
	else
	{
			$r=send_sms($mobile,$txt);
			if ($r=="success")
			{
				$link[0]['text'] = "������һҳ";
				$link[0]['href'] = "{$url}";
				crmmsg("���ͳɹ���",2,$link);
			}
			else
			{
				$link[0]['text'] = "������һҳ";
				$link[0]['href'] = "{$url}";
				crmmsg("����ʧ�ܣ�����δ֪��",2,$link);
			}
	}
}
?>