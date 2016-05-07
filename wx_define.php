<?php
define("MEMCACHE_HOST","LOCALHOST");
define("MEMCACHE_PORT",11211);

function memcache_init(){
    $mem = new Memcache();
    $mem->connect(MEMCACHE_HOST,MEMCACHE_PORT);
    return $mem;
}
?>
