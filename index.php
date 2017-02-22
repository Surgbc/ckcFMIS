<?php

/*
 * JKUSDA FINANCIAL MANAGEMENT SYSTEM V1.0
 * API for using the CKC FINANCIAL SYSTEM
 * Sep 23 - Oct, 2014
 * Brian Onang'o
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

session_start();

/*
 * Identification: auth
 */
$auth = new jkusdatr_auth();
$ui = new jkusdatr_ui($auth);
/*
 *	Each request requires $_GET["main"]
 * Default $_GET["main"] = "page"
 *
 */
isset($_GET["main"])? $csyber_main = stripslashes($_GET["main"]): $csyber_main = "page";

$ret = "";
switch($csyber_main)
{
	case "page":
		goto defaulta;
		break;
	case "receipts":
		//either add, view, 
		include "src/receipts.class.php";
		$receipt = new jkusdatr_receipts($auth);
		$ret = $receipt->__process_request();
		break;
	case "statement":
		include "src/statement.class.php";
		$statement = new jkusdatr_statement();
		$ret = $statement->__process_request();
		break;
	case "man":
		include "src/manual.class.php";
		$statement = new jkusdatr_manual();
		$ret = $statement->__process_request();
		break;
	case "auth":
		//include "src/auth.class.php";
		$auth->__start();
		$ret = $auth->__process_request();
		break;
		/*
		 *	Display the appropriate page or page segment
		 */
	default:
		defaulta:
		//$ui = new jkusdatr_ui($auth);
		$ui->__get_page();
		$ret = $ui->__process_request();
		break;
}
echo $ret;
exit();

/*
 Show user.....................................*
 External access
 Inside:
		Log out................................*
		User adjust functions
	Functions
	Going back...(Cancel &co)
	More:::
	
	Wednesday:
		Treasurer actions
		Deacon actions
		departments
	Thursday
		Receipts
			View 
			Delete
			Print			
		Statement
		Departments
		Cash count
		Manual
			User
			Developer
		
 .
 .
 .
		
*/
?>