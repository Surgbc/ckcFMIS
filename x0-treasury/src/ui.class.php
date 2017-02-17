<?php

class jkusdatr_ui extends CSYBER
{
 public function __construct()
 {
	$sub = isset($_GET["sub"])?stripslashes($_GET["sub"]):"all";
	
	switch($sub)
	{
		case "all":
			$page = file_get_contents("ui/home.php");
			break;
		case "loggedin":
			$page = file_get_contents("ui/loggedin.php");
			break;
		case "loggedout":	
			$page = file_get_contents("ui/loggedout.php");
			break;
		case "loginform":
			$page = file_get_contents("ui/loginform.php");
			break;
		default:
			break;		
	}
	$this->result = $page;
 }
}