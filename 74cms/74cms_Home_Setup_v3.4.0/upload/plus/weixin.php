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
					$word= "�ظ�j���ؽ�����Ƹ���ظ�n����������Ƹ�������Գ�������ְλ�����硰��ơ���ϵͳ���᷵����Ҫ�ҵ���Ϣ������Ŭ�����������Ի��ķ���ƽ̨��лл��ע��";
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
							$word="��վ΢�Žӿ��Ѿ��ر�";
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
											$word.="{$row['companyname']}\n��Ƹְλ��{$row['jobs_name']}\nн�������{$row['wage_cn']}\n��Ƹ������{$row['amount']}\n�������ڣ�{$row['addtime']}\n��ֹ���ڣ�{$row['deadline']} \n--------------------------\n";
											}
										}
										if(empty($word))
										{
											$word="û���ҵ������ؼ��� {$keyword} ����Ϣ�����������ؼ���";
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