<?php
 /*
 * 74cms �������� ֧����ʽ
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
//��ȡ֧����ʽ�б�
function get_payment($type="")
{
	global $db;
	if (!empty($type)) $wheresql="  WHERE p_install=".intval($type)."  ";
	$sql = "select * from ".table('payment')." ".$wheresql." order BY listorder desc";
	$list=$db->getall($sql);
	return $list;
}
//��ȡ����֧����ʽ
function get_payment_one($name){
global $db;
$sql = "select * from ".table('payment')." WHERE typename='".$name."'";
$info=$db->getone($sql);
return $info;
}
//ж��֧����ʽ
function uninstall_payment($id)
{
global $db;
if (!intval($id)) return false;
$sql= "UPDATE ".table('payment')." SET p_install='1' WHERE id='$id'";
if (!$db->query($sql))return false;
return true;
}
//�޸�֧���б�����
function edit_payment_listorder($id,$eid)
{
global $db;
if (!intval($id) || !intval($eid)) return false;
$sql= "UPDATE ".table('payment')." SET listorder='$eid' WHERE id='$id'";
if (!$db->query($sql))return false;
return true;
}
?>