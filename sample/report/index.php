<!--

Tutoralzine Demo
Original tutorial: Making Our Own Twitter Timeline
Tutorial URL: http://tutorialzine.com/2009/09/making-our-own-twitter-timeline/

You are free to use the following demo code for any purpose you see fit.

-->

<?php

define('INCLUDE_CHECK',1);
require "functions.php";
require "connect.php";
session_start();
#if(!isset($_SESSION['user'])){
/*
 *
 * 获得用户授权信息
 */
    $appid = "wxfbe9dce4e7ae6926";
    $secret = "36225b5d3a10b08c13d986d9fca7da71";
    $code = $_GET["code"];
    $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$get_token_url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $res = curl_exec($ch);
    curl_close($ch);
    $json_obj = json_decode($res,true);

    //根据openid和access_token查询用户信息
    $access_token = $json_obj['access_token'];
    $openid = $json_obj['openid'];
    $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $res = curl_exec($ch);
    curl_close($ch);

    //解析json
    $user_obj = json_decode($res,true);
    $_SESSION['user'] = $user_obj;
#}
print_r($_SESSION['user']);
// remove tweets older than 1 hour to prevent spam
#mysql_query("DELETE FROM reportTimeline WHERE id>1 AND dt<SUBTIME(NOW(),'0 1:0:0')");

//fetch the timeline
#$qid = mysql_query("SELECT * FROM user WHERE wx_id='".$_SESSION['user']['openid']."'");
#$idrow =  mysql_fetch_assoc($qid);
#printf($idrow['id']."\n");
#if(!isset($_SESSION['userid'])){
#    $_SESSION['userid']=$idrow['id'];
#}

#$q = mysql_query("SELECT * FROM reportTimeline WHERE userid=".$idrow['id']." ORDER BY ID DESC");
/*
 *
 * 根据用户授权的信息，从数据库里取出相应的报告
 */
$q = mysql_query("SELECT * FROM reportTimeline,user  where user.id = reportTimeline.userid ORDER BY reportTimeline.id DESC");

$timeline='';
while($row=mysql_fetch_assoc($q))
{
	$timeline.=formatTweet($row['msg'],$row['dt'],$row['title'],$row['name']);
}

// fetch the latest tweet
$lastweet = '';
list($lastTweet) = mysql_fetch_array(mysql_query("SELECT msg FROM reportTimeline  ORDER BY id DESC LIMIT 1"));

if(!$lastTweet) $lastTweet = "You don't have any report yet!";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.7 minimum-scale=1.7, maximum-scale=2.0; charset=utf-8">

<title>工作报告</title>

<link rel="stylesheet" type="text/css" href="demo.css" />
<script src="http://libs.baidu.com/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="script.js"></script>


</head>

<body>

<div id="twitter-container">
<form id="tweetForm" action="submit.php" method="post">

<span class="counter">500</span>
<label for="inputField">报告格式</label>

<input type="text" name="inputTitle" id="inputTitle" tabindex="1" rows="1" cols="10" placeholder="主题"></input>
<textarea name="inputField" id="inputField" tabindex="2" rows="3" cols="20" placeholder="报告正文"></textarea>
<input class="submitButton inact" name="submit" type="submit" value="提交" />

<span class="latest"><strong>最新: </strong><span id="lastTweet"><?=$lastTweet?></span></span>

<div class="clear"></div>

</form>

<h3 class="timeline">历史报告</h3>

<ul class="statuses"><?=$timeline?></ul>


</div>

</body>
</html>
