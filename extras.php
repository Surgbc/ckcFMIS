<?php

/*

Author: Brian Onang'o
Date:	Friday September 25, 2015

EXTRAS
	- Import emails
	- Create HTML/Img Receipts
	- Send receipts
	
http://localhost/treasury/jkusdatreasury/oldckc/extras.php?action=import&date=2015/9/12
*/

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('UTC\EAT');
Session_start();

DEFINE("CSYBER", "TREASURY");
include "config/config.php";
#include "src/mysql.class.php";
include "src/PHPExcel.class.php";
include "src/csyber.class.php";			//*main_class: all csyber
include "src/jkusdatr.class.php";		//*main_class: jkusdatr
include "src/extras.class.php";

echo "Todo: Set Time Zone",EOL;

$action = isset($_GET["action"])?stripslashes($_GET["action"]):"missing action";
if($action == "missing action")
{
	echo B,"?action=??: ",B1,"Please set what you want to do: import/generate/send", EOL;
	goto pageEnd;
}
$extras = new Extras();
$date = isset($_GET["date"])?stripslashes($_GET["date"]):false;
	if($date === false)
	{
		echo B,"Fatal : ",B1,"Date Not Set", EOL;
		goto pageEnd;
	}
$extras->__set_Date($date);
if($action == "import")
{
echo date("H:i:s")," Starting Importer",EOL;
//$importObjCreator = new PHPExcel();
$filepath = "ExtraFiles/Emails.csv";
$file = fopen($filepath,"r");

while(! feof($file))
{
  $tmp = fgetcsv($file);
  if(!isset($tmp[0]))continue;
  $NAME = addslashes($tmp[0]);
  $EMAIL = addslashes($tmp[1]);
  if($NAME == 'NAME')continue; //skip first rows
  if($EMAIL == '')
    {
    $names = explode(' ', $NAME);
    foreach($names as $name)$EMAIL .= $name;
    }
    if($EMAIL[0] == '_')
    {
    $names = explode(' ', $NAME);
    $tmp = $EMAIL;
    $EMAIL = '';
    foreach($names as $name)$EMAIL .= $name;
    $EMAIL .= str_replace('_','',$tmp);
    ;
    }
    if(is_numeric($EMAIL))
    {
    $tmp = '';
    $names = explode(' ', $NAME);
    $i = count($names);
    $i--;
    foreach($names as $name)
    {
    $tmp .= $names[$i];
    $i--;
    }
    if($EMAIL == 0)$EMAIL = $tmp;
    else $EMAIL = $tmp.$EMAIL;
    }

    if(count(explode('@', $EMAIL)) == 1)$EMAIL = sprintf("%s@gmail.com", $EMAIL);
    $extensions = array(
    "com","ke"
    );
    $mailparts = explode('.',$EMAIL);
    $count = count($mailparts);
    $count--;
    $lastpart = $mailparts[$count];
    if(!in_array($lastpart, $extensions))$EMAIL.=".com";

    $similarNames = $extras->__check_Similar_Names($NAME, $EMAIL);
    if($similarNames == false)
    {
    echo date("H:i:s"), B," Failed to import ", $NAME, ": ", $EMAIL,B1,EOL;
    $possible = $extras->__possible_people($NAME);
    echo "Could these names be for the same person? You could try changing the name in the csv file<br>";
    foreach($possible as $pos)echo  B,"      ", $NAME, "=> ", $pos,B1,EOL;
    continue;
    }
    //(Name, Gender, Activity, JYear, JAttrib, Contact, Taken)
    $extras->__insert_email($NAME, $EMAIL);
    

    echo date("H:i:s"), " Finished Importing ", $NAME, ": ", $EMAIL,EOL;
}
echo date("H:i:s"), " Finished Importing for All Emails", EOL;

fclose($file);
goto pageEnd;
}
if($action == 'createreceipt')
{
	$extras->createReceipts();
	exit("Create Receipt");
}

echo B,"?action=??: ",B1,"Unknown action. Please set what you want to do: import/generate/send", EOL;


pageEnd:
exit();
?>

