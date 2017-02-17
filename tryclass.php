<?php

class tryclass Extends JKUSDATREASURY
{

 public function __construct()
 {
	$this->__connect();
	$query = "SELECT * from receipts where CollectionDate = '2014/10/18'";
	$check = mysqli_query($this->link, $query);
	echo mysqli_error($this->link);
	
	$gtotal = 0;$gdowntotal = 0;
	while($row = mysqli_fetch_assoc($check))
	{
		$localtotal = 0;
		foreach($row as $a=>$b)
		{
			if(is_numeric($b) && $a != "Ind" && $a != "Uploaded")
			{
				$localtotal += $b;
				$gtotal += $b;
			}
		}
		$name = $row["Name"];
		
		
		$new_query = "SELECT * from downloadedreceipts where CollectionDate = '2014/10/18' AND Name='$name'";
		$check1 = mysqli_query($this->link, $new_query);
		$total = 0;
		while($row1 = mysqli_fetch_assoc($check1))
		{
			foreach($row1 as $a=>$b)
			{
				if(is_numeric($b) && $a != "Ind")
				{
					$total += $b;
					$gdowntotal +=$b;
				}
			}
				
		}
		
		if($localtotal != $total){echo "$name  downloaded total: $total   localtotal:$localtotal<br>";}
		
	}
	echo "downloaded total: $gdowntotal   localtotal:$gtotal<br>";
	exit();
 }
}