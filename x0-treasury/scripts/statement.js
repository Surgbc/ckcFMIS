$(document).ready(function(){download_statement();});

var html_statement = "";
var receipts_to_upload = {};

var download_statement = function ()
{
	var url = "index.php?main=statement&action=download&from=2014-11-15&to=2014-11-15";
	$.get( url, function( data ) {
		html_statement = data;
		//alert(html_statement);
		receipts_from_statements();
		upload_receipts();
	},"html");
}

var receipts_from_statements = function()
{

	var statement = html_statement;
	var fields = new Array(
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
			
	var parts = {};
	var i = 0;
	statement.replace(/<tr>(.*?)<\/tr>/g, function () {
    //arguments[0] is the entire match
    parts[i] = arguments[1];
	i++;
	});
	
	//$("body").append(parts);
	//alert(parts[0]);
	//alert(parts[0]);
	var fcount = 0;
	$.each(parts, function(a, part){
		var i = 0;
		var tmp = [];
		part.replace(/<td>(.*?)<\/td>/g, function () {
			tmp[i] = arguments[1];
			i++;
		});
		//$("body").append(tmp);
		
		var receipt = {};
		$.each(fields, function(key, val){receipt[val] = tmp[key];});
		var tmpcombined = receipt["Combined"];
		//replace
		
		receipt["Combined"] = receipt["Combined"];
		delete receipt["ChurchBudget"];
		
		//development
		dev_search = {"Uns1":"Amt1", "Uns2":"Amt2", "Uns3":"Amt3"};
		$.each(dev_search, function(a,b){
			if(receipt[a] == 'Development')
			{
				receipt["devt"] =  receipt[b];
				delete receipt[a];delete receipt[b];
			}
			if(receipt[a] == 'None' || receipt[a] == 0)
			{
				delete receipt[a];delete receipt[b];
			}
			
		});
		
		$.each(receipt, function(a,b)
		{
			if(a == 'CollectionDate')
			{
				receipt["date"] =  b;
				delete receipt[a];
			}
			if(a == 'CampOffering')
			{
				receipt["camp"] =  b;
				delete receipt[a];
			}
		});
		
		$.each(receipt, function(a,b){
			//$("body").append(a+"="+b+"<br><br>");
		});
		
		receipts_to_upload[fcount] = receipt;
		fcount++;
	});
	
	
}

var upload_receipts = function()
{
	$.each(receipts_to_upload, function(a, receipt)
	{
		var tmp = {};
		var i = 0;
		$.each(receipt, function(c,d){
			tmp[i] = c+"="+d;
			//$("body").append(tmp[i]+"<br><br>");
			i++;
		});
		var data = "";
		$.each(tmp, function(c,d){
			data += d;
			data += "&";
		});
		data += "submit=submit";
		//$("body").append(data+"<br><br>");
		
		var url = "index.php?main=receipts&action=add&"+data;
		$.get( url, function( data ) {$("body").append(url+"<br><br>");},"html");
		//http://localhost/csyber/finance/jkusdatr/?main=receipts&action=add&submit=submit&name=Carol%20Kade&tithe=700&Ind=21
	});
	
}