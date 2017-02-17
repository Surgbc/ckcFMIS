<?php

$page = file_get_contents("ckcstatementfiltered.htm");

//echo $page;

preg_match_all("/<tr>.*<\/tr>/", $page, $receipts);
$receipts = $receipts[0];

$fields = array(
			"Ind",
			"CollectionDate",
			"Name",
			"Tithe",
			"Combined",
			"CampOffering",
			"ChurchBudget",
			"Uns1",
			"Amt1",
			"Uns2",
			"Amt2",
			"Uns3",
			"Amt3"
			);
foreach($receipts as $receipt)
{
	//echo $receipt;echo"<br><br><br>";
	preg_match_all("/<td>[a-zA-Z0-9 \.-]*<\/td>/", $receipt, $parts);
	$parts = $parts[0];
	foreach($parts as $part)$tmp[] = preg_replace("/<.?td>/", "", $part);
	
	$i = 0;
	$receipt = array();
	foreach($fields as $key)
	{	
		$part = $tmp[$i];
		$receipt[$key] = preg_replace("/<.?td>/", "", $part);
		$i++;
	}
	//combined
	$combined = $receipt["Combined"] + $receipt["ChurchBudget"];
	unset($receipt["Combined"]);
	unset($receipt["ChurchBudget"]);
	//Development
	$dev_search = array("Uns1"=>"Amt1", "Uns2"=>"Amt2", "Uns3"=>"Amt3");
	$development = 0; //default
	foreach($dev_search as $key=>$val)
	{
		if($receipt[$key] == "Development")
		{
			$development = $receipt[$val];
			unset($receipt[$key]);
		}
		if($receipt[$key] == "None" || $receipt[$key] == "0")unset($receipt[$key]);
		//echo $receipt[$key];echo"<br><br><br>";
	}
	
	//
	foreach($receipt as $key=>$val)$$key = $val;
	
	$sortedReceipt = array("Ind"=>$Ind, "Name"=>$Name, "Tithe"=>$Tithe, "Combined"=>$combined, "CampOffering"=>$CampOffering, "Uns1"=>isset($Uns1)?$Uns1:"", "Amt1"=>isset($Uns1)?$Amt1:0, "Uns2"=>isset($Uns2)?$Uns2:"", "Amt2"=>isset($Uns2)?$Amt2:0, "Uns3"=>isset($Uns3)?$Uns3:"", "Amt3"=>isset($Uns3)?$Amt3:0);
	
	var_dump($sortedReceipt);
	break;
}