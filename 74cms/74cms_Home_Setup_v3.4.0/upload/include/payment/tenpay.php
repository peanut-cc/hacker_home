<?php
/**
 * 74cms �Ƹ�֧ͨ�����
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
	$bargainor_id = trim($payment['partnerid']);////�̻����
	$key=trim($payment['ytauthkey']);//MD5��Կ
	$return_url=$order['v_url'];//����url,��ַӦΪ����·��,����httpЭ��
	//date_default_timezone_set(PRC);
    $strDate = date("Ymd");
    $strTime = date("His");
    $randNum = rand(1000, 9999);//4λ�����
    $strReq = $strTime . $randNum;//10λ���к�,�������е�����	
    $transaction_id = $bargainor_id . $strDate . $strReq;/* �Ƹ�ͨ���׵��ţ�����Ϊ��10λ�̻���+8λʱ�䣨YYYYmmdd)+10λ��ˮ�� */
	$sp_billno = $order['oid'];//������ �̼Ҷ�����,����������32λ��ȡǰ32λ���Ƹ�ֻͨ��¼�̼Ҷ����ţ�����֤Ψһ��
	//$total_fee ="1";/* ��Ʒ�۸񣨰����˷ѣ����Է�Ϊ��λ */
	$total_fee =intval($order['v_amount'])*100;/* ��Ʒ�۸񣨰����˷ѣ����Է�Ϊ��λ */
	$desc = "�����ţ�" . $transaction_id;
	/* ����֧��������� */
    $reqHandler = new PayRequestHandler();
    $reqHandler->init();
    $reqHandler->setKey($key);
	$reqHandler->setParameter("bargainor_id", $bargainor_id);			//�̻���
	$reqHandler->setParameter("sp_billno", $sp_billno);					//�̻�������
	$reqHandler->setParameter("transaction_id", $transaction_id);		//�Ƹ�ͨ���׵���
	$reqHandler->setParameter("total_fee", $total_fee);					//��Ʒ�ܽ��,�Է�Ϊ��λ
	$reqHandler->setParameter("return_url", $return_url);				//���ش����ַ
	$reqHandler->setParameter("desc", $desc);	//��Ʒ����
	$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
	$reqUrl = $reqHandler->getRequestURL();
	$def_url  ="<a href=\"".$reqUrl."\" target=\"_blank\"><img src=\"".$_CFG['site_template']."images/25.gif\" border=\"0\"/></a>";
	 return $def_url;
    }
/**
 * ��Ӧ����
*/
function respond()
{
$payment= get_payment_info('tenpay');
$key = $payment['ytauthkey'];
/* ����֧��Ӧ����� */
$resHandler = new PayResponseHandler();
$resHandler->setKey($key);
	if($resHandler->isTenpaySign())
	{
	//�̻�����
	$sp_billno = $resHandler->getParameter("sp_billno");
	//�Ƹ�ͨ���׵���
	$transaction_id = $resHandler->getParameter("transaction_id");
	//���,�Է�Ϊ��λ
	$total_fee = $resHandler->getParameter("total_fee");
	$pay_result = $resHandler->getParameter("pay_result");
		if( "0" == $pay_result ) 
		{
		return order_paid($sp_billno);
		}
		else
		{
		return false;
		}
	}
	else
	{
	return false;
	}
}
function pay_info()
{
$arr['p_introduction']="�Ƹ�ͨ���������";
$arr['notes']="�Ƹ�ͨ��ϸ������";
$arr['partnerid']="�Ƹ�ͨ�̻���ţ�";
$arr['ytauthkey']="�Ƹ�ͨMD5 ��Կ��";
$arr['fee']="�Ƹ�ͨ���������ѣ�";
return $arr;
}
//----------------------------------------------------
//�Ƹ�ͨ�Դ�class
class PayRequestHandler extends RequestHandler {
	
	function __construct() {
		$this->PayRequestHandler();
	}
	
	function PayRequestHandler() {
		//Ĭ��֧�����ص�ַ
		$this->setGateURL("https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi");	
	}
	
