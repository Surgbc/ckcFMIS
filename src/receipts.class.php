<?php
/*
 * Class:	jkusdatr_receipts
 * Author:	Brian Onang'o
 * Date:	Sep/Oct 2014
 *....................................................................................
 * Requires: JKUSDATREASURY
 *....................................................................................
 * Purpose: Working with receipt objects in the JKUSDA financial management system
 * Functions:
 * 		Adding receipts
 *		Printing receipts
 *		Viewing receipts
 *		Deleting receipts
 *		uploading receipts
 *....................................................................................
 * Request format
 * 	?main=receipts&action=action&...
 *....................................................................................
 * Returned object:
 *		html string. Numeric for error codes or success codes
 *		codes:
 *			7 success
 *			Error codes:
 *				
 */
class jkusdatr_receipts Extends JKUSDATREASURY
{
 /*
  * choose the function depending on the value of $_GET["action"]
  */
 private $auth;
 
 public function __construct($auth)
 {
	$this->auth = $auth;
	isset($_GET["action"])?$receipt_action = addslashes($_GET["action"]): $recipt_action = "view";
	
	switch($receipt_action)
	{
		case "totals":
			$this->__get_date_totals();
			break;
		case "add":
			$this->__add_receipt();
			break;
		case "delete":
			$this->__delete_receipt();
			break;
		case "newdelete":
			$this->__new_delete_receipt();
			break;
		case "view":
			$this->__view_formatted_receipt();
			break;
		case "viewimg":
			$this->__view_receipt_img();
			break;
		case "viewimgall":
			$this->__img_generate_all();
			break;
		
		case "newview":
			$this->__new_view_formatted_receipt();
			break;
		case "print":
			$this->__print_receipt();
			break;
		case "upload":
			$this->__upload_receipt();
			break;
		case "download":
			$this->__download_receipts();
			break;
		case "create":
			$this->__create_receipt();
			break;
		case "email":
			$this->__email_receipt();
			break;
		default:
			$this->result = 100;
			;
	}
	
	return;
 }
 /*
  * Requires fields:
  * 	submit=submit&name=name
  *		default date is todate
  *		add receipts to either receipts table or downloadedreceipts depending on the presence of $_GET["Ind"].
  *			if isset $_GET["Ind"], add to downloadedreceipts otherwise add to receipts
  */
 
 private function __get_date_totals()
 {
	$fields = array("date");
	$date	= isset($_GET["date"])?addslashes($_GET["date"]): (isset($_GET["ndate"])?addslashes($_GET["ndate"]):false);
	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}	 //error.missing fields
	
	if(isset($_GET["date"]))$table = "receipts"; else $table = "downloadedreceipts";
	
	$this->__connect();
	$query = sprintf("SELECT * FROM %s WHERE CollectionDate ='%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	$total = 0;
	while($row = mysqli_fetch_assoc($check))
	{
		foreach($row as $a=>$b)
		{
			if(is_numeric($b) && $a != "Ind" && $a != "Uploaded")
			{
				$total += $b;
			}
		}
	}
	
	$total = "total:".$total;
	$this->result = $total;
	return true;
 }
 private function __add_receipt()
 {
	$vars	= $_GET; //check. change to $_POST
	foreach($vars as $ca=>$rol)$vars[strtolower($ca)] = $rol;
	//$var contains $_GET vars with lower case keys
	
	//array containing field_names_in_the_tables and the corresponding variable names inserted into the_table_fields
	$fields = array("Name"=>"name",
					"CollectionDate"=>"date",
					"Tithe"=>"tithe",
					"Combined"=>"combined",
					"CampOffering"=>"camp",
					"Uns1"=>"uns1",
					"Uns2"=>"uns2",
					"Uns3"=>"uns3",
					"Uns4"=>"uns4",
					"Uns5"=>"uns5",
					"Uns6"=>"uns6",
					"Uns7"=>"uns7",
					"Amt1"=>"amt1",
					"Amt2"=>"amt2",
					"Amt3"=>"amt3",
					"Amt4"=>"amt4",
					"Amt5"=>"amt5",
					"Amt6"=>"amt6",
					"Amt7"=>"amt7",
					"Building"=>"devt",
					"Email"=>"email"
					);	
					
	//assign to each variable given in $field its value from $vars or assign it a defaulr value if its not contained in $vars
	//fatal: missing $vars["Submit"]
	foreach($vars as $ca=>$rol)if($rol == '')unset($vars[$ca]);
	$submit = isset($vars["submit"])?true:false;
	if(!$submit){$this->result = 101; return;}				//error
	$date	= isset($vars["date"])?addslashes($vars["date"]):date("Y-m-d");
	$name	= isset($vars["name"])?addslashes($vars["name"]):false;
	$tithe	= isset($vars["tithe"])?addslashes($vars["tithe"]):0;
	$combined= isset($vars["combined"])?addslashes($vars["combined"]):0;
	$camp	= isset($vars["camp"])?addslashes($vars["camp"]):0;
	$devt	= isset($vars["devt"])?addslashes($vars["devt"]):0;
	$uns1	= isset($vars["uns1"])?addslashes($vars["uns1"]):"";
	$amt1	= isset($vars["amt1"])?addslashes($vars["amt1"]):0;
	$uns2	= isset($vars["uns2"])?addslashes($vars["uns2"]):"";
	$amt2	= isset($vars["amt2"])?addslashes($vars["amt2"]):0;
	$uns3	= isset($vars["uns3"])?addslashes($vars["uns3"]):"";
	$amt3	= isset($vars["amt3"])?addslashes($vars["amt3"]):0;
	$uns4	= isset($vars["uns4"])?addslashes($vars["uns4"]):"";
	$amt4	= isset($vars["amt4"])?addslashes($vars["amt4"]):0;
	$uns5	= isset($vars["uns5"])?addslashes($vars["uns5"]):"";
	$amt5	= isset($vars["amt5"])?addslashes($vars["amt5"]):0;
	$uns6	= isset($vars["uns6"])?addslashes($vars["uns6"]):"";
	$amt6	= isset($vars["amt6"])?addslashes($vars["amt6"]):0;
	$uns7	= isset($vars["uns7"])?addslashes($vars["uns7"]):"";
	$amt7	= isset($vars["amt7"])?addslashes($vars["amt7"]):0;
        $email = '';
	//assign to field_values[] the values of the variables in the order in which the variables appear in $fields
	//fatal: when value === false
	$field_values = array();
	foreach($fields as $key=>$field)
	{
		#echo "$key $field<br>";
		if($$field === false){$this->result = 102; return;}	//fatal
		$$field = str_replace(",", "", $$field);			//Remove commas from values. Important for numeric values such as 1,300
		$field_values[] = $$field;
	}
	
	$i = 0;
	foreach($fields as $key=>$val){if($val == "combined"){$field_values[$i] = /* 2**/ $field_values[$i]; }$i++;}//check double
	//Fields and values as they should appear in INSERT query
	$query_fields = implode(",", array_keys($fields));
	var_dump($field_values);
	$query_values = "'".implode("','", $field_values)."'";
	
	//Decide which table (and query) to use depending on $_GET["Ind"].
	//If isset($_GET["Ind"]) then insert also Ind into Ind
	if(isset($_GET["Ind"]))
	{
		//goto add_downloaded_receipt;
		$ind	= isset($vars["ind"])?addslashes($vars["ind"]):0;
		$table = "downloadedreceipts";
		$query_fields = "Ind,".$query_fields;
		$query_values = "$ind,".$query_values;
	}
	else
	{
		$table = "receipts";	
	}
	$query = sprintf("INSERT INTO %s (%s) VALUES(%s)", $table, $query_fields, $query_values);
	echo $query;
	//database connection
	$this->__connect();
	mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){echo mysqli_error($this->link);$this->result = 103; return;}		//fatal
	$this->__disconnect();
	
