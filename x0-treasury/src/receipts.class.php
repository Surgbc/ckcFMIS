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
 public function __construct()
 {
	isset($_GET["action"])?$receipt_action = stripslashes($_GET["action"]): $recipt_action = "view";
	
	switch($receipt_action)
	{
		case "add":
			$this->__add_receipt();
			break;
		case "delete":
			$this->__delete_receipt();
			break;
		case "view":
			$this->__view_formatted_receipt();
			break;
		case "print":
			$this->__print_receipt();
			break;
		case "upload":
			$this->__upload_receipt();
			break;
		case "alltotal":
			$this->alltotal();
			break;
		case "ppltotal":
			$this->ppltotal();
			break;
		case "daytotals":
			$this->day_totals();
			break;
		case "balancing":
			$this->__balancing();
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
  
 private function alltotal()
 {
	/*
		capture:
			expenditure
				2014-10-29     Jerry Brian
				2014-10-26     Kevin Ngoima     Makueni Mission: 50.00
				2014-10-26     George Nyoro     Makueni Mission: 100.00
				
				communication	-100
				Reagan omullo -500
				Kenneth Wandugu 500
				
				
				Bibles 
				Follow-up
				Transport
	*/
	$table = "downloadedreceipts";
	$table = "receipts";//2014-8-28
	$query = sprintf("SELECT CollectionDate, Name, Uns1, Uns2, Uns3, Uns4, Uns5, Uns6, Uns7, Amt1, Amt2, Amt3, Amt4, Amt5, Amt6, Amt7 FROM %s WHERE CollectionDate > '2014-1-1' AND CollectionDate<='2015-1-31'", $table);
	//wiki, notes, PULSEMOD, Xu&co, 02, check &co
	
	$this->__connect();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	$total = 0;
	$expenditure = 0;
	$income = 0;
	$people = array();
	echo "<table>";
	$keyword = isset($_GET["keyword"])?$_GET["keyword"]:"Makueni";
	while($row = mysqli_fetch_assoc($check))
	{
		$i = 1;
		while($i<=7)
		{
			$var = sprintf("Uns%s", $i);
			$amt = sprintf("Amt%s", $i);
			$abc = $row[$var];
			//if(stripos($abc, "makueni") !== false || stripos($abc, "mission") !== false)
			//if(stripos($abc, "Mission") !== false || stripos($abc, "Makueni") !== false || stripos($abc, "Londiani") !== false)
			if(stripos($abc, $keyword) !== false)
			//if($abc == "Londiani")
			{
				//var_dump($row);
				//echo sprintf("%s &nbsp;&nbsp;&nbsp;&nbsp;%s &nbsp;&nbsp;&nbsp;&nbsp;%s: %s<br>",$row["CollectionDate"], $row["Name"], $row[$var], $row[$amt]);
				echo sprintf("<tr><td>%s</td><td>%s</td><td>%s:</td><td>%s</td></tr>",$row["CollectionDate"], $row["Name"], $row[$var], $row[$amt]);
				$tmpamt = $row[$amt];
				if($tmpamt>0)$income += $tmpamt;
				else $expenditure += $tmpamt;
				$total += $tmpamt;
			}//
			
			//echo $abc."<br>"; 
			$i++;
		}
	}
	echo "</table>";
	echo "income: ".$income."<br>";
	echo "expenditure: ".$expenditure."<br>";
	echo "balance: ".$total."<br>"; 
	
	/*
		Balancing
		...Which are missing in one table...
	*/
 }
 
 private function ppltotal()
 {
	$table = "downloadedreceipts";
	$table = "receipts";//2014-8-28
	$query = sprintf("SELECT CollectionDate, Name, Uns1, Uns2, Uns3, Uns4, Uns5, Uns6, Uns7, Amt1, Amt2, Amt3, Amt4, Amt5, Amt6, Amt7 FROM %s WHERE CollectionDate > '2014-1-1' AND CollectionDate<='2014-12-31'", $table);
	
	$this->__connect();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	$total = 0;
	$names = array();
	$expenditure = 0;
	$income = 0;
	$people = array();
	echo "<table>";
	while($row = mysqli_fetch_assoc($check))
	{
		$i = 1;
		while($i<=7)
		{
			$var = sprintf("Uns%s", $i);
			$amt = sprintf("Amt%s", $i);
			$abc = $row[$var];
			$name = $row["Name"];
			//if(stripos($abc, "makueni") !== false || stripos($abc, "mission") !== false)
			//if(stripos($abc, "Mission") !== false || stripos($abc, "Makueni") !== false || stripos($abc, "Londiani") !== false)
			if(stripos($abc, "Makueni") !== false)
			{
				if($row[$amt] > 0)
				{
					if(!isset($names[$name]))$names[$name] = $row[$amt];
					else 
					{
						$tmp = $names[$name];
						//echo sprintf("<tr><td>%s</td><td>%s:</td></tr>",$name, $tmp);
						$tmp += $row[$amt];
						$names[$name] = $tmp;
					}
				}
			}
			$i++;
		}
	}
	//var_dump($names);exit("");
	$tmpnames = array();
	namesort:
	//echo "in namesort";
	foreach($names as $name=>$total)
	{
		$total = 0;
		//echo "init count".count($names);
		while(isset($names[$name]))
		{
			$total += $names[$name];
			unset($names[$name]);
		}
		//echo "new count".count($names);
		//$tmpnames = $total;
		$tmpnames[$name] = $total;
		//goto namesort;
	}
	foreach($tmpnames as $name=>$total)
	{
		echo sprintf("<tr><td>%s</td><td>%s:</td></tr>",$name, $total);
	}
	echo "</table>";
	echo "income: ".$income."<br>";
	echo "expenditure: ".$expenditure."<br>";
	echo "balance: ".$total."<br>"; 
	
 }
 
 private function __balancing()
 {
 
 }
 
 private function day_totals()
 {
	/*
		capture:
			expenditure
				2014-10-29     Jerry Brian
				2014-10-26     Kevin Ngoima     Makueni Mission: 50.00
				2014-10-26     George Nyoro     Makueni Mission: 100.00
	*/
	$table = "downloadedreceipts";
	//$table = "receipts";
	$date = $_GET["date"];
	$tithe = 0;
	$combined = 0;
	$total = 0;
	
	$query = sprintf("SELECT Tithe FROM %s WHERE CollectionDate = '%s'", $table, $date);
	
	$this->__connect();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	while($row = mysqli_fetch_assoc($check))$tithe += $row["Tithe"];
	$total += $tithe;
	echo "Tithe: $tithe<br>";
	
	$query = sprintf("SELECT Combined FROM %s WHERE CollectionDate = '%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	while($row = mysqli_fetch_assoc($check))$combined += $row["Combined"];
	$total += $combined;
	echo "Combined: Total: $combined Half:".$combined/2 . "<br>";
	
	$camp = 0;
	$query = sprintf("SELECT CampOffering FROM %s WHERE CollectionDate = '%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	while($row = mysqli_fetch_assoc($check))$camp += $row["CampOffering"];
	$total += $camp;
	echo "Camp Offering: Total: $camp<br>";
	
	$devt = 0;
	$query = sprintf("SELECT Building FROM %s WHERE CollectionDate = '%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	while($row = mysqli_fetch_assoc($check))$devt += $row["Building"];
	//$total += $devt;
	//echo "Development: Total: $devt<br>";
	//exit();
	$query = sprintf("SELECT CollectionDate, Name, Uns1, Uns2, Uns3, Uns4, Uns5, Uns6, Uns7, Amt1, Amt2, Amt3, Amt4, Amt5, Amt6, Amt7 FROM %s WHERE CollectionDate = '%s'", $table, $date);
	$others = array();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return 0;} 						// error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return 0;} // error
	//$total = 0;
	//$people = array();
	while($row = mysqli_fetch_assoc($check))
	{	
		$uns1 = $row['Uns1'];	$amt1 = $row['Amt1'];
		$uns2 = $row['Uns2'];	$amt2 = $row['Amt2'];
		$uns3 = $row['Uns3'];	$amt3 = $row['Amt3'];
		$uns4 = $row['Uns4'];	$amt4 = $row['Amt4'];
		$uns5 = $row['Uns5'];	$amt5 = $row['Amt5'];
		$uns6 = $row['Uns6'];	$amt6 = $row['Amt6'];
		$uns7 = $row['Uns7'];	$amt7 = $row['Amt7'];
		for($i=1; $i<8; $i++)
		{
			$varname = "uns".$i;
			$amt = "amt".$i;
			if($$varname == "Development"){$devt += $$amt;continue;}
			if($$varname != '')
			{
				//if(!in_array($$varname, $others))$others[$$varname] = 0;
				//if(!isset($others["$$varname"]))$others[$$varname] = 0;
				$tmp = isset($others[$$varname])?$others[$$varname]:0;
				$tmp += $$amt;
				$others[$$varname] = $tmp;
			}
			//echo $$varname;
		}
	}
	echo "Development: Total: $devt<br>";
	$total += $devt;
	foreach($others as $ca=>$rol){echo "$ca: $rol<br>"; $total += $rol;}
	echo "total: ".$total."<br>"; 
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
					"Building"=>"devt"
					);	
					
	//assign to each variable given in $field its value from $vars or assign it a defaulr value if its not contained in $vars
	//fatal: missing $vars["Submit"]
	$submit = isset($vars["submit"])?true:false;
	if(!$submit){$this->result = 101; return;}				//error
	$date	= isset($vars["date"])?stripslashes($vars["date"]):date("Y-m-d");
	$name	= isset($vars["name"])?stripslashes($vars["name"]):false;
	$tithe	= isset($vars["tithe"])?stripslashes($vars["tithe"]):0;
	$combined= isset($vars["combined"])?stripslashes($vars["combined"]):0;
	$camp	= isset($vars["camp"])?stripslashes($vars["camp"]):0;
	$devt	= isset($vars["devt"])?stripslashes($vars["devt"]):0;
	$uns1	= isset($vars["uns1"])?stripslashes($vars["uns1"]):"";
	$amt1	= isset($vars["amt1"])?stripslashes($vars["amt1"]):0;
	$uns2	= isset($vars["uns2"])?stripslashes($vars["uns2"]):"";
	$amt2	= isset($vars["amt2"])?stripslashes($vars["amt2"]):0;
	$uns3	= isset($vars["uns3"])?stripslashes($vars["uns3"]):"";
	$amt3	= isset($vars["amt3"])?stripslashes($vars["amt3"]):0;
	$uns4	= isset($vars["uns4"])?stripslashes($vars["uns4"]):"";
	$amt4	= isset($vars["amt4"])?stripslashes($vars["amt4"]):0;
	$uns5	= isset($vars["uns5"])?stripslashes($vars["uns5"]):"";
	$amt5	= isset($vars["amt5"])?stripslashes($vars["amt5"]):0;
	$uns6	= isset($vars["uns6"])?stripslashes($vars["uns6"]):"";
	$amt6	= isset($vars["amt6"])?stripslashes($vars["amt6"]):0;
	$uns7	= isset($vars["uns7"])?stripslashes($vars["uns7"]):"";
	$amt7	= isset($vars["amt7"])?stripslashes($vars["amt7"]):0;
	
	//assign to field_values[] the values of the variables in the order in which the variables appear in $fields
	//fatal: when value === false
	$field_values = array();
	foreach($fields as $key=>$field)
	{
		if($$field === false){$this->result = 102; return;}	//fatal
		$$field = str_replace(",", "", $$field);			//Remove commas from values. Important for numeric values such as 1,300
		$field_values[] = $$field;
	}
	
	$i = 0;
	foreach($fields as $key=>$val){if($val == "combined"){$field_values[$i] =  2* $field_values[$i]; }$i++;}
	//Fields and values as they should appear in INSERT query
	$query_fields = implode(",", array_keys($fields));
	$query_values = "'".implode("','", $field_values)."'";
	
	//Decide which table (and query) to use depending on $_GET["Ind"].
	//If isset($_GET["Ind"]) then insert also Ind into Ind
	if(isset($_GET["Ind"]))
	{
		//goto add_downloaded_receipt;
		$ind	= isset($vars["ind"])?stripslashes($vars["ind"]):0;
		$table = "downloadedreceipts";
		$query_fields = "Ind,".$query_fields;
		$query_values = "$ind,".$query_values;
	}
	else
	{
		$table = "receipts";	
	}
	$query = sprintf("INSERT INTO %s (%s) VALUES(%s)", $table, $query_fields, $query_values);
	
	//database connection
	$this->__connect();
	mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){echo mysqli_error($this->link);$this->result = 103; return;}		//fatal
	$this->__disconnect();
	
	$this->result = 7;
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
	$id	= isset($vars["id"])?stripslashes($vars["id"]):false;
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
	
	$this->result = $this->__receipt_head_html().$ret. $this->__receipt_foot_html();
	return true;
 }
 /*
  * Fetch receipt data from appropriate table depending on which of $_GET["ind"] or $_GET["id"] is set
  */
 private function __view_receipt()
 {
	$vars	= $_GET; //check. change to $_POST?
	$submit = isset($vars["submit"])?true:false;
	if(!$submit){$this->result = 101; return 0;}		

	$id	= isset($vars["id"])?stripslashes($vars["id"]): (isset($vars["ind"])?stripslashes($vars["ind"]):false);
	if($id === false){$this->result = 102; return 0;} //error.missing fields
	
	if(isset($vars["ind"]))$table = "receipts";else $table = "downloadedreceipts";
	$query = sprintf("SELECT * FROM %s WHERE Ind ='%s'", $table, $id);
	
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
 
 private function __get_first_id($date, $table)
 {
	$query = sprintf("SELECT Ind FROM %s WHERE CollectionDate ='%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	$row = mysqli_fetch_assoc($check)["Ind"]; //first appearance
	return $row;
 }
 
  private function __get_last_id($date, $table)
 {
	$query = sprintf("SELECT Ind FROM %s WHERE CollectionDate ='%s'", $table, $date);
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link)){$this->result = 103; return false;} // error
	if($check === false || mysqli_num_rows($check) == 0){$this->result = 104; return false;}  //none
	while($row = mysqli_fetch_assoc($check)["Ind"])$ret = $row;
	return $ret;
 }
 /*
  * Require:
  *		date||ndate. receipts start at first id for the given date. date:receipts, ndate: downloadedreceipts
  * 	start: if this is given, then the start id for the given date is overriden
  * 
  */
 private function __print_receipt()
 {
	$fields = array("date");
	$date	= isset($_GET["date"])?stripslashes($_GET["date"]): (isset($_GET["ndate"])?stripslashes($_GET["ndate"]):false);
		
	foreach($fields as $key=>$field)if($$field === false){$this->result = 102; return 0;}	 //error.missing fields
	
	if(isset($_GET["date"]))$table = "receipts"; else $table = "downloadedreceipts";
	
	$this->__connect();
	//$fid = $this->__get_first_id($date, $table);
	isset($_GET["start"])?$fid = stripslashes($_GET["start"]):$fid = $this->__get_first_id($date, $table);
	isset($_GET["range"])?$lid = $fid -1 + stripslashes($_GET["range"]):$lid = $this->__get_last_id($date, $table);
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
	
	$count = 0;
	$trs = array();
	foreach($receipts as $receipt)
	{
		($count%2 == 0)? $trs[] = sprintf("<tr id=\"itr\">%s", $receipt):$trs[] = sprintf("%s</tr>", $receipt);
		$count++;
	}
	
	$receipthead = $this->__receipt_head_html();
	$receiptfoot = $this->__receipt_foot_html();
	
	$receipts = $receipthead;
	
	$interval = 196.6;
	$top = 0;
	
	$trslen = count($trs);
	$page = 0;
	for($i=0; $i < $trslen; $page++, $i+=6)
	{
		$tmp = array(); 
		$ii = $i+6;
		for($j = $i; $j < $ii; $j++)$tmp[] =  isset($trs[$j])?$trs[$j]:"";
		$ret = join("", $tmp);
		$top = $interval * $page;
		$receipts .= sprintf("
				<table id=\"receipttable\" style=\"top:%smm;\">
				%s
				</table>
				", $top, $ret);	
	}
	$receipts .= $receiptfoot;
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
 /*
  *	To upload all for given date, call this repeatedly until value returned is 104
  */
 private function __upload_receipt_by_date()
 {
	$date = stripslashes($_GET["date"]);
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
		$this->__upload_receipt_by_id();
	}
	$this->result = 7; 
	return true;	//assuming no error is returned from $this->__upload_receipt_by_id()
	//sample http://localhost/treasury/index.php?main=receipts&action=upload&date=2014-09-24
 }
 /*
  * Upload single local receipt which may result in upto 3 receipts to be uploaded
  */
 private function __upload_receipt_by_id()
 {
	//fetch data for $_GET["id"] from table. Return error 104 if nothing is found
	$id = stripslashes($_GET["id"]);
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
		"CombinedOff"=>"Combined",
		"CampOff"=>"CampOffering"
		);
		
	$total = 0;
	//Shared values required for all receipts
	$values = array("Submit"=>"Submit", "Created_by"=>"Jkusda");
	//assigned to each *required POST_VAR its value from $tmp
	foreach($fields as $key=>$val)$values[$key] = $tmp[$val];
	
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
		if((float)$tmp[$val] >0 )$unspecifiedtablevalues[$tmp[$key]] = $tmp[$val];
	
	//other categories by receipt categorization. The Unsx table_fields are classified into these
	$othercategories = array(
		"AMO", "Pathfinder", "Adventurous", "Msamaria Mwema", "Sabbath School", "Choir", "Chaplaincy", "Adventist Muslim Relations", "Personal Ministries", "Youth","Camp Expenses","Stewardship","Deaconry","Evangelism","Church Budget","Women Ministries"
	);
	$othercategoriesfromtable = array();
	$unspecified = array();
	if((float)$tmp["Building"] > 0)$othercategoriesfromtable["Development"] = $tmp["Building"]; //one unspecified already used
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
	$mainFields = array("Tithe","CombinedOff","CampOff");
	$otherFields = array("Others"=>"Amount","OtherCategories"=>"CategoryAmount");
	for($i = 0; $i < $num_receipts; $i++)
	{
		$total = 0;
		foreach($shared_fields as $key)$receipts[$i][$key] = $values[$key];
		foreach($mainFields as $key)
			if(isset($values[$key])){$receipts[$i][$key] = $values[$key]; $total+=$values[$key]; unset($values[$key]);}
				else $receipts[$i][$key] = 0;
		foreach($otherFields as $key=>$val)
			if(count($othercategoriesfromtable)>0)
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
			if(count($unspecified)>0)
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
	foreach($upload_receipts as $field)
	{
		$successful_upload = $this->__ckc_receipt_upload($field);
		if($successful_upload !== false && $successful_upload != false)
		{
			if($uploaded == "F")$uploaded = 1;
			else $uploaded++;
			$query = sprintf("UPDATE receipts SET Uploaded ='%s' WHERE Ind = '%s'", $uploaded, $id);
		}
		else 
			$query = sprintf("UPDATE receipts SET Uploaded ='%sF' WHERE Ind = '%s'", $uploaded, $id);
		mysqli_query($this->link, $query);
	}
	//$this->__disconnect();
	$this->result = 7; return true; //check. Handling errors in multiple receipts that occur midway so that uploaded = iF
	//sample http://localhost/treasury/index.php?main=receipts&action=upload&id=8
 }
 
	/*
	 * Take receipt data, put in html_receipt to get receipt that can be printed
	 */
 private function _single_recept_format($receipt_data, $table)
 {
	$a = $receipt_data;
	$items = array("Tithe 10%",isset($a["Uns1"])?$a["Uns1"]:"&nbsp;",isset($a["Uns6"])?$a["Uns6"]:"&nbsp;","Combined Offerings 10%++", isset($a["Uns3"])?$a["Uns3"]:"&nbsp;", "Camp Meeting Offerings", isset($a["Uns7"])?$a["Uns7"]:"&nbsp;", isset($a["Uns2"])?$a["Uns2"]:"&nbsp;", "Building Fund", isset($a["Uns5"])?$a["Uns5"]:"&nbsp;", isset($a["Uns4"])?$a["Uns4"]:"&nbsp;", "TOTAL");
	
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
		<td class=\"footerm\"><span>Treasurer</span><span id=\"footerls\" class=\"footermsc\">&nbsp;</span></td>
		<td class=\"footerr\"><span>Date</span><span id=\"footerls\" class=\"footerrsc\">%s</span></td></table>
	</div>
	</div>
	", $receipt_num, $receipt_data["Name"], 
		$items[0], $shs[0], $cts[0],$items[1], $shs[1], $cts[1],$items[2], $shs[2], $cts[2],$items[3], $shs[3], $cts[3],
		$items[4], $shs[4], $cts[4],$items[5], $shs[5], $cts[5],$items[6], $shs[6], $cts[6],$items[7], $shs[7], $cts[7],
		$items[8], $shs[8], $cts[8],$items[9], $shs[9], $cts[9],$items[10], $shs[10], $cts[10],$items[11], $shs[11], $cts[11],
	$date);
	
	
	return $receipt;
	
 }
 
 private function __receipt_head_html()
 {
	$html = "
	<html>
	<head>
	<style type=\"text/css\">	
	#receipt{border: 1px solid black; width: 135mm; height: 65mm; font-size:3.2mm;}
	#receiptinside{padding-top:5mm;padding-left:5mm;}
	#receiptheader{text-align: center; font-weight:bold;}
	#spl{width:60mm;float:left;}
	#spr{width:65mm;float:right;}
	u.dotted{
	border-bottom: 1px dotted #000;
	text-decoration: none; 
	}
	#spln{width:90mm;float:left;}
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
	return $errors[$code];
 }
}