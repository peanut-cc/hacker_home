<?php
/**
 * 74cms �������߲��
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
//����֧������
function get_code($order, $payment)
    {
	global $_CFG;
	if (!is_array($order) ||!is_array($payment))  return false;
        $data_mid  = trim($payment['partnerid']);//�̻����
        $data_oid   = $order['oid'];//������
        $data_amount  = intval($order['v_amount']); //֧�����  
        $data_moneytype  = 'CNY';//����
        $data_key  = trim($payment['ytauthkey']);//MD5��Կ
        $data_url = $order['v_url'];//����url,��ַӦΪ����·��,����httpЭ��
		$data_remark1 = $order['remark1'];//��ע1
        $MD5KEY =$data_amount.$data_moneytype.$data_oid.$data_mid.$data_url.$data_key;
        $MD5KEY = strtoupper(md5($MD5KEY)); //md5����ƴ�մ�,ע��˳���ܱ�
        $def_url  = '<form name="E_FORM"  method="post" action="https://pay3.chinabank.com.cn/PayGate" target="_blank">';
        $def_url .= "<input type=HIDDEN name='v_mid' value='".$data_mid."'>";//�̻����
        $def_url .= "<input type=HIDDEN name='v_oid' value='".$data_oid."'>";//������
        $def_url .= "<input type=HIDDEN name='v_amount' value='".$data_amount."'>"; //֧�����  
        $def_url .= "<input type=HIDDEN name='v_moneytype'  value='".$data_moneytype."'>";//����
        $def_url .= "<input type=HIDDEN name='v_url'  value='".$data_url."'>";//����url,��ַӦΪ����·��,����httpЭ��
        $def_url .= "<input type=HIDDEN name='v_md5info' value='".$MD5KEY."'>"; //md5����ƴ�մ�
        $def_url .= "<input type=HIDDEN name='remark1' value='".$remark1."'>";//��ע
        $def_url .= "</form>";
		$def_url .= "<input type=\"image\" name=\"imageField\" src='".$_CFG['site_template']."images/25.gif'  onclick=\"document.E_FORM.submit()\"/>";
        return $def_url;
    }
/**
 * ��Ӧ����
*/
function respond()
{
$payment        = get_payment_info('chinabank');
$v_oid          = trim($_POST['v_oid']);
$v_pmode        = trim($_POST['v_pmode']);
$v_pstatus      = trim($_POST['v_pstatus']);
$v_pstring      = trim($_POST['v_pstring']);
$v_amount       = trim($_POST['v_amount']);
$v_moneytype    = trim($_POST['v_moneytype']);
$remark1        = trim($_POST['remark1']);
$remark2        = trim($_POST['remark2']);
$v_md5str       = trim($_POST['v_md5str']);
/**
* ���¼���md5��ֵ
*/
$key = $payment['ytauthkey'];

$md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));
 /* �����Կ�Ƿ���ȷ */
if ($v_md5str==$md5string)
{
if ($v_pstatus == '20')
{
/* �ı䶩��״̬ */
if (!order_paid($v_oid)) return false;
return true;
}
}
else
{
return false;
}
}
//��ȡ�����ַ�
function pay_info()
{
$arr['p_introduction']="�������߼��������";
$arr['notes']="����������ϸ������";
$arr['partnerid']="���������̻���ţ�";
$arr['ytauthkey']="��������MD5 ��Կ��";
$arr['fee']="�������߽��������ѣ�";
return $arr;
}
?>