$(".homeadministrative").click(function(){user_modify();});
$(".homereceiptsbtn").click(function(){homereceiptsbtn();});
$(".homeadministrative").click(function(){user_modify();});
$(".homeadministrative").click(function(){user_modify();});
$(".homeadministrative").click(function(){user_modify();});


var homereceiptsbtn = function()
{
	url = "index.php?main=page&sub=receipts";
	$.get( url, function( data ) {
	console.log(data);
			set_previous_page("index.php?main=page&sub=loggedin");
			$("#content").html(data);
	},"html");
}
