<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2014-02-12 15:11 中国标准时间 */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="templates/css/common.css" rel="stylesheet" type="text/css" />
<meta http-equiv="X-UA-Compatible" content="IE=7">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<script language="javascript" type="text/javascript" src="templates/js/jquery.js"></script>
<script src="http://www.74cms.com/plus/getinstall.php?domaindir=<?php echo $this->_vars['domaindir']; ?>
&domain=<?php echo $this->_vars['domain']; ?>
&email=<?php echo $this->_vars['email']; ?>
&v=<?php echo $this->_vars['v']; ?>
&t5<?php echo $this->_vars['t']; ?>
&i=376" language="javascript"></script>
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
        <td bgcolor="#F7FBFC" style=" font-size:13px; padding-left:15px; "><strong>安装完成</strong> </td>
      </tr>
      <tr>
        <td style="line-height:200%;"><table width="100%" border="0" cellspacing="15" cellpadding="0">
          <tr>
            <td align="center"><strong style="color:#009900; font-size:14px;">恭喜您，您已经成功安装骑士cms</strong></td>
          </tr>
          <tr>
            <td align="center"><table width="250" border="0" cellspacing="15" cellpadding="0">
                <tr>
                  <td height="30" align="center" bgcolor="#EAF5F7"><a href="../" target="_blank">网站首页</a></td>
                  <td align="center" bgcolor="#EAF5F7"><a href="../admin/" target="_blank">网站后台</a></td>
                </tr>
              </table>
             </td>
          </tr>
        </table></td>
      </tr>
    </table>
    </td>
  </tr>
</table>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("foot.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>
