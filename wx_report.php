<?php
    echo "hellow";
    $appid = "wx0f1a87a12f16eda2";
    $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3a%2f%2f121.42.41.142%2freport%2findex.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
    header("Location:".$url);
?>
