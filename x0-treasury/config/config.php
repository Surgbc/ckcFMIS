<?php

if(!DEFINED("CSYBER")) exit("unauthorised config access!");

//database
DEFINE("SERVER", "localhost");
DEFINE("DBUSER", "root");
DEFINE("DBPASS", "");

DEFINE("CSYBER_DB", "JKUSDATR");

//CKC CONFIG
DEFINE("CKCLOGIN", "http://ckcfinancialsystem.org/login.php");
DEFINE("CKCUSER", "");
DEFINE("CKCPASS", "");
//DEFINE("CKCCOOKIE", "C:\wamp\www\\treasury\use/ckcookie.txt"); 
DEFINE("CKCCOOKIE", dirname(__FILE__)."\ckccookie"); 
DEFINE("CKCRECEIPT", "http://ckcfinancialsystem.org/addreceipt.php");
DEFINE("CKCRECEIPTSUCCESS","Record has been Added!");
DEFINE("CKCSTATEMENT", "http://ckcfinancialsystem.org/searchtreasurer.php");//searchtreasurer.php

DEFINE("AUTH_SALT", "JKUSDATtr");

?>