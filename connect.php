<?php
/**
  * wechat php test
  */

//define your token
include_once("wx_define.php");
define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
//$wechatObj->responseMsg();注意：这里是因为接入成功了，所以可以注释掉上段代码的接入函数，直接执行操作的函数。这里可以做一个优化，见下段代码

if(!isset($_GET['echostr'])){
$wechatObj->responseMsg();
}else{
$wechatObj->valid();
}//优化说明，获取到echostr证明要接入，没有获取到就执行操作的函数；正常情况下要先接入，接入后要注释掉valid,开启responseMsg，再重新上传代码，优化后只要上传一次就好

class wechatCallbackapiTest//这是一个类，一直到最后的大括号
{
	public function valid()//这是一个接入的函数
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()//这是接入后进行操作的函数，包括自动回复
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                //libxml_disable_entity_loader(true);    不注释掉就不执行？？
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
			    $type = trim($postObj->Event);
			    $EventKey = $postObj->EventKey;
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

			if($type == "subscribe")
			{
				$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<ArticleCount>1</ArticleCount>
						<Articles>
						<item>
						<Title><![CDATA[**企业微信OA平台，敏捷办公新时代]]></Title>
						<Description><![CDATA[欢迎进入**企业官方网站]]></Description>
						<PicUrl><![CDATA[http://file.cbda.cn/uploadfile/2015/0814/20150814142422466.jpg]]></PicUrl>
						<Url><![CDATA[http://weixin.qq.com]]></Url>
						</item>
						</Articles>
						</xml>";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
				echo $resultStr;
			}

			if($type == "CLICK" and $EventKey == "V1001_WEATHER")
			{
				$msgType = "text";
				{
					$cityname=urlencode("杭州");//将字符串转化成url编码
					$url="http://v.juhe.cn/weather/index?format=2&cityname=".$cityname."&key=a692c7c032961b01c10b3d6fb46d1807";
					$str=file_get_contents($url);//把整个文件放到字符串里
					$de_json = json_decode($str,TRUE);//把json数据转化成数组形式
					$contentStr="城市：".$de_json['result']['today']['city']."\n日期：".$de_json['result']['today']['date_y'].$de_json['result']['today']['week']."\n当前温度：".$de_json['result']['sk']['temp']."度\n今日风级：". $de_json['result']['sk']['wind_strength'].$de_json['result']['sk']['wind_direction']."\n今日温度：".$de_json['result']['today']['temperature']."\n今日天气：".$de_json['result']['today']['weather']."\n着装建议：".$de_json['result']['today']['dressing_advice']."";

				}
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}



			if(!empty( $keyword ))
			{
				$msgType = "text";
				$contentStr = "欢迎关注**企业微信OA平台，祝您工作愉快！";
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

	private function checkSignature()  //验证token的函数，知道干什么就好了，不需要一个个理解，诶最好还是去理解一下/(ㄒoㄒ)/~~
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
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

?>
