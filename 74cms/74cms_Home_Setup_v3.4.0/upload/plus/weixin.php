<?php

		define('IN_QISHI', true);
		require_once(dirname(__FILE__).'/../include/plus.common.inc.php');
		define("TOKEN", $_CFG['weixin_appsecret']);

		require_once(QISHI_ROOT_PATH.'include/mysql.class.php');
class wechatCallbackapiTest extends mysql
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature())
		{
        	exit($echoStr);
        }
    }
    public function responseMsg()
    {
		if(!$this->checkSignature())
		{
        	exit();
        }
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr))
		{
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
				$keyword = iconv("utf-8","gb2312",$keyword);
                $time = time();
				$event = trim($postObj->Event);
				if ($event === "subscribe")
				{
					$word= "回复j返回紧急招聘，回复n返回最新招聘！您可以尝试输入职位名称如“会计”，系统将会返回您要找的信息，我们努力打造最人性化的服务平台，谢谢关注。";
					$text="<xml>
					<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
					<FromUserName><![CDATA[".$toUsername."]]></FromUserName>
					<CreateTime>".$time."</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[".$word."]]></Content>
					</xml> ";
					exit($text);				
				}	 
               	if (!empty($keyword))
				{
				
					if($_CFG['sina_apiopen']=='0')
					{
							$word="网站微信接口已经关闭";
							$text="<xml>
							<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
							<FromUserName><![CDATA[".$toUsername."]]></FromUserName>
							<CreateTime>".$time."</CreateTime>
							<MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[".$word."]]></Content>
							</xml> ";
							exit($text);
					}
				
										$limit=" LIMIT 6";
										$orderbysql=" ORDER BY refreshtime DESC";
										if($keyword=="n")
										{
											$jobstable=table('jobs_search_rtime');			 
										}
										else if($keyword=="j")
										{
											$jobstable=table('jobs_search_rtime');
											$wheresql=" where `emergency`=1 ";	
										}
										else
										{
										$jobstable=table('jobs_search_key');
										$wheresql.=" where likekey LIKE '%{$keyword}%' ";
										}
										$word='';
										$list = $id = array();
										$idresult = $this->query("SELECT id FROM {$jobstable} ".$wheresql.$orderbysql.$limit);
										while($row = $this->fetch_array($idresult))
										{
										$id[]=$row['id'];
										}
										if (!empty($id))
										{
										$wheresql=" WHERE id IN (".implode(',',$id).") ";
										$result = $this->query("SELECT * FROM ".table('jobs').$wheresql.$orderbysql);	
											while($row = $this->fetch_array($result))
											{
											//$row['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$row['id']));
											$row['addtime']=date("Y-m-d",$row['addtime']);
											$row['deadline']=date("Y-m-d",$row['deadline']);
											$row['refreshtime']=date("Y-m-d",$row['refreshtime']);
											$word.="{$row['companyname']}\n招聘职位：{$row['jobs_name']}\n薪金待遇：{$row['wage_cn']}\n招聘人数：{$row['amount']}\n发布日期：{$row['addtime']}\n截止日期：{$row['deadline']} \n--------------------------\n";
											}
										}
										if(empty($word))
										{
											$word="没有找到包含关键字 {$keyword} 的信息，试试其他关键字";
											$text="<xml>
											<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
											<FromUserName><![CDATA[".$toUsername."]]></FromUserName>
											<CreateTime>".$time."</CreateTime>
											<MsgType><![CDATA[text]]></MsgType>
											<Content><![CDATA[".$word."]]></Content>
											</xml> ";
											exit($text);
										}
										else
										{
												$word=rtrim($word,'/\n');
												$word=rtrim($word,'-');
												$text="<xml>
												<ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
												<FromUserName><![CDATA[".$toUsername."]]></FromUserName>
												<CreateTime>".$time."</CreateTime>
												<MsgType><![CDATA[text]]></MsgType>
												<Content><![CDATA[".$word."]]></Content>
												</xml> ";
												exit($text);
										}	 
				}
				else 
				{
				exit("");
				}
    	}
	}	
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );		
		if($tmpStr == $signature )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
//
	$wechatObj = new wechatCallbackapiTest($dbhost,$dbuser,$dbpass,$dbname);
		if(isset($_REQUEST['echostr']))
					 $wechatObj->valid();
		elseif(isset($_REQUEST['signature']))
		{			  
			$wechatObj->responseMsg();
		}
?>