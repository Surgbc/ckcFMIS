<?php
/*
 * Manual
 */
class jkusdatr_manual Extends JKUSDATREASURY
{

 public function __construct()
 {
	isset($_GET["part"])?$part = stripslashes($_GET["part"]): $part = "part";
	switch($part)
	{
		case "receipt":
			$this->__receipt_man();
			break;
		case "statement":
			$this->__statement_man();
			break;
		default:
			$this->__sys_man();
			;
	}
	
	return;
 }
 
 private function __receipt_man()
 {
	$calls = array
	(
		""
	);
	$error_codes = array
	(
		7=>"Success!",
		100=>"Unknown receipt action",
		101=>"Data not submitted",
		102=>"Missing fields. Check Manual",
		103=>"Mysqli error",
		104=>"Requested data not found"
	);
 }
 
 private function __statement_man()
 {
 
 }
 
 private function __sys_man()
 {
 
 }
 protected function error_codes($code)
 {
	return 7;
 }
 
}