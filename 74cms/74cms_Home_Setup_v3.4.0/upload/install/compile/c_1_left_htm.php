<?php /* V2.10 Template Lite 4 January 2007  (c) 2005-2007 Mark Dickenson. All rights reserved. Released LGPL. 2014-02-12 15:10 中国标准时间 */ ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if ($this->_vars['act'] == "1"): ?>
<tr>
<td height="40" align="center" class="link_1">许可协议</td>
</tr>
<?php elseif ($this->_vars['act'] > 1): ?>
<tr>
<td height="40" align="center" class="link_2">许可协议</td>
</tr>
<?php else: ?>
<tr>
<td height="40" align="center" >许可协议</td>
</tr>
<?php endif; ?>
<!-- -->
<?php if ($this->_vars['act'] == "2"): ?>
<tr>
<td height="40" align="center" class="link_1">环境检测</td>
</tr>
<?php elseif ($this->_vars['act'] > 2): ?>
<tr>
<td height="40" align="center" class="link_2">环境检测</td>
</tr>
<?php else: ?>
<tr>
<td height="40" align="center" >环境检测</td>
</tr>
<?php endif; ?>
<!-- -->
<?php if ($this->_vars['act'] == "3"): ?>
<tr>
<td height="40" align="center" class="link_1">参数配置</td>
</tr>
<?php elseif ($this->_vars['act'] > 3): ?>
<tr>
<td height="40" align="center" class="link_2">参数配置</td>
</tr>
<?php else: ?>
<tr>
<td height="40" align="center" >参数配置</td>
</tr>
<?php endif; ?>
<tr>
<td height="40" align="center">开始安装</td>
</tr>
<!-- -->
<?php if ($this->_vars['act'] == "5"): ?>
<tr>
<td height="40" align="center" class="link_1">安装完成</td>
</tr>
<?php elseif ($this->_vars['act'] > 5): ?>
<tr>
<td height="40" align="center" class="link_2">安装完成</td>
</tr>
<?php else: ?>
<tr>
<td height="40" align="center" >安装完成</td>
</tr>
<?php endif; ?>
</table>