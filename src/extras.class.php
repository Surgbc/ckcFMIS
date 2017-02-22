<?php

class Extras Extends JKUSDATREASURY
{
	private $date;
	
 public function __construct()
 {
	$mysql = $this->__connect();
	
 }
 
 public function __set_Date($date)
 {
	$this->date = $date;
	return true;
 }
 
 public function __possible_people($name)
 {
	$possible = array();
	$nameparts = explode(' ', $name);
	foreach($nameparts as $name)
	{
		if(strlen($name) == 1)continue;
		$search = sprintf("SELECT Name FROM  `downloadedreceipts` WHERE  `Name` REGEXP  '%s' AND CollectionDate = '%s' AND EMAIL !=''", $name, $this->date);
		$search = Mysqli_query($this->link, $search);
		if($search === false || mysqli_num_rows($search) == 0)continue;
		while($row = mysqli_fetch_assoc($search))$possible[] = $row["Name"];
	}
	return $possible;
 }
 
 public function __insert_email($name, $email)
 {
 //	echo $name;
 //	echo $email;
 //	exit();
	$query = sprintf("UPDATE downloadedreceipts SET EMAILSENT = 'F' WHERE EMAIL = ''");
	$update = Mysqli_query($this->link, $query);
	
	$query = sprintf("UPDATE downloadedreceipts SET EMAIL = '%s' WHERE Name = '%s' AND CollectionDate = '%s'", $email, $name, $this->date);
	$update = Mysqli_query($this->link, $query);
	return true;
 }
 
 public function __check_Similar_Names($name, $email)
 {
	$query = sprintf("SELECT IND FROM downloadedreceipts WHERE CollectionDate = '%s'", $this->date);
	$check = Mysqli_query($this->link, $query);
	if($check === false || mysqli_num_rows($check) == 0)
	{
		echo date("H:i:s"),B," Fatal Error: no records for ", $this->date, " or another error ",Mysqli_error($this->link),B1, EOL;
		echo date("H:i:s")," Exiting... ", EOL;
		exit();
	}
	$allPeople = array();
	while($row = mysqli_fetch_assoc($check))$allPeople[] = $row["IND"];
	
	$query = sprintf("SELECT IND, Name	 FROM downloadedreceipts WHERE CollectionDate = '%s' AND NAME = '%s'", $this->date, $name);
	//echo $query;
	$check = Mysqli_query($this->link, $query);
	if($check === false || mysqli_num_rows($check) == 0)
	{
		/*echo date("H:i:s"),B," Fatal Error:", Mysqli_error($this->link),B1, EOL;
		echo date("H:i:s")," Exiting... ", EOL;
		exit();
		*/
		return false;
	}
	$people = array();
	$i = 0;
	while($row = mysqli_fetch_assoc($check))
	{
		$tmp = $row;
		//var_dump($tmp);
		$name =  $tmp['Name'];
		$ind  =  $tmp['IND'];
		if(in_array($name, $people))//multiple entries for same name. We shall not assume that entries for people with similar names cannot follow each other.
		{
			echo  EOL,B,"Duplicate name : ",B1,$name, EOL;
			$query = sprintf("UPDATE downloadedreceipts SET EMAIL = 'example@jkusdatr.com' WHERE Name = '%s'", $name);
			//echo $query;
			$update = Mysqli_query($this->link, $query);
			echo "Email set to example@jkusdatr.com",$name, EOL;
			return false;
		}
	}
	//var_dump($people);
	//unset($people);
	return true;
 }
 /*
  *	No one has been taken so far
  */
 public function __reset_Family_Data()
 {
	$delquery = "UPDATE jkusdafamilies SET TAKEN = 'FALSE'  WHERE 1";
	$delquery1 = "DELETE FROM familymembers WHERE 1";
	$delquery2 = "UPDATE jkusdafamilies SET Gender = 'M'  WHERE Gender = ''";
	$delquery3 = "UPDATE jkusdafamilies SET ACTIVITY = 'A'  WHERE ACTIVITY = ''";
	echo date("H:i:s")," Resetting data: No member taken yet",EOL;
	$del = Mysqli_query($this->link, $delquery);
	$del = Mysqli_query($this->link, $delquery1);
	$del = Mysqli_query($this->link, $delquery2);
	$del = Mysqli_query($this->link, $delquery3);
	echo date("H:i:s")," Assuming no error in Resetting data",EOL;
	return true;
 }
 
