$(".modifysignup").click(function(){modifysignup($(this).attr("alt"));});
$(".modifycancel").click(function(){load_previous_page();});
$(".modifysubmit").click(function(){modifysubmit();});
$(".modifyalter").click(function(){modifyalter();});
$(".alterselfsubmit").click(function(){alterselfsubmit();});
$(".modifyedit").click(function(){modifyedit();});



var ckcreceiptvar;

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

var viewreceiptupdate = function()
{
	var url = $("#viewreceiptiframe").attr("src");
	//alert(url);
	if(url == undefined)return false;
	var newurl = url.replace("newview","upload");
	//alert(newurl);
	console.log(newurl);
	$.get( newurl, function( data ) {
			//viewreceiptnav_go();
	},"html");
}

var viewreceiptprint = function()
{
	$("#receiptspanrightheader").html("View Receipts");
	url = "?main=page&sub=viewreceiptsprint&submit=submit";
	$.get( url, function( data ) {
			$("#receiptspancontent").html(data);
	},"html");
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