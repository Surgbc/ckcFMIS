<?php
/*
 *	Root Csearch Functions
 */
if(!DEFINED("CSYBER"))exit("csyber backdoor!");

class CSYBER
{
 /*
  * Carries the Facebook object after authentification so that authentification is not done from here
  */
 protected $fb;
/*
 * the user data as returned by $fb->api(/$user);
 */
 protected $user;
/*
 * userid extracted from $user
 */
 protected $userid;
/*
 *Mysql connection
 */
 protected $link;
/*
 *	Stores errors encountered in request
 */
 protected $errors = array();
/*
 *	Results of the request
 *  Includes also errors, if any
 */
 protected $result;
/*
 * Do nothing when this object is created
 */
 public function __construct()
 {
	;
 }
 

/*
 *  Including files
 *	Redundant function. See: class csearchFileLoader
 */
 public function __LoadFile($files)
 {
	!is_array($files)?$files = Array($files):false;
	
	foreach($files as $file) include $file;
 }
/*
 * set authenticated facebook object for this $this
 */
 public function setFacebook($fb)
 {
	$this->fb = $fb;
 }
/*
 * Get user data from $facebook to $this
 */
 public function __loadUser()
 {
	$facebook = $this->fb;
	$this->user = $facebook->api("/me", "GET");
	$this->userid = $facebook->getUser();
	return;
 }
/*
 * Get any errors in requesting url{missing fields, etc}
 */
 protected function __csystemErrors($fields, $error)
 {
	switch($error)
	{
		case 'null':
			$retNull = array();
			foreach($fields as $identifier=>$therest)
			{
				if($therest == '' && $therest !== 0)$retNull[] = $identifier;
			}
			count($retNull)>0?$ret = array("error"=>array("Missing Fields"=>$retNull)): $ret = null;
			break;
		case 'numeric':
			$retNull = array();
			foreach($fields as $identifier=>$therest)
			{
				if(!is_Numeric($therest))$retNull[] = $identifier;
			}
			count($retNull)>0?$ret = array("error"=>array("Not Numeric"=>$retNull)): $ret = null;
			break;
		default:
			$ret = null;
	}
	return $ret;
 }
 
 protected function __errorExtend($ca, $rol)
 {
	if(!is_array($ca))$ca = array();
	if(!is_array($rol))$rol = array();
	
	$ret = array_merge($ca, $rol);
	return $ret;
 }
/*
 * Make a Mysql connection
 * Requires SERVER, DBUSER, DBPASS
 */
 protected function __connect()
 {
	$link = mysqli_connect(SERVER,DBUSER,DBPASS); 
	if(!$link) return mysqli_error(); 
	
	$this->link = $link;
	mysqli_select_db($link, CSYBER_DB);
	return $link;
 }
/*
 * close Mysql connection
 */
 public function __disconnect()
 {
	return mysqli_close($this->link); 
 }
 /*
  * Output from $this
  */
 public function __Output()
 {
	if($this->errors != null) return $this->errors;
	if($this->output == null)$this->output = array();
	return $this->output;
 }
 
 protected function __getField($field, $default = '')
 {
	isset($_GET[$field])?$default = strtoLower(addslashes ($_GET[$field])): false;
	return $default;
 }	
 /*
  *Return false is user is not page admin of $pid
  */
 private function __isPageAdmin($pid)
 {
	$facebook = $this->fb;
	$isAdmin = $facebook->api(array(
			"method"=>"fql.query",
			"query"=>"SELECT name FROM page WHERE page_id IN (SELECT page_id FROM page_admin WHERE uid=me()) AND page_id = $pid" 
	));
	if(is_array($isAdmin)){if(count($isAdmin) == 0)$isAdmin = false;
							else $isAdmin = true;}
		else if($is_admin == false || $is_admin == null)$is_admin = false;
			else $is_admin = true;
	return $isAdmin;
 }
 
 protected function __isAdmin()
 {
	/*
	$this->__loadUser();
	if(is_array($this->user))$userid = $this->user["id"];
	else $userid = $this->userid;
	/**/
	$userid="1516860866";
	return config::__isAdmin($userid);
	
 }
 /*
  * $this->group is in child class
  * required $gid should be greater than or equal to $this->group
  */
 protected function __permissions($gid)
 {
	//echo "$gid compare :".$this->group;
	if($gid < $this->group)return 0;
	return true;
 }
 
 public function __process_request()
 {
	if(is_numeric($this->result))
	{
		$error = $this->error_codes($this->result);
		$ret = sprintf("%s:%s", $this->result, $error);
	}else $ret = $this->result;
	return $ret;
 }
 
}

?>