<?php
 /*
 * 74cms ��֤����
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
$act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : ''; 
$email=trim($_POST['email']);
$send_key=trim($_POST['send_key']);
if (empty($send_key) || $send_key<>$_SESSION['send_key'])
{
exit("Ч�������");
}
if ($act=="send_code")
{
		if (empty($email) || !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]w+)*$/",$email))
		{
		exit("�����ʽ����");
		}
		$sql = "select * from ".table('members')." where email = '{$email}' LIMIT 1";
		$userinfo=$db->getone($sql);
		if ($userinfo && $userinfo['uid']<>$_SESSION['uid'])
		{
		exit("�����Ѿ����ڣ�����д��������");
		}
		elseif(!empty($userinfo['email']) && $userinfo['email_audit']=="1" && $userinfo['email']==$email)
		{
		exit("������� {$email} �Ѿ�ͨ����֤��");
		}
		else
		{
			if ($_SESSION['sendemail_time'] && (time()-$_SESSION['sendemail_time'])<60)
			{
			exit("��60����ٽ�����֤��");
			}
			$rand=mt_rand(100000, 999999);
			if (smtp_mail($email,"{$_CFG['site_name']}�ʼ���֤","{$QISHI['site_name']}��������<br>�����ڽ���������֤����֤��Ϊ:<strong>{$rand}</strong>"))
			{
			$_SESSION['verify_email']=$email;
			$_SESSION['email_rand']=$rand;
			$_SESSION['sendemail_time']=time();
			exit("success");
			}
			else
			{
			exit("�������ó�������ϵ��վ����Ա");
			}
		} 
}
elseif ($act=="verify_code")
{
	$verifycode=trim($_POST['verifycode']);
	if (empty($verifycode) || empty($_SESSION['email_rand']) || $verifycode<>$_SESSION['email_rand'])
	{
		exit("��֤�����");
	}
	else
	{
			$uid=intval($_SESSION['uid']);
			if (empty($uid))
			{
				exit("ϵͳ����UID��ʧ��");
			}
			else
			{
					$setsqlarr['email']=$_SESSION['verify_email'];
					$setsqlarr['email_audit']=1;
					updatetable(table('members'),$setsqlarr," uid='{$uid}'");
					if ($_SESSION['utype']=="2")
					{
					$u['email']=$_SESSION['verify_email'];
					updatetable(table('resume'),$u," uid='{$uid}'");
					updatetable(table('resume_tmp'),$u," uid='{$uid}'");
					}
					unset($setsqlarr,$_SESSION['verify_email'],$_SESSION['email_rand'],$u);
					if ($_CFG['operation_mode']=='1' && $_SESSION['utype']=='1')
					{
						$rule=get_cache('points_rule');
						if ($rule['verifyemail']['value']>0)
						{
							$info=$db->getone("SELECT uid FROM ".table('members_handsel')." WHERE uid ='{$_SESSION['uid']}' AND htype='verifyemail'   LIMIT 1");
							if(empty($info))
							{
							$time=time();			
							$db->query("INSERT INTO ".table('members_handsel')." (uid,htype,addtime) VALUES ('{$_SESSION['uid']}', 'verifyemail','{$time}')");
							require_once(QISHI_ROOT_PATH.'include/fun_company.php');
							report_deal($_SESSION['uid'],$rule['verifyemail']['type'],$rule['verifyemail']['value']);
							$user_points=get_user_points($_SESSION['uid']);
							$operator=$rule['verifyemail']['type']=="1"?"+":"-";
							$_SESSION['handsel_verifyemail']=$_CFG['points_byname'].$operator.$rule['verifyemail']['value'];
							write_memberslog($_SESSION['uid'],1,9001,$_SESSION['username']," ����ͨ����֤��{$_CFG['points_byname']}({$operator}{$rule['verifyemail']['value']})��(ʣ��:{$user_points})");
							}
						}
					} 
					exit("success");
			}
	}
}
?>