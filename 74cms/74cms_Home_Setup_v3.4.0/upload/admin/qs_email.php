<?php
 /*
 * 74cms �ʼ�����
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
$act = !empty($_GET['act']) ? trim($_GET['act']) : 'send';
$smarty->assign('pageheader',"�ʼ�Ӫ��");
//��Ҫע��
check_permissions($_SESSION['admin_purview'],"send_email");
if($act == 'send')
{
	get_token();
	$url=trim($_REQUEST['url']);
	if (empty($url))
	{
	$url="?act=send";
	}
	$smarty->assign('url',$url);
	$smarty->display('mail/admin_mail_send.htm');
}
elseif($act == 'email_send')
{
	$email=trim($_POST['email']);
	$subject=trim($_POST['subject']);
	$body=trim($_POST['body']);
	$url=trim($_REQUEST['url']);
	if (empty($subject) || empty($body))
	{
	crmmsg('��������ݲ���Ϊ�գ�',0);
	}
	if (empty($email))
	{
	crmmsg('�ռ��˲���Ϊ�գ�',0);
	}
	if (!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/",$email))
	{
		$link[0]['text'] = "������һҳ";
		$link[0]['href'] = "{$url}";
		crmmsg("����ʧ�ܣ�<strong>{$mobile}</strong> ��ʽ����ȷ",1,$link);
		
	}
	else
	{
			if (smtp_mail($email,$subject,$body))
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