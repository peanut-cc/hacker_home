<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2014-02-12 15:10 �й���׼ʱ�� */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="templates/css/common.css" rel="stylesheet" type="text/css" />
<meta http-equiv="X-UA-Compatible" content="IE=7">
<link rel="shortcut icon" href="<?php echo $this->_vars['QISHI']['site_dir']; ?>
favicon.ico" />
<script language="javascript" type="text/javascript" src="templates/js/openlayer.js"></script>
<script language="javascript" type="text/javascript" src="templates/js/jquery.js"></script>
<title>��װ�� - ��ʿPHP�˲�ϵͳ(www.74cms.com)</title>
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
    <td valign="top">
	 <form action="index.php?act=4" method="post">
	<table width="98%" border="0" align="center" cellpadding="6" cellspacing="0">
      <tr>
        <td bgcolor="#F7FBFC" style=" font-size:13px; padding-left:15px;  "> <strong>���ݿ�����</strong> </td>
      </tr>
      <tr>
        <td style="line-height:200%;"><table width="100%" border="0" cellspacing="3" cellpadding="0">
          <tr>
            <td width="17%" align="right">���ݿ�������</td>
            <td width="83%"><input name="dbhost" type="text" id="dbhost" value="localhost"  class="step_text" /></td>
          </tr>
          <tr>
            <td align="right">���ݿ��û�����</td>
            <td><input name="dbuser" type="text" id="dbuser"  class="step_text" /></td>
          </tr>
          <tr>
            <td align="right">���ݿ����룺</td>
            <td><input name="dbpass" type="text" id="dbpass"  class="step_text" /></td>
          </tr>
		            <tr>
            <td align="right">���ݿ����ƣ�</td>
            <td><input name="dbname" type="text" id="dbname"  class="step_text" /></td>
          </tr>
          <tr>
            <td align="right">���ݱ�ǰ׺��</td>
            <td><input name="pre" type="text" id="pre" value="qs_"  class="step_text" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
      <table width="98%" border="0" align="center" cellpadding="6" cellspacing="0">
        <tr>
          <td bgcolor="#F7FBFC" style=" font-size:13px; padding-left:15px;  "><strong>����Ա�˺�</strong> </td>
        </tr>
        <tr>
          <td style="line-height:200%;"><table width="100%" border="0" cellspacing="3" cellpadding="0">
            <tr>
              <td width="17%" align="right">����Ա������</td>
              <td width="83%"><input name="admin_name" type="text" id="admin_name"  class="step_text" /></td>
            </tr>
            <tr>
              <td align="right">��¼���룺</td>
              <td><input name="admin_pwd" type="password" id="admin_pwd"  class="step_text" /></td>
            </tr>
            <tr>
              <td align="right">����ȷ�ϣ�</td>
              <td><input name="admin_pwd1" type="password" id="admin_pwd1"  class="step_text" /></td>
            </tr>
            <tr>
              <td align="right">�������䣺</td>
              <td><input name="admin_email" type="text" id="admin_email"  class="step_text" /></td>
            </tr>

          </table></td>
        </tr>
        <tr>
          <td height="55" align="center"  >
	<input name="" type="button"  class="step_submit" onclick="window.location.href='index.php?act=2';" value="��һ��" />
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <input type="submit" name="" value="��һ��"  class="step_submit"  onclick="openLayer('op1','tis');"/>
		 
		  </td>
        </tr>
      </table>
	   </form></td>
  </tr>
</table>
  <!--�����������-->
<span id="op1"></span>
<div id="tis" style="display: none" >
<div style="width:350px; height:40px; font-size:12px; background-color: #FFFFFF; text-align:center; padding:20px; color:#003399">
<img src="templates/images/loading.gif" /><br /><br />
���ڰ�װ���벻Ҫ�رմ��ڡ�
</div>
</div>
<!--����������ݽ���-->
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("foot.htm", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
</body>
</html>