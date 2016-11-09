<?php
/**
 * Created by PhpStorm.
 * User: mohammad.haider
 * Date: 008 11/8/2016
 * Time: 9:48 PM
 */
set_include_path("../");
require_once 'include/DB_Functions.php';

if (!empty($_GET['sel']) && !empty($_POST['value'])) {
    $db = new DB_Functions();
    // query for options based on value
    $html = "<option>--Select--</option>" ."\n";
    switch($_GET['sel']) {
        case 1:
            $sql = "SELECT DISTINCT userid FROM (SELECT *, DATE_Format(`time`,'%d-%m-%Y') as date FROM geo HAVING date = '"
                .$_POST['value']
                ."') as t";
            $result = mysqli_query($db->con, $sql);
            while ($row = mysqli_fetch_assoc($result)){
                $tmp = $row['userid'];
                $html .= "<option value='".$tmp."'>".$tmp."</option>" ."\n";
            }
            break;
        case 2:
            if (!empty($_POST['value1'])) {
                $sql = "SELECT DISTINCT tripid FROM (SELECT *, DATE_Format(`time`,'%d-%m-%Y') as date FROM geo HAVING date = '"
                    .$_POST['value1'] ."' AND userid=" .$_POST['value']
                    .") as t";
                $result = mysqli_query($db->con, $sql);
                while ($row = mysqli_fetch_assoc($result)){
                    $tmp = $row['tripid'];
                    $html .= "<option value='".$tmp."'>".$tmp."</option>" ."\n";
                }
            }
            break;
    }
    die($html);
}
die('error');
?>