<?php
 /*
 * 74cms �������� ģ������
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
function get_templates_info($file){
	$file_info = array('name'=>'', 'version'=> '', 'author'=>'', 'authorurl'=>'');
    if (!$fp = @fopen($file,'rb'))
	{
		 return false;
	}
    $str = fread($fp, 200);
    @fclose($fp);
    $arr = explode("\n", $str);
    foreach ($arr as $val){
        $pos = strpos($val, ':');
        if ($pos > 0){
            $type = trim(substr($val, 0, $pos), "-\n\r\t ");
            $value = trim(substr($val, $pos+1), "/\n\r\t ");
            if ($type == 'name'){
                $file_info['name'] = $value;
            }
            elseif ($type == 'version'){
                $file_info['version'] = $value;
            }
            elseif ($type == 'author'){
                $file_info['author'] = $value;
            }
			 elseif ($type == 'authorurl'){
                $file_info['authorurl'] = $value;
            }
        }
    }
    return $file_info;
}
function get_user_tpl($type,$tpldir)
{
	global $db;
	$type=intval($type);
	$result = $db->query("select * from ".table('tpl')." where tpl_type='{$type}'");
	while($row = $db->fetch_array($result))
	{
	$row['info']=get_templates_info("../templates/".$tpldir."/".$row['tpl_dir']."/info.txt");
	$row_arr[] =$row;
	}
	return $row_arr;
}
function get_user_tpl_dir($type)
{
	global $db;
	$type=intval($type);
	$result = $db->query("select tpl_dir from ".table('tpl')." where tpl_type='{$type}'");
	while($row = $db->fetch_array($result))
	{
	$row_arr[] =$row['tpl_dir'];
	}
	return $row_arr;
}
?>