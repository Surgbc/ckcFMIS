<?php
/*
 * Download statements from ckc
 * *get statement from local server
 * get totals(cash count)
 */
class jkusdatr_statement Extends JKUSDATREASURY
{

 public function __construct()
 {
	isset($_GET["action"])?$sts_action = stripslashes($_GET["action"]): $sts_action = "view";
	switch($sts_action)
	{
		case "download":
			$this->__download_statement();
			break;
		case "cashcount":
			$this->__cash_count();
			break;
		default:
			$this->result = 200;
			;
	}
	
	return;
 }
 
 private function __cash_count()
 {
	$from = isset($_GET["from"])?date(stripslashes($_GET["from"])):date("Y-m-d");
	$to = isset($_GET["to"])?date(stripslashes($_GET["to"])):date("Y-m-d");
	
	$this->__connect();
	$query = sprintf("SELECT Tithe, Combined, CampOffering, Building, Amt1, Amt2, Amt2, Amt4, Amt5, Amt6, Amt7 FROM receipts where CollectionDate >= '%s' AND CollectionDate <= '%s'", $from, $to);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return;}
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	
	$rows = array();
	while($row = mysqli_fetch_assoc($check))$rows[] = $row;
	
	$total = 0;
	foreach($rows as $entry)foreach($entry as $key=>$val)$total += $val;
	print_r($total);
	
 }
 
 private function __download_statement()
 {
	//echo file_get_contents("use\ckcoctweek2.htm");exit();//check delete this
	$from = isset($_GET["from"])?stripslashes($_GET["from"]):date("Y-m-d");
	$to = isset($_GET["to"])?stripslashes($_GET["to"]):date("Y-m-d");
	$data = sprintf("FDate=%s&TDate=%s&Search=Search", $from, $to);
	echo $data;
	$this->__ckc_login();													
	$this->result = $this->__ckc_statement_download($data);
	return true;
	//sample http://localhost/treasury/index.php?main=statement&action=download
	//sample http://localhost/treasury/index.php?main=statement&action=download&from=2014-09-27&to=2014-09-27
 }
 protected function error_codes($code)
 {
	$errors = array
	(
		7=>"Success!",
		200=>"Unknown statement action",
		101=>"Data not submitted",
		102=>"Missing fields. Check Manual",
		103=>"Mysqli error",
		104=>"Requested data not found"
	);
	return $errors[$code];
 }
 
}