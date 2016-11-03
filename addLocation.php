<?php
include("logger/ip_tracker.php");

if (isset($_POST['tag']) && $_POST['tag'] != '') {
	$tag = $_POST['tag'];
	// include db handler
	require_once 'include/DB_Functions.php';
	$db = new DB_Functions();
	// response Array
	$response = array("tag" => $tag, "error" => FALSE);
	// check for tag type
	if ($tag == 'addLocation') {
		$uid = $_POST['uid'];
		$lat = $_POST['lat'];
		$long = $_POST['long'];
		$speed = $_POST['speed'];
		$bearing = $_POST['bearing'];
		$accuracy = $_POST['accuracy'];
		$fixtime = $_POST['fixtime'];
		//check if the user exists
		if ($db->userExists($uid) == false) {
			$newId = $db->login();
			if ($newId != false) {
				$uid = $newId;
				$response["uid"] = $uid;
			} else {
				$uid = -1;//unknown user
			}
		}
		$success = $db->storeGeoData($uid, $lat, $long, $speed, $bearing, $accuracy, $fixtime);
	    if($success) {
            $response["error"] = false;
            $response["error_msg"] = "no error. Data added successfully";
			$response["error_no"] = "10";
            echo json_encode($response);
	    } else {
	    	$response["error"] = true;
            $response["error_msg"] = "Data couldn't be added";
			$response["error_no"] = "11";
            echo json_encode($response);
	    }
	} else {
		$response["error"] = TRUE;
		$response["error_msg"] = "Unknow 'tag' value";
		$response["error_no"] = "12";
		echo json_encode($response);
	}
} else {
	$response["error"] = TRUE;
	$response["error_msg"] = "Required parameter is missing!";
	$response["error_no"] = "13";
	echo json_encode($response);
}

?>