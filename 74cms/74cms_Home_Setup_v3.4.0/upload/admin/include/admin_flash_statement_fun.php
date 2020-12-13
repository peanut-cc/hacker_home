<?php
 /*
 * 74cms FLASH����
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

function get_userreg_30_days()
{
	global $db;
	$xml="userreg_30_days.xml";
	if (!check_xml($xml))
	{
		$datelist=array();
		for ($i = 30; $i>=1; $i--)
		{
		$day=date("m/d",strtotime("-{$i} day"));
		$datelist[$day]=0;
		}
		$result = $db->query("SELECT * FROM ".table('members_log')." WHERE log_type=1000 and  log_addtime>".strtotime("-30 day"));
		while($row = $db->fetch_array($result))
		{
			$date=date("m/d",$row['log_addtime']);
			$datelist[$date]++;
		}
		write_xml($xml,$datelist);
	}	 
}
function meal_log_pie($pie_type='1',$utype='1')
{
	global $db,$_CFG;
	$xml="meal_log_pie.xml";
	$datelist=array();
	$nowtime=mktime(0,0,0,0,0,date('Y'));//��ȡ��ǰ���ʱ���
	$result = $db->query("SELECT log_amount,log_addtime FROM ".table('members_charge_log')." WHERE log_mode={$_CFG['operation_mode']} AND  log_ismoney=2 AND log_addtime>{$nowtime} AND log_utype={$utype}");
	while($row = $db->fetch_array($result))
	{
		if($pie_type=='1'){
			$date=date("Y/m",$row['log_addtime']);
			$datelist[$date]+=$row['log_amount'];
		}elseif($pie_type=='2'){
			$date=date('n',$row['log_addtime']);
			if($date>=1 && $date<4){
				$datelist['��һ����']+=$row['log_amount'];
			}elseif($date>=4 && $date<=6){
				$datelist['�ڶ�����']+=$row['log_amount'];
			}elseif($date>6 && $date<=9){
				$datelist['��������']+=$row['log_amount'];
			}elseif($date>9 && $date<=12){
				$datelist['���ļ���']+=$row['log_amount'];
			}
		}
	}		
	write_client($xml,$datelist);
}
function check_xml($xml)
{
	$xmlname=ADMIN_ROOT_PATH."statement/{$xml}";
	if (!is_writable(ADMIN_ROOT_PATH.'statement/'))
	{
	exit("���Ƚ���̨��statement��Ŀ¼���ÿɶ�д��");
	}
	if (file_exists($xmlname))
	{
		$filemtime=filemtime($xmlname);
		if ($filemtime>strtotime("-1 day"))
		{
			return true;
		}
	}
	return false;
}
function write_xml($xml, $array)
{
	$content = "<graph divlinecolor='FEDD69' numdivlines='4' showAreaBorder='1' areaBorderColor='cccccc' numberPrefix='' showNames='1' numVDivLines='29' vDivLineAlpha='30' formatNumberScale='0' rotateNames='1'  decimalPrecision='0' bgColor='' yAxisName=''  showAlternateVGridColor='0' canvasBorderThickness='0' decimalPrecision='0' areaBorderColor='cccccc'>
>\n";
	$content .= "<categories fontSize='9'>\n";
	foreach($array as $key => $value)
	{
	$content .= "<category name='{$key}'/>\n";
	}
	$content .= "</categories>\n";
	$content .= "<dataset  color='00CC00' showValues='0' areaAlpha='30' showAreaBorder='1' areaBorderThickness='1' areaBorderColor='006600'>\n";
	foreach($array as $key => $value)
	{
	$content .= "<set value='{$value}' />\n";
	}
	$content .= "</dataset>\n";
	$content .= "</graph>\n";
	$xmlname=ADMIN_ROOT_PATH."statement/{$xml}";
	if (!file_put_contents($xmlname, $content, LOCK_EX))
	{
		$fp = @fopen($xmlname, 'wb+');
		if (!$fp)
		{
			exit('��xml�ļ�ʧ�ܣ������ú�̨Ŀ¼��statement���Ķ�дȨ��');
		}
		if (!@fwrite($fp, trim($content)))
		{
			exit('��xml�ļ�ʧ�ܣ������ú�̨Ŀ¼��statement���Ķ�дȨ��');
		}
		@fclose($fp);
	}
}
function write_client($xml,$array)
{
	$content = '<chart ShowAboutMenuItem="0"  numberPrefix=" "  exportEnabled="1" exportShowMenuItem="1" exportAtClient="1" exportHandler="fcExporter1" baseFont="����"  baseFontSize="14" bgColor="#FFFFFF" shadowAlpha="100" canvasBgColor="#FFFFFF"  >\n';
	foreach($array as $key => $value)
	{
	$content .= "<set name='{$key}' value='{$value}' /> \n";
	}
	$content .= "</chart>\n";
	$xmlname=ADMIN_ROOT_PATH."statement/{$xml}";
	if (!file_put_contents($xmlname, $content, LOCK_EX))
	{
		$fp = @fopen($xmlname, 'wb+');
		if (!$fp)
		{
			exit('��xml�ļ�ʧ�ܣ������ú�̨Ŀ¼��statement���Ķ�дȨ��');
		}
		if (!@fwrite($fp, trim($content)))
		{
			exit('��xml�ļ�ʧ�ܣ������ú�̨Ŀ¼��statement���Ķ�дȨ��');
		}
		@fclose($fp);
	}
}

?>