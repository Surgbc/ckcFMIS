$(document).ready(function(){
	csyber_init();
	//$("#identifier").hide();
});


/*
 * csyber global vars
 * 
 */

var stack = {}; //holds previous pages in $("#content")
var glob_user = 0; //show if there is a user logged in. Username: Group
/*
 * functions
 */
var csyber_init = function()
{
	fill_page(0);
}

 
var fill_page = function(ind)
{
	var i = ind;
	var url = "index.php?main=page&sub=watever&ind="+i;
	//alert(url);
	$.get( url, function( data ) {
		//alert(data);
		switch(i)
		{
			case 0:
				glob_user =(data=="GUEST")?0:data;
				show_user();
				break;
			case 1:
				fill_inside_out_page(data);
				break;	
			case 2:
				fill_login_form(data);
				break;
				//insidepage
		}
		//alert(data);
		if(data != "EOR"){fill_page(++i);}
	},"html");
}

var fill_inside_out_page = function(data)
{
	$("#insidepage").html(data);
	$("#content").html($("#insidepage").html());
}

var fill_login_form = function(data)
{
	$("#loginform").html(data);
}
/*
var filled_logged_out_page = function(data)
{
	$("#loggedoutpage").html(data["page"]);
	$("#loginform").html(data["loginform"]);
	$("#content").html($("#loggedoutpage").html());
}

var fill_logged_in_page = function(data)
{
	$("#loggedinpage").html(data["page"]);
	$("#content").html($("#loggedinpage").html());
	glob_user = data["user"];
	show_user();
}
*/
var show_user = function()
{
	var user = glob_user;
	if(user != 0)
	{
		var tmp = user.split(":");
		var username = tmp[0];
		var group = tmp[1];
		$("#logout").html("Logout: ");
		$("#username").html(username);
		$("#from").html(" From "+group);
		$("#identifier").html($("#identifier_hd").html());
	}else $("#identifier").html("");
}

var logout = function()
{	
	url = "index.php?main=auth&action=logout";
	$.get( url, function( data ) {glob_user=0;fill_page(0);},"html");
}

var user_modify = function()
{	
	url = "index.php?main=auth&action=modify";
	$.get( url, function( data ) {
		page = $("#content").html();
		set_previous_page("index.php?main=page&sub=loggedin");
		$("#content").html(data);
	},"html");
}

var set_previous_page = function(page)
{
	var i = 0;
	//check. other ways to find length of prev_page
	$.each(prev_page, function(a,b){i++;});
	prev_page[i] = page;
}

var load_previous_page = function()
{
	
	var i = 0;
	$.each(prev_page, function(a,b){page = prev_page[i++];});
	//$("#content").html(page);
	//delete(prev_page[i];)
	var tmp = {};
	i--;
	
	for(j=0;j<i;j++)tmp[j] = prev_page[j];
	prev_page = tmp;	
	var i=0;
	var url = page;
	$.get( url, function( data ) {
		$("#content").html(data);
	},"html");
	/**/
}
/*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*/

var login_error = function(error_str)
{
	$("#uman").html(error_str);
}

/*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*/

var modifysignup = function(user)
{	
	gb_user = user;
	url = "index.php?main=auth&action=modifyforms&sub=signup";
	$.get( url, function( data ) {
			page = $("#content").html();
			set_previous_page("index.php?main=auth&action=modify");
			$("#content").html(data);
	},"html");
}

var modifysubmit = function()
{
	login_error("");
	var user = $("#login_user").val();
	var pass = $("#login_pass").val();
	var name = $("#login_name").val();
	
	url = "?main=auth&submit=submit&user="+user+"&pass="+pass+"&name="+name+"&action=signup&group="+gb_user;
	
	$.get( url, function( data ) {
		var tmp = data.split(":");
		var code = tmp[0];
		if(code == 7)
		{
			load_previous_page();
		}
		else
		{
			login_error(data);
		}
		
		/*if(code != 7)
		{
			$("#login_user").val("");
			$("#login_pass").val("");
			$("#login_name").val("");
		}else load_previous_page();
		*/
	},"html");
}

var alterselfsubmit = function()
{
	var user = $("#login_user").val();
	var pass = $("#login_pass").val();
	var name = $("#login_name").val();
	
	url = "?main=auth&submit=submit&user="+user+"&pass="+pass+"&name="+name+"&action=alter";
	$.get( url, function( data ) {
		var tmp = data.split(":");
		var code = tmp[0];
		if(code != 7)
		{
			login_error(data);
			//$("#login_user").val("");
			//$("#login_pass").val("");
			//$("#login_name").val("");
		}else load_previous_page();
	},"html");
}

var modifyalter = function()
{
	url = "index.php?main=auth&action=modifyforms&sub=alterself";
	$.get( url, function( data ) {
			//page = $("#content").html();
			//set_previous_page(page);
			set_previous_page("index.php?main=auth&action=modify");
			$("#content").html(data);
	},"html");
}

var modifyedit = function()
{
	url = "index.php?main=auth&action=modifyforms&sub=modifyedit";
	$.get( url, function( data ) {
			//page = $("#content").html();
			set_previous_page("index.php?main=auth&action=modify");
			$("#content").html(data);
	},"html");
}


/*88888888888888888888888888*//*88888888888888888888888888*//*RECEIPTS*//*88888888888888888888888888*//*88888888888888888888888888*/


/*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*//*88888888888888888888888888*/

var push = function(reg)
{
	var localstack = stack;
	var i= 0;
	$.each(localstack, function(a,b){i++;});
	localstack[i] = reg;
	
	stack = localstack;
	return true;
}




//check if logged in, then load appropriate page
var vlogged_in = 0;
var glob_user = 0;
var prev_page = {};
var gb_user = 1;			//Creating new user. 0 for treasurer, 1 for deacon(ness)
var modifyedituid = 0;

var logged_in = function()
{
	url = "index.php?main=auth&action=getuser";
	$.get( url, function( data ) {
		if(data.substr(0,3) == 105){vlogged_in = 0; glob_user = 0;}
		else{ vlogged_in = 1; glob_user = data;}
		//load_page();
		fill_page();
	},"html");
}

var load_page = function()
{
	if(vlogged_in == 0)url = "index.php?main=page&sub=loggedout";
	else url = "index.php?main=page&sub=loggedin";
	$.get( url, function( data ) {
		show_user();
		$("#content").html(data);
	},"html");
}

var loading = function()
{
	/*check*/
	/*
	var load = "<div id=\"loading\">Loading...</div>";
	$("#content").append(load);
	*/
}

var x_show_user = function()
{
	var user = glob_user;
	if(user != 0)
	{
		var tmp = user.split(":");
		var username = tmp[0];
		var group = tmp[1];
		$("#identifier").show();
		$("#logout").html("Logout: ");
		$("#username").html(username);
		$("#from").html(" From "+group);
		//var div = "<div><span id =\"logout\">Logout: </span><span id =\"username\">"+username+"</span><span id =\"from\"> From "+group+"</span></div>";
		//$("#identifier").html(div);	
	}else $("#identifier").hide();
}




/*
	
 */
