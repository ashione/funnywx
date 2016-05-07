<?php
include_once 'wx_table_base.php';
include_once 'wx_db.php';

class user extends WXtableBase{
    public $id;
    public $wx_id;
    public $name;
    public $phonenumber;
    public $email;
    public $department;
    public $gender=0;

    public function __construct(){
    }

    public function __construct1($wx_id_,$name_,$phonenumber_,$email_,$department_,$gender_=0){
        $this->wx_id = $wx_id_;
        $this->name = $name_;
        $this->phonenumber = $phonenumber_;
        $this->email = $email_;
        $this->department = $department_;
        $this->gender = $gender_;

    }
    public function getVarsNameList(){
        return get_object_vars($this);
    }

    public function setModel($model){
        foreach($this->getVarsNameList() as $key => $value){
            $this->$key = $model->$key;
        }
    }

    public static function getNewModel($model){
        $newModel = new user();
        foreach($newModel->getVarsNameList() as $key => $value){
            $newModel->$key = $model->$key;
        }
        return $newModel;
    }

    public static function fetchAll(){
        $db = WXdb::getInstance();

        $rows = $db->fetchAll("SELECT * FROM ". get_called_class());
        //foreach($rows as $key => $value){
        //    printf($key);
        //    print_r($value);
        //    printf($value->id);
        //}
        return  array_map(get_called_class()."::getNewModel",$rows);
    }
    public static function findByKey($key,$value){
        $db = WXdb::getInstance();
        $query = sprintf('SELECT * FROM %s WHERE %s="%s"',
            mysql_real_escape_string(get_called_class()),
            $key,$value);
        $rows = $db->fetchAll($query);
        return  array_map(get_called_class()."::getNewModel",$rows);

    }
    public static function addModel($userIns){
        $db = WXdb::getInstance();
        $query = sprintf('INSERT INTO %s(wx_id,name,phonenumber,email,department,gender)  VALUES("%s","%s","%s","%s","%s",%d)',
            mysql_real_escape_string(get_called_class()),
            $userIns->wx_id,
            $userIns->name,
            $userIns->phonenumber,
            $userIns->email,
            $userIns->department,
            $userIns->gender);

        //$row = $db->fetchAll("SELECT * FROM ? ORDER BY RAND() LIMIT 1",get_called_class());
        //printf($query.' query\n');
        return $db->excute($query);
        //echo gettype($row);
        #return  array_map("joke::getNewModel",$row);
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
