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

    function angleDiff($firstAngle, $secondAngle) {
        $difference = $secondAngle - $firstAngle;
        while ($difference < -180) $difference += 360;
        while ($difference > 180) $difference -= 360;
        return $difference;
    }

    function normAngle($difference) {
        while ($difference < -180) $difference += 360;
        while ($difference > 180) $difference -= 360;
        return $difference;
    }

    function cluster($lat, $lng, $bearing, $speed, $fixtime)
    {
        //1. check if this point lies within xy-meters from other points
        $sql = "SELECT * FROM geoClustered";
        $result = mysqli_query($this->con, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $diff = normAngle($bearing - $row['bearing']);
            if(($lat > $row['lat']-0.00018 && $lat < $row['lat']+0.00018) && //~20m
                ($lng>$row['long']-0.00022 && $lng < $row['long']+0.00022) && //~20m
                ($diff >-45 && $diff<45)
                )
            {
                //re average the speed
                $n = $row['clusterN']+1;
                $newSpeed = ($row['speed'] * $row['clusterN'] + $speed) /$n;
                //store it
                $id = $row['id'];
                $sql = "UPDATE geoClustered SET speed = $newSpeed, clusterN=$n WHERE id='$id'";
                mysqli_query($GLOBALS["___mysqli_ston"], $sql);
                return;
            }
        }
        //not belong to any cluster:
        $sql = "INSERT INTO geoClustered (lat, long, speed, bearing, fixtime) VALUES('$lat', '$lng', $bearing, $speed, $fixtime)";
        mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    }
}

?>