 public function __start_Family()
 {
	$familyname = $this->familyname;
	echo date("H:i:s")," Starting ", $familyname, EOL;
	$query = sprintf("INSERT INTO familydata (FamilyName) VALUES ('%s')", $familyname);
	Mysqli_query($this->link, $query);
	//echo Mysqli_error($this->link);
	echo date("H:i:s"), " ", $familyname, " started ",EOL;
 }
 
 public function __get_Family_Name()
 {
 
 }
 /*
  * Go to table
  * Read DADs
  * Check if DADs are enough for the number of families
  * Randomly select DAD for families
  * Show that DAD is taken
  * Save DAD to Family File
  * Update family attributes: men, ladies, active, 6th, 5th, 4th, 3rd, 2nd, 1st, associate
  
  *RQ:: table
	Num	Name| 	Gender|	Activity	Year|	Attrib(DAD, MUM, Teacher, Member)	Taken(True/False)
  */
 public function __getDAD()
 {
	$familyname = $this->familyname;
	echo date("H:i:s")," Choosing dad for ", $familyname, EOL;
	$query = "SELECT * FROM jkusdafamilies Where JAttrib = 'DAD' AND Taken = 'FALSE'";
	$check = Mysqli_query($this->link, $query);
	//var_dump($check);
	if($check === false || mysqli_num_rows($check) == 0)
	{
		echo date("H:i:s"),B," Fatal Error: Existing dads are insuffienct for all the families. Please all more dads", B1, EOL;
		echo date("H:i:s")," Exiting... ", EOL;
		exit();
	}
	while($row = mysqli_fetch_assoc($check))$inputdadslist[] = $row;
	$dadscount = count($inputdadslist);
	echo date("H:i:s")," Selecting Family Dad ", EOL;
	$dadindex = rand(0,999);
	$dadindex = $dadindex % $dadscount;
	$daddata = $inputdadslist[$dadindex];
	$dadName = $daddata["Name"];
	$dadInd = $daddata["Ind"];
	echo date("H:i:s")," Selected Family Dad: ", $dadName, EOL;
	$this->__update_Taken($dadInd);
	echo date("H:i:s"), $dadName, " is taken",EOL;
	echo date("H:i:s")," Setting dad: ", $dadName, EOL;
	$this->__setDAD($dadInd);
	echo date("H:i:s")," Setting family member: ", $dadName, EOL;
	$this->__setFamilyMember($daddata);
}

 private function __setDAD($ind)
 {
	$updatequery = sprintf("UPDATE familydata SET DAD = '%s'  WHERE FamilyName = '%s'",$ind, $this->familyname);
	$update = Mysqli_query($this->link, $updatequery);
	echo Mysqli_error($this->link);
	return true;
 } 
 
 private function __setFamilyMember($memberdata)
 {
	$memberid = $memberdata["Ind"];
	$query = sprintf("Insert INTO familymembers (FamilyName, Member) VALUES('%s', '%s')",$this->familyname, $memberid);
	$run = Mysqli_query($this->link, $query);
	echo Mysqli_error($this->link);
	$this->__updateActivity($memberdata);
	$this->__updateGender($memberdata);
	return true;
 } 
 
