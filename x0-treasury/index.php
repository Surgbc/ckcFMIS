<?php

/*
 * JKUSDA FINANCIAL MANAGEMENT SYSTEM V1.0
 * Sep 23, 2014
 * Brian Onang'o
 */
 
 
DEFINE("CSYBER", "JKUSDATR");

include "config/config.php";
include "src/jkusdatr.class.php";

session_start();

$treasury = new jkusdatreasury();

//show page if no *main_request
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
		$receipt = new jkusdatr_receipts();
		$ret = $receipt->__process_request();
		break;
	case "statement":
		include "src/statement.class.php";
		$statement = new jkusdatr_statement();
		$ret = $statement->__process_request();
		break;
	case "manual":
		include "src/manual.class.php";
		$statement = new jkusdatr_manual();
		$ret = $statement->__process_request();
		break;
	case "auth":
		include "src/auth.class.php";
		$statement = new jkusdatr_auth();
		$ret = $statement->__process_request();
		break;
	default:
		defaulta:
		include "src/ui.class.php";
		$ui = new jkusdatr_ui();
		$ret = $ui->__process_request();
		break;
}
echo $ret;
exit();
/*
	TUESDAY:
		CSYBER:
			JKUSDAtr
				Homepage
					Error handler
					Loader
					DISPLAY(er)
				Outside page
					Help
				More:
					____________________________
				
		LOS
		ACC
		
	
	Auth: auto.........................................(1)
	Manual:
		For auth........................................2
		For receipts....................................3
		For statements..................................4
		Integrate auth into all /|\these................5
		UI: ............................................6
			But where is the time? 5 more days to go
		More: 
			Opening Server
			Local
	UI:
		Receipts
		Statement
		Manual
	_________________________________________________
	Error handling
	Manual
	
	receipts..................................1
		upload
			multiple receipts that stall along the way so that receipt["Uploaded"] = iF
	statements................................2
		all
			ccount
				consider:
					-> by_id
					-> view_statement
	manual.....................................3
			tentative
	logging in
		.......................................1..1
	More: Wednesday
			man
			logging in
		Thursday:
			ui
			openning server....................2...2
		More:
			local
	ui
		.......................................3....3
	local
		.......................................5
	openning server
		.......................................7
	More:
		ckc_functions error handling
			in statement
			in receipts
*/
?>