<?php
function tpl_function_qishi_explain_list($params, &$smarty)
{
	global $db,$_CFG;
	$arrset=explode(',',$params['set']);
	foreach($arrset as $str)
	{
	$a=explode(':',$str);
		switch ($a[0])
		{
		case "列表名":
			$aset['listname'] = $a[1];
			break;
		case "显示数目":
			$aset['row'] = $a[1];
			break;
		case "标题长度":
			$aset['titlelen'] = $a[1];
			break;
		case "开始位置":
			$aset['start'] = $a[1];
			break;
		case "填补字符":
			$aset['dot'] = $a[1];
			break;
		case "排序":
			$aset['displayorder'] = $a[1];
			break;
		case "分类ID":
			$aset['type_id'] = $a[1];
			break;
		case "页面":
			$aset['showname'] = $a[1];
			break;
		}
	}
	if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
	$aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
	$aset['row']=isset($aset['row'])?intval($aset['row']):10;
	$aset['start']=isset($aset['start'])?intval($aset['start']):0;
	$aset['titlelen']=isset($aset['titlelen'])?intval($aset['titlelen']):15;
	$aset['dot']=isset($aset['dot'])?$aset['dot']:'';
	$aset['showname']=isset($aset['showname'])?$aset['showname']:'QS_explainshow';
if (isset($aset['displayorder']))
{
	if (strpos($aset['displayorder'],'>'))
	{
	$arr=explode('>',$aset['displayorder']);
	$arr[0]=preg_match('/show_order|id/',$arr[0])?$arr[0]:"";
	$arr[1]=preg_match('/asc|desc/',$arr[1])?$arr[1]:"";
		if ($arr[0] && $arr[1])
		{
		$orderbysql=" ORDER BY ".$arr[0]." ".$arr[1];
		}
	}
}
else
{
	$orderbysql="  ORDER BY show_order desc";
}
	$wheresql=" WHERE is_display=1 ";
	if ($aset['type_id'])$wheresql.=" AND  type_id=".intval($aset['type_id']);
	if ($_CFG['subsite']=="1" && $_CFG['subsite_filter_explain']=="1")
	{
		$wheresql.=" AND (subsite_id=0 OR subsite_id=".intval($_CFG['subsite_id']).") ";
	}
	$limit=" LIMIT ".abs($aset['start']).','.$aset['row'];
	$result = $db->query("SELECT tit_color,tit_b,title,id,addtime,is_url FROM ".table('explain')." ".$wheresql.$orderbysql.$limit);
	$list = array();
	while($row = $db->fetch_array($result))
	{
		$row['title_']=$row['title'];
		$style_color=$row['tit_color']?"color:".$row['tit_color'].";":'';
		$style_font=$row['tit_b']=="1"?"font-weight:bold;":'';
		$row['title']=cut_str($row['title'],$aset['titlelen'],0,$aset['dot']);
		if ($style_color || $style_font)$row['title']="<span style=".$style_color.$style_font.">".$row['title']."</span>";
		if (!empty($row['is_url']) && $row['is_url']!='http://')
		{
		$row['url']= $row['is_url'];
		}
		else
		{
		$row['url'] = url_rewrite($aset['showname'],array('id'=>$row['id']));
		}
		$list[] = $row;
	}
	$smarty->assign($aset['listname'],$list);
}
?>