 private function __updateActivity($memberdata)
 {
	$activity = $memberdata["Activity"];
	$activity == 'A'?$field = "ACTIVE":$field = "PASSIVE";
	$query = sprintf("SELECT %s FROM familydata WHERE FamilyName = '%s'", $field, $this->familyname);
	$check = Mysqli_query($this->link, $query);
	while($row = mysqli_fetch_assoc($check))$activity = $row[$field];
	$activity++;
	$query = sprintf("UPDATE familydata SET %s = '%s'WHERE FamilyName = '%s'", $field, $activity, $this->familyname);
	$check = Mysqli_query($this->link, $query);
	return true;
 }
  private function __updateGender($memberdata)
 {
	$gender = $memberdata["Gender"];
	$gender == 'M'?$field = "MEN":$field = "LADIES";
	$query = sprintf("SELECT %s FROM familydata WHERE FamilyName = '%s'", $field, $this->familyname);
	$check = Mysqli_query($this->link, $query);
	while($row = mysqli_fetch_assoc($check))$activity = $row[$field];
	$activity++;
	$query = sprintf("UPDATE familydata SET %s = '%s'WHERE FamilyName = '%s'", $field, $activity, $this->familyname);
	$check = Mysqli_query($this->link, $query);
	return true;
 }
 
 private function __update_Taken($ind)
 {
	$updatequery = sprintf("UPDATE jkusdafamilies SET TAKEN = 'TRUE'  WHERE IND = '%s'",$ind);
	$update = Mysqli_query($this->link, $updatequery);
	echo Mysqli_error($this->link);
	return true;
 }
  public function __saveDAD()
 {
 
 }
 
 public function __getMUM()
 {
	$familyname = $this->familyname;
	echo date("H:i:s")," Choosing mum for ", $familyname, EOL;
	$query = "SELECT * FROM jkusdafamilies Where JAttrib = 'MUM' AND Taken = 'FALSE'";
	$check = Mysqli_query($this->link, $query);
	//var_dump($check);
	if($check === false || mysqli_num_rows($check) == 0)
	{
		echo date("H:i:s"),B," Fatal Error: Existing mums are insuffienct for all the families. Please all more dads", B1, EOL;
		echo date("H:i:s")," Exiting... ", EOL;
		exit();
	}
	while($row = mysqli_fetch_assoc($check))$inputdadslist[] = $row;
	$dadscount = count($inputdadslist);
	echo date("H:i:s")," Selecting Family Mum ", EOL;
	$dadindex = rand(0,999);
	$dadindex = $dadindex % $dadscount;
	$daddata = $inputdadslist[$dadindex];
	$dadName = $daddata["Name"];
	$dadInd = $daddata["Ind"];
	echo date("H:i:s")," Selected Family Mum: ", $dadName, EOL;
	$this->__update_Taken($dadInd);
	echo date("H:i:s"), $dadName, " is taken",EOL;
	echo date("H:i:s")," Setting mum: ", $dadName, EOL;
	$this->__setMUM($dadInd);
	echo date("H:i:s")," Setting family member: ", $dadName, EOL;
	$this->__setFamilyMember($daddata);
 } 
 
 private function __setMUM($ind)
 {
	$updatequery = sprintf("UPDATE familydata SET MUM = '%s'  WHERE FamilyName = '%s'",$ind, $this->familyname);
	$update = Mysqli_query($this->link, $updatequery);
	echo Mysqli_error($this->link);
	return true;
 } 
 
