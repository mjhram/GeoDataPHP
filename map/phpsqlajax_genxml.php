<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_include_path("../");
require_once 'include/DB_Functions.php';

function parseToXML($htmlStr) 
{ 
    $xmlStr=str_replace('<','&lt;',$htmlStr);
    $xmlStr=str_replace('>','&gt;',$xmlStr);
    $xmlStr=str_replace('"','&quot;',$xmlStr);
    $xmlStr=str_replace("'",'&apos;',$xmlStr);
    $xmlStr=str_replace("&",'&amp;',$xmlStr);
    return $xmlStr;
}
if(isset($_GET['date']))
    $aDate = $_GET['date'];
else
    $aDate = "";
// Opens a connection to a mySQL server
$db = new DB_Functions();

/*$connection=mysql_connect (localhost, $username, $password);
if (!$connection) {
  die('Not connected : ' . mysql_error());
}

// Set the active mySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
}*/

// Select all the rows in the markers table
//$query = "SELECT * FROM geo WHERE 1";
$sql = "SET @@session.time_zone = '+03:00';";
mysqli_query($db->con, $sql);
$query = "SELECT *, DATE_Format(`time`,'%d-%m-%Y') AS date FROM `geo`";
if(!empty($aDate)) {
    $query .= "HAVING date = '$aDate'";
}

$result = mysqli_query($db->con, $query);
if (!$result) {
  die('Invalid query: ' . mysqli_error($db->con));
}

header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<markers>';

// Iterate through the rows, printing XML nodes for each
while ($row = mysqli_fetch_assoc($result)){
    $hasInfo = $row['hasInfo'];
    // accuracy, altitude, bearing, speed
    if($hasInfo[2]=='1' &&$hasInfo[3]=='1') {
        echo '<marker ';
        echo 'lat="' . $row['lat'] . '" ';
        echo 'lng="' . $row['long'] . '" ';
        echo 'speed="' . $row['speed'] . '" ';
        echo 'accuracy="' . $row['accuracy'] . '" ';
        echo 'bearing="' . $row['bearing'] . '" ';
        echo '/>';
    }
}

// End XML file
echo '</markers>';

?>