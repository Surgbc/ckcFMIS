<?php
/*
 * This file can be used for debugging. Just remember to uncomment line 6
 */
//required to include a number of files 
DEFINE("CSYBER", "JKUSDATR");
//var_dump($_GET);exit();
/*
 *	Files required everywhere
 */
include "config/config.php";			//configuration
include "src/csyber.class.php";			//*main_class: all csyber
include "src/jkusdatr.class.php";		//*main_class: jkusdatr
include "src/auth.class.php";			//*main_auth:  beta
include "src/ui.class.php";				//class to display output


$auth = new jkusdatr_auth();
$auth->__start();
		$ret = $auth->__process_request();
echo $ret;
