<?php
/*
 * Login
 * Action 
 *	Require:
		User
 */
class jkusdatr_auth Extends JKUSDATREASURY
{

 protected $group;
 public $authname;
 public $authuid;
 
 public function __construct()
 {
	$this->__getuser();
	return;
 }
 
 public function __start()
 {
	isset($_GET["action"])?$sts_action = stripslashes($_GET["action"]): $sts_action = "login";
	switch($sts_action)
	{
		case "login":
			$this->__login();
			break;
		case "getuser":
			$this->__getuser();
			break;
		case "signup":
			$this->__signup();
			break;
		case "logout":
			$this->__logout();
			break;
		case "alter":
			$this->__alteruser();
			break;
		case "modify":					//get appropriate page
			$this->__modifyuser();
			break;
		case "promote":
			$this->__promoteuser();
			break;
		case "demote":
			$this->__demoteuser();
			break;
		case "drop":
			$this->__dropuser();
			break;
		case "modifyforms":
			$this->__modifyforms();
			break;
		case "getusers":
			$this->__getusers();
			break;
		default:
			$this->result = 200;
			;
	}
	return;
 }
 
 private function __login()
 {
	$vars	= $_GET; 					//check. change to $_POST?
	$submit = isset($vars["submit"])?true:false;
	if(!$submit){$this->result = 101; return 0;}
	
	$user = isset($vars["user"])?stripslashes($vars["user"]): false;
	$pass = isset($vars["pass"])?stripslashes($vars["pass"]): false;
	
	$fields = array("user", "pass"); 	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}
	
	
	$this->__connect();
	$user = $this->__encrypt($user);
	$pass = $this->__encrypt($pass);
	
	$query = sprintf("SELECT * FROM AUTH WHERE USER = '%s' AND PASS = '%s'", $user, $pass);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	$row = mysqli_fetch_assoc($check);
	
