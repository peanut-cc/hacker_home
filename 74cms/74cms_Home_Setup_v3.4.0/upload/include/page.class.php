<?php
 /*
 * 74cms ��ҳ
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
class page{
 var $page_name="page";
 var $next_page='��һҳ';
 var $pre_page='��һҳ';
 var $first_page='��ҳ';
 var $last_page='βҳ';
 var $pre_bar='<<';
 var $next_bar='>>';
 var $format_left='';
 var $format_right='';
 var $pagebarnum=12;
 var $totalpage=0;
 var $nowindex=1;
 var $url="";
 var $offset=0;
 
 function page($array)
 {
  if(is_array($array))
  {
     if(!array_key_exists('total',$array))$this->error(__FUNCTION__,'need a param of total');
     $total=intval($array['total']);
     $perpage=(array_key_exists('perpage',$array))?intval($array['perpage']):10;
     $nowindex=(array_key_exists('nowindex',$array))?intval($array['nowindex']):'';
     $url=(array_key_exists('url',$array))?$array['url']:'';
     $alias = (array_key_exists('alias', $array)) ? $array['alias'] : '';
     $getarray = (array_key_exists('getarray', $array)) ? $array['getarray'] : '';
  }
  else
  	{
     $total=$array;
     $perpage=10;
     $nowindex='1';
     $url='';
     $alias = '';
     $getarray = '';

  }

  if((!is_int($total))||($total<0))$this->error(__FUNCTION__,$total.' is not a positive integer!');

  if((!is_int($perpage))||($perpage<=0))$this->error(__FUNCTION__,$perpage.' is not a positive integer!');

  if(!empty($array['page_name']))$this->set('page_name',$array['page_name']);

  $this->_set_nowindex($nowindex);

  $this->_set_url($url);

  $this->totalpage=ceil($total/$perpage);

  $this->offset=($this->nowindex-1)*$perpage;
  
  $this->alias = $alias;
  
  $this->getarray = $getarray;
 }

 function set($var,$value)
 {

  if(in_array($var,get_object_vars($this)))

     $this->$var=$value;

  else {

   $this->error(__FUNCTION__,$var." does not belong to PB_Page!");

  }

 }

 function next_page($style=''){
 	if($this->nowindex<$this->totalpage){
		return $this->_get_link($this->_get_url($this->nowindex+1),$this->next_page,$style);
	}
	return '<a class="'.$style.'">'.$this->next_page.'</a>';
 }

 function pre_page($style=''){
 	if($this->nowindex>1){
   	return $this->_get_link($this->_get_url($this->nowindex-1),$this->pre_page,$style);
  }
  return '<a class="'.$style.'">'.$this->pre_page.'</a>';
 }

 function first_page($style=''){
 	if($this->nowindex==1){
    	return '<a class="'.$style.'">'.$this->first_page.'</a>';
 	}
  return $this->_get_link($this->_get_url(1),$this->first_page,$style);
 }

 function last_page($style=''){
 	if($this->nowindex==$this->totalpage||$this->totalpage==0){

      return '<a class="'.$style.'">'.$this->last_page.'</a>';

  }

  return $this->_get_link($this->_get_url($this->totalpage),$this->last_page,$style);

 }


 function nowbar($style='',$nowindex_style='')

 {

  $plus=ceil($this->pagebarnum/2);

  if($this->pagebarnum-$plus+$this->nowindex>$this->totalpage)$plus=($this->pagebarnum-$this->totalpage+$this->nowindex);

  $begin=$this->nowindex-$plus+1;

  $begin=($begin>=1)?$begin:1;

  $return='';

  for($i=$begin;$i<$begin+$this->pagebarnum;$i++)

  {

   if($i<=$this->totalpage){

    if($i!=$this->nowindex)

        $return.=$this->_get_text($this->_get_link($this->_get_url($i),$i,$style));

    else

        $return.=$this->_get_text('<a class="'.$nowindex_style.'">'.$i.'</a>');

   }else{

    break;

   }

   $return.="\n";

  }

  unset($begin);

  return $return;

 }

 /**

  * ��ȡ��ʾ��ת��ť�Ĵ���

  *

  * @return string

  */

 function select()

 {

   $return='<select name="PB_Page_Select">';

  for($i=1;$i<=$this->totalpage;$i++)

  {

   if($i==$this->nowindex){

    $return.='<option value="'.$i.'" selected>'.$i.'</option>';

   }else{

    $return.='<option value="'.$i.'">'.$i.'</option>';

   }

  }

  unset($i);


  $return.='</select>';

  return $return;

 }



 /**

  * ��ȡmysql �����limit��Ҫ��ֵ

  *

  * @return string

  */

 function offset()

 {

  return $this->offset;

 }



 /**

  * ���Ʒ�ҳ��ʾ��������������Ӧ�ķ��

  *

  * @param int $mode

  * @return string

  */

 function show($mode=1)

 {

  switch ($mode)

  {

   case '1':

    $this->next_page='��һҳ';

    $this->pre_page='��һҳ';

    return $this->pre_page().$this->nowbar().$this->next_page().'��'.$this->select().'ҳ';

    break;

   case '2':

    $this->next_page='��һҳ';

    $this->pre_page='��һҳ';

    $this->first_page='��ҳ';

    $this->last_page='βҳ';

    return $this->first_page().$this->pre_page().'[��'.$this->nowindex.'ҳ]'.$this->next_page().$this->last_page().'��'.$this->select().'ҳ';

    break;

   case '3':

    $this->next_page='��һҳ';

    $this->pre_page='��һҳ';

    $this->first_page='��ҳ';

    $this->last_page='βҳ';

    return $this->first_page()."".$this->pre_page()."".$this->nowbar("","select")."".$this->next_page()."".$this->last_page()."<a>".$this->nowindex."/".$this->totalpage."ҳ</a><div class=\"clear\"></div>";

    break;

   case '4':

    $this->next_page='��һҳ';

    $this->pre_page='<';

    return "<span>".$this->nowindex."/".$this->totalpage."ҳ</span>".$this->pre_page().$this->next_page()."<div class=\"clear\"></div>";

    break;

   case '5':

    return $this->pre_bar().$this->pre_page().$this->nowbar().$this->next_page().$this->next_bar();

    break;

  }



 }

 function _set_url($url="")

 {

  if(!empty($url)){

   $this->url=$url.((stristr($url,'?'))?'&':'?').$this->page_name."=";

  }else{

   if(empty($_SERVER['QUERY_STRING'])){

    $this->url=$this->request_url()."?".$this->page_name."=";

   }else{

    if(stristr($_SERVER['QUERY_STRING'],$this->page_name.'=')){

     $this->url=str_replace($this->page_name.'='.$this->nowindex,'',$this->request_url());

     $last=$this->url[strlen($this->url)-1];

     if($last=='?'||$last=='&'){

         $this->url.=$this->page_name."=";

     }else{

         $this->url.='&'.$this->page_name."=";

     }

    }else{

     $this->url=$this->request_url().'&'.$this->page_name.'=';

    }

   }

  }

 }
