<?php
    #echo "hellow";
    $appid = "wxfbe9dce4e7ae6926";
    $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3a%2f%2f121.42.41.142%2freport%2findex.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
    header("Location:".$url);
?>
