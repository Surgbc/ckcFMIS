<?php

if(!DEFINED("CSYBER")) exit("unauthorised config access!");

/*
 * MYSQL SERVER AND DB CREDENTIALS
 */
DEFINE("SERVER", "localhost");
DEFINE("DBUSER", "root");
DEFINE("DBPASS", "ca!@kaql");

DEFINE("CSYBER_DB", "JKUSDATR");


/*
 *	CKC CREDENTIALS
 */
DEFINE("CKCLOGIN", "http://ckcfinancialsystem.org/login.php");
DEFINE("CKCUSER", "Rimpa");
DEFINE("CKCPASS", "rimpa");
DEFINE("CHURCHNAME",sprintf("%s", CKCUSER));
//DEFINE("CKCUSER", "");
//DEFINE("CKCPASS", "");

//where to store the cookies
/*
	Modify this
*/
DEFINE("CKCCOOKIE", dirname(__FILE__)."/ckccookie"); 
DEFINE("CKCRECEIPT", "http://ckcfinancialsystem.org/addreceipt.php");
DEFINE("CKCRECEIPTSUCCESS","Record has been Added!");
DEFINE("CKCSTATEMENT", "http://ckcfinancialsystem.org/searchtreasurer.php");//searchtreasurer.php

DEFINE("AUTH_SALT", "JKUSDATtr");

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
define('B','<b>');
define('B1','</b>');

DEFINE("RECEIPTIMGPATH","ExtraFiles/Receipt.png");
DEFINE("RECEIPTSFILE","ExtraFiles/Receipts/");


DEFINE("MAILSERVER",'mail.adhrc.co.ke');
DEFINE("MAILSERVERPORT",25);
DEFINE("MAILSERVERENCRYPTION",'');
DEFINE("MAILSERVERUNAME",'jkusda@adhrc.co.ke');
DEFINE("MAILSERVERPASS",'JKUSDA123#');

DEFINE("FROMEMAIL", "jkusda@adhrc.co.ke");
DEFINE("FROMNAME", sprintf("%s Treasury", CHURCHNAME));
DEFINE("REPLYEMAIL", "adarzeph@gmail.com");
DEFINE("REPLYNAME", "Treasurer");

?>