	/**
	*@Override
	*��ʼ��������Ĭ�ϸ�һЩ������ֵ����cmdno,date�ȡ�
	*/
	function init() {
		//�������
		$this->setParameter("cmdno", "1");
		
		//����
		$this->setParameter("date",  date("Ymd"));
		
		//�̻���
		$this->setParameter("bargainor_id", "");
		
		//�Ƹ�ͨ���׵���
		$this->setParameter("transaction_id", "");
		
		//�̼Ҷ�����
		$this->setParameter("sp_billno", "");
		
		//��Ʒ�۸��Է�Ϊ��λ
		$this->setParameter("total_fee", "");
		
		//��������
		$this->setParameter("fee_type",  "1");
		
		//����url
		$this->setParameter("return_url",  "");
		
		//�Զ������
		$this->setParameter("attach",  "");
		
		//�û�ip
		$this->setParameter("spbill_create_ip",  "");
		
		//��Ʒ����
		$this->setParameter("desc",  "");
		
		//���б���
		$this->setParameter("bank_type",  "0");
		
		//�ַ�������
		$this->setParameter("cs",  "gbk");
		
		//ժҪ
		$this->setParameter("sign",  "");
		
	}
	
	/**
	*@Override
	*����ǩ��
	*/
	function createSign() {
		$cmdno = $this->getParameter("cmdno");
		$date = $this->getParameter("date");
		$bargainor_id = $this->getParameter("bargainor_id");
		$transaction_id = $this->getParameter("transaction_id");
		$sp_billno = $this->getParameter("sp_billno");
		$total_fee = $this->getParameter("total_fee");
		$fee_type = $this->getParameter("fee_type");
		$return_url = $this->getParameter("return_url");
		$attach = $this->getParameter("attach");
		$spbill_create_ip = $this->getParameter("spbill_create_ip");
		$key = $this->getKey();
		
		$signPars = "cmdno=" . $cmdno . "&" .
				"date=" . $date . "&" .
				"bargainor_id=" . $bargainor_id . "&" .
				"transaction_id=" . $transaction_id . "&" .
				"sp_billno=" . $sp_billno . "&" .
				"total_fee=" . $total_fee . "&" .
				"fee_type=" . $fee_type . "&" .
				"return_url=" . $return_url . "&" .
				"attach=" . $attach . "&";
		
		if($spbill_create_ip != "") {
			$signPars .= "spbill_create_ip=" . $spbill_create_ip . "&";
		}
		
		$signPars .= "key=" . $key;
		
		$sign = strtolower(md5($signPars));
		
		$this->setParameter("sign", $sign);
		
		//debug��Ϣ
		$this->_setDebugInfo($signPars . " => sign:" . $sign);
		
	}

}
class PayResponseHandler extends ResponseHandler {
	
	/**
	*@Override
	*/
	function isTenpaySign() {
		$cmdno = $this->getParameter("cmdno");
		$pay_result = $this->getParameter("pay_result");
		$date = $this->getParameter("date");
		$transaction_id = $this->getParameter("transaction_id");
		$sp_billno = $this->getParameter("sp_billno");
		$total_fee = $this->getParameter("total_fee");		
		$fee_type = $this->getParameter("fee_type");
		$attach = $this->getParameter("attach");
		$key = $this->getKey();
		
		$signPars = "";
		//��֯ǩ����
		$signPars = "cmdno=" . $cmdno . "&" .
				"pay_result=" . $pay_result . "&" .
				"date=" . $date . "&" .
				"transaction_id=" . $transaction_id . "&" .
				"sp_billno=" . $sp_billno . "&" .
				"total_fee=" . $total_fee . "&" .
				"fee_type=" . $fee_type . "&" .
				"attach=" . $attach . "&" .
				"key=" . $key;
				
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
		
		//debug��Ϣ
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;
		
	}
	
}
class RequestHandler {
	
	/** ����url��ַ */
	var $gateUrl;
	
	/** ��Կ */
	var $key;
	
	/** ����Ĳ��� */
	var $parameters;
	
	/** debug��Ϣ */
	var $debugInfo;
	
	function __construct() {
		$this->RequestHandler();
	}
	
	function RequestHandler() {
		$this->gateUrl = "https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi";
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
	}
	
	/**
	*��ʼ��������
	*/
	function init() {
		//nothing to do
	}
	
	/**
	*��ȡ��ڵ�ַ,����������ֵ
	*/
	function getGateURL() {
		return $this->gateUrl;
	}
	
