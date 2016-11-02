<?php
/*
if (!defined('twoDays')) define("twoDays", 48*3600);
if (!defined('MinutesBeforeNow')) define("MinutesBeforeNow", 15);
if (!defined('HoursBeforeNow')) define("HoursBeforeNow", 6);
$x15min = MinutesBeforeNow*60;
$x6hours = HoursBeforeNow * 3600;
if (!defined('treq_condition1')) define ("treq_condition1", "
    				  (status IS NULL AND TIME_TO_SEC( TIMEDIFF( NOW( ) , time ) ) <= $x15min)
    				  "
    		);
if (!defined('treq_condition2')) define ("treq_condition2", "
							(
    				  (status = 'assigned' OR status = 'picked') 
    				  AND 
    				  (TIME_TO_SEC( TIMEDIFF( NOW( ) , time ) ) <= $x6hours)
    				  )"
    		);
if (!defined('Latitude_dist')) define("Latitude_dist", 0.1);
if (!defined('Longitude_dist')) define("Longitude_dist", 0.1);
if (!defined('loc_expire_time_insec')) define("loc_expire_time_insec", 12.0*3600);
*/
?>
