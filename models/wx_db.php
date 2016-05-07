<?php
//Here is a PDO helper class to get you started . . .

define('DB_MAIN', 'localhost|wechat|wechat|wechat');

// Connect to database db1
//$db = new my_db(DB_MAIN);
//$db = WXdb::getInstance();
// Get an array of stdClass's
//$rows = $db->fetchAll('SELECT * FROM joke ');
//print_r($rows);

class WXdb{

    private static $databases;
    private $connection;
    public static $WXdb = null;

    private function __construct($connDetails=DB_MAIN){
        if(WXdb::$WXdb!=null)
            return;
        if(!is_object(self::$databases[$connDetails])){
            list($host, $user, $pass, $dbname) = explode('|', $connDetails);
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
            self::$databases[$connDetails] = new PDO($dsn, $user, $pass);
        }
        $this->connection = self::$databases[$connDetails];
    }
    public static function getInstance(){
        if(WXdb::$WXdb == null){
            $instance = new WXdb();
            return $instance;
        }
        else
            return $WXdb::$WXdb;
    }

    public function fetchAll($sql){
        $args = func_get_args();
        array_shift($args);
        $statement = $this->connection->prepare($sql);
        $statement->execute($args);
         return $statement->fetchAll(PDO::FETCH_OBJ);
    }
    public function excute($sql){
        $args = func_get_args();
        #array_shift($args);
        $statement = $this->connection->prepare($sql);
        try{
            $result = $statement->execute();
        }
        catch(PDOException $e){
            trigger_error('Error occured while trying to insert into the DB:' . $e->getMessage(), E_USER_ERROR);
            $result = false;
        }
        #printf($result.'\n');
        return $result;
        #return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}

?>
