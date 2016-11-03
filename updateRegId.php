 <?php
 include("logger/ip_tracker.php");

 //if (isset($_POST['tag']) && $_POST['tag'] != '') 
{
    $tag = "";//$_POST['tag'];
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("tag" => $tag, "error" => FALSE);
 
    // check for tag type
    //if ($tag == 'login') 
    {
        $userId = $_POST['userId'];
        $regId = $_POST['regId'];
        $error = $db->updateUserRegId($userId, $regId);
        
        if($error) {
            // error updating trequest
            $response["error"] = TRUE;
            $response["error_msg"] = "Error while updating registeration id!";
            $response["error_no"] = "51";
            echo json_encode($response);
        } else {
        	$response["error"] = false;
        	$response["error_msg"] = "RegId updated successfully";
            $response["error_no"] = "50";
            echo json_encode($response);
        }
    } 
} 
?>
