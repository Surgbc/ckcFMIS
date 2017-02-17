<?php

/*
 * Connect to db_server
 * Disconnect  from db_server
 */
if(!DEFINED("CSYBER"))exit("Mysql backdoor!");

class Mysql
{
 protected $link;
 
 public function Mysql(){}
 
 protected function __connect()
 {
	$link = mysql_connect(SERVER,DBUSER,DBPASS); 
	if(!$link) return mysql_error(); 
	
	$this->link = $link;
	return $link;
 }
 
 public function __disconnect()
 {
	return mysql_close($this->link); 
 }
 
}

?>