  public function __getTeacher()
 {
	$familyname = $this->familyname;
	echo date("H:i:s")," Choosing teacher for ", $familyname, EOL;
	$query = "SELECT * FROM jkusdafamilies Where JAttrib = 'TEACHER' AND Taken = 'FALSE'";
	$check = Mysqli_query($this->link, $query);
	//var_dump($check);
	if($check === false || mysqli_num_rows($check) == 0)
	{
		echo date("H:i:s"),B," Fatal Error: Existing teachers are insuffienct for all the families. Please all more dads", B1, EOL;
		echo date("H:i:s")," Exiting... ", EOL;
		exit();
	}
	while($row = mysqli_fetch_assoc($check))$inputdadslist[] = $row;
	$dadscount = count($inputdadslist);
	echo date("H:i:s")," Selecting Family teacher ", EOL;
	$dadindex = rand(0,999);
	$dadindex = $dadindex % $dadscount;
	$daddata = $inputdadslist[$dadindex];
	$dadName = $daddata["Name"];
	$dadInd = $daddata["Ind"];
	echo date("H:i:s")," Selected Family teacher: ", $dadName, EOL;
	$this->__update_Taken($dadInd);
	echo date("H:i:s"), $dadName, " is taken",EOL;
	echo date("H:i:s")," Setting teacher: ", $dadName, EOL;
	$this->__setTeacher($dadInd);
	echo date("H:i:s")," Setting family member: ", $dadName, EOL;
	$this->__setFamilyMember($daddata);
 } 
 
 private function __setTeacher($ind)
 {
	$updatequery = sprintf("UPDATE familydata SET TEACHER = '%s'  WHERE FamilyName = '%s'",$ind, $this->familyname);
	$update = Mysqli_query($this->link, $updatequery);
	echo Mysqli_error($this->link);
	return true;
 } 
 
 public function __saveTeacher()
 {
 
 }
 
 //here is where we now bargain
 public function __getMember()
 {
	$this->activity = "A";
	$this->gender = "M";
	if($this->__selectMember() == true)goto getMemberEnd;
	$this->activity = "P";
	if($this->__selectMember() == true)goto getMemberEnd;
	$this->activity = "A";
	$this->gender = "F";
	if($this->__selectMember() == true)goto getMemberEnd;
	$this->activity = "P";
	if($this->__selectMember() == true)goto getMemberEnd;
	echo date("H:i:s"),B," No more member to select ", B1, EOL;
		return false;
	getMemberEnd:
		echo date("H:i:s")," New member for: ", $this->familyname, EOL;
		return true;
 }
 
 private function __selectMember()
 {
	$query = "SELECT * FROM jkusdafamilies Where Taken = 'FALSE'";
	$check = Mysqli_query($this->link, $query);
	$check = Mysqli_query($this->link, $query);
	//var_dump($check);
	if($check === false || mysqli_num_rows($check) == 0)
	{
		return false;
	}
	//5th year
	$gender = $this->gender;
	$activity = $this->activity;
	$query = "SELECT * FROM jkusdafamilies Where Taken = 'FALSE' AND JYear = 'Year6' AND Activity = '$activity' AND Gender = '$gender'";
	$check = Mysqli_query($this->link, $query);
	//var_dump($check);
	if($check === false || mysqli_num_rows($check) == 0)
	{
		$query = "SELECT * FROM jkusdafamilies Where Taken = 'FALSE' AND JYear = 'Year5' AND Activity = '$activity' AND Gender = '$gender'";
		$check = Mysqli_query($this->link, $query);
		if($check === false || mysqli_num_rows($check) == 0)
		{
			$query = "SELECT * FROM jkusdafamilies Where Taken = 'FALSE' AND JYear = 'Year4' AND Activity = '$activity' AND Gender = '$gender'";
			$check = Mysqli_query($this->link, $query);
			if($check === false || mysqli_num_rows($check) == 0)
			{
			$query = "SELECT * FROM jkusdafamilies Where Taken = 'FALSE' AND JYear = 'Year3' AND Activity = '$activity' AND Gender = '$gender'";
			$check = Mysqli_query($this->link, $query);
			
			if($check === false || mysqli_num_rows($check) == 0)
			{
			$query = "SELECT * FROM jkusdafamilies Where Taken = 'FALSE' AND JYear = 'Year2' AND Activity = '$activity' AND Gender = '$gender'";
			$check = Mysqli_query($this->link, $query);
			if($check === false || mysqli_num_rows($check) == 0)
			{
			$query = "SELECT * FROM jkusdafamilies Where Taken = 'FALSE' AND JYear = 'Year1' AND Activity = '$activity' AND Gender = '$gender'";
			$check = Mysqli_query($this->link, $query);
			if($check === false || mysqli_num_rows($check) == 0)
			{
			$query = "SELECT * FROM jkusdafamilies Where Taken = 'FALSE' AND JYear = 'Associates' AND Activity = '$activity' AND Gender = '$gender'";
			$check = Mysqli_query($this->link, $query);
				if($check === false || mysqli_num_rows($check) == 0)
				{
				
					return false;
				}
			}
			}
			}
			}
		}
		
	}
		
	while($row = mysqli_fetch_assoc($check))$inputmemberslist[] = $row;
	$memberscount = count($inputmemberslist);
//	echo date("H:i:s")," Selecting Family teacher ", EOL;
	$memberindex = rand(0,999);
	$memberindex = $memberindex % $memberscount;
	$memberdata = $inputmemberslist[$memberindex];
	$memberName = $memberdata["Name"];
	$memberInd = $memberdata["Ind"];
//	echo date("H:i:s")," Selected Family teacher: ", $memberName, EOL;
	$this->__update_Taken($memberInd);
//	echo date("H:i:s"), $memberName, " is taken",EOL;
//	echo date("H:i:s")," Setting teacher: ", $memberName, EOL;
//	$this->__setTeacher($memberInd);
//	echo date("H:i:s")," Setting family member: ", $memberName, EOL;
	$this->__setFamilyMember($memberdata);
	return true;
 }
 
