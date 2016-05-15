<?php

abstract class WXtableBase{
    abstract public function getVarsNameList();
    abstract public function setModel($model);
    abstract static public function getNewModel($model);
    abstract static public function fetchAll();
    abstract static public function addModel($model);
}


?>
