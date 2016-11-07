<?php
/**
 * Created by PhpStorm.
 * User: mohammad.haider
 * Date: 006 11/6/2016
 * Time: 9:10 AM
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (true || isset($_POST['tag']) && $_POST['tag'] != '')
{
    $tag = $_POST['tag'];
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();

    $response = array("tag" => $tag, "error" => FALSE);
    // check for tag type
    if (true || $tag == 'login') {
        if(isset($_GET['date']))
            $aDate = $_GET['date'];
        else
            $aDate = "";

        if(isset($_GET['limit']))
            $limit = $_GET['limit'];
        else
            $limit = 0;

        if(isset($_GET['initial']))
            $initial = $_GET['initial'];
        else
            $initial = 0;

        $query = "SELECT *, DATE_Format(`time`,'%d-%m-%Y') AS date FROM `geo`";
        if(!empty($aDate)) {
            $query .= "HAVING date = '$aDate'";
        }
        if($limit != 0) {
            $query .= "LIMIT ". $initial ."," .$limit;
        }
        $result = mysqli_query($db->con, $query);
        $data = array();
        $firstPoint = true;
        while ($row = mysqli_fetch_assoc($result)) {
            if($firstPoint) {
                $prevLat = $row['lat'];
                $prevLong = $row['long'];
                $firstPoint = false;
            } else {
                $speed = $row['speed'];
                $isCal = false;
                if ($speed == 0) {
                    $isCal = true;
                }
            }
            array_push($data, $row);
        }
        $response["error"] = false;
        $response["error_msg"] = "Done";
        $response["error_no"] = "0";
        $response["data"] = $data;

        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Unknow 'tag' value";
        $response["error_no"] = "2";
        echo json_encode($response);
    }
    
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter is missing!";
    $response["error_no"] = "3";
    echo json_encode($response);
}
