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
$query = "SELECT * FROM geo WHERE 1";
$result = mysqli_query($db->con, $query);
if (!$result) {
  die('Invalid query: ' . mysqli_error($db->con));
}

header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<markers>';

// Iterate through the rows, printing XML nodes for each
while ($row = mysqli_fetch_assoc($result)){
  // ADD TO XML DOCUMENT NODE
  echo '<marker ';
  echo 'name="Marker Name" ';
  echo 'address="Marker Address" ';
  echo 'lat="' . $row['lat'] . '" ';
  echo 'lng="' . $row['long'] . '" ';
  echo 'speed="' . $row['speed'] . '" ';
  echo 'accuracy="' . $row['accuracy'] . '" ';
  echo 'type="resturant" ';
  echo '/>';
}

// End XML file
echo '</markers>';

?>