 public function __fetchMembers()
 {
	$familyname = $this->familyname;
	$query = "SELECT Member FROM familymembers Where FamilyName = '$familyname'";
	$check = Mysqli_query($this->link, $query);
	if($check === false || mysqli_num_rows($check) == 0)
	{
		echo date("H:i:s"),B," Fatal Error: Unknown error. Please contact admin", B1, EOL;
		echo date("H:i:s")," Exiting... ", EOL;
		exit();
	}
	$membersp = array();
	while($row = mysqli_fetch_assoc($check))$membersp[] = $row["Member"];
	
	//get dad
	$query = "SELECT DAD FROM familydata Where FamilyName = '$familyname'";
	$check = Mysqli_query($this->link, $query);
	while($row = mysqli_fetch_assoc($check))$dadid = $row["DAD"];
	$query = "SELECT MUM FROM familydata Where FamilyName = '$familyname'";
	$check = Mysqli_query($this->link, $query);
	while($row = mysqli_fetch_assoc($check))$mumid = $row["MUM"];
	$query = "SELECT TEACHER FROM familydata Where FamilyName = '$familyname'";
	$check = Mysqli_query($this->link, $query);
	while($row = mysqli_fetch_assoc($check))$teacherid = $row["TEACHER"];
	
	$members = array();
	foreach($membersp as $ind)
	{
		$query = "SELECT NAME, CONTACT FROM jkusdafamilies Where Ind = '$ind'";
		$check = Mysqli_query($this->link, $query);
		while($row = mysqli_fetch_assoc($check))
		{
			$name = $row["NAME"];
			$contact = $row["CONTACT"];
			switch($ind)
			{
				case $dadid:
					$members["dad"] = array("name"=>$name, "contact"=>$contact);
					break;
				case $mumid:
					$members["mum"] = array("name"=>$name, "contact"=>$contact);
					break;
				case $teacherid:
					$members["teacher"] = array("name"=>$name, "contact"=>$contact);
					break;
				default:
					$members["members"][] = array("name"=>$name, "contact"=>$contact);
					;
			}
		}
	}
	return $members;
 }
 
 
 
}


//http://cub.telestudio.co.ke/

/*
Working
	SendKeystrokes
		...anyway... to copy data from excel to mysql

*/
?>

