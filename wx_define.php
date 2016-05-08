<?php
define("MEMCACHE_HOST","LOCALHOST");
define("MEMCACHE_PORT",11211);

function memcache_init(){
    $mem = new Memcache();
    $mem->connect(MEMCACHE_HOST,MEMCACHE_PORT);
    return $mem;
}
function now_time(){
    date_default_timezone_set("PRC");
    return date('Y-m-d',time());
}
?>
