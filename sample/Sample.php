<?php

include_once "WXBizMsgCrypt.php";

// 假设企业号在公众平台上设置的参数如下
$encodingAesKey = "hxpJamsiCdKiENShXjIvgWe11YFRolaJw7PFz6sR9CO";
$token = "6mUonZ";
$corpId = "wx0908a426d03e8bbc";

/*
------------使用示例一：验证回调URL---------------
*企业开启回调模式时，企业号会向验证url发送一个get请求
假设点击验证时，企业收到类似请求：
* GET /cgi-bin/wxpush?msg_signature=5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3&timestamp=1409659589&nonce=263014780&echostr=P9nAzCzyDtyTWESHep1vC5X9xho%2FqYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp%2B4RPcs8TgAE7OaBO%2BFZXvnaqQ%3D%3D
* HTTP/1.1 Host: qy.weixin.qq.com

接收到该请求时，企业应
1.解析出Get请求的参数，包括消息体签名(msg_signature)，时间戳(timestamp)，随机数字串(nonce)以及公众平台推送过来的随机加密字符串(echostr),
这一步注意作URL解码。
2.验证消息体签名的正确性
3. 解密出echostr原文，将原文当作Get请求的response，返回给公众平台
第2，3步可以用公众平台提供的库函数VerifyURL来实现。

*/

// $sVerifyMsgSig = HttpUtils.ParseUrl("msg_signature");
//$sVerifyMsgSig = "5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3";
$sVerifyMsgSig = $_GET["msg_signature"];
// $sVerifyTimeStamp = HttpUtils.ParseUrl("timestamp");
//$sVerifyTimeStamp = "1409659589";
$sVerifyTimeStamp = $_GET["timestamp"];
// $sVerifyNonce = HttpUtils.ParseUrl("nonce");
//$sVerifyNonce = "263014780";
$sVerifyNonce = $_GET["nonce"];
// $sVerifyEchoStr = HttpUtils.ParseUrl("echostr");
//$sVerifyEchoStr = "P9nAzCzyDtyTWESHep1vC5X9xho/qYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp+4RPcs8TgAE7OaBO+FZXvnaqQ==";
$sVerifyEchoStr = $_GET["echostr"];

// 需要返回的明文
$sEchoStr = "";
#$sEchoStr = $_GET["echostr"];

$wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
$errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
if ($errCode == 0) {
	//
	// 验证URL成功，将sEchoStr返回
    // HttpUtils.SetResponce($sEchoStr);
   echo $sEchoStr;
} else {
	print("ERR: " . $errCode . "\n\n");
}

/*
------------使用示例二：对用户回复的消息解密---------------
用户回复消息或者点击事件响应时，企业会收到回调消息，此消息是经过公众平台加密之后的密文以post形式发送给企业，密文格式请参考官方文档
假设企业收到公众平台的回调消息如下：
POST /cgi-bin/wxpush? msg_signature=477715d11cdb4164915debcba66cb864d751f3e6&timestamp=1409659813&nonce=1372623149 HTTP/1.1
Host: qy.weixin.qq.com
Content-Length: 613
<xml>
<ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName><Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt>
<AgentID><![CDATA[218]]></AgentID>
</xml>

企业收到post请求之后应该
1.解析出url上的参数，包括消息体签名(msg_signature)，时间戳(timestamp)以及随机数字串(nonce)
2.验证消息体签名的正确性。
3.将post请求的数据进行xml解析，并将<Encrypt>标签的内容进行解密，解密出来的明文即是用户回复消息的明文，明文格式请参考官方文档
第2，3步可以用公众平台提供的库函数DecryptMsg来实现。
*/

// $sReqMsgSig = HttpUtils.ParseUrl("msg_signature");
$sReqMsgSig = $_GET["msg_signature"];
// $sReqTimeStamp = HttpUtils.ParseUrl("timestamp");
$sReqTimeStamp = $_GET["timestamp"];
// $sReqNonce = HttpUtils.ParseUrl("nonce");
$sReqNonce = $_GET["nonce"];
// post请求的密文数据
// $sReqData = HttpUtils.PostData();
$sReqData = "<xml><ToUserName><![CDATA[%s]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt><AgentID><![CDATA[%s]]></AgentID></xml>";
$sMsg = "";  // 解析之后的明文
$errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
if ($errCode == 0) {
	// 解密成功，sMsg即为xml格式的明文
	// TODO: 对明文的处理
	// For example:
	$xml = new DOMDocument();
	$xml->loadXML($sMsg);
	$content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;
	print("content: " . $content . "\n\n");
	// ...
	// ...
} else {
	print("ERR: " . $errCode . "\n\n");
	//exit(-1);
}

/*
------------使用示例三：企业回复用户消息的加密---------------
企业被动回复用户的消息也需要进行加密，并且拼接成密文格式的xml串。
假设企业需要回复用户的明文如下：
<xml>
<ToUserName><![CDATA[mycreate]]></ToUserName>
<FromUserName><![CDATA[wx5823bf96d3bd56c7]]></FromUserName>
<CreateTime>1348831860</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[this is a test]]></Content>
<MsgId>1234567890123456</MsgId>
<AgentID>128</AgentID>
</xml>

为了将此段明文回复给用户，企业应：
1.自己生成时间时间戳(timestamp),随机数字串(nonce)以便生成消息体签名，也可以直接用从公众平台的post url上解析出的对应值。
2.将明文加密得到密文。
3.用密文，步骤1生成的timestamp,nonce和企业在公众平台设定的token生成消息体签名。
4.将密文，消息体签名，时间戳，随机数字串拼接成xml格式的字符串，发送给企业号。
以上2，3，4步可以用公众平台提供的库函数EncryptMsg来实现。
*/