	$this->result = 7;
	if(isset($_GET["iframe"]))header("location:index.php?main=page&sub=addreceipts");	//reload page containing form if loaded from iframe
	return true;
	//sample:http://localhost/csyber/finance/jkusdatr/?main=receipts&action=add&submit=submit&name=Carol%20Kade&tithe=700
 }
 
 /*
  * Delete from receipts. Cannot and should not delete from downloadedreceipts
  * Require:
  *  Ind, Submit=Submit
  * ?main=receipts&action=delete&id=7
  */
 private function __delete_receipt()
 {
	$vars	= $_GET; //check. change to $_POST?
	$fields = array("Ind"=>"id");
	
	$submit = isset($vars["submit"])?true:false;
	if(!$submit){$this->result = 101; return;}				//error
	$id	= isset($vars["id"])?addslashes($vars["id"]):false;
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return;} //error.missing fields
	
	$query = sprintf("DELETE FROM receipts WHERE Ind ='%s'", $id);
	$this->__connect();
	mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return;} // error
	$this->__disconnect();
	
	$this->result = 7;
	return true;
	//sample http://localhost/csyber/finance/jkusdatr/?main=receipts&action=delete&id=7
 }
 
 private function __new_delete_receipt()
 {
	$vars	= $_GET;
	$fields = array("ind", "date");	
	
	$date = isset($vars["date"])?addslashes($vars["date"]):false;
	$ind = isset($vars["ind"])?addslashes($vars["ind"]):false;
	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}	 //error.missing fields
	
	$table = "receipts";
	
	$this->__connect();
	$query = sprintf("SELECT Ind FROM %s WHERE CollectionDate ='%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	
	$i = 0;
	while($i <= $ind && $row = mysqli_fetch_assoc($check)){$row = $row["Ind"];$tmp = $row;$i++;}
	$id = $tmp;
	$_GET["id"] = $id;
	$this->__delete_receipt();
	$this->result = 7;
	return true;
	//sample http://localhost/csyber/finance/jkusdatr/?main=receipts&action=delete&id=7
 }
 /*
  * Return receipt(data + format)
  * Require:
  *		Ind for accessing receipt from receipt
  *			or
  *		Id	for accessing receipt from downloadedreceipts
  */
 private function __view_formatted_receipt()
 {
	$ret = $this->__view_receipt();
	if(is_numeric($ret))return;
	$addedscripts = "<script type=\"text/javascript\" src=\"ui/carl/scripts/latemodifyscripts.js\"></script>"; //check. What is this doing here
	$this->result = $this->__receipt_head_html().$ret. $this->__receipt_foot_html();
	return true;
 }
 
 private function __email_receipt()
 {
	$fields = array("date");
	$date	= isset($_GET["date"])?addslashes($_GET["date"]): (isset($_GET["ndate"])?addslashes($_GET["ndate"]):false);
	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}	 //error.missing fields
	
	if(isset($_GET["date"]))$table = "receipts"; else $table = "downloadedreceipts";
	
	$this->__connect();
	//$fid = $this->__get_first_id($date, $table);
	isset($_GET["start"])?$fid = addslashes($_GET["start"]):$fid = $this->__get_first_id($date, $table);
	isset($_GET["range"])?$lid = $fid -1 + addslashes($_GET["range"]):$lid = $this->__get_last_id($date, $table);
	
	if($fid === false || $lid === false)return false;
	
	$range = $lid - $fid;
	
	$query = sprintf("SELECT Ind, Name, Email FROM %s WHERE EMAILSENT='F' AND EMAIL !='' AND Ind >= '%s' AND Ind <= '%s'", $table, $fid, $lid);
	$this->__connect();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	while($row = mysqli_fetch_assoc($check))
	{
		$id = $row["Ind"];
		$email = $row["Email"];
		$name = $row["Name"];
		//var_dump($row);
		$this->__send_mail($id, $email, $name);
		
		$query = sprintf("Update %s SET EMAILSENT = 'T' WHERE Ind = '%s' ", $table, $id);
		$update = mysqli_query($this->link, $query);
		echo "Email sent to ", $name, " at ", $email,EOL;
		//echo $query,EOL;
		//exit();
	}
	return true;
	//sample http://localhost/treasury/index.php?main=receipts&action=print&ndate=2014-09-27&submit=submit&range=10&start=26001
	//sample http://localhost/treasury/index.php?main=receipts&action=print&date=2014-09-25&submit=submit&range=10
 } 
 
 private function __send_mail($id, $email, $name)
 {

	include "src/swift/swift_required.php";
	$message = Swift_Message::newInstance()
		->setSubject('Receipt')
		->setFrom(array(FROMEMAIL => FROMNAME))
		->setReplyTo(array(REPLYEMAIL => REPLYNAME))
		->setTo(array($email => $name));
	$message->setBody(
	'<html>' .
	' <head></head>' .
	' <body>'.
	/*'We are sorry about the problem that occured with the system when generating the receipts we sent you earlier. If failed to reflect the half of the combined offerings that go to CKC. But we have now rectified this error. We appreciate those who noticed this and brought it to our attention.'.*/
	'<br><img src="' . $message->embed(Swift_Image::fromPath(RECEIPTSFILE.$id.".png")) .'" alt="Image" />' .
	'<br>Please download your receipt below if it doesn\'t show up up here' .
	' </body>' .
	'</html>',
	'text/html' // Mark the content-type as HTML
	);
	$message->attach(Swift_Attachment::fromPath(RECEIPTSFILE.$id.".png"));
	//echo $message->tostring();
	$transport = Swift_SmtpTransport::newInstance(MAILSERVER, MAILSERVERPORT, MAILSERVERENCRYPTION)
	#$transport = Swift_SmtpTransport::newInstance('aspmx.l.google.com', 25)
	#$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls')
	//$transport = Swift_SendMailTransport::newInstance('C:\xampp\sendmail\sendmail.exe -t');
	->setUsername(MAILSERVERUNAME)
	->setPassword(MAILSERVERPASS);
	$mailer = Swift_Mailer::newInstance($transport);
	//echo "brian";
	$result = $mailer->send($message);
	//exit($result."Brian 2");
	return true;
 }
 
 private function __print_receipt()
 {
	$fields = array("date");
	$date	= isset($_GET["date"])?addslashes($_GET["date"]): (isset($_GET["ndate"])?addslashes($_GET["ndate"]):false);
	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}	 //error.missing fields
	
	if(isset($_GET["date"]))$table = "receipts"; else $table = "downloadedreceipts";
	
	$this->__connect();
	//$fid = $this->__get_first_id($date, $table);
	isset($_GET["start"])?$fid = addslashes($_GET["start"]):$fid = $this->__get_first_id($date, $table);
	isset($_GET["range"])?$lid = $fid -1 + addslashes($_GET["range"]):$lid = $this->__get_last_id($date, $table);
	if($fid === false || $lid === false)return false;
	
	$range = $lid - $fid;
	
	$receipts = array();
	$count = 0;
	for($id = $fid; $id <= $lid; $id++)
	{
		isset($_GET["date"])?$_GET["ind"] = $id: $_GET["id"] = $id;;
		(($tmp = $this->__view_unemailed_receipt()) != null && !is_numeric($tmp))?$receipts[] = sprintf("<td id=\"itd\">%s</td>",$tmp): $lid++;
		
		$count++;
		if($count > 3*$range)break;
	}
	$count = 0;
	$trs = array();
	foreach($receipts as $receipt)
	{
		($count%2 == 0)? $trs[] = sprintf("<tr id=\"itr\">%s", $receipt):$trs[] = sprintf("%s</tr>", $receipt);
		$count++;
	}
	if(is_numeric($this->result) && $this->__fatal__($this->result) == true)return false; //if an error occurs (above:in functions that are called)
	$receipthead = $this->__receipt_head_html();
	$receiptfoot = $this->__receipt_foot_html();
	
	$receipts = $receipthead;
	//var_dump($trs);exit();
	//$interval = 196.6;
	$interval = 398.2;
	$top = 0;
	$trslen = count($trs);	//trs are all the receipts loaded
	$page = 0;
	for($i=0; $i < $trslen; $page++, $i+=12)	//after every 12 receipts, we go to another page
	{
		$tmp = array(); 
		$ii = $i+12;
		for($j = $i; $j < $ii; $j++)$tmp[] =  isset($trs[$j])?$trs[$j]:"";
		//var_dump($tmp);exit();
		$ret = join("", $tmp);
		$top = $interval * $page;
		$receipts .= sprintf("
				<table id=\"receipttable\" style=\"top:%smm;\">
				%s
				</table>
				", $top, $ret);	
				//echo $top."<br><br>";
		//var_dump($receipts);exit();
		
	}
	$print_js = "<script type=\"text/javascript\">window.print();</script>";
	$receipts .= $print_js. $receiptfoot;
	//$this->__disconnect();
	$this->result = $receipts;
	return $receipts;
	//sample http://localhost/treasury/index.php?main=receipts&action=print&ndate=2014-09-27&submit=submit&range=10&start=26001
	//sample http://localhost/treasury/index.php?main=receipts&action=print&date=2014-09-25&submit=submit&range=10
 }
 
 private function __view_receipt_img()
 {
	$ret = $this->__view_receipt(true);
	//_img_receipt_format
	//var_dump( $ret);exit();
	/*
	if(is_numeric($ret))return;
	$addedscripts = "<script type=\"text/javascript\" src=\"ui/carl/scripts/latemodifyscripts.js\"></script>"; //check. What is this doing here
	$this->result = $this->__receipt_head_html().$ret. $this->__receipt_foot_html();
	*/
	return true;
 
 }
 
 private function __new_view_formatted_receipt()
 {
	$vars	= $_GET;
	$fields = array("date", "ind");	
	
	$date = isset($vars["date"])?addslashes($vars["date"]):false;
	$ind = isset($vars["ind"])?addslashes($vars["ind"]):(isset($vars["id"])?addslashes($vars["id"]):false);
	
	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}	 //error.missing fields
	
	if(isset($vars["ind"]))$table = "receipts";else $table = "downloadedreceipts";
	
	$this->__connect();
	$query = sprintf("SELECT * FROM %s WHERE CollectionDate ='%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	
	$i = 0;
	while($i <= $ind && $row = mysqli_fetch_assoc($check)){$row = $row["Ind"];$tmp = $row;$i++;}
	$this->__disconnect();
	
	if(isset($vars["ind"]))$_GET["ind"] = $tmp;else $_GET["id"] = $tmp;
	
	$ret = $this->__view_formatted_receipt();
	return true;
 }
 /*
  * Fetch receipt data from appropriate table depending on which of $_GET["ind"] or $_GET["id"] is set
  */
 private function __view_unemailed_receipt()
 {
	$vars	= $_GET; //check. change to $_POST?
	$submit = isset($vars["submit"])?true:false;
	if(!$submit){$this->result = 101; return 0;}		

	$id	= isset($vars["id"])?addslashes($vars["id"]): (isset($vars["ind"])?addslashes($vars["ind"]):false);
	if($id === false){$this->result = 102; return 0;} //error.missing fields
	if(isset($vars["ind"]))$table = "receipts";else $table = "downloadedreceipts";
	$query = sprintf("SELECT * FROM %s WHERE Ind ='%s' AND EMAILSENT='F'", $table, $id);
	
	$this->__connect();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	$row = mysqli_fetch_assoc($check);
	$this->__disconnect();
	$ret = $this->_single_recept_format($row, $table);
	return $ret;	
	//sample http://localhost/csyber/finance/jkusdatr/?main=receipts&action=view&submit=submit&id=11
 }
 private function __view_receipt($raw=null)
 {
	$vars	= $_GET; //check. change to $_POST?
	$submit = isset($vars["submit"])?true:false;
	if(!$submit){$this->result = 101; return 0;}		

	$id	= isset($vars["id"])?addslashes($vars["id"]): (isset($vars["ind"])?addslashes($vars["ind"]):false);
	if($id === false){$this->result = 102; return 0;} //error.missing fields
	if(isset($vars["ind"]))$table = "receipts";else $table = "downloadedreceipts";
	$query = sprintf("SELECT * FROM %s WHERE Ind ='%s'", $table, $id);
	$this->__connect();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	$row = mysqli_fetch_assoc($check);
	$this->__disconnect();
	if($raw != null)$ret = $this->_img_receipt_format($row, $table);
	else $ret = $this->_single_recept_format($row, $table);
	return $ret;	
	//sample http://localhost/csyber/finance/jkusdatr/?main=receipts&action=view&submit=submit&id=11
 }
 
 private function __get_first_id($date, $table)
 {
	$query = sprintf("SELECT Ind FROM %s WHERE CollectionDate ='%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	
	$count = 0;
	$prev = 0;
	
	while($row = mysqli_fetch_assoc($check))
	{
		$row = $row["Ind"];
		if($count == 0)$prev = $row;
		else
		{
			if($row < $prev)$prev = $row;
		}
		$count++;
	}
	return $prev;
 }
 
  private function __get_last_id($date, $table)
 {
	$query = sprintf("SELECT Ind FROM %s WHERE CollectionDate ='%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	$count = 0;
	while($row = mysqli_fetch_assoc($check))
	{
		$row = $row["Ind"];
		if($count == 0)$prev = $row;
		else
		{
			if($row > $prev)$prev = $row;
		}
		$count++;
	}
	return $prev;
 }
 /*
  * Require:
  *		date||ndate. receipts start at first id for the given date. date:receipts, ndate: downloadedreceipts
  * 	start: if this is given, then the start id for the given date is overriden
  * 
  */
 private function __img_generate_all()
 {
	$fields = array("date");
	$date	= isset($_GET["date"])?addslashes($_GET["date"]): (isset($_GET["ndate"])?addslashes($_GET["ndate"]):false);
	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}	 //error.missing fields
	
	if(isset($_GET["date"]))$table = "receipts"; else $table = "downloadedreceipts";
	
	$this->__connect();
	//$fid = $this->__get_first_id($date, $table);
	isset($_GET["start"])?$fid = addslashes($_GET["start"]):$fid = $this->__get_first_id($date, $table);
	isset($_GET["range"])?$lid = $fid -1 + addslashes($_GET["range"]):$lid = $this->__get_last_id($date, $table);
	if($fid === false || $lid === false)return false;
	
	$range = $lid - $fid;
	
	$receipts = array();
	$count = 0;
	for($id = $fid; $id <= $lid; $id++)
	{
		isset($_GET["date"])?$_GET["ind"] = $id: $_GET["id"] = $id;;
		#(($tmp = $this->__view_receipt()) != null && !is_numeric($tmp))?$receipts[] = sprintf("<td id=\"itd\">%s</td>",$tmp): $lid++;
		$this->__view_receipt(true);
		$count++;
		if($count > 3*$range)break;
	}
	return true;
	//sample http://localhost/treasury/index.php?main=receipts&action=print&ndate=2014-09-27&submit=submit&range=10&start=26001
	//sample http://localhost/treasury/index.php?main=receipts&action=print&date=2014-09-25&submit=submit&range=10
 }
 
 public function __create_receipt()
 {
	$fields = array("date");
	$date	= isset($_GET["date"])?addslashes($_GET["date"]): (isset($_GET["ndate"])?addslashes($_GET["ndate"]):false);
	
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}	 //error.missing fields
	
	if(isset($_GET["date"]))$table = "receipts"; else $table = "downloadedreceipts";
	
	$this->__connect();
	//$fid = $this->__get_first_id($date, $table);
	isset($_GET["start"])?$fid = addslashes($_GET["start"]):$fid = $this->__get_first_id($date, $table);
	isset($_GET["range"])?$lid = $fid -1 + addslashes($_GET["range"]):$lid = $this->__get_last_id($date, $table);
	if($fid === false || $lid === false)return false;
	
	$range = $lid - $fid;
	
	$receipts = array();
	$count = 0;
	for($id = $fid; $id <= $lid; $id++)
	{
		isset($_GET["date"])?$_GET["ind"] = $id: $_GET["id"] = $id;;
		(($tmp = $this->__view_receipt()) != null && !is_numeric($tmp))?$receipts[] = sprintf("<td id=\"itd\">%s</td>",$tmp): $lid++;
		
		$count++;
		if($count > 3*$range)break;
	}
	exit($receipts[0]);
	$count = 0;
	$trs = array();
	foreach($receipts as $receipt)
	{
		($count%2 == 0)? $trs[] = sprintf("<tr id=\"itr\">%s", $receipt):$trs[] = sprintf("%s</tr>", $receipt);
		$count++;
	}
	if(is_numeric($this->result) && $this->__fatal__($this->result) == true)return false; //if an error occurs (above:in functions that are called)
	$receipthead = $this->__receipt_head_html();
	$receiptfoot = $this->__receipt_foot_html();
	
	//$receipts = $receipthead;
	$receipts = ""; 
	//var_dump($trs);exit();
	//$interval = 196.6;
	$interval = 398.2;
	$top = 0;
	$trslen = count($trs);	//trs are all the receipts loaded
	$page = 0;
	for($i=0; $i < $trslen; $page++, $i+=12)	//after every 12 receipts, we go to another page
	{
		$tmp = array(); 
		$ii = $i+12;
		for($j = $i; $j < $ii; $j++)$tmp[] =  isset($trs[$j])?$trs[$j]:"";
		//var_dump($tmp);exit();
		$ret = join("", $tmp);
		$top = $interval * $page;
		$receipts .= sprintf("
				<table id=\"receipttable\" style=\"top:%smm;\">
				%s
				</table>
				", $top, $ret);	
				//echo $top."<br><br>";
		//var_dump($receipts);exit();
		
	}
	$print_js = "<script type=\"text/javascript\">window.print();</script>";
	$receipts .= $print_js. $receiptfoot;
	//$this->__disconnect();
	$this->result = $receipts;
	return $receipts;
	//sample http://localhost/treasury/index.php?main=receipts&action=print&ndate=2014-09-27&submit=submit&range=10&start=26001
	//sample http://localhost/treasury/index.php?main=receipts&action=print&date=2014-09-25&submit=submit&range=10
 }
 
 /*
  * Transfer receipt data from local server to ckc server
  * Upload by id(first priority) or by date -> $_GET["id"], $_GET["date"]
  */
 private function __upload_receipt()
 {
	$filter = isset($_GET["id"])?"id":"date";
	switch($filter)
	{
		case "id":
			$this->__upload_receipt_by_id();
			break;
		default:
			$this->__upload_receipt_by_date();
	}
 }
 
 private function __download_receipts()
 {
	$date = isset($_GET["date"])?addslashes($_GET["date"]):false;
	if($date === false)
	{
		echo B,"Fatal : ",B1,"Date Not Set", EOL;
		exit();
	}
	$fields = sprintf("FDate=%s&TDate=%s&Search=Search", $date, $date);
	$this->__ckc_login();
	$page = $this->__ckc_statement_download($fields);
	
	echo $page;
	return true;
 }
 /*
  *	To upload all for given date, call this repeatedly until value returned is 104
  */
 private function __upload_receipt_by_date()
 {
	$date = addslashes($_GET["date"]);
	$this->__connect();
	$query = sprintf("SELECT Ind FROM receipts WHERE CollectionDate ='%s' AND (Uploaded = 'F' OR Uploaded = 'FF')",$date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	
	$ids = array();
	while($tdate = mysqli_fetch_assoc($check))$ids[] = $tdate["Ind"];
	$range = 5;
	$num_ids = count($ids);
	($num_ids < 5)?$range = $num_ids:false;
	
	for($i=0; $i < $range; $i++)
	{
		$_GET["id"] = $ids[$i];
		//echo $ids[$i]."<br>";
		$this->__upload_receipt_by_id();
	}
	$date = $_GET["date"];
	$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	echo "<script type=\"text/javascript\">/*alert(\"done\");*/window.location.assign(\"".$actual_link."?main=receipts&action=upload&date=".$date."\");</script>";
	$this->result = 7; 
	return true;	//assuming no error is returned from $this->__upload_receipt_by_id()
	//sample http://localhost/treasury/index.php?main=receipts&action=upload&date=2014-09-24
 }
 /*
  * Upload single local receipt which may result in upto 3 receipts to be uploaded
  */
 private function __upload_receipt_by_id()
 {
	/*$this->__ckc_login();
	"EntryDate=2014-10-18&Name=Akinyi+Milka&Submit=Submit&Created_by=Jkusda&Tithe=-1690.00&Combined=0&CampOff=0.00&Others=None&Amount=0&OtherCategories=None&CategoryAmount=0&Unspecified=Gastones-Compassionate&UnspecifiedAmount=-50.00&TotalAmount=200";
		echo $field."<br>";//exit();
	$successful_upload = $this->__ckc_receipt_upload($field);
		
		exit("uploaded");
		
		
	$this->__ckc_login();	
	$names = array
	(
		"Akinyi+Milka+Odeny",
		"Pascal+Taro",
		"Anon",
		"Lydya+Obwocha",
		"Joshua+Ochenge",
		"Najjuma+Ephie",
		"Lucy+Alila",
		"Sharon+Owuor",
		"Amos+Nyamao",
	);
	$amounts = array
	(
		0=>array("Tithe"=>-1690.0,
				"Combined"=>-5.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"Gastones-Compassionate",
				"UnspecifiedAmount"=>-50.00,
				"TotalAmount"=>-1750
				),
		1=>array("Tithe"=>0.00,
				"Combined"=>0.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"None",
				"UnspecifiedAmount"=>0.00,
				"TotalAmount"=>-60
				),
		2=>array("Tithe"=>0.00,
				"Combined"=>0.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"None",
				"UnspecifiedAmount"=>0.00,
				"TotalAmount"=>-50
				),
		3=>array("Tithe"=>-500.00,
				"Combined"=>0.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"None",
				"UnspecifiedAmount"=>0.00,
				"TotalAmount"=>-530.00
				),
		4=>array("Tithe"=>0.00,
				"Combined"=>0.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"None",
				"UnspecifiedAmount"=>0.00,
				"TotalAmount"=>-10.00
				),
		5=>array("Tithe"=>-2500.00,
				"Combined"=>0.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"None",
				"UnspecifiedAmount"=>0.00,
				"TotalAmount"=>-2500.00
				),
		6=>array("Tithe"=>0.00,
				"Combined"=>-100.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"None",
				"UnspecifiedAmount"=>0.00,
				"TotalAmount"=>-100.00
				),
		7=>array("Tithe"=>0.00,
				"Combined"=>-50.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"None",
				"UnspecifiedAmount"=>0.00,
				"TotalAmount"=>-50.00
				),
		8=>array("Tithe"=>-130.00,
				"Combined"=>-20.00,
				"CampOff"=>0.00,
				"Others"=>"None",
				"Amount"=>0.00,
				"OtherCategories"=>"None",
				"CategoryAmount"=>0.00,
				"Unspecified"=>"None",
				"UnspecifiedAmount"=>0.00,
				"TotalAmount"=>-150.00
				),		
	);
		
		
		
		
			$i = 0;
		foreach($names as $name)
		{
		$field = "EntryDate=2014-10-18&Name=$name&Submit=Submit&Created_by=Jkusda&Tithe=".$amounts[$i]["Tithe"]."&Combined=".$amounts[$i]["Combined"]."&CampOff=".$amounts[$i]["CampOff"]."&Others=".$amounts[$i]["Others"]."&Amount=".$amounts[$i]["Amount"]."&OtherCategories=".$amounts[$i]["OtherCategories"]."&CategoryAmount=".$amounts[$i]["CategoryAmount"]."&Unspecified=".$amounts[$i]["Unspecified"]."&UnspecifiedAmount=".$amounts[$i]["UnspecifiedAmount"]."&TotalAmount=".$amounts[$i]["TotalAmount"];
		
		
		//$successful_upload = $this->__ckc_receipt_upload($field);
		
		if($i==5){echo $field."<br><br>";$successful_upload = $this->__ckc_receipt_upload($field);exit();}
		$i++;
		}
		exit();
		/**/
		//exit();

		
		
		
		
		
		
		
		
		
		
	//fetch data for $_GET["id"] from table. Return error 104 if nothing is found
	$id = addslashes($_GET["id"]);
	$this->__connect();
	$query = sprintf("SELECT * FROM receipts WHERE Ind ='%s' AND (Uploaded = 'F' OR Uploaded = 'FF')",$id);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	$tmp = mysqli_fetch_assoc($check);
	
	//shared fields appearing in all receipts. POST_VAR=>TABLE_FIELD
	$fields = array(
		"EntryDate"=>"CollectionDate", 
		"Name"=>"Name", 
		"Tithe"=>"Tithe",
		"Combined"=>"Combined",
		"CampOff"=>"CampOffering"
		);
		
	$total = 0;
	//Shared values required for all receipts
	$values = array("Submit"=>"Submit", "Created_by"=>CHURCHNAME);
	//assigned to each *required POST_VAR its value from $tmp
	foreach($fields as $key=>$val)$values[$key] = stripslashes($tmp[$val]);
	//More table_fields that do not neccessarily have to be on each receipt
	$unspecifiedtablefields = array(	
		"Uns1"=>"Amt1",
		"Uns2"=>"Amt2",
		"Uns3"=>"Amt3",
		"Uns4"=>"Amt4",
		"Uns5"=>"Amt5",
		"Uns6"=>"Amt6",
		"Uns7"=>"Amt7",
	);
	
	//values of unspecified table_fields. Unsx=Amtx
	//Assign these only iff their values is greater than zero.
	$unspecifiedtablevalues = array();	
	foreach($unspecifiedtablefields as $key=>$val)
		if((float)$tmp[$val] !=0 )$unspecifiedtablevalues[$tmp[$key]] = stripslashes($tmp[$val]); //check negative
	//other categories by receipt categorization. The Unsx table_fields are classified into these
	$othercategories = array(
		"AMO", "Pathfinder", "Adventurous", "Msamaria Mwema", "Sabbath School", "Choir", "Chaplaincy", "Adventist Muslim Relations", "Personal Ministries", "Youth","Camp Expenses","Stewardship","Deaconry","Evangelism","Church Budget","Women Ministries"
	);
	$othercategoriesfromtable = array();
	$unspecified = array();
	if((float)abs($tmp["Building"]) >= 0 )$othercategoriesfromtable["Development"] = $tmp["Building"]; //one unspecified already used
	//Other categories by receipt classification. If in table_fields there is Unsx that matches othercategories/|\
	//Otherwise it is unspecified
	foreach($unspecifiedtablevalues as $key=>$val){if(in_array($key, $othercategories))$othercategoriesfromtable[$key] = $val;else $unspecified[$key]=$val;}
	
	/*
	 * Now there are three data arrays:
	 *			$values						Fields that are uniquely named and exist in every receipt
				$othercategoriesfromtable	Fields from receipts that are selected. Only two can be used in a single receipt
				$unspecified				Fields whose names are created in the receipt. Only one per receipt
	 */
	//receipts. Multiple receipts for entries that can not fit in a single ckc receipt
	//ckc receipt: Date, Name, Tithe, Combined, camp, Others, Others, Unspecified 
	$receipts = array();
	//number of receipts. Determined from which is greater between the number of receipts required for
	//unspecified or othercategories
	$j = ceil(count($othercategoriesfromtable)/2);				//receipts required for othercategories; 2 per receipt
	$k = count($unspecified);									//receipts required for unspecified; 1 per receipt
	$num_receipts = ($j >= $k && $j > 0)?$j:(($k >= $j && $k > 0)?$k:1);
	
	$shared_fields = array("EntryDate", "Name", "Submit", "Created_by");
	$mainFields = array("Tithe","Combined","CampOff");
	$otherFields = array("Others"=>"Amount","OtherCategories"=>"CategoryAmount");
	for($i = 0; $i < $num_receipts; $i++)
	{
		$total = 0;
		foreach($shared_fields as $key)$receipts[$i][$key] = addslashes($values[$key]);
		foreach($mainFields as $key)
			if(isset($values[$key]))
			{
				if($key == "Combined")$receipts[$i][$key] = ($values[$key]/2);  //remove decimal places from combined offerings. CKC system does recongize them 
				else{ $receipts[$i][$key] = $values[$key]; }
				//$receipts[$i][$key] = $values[$key];
				$total+=$values[$key]; unset($values[$key]);
			}
				else $receipts[$i][$key] = 0;
		foreach($otherFields as $key=>$val)
			if(count($othercategoriesfromtable))//negative
			{
				foreach($othercategoriesfromtable as $ikey=>$ival)
				{
					$receipts[$i][$key] = $ikey;
					$receipts[$i][$val] = $ival;
					$total += $ival;
					unset($othercategoriesfromtable[$ikey]);
					break;
				}
			}
			else
			{
				$receipts[$i][$key] = "None";
				$receipts[$i][$val] = 0;
			}
			if(count($unspecified)>0)//negative
			{
				foreach($unspecified as $ikey=>$ival)
				{
					$receipts[$i]["Unspecified"] = $ikey;
					$receipts[$i]["UnspecifiedAmount"] = $ival;
					$total += $ival;
					unset($unspecified[$ikey]);
					break;
				}
			}
			else
			{
				$receipts[$i]["Unspecified"] = "None";
				$receipts[$i]["UnspecifiedAmount"] = 0;
			}
			$receipts[$i]["TotalAmount"] = $total;
	}
	$postfields = array();
	//data to upload field=val&field1=val1
	foreach($receipts as $i=>$receipt)
		foreach($receipt as $field=>$val)$postfields[$i][] = sprintf("%s=%s", urlencode($field), urlencode($val));
	$upload_receipts = array();
	foreach($postfields as $field)$upload_receipts[] = join("&", $field);
	
	
	$this->__ckc_login();
	$uploaded = "F";
	foreach($upload_receipts as $field)
	{
		
		//echo $field;exit();
		$successful_upload = $this->__ckc_receipt_upload($field);		
		//$successful_upload = false;  ///check. delete
		if($successful_upload !== false && $successful_upload != false)
		{
			if($uploaded == "F")$uploaded = 1;
			else $uploaded++;
			$query = sprintf("UPDATE receipts SET Uploaded ='%s' WHERE Ind = '%s'", $uploaded, $id);
		}
		else 
			$query = sprintf("UPDATE receipts SET Uploaded ='%sF' WHERE Ind = '%s'", $uploaded, $id);
		mysqli_query($this->link, $query);
		;
	}
	//exit("Header:location");
	//header("location:http://localhost/treasury/jkusdatreasury/oldckc/?main=receipts&action=upload&date=2014/10/18");
	//$this->__disconnect();
	$this->result = 7; return true; //check. Handling errors in multiple receipts that occur midway so that uploaded = iF
	//sample http://localhost/treasury/index.php?main=receipts&action=upload&id=8
 }
 private function _img_receipt_format($receipt_data, $table)
 {
 
	$auth = $this->auth;
	$treasurer = $auth->authname;
	
	$a = $receipt_data;
	$items = array("Tithe 10%",isset($a["Uns1"])?$a["Uns1"]:"&nbsp;",isset($a["Uns6"])?$a["Uns6"]:"&nbsp;","Combined Offerings 10%+", isset($a["Uns3"])?$a["Uns3"]:"&nbsp;", "Camp Meeting Offerings", isset($a["Uns7"])?$a["Uns7"]:"&nbsp;", isset($a["Uns2"])?$a["Uns2"]:"&nbsp;", "Building Fund", isset($a["Uns5"])?$a["Uns5"]:"&nbsp;", isset($a["Uns4"])?$a["Uns4"]:"&nbsp;", "TOTAL");
	
	$vars = array("tithe"=>"Tithe","unsp1"=>"Amt1","unsp2"=>"Amt2","unsp3"=>"Amt3","unsp4"=>"Amt4","unsp5"=>"Amt5","unsp6"=>"Amt6","unsp7"=>"Amt7","comb"=>"Combined","camp"=>"CampOffering","devt"=>"Building");
	
	$totalsh = 0;
	$totalcts = 0;
	foreach($vars as $var=>$tableField)
	{
		$tmp = $var. "sh";
		$tmp1 = $var. "cts";
		if($receipt_data[$tableField] == 0 || $receipt_data[$tableField] == "")
		{
			$$tmp = "&nbsp";
			$$tmp1 = "&nbsp";
		}
		else
		{
			$$tmp = floor($receipt_data[$tableField]);
			$$tmp1 = floor(100* ($receipt_data[$tableField]-$$tmp));
			$totalsh += $$tmp;
			$totalcts += $$tmp1;
		}
	}
	//If cents are more than 100, do correct calculations
	if($totalcts >= 100){$totalsh +=1;  $totalcts -= 100;} 
	
	$shs = array($tithesh,$unsp1sh, $unsp6sh, $combsh,$unsp3sh, $campsh, $unsp7sh, $unsp2sh, $devtsh,$unsp5sh,$unsp4sh,$totalsh);
	$cts = array($tithects,$unsp1cts,$unsp6cts, $combcts,$unsp3cts, $campcts, $unsp7cts, $unsp2cts, $devtcts,$unsp5cts,$unsp4cts,$totalcts);
	
	$collection_time =  strtotime($receipt_data["CollectionDate"]);
	$date = date("d/m/Y", $collection_time);
	//exit($date);
	if($table == "receipts")$receipt_num = sprintf("8%s%s", date("md", $collection_time), $receipt_data["Ind"]);
	else $receipt_num = $receipt_data["Ind"];
	$receipt = sprintf
	("
	", $receipt_num, $receipt_data["Name"], 
		$items[0], $shs[0], $cts[0],$items[1], $shs[1], $cts[1],$items[2], $shs[2], $cts[2],$items[3], $shs[3], $cts[3],
		$items[4], $shs[4], $cts[4],$items[5], $shs[5], $cts[5],$items[6], $shs[6], $cts[6],$items[7], $shs[7], $cts[7],
		$items[8], $shs[8], $cts[8],$items[9], $shs[9], $cts[9],$items[10], $shs[10], $cts[10],$items[11], $shs[11], $cts[11],
	$treasurer, $date);
	$item = array();
	foreach($items as $ca=>$rol)
	{
		$item[$ca] = array($items[$ca],$shs[$ca],$cts[$ca]);
	}
	$item["receiptnum"] = $receipt_num;
	$item["name"] = $receipt_data["Name"];
	$item["church"] = "JKUSDA";
	$item["treasurer"] = $treasurer;
	$item["date"] = $date;
	$this->_img_gen($item);
	return $receipt;
	
 } 
 
 private function _img_gen($items)
 {
	$positions = array
	(
	"header"=>array(
				"receiptnum"=>array(420,30),
				"name"=>array(70,65)
			),
	"footer"=>array(
				"church"=>array(80,195),
				"treasurer"=>array(270,195),
				"date"=>array(410,195)
			),
	"column1"=>array(
				"unknown"=>array(30,85),
				"shs"=>array(155,85),
				"cts"=>array(215,85)
			),
	"column2"=>array(
				"unknown"=>array(270,85),
				"shs"=>array(400,85),
				"cts"=>array(460,85)
			)
	);
	$fontsizes = array
	(
	"header"=>array(
				"receiptnum"=>5,
				"name"=>5
			),
	"footer"=>array(
				"church"=>2,
				"treasurer"=>2,
				"date"=>5
			),
	"column1"=>array(
				"unknown"=>2.5,
				"shs"=>4,
				"cts"=>4
			),
	"column2"=>array(
				"unknown"=>2.5,
				"shs"=>4,
				"cts"=>4
			)
			
	);
	$fontcolours = array
	(
	"header"=>array(
				"receiptnum"=>array(250, 0, 0),
				"name"=>array(0, 0, 255 )
			),
	"footer"=>array(
				"church"=>array(0, 0, 255),
				"treasurer"=>array(0, 0, 255),
				"date"=>array(0, 0, 255)
			),
	"column1"=>array(
				"unknown"=>array(0, 0, 255),
				"shs"=>array(0, 0, 255),
				"cts"=>array(0, 0, 255)
			),
	"column2"=>array(
				"unknown"=>array(0, 0, 255),
				"shs"=>array(0, 0, 255),
				"cts"=>array(0, 0, 255)
			)	
	);
	$receipt = imagecreatefrompng ( RECEIPTIMGPATH ) ;
	$background = imagecolorallocate( $receipt, 255, 255, 255 );
	//$text_colour = imagecolorallocate( $receipt, 0, 0, 0 );
	$text_colour = imagecolorallocate( $receipt, 250, 0, 0 );
	
	$this->__img_receipt_set_header($receipt,$positions["header"],$fontsizes["header"],$fontcolours["header"], $items);
	$this->__img_receipt_set_header($receipt,$positions["footer"],$fontsizes["footer"],$fontcolours["footer"], $items);
	$this->__img_receipt_set_cols($receipt,$positions,$fontsizes,$fontcolours, $items);
	
	if(isset($_GET["view"]))
	if($_GET["view"]=="true")
	{
		header( "Content-type: image/png" );
		imagepng( $receipt);
	}
	imagepng( $receipt, sprintf("%s%s.png",RECEIPTSFILE, $items["receiptnum"] ));
	
	imagecolordeallocate($receipt, $text_colour);
	imagecolordeallocate($receipt, $background);
	imagedestroy( $receipt );
	
 }
 
 
 private function __img_receipt_set_cols($img, $positions, $fontsizes, $fontcolour, $body)
 {
	$setpositions = array(0,3,5,8,11);		//where in img the namee of item is set.
	
	$col = "column1";
	$col1max = 5;
	$colpositions = $positions["$col"];
	$colfontsizes = $fontsizes["$col"];
	$colfontcolour = $fontcolour["$col"];
	
	foreach($colpositions as $pos=>$ordinates)
	{
		$fontcolourarray = $colfontcolour[$pos];
		$fontsize	= $colfontsizes[$pos];
		$posarray 	= $colpositions[$pos];
		
		$objcolor = imagecolorallocate($img, $fontcolourarray[0],$fontcolourarray[1],$fontcolourarray[2]);
		$i = 0;
		$startpos = $posarray[1];
		foreach($body as $index=>$bodyarr)
		{
			$map = array("unknown"=>0,"shs"=>1,"cts"=>2);
			$posarray[1] = $startpos + 17 * ($i-0);
			if($pos == 'unknown')
			{
				if(in_array($index, $setpositions));
				else
				{
					
					imagestring($img, $fontsize, $posarray[0], $posarray[1], $bodyarr[$map[$pos]], $objcolor);
					
				}
			}
			else
			{
				#if($bodyarr[$map[$pos]] == '&nbsp')$bodyarr[$map[$pos]] = "";
				if(!is_numeric($bodyarr[$map[$pos]]))$bodyarr[$map[$pos]] = "";
				imagestring($img, $fontsize, $posarray[0], $posarray[1], $bodyarr[$map[$pos]], $objcolor);
			}
			$i++;
			if($i > $col1max)break;
		}		
	}
	
	$col = "column2";
	$col1max_1 = $col1max;
	$col1max = 11;
	$colpositions = $positions["$col"];
	$colfontsizes = $fontsizes["$col"];
	$colfontcolour = $fontcolour["$col"];
	
	foreach($colpositions as $pos=>$ordinates)
	{
		$fontcolourarray = $colfontcolour[$pos];
		$fontsize	= $colfontsizes[$pos];
		$posarray 	= $colpositions[$pos];
		
		$objcolor = imagecolorallocate($img, $fontcolourarray[0],$fontcolourarray[1],$fontcolourarray[2]);
		$i = $col1max_1;
		$startpos = $posarray[1];
		foreach($body as $index=>$bodyarr)
		{
			if($index <= $i)continue;
			$map = array("unknown"=>0,"shs"=>1,"cts"=>2);
			$posarray[1] = $startpos + 17 * ($i - $col1max_1);
			if($pos == 'unknown')
			{
				if(in_array($index, $setpositions));
				else
				{
					
					imagestring($img, $fontsize, $posarray[0], $posarray[1], $bodyarr[$map[$pos]], $objcolor);
					
				}
			}
			else
			{
				if(!is_numeric($bodyarr[$map[$pos]]))$bodyarr[$map[$pos]] = "";
				imagestring($img, $fontsize, $posarray[0], $posarray[1], $bodyarr[$map[$pos]], $objcolor);
			}
			#echo "$i :  $pos: ",$posarray[0],"<br>";
			#echo "$i :  $pos: ",$posarray[1],"<br>";
			#echo $bodyarr[$map[$pos]],"<BR>";
			//echo $i,"<BR>";
			$i++;
			if($i > $col1max+1)break;
		}		
	}
	#exit();

	return true;
 }
 private function __img_receipt_set_header($img, $positions, $fontsizes, $fontcolour, $header)
 {
	foreach($positions as $pos=>$ordinates)
	{
		$fontcolourarray = $fontcolour[$pos];
		$fontsize	= $fontsizes[$pos];
		$posarray 	= $positions[$pos];
		
		$objcolor = imagecolorallocate($img, $fontcolourarray[0],$fontcolourarray[1],$fontcolourarray[2]);
		imagestring($img, $fontsize, $posarray[0], $posarray[1], $header[$pos], $objcolor);
	}
	return true;
 }
	/*
	 * Take receipt data, put in html_receipt to get receipt that can be printed
	 */
 private function _single_recept_format($receipt_data, $table)
 {
 
	$auth = $this->auth;
	$treasurer = $auth->authname;
	
	$a = $receipt_data;
	$items = array("Tithe 10%",isset($a["Uns1"])?$a["Uns1"]:"&nbsp;",isset($a["Uns6"])?$a["Uns6"]:"&nbsp;","Combined Offerings 10%+", isset($a["Uns3"])?$a["Uns3"]:"&nbsp;", "Camp Meeting Offerings", isset($a["Uns7"])?$a["Uns7"]:"&nbsp;", isset($a["Uns2"])?$a["Uns2"]:"&nbsp;", "Building Fund", isset($a["Uns5"])?$a["Uns5"]:"&nbsp;", isset($a["Uns4"])?$a["Uns4"]:"&nbsp;", "TOTAL");
	
	$vars = array("tithe"=>"Tithe","unsp1"=>"Amt1","unsp2"=>"Amt2","unsp3"=>"Amt3","unsp4"=>"Amt4","unsp5"=>"Amt5","unsp6"=>"Amt6","unsp7"=>"Amt7","comb"=>"Combined","camp"=>"CampOffering","devt"=>"Building");
	
	$totalsh = 0;
	$totalcts = 0;
	foreach($vars as $var=>$tableField)
	{
		$tmp = $var. "sh";
		$tmp1 = $var. "cts";
		if($receipt_data[$tableField] == 0 || $receipt_data[$tableField] == "")
		{
			$$tmp = "&nbsp";
			$$tmp1 = "&nbsp";
		}
		else
		{
			$$tmp = floor($receipt_data[$tableField]);
			$$tmp1 = floor(100* ($receipt_data[$tableField]-$$tmp));
			$totalsh += $$tmp;
			$totalcts += $$tmp1;
		}
	}
	//If cents are more than 100, do correct calculations
	if($totalcts >= 100){$totalsh +=1;  $totalcts -= 100;} 
	
	$shs = array($tithesh,$unsp1sh, $unsp6sh, $combsh,$unsp3sh, $campsh, $unsp7sh, $unsp2sh, $devtsh,$unsp5sh,$unsp4sh,$totalsh);
	$cts = array($tithects,$unsp1cts,$unsp6cts, $combcts,$unsp3cts, $campcts, $unsp7cts, $unsp2cts, $devtcts,$unsp5cts,$unsp4cts,$totalcts);
	
	$collection_time =  strtotime($receipt_data["CollectionDate"]);
	$date = date("d/m/Y", $collection_time);
	if($table == "receipts")$receipt_num = sprintf("8%s%s", date("md", $collection_time), $receipt_data["Ind"]);
	else $receipt_num = $receipt_data["Ind"];
	$receipt = sprintf
	("
	<div id=\"receipt\">
	<div id=\"receiptinside\">
	<div>
	<div id = \"receiptheader\"><span id=\"spl\">SEVENTH-DAY ADVENTIST CHURCH</span><span id=\"spr\">&nbsp;</span></div>
	<div id = \"receiptheader\"><span id=\"spl\">CENTRAL KENYA CONFERENCE</span><span id=\"spr\">Receipt Number: %s</span></div>
	<div id = \"receiptheader\"><span id=\"spl\">P.O BOX 41352, NAIROBI</span><span id=\"spr\">&nbsp;</span></div>
	</div>
	<div>&nbsp;</div>
	<div id=\"receiptbodyname\"><span id=\"spln\"><span>NAME</span><span id=\"splnr\">%s</span></span>
			<span id=\"sprn\">Shs.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cts.</span>
	</div>
	<br><br>
	<div>
	<span id=\"spntl\">
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamtbottom\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
	</span>
	<span id=\"spntr\">
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitem\">%s</span>
			<span class=\"receiptamt\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
		<div id=\"receiptlist\"><span class=\"receiptitemtotal\">%s</span>
			<span class=\"receiptamttotal\">
				<span class=\"receiptshs\">%s</span><span class=\"receiptcnts\">%s</span>
			</span></div>
	</span>
	</div>
	&nbsp;
	<br><br>
	<table class=\"footer\">
		<td class=\"footerl\"><span>Church</span><span id=\"footerls\" class=\"footerlsc\">JKUSDA</span></td>
		<td class=\"footerm\"><span>Treasurer</span><span id=\"footerls\" class=\"footermsc\">%s</span></td>
		<td class=\"footerr\"><span>Date</span><span id=\"footerls\" class=\"footerrsc\">%s</span></td></table>
	</div>
	</div>
	", $receipt_num, $receipt_data["Name"], 
		$items[0], $shs[0], $cts[0],$items[1], $shs[1], $cts[1],$items[2], $shs[2], $cts[2],$items[3], $shs[3], $cts[3],
		$items[4], $shs[4], $cts[4],$items[5], $shs[5], $cts[5],$items[6], $shs[6], $cts[6],$items[7], $shs[7], $cts[7],
		$items[8], $shs[8], $cts[8],$items[9], $shs[9], $cts[9],$items[10], $shs[10], $cts[10],$items[11], $shs[11], $cts[11],
	$treasurer, $date);
	
	
	return $receipt;
	
 }
 
 private function __receipt_head_html()
 {
	$html = "
	<html>
	<head>
	<style type=\"text/css\">	
	#receipt{border: 1px solid black; width: 135mm; height: 66mm; font-size:3.2mm;}
	#receiptinside{padding-top:5mm;padding-left:5mm;}
	#receiptheader{text-align: center; font-weight:bold;}
	#spl{width:60mm;float:left;}
	#spr{width:65mm;float:right;}
	u.dotted{
	border-bottom: 1px dotted #000;
	text-decoration: none; 
	}
	#spln{width:90mm;float:left; }
	#splnl{width:90mm;border-bottom: 1px dotted #000;padding-left:5mm;float:left;}
	#splnr{width:74mm;border-bottom: 1px dotted #000;padding-left:5mm;float:right;}
	#sprn{width:22mm;float:right;}
	#spntl{float:left; width:61mm;}
	#spntr{float:right; width:66mm;}
	
	#receiptlist{width:61mm; background:green;}
	.receiptitem{ border-bottom:1px solid; width:34mm; float:left;font-size:11px;height:16px;}
	.receiptitemtotal{width:34mm; float:left;font-size:11px;height:16px;}
	
	
	.receiptamt{width:26mm;float:right;border:1px solid;border-bottom:0px solid;text-align:center;}
	.receiptamtbottom{width:26mm;float:right;border:1px solid;text-align:center;}	
	.receiptamttotal{width:26mm;float:right;border:1px solid;border-bottom: 5px solid;text-align:center;}
		
	.receiptshs{border-right: 1px solid; width:15mm;float:left;}
	.receiptcnts{ width:9mm;float:right;}
	
	#footerls{border-bottom: 1px dotted #000; float:right; text-align: center;}
	.footerlsc{width: 35mm;}
	.footermsc{width: 27mm;}
	.footerrsc{width: 23mm;}
	
	table.footer{width:100%%;}
	.footerl{width:40%%;}
	.footerm{width:35%%;}
	.footerr{width:25%%;}
	
	#receiptrow1{position:fixed; top:0mm;}
	#receiptrow2{position:fixed; top:65mm;}
	#receiptrow3{position:fixed; top:130mm;}
	.receiptcolumn1{position:fixed; left:0mm;}
	.receiptcolumn2{position:fixed; left:135mm;}
	
	table#receipttable{border-collapse: collapse; height:100mm; position:absolute;}
	td#itd{padding: 0px;}
	tr#itr{padding: 0px;}
	</style>
	</head>
	<body>";
	return $html;
 }
 
 private function __receipt_foot_html()
 {
	$html = "
	</body>
	</html>";
	return $html;
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
 
 protected function __fatal__($code)
 {
	$fatal = array
		(
			101
		);
	if(in_array($code, $fatal))return true;
	return false;
 }
}

/*
	1 Akinyi Milka
	4 Pascal Taro
	7. Anon
	8 Lydia
	10 Joshua Ochenge
	11 Najjuma-remove one
	halves
	13. Lucy Alila
	14. Sharon Owuor
	16. 
*/
