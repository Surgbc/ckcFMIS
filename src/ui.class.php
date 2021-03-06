<?php

class jkusdatr_ui extends CSYBER
{
 private $ui_uid;
 private $ui_grp;
 private $ui_name;
 private $client_device;
 
 public function __construct($auth)
 {
	$this->__what_client();
	$this->ui_uid =$auth->authuid;
	$this->ui_grp = $auth->__get_group();
	$this->ui_name = $auth->authname;
	
 }
 
 public function __get_page()
 {
	$sub = isset($_GET["sub"])?stripslashes($_GET["sub"]):"home";
	switch($sub)
	{
		case "home":
			$page = ($this->client_device == "comp")?file_get_contents("ui/carl/pages/home.php"):"...not comp...";
			break;
		case "loggedin"://ui/pages/loggedin.php
			$page = ($this->client_device == "comp")?file_get_contents("ui/carl/pages/loggedin.php"):"...not comp...";
			break;
		case "loggedout**":				/*obsolete*/
			$page = file_get_contents("ui/carl/loggedout.php");
			break;
		case "loginform":
			$page = file_get_contents("ui/pages/carl/loginform.php");
			break;
		case "receipts":
			$page = $this->__get_receipts_edit_form();
			break;
		case "addreceipts":
			$page = file_get_contents("ui/carl/pages/receipts/addreceipts.php");
			break;
		case "viewreceipts":
			$page = file_get_contents("ui/carl/pages/receipts/viewreceipts.php");
			break;
		case "viewreceiptsprint":
			$page = file_get_contents("ui/carl/pages/receipts/viewreceiptsprint.php");
			break;
		default:
			$page = $this->_get_all_pages();
			break;		
	}
	//echo $this->result;exit();
	$this->result = $page;
	
	return true;
 }
 
 private function __get_receipts_edit_form()
 {
	$ret = "";
	switch($this->ui_grp)
	{
		case "Treasury":
		case "Deaconry":
			$ret = file_get_contents("ui/carl/pages/receipts/adminreceipts.php");
			break;
		default:
			;
	}
	return $ret;
 }
 
 /*
  * Get and set the type of client
  * check. How to get the thing
  */
 private function __what_client()
 {
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$os = preg_match('/Linux/',$agent)?"comp":(preg_match('/Win/',$agent)?"comp":(preg_match('/Mac/',$agent)?"comp":"no comp"));
	
	$this->client_device = $os;
	
	return true;
 }
 /*
  * Get elements of array one at a time
  */
 private function _get_all_pages()
 {
	$ind = isset($_GET["ind"])?$_GET["ind"]:0;
	$comploggedoutsegments = array
	(
		"user"=>"GUEST",			//show that no user is logged in
		"page"=>file_get_contents("ui/carl/pages/loggedout.php"),
		"loginform"=>file_get_contents("ui/carl/pages/loginform.php"),
		"EOR"=>"EOR"			//end of parts
	);
	$comploggedinsegments = array
	(
		"user" => sprintf("%s:%s", $this->ui_name, $this->ui_grp),
		"page"=>file_get_contents("ui/carl/pages/loggedin.php"),
		"loginform"=>file_get_contents("ui/carl/pages/loginform.php"),
		"EOR"=>"EOR"			//end of parts
	);
	
	$arr_len = ($this->client_device == "comp")?(($this->ui_uid ==0)?count($comploggedoutsegments): count($comploggedinsegments)):"get no comp";
	
	$i = 0;
	$loggedoutid = "0";
	if($this->ui_uid == $loggedoutid)
	{
		foreach($comploggedoutsegments as $key=>$val)
		{
			$ret = $val;
			if($i == $ind)break;
			$i++;
		}
	}else 
		foreach($comploggedinsegments as $key=>$val)
		{
			$ret = $val;
			if($i == $ind)break;
			$i++;
		}
	/*for($i=0;$i<$ind && $i<$arr_len)
	{
		$reti = $comploggedoutsegments[$i];
		$reto = $comploggedinsegments[$i];
	}
	$ret = ($this->client_device == "comp")?(($this->ui_uid ==0)?$comploggedoutsegments[$ind]: $comploggedinsegments):"get no comp";
	//var_dump($ret);exit();
	return json_encode($ret);
	*/
	return $ret;
 }
 
 protected function error_codes($code)
 {
	$errors = array
	(
		7=>"Success!",
		100=>"Unknown receipt action",
		101=>"Data not submitted",
		102=>"Missing fields. Check Manual",
		103=>"Mysqli error",
		104=>"Requested data not found"
	);
	$ret = isset($errors[$code])?$errors[$code]:"Unknown error, no result. Please try again";
	return $ret;
 }
 
}
