$(".modifysignup").click(function(){modifysignup($(this).attr("alt"));});
$(".modifycancel").click(function(){load_previous_page();});
$(".modifysubmit").click(function(){modifysubmit();});
$(".modifyalter").click(function(){modifyalter();});
$(".alterselfsubmit").click(function(){alterselfsubmit();});
$(".modifyedit").click(function(){modifyedit();});



var ckcreceiptvar;

var html_statement = "";
var receipts_to_upload = {};
var combinedfactor = 2;

var modifyeditselect = function(tmpuser)
{
	modifyedituid = tmpuser.attr("uid");
	$("#login_userdep").val(tmpuser.attr("dep"));
}


/*							Receipts						*/
$(".newreceipt").click(function(){newreceipt();});
$(".viewreceipt").click(function(){viewreceipt(0);});
$(".viewreceiptlocal").click(function(){viewreceipt(1);});
$(".viewreceiptprint").click(function(){viewreceiptprint();});
$(".importemails").click(function(){importemails1();});
$(".receiptsubmit").click(function(){receiptsubmit();}); //does this still do anythign useful?


var newreceipt = function()
{
	$("#receiptspanrightheader").html("Add Receipts");
	var iframe = "<iframe src=\"index.php?main=page&sub=addreceipts\" width=\"90%\" height=\"400px\"></iframe>";
	$("#receiptspancontent").html(iframe);
	
	/*url = "index.php?main=page&sub=addreceipts";
	$.get( url, function( data ) {
			$("#receiptspanrightheader").html("Add Receipts");
			var iframe = "<iframe src=\"index.php?main=page&sub=addreceipts\" width=\"90%\" height=\"400px\"></iframe>";
			$("#receiptspancontent").html(iframe);
	},"html");
	*/
}

var viewreceipt = function(alt)
{
	ckcreceiptvar = alt;
	//get navigation *tools first
	/*
	 *Start here:
	 *		Receipt nav
				view receipts
				delete receipts: command
			Next: Print receipts
				nav
				print print
	 */
	//url = "?main=receipts&action=view&submit=submit&id=11";
	$("#receiptspanrightheader").html("View Receipts");
	url = "?main=page&sub=viewreceipts&submit=submit";
	$.get( url, function( data ) {
			//$("#receiptspanrightheader").html("View Receipts");
			//var iframe = "<iframe src=\""+url+"\" width=\"90%\" height=\"400px\"></iframe>";
			$("#receiptspancontent").html(data);
		//	$("#receiptspancontent").append(iframe);
	},"html");
}

var viewreceiptnav_go = function()
{	
	var rcptyr = $(".viewreceiptyr").val();
	var rcptmnt = $(".viewreceiptmt").val();
	var rcptdt = $(".viewreceiptdt").val();
	var rcptstrt = $(".viewreceiptstart").val();
	
	
	if(rcptdt == undefined || rcptdt == "")$(".viewreceiptdt").val("1");
	if(rcptstrt == undefined || rcptstrt == "")$(".viewreceiptstart").val("0");
	
	rcptstrt = $(".viewreceiptstart").val();
	rcptdt = $(".viewreceiptdt").val();
	
	var rcptmonths = 
	{
		"Jan":1,"Feb":2,"Mar":3,"Apr":4,"May":5,"Jun":6,"Jul":7,"Aug":8,"Sep":9,"Oct":10,"Nov":11,"Dec":12
	}
	var rcptdate = rcptyr+"/"+rcptmonths[rcptmnt]+"/"+rcptdt;
	
		var url = "?main=receipts&action=totals&date="+rcptdate;
		$.get( url, function( data ) {
			$("#viewreceiptspanel").html("<div>"+data+"</div>");
			
			var ind = (ckcreceiptvar == 0)?"id":"ind";
			var url = "index.php?main=receipts&action=newview&submit=submit&"+ind+"="+rcptstrt+"&date="+rcptdate;
			var iframe = "<iframe id=\"viewreceiptiframe\"src=\""+url+"\" width=\"90%\" height=\"270px\"></iframe>";
			$("#viewreceiptspanel").append(iframe);
	
	
		},"html");
		/**/
	//$(".viewreceiptfoward").focus();
	/**/
}

var viewreceiptnav_next = function()
{
	var rcptstrt = $(".viewreceiptstart").val();
	$(".viewreceiptstart").val(++rcptstrt);
	viewreceiptnav_go();
}

