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
if($action == "import")
{
echo date("H:i:s")," Starting Importer",EOL;
//$importObjCreator = new PHPExcel();
$filepath = "ExtraFiles/Emails.xlsx";
				//set up Cache
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

$importer = PHPExcel_IOFactory::load($filepath);
$sheetNames = Array(
			"Emails"
		);
$date = isset($_GET["date"])?stripslashes($_GET["date"]):false;
	if($date === false)
	{
		echo B,"Fatal : ",B1,"Date Not Set", EOL;
		goto pageEnd;
	}
$extras->__set_Date($date);
foreach($sheetNames as $sheetName)
{
	$year = $sheetName;
	$sheet = $importer->getSheetByName($sheetName);
	echo date("H:i:s"), " Importing " ,$sheetName,EOL;
	$row = 1;
	$col = 0;
	$requiredcols = array(
			"NAME"=>0,
			"EMAIL"=>0,
	);
	$colval = $sheet->getCellByColumnAndRow($col, $row)->getValue();
	
	$colscount = 0;
	while($colval != '')
	{
		if(isset($requiredcols[strtoupper($colval)])){$requiredcols[strtoupper($colval)] = $col; $colscount++;}
		$col++;
		$colval = $sheet->getCellByColumnAndRow($col, $row)->getValue();
	}
	
	if($colscount < 2)
	{
		echo date("H:i:s"), " Some columns missing from Excel Sheet (NAME, EMAIL) " ,EOL;
		echo date("H:i:s"), " Exiting... " ,EOL;
		exit();
	}

	echo date("H:i:s"), " Required Columns Found " ,EOL;
	$row++;
	$col = 0;
	foreach($requiredcols as $ca=>$rol)$$ca = '';			//create these vars
	//var_dump($requiredcols);exit();
	while($colval = $sheet->getCellByColumnAndRow(0, $row)->getValue() != '')//while still within range
	{
		foreach($requiredcols as $ca=>$rol)
		{
			$$ca = $sheet->getCellByColumnAndRow($rol, $row)->getValue();
			$$ca = addslashes($$ca);
			$str = sprintf("%s				\t\t\t%s", $NAME, $EMAIL);
		}
		/*
			Add missing parts to emails
		*/
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
			foreach($possible as $pos)echo  B,"      ", $NAME, "=> ", $pos,B1,EOL;
			$row++;continue;
		}
		//(Name, Gender, Activity, JYear, JAttrib, Contact, Taken)
		$extras->__insert_email($NAME, $EMAIL);
		$row++;

		echo date("H:i:s"), " Finished Importing ", $NAME, ": ", $EMAIL,EOL;
	}
	echo date("H:i:s"), " Finished Importing for All Emails", EOL;
	
	//echo $sheet->getCellByColumnAndRow($col, $row)->getValue(),EOL;//;->setCellValue("A1","Its Working");
}
goto pageEnd;
}

if($action == 'createreceipt')
{
	$extras->createReceipts();
	exit("Create Receipt");
}

echo B,"?action=??: ",B1,"Unknown action. Please set what you want to do: import/generate/send", EOL;



/*$famInit = new Family(0);
$famInit->__clear_Disfunct_Families();
$famInit->__reset_Family_Data();

for($i = 1; $i <= $num_families; $i++)
{
	$familyObject[$i] = new Family($i);
	$familyObject[$i]->__start_Family();
	$familyObject[$i]->__getDAD();
	$familyObject[$i]->__getMUM();
	//$familyObject[$i]->__getTeacher();
}

$i = 1;
while($familyObject[$i]->__getMember() == true)
{
	$i++;
	if($i > $num_families)$i = 1;
}
*/
pageEnd:
exit();
?>