// 需要发送的明文
$sRespData = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content><MsgId>%s</MsgId><AgentID>%s</AgentID></xml>";
$sEncryptMsg = ""; //xml格式的密文
$errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
if ($errCode == 0) {
	// TODO:
	// 加密成功，企业需要将加密之后的sEncryptMsg返回
	// HttpUtils.SetResponce($sEncryptMsg);  //回复加密之后的密文
} else {
	print("ERR: " . $errCode . "\n\n");
	// exit(-1);
}



$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();
/**
 * PHP100设置微信定义菜单的类，很简单，兴趣的小盆友们继续修改
 * 请帮忙宣保留 PHP100说明即可，谢谢
 * author 张恩民 微信号:haowubai 
 * PHP100 的官方微信 : php100mm 网址 www.php100.com
 * php培训就找 : 创恩IT教育，小班制编程教学，学编程得找个靠谱的！
 * www.chuangen.c.om PHP100中文网旗下培训中心 
 */

$wx = new php100_weixin_button();
$wx->app_menu();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
   <ToUserName><![CDATA[%s]]></ToUserName>
   <FromUserName><![CDATA[%s]]></FromUserName>
   <CreateTime>%s</CreateTime>
   <MsgType><![CDATA[%s]]></MsgType>
   <Event><![CDATA[%s]]></Event>
   <AgentID>4</AgentID>
</xml>";
            
 				
			   $guanzhu = $postObj->Event;   // 关注时候自动推送的欢迎消息
					  if ( $guanzhu =="subscribe")
					     {
					  		$msgType = " <xml>
   <ToUserName><![CDATA[toUser]]></ToUserName>
   <FromUserName><![CDATA[fromUser]]></FromUserName>
   <CreateTime>12345678</CreateTime>
   <MsgType><![CDATA[news]]></MsgType>
   <ArticleCount>2</ArticleCount>
   <Articles>
       <item>
           <Title><![CDATA[通讯录]]></Title> 
           <Description><![CDATA[点击进入]]></Description>
           <PicUrl><![CDATA[http://img3.3lian.com/2006/017/18/051.jpg]]></PicUrl>
           <Url><![CDATA[http://bbs.hh010.com]]></Url>
       </item>
       <item>
           <Title><![CDATA[title]]></Title>
           <Description><![CDATA[description]]></Description>
           <PicUrl><![CDATA[picurl]]></PicUrl>
           <Url><![CDATA[url]]></Url>
       </item>
   </Articles>
</xml>";                        
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType);
                      	    echo $resultStr;                      
                          
						 }	      
				if(!empty( $keyword ))//消息自动回复功能
                {
              		$msgType = "text";
					switch( $keyword )
					 {
					      case"1";
						$contentStr ="  我们是湘潭大学爱好者组织的校园服务平台，简称湘大1314科技，我们渴望计算机爱好者或者计算机特长朋友的加入，编程开发，嵌入式系统，街舞爱好者，电子音乐爱好者。 管理员：yaboomstar 曾在三翼校园，计算机协会都有着相当的贡献。";
						  break;
						 case"2";
						$contentStr ="湘潭电话：15292296692 桂东电话：15173535352 QQ:1991668111 QQ:410074342  www.yaboomstar.com ";
					     break;
						 default;//与case其他之外的消息回复
                	    $contentStr = " 欢迎来到yaboomstar的公众号，和www.yaboomstar.com 网站同时开发定性，预计4月底完成，敬请期待哦。回复：1、公司简介. 回复：2、联系方式。";
                	     
					  }
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}
//自定义菜单

class php100_weixin_button {

	public $appid = "wxbcbbb895d0489663";
	public $appsecret = "11fc826e56230a9ba8825443627a7b48";
	
	//获得凭证接口
	//返回数组，access_token 和  time 有效期 
	public function access_token() {
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";
		$cont = file_get_contents($url);
		return json_decode($cont, 1);
	}

   //创建自定义菜单
	function app_menu() {
        $data = ' {
		     "button":[
		     {	
		         "name":"PHP100",
		         "sub_button":[
		            {
		               "type":"click",
		               "name":"互联网热点",
		               "key":"A1"
		            },
		            {
		               "type":"click",
		               "name":"悬赏与外包",
		               "key":"A2"
		            },
		            {
		               "type":"click",
		               "name":"求职与招聘",
		               "key":"A3"
		            },
		            {
		               "type":"click",
		               "name":"PHP100论坛",
		               "key":"A4"
		            },
		            {
		               "type":"click",
		               "name":"个人中心",
		               "key":"A5"
		            }]
		      },
		      {
		           "name":"编程助手",
		           "sub_button":[
		            {
		               "type":"click",
		               "name":"函数查询",
		               "key":"B2"
		            },
		            {
		               "type":"click",
		               "name":"热门书籍",
		               "key":"B3"
		            },
		            {
		               "type":"click",
		               "name":"教程与讲座",
		               "key":"B4"
		            },
		            {
		               "type":"click",
		               "name":"培训与课程",
		               "key":"B5"
		            }]
		      },
		      {
		           "name":"自娱自乐",
		           "sub_button":[
		            {
		               "type":"click",
		               "name":"发布心情",
		               "key":"C1"
		            },
		            {
		               "type":"click",
		               "name":"开心段子",
		               "key":"C2"
		            },
		            {
		               "type":"click",
		               "name":"免费与优惠",
		               "key":"C3"
		            }]
		       }]
		 	}';

		$access_token = $this -> access_token();
		$ch = curl_init('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . 
		$access_token['access_token']);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
		$data = curl_exec($ch);
		//print_r($data);//创建成功返回：{"errcode":0,"errmsg":"ok"}
	}



}//end class



?>