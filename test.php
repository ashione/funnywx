<?php
include_once './models/joke.php';
include_once '../sample/wx_define.php';
include_once './models/user.php';
$model = joke::getRandomJoke();
//$models = joke::fetchAll();
#print_r(get_object_vars($model[0]));
#print_r($model[0]);
#printf(md5($model[0]->content));

#$mc = memcache_init();
#$mc->set('testkey2','testvalue2');
#echo $mc->get('testkey2');
//$keyword='左凌轩 123888 skyzlx@gmail.com 技术部 0';
//list($name, $phonenumber,$email, $department, $gender) = explode(' ', $keyword);
//$gender = intval($gender);
//$fromUsername = "oHQ98wH_QFQrcu7UzP64yrL4Rkv4";
//$userItem = new user($fromUsername,$name,$phonenumber,$email,$department,$gender);
//print_r(get_object_vars($userItem));
//printf(user::addModel($userItem))
//function memcache_init(){
//    $mem = new Memcache();
//    $mem->connect("localhost",11211);
//    return $mem;
//}

$model = user::findByKey('name',"左凌轩");
#print_r($model);
printf($model[0]->toString());
?>
