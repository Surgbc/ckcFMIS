$(".modifysignup").click(function(){modifysignup($(this).attr("alt"));});
$(".modifycancel").click(function(){load_previous_page();});
$(".modifysubmit").click(function(){modifysubmit();});
$(".modifyalter").click(function(){modifyalter();});
$(".alterselfsubmit").click(function(){alterselfsubmit();});
$(".modifyedit").click(function(){modifyedit();});

//$(".selectname").change(function(){alert($(this).attr("class"));});

var modifyeditselect = function(tmpuser)
{
	modifyedituid = tmpuser.attr("uid");
	$("#login_userdep").val(tmpuser.attr("dep"));
}



var modifyedit = function()
{
	url = "index.php?main=auth&action=modifyforms&sub=modifyedit";
	$.get( url, function( data ) {
			page = $("#content").html();
			set_previous_page(page);
			$("#content").html(data);
	},"html");
}

var alterselfsubmit = function()
{
	var user = $("#login_user").val();
	var pass = $("#login_pass").val();
	var name = $("#login_name").val();
	
	url = "?main=auth&submit=submit&user="+user+"&pass="+pass+"&name="+name+"&action=alter";
	$.get( url, function( data ) {
		alert(data);
		var tmp = data.split(":");
		var code = tmp[0];
		if(code != 7)
		{
			$("#login_user").val("");
			$("#login_pass").val("");
			$("#login_name").val("");
		}else load_previous_page();
	},"html");
}

var modifyalter = function()
{
	url = "index.php?main=auth&action=modifyforms&sub=alterself";
	$.get( url, function( data ) {
			page = $("#content").html();
			set_previous_page(page);
			$("#content").html(data);
	},"html");
}
var modifysignup = function(user)
{	
	gb_user = user;
	url = "index.php?main=auth&action=modifyforms&sub=signup";
	$.get( url, function( data ) {
			page = $("#content").html();
			set_previous_page(page);
			$("#content").html(data);
	},"html");
}	

var modifysubmit = function()
{
	var user = $("#login_user").val();
	var pass = $("#login_pass").val();
	var name = $("#login_name").val();
	
	url = "?main=auth&submit=submit&user="+user+"&pass="+pass+"&name="+name+"&action=signup&group="+gb_user;
	$.get( url, function( data ) {
		alert(data);
		var tmp = data.split(":");
		var code = tmp[0];
		if(code != 7)
		{
			$("#login_user").val("");
			$("#login_pass").val("");
			$("#login_name").val("");
		}else load_previous_page();
	},"html");
}

