<?php

class jkusdatr_receipts Extends JKUSDATREASURY
{

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
			break;
		default:
			;
	}
	
	return;
 }
 
 private function __add_receipt()
 {
	$vars	= $_GET; //check. change to $_POST
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
	$submit = isset($vars["submit"])?true:false;
	if(!$submit)return;				//error
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
	
	
	$field_values = array();
	foreach($fields as $key=>$field)
	{
		if($$field === false)return; //error.missing fields
		$field_values[] = $$field;
	}
	$query_fields = implode(",", array_keys($fields));
	$query_values = "'".implode("','", $field_values)."'";
	$query = sprintf("INSERT INTO receipts (%s) VALUES(%s)", $query_fields, $query_values);
	
	$this->__connect();
	mysqli_query($this->link, $query);
	if(mysqli_error($this->link))return;// error
	$this->__disconnect();
	return true;	//no error
	//sample:http://localhost/csyber/finance/jkusdatr/?main=receipts&action=add&submit=submit&name=Carol%20Kade&tithe=700
 }
 
 private function __delete_receipt()
 {
	$vars	= $_GET; //check. change to $_POST?
	$fields = array("Ind"=>"id");
	
	$submit = isset($vars["submit"])?true:false;
	if(!$submit)return;				//error	
	$id	= isset($vars["id"])?stripslashes($vars["id"]):false;
	
	foreach($fields as $key=>$field)
	{
		if($$field === false)return; //error.missing fields
	}
	
	$query = sprintf("DELETE FROM receipts WHERE Ind ='%s'", $id);
	//echo $query;
	$this->__connect();
	mysqli_query($this->link, $query);
	if(mysqli_error($this->link))return;// error
	$this->__disconnect();
	return true;	//no error
 }
 private function __view_formatted_receipt()
 {
	$receipt = $this->__receipt_head_html().$this->__view_receipt(). $this->__receipt_foot_html();
	echo $receipt;
	return $receipt;
 }
 private function __view_receipt()
 {
	/*
	 * single receipt at a time. All receipts view from treasurer's cash statement
	 */
	$vars	= $_GET; //check. change to $_POST?
	$fields = array("Ind"=>"id");
	
	$submit = isset($vars["submit"])?true:false;
	if(!$submit)return;				//error	
	$id	= isset($vars["id"])?stripslashes($vars["id"]):false;
	
	foreach($fields as $key=>$field)
	{
		if($$field === false)return; //error.missing fields
	}
	
	$query = sprintf("SELECT * FROM receipts WHERE Ind ='%s'", $id);
	//echo $query;
	$this->__connect();
	$result = array();
	$check = mysqli_query($this->link, $query);
	if(mysqli_error($this->link))return;// error
	if($check === false || mysqli_num_rows($check) == 0)return; //error
	$row = mysqli_fetch_assoc($check);
	//var_dump($row);
	$this->__disconnect();
	
	$ret = $this->_single_recept_format($row);
	return $ret;	//no error
	//sample http://localhost/csyber/finance/jkusdatr/?main=receipts&action=view&submit=submit&id=11
 }
 
 private function __print_receipt()
 {
	/*
	 * Require a range
	 */
	//Assuming range
	$ret = $this->__view_receipt();
	$receipthead = $this->__receipt_head_html();
	$receiptfoot = $this->__receipt_foot_html();
	
	$receipts = $receipthead. sprintf("
	<div id=\"receiptrow1\" class = \"receiptcolumn1\">%s</div><div id=\"receiptrow1\" class = \"receiptcolumn2\">%s</div>
	<div id=\"receiptrow2\" class = \"receiptcolumn1\">%s</div><div id=\"receiptrow2\" class = \"receiptcolumn2\">%s</div>
	<div id=\"receiptrow3\" class = \"receiptcolumn1\">%s</div><div id=\"receiptrow3\" class = \"receiptcolumn2\">%s</div>
	
	", $ret, $ret, $ret, $ret, $ret, $ret).$receiptfoot;
	
	echo $receipts;
 }
 
 private function __upload_receipt()
 {
 
 }
 
 private function _single_recept_format($receipt_data)
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
			$$tmp1 = 100* ($receipt_data[$tableField]-$$tmp);
			
			$totalsh += $$tmp;
			$totalcts += $$tmp1;
		}
	}
	//$tithesh = floor($receipt_data["Tithe"]);
	//$tithects = 100* ($receipt_data["Tithe"]-$tithesh);
	$shs = array($tithesh,$unsp1sh, $unsp6sh, $combsh,$unsp3sh, $campsh, $unsp7sh, $unsp2sh, $devtsh,$unsp5sh,$unsp4sh,$totalsh);
	$cts = array($tithects,$unsp1cts,$unsp6cts, $combcts,$unsp3cts, $campcts, $unsp7cts, $unsp2cts, $devtcts,$unsp5cts,$unsp4cts,$totalcts);
	
	$collection_time =  strtotime($receipt_data["CollectionDate"]);
	$date = date("d/m/Y", $collection_time);
	$receipt_num = sprintf("8%s%s", date("md", $collection_time), $receipt_data["Ind"]);
	$receipt = sprintf
	("
	<div id=\"receipt\">
	<div>
	<div id = \"receiptheader\"><span id=\"spl\">SEVENTH-DAY ADVENTIS CHURCH</span><span id=\"spr\">&nbsp;</span></div>
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
	#receipt{border: 1px solid black; padding: 20px; width: 130mm; height: 65mm; font-size:3.2mm;}
	#receiptheader{text-align: center; font-weight:bold;}
	#spl{width:65mm;float:left;}
	#spr{width:65mm;float:right;}
	u.dotted{
	border-bottom: 1px dotted #000;
	text-decoration: none; 
	}
	#spln{width:90mm;float:left;}
	#splnl{width:90mm;border-bottom: 1px dotted #000;padding-left:5mm;float:left;}
	#splnr{width:74mm;border-bottom: 1px dotted #000;padding-left:5mm;float:right;}
	#sprn{width:22mm;float:right;}
	#spntl{float:left; width:65mm;}
	#spntr{float:right; width:65mm;}
	
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
	#receiptrow2{position:fixed; top:76mm;}
	#receiptrow3{position:fixed; top:152mm;}
	.receiptcolumn1{position:fixed; left:0mm;}
	.receiptcolumn2{position:fixed; left:141mm;}
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
}

/*
 *	In which situtations would it be required that we view just a single receipt?
 * Return from __view_receipt???
 *  Print_receipts:	format receipts to print. How may per page?
		Range
		Resize recepits to fit in ***machine a4
 
 *	upload receipts: all at a go, or any range at a go, even one.->11.00
 *data entry
 
 footer
	UnderLine
	Format fonts
 */