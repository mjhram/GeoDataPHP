<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("logger/ip_tracker.php");

if (isset($_POST['tag']) && $_POST['tag'] != '') {
    $tag = $_POST['tag'];
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();

    // response Array
    $response = array("tag" => $tag, "error" => FALSE);
     // check for tag type
    if ($tag == 'login') {

        $user = $db->login();
        if ($user != false) {
            $response["error"] = FALSE;
            $response["error_no"] = "0";
            $response["uid"] = $user;
        } else {
            $response["error"] = TRUE;
            $response["error_msg"] = "Can't add new user";
            $response["error_no"] = "1";
        }
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

?>
