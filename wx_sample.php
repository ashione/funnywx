<?php
//define your token
include_once './models/joke.php';
include_once './wx_define.php';
include_once './models/user.php';
include_once 'wx_distance.php';
include_once './models/attendance.php';

define("TOKEN", "6mUonZ");//改成自己的TOKEN
define('APP_ID', 'wx688aaa7ee34cd8df');//改成自己的APPID
define('APP_SECRET', '0197f0241b2e84f74bbc715a47a94b24');//改成自己的APPSECRET

$wechatObj = new wechatCallbackapiTest(APP_ID,APP_SECRET);
#$wechatObj->valid();
$wechatObj->Run();

class wechatCallbackapiTest
{
    private $fromUsername;
    private $toUsername;
    private $times;
    private $keyword;
    private $app_id;
    private $app_secret;


    public function __construct($appid,$appsecret)
    {
        # code...
        $this->app_id = $appid;
        $this->app_secret = $appsecret;
    }
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    /**
     * 运行程序
     * @param string $value [description]
     */
    public function Run()
    {
        $this->responseMsg();
        //$arr[]= "您好，这是自动回复，我现在不在，有事请留言，我会尽快回复你的^_^";
        #echo $this->make_xml("text",$arr);
    }
    public function responseMsg()
    {
        $mc = memcache_init();
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];//返回回复数据
        if (!empty($postStr)){
                //$access_token = $this->get_access_token();//获取access_token
                //$this->createmenu($access_token);//创建菜单
                //$this->getmenu($access_token);//创建菜单
                //$this->delmenu($access_token);//删除菜单
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $this->fromUsername = $postObj->FromUserName;//发送消息方ID
                $this->toUsername = $postObj->ToUserName;//接收消息方ID
                $this->keyword = trim($postObj->Content);//用户发送的消息
                $this->times = time();//发送时间
                $MsgType = $postObj->MsgType;//消息类型
                if($MsgType=='event'){
                    $MsgEvent = $postObj->Event;//获取事件类型
                    if ($MsgEvent=='subscribe') {//订阅事件
                        $arr[] = "你好，我是大杰哥，现在我们是好友咯![愉快][玫瑰]";
                        echo $this->make_xml("text",$arr);
                        exit;
                    }
                    elseif ($MsgEvent=='CLICK') {//点击事件
                        $EventKey = $postObj->EventKey;//菜单的自定义的key值，可以根据此值判断用户点击了什么内容，从而推送不同信息
                        if($EventKey == "V1001_WEATHER")
                        {
                            #$cityname=urlencode("杭州");//将字符串转化成url编码
                            //$url="http://v.juhe.cn/weather/index?format=2&cityname=".$cityname."&key=a692c7c032961b01c10b3d6fb46d1807";
                            if($mc->get($this->fromUsername.'_location',false)){
                                $url="http://v.juhe.cn/weather/geo?format=2".
                                    "&key=a692c7c032961b01c10b3d6fb46d1807&lon=".
                                    $mc->get($this->fromUsername.'_longitude').'&lat='.
                                    $mc->get($this->fromUsername.'_latitude');
                                $str=file_get_contents($url);//把整个文件放到字符串里
                                $de_json = json_decode($str,TRUE);//把json数据转化成数组形式
                                $contentStr="城市：".$de_json['result']['today']['city']."\n日期：".$de_json['result']['today']['date_y'].$de_json['result']['today']['week']."\n当前温度：".$de_json['result']['sk']['temp']."度\n今日风级：". $de_json['result']['sk']['wind_strength'].$de_json['result']['sk']['wind_direction']."\n今日温度：".$de_json['result']['today']['temperature']."\n今日天气：".$de_json['result']['today']['weather']."\n着装建议：".$de_json['result']['today']['dressing_advice']."";

                                $arr[] = $contentStr;
                            }
                            else{

                                $arr[] = "未获得您的地址位置";
                            }
                            goto finish;
                        }
                        elseif($EventKey == "V1001_ATTENDANCE"){
                           $attendanceIns = new attendance($this->fromUsername,
                               now_time(),
                               $mc->get($this->fromUsername.'_latitude','0.0'),
                               $mc->get($this->fromUsername.'_longitude','0.0'));
                           if(attendance::addModel($attendanceIns)){
                               $arr[] = "签到成功(输入ckqd查询本月签到)";
                           }
                           else{
                               $arr[] = "签到失败，或者您今日已经完成签到(输入ckqd查询本月签到)";
                           }
                           goto finish;
                        }
                        echo $this->make_xml("text",$arr);
                        exit;
                    }
                    elseif ($MsgEvent=='LOCATION') {//点击事件
                        #$arr[] = "LOCATION 纬度:".$postObj->Latitude.' 经度:'.$postObj->Longitude."\n距离 : ".distance($postObj->Latitude,$postObj->Longitude);
                        $arr[] = "您距离目的大约(千米):".distance((float)$postObj->Latitude,(float)$postObj->Longitude);
                        $mc->set($this->fromUsername.'_location',true);
                        $mc->set($this->fromUsername.'_latitude',(string)$postObj->Latitude);
                        $mc->set($this->fromUsername.'_longitude',(string)$postObj->Longitude);
                        #echo $this->make_xml("text",$arr);
                        goto finish;
                    }
                }
                elseif($MsgType=='text'){
                    if($mc->get($this->fromUsername,false)){

                        list($name, $phonenumber,$email, $department, $gender) = explode(' ', $this->keyword);
                        $gender = intval($gender);
                        $userItem = new user($this->fromUsername,$name,$phonenumber,$email,$department,$gender);
                        #$arr[] = "显示注册信息:".$userItem->name.' '.$userItem->phonenumber.$userItem->email.$userItem->department.$userItem->gender;
                        #goto finish;
                        if(user::addModel($userItem)){
                            $arr[] = "注册成功";
                        }
                        else{
                            $arr[]= "填写错误，请重新输入bd;\n或您已经注册，不需要重复注册。";
                        }
                        $mc->delete($this->fromUsername);
                        goto finish;
                    }

                    if($mc->get($this->fromUsername.'_cz',false)){
                        $models = user::findByKey('name',$this->keyword);
                        if(count($models))
                            $arr[] = "查找成功:".$models[0]->toString();
                        else
                            $arr[] = "查无此人.";
                        $mc->delete($this->fromUsername.'_cz');
                        goto finish;

                    }
                    if($this->keyword == "bd"){

                        $arr[]= "您启动了绑定程序。\n请按照以下格式输入信息\n姓名 电话 邮箱 部门 性别(如：张三 13888888888 test@test.com 技术部 0)";
                        $mc->set($this->fromUsername,true);
                    }
                    elseif($this->keyword == 'cz'){
                        $arr[]= "您启动了查找程序。(请输入需要查找的名字,如 左凌轩)";
                        $mc->set($this->fromUsername.'_cz',true);
                    }
                    elseif($this->keyword=='ckqd'){
                        $records=attendance::findCurrentMonthRecordsById($this->fromUsername);
                        $resultStr = "本月签到记录:";
                        if(count($records)){
                            foreach($records as $item){
                                $resultStr = $resultStr."\n".$item->getAttendanceTime();
                            }
                        }
                        $arr[] = $resultStr;
                    }
                    else{
                        $model = joke::getRandomJoke();
                        $arr[]= "下面是随机的一句话 : \n" .$model[0]->content;
                        //$arr[] = $model[0]->content;

                    }
                    finish:
                    echo $this->make_xml("text",$arr);
                }

        }else {
            //$model = joke::getRandomJoke();
            //print_r($model);
            //$arr[]= "您刚才说的是:".$this->keyword."\n下面是随机的一句话 : \n" . get_object_vars($model[0]);
            echo "this a file for weixin API!";
            exit;
        }
    }
    /**
     * 获取access_token
     */
    private function get_access_token()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->app_id."&secret=".$this->app_secret;
        $data = json_decode(file_get_contents($url),true);
        if($data['access_token']){
            return $data['access_token'];
        }else{
            return "获取access_token错误";
        }
    }
    /**
     * 创建菜单
     * @param $access_token 已获取的ACCESS_TOKEN
     */
    public function createmenu($access_token)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $arr = array(
            'button' =>array(
                array(
                    'name'=>urlencode("生活查询"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("天气查询"),
                            'type'=>'click',
                            'key'=>'VCX_WEATHER'
                        ),
                        array(
                            'name'=>urlencode("身份证查询"),
                            'type'=>'click',
                            'key'=>'VCX_IDENT'
                        )
                    )
                ),
                array(
                    'name'=>urlencode("轻松娱乐"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("刮刮乐"),
                            'type'=>'click',
                            'key'=>'VCX_GUAHAPPY'
                        ),
                        array(
                            'name'=>urlencode("幸运大转盘"),
                            'type'=>'click',
                            'key'=>'VCX_LUCKPAN'
                        )
                    )
                ),
                array(
                    'name'=>urlencode("我的信息"),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode("关于我"),
                            'type'=>'click',
                            'key'=>'VCX_ABOUTME'
                        ),
                        array(
                            'name'=>urlencode("工作信息"),
                            'type'=>'click',
                            'key'=>'VCX_JOBINFORMATION'
                        )
                    )
                )
            )
        );
        $jsondata = urldecode(json_encode($arr));
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$jsondata);
        curl_exec($ch);
        curl_close($ch);
    }
    /**
     * 查询菜单
     * @param $access_token 已获取的ACCESS_TOKEN
     */

    private function getmenu($access_token)
    {
        # code...
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$access_token;
        $data = file_get_contents($url);
        return $data;
    }
    /**
     * 删除菜单
     * @param $access_token 已获取的ACCESS_TOKEN
     */

    private function delmenu($access_token)
    {
        # code...
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;
        $data = json_decode(file_get_contents($url),true);
        if ($data['errcode']==0) {
            # code...
            return true;
        }else{
            return false;
        }
    }

    /**
     *@param type: text 文本类型, news 图文类型
     *@param value_arr array(内容),array(ID)
     *@param o_arr array(array(标题,介绍,图片,超链接),...小于10条),array(条数,ID)
     */

    private function make_xml($type,$value_arr,$o_arr=array(0)){
        //=================xml header============
        $con="<xml>
                    <ToUserName><![CDATA[{$this->fromUsername}]]></ToUserName>
                    <FromUserName><![CDATA[{$this->toUsername}]]></FromUserName>
                    <CreateTime>{$this->times}</CreateTime>
                    <MsgType><![CDATA[{$type}]]></MsgType>";

          //=================type content============
        switch($type){

            case "text" :
                $con.="<Content><![CDATA[{$value_arr[0]}]]></Content>
                    <FuncFlag>{$o_arr}</FuncFlag>";
            break;

            case "news" :
                $con.="<ArticleCount>{$o_arr[0]}</ArticleCount>
                     <Articles>";
                foreach($value_arr as $id=>$v){
                    if($id>=$o_arr[0]) break; else null; //判断数组数不超过设置数
                    $con.="<item>
                         <Title><![CDATA[{$v[0]}]]></Title>
                         <Description><![CDATA[{$v[1]}]]></Description>
                         <PicUrl><![CDATA[{$v[2]}]]></PicUrl>
                         <Url><![CDATA[{$v[3]}]]></Url>
                         </item>";
                }
                $con.="</Articles>
                     <FuncFlag>{$o_arr[1]}</FuncFlag>";
            break;

        } //end switch

         //=================end return============
        $con.="</xml>";

        return $con;
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

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
?>
