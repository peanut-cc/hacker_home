<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2014-02-12 15:10 中国标准时间 */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="templates/css/common.css" rel="stylesheet" type="text/css" />
<meta http-equiv="X-UA-Compatible" content="IE=7">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<title>安装向导 - 骑士PHP人才系统(www.74cms.com)</title>
</head>
<body>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("tip.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:8px;">
  <tr>
    <td width="186" valign="top"><table width="180" border="0" cellspacing="0" cellpadding="0"  class="left_table_right_dot">
      <tr>
        <td>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("left.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>	
		</td>
      </tr>
    </table></td>
    <td valign="top"><table width="98%" border="0" align="center" cellpadding="6" cellspacing="0">
      <tr>
        <td bgcolor="#F7FBFC" style=" font-size:13px; padding-left:15px;  "> <strong>服务器信息</strong> </td>
      </tr>
      <tr>
        <td style="line-height:200%;">
		服务器操作系统：<span style="color:#0066CC"><?php echo $this->_vars['system_info']['os']; ?>
</span><br />
服务器解译引擎：<span style="color:#0066CC"><?php echo $this->_vars['system_info']['web_server']; ?>
</span><br />
PHP版本：<span style="color:#0066CC"><?php echo $this->_vars['system_info']['php_ver']; ?>
</span><br />
上传附件最大值：<span style="color:#0066CC"><?php echo $this->_vars['system_info']['max_filesize']; ?>
</span>
</td>
      </tr>
    </table>
      <table width="98%" border="0" align="center" cellpadding="6" cellspacing="0">
        <tr>
          <td bgcolor="#F7FBFC" style=" font-size:13px; padding-left:15px;  "><strong>目录权限检测</strong> </td>
        </tr>
        <tr>
          <td style="line-height:200%;"><table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="33%" align="center"><strong>目录名</strong></td>
              <td width="33%" align="center"><strong>读取权限</strong></td>
              <td align="center"><strong>写入权限</strong></td>
            </tr>
          	<?php if (isset($this->_sections['dir'])) unset($this->_sections['dir']);
$this->_sections['dir']['name'] = 'dir';
$this->_sections['dir']['loop'] = is_array($this->_vars['dir_check']) ? count($this->_vars['dir_check']) : max(0, (int)$this->_vars['dir_check']);
$this->_sections['dir']['show'] = true;
$this->_sections['dir']['max'] = $this->_sections['dir']['loop'];
$this->_sections['dir']['step'] = 1;
$this->_sections['dir']['start'] = $this->_sections['dir']['step'] > 0 ? 0 : $this->_sections['dir']['loop']-1;
if ($this->_sections['dir']['show']) {
	$this->_sections['dir']['total'] = $this->_sections['dir']['loop'];
	if ($this->_sections['dir']['total'] == 0)
		$this->_sections['dir']['show'] = false;
} else
	$this->_sections['dir']['total'] = 0;
if ($this->_sections['dir']['show']):

		for ($this->_sections['dir']['index'] = $this->_sections['dir']['start'], $this->_sections['dir']['iteration'] = 1;
			 $this->_sections['dir']['iteration'] <= $this->_sections['dir']['total'];
			 $this->_sections['dir']['index'] += $this->_sections['dir']['step'], $this->_sections['dir']['iteration']++):
$this->_sections['dir']['rownum'] = $this->_sections['dir']['iteration'];
$this->_sections['dir']['index_prev'] = $this->_sections['dir']['index'] - $this->_sections['dir']['step'];
$this->_sections['dir']['index_next'] = $this->_sections['dir']['index'] + $this->_sections['dir']['step'];
$this->_sections['dir']['first']	  = ($this->_sections['dir']['iteration'] == 1);
$this->_sections['dir']['last']	   = ($this->_sections['dir']['iteration'] == $this->_sections['dir']['total']);
?>
			<tr>
				<td><?php echo $this->_vars['dir_check'][$this->_sections['dir']['index']]['dir']; ?>
</td>
				<td align="center"><?php echo $this->_vars['dir_check'][$this->_sections['dir']['index']]['read']; ?>
</td>
				<td align="center"><?php echo $this->_vars['dir_check'][$this->_sections['dir']['index']]['write']; ?>
</td>
			</tr>
			<?php endfor; endif; ?>
          </table>          </td>
        </tr>
        <tr>
          <td height="55" align="center"  >
		  <form action="index.php" method="get">
	<input name="act" type="hidden" value="3" />
	<input name="" type="button"  class="step_submit" onclick="window.location.href='index.php?act=1';" value="上一步" />
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
        <input type="submit" name="" value="下一步"  class="step_submit" />
		  </form>
		  </td>
        </tr>
      </table></td>
  </tr>
</table>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("foot.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>
