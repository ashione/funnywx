<?php

if(!defined('INCLUDE_CHECK')) die('You are not allowed to execute this file directly');
session_start();
function relativeTime($dt,$precision=2)
{
	$times=array(	365*24*60*60	=> "year",
					30*24*60*60		=> "month",
					7*24*60*60		=> "week",
					24*60*60		=> "day",
					60*60			=> "hour",
					60				=> "minute",
					1				=> "second");

	$passed=time()-$dt;

	if($passed<5)
	{
		$output='少于5秒前';
	}
	else
	{
		$output=array();
		$exit=0;

		foreach($times as $period=>$name)
		{
			if($exit>=$precision || ($exit>0 && $period<60)) break;

			$result = floor($passed/$period);
			if($result>0)
			{
				$output[]=$result.' '.$name.($result==1?'':'s');
				$passed-=$result*$period;
				$exit++;
			}
			else if($exit>0) $exit++;
		}

		$output=implode(' and ',$output).' ago';
	}

	return $output;
}

function formatTweet($tweet,$dt,$title)
{
	if(is_string($dt)) $dt=strtotime($dt);

	$tweet=htmlspecialchars(stripslashes($tweet));

	return'
	<li>
	<a href="#"><img class="avatar" src="'.$_SESSION['user']['headimgurl'].'" width="48" height="48" alt="avatar" /></a>
	<div class="tweetTxt">
	<strong><a href="#">'.$title.'</a></strong> '. preg_replace('/((?:http|https|ftp):\/\/(?:[A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?[^\s\"\']+)/i','<a href="$1" rel="nofollow" target="blank">$1</a>',$tweet).'
	<div class="date">'.date('Y-m-d h:i:s',$dt).'</div>
	</div>
	<div class="clear"></div>
	</li>';

}

?>
