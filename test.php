<?php
include_once './models/joke.php';
include_once './wx_define.php';
include_once './models/user.php';
include_once './models/attendance.php';
include_once './wx_distance.php';

$model = joke::getRandomJoke();
//$models = joke::fetchAll();
#print_r(get_object_vars($model[0]));
#print_r($model[0]);
#printf(md5($model[0]->content));

//$mc = memcache_init();
//$mc->set('testkey2','testvalue2');
//echo $mc->get('testkey2');
//$mc->flush();
//echo $mc->get('testkey2');
#$keyword='左凌轩 123888 skyzlx@gmail.com 技术部 0';
#list($name, $phonenumber,$email, $department, $gender) = explode(' ', $keyword);
#$gender = intval($gender);
$fromUsername = "om7dmw_lS-9kw6Pp8vI9Trcl4SpY";
#$userItem = new user($fromUsername,$name,$phonenumber,$email,$department,$gender);
#print_r(get_object_vars($userItem));
//printf(user::addModel($userItem));
//function memcache_init(){
//    $mem = new Memcache();
//    $mem->connect("localhost",11211);
//    return $mem;
//}

//$model = user::findByKey('name',"左凌轩");
//#print_r($model);
//printf($model[0]->toString());
#printf(distance(31.289680,121.215553));
#printf(date('Y-m-d',time()));
printf(now_time()."\n");

$attendanceItem = new attendance($fromUsername,now_time(),'0.0','0.0');

printf($attendanceItem->toString());
if(attendance::addModel($attendanceItem)){
    echo "oK\n" ;
}
else{
     echo "failed\n";
}
$items = attendance::findCurrentMonthRecordsById($fromUsername);
print_r($items);
foreach($items as $item){
     printf($item->getAttendanceTime()."\n");
}
?>
