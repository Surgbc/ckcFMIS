<?php

if(!DEFINED("CSYBER")) exit("unauthorised config access!");

/*
 * MYSQL SERVER AND DB CREDENTIALS
 */
DEFINE("SERVER", "localhost");
DEFINE("DBUSER", "root");
DEFINE("DBPASS", "");

DEFINE("CSYBER_DB", "JKUSDATR");


/*
 *	CKC CREDENTIALS
 */
DEFINE("CKCLOGIN", "http://ckcfinancialsystem.org/login.php");
DEFINE("CKCUSER", "");
DEFINE("CKCPASS", "");
//DEFINE("CKCUSER", "");
//DEFINE("CKCPASS", "");

//where to store the cookies
/*
	Modify this
*/
DEFINE("CKCCOOKIE", dirname(__FILE__)."\ckccookie"); 
DEFINE("CKCRECEIPT", "http://ckcfinancialsystem.org/addreceipt.php");
DEFINE("CKCRECEIPTSUCCESS","Record has been Added!");
DEFINE("CKCSTATEMENT", "http://ckcfinancialsystem.org/searchtreasurer.php");//searchtreasurer.php

DEFINE("AUTH_SALT", "JKUSDATtr");

?>