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

    /*
     * 建立一个php程序到mysql数据库的连接
     */
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

    /*
     * 到数据库的连接只需要建立一个就可以，
     * 所以每一次需要操作数据库的时候返回一个已经生成的连接
     * 或者在没有连接的时候新建一个
     */

    public static function getInstance(){
        if(WXdb::$WXdb == null){
            $instance = new WXdb();
            return $instance;
        }
        else
            return $WXdb::$WXdb;
    }

    /*
     * 传入一个数据库的查询语句，返回查询的所有数据结果
     */
    public function fetchAll($sql){
        $args = func_get_args();
        array_shift($args);
        $statement = $this->connection->prepare($sql);
        $statement->execute($args);
         return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    /*
     * 实现非返回结果时，确认操作是否成功
     */
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