var viewreceiptnav_prev = function()
{
	var rcptstrt = $(".viewreceiptstart").val();
	if(rcptstrt>0)rcptstrt--;
	$(".viewreceiptstart").val(rcptstrt);
	viewreceiptnav_go();
}

var viewreceiptdelete = function()
{
	var url = $("#viewreceiptiframe").attr("src");
	if(url == undefined)return false;
	var newurl = url.replace("newview","newdelete");
	$.get( newurl, function( data ) {
			viewreceiptnav_go();
	},"html");
}


var viewreceiptdownload = function()
{
	var rcptyr = $(".viewreceiptyr").val();
	var rcptmnt = $(".viewreceiptmt").val();
	var rcptdt = $(".viewreceiptdt").val();
	
	
	if(rcptdt == undefined || rcptdt == "")$(".viewreceiptdt").val("1");
	
	rcptdt = $(".viewreceiptdt").val();
	var rcptmonths = 
	{
		"Jan":'01',"Feb":'02',"Mar":'03',"Apr":'04',"May":'05',"Jun":'06',"Jul":'07',"Aug":'08',"Sep":'09',"Oct":10,"Nov":11,"Dec":12
	}
	var rcptdate = rcptyr+"-"+rcptmonths[rcptmnt]+"-"+rcptdt;
	
	//var mydate = (ckcreceiptvar == 0)?"id":"ind";
	var mydate = "date";
	var url = "?main=receipts&action=download&"+mydate+"="+rcptdate;
	console.log(url);
	var iframe = "<iframe id=\"viewreceiptiframe\"src=\""+url+"\" width=\"90%\"></iframe>";
	$("#viewreceiptspanel").html(iframe);
	console.log(url);
	$.get( url, function( data ) {
		html_statement = data;
		//$("#viewreceiptspanel").html(data);
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
		tmpcombined=tmpcombined.split(",").join("");
		receipt["Combined"] = combinedfactor * tmpcombined;//receipt["Combined"];
		console.log("first: "+ tmpcombined);
		console.log("sec: "+ receipt["Combined"]);
		//receipt["Combined"] = receipt["Combined"];
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
		$.get( url, function( data ) {$("#viewreceiptspanel").html(url+"<br><br>");},"html");
		//http://localhost/csyber/finance/jkusdatr/?main=receipts&action=add&submit=submit&name=Carol%20Kade&tithe=700&Ind=21
	});
	
}

var viewreceiptupdate = function()
{
	var rcptyr = $(".viewreceiptyr").val();
	var rcptmnt = $(".viewreceiptmt").val();
	var rcptdt = $(".viewreceiptdt").val();
	
	
	if(rcptdt == undefined || rcptdt == "")$(".viewreceiptdt").val("1");
	
	rcptdt = $(".viewreceiptdt").val();
	var rcptmonths = 
	{
		"Jan":1,"Feb":2,"Mar":3,"Apr":4,"May":5,"Jun":6,"Jul":7,"Aug":8,"Sep":9,"Oct":10,"Nov":11,"Dec":12
	}
	var rcptdate = rcptyr+"/"+rcptmonths[rcptmnt]+"/"+rcptdt;
	
	//var mydate = (ckcreceiptvar == 0)?"id":"ind";
	var mydate = "date";
	var url = "?main=receipts&action=upload&"+mydate+"="+rcptdate;
	var iframe = "<iframe id=\"viewreceiptiframe\"src=\""+url+"\" width=\"90%\"></iframe>";
	$("#viewreceiptspanel").html(iframe);
}

var importemails1 = function()
{
	$("#receiptspanrightheader").html("Import Emails from Excel");
	url = "extras.php?action=import&date=dontcare";
	//alert("a");
	var iframe = "<iframe id=\"viewreceiptiframe\"src=\""+url+"\" width=\"90%\"></iframe>";
	//$("#viewreceiptspanel").html("here");
	$("#receiptspancontent").html("DEPRECATED. Use other button");
	//$("#viewreceiptspanel").html(iframe);
	console.log(url);
}