	$tmp = md5(sprintf("%s%s",time(), $user));
	setcookie("JKUSDAtr", $tmp);
	$_SESSION["SID"] = $tmp;
	$_SESSION["USER"] = $user;
	$this->__disconnect();
	$this->result = 7; return true;
	
 }
 /*
  * Users can also alter their own details: user, pass, name, group
  */
 private function __alteruser()
 {
	$user = $this->__is_logged_in();
	if($user === false)return false;
	$fields = array
	(
		"USER"=>isset($_GET["user"])?(($_GET["user"] != "")?$this->__encrypt(stripslashes($_GET["user"])):false):false,
		"PASS"=>isset($_GET["pass"])?(($_GET["pass"] != "")?$this->__encrypt(stripslashes($_GET["pass"])):false):false,
		"NAME"=>isset($_GET["name"])?(($_GET["name"] != "")?stripslashes($_GET["name"]):false):false
	);
	$queryfields = array();
	foreach($fields as $key=>$val)if($val === false)unset($fields[$key]);else $queryfields[] = sprintf("%s='%s'", $key, $val);
	if(count($queryfields) == 0){$this->result = 102; return 0;} 
	$query = sprintf("UPDATE AUTH SET %s WHERE USER = '%s'", implode(",", $queryfields), $user);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;}
	
	$this->result = 7;
	return true;
 }
 
 private function __is_logged_in()
 {
	$cookiesid = isset($_COOKIE["JKUSDAtr"])?stripslashes($_COOKIE["JKUSDAtr"]):false;
	$sessionsid = isset($_SESSION["SID"])?stripslashes($_SESSION["SID"]):false;
	if($cookiesid != $sessionsid || ($cookiesid === false)){$this->result = 105; return false;}
	return $_SESSION["USER"];
 }
 
 private function __logout()
 {
	setcookie("JKUSDAtr", "abcxyz", -3600);
	$_SESSION["SID"] = false;
	$_SESSION["USER"] = false;
	$this->result = 7;
	return true;
 }
 
 private function __getuser()
 {
	$this->group = 100;
	$this->authname = 0;
	$this->authuid = 0;
	$cookiesid = isset($_COOKIE["JKUSDAtr"])?stripslashes($_COOKIE["JKUSDAtr"]):false;
	$sessionsid = isset($_SESSION["SID"])?stripslashes($_SESSION["SID"]):false;
	//var_dump($_COOKIE); echo"<br><br>";echo $cookiesid; echo"<br><br>";echo $cookiesid; echo"<br><br>";var_dump($_SESSION);
	if($cookiesid != $sessionsid || ($cookiesid === false)){$this->result = 105; return 0;} 	

	$user = $_SESSION["USER"];
	
	$query = sprintf("SELECT * FROM AUTH WHERE USER = '%s'", $user);
	$this->__connect();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error. check change code
	$row = mysqli_fetch_assoc($check);
	$group = $this->__get_group($row["GROUPtr"]);
	$name = $row["NAME"];
	$this->group = $row["GROUPtr"];
	$this->result = sprintf("%s:%s", $name, $group);
	
	$this->authuid = $user;
	$this->authname = $name;
	
	return true;	
 }
 
 private function __promoteuser()
 {
	if($this->__permissions(0) == 0){$this->result = 106; return false;} //admin priv
	$vars = $_GET;
	$user = isset($vars["user"])?stripslashes($vars["user"]): false;
	$group = isset($vars["group"])?stripslashes($vars["group"]): 0;
	$fields = array("user", "group"); 	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}
	
	$query = sprintf("UPDATE AUTH SET GROUPtr = '%s' WHERE USER = '%s'", $group, $user);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;}
	
	$this->result = 7;
	return true;
 }
 
 private function __demoteuser()
 {
	if($this->__permissions(0) == 0){$this->result = 106; return false;} //admin priv
	$vars = $_GET;
	$user = isset($vars["user"])?stripslashes($vars["user"]): false;
	$group = isset($vars["group"])?stripslashes($vars["group"]): 1;
	$fields = array("user", "group"); 	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}
	
	$query = sprintf("UPDATE AUTH SET GROUPtr = '%s' WHERE USER = '%s'", $group, $user);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;}
	
	$this->result = 7;
	return true;
 }
 
 //admin priv
 private function __dropuser()
 {
	if($this->__permissions(0) == 0){$this->result = 106; return false;} //admin priv
	$vars = $_GET;
	$user = isset($vars["user"])?stripslashes($vars["user"]): false;
	$fields = array("user"); 	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}
	
	$query = sprintf("DELETE FROM AUTH WHERE USER = '%s'", $user);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;}
	
	$this->result = 7;
	return true;
 }
 
 /*
  *	Create first user
  * Log in
  * Require login superadmin to sign up other admins
  * Incomplete. Require permission
  */
 private function __signup()
 {
	if($this->__permissions(0) == 0){$this->result = 106; return false;} //admin priv
	$vars	= $_GET; 					//check. change to $_POST?
	$submit = isset($vars["submit"])?true:false;
	if(!$submit){$this->result = 101; return 0;}
	
	$user = isset($vars["user"])?stripslashes($vars["user"]): false;
	$pass = isset($vars["pass"])?stripslashes($vars["pass"]): false;
	$group = isset($vars["group"])?stripslashes($vars["group"]): 5;
	$name = isset($vars["name"])?stripslashes($vars["name"]): false;
	
	$fields = array("user", "pass", "group", "name"); 	
	foreach($fields as $key=>$field)if($$field === false || $$field ==""){$this->result = 102; return 0;}
	
	$this->__connect();
	$user = $this->__encrypt($user);
	$pass = $this->__encrypt($pass);
	
	$query = sprintf("INSERT INTO AUTH (USER, PASS, NAME, GROUPtr) VALUES('%s','%s','%s','%s')", $user, $pass, $name, $group);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	$this->__disconnect();
	$this->result = 7; return true;
	
 }
 
 private function __encrypt($val)
 {
	$salt = AUTH_SALT;
	$val = $val.$salt.$val;
	return md5($val);
 }
 
 public function __get_group($gid = false)
 {
	if($gid == false)$gid = $this->group;
	$groups = array
	(
		0=>"Treasury",
		1=>"Deaconry",
		5=>"Guest",
		100=>"none"
	);
	
	return $groups[$gid];
 }
 
 private function __modifyuser()
 {
	$group = $this->group;
	switch($group)
	{
		case 0:
			$ret = file_get_contents("ui/carl/pages/admin_modify.php");
				//can drop, sign up new, promote or alter new.
			break;
		case 1:
			$ret = file_get_contents("ui/carl/pages/deaconry_modify.php");
			//can only alter self
			break;
		default:
			$ret = file_get_contents("ui/carl/pages/guest_modify.php");
			//cannot do a thing
			break;
	}
	$this->result = $ret;
	return true;
 }
 
 private function __modifyforms()
 {
	$sub = isset($_GET["sub"])?stripslashes($_GET["sub"]):"none";
	switch($sub)
	{
		case "signup":
			$ret = file_get_contents("ui/carl/pages/modifyforms/modifysignup.php");
			break;
		case "alterself":
			$ret = file_get_contents("ui/carl/pages/modifyforms/alterself.php");
			break;
		case "modifyedit":
			$ret = file_get_contents("ui/carl/pages/modifyforms/modifyedit.php");
			break;	
		default:
			;
	}
	$this->result = $ret;
	return true;
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
		104=>"Wrong Username and Password Combination",
		105=>"Session Ended. User is logged out",
		106=>"Insufficient priviledges"
	);
	return $errors[$code];
 }
 
 private function __getusers()
 {
	$this->__connect();
	
	$query = "SELECT * FROM auth WHERE 1";
	$check = mysqli_query($this->link, $query);
	//assuming data exists and there is no error;
	//if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	$users = array();
	$groups = array
	(
		0=>"Treasury",
		1=>"Deaconry",
		5=>"Guest"
	);
	while($row = mysqli_fetch_assoc($check))if($row["USER"] != $_SESSION["USER"]) $users[$row["USER"]] = array("name"=>$row["NAME"], "group"=>$groups[$row["GROUPtr"]]);
	$this->result = json_encode($users);
	$this->__disconnect();
	return true;
 }
 
}

/*
 *
 *http://localhost/treasury/?main=auth&submit=submit&user=0775985734&pass=pass&name=Admin%20Brian&group=0&action=login
 *http://localhost/treasury/?main=auth&submit=submit&action=getuser
 *http://localhost/treasury/?main=auth&submit=submit&action=logout
 *http://localhost/treasury/?main=auth&submit=submit&user=admin&pass=pass&name=Admin%20Brian&group=0&action=alter
 *http://localhost/treasury/?main=auth&submit=submit&action=promote&group=1&user=67c74ddb3c9df824c406f68b84d2252a
 *http://localhost/treasury/?main=auth&submit=submit&action=drop&user=brian
 */
