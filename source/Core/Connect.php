<?php


namespace Source\Core;
use PDO;
use PDOException;

class Connect
{
    private const  HOST = "localhost";
    private const USER = CONF_DB_USER;
    private const DBNAME = CONF_DB_NAME;
    private const PASSWD = "";
    private const OPTIONS = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE
    ];

    private static $instance;

    /**
     * @return mixed
     */
    public static function getInstance() :?PDO
    {
        if(empty(self::$instance)){
            try{
                self::$instance = new PDO("mysql:host=".self::HOST.";dbname=".self::DBNAME,self::USER,self::PASSWD,self::OPTIONS);
            }catch (PDOException $exception){
                redirect("/ops/problemas");
            }

        }
        return self::$instance;

    }



    final public function __construct()
    {
    }
    final public function __clone()
    {

    }

}