	/**
	*������ڵ�ַ,����������ֵ
	*/
	function setGateURL($gateUrl) {
		$this->gateUrl = $gateUrl;
	}
	
	/**
	*��ȡ��Կ
	*/
	function getKey() {
		return $this->key;
	}
	
	/**
	*������Կ
	*/
	function setKey($key) {
		$this->key = $key;
	}
	
	/**
	*��ȡ����ֵ
	*/
	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}
	
	/**
	*���ò���ֵ
	*/
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	
	/**
	*��ȡ��������Ĳ���
	*@return array
	*/
	function getAllParameters() {
		return $this->parameters;
	}
	
	/**
	*��ȡ������������URL
	*/
	function getRequestURL() {
	
		$this->createSign();
		
		$reqPar = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			$reqPar .= $k . "=" . urlencode($v) . "&";
		}
		
		//ȥ�����һ��&
		$reqPar = substr($reqPar, 0, strlen($reqPar)-1);
		
		$requestURL = $this->getGateURL() . "?" . $reqPar;
		
		return $requestURL;
		
	}
		
	/**
	*��ȡdebug��Ϣ
	*/
	function getDebugInfo() {
		return $this->debugInfo;
	}
	
	/**
	*�ض��򵽲Ƹ�֧ͨ��
	*/
	function doSend() {
		header("Location:" . $this->getRequestURL());
		exit;
	}
	
	/**
	*����md5ժҪ,������:����������a-z����,������ֵ�Ĳ������μ�ǩ����
	*/
	function createSign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("" != $v && "sign" != $k) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$this->setParameter("sign", $sign);
		
		//debug��Ϣ
		$this->_setDebugInfo($signPars . " => sign:" . $sign);
		
	}	
	
	/**
	*����debug��Ϣ
	*/
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}

}
class ResponseHandler  {
	
	/** ��Կ */
	var $key;
	
	/** Ӧ��Ĳ��� */
	var $parameters;
	
	/** debug��Ϣ */
	var $debugInfo;
	
	function __construct() {
		$this->ResponseHandler();
	}
	
	function ResponseHandler() {
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
		
		/* GET */
		foreach($_GET as $k => $v) {
			$this->setParameter($k, $v);
		}
		/* POST */
		foreach($_POST as $k => $v) {
			$this->setParameter($k, $v);
		}
	}
		
	/**
	*��ȡ��Կ
	*/
	function getKey() {
		return $this->key;
	}
	
	/**
	*������Կ
	*/	
	function setKey($key) {
		$this->key = $key;
	}
	
	/**
	*��ȡ����ֵ
	*/	
	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}
	
	/**
	*���ò���ֵ
	*/	
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	
	/**
	*��ȡ��������Ĳ���
	*@return array
	*/
	function getAllParameters() {
		return $this->parameters;
	}	
	
	/**
	*�Ƿ�Ƹ�ͨǩ��,������:����������a-z����,������ֵ�Ĳ������μ�ǩ����
	*true:��
	*false:��
	*/	
	function isTenpaySign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug��Ϣ
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;
		
	}
	
	/**
	*��ȡdebug��Ϣ
	*/	
	function getDebugInfo() {
		return $this->debugInfo;
	}
	
	/**
	*��ʾ��������
	*@param $show_url ��ʾ��������url��ַ,����url��ַ����ʽ(http://www.xxx.com/xxx.php)��
	*/	
	function doShow($show_url) {
		$strHtml = "<html><head>\r\n" .
			"<meta name=\"TENCENT_ONLINE_PAYMENT\" content=\"China TENCENT\">" .
			"<script language=\"javascript\">\r\n" .
				"window.location.href='" . $show_url . "';\r\n" .
			"</script>\r\n" .
			"</head><body></body></html>";
			
		echo $strHtml;
		
		exit;
	}
	
	/**
	 * �Ƿ�Ƹ�ͨǩ��
	 * @param signParameterArray ǩ���Ĳ�������
	 * @return boolean
	 */	
	function _isTenpaySign($signParameterArray) {
	
		$signPars = "";
		foreach($signParameterArray as $k) {
			$v = $this->getParameter($k);
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}			
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug��Ϣ
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;		
		
	
	}
	
	/**
	*����debug��Ϣ
	*/	
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}
	
}
?>