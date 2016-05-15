<?php
define('INCLUDE_CHECK',1);
require "functions.php";
require "connect.php";
session_start();

if(ini_get('magic_quotes_gpc'))
$_POST['inputField']=stripslashes($_POST['inputField']);



$_POST['inputField'] = mysql_real_escape_string(strip_tags($_POST['inputField']),$link);

if(mb_strlen($_POST['inputField']) < 1 || mb_strlen($_POST['inputField'])>500)
die("0");

$qid = mysql_query("SELECT * FROM user WHERE wx_id='".$_SESSION['user']['openid']."'");
$idrow =  mysql_fetch_assoc($qid);

mysql_query("INSERT INTO reportTimeline SET msg='".$_POST['inputField']."',dt=NOW(),userid=".$idrow['id'].",title='".$_POST['inputTitle']."'");

if(mysql_affected_rows($link)!=1)
die("0");

echo formatTweet($_POST['inputField'],time(),$_POST['inputTitle'],$idrow['name']);

?>