function _set_nowindex($nowindex)
{
	if(empty($nowindex))
	{
		   if(isset($_GET[$this->page_name]))
		   {
			$this->nowindex=intval($_GET[$this->page_name]);
		   }
	}
	else
	{
	   $this->nowindex=intval($nowindex);
	}
	$this->nowindex=$this->nowindex===0?1:$this->nowindex;
}

 function _get_url($pageno=1)
 {
 	if ($this->alias && $this->getarray)
	{
	$get=$this->getarray;
	$get['page']=$pageno;
	if ($get['key']) $get['key']=rawurlencode($get['key']);
	return url_rewrite($this->alias,$get);
	}
	else
	{
	return $this->url.$pageno;
	}
 }

 function _get_text($str)
 {
  return $this->format_left.$str.$this->format_right;
 }

 function _get_link($url,$text,$style='')
 {
  $style=(empty($style))?'':'class="'.$style.'"';
  return '<a '.$style.' href="'.$url.'">'.$text.'</a>';
 }
 function error($function,$errormsg)
 {
     die('Error in file <b>'.__FILE__.'</b> ,Function <b>'.$function.'()</b> :'.$errormsg);
 }
 function request_url()
 {     
  	if (isset($_SERVER['REQUEST_URI']))     
    {        
   	 $url = $_SERVER['REQUEST_URI'];    
    }
	else
	{    
		  if (isset($_SERVER['argv']))        
			{           
			$url = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];      
			}         
		  else        
			{          
			$url = $_SERVER['PHP_SELF'] .'?'.$_SERVER['QUERY_STRING'];
			}  
    }    
    return $url; 
}
}
?>