<?php
/**
 * Created by PhpStorm.
 * User: mohammad.haider
 * Date: 006 11/6/2016
 * Time: 9:10 AM
 */
set_include_path("../");
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$query = "SELECT * FROM `geo`";
$result = mysqli_query($db->con, $query);
while ($row = mysqli_fetch_assoc($result)){
    $speed = $row['speed'];
    $isCal = false;
    if($speed == 0) {
        $isCal = true;
    }
}