var emailreceipts = function()
{
	var rcptyr = $(".viewreceiptyr").val();
	var rcptmnt = $(".viewreceiptmt").val();
	var rcptdt = $(".viewreceiptdt").val();
	
	
	if(rcptdt == undefined || rcptdt == "")$(".viewreceiptdt").val("1");
	
	rcptdt = $(".viewreceiptdt").val();
	
	var rcptmonths = 
	{
		"Jan":1,"Feb":2,"Mar":3,"Apr":4,"May":5,"Jun":6,"Jul":7,"Aug":8,"Sep":9,"Oct":10,"Nov":11,"Dec":12
	}
	var rcptdate = rcptyr+"/"+rcptmonths[rcptmnt]+"/"+rcptdt;
	//var mydate = (ckcreceiptvar == 0)?"id":"ind";
	var mydate = "ndate";
	var url = "?main=receipts&action=email&"+mydate+"="+rcptdate;
	var iframe = "<iframe id=\"viewreceiptiframe\"src=\""+url+"\" width=\"90%\"></iframe>";
	$("#viewreceiptspanel").html(iframe);
}
var receiptimg = function()
{
	var rcptyr = $(".viewreceiptyr").val();
	var rcptmnt = $(".viewreceiptmt").val();
	var rcptdt = $(".viewreceiptdt").val();
	
	
	if(rcptdt == undefined || rcptdt == "")$(".viewreceiptdt").val("1");
	
	rcptdt = $(".viewreceiptdt").val();
	
	var rcptmonths = 
	{
		"Jan":1,"Feb":2,"Mar":3,"Apr":4,"May":5,"Jun":6,"Jul":7,"Aug":8,"Sep":9,"Oct":10,"Nov":11,"Dec":12
	}
	var rcptdate = rcptyr+"/"+rcptmonths[rcptmnt]+"/"+rcptdt;
	//var mydate = (ckcreceiptvar == 0)?"id":"ind";
	var mydate = "ndate";
	var url = "?main=receipts&action=viewimgall&submit=submit&view=true&"+mydate+"="+rcptdate;
	var iframe = "<iframe id=\"viewreceiptiframe\"src=\""+url+"\" width=\"90%\"></iframe>";
	$("#viewreceiptspanel").html(iframe);
}

var importemails = function()
{
	var rcptyr = $(".viewreceiptyr").val();
	var rcptmnt = $(".viewreceiptmt").val();
	var rcptdt = $(".viewreceiptdt").val();
	
	
	if(rcptdt == undefined || rcptdt == "")$(".viewreceiptdt").val("1");
	
	rcptdt = $(".viewreceiptdt").val();
	
	var rcptmonths = 
	{
		"Jan":1,"Feb":2,"Mar":3,"Apr":4,"May":5,"Jun":6,"Jul":7,"Aug":8,"Sep":9,"Oct":10,"Nov":11,"Dec":12
	}
	var rcptdate = rcptyr+"/"+rcptmonths[rcptmnt]+"/"+rcptdt;
	
	//var mydate = (ckcreceiptvar == 0)?"id":"ind";
	var mydate = "ndate";
	var url = "extras.php?action=import&date="+rcptdate;
	var iframe = "<iframe id=\"viewreceiptiframe\"src=\""+url+"\" width=\"90%\"></iframe>";
	$("#viewreceiptspanel").html(iframe);
}

var viewreceiptprint = function()
{
	$("#receiptspanrightheader").html("Print Receipts");
	url = "?main=page&sub=viewreceiptsprint&submit=submit";
	$.get( url, function( data ) {
			$("#receiptspancontent").html(data);
	},"html");
	console.log("printing");
}

var viewreceiptprintbtn = function()
{
	var rcptyr = $(".viewreceiptyr").val();
	var rcptmnt = $(".viewreceiptmt").val();
	var rcptdt = $(".viewreceiptdt").val();
	
	
	if(rcptdt == undefined || rcptdt == "")$(".viewreceiptdt").val("1");
	
	rcptdt = $(".viewreceiptdt").val();
	
	var rcptmonths = 
	{
		"Jan":1,"Feb":2,"Mar":3,"Apr":4,"May":5,"Jun":6,"Jul":7,"Aug":8,"Sep":9,"Oct":10,"Nov":11,"Dec":12
	}
	var rcptdate = rcptyr+"/"+rcptmonths[rcptmnt]+"/"+rcptdt;
	
	//var mydate = (ckcreceiptvar == 0)?"id":"ind";
	var mydate = "ndate";
	var url = "index.php?main=receipts&action=print&submit=submit&"+mydate+"="+rcptdate;
	var iframe = "<iframe id=\"viewreceiptiframe\"src=\""+url+"\" width=\"90%\"></iframe>";
	$("#viewreceiptspanel").html(iframe);
}

var receiptsubmit = function()
{
	
}