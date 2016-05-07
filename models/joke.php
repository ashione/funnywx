<?php
include_once 'wx_table_base.php';
include_once 'wx_db.php';

class joke extends WXtableBase{
    public $id=0;
    public $content;
    public $dislike=0;
    public $like=0;
    public $md5;

    public function getVarsNameList(){
        return get_object_vars($this);
    }

    public function setModel($model){
        foreach($this->getVarsNameList() as $key => $value){
            $this->$key = $model->$key;
        }
    }

    public static function getNewModel($model){
        $newModel = new joke();
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
        return  array_map("joke::getNewModel",$rows);
    }

    public static function getRandomJoke(){
        $db = WXdb::getInstance();
        $query = sprintf("SELECT * FROM %s ORDER BY RAND() LIMIT 1",
                         mysql_real_escape_string(get_called_class()));

        //$row = $db->fetchAll("SELECT * FROM ? ORDER BY RAND() LIMIT 1",get_called_class());
        $row = $db->fetchAll($query);
        //printf($query);
        //echo gettype($row);
        return  array_map("joke::getNewModel",$row);
    }
    public static function addModel($model){

    }

}

//$models = (joke::fetchAll());
//print_r($models);

?>
