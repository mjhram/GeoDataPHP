<?php



class DB_Functions {
 
    private $db;
 	public $con;

    //put your code here
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
		$con = $this->con = $this->db->connect();
		mysqli_set_charset($con, 'utf8');
    }
 
    // destructor
    function __destruct() {
         
    }
 
	public function login() {
		$insertStr = "INSERT INTO users() VALUES()";
		$result = mysqli_query($GLOBALS["___mysqli_ston"], $insertStr);
		// check for successful store
		if ($result) {
			// get user details
			$uid = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res); // last inserted id
			return $uid;
		} else {
			return false;
		}
	}

	public function userExists($userid){
		$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE id = '$userid'");
		if($result==true && ($row =  mysqli_fetch_array($result)) != null) {
			return true; //user exists
		}
		return false;//user not exist
	}

	public function storeGeoData($uid, $lat, $long, $speed, $bearing, $accuracy, $fixtime) {
		if($this->userExists($uid) == false) {
			$newId = login();
			if($newId != false) {
				$uid = $newId;
			} else {
				$uid = -1;//unknown user
			}
		}
		$insertStr = "INSERT INTO `geo`(`userid` ,`lat` ,`long` ,`speed` ,`bearing` ,`accuracy` ,`fixtime` ) "
			."VALUES('$uid, '$lat', '$long', '$speed', '$bearing', '$accuracy', '$fixtime')";
		$result = mysqli_query($GLOBALS["___mysqli_ston"], $insertStr);
		if ($result) {
			return true;//success
		} else {
			return false;
		}
	}





    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $phone, $password, $gcmRegId, $type) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
		$insertStr = "INSERT INTO users(unique_id, name, email, phone, ";
		if(!empty($gcmRegId)) {
			$insertStr .= "regId,";
		}
		$insertStr .= " Type, encrypted_password, salt, created_at)";
		$insertStr .= "VALUES('$uuid', '$name', '$email', '$phone', ";
		if(!empty($gcmRegId)) {
			$insertStr .= "'$gcmRegId',";
		}
		$insertStr .= " '$type', '$encrypted_password', '$salt', NOW())";

		$result = mysqli_query($GLOBALS["___mysqli_ston"], $insertStr);
        // check for successful store
        if ($result) {
            // get user details 
            $uid = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res); // last inserted id
            $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users WHERE uid = $uid");
            // return user details
            return mysqli_fetch_array($result);
        } else {
            return false;
        }
    }

	/**
	 * Get user by name and password
	 */
	public function getUserByNameAndPassword($name, $password, $type, $regId) {
		//1. check same type, OK, else
		//2. check any type, if OK, add type (cannot be implemented since name and email are unique)
		$sql="SELECT * FROM users  LEFT JOIN carinfo ON uid=driverId WHERE name = '$name' AND type='$type'";
		$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		// check for result
		$no_of_rows = mysqli_num_rows($result);
		if ($no_of_rows > 0) {
			//user exist with same type
			$result = mysqli_fetch_array($result);
			$salt = $result['salt'];
			$encrypted_password = $result['encrypted_password'];
			$hash = $this->checkhashSSHA($salt, $password);
			//echo $salt ."---" .$password ."---" .$hash;
			// check for password equality
			if ($encrypted_password == $hash) {
				// user authentication details are correct
				return $result;
			}
		} /*else {
			//user not found. check for any type
			$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users  LEFT JOIN carinfo ON uid=driverId WHERE name = '$name'") or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			// check for result
			$no_of_rows = mysqli_num_rows($result);
			if ($no_of_rows > 0) {
				//user exist with same type
				$result = mysqli_fetch_array($result);
				$salt = $result['salt'];
				$encrypted_password = $result['encrypted_password'];
				$hash = $this->checkhashSSHA($salt, $password);
				// check for password equality
				if ($encrypted_password == $hash) {
					//duplicate the user, with new type
					$newuser = $this->duplicateUser($result, $type, $regId);
					return $newuser;
				}
			}
		}*/
		// user not found
		return false;
	}

    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
        $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM users  LEFT JOIN carinfo ON uid=driverId WHERE email = '$email'") or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        // check for result 
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysqli_fetch_array($result);
            $salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }
 
    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT uid, unique_id, name, email, phone, regId, Type, created_at, updated_at, Type from users WHERE email = '$email'");
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed
            return mysqli_fetch_array($result);
        } else {
            // user not existed
            return false;
        }
    }

	public function isUserNameExist($name) {
		$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT uid, unique_id, name, email, phone, regId, Type, created_at, updated_at, Type from users WHERE name = '$name'");
		$no_of_rows = mysqli_num_rows($result);
		if ($no_of_rows > 0) {
			// user existed
			return mysqli_fetch_array($result);
		} else {
			// user not existed
			return false;
		}
	}

	public function getTotals() {
		$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT Type, COUNT(Type) as counts FROM users GROUP BY Type ORDER BY Type DESC");
		$counts['Pas'] = 0;
		$counts['Drv'] = 0;
		while($row = $result->fetch_assoc()) {
			if(strcmp($row['Type'], "Pas")==0){
				$counts['Pas']= $row['counts'];
			}elseif(strcmp($row['Type'], "DRV")==0){
				$counts['Drv']= $row['counts'];
			}
		}
		return $counts;
	}

	public function getAnnouncements($type) {
		$result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM announcements WHERE type='$type' ORDER BY time DESC");
		$no_of_rows = mysqli_num_rows($result);
		if ($no_of_rows > 0) {
			// user existed
			return mysqli_fetch_array($result);
		} else {
			// user not existed
			return array();
		}
	}
	
    /*public function getTRequestsRowByRow($firstTime) {
    	if($firstTime){
	    	$now = new DateTime();
	    	$x15min = 0;//$now->getTimestamp() - 15*60;
	    	//echo $x15min;
	    	$result = mysql_query("SELECT * from taxi_requests WHERE time >= '$x15min' AND driverId <= 0");
	        $no_of_rows = mysql_num_rows($result);
	        if ($no_of_rows > 0) {
	            // user existed 
	            return mysql_fetch_array($result);
	        } else {
	            // no requests
	            return false;
	        }
	    } else {
	    	return mysql_fetch_array($result);
	    }
    }*/

	public function updateCarInfo($driverId, $brand, $model, $make, $color, $plateno, $other) {
		$sql2 = "INSERT INTO carinfo(driverId, brand, model, make, color, plateno, other)";
		$sql2 .= "VALUES($driverId, '$brand', '$model', '$make', '$color', '$plateno', '$other')";
		$sql2 .= "ON DUPLICATE KEY UPDATE brand='$brand', model='$model', make='$make', color='$color', plateno='$plateno', other='$other'";
		$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql2);
		//echo $sql2;
		return $result2;
	}

    public function addTRequest($email, $passangerId, $long, $lat, $long2, $lat2,
								$fromDesc, $toDesc,
								$phone, $suggestedFee, $noOfPassangers, $additionalNotes) {
    	//if($isNew) 
    	{
    		$sql = "INSERT INTO taxi_requests(passangerEmail, passangerId, fromLong, fromLat, toLong, toLat, fromDesc, toDesc, requester_mobile, suggestedFee, noOfPassangers, additionalNotes)";

			$sql .= "VALUES('$email', '$passangerId', '$long', '$lat', '$long2', '$lat2', '$fromDesc', '$toDesc', '$phone', '$suggestedFee', '$noOfPassangers', '$additionalNotes')";

			//echo $sql;
        	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        	// check for successful store
        	if($result) {
        		$insertedId = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
				$sql2 = "INSERT INTO passenger_req(passenger_uid, request_idx)";
				$sql2 .= "VALUES('$passangerId', '$insertedId')";
				$sql2 .= "ON DUPLICATE KEY UPDATE request_idx='$insertedId'";

				$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql2);
				$this->notifyAllDrivers($insertedId, $long, $lat);
        		return $insertedId;
        	} else {
        		return -1;
        	}
        	
        } 
    }

	public function getRequests4Driver($drvId, $getCount) {
		include 'include/constants.php';
		//$now = new DateTime();
		//$x15min = $now->getTimestamp() - MinutesBeforeNow*60;
		$x6hours = HoursBeforeNow * 3600;

		//1. tasks not assigned yet
		$sql = "SELECT * FROM taxi_requests LEFT JOIN users ON users.uid=taxi_requests.passangerId
                RIGHT JOIN passenger_req ON idx = request_idx
    			WHERE taxi_requests.driverId = '$drvId'
    			AND " .treq_condition2 .
			//(status = 'assigned' OR status = 'picked')
			//AND time >= $x6hours
			" ORDER BY time DESC";
		//echo $sql;

		$requests = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		//echo $sql;
		if($requests==false) {
			return array();
		}
		$no_of_rows = mysqli_num_rows($requests);
		if($getCount==true) {
			return $no_of_rows;
		}
		if ($no_of_rows == 1) {
			// user existed
			$row =  mysqli_fetch_array($requests);
		} else if ($no_of_rows > 1){
			//more than one request
			$row =  mysqli_fetch_array($requests);
		} else {
			// no requests
			$row = array();
		}
		return $row;
	}

	public function getRequests4Passenger($passengerId) {
		include 'include/constants.php';
		//$now = new DateTime();
		$x15min = MinutesBeforeNow*60;
		$x6hours = HoursBeforeNow * 3600;
		//1. tasks not assigned yet time within 15min
		//2. tasks assigned, time within 6hour
		$sql = "SELECT *, TIME_TO_SEC( TIMEDIFF( NOW( ) , time ) ) as secondsToNow  FROM taxi_requests
				LEFT JOIN users ON users.uid=taxi_requests.passangerId
				RIGHT JOIN passenger_req ON idx = request_idx
    			WHERE taxi_requests.passangerId = '$passengerId'
    				AND (" .treq_condition1 ." OR " .treq_condition2 .
			/*(
              (status IS NULL AND TIME_TO_SEC( TIMEDIFF( NOW( ) , time ) ) <= $x15min) OR
              (status = 'assigned' AND TIME_TO_SEC( TIMEDIFF( NOW( ) , time ) ) <= $x6hours) OR
              (status = 'picked' AND TIME_TO_SEC( TIMEDIFF( NOW( ) , time ) ) <= $x6hours)
              ) */
			") ORDER BY time DESC"; //AND driverId <= 0
		//echo $sql;

		$requests = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		//echo $sql;
		$no_of_rows = mysqli_num_rows($requests);
		if ($no_of_rows == 1) {
			// user existed
			$row =  mysqli_fetch_array($requests);
		} else if ($no_of_rows > 1){
			//more than one request
			$row =  mysqli_fetch_array($requests);
		} else {
			// no requests
			$row = array();
		}
		return $row;
	}

	public function getAllDrivers($passengerLong, $passengerLat){
		include 'include/constants.php';
		$longMin = $passengerLong - Longitude_dist;
		$longMax = $passengerLong + Longitude_dist;
		$latMin = $passengerLat - Latitude_dist;
		$latMax = $passengerLat + Latitude_dist;
		//conditions:
		// 1. Driver
		// 2. withing 25km
		// 3. must be not assigned
		// 4. has updated state (less than 6h)
		$sql = "SET @@session.time_zone = '+03:00';";
		mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$locExpireTime = loc_expire_time_insec;
		$sql = "SELECT uid, taxi_loc.time AS loc_time, users.name, phone, image_id, longitude, latitude FROM users LEFT JOIN taxi_loc ON users.uid=taxi_loc.driverId
				WHERE users.Type = 'DRV' AND
				taxi_loc.state = '0' AND
				(TIME_TO_SEC( TIMEDIFF( NOW( ) , taxi_loc.time ) ) <= $locExpireTime) AND
				(longitude BETWEEN '$longMin' AND '$longMax') AND
				(latitude BETWEEN '$latMin' AND '$latMax')
				";
		$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		// users found
		//check regId
		//if($regId == $user["regId"])
		$result = array();
		while ($row = mysqli_fetch_assoc($result2)) {
			array_push($result,
				array(
					'drvId'=>$row['uid'],
					'name' => $row['name'],
					'loc_time' => $row['loc_time'],
					'phone' => $row['phone'],
					'imageId' => $row["image_id"],
					'long'=>$row['longitude'],
					'lat'=>$row['latitude']
				));
		};
		return $result;
	}

	public function notifyAllDrivers($message, $passengerLong, $passengerLat) {
   		include_once 'gcm.php';
		include 'include/constants.php';
		$gcm = new GCM();
		$longMin = $passengerLong - Longitude_dist;
		$longMax = $passengerLong + Longitude_dist;
		$latMin = $passengerLat - Latitude_dist;
		$latMax = $passengerLat + Latitude_dist;
		//conditions:
		// 1. Driver
		// 2. withing 25km
		// 3. must be not assigned
		// 4. has updated state (less than 6h)
		$sql = "SELECT uid, longitude, latitude, regId FROM users LEFT JOIN taxi_loc ON users.uid=taxi_loc.driverId
				WHERE users.Type = 'DRV' AND
				(longitude BETWEEN '$longMin' AND '$longMax') AND
				(latitude BETWEEN '$latMin' AND '$latMax')
				";
		$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		while($row = mysqli_fetch_assoc($result2)) {
				if(!empty($row['regId'])) {
						$registatoin_ids[] = $row['regId'];
						$uids[] = $row['uid'];
				}
		}
		if(!isset($registatoin_ids) || count($registatoin_ids)<=0 ) {
			return;
		}
		//print_r($registatoin_ids);
		//echo "-:-";
		//print_r($message);
		//echo "-:-";
		$message = array("message" => $message);
		$result = $gcm->send_notification($registatoin_ids, $message);
		//process results:
		//echo "-:-".$result ."------";
		$res = json_decode($result, true);
		$res = $res['results'];
		//var_dump($res);
		for($i=0; $i<count($registatoin_ids);$i++) {
				//echo "--".$i.":";
				$tmp_id = $uids[$i];
				if(isset($res[$i]['message_id'])) {
					//echo "-";
					if(isset($res[$i]['registration_id'])) {
							$tmp = $res[$i]['registration_id'];
							//replace registeration id by canonical id (official/newer one)
							$sql = "UPDATE users SET regId = '$tmp' WHERE uid = '$tmp_id'";
							$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
							//echo "update it".$i;
					}//else keep reg_id
				} else if(isset($res[$i]['error'])){
						//remove regid
						$sql = "UPDATE users SET regId = '' WHERE uid = '$tmp_id'";
						$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						//echo "remove it".$i;
				}
		}
		return $result;
	}
	
	public function acceptTReqByDriver($passangerId, $drvId, $tReqId) {
    	//if($isNew) 
    	{
    		$error = $this->updateTRequest($tReqId, "assigned", $drvId);
        	// check for successful store
        	if(!$error) {
        		$message = array(
		        	'tag' => "drvId",
		        	'data' => $drvId
		        );
        		$this->notifyPassanger($passangerId, json_encode($message));
        		return true;
        	} else {
        		return false;
        	}
        	
        } 
    }
	
	public function notifyPassanger($passId, $message) {
   		include_once 'gcm.php';
		$gcm = new GCM();
		$sql = "SELECT uid, regId FROM users WHERE Type = 'Pas' AND uid='$passId'";
		//echo $sql;
		$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if($result2 == false) {
			//echo "empty";
			return;
		}
		$registatoin_ids = array();
		while($row = mysqli_fetch_assoc($result2)) {
			//print_r($row);
			$registatoin_ids[] = $row['regId'];
			$uids[] = $row['uid'];
			//$registatoin_ids = array($registatoin_id);
		}
		if(empty($registatoin_ids)) {
			//echo "2";
			return;
		}
		$message = array("message" => $message);
		$result = $gcm->send_notification($registatoin_ids, $message);
		
		//process results:
		$res = json_decode($result, true);
		$res = $res['results'];
		//var_dump($res);
		for($i=0; $i<count($registatoin_ids);$i++) {
				//echo "--".$i.":";
				$tmp_id = $uids[$i];
				if(isset($res[$i]['message_id'])) {
					//echo "-";
					if(isset($res[$i]['registration_id'])) {
						$tmp = $res[$i]['registration_id'];
						
						//replace registeration id by canonical id (official/newer one)
						$sql = "UPDATE users SET regId = '$tmp' WHERE uid = '$tmp_id'";
						$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						//echo "update it".$i;
					}//else keep reg_id
				} else if(isset($res[$i]['error'])){
					//remove regid
					$sql = "UPDATE users SET regId = '' WHERE uid = '$tmp_id'";
						$result2 = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					//echo "remove it".$i;
				}
		}
		return $result;
	}
	
    public function isTaxiHasLocation($email) {
        $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT email from taxi_loc WHERE email = '$email'");
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }
    
    public function getTaxiLocation($drvId) {
		if($drvId == -1) {
			return array();
		}
    	$sql = "SELECT * from taxi_loc WHERE driverId = '$drvId'";
        $result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        if($result == false) {
        	return array();
        }
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
        	$row =  mysqli_fetch_array($result);
            // user existed 
            $resArray = array(
            	'drvId' => $row['driverId'],
            	'latitude' => $row['latitude'],
            	'longitude' => $row['longitude'],
            	'time' => $row['time'],
            	'state' => $row['state']
            );
            return $resArray;
        } else {
            // no driver
            return array();
        }
    }
    
    public function updateTRequest($requestId, $state, $drvId) {
    	{
        	$sql = "UPDATE taxi_requests SET cnt=cnt+1, driverId='$drvId', status='$state' WHERE idx='$requestId'";
        	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        	//echo $sql;
        	// check for successful store
            return !$result;
        }
    }

	public function cancelTRequest($requestId) {
		{
			$sql = "UPDATE taxi_requests SET cnt=cnt+1, driverId='-1', status=NULL WHERE idx='$requestId'";
			$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
			//echo $sql;
			// check for successful store
			return !$result;
		}
	}
    
    public function storeTaxiLoc($drvId, $long, $lat, $state, $isNew) {
    	if($isNew) {
    		$sql = "INSERT INTO taxi_loc(driverId, longitude, latitude, state) VALUES('$drvId', '$long', '$lat', '$state')";
    		//echo $sql;
        	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        	// check for successful store
            	return !$result;
            	
        } else {
        	$sql = "UPDATE taxi_loc SET cnt=cnt+1, longitude = '$long', latitude='$lat', state='$state' WHERE driverId='$drvId'";
        	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        	//echo $sql;
        	// check for successful store
            	return !$result;
        }
    }
    
    public function storeTaxiState($drvId, $state, $isNew) {
    	if($isNew) {
    		$sql = "INSERT INTO taxi_loc(driverId, state) VALUES('$drvId', '$state')";
    		//echo $sql;
        	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        	// check for successful store
            	return !$result;
        } else {
        	$sql = "UPDATE taxi_loc SET cnt=cnt+1, state='$state' WHERE driverId='$drvId'";
        	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
        	//echo $sql;
        	// check for successful store
            	return !$result;
        }
    }
 
	public  function updatePassword($uid, $password) {
		$hash = $this->hashSSHA($password);
		$encrypted_password = $hash["encrypted"]; // encrypted password
		$salt = $hash["salt"];
		$query = "UPDATE users SET encrypted_password='".$encrypted_password."', salt='".$salt."' where uid=".$uid;
		$result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
		// check for successful store
		if ($result) {
			//success
			return true;
		} else {
			return false;
		}
	}
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    }

	function distance($lat1, $lon1, $lat2, $lon2, $unit) {

		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}
}
 
?>