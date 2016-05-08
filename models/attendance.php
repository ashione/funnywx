<?php
include_once 'wx_table_base.php';
include_once 'wx_db.php';

class attendance extends WXtableBase{
    private $wx_id;
    private $attendance_time;
    private $attendance_latitude;
    private $attendance_longitude;

    public function __construct(){
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
           call_user_func_array(array($this,$f),$a);
        }
    }
    public function __construct4($wx_id_,$attendance_time_,$attendance_latitude,$attendance_longitude){
        $this->wx_id=$wx_id_;
        $this->attendance_time = $attendance_time_;
        $this->attendance_latitude = $attendance_latitude;
        $this->attendance_longitude = $attendance_longitude;
    }
    public function getVarsNameList(){
        return get_object_vars($this);
    }
    public function setModel($model){
        foreach($this->getVarsNameList() as $key => $value){
            $this->$key = $model->$key;
        }
    }

    public static function fetchAll(){
        $db = WXdb::getInstance();

        $rows = $db->fetchAll("SELECT * FROM ". get_called_class());
        return  array_map(get_called_class()."::getNewModel",$rows);
    }

    public static function findCurrentMonthRecordsById($id){
        $db = WXdb::getInstance();
        $query = sprintf('SELECT * FROM %s WHERE
            YEAR(NOW())=YEAR(attendance_time) AND
            MONTH(NOW())=MONTH(attendance_time) AND wx_id="%s"',
            mysql_real_escape_string(get_called_class()),
            $id);
        printf($query."\n");
        $rows = $db->fetchAll($query);

        return  array_map(get_called_class()."::getNewModel",$rows);
    }
    public static function addModel($attendanceIns){
        $db = WXdb::getInstance();
        $query = sprintf('INSERT INTO %s(wx_id,attendance_time,
            attendance_latitude,
            attendance_longitude)  VALUES("%s","%s","%s","%s")',
            mysql_real_escape_string(get_called_class()),
            $attendanceIns->wx_id,
            $attendanceIns->attendance_time,
            $attendanceIns->attendance_latitude,
            $attendanceIns->attendance_longitude);
        //printf($query."\n");
        return $db->excute($query);
    }
    public function getAttendanceTime(){
        return $this->attendance_time;
    }

    public static function getNewModel($model){
        $instance = new attendance();
        foreach($instance->getVarsNameList() as $key => $value){
            $instance->$key = $model->$key;
        }
        return $instance;
    }

    public function toString(){
        $str = "";
        foreach($this->getVarsNameList() as $key => $value){
            $str=$str.' '.$value;
        }
        return $str;

    }

}
?>
