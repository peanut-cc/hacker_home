<?php
 /*
 * 74cms ��֤��
 * ============================================================================
 * ��Ȩ����: ��ʿ���磬����������Ȩ����
 * ��վ��ַ: http://www.74cms.com��
 * ----------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ���ڲ�������ҵĿ�ĵ�ǰ���¶Գ����������޸ĺ�
 * ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ============================================================================
*/
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
// HTTP/1.1
header('Cache-Control: private, no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0, max-age=0', false);
// HTTP/1.0
header('Pragma: no-cache');
define('QISHI_ROOT_PATH', dirname(dirname(__FILE__)).'/');
ini_set('session.save_handler', 'files');
session_save_path(QISHI_ROOT_PATH.'data/sessions/');
session_start();
error_reporting(E_ERROR);
class imageCaptcha
{
private $height;
private $width;
private $textNum; 
private $textContent;
private $fontColor;
private $randFontColor; 
private $fontSize;
private $bgColor;
private $randBgColor;
private $textLang;
private $noisePoint;
private $noiseLine;
private $distortion;
private $distortionImage;
private $showBorder;
private $image;
private $rootpath;
public function imageCaptcha()
{
	$this->textNum = 4;
	$this->fontSize = 15;
	$this->fontFamily = '';
	$this->textLang = 'en';
	$this->noisePoint = 100;
	$this->noiseLine = 0;
	$this->distortion = false;
	$this->showBorder = false;
	$this->rootpath= str_replace('include/imagecaptcha.php', '', str_replace('\\', '/', __FILE__));
}
public function set_show_mode()
{
	require_once($this->rootpath."data/cache_captcha.php");
	$this->cfg=$data;
	$this->width=$this->cfg['captcha_width'];
	$this->height=$this->cfg['captcha_height'];
	$this->textNum=empty($this->cfg['captcha_textlength'])?mt_rand(3,6):$this->cfg['captcha_textlength'];
	$this->fontColor=$this->cfg['captcha_textcolor']?sscanf($this->cfg['captcha_textcolor'],'#%2x%2x%2x'):'';
	$this->fontSize=$this->cfg['captcha_textfontsize'];
	$this->textLang=$this->cfg['captcha_lang'];
	$this->bgColor=$this->cfg['captcha_bgcolor']?sscanf($this->cfg['captcha_bgcolor'],'#%2x%2x%2x'):'';
	$this->noisePoint=$this->cfg['captcha_noisepoint'];
	$this->noiseLine=$this->cfg['captcha_noiseline'];
	$this->distortion=$this->cfg['captcha_distortion'];
}
public function initImage() //@��ʼ����֤��ͼƬ
{   
	if(empty($this->width))
	{
	$this->width=floor($this->fontSize*1.3)*$this->textNum+20;
	}
	if(empty($this->height))
	{
	$this->height=floor($this->fontSize*2.3);
	}
	$this->image=imagecreatetruecolor($this->width,$this->height);
	if(empty($this->bgColor))
	{
	$this->randBgColor=imagecolorallocate($this->image,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
	}
	else
	{
	$this->randBgColor=imagecolorallocate($this->image,$this->bgColor[0],$this->bgColor[1],$this->bgColor[2]);
	}
	imagefill($this->image,0,0,$this->randBgColor);
}
public function randText($type)//@��������ַ�
{    
	$string='';
	switch($type)
	{
		case 'en':
		$string='ACDEFGHKLMNPQRSTUVWXYabcdehkmnprsuvwxy3469';
		$string=str_shuffle($string);
		$string=substr($string,0,$this->textNum);
		$string=chunk_split($string,1,',');
		$string=rtrim($string,',');
		$string=explode(',',$string);
		break;
		case 'cn':
		$string="��,ȥ,��,Ǯ,��,��,Ϊ,δ,��,��,��,��,��,��,̫,��,��,һ,��,Ԫ,Ҫ,Ҳ,��,��,Ʒ,��,��,˵,��,ʱ,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,д,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,��,æ,ô";
		require_once($this->rootpath."data/config.php");
		if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
		{
		$string=iconv(QISHI_DBCHARSET,"utf-8",$string);
		}
		$string=explode(',',$string);
		shuffle($string);
		$string=array_slice($string,0,$this->textNum);
		break;
	}
	return $string;
}
public function createText()//@������ֵ���֤��
{    
	$text_array=$this->randText($this->textLang);
	$this->textContent=implode('',$text_array);
	if ($this->textLang=="cn" && strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
	{
	$this->textContent=iconv("utf-8",QISHI_DBCHARSET,$this->textContent);
	}
	if(empty($this->fontColor))
	{
	$this->randFontColor=imagecolorallocate($this->image,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
	}
	else
	{
	$this->randFontColor=imagecolorallocate($this->image,$this->fontColor[0],$this->fontColor[1],$this->fontColor[2]);
	}
	$font=$this->getfontFamily();
	if(empty($font)) exit();
	$fontdir =$this->rootpath.'data/font/'.$this->textLang."/";
	for($i=0;$i<$this->textNum;$i++)
	{
		$this->fontFamily=$fontdir.$font[array_rand($font,1)];
		imagettftext($this->image,$this->fontSize,mt_rand(-20,20),$i*$this->fontSize+($this->width/$this->textNum)-floor($this->fontSize/2),floor($this->height/2+$this->fontSize/2),$this->randFontColor,$this->fontFamily,$text_array[$i]);
	}
}
public function createNoisePoint()//@���ɸ��ŵ�
{    
	for($i=0;$i<$this->noisePoint;$i++)
	{
		//$pointColor=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
		imagesetpixel($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),$this->randFontColor);
	}
}
public function getfontFamily()//��ȡ����
{    
	$dir =$this->rootpath.'data/font/'.$this->textLang."/";
		if($handle = @opendir($dir))
		{
			$i = 0;
			while(false !== ($file = @readdir($handle)))
			{
				if(strcasecmp(substr($file,-4),'.ttf')===0)
				{
					$list[]= $file;
					$i++;
				}
			}
		}	
	return 	$list;
}
public function createNoiseLine()//@����������
{    
	for($i=0;$i<$this->noiseLine;$i++)
	{
		//$lineColor=imagecolorallocate($this->image,mt_rand(150,255),mt_rand(150,255),mt_rand(150,255));
		imageline($this->image,0,mt_rand(0,$this->width),$this->width,mt_rand(0,$this->height),$this->randFontColor);
	}
}
public function distortionText()//@Ť������
{    
	$this->distortionImage=imagecreatetruecolor($this->width,$this->height);
	imagefill($this->distortionImage,0,0,$this->randBgColor);
	for($x=0;$x<$this->width;$x++)
	{
		for($y=0;$y<$this->height;$y++)
		{
		$rgbColor=imagecolorat($this->image,$x,$y);
		imagesetpixel($this->distortionImage,(int)($x+sin($y/$this->height*2*M_PI-M_PI*0.5)*3),$y,$rgbColor);
		}
	}
	$this->image=$this->distortionImage;
}
public function createImage()//@������֤��ͼƬ
{    
	header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
	// HTTP/1.1
	header('Cache-Control: private, no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0, max-age=0', false);
	// HTTP/1.0
	header('Pragma: no-cache');
	$this->initImage(); //��������ͼƬ
	$this->createText(); //�����֤���ַ�
	$this->createNoisePoint(); //�������ŵ�
	$this->createNoiseLine(); //����������
	if($this->distortion =="1")//Ť������
	{
	$this->distortionText();
	}
	if($this->showBorder)//��ӱ߿�
	{
	$color = ImageColorAllocate($this->image, $this->showBordercolor[0],$this->showBordercolor[1],$this->showBordercolor[2]);
	imagerectangle($this->image,0,0,$this->width-1,$this->height-1,$color);
	} 
	imagepng($this->image);
	imagedestroy($this->image);
	if($this->distortion !=false)
	{
	imagedestroy($this->distortionImage);
	}
	return $this->textContent;
}
}
?>
<?php
$act = !empty($_POST['act']) ? trim($_POST['act']) : '';
if ($act=="verify")
{
	$c=trim($_POST['postcaptcha']);
	if (preg_match("/^[A-Za-z0-9]*$/",$c))
	{
	$c=strtolower($c);
	}
	else
	{
		$rootpath= str_replace('include/imagecaptcha.php', '', str_replace('\\', '/', __FILE__));
		require_once($rootpath.'data/config.php');
		if (strcasecmp(QISHI_DBCHARSET,"utf8")!=0)
		{
		$c=iconv("utf-8",QISHI_DBCHARSET,$c);
		}
	}
	if (empty($c) || empty($_SESSION['imageCaptcha_content']) || $_SESSION['imageCaptcha_content']<>$c)
	{
	exit("false");
	}
	else
	{
	exit("true");
	}
}
else
{
header("Content-type:image/png"); 
$code_obj = new imageCaptcha(); 
$code_obj->set_show_mode();
$code = $code_obj->createImage();
$_SESSION['imageCaptcha_content'] = strtolower($code);
}
?>