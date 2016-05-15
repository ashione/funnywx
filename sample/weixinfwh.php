<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "6mUonZ");
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