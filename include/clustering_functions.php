<?php
/**
 * Created by PhpStorm.
 * User: mohammad.haider
 * Date: 007 11/7/2016
 * Time: 9:22 PM
 */
class clustering_functions {
    private $db;
    public $con;
    
    function __construct()
    {
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->con = $this->db->connect();
        mysqli_set_charset($this->con, 'utf8');
        $sql = "SET @@session.time_zone = '+03:00';";
        mysqli_query($this->con, $sql);
        $sql = "DROP TABLE IF EXISTS `geoClustered`;
                CREATE TABLE  `geoClustered` (
                 `id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ,
                 `lat` VARCHAR( 15 ) CHARACTER SET ASCII NOT NULL ,
                 `long` VARCHAR( 15 ) CHARACTER SET ASCII NOT NULL ,
                 `speed` DOUBLE DEFAULT NULL ,
                 `bearing` DOUBLE DEFAULT NULL ,
                 `fixtime` BIGINT( 20 ) DEFAULT NULL ,
                PRIMARY KEY (  `id` )
                ) ENGINE = INNODB DEFAULT CHARSET = latin1;";
        mysqli_query($this->con, $sql);
    }

    // destructor
    function __destruct()
    {
        //$sql = "DROP TABLE IF EXISTS `geoClustered`";
        //mysqli_query($this->con, $sql);
    }

    function cluster($lat, $lng, $bearing, $speed, $fixtime)
    {
        //1. check if this point lies within x-meters from other points
        
    }
}

?>