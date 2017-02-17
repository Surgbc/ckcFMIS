$(document).ready(function(){
	$("#identifier").hide();
	loading();
	logged_in();
});

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
		//alert(data);
		if(data.substr(0,3) == 105){vlogged_in = 0; glob_user = 0;}
		else{ vlogged_in = 1; glob_user = data;}
		load_page();
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

var show_user = function()
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

var load_previous_page = function()
{
	var i = 0;
	$.each(prev_page, function(a,b){page = prev_page[i++];});
	//alert("num pages="+i+"page="+page);
	$("#content").html(page);
	//delete(prev_page[i];)
	var tmp = {};
	i--;
	for(j=0;j<i;j++)tmp[j] = prev_page[j];
	prev_page = tmp;	
	var i=0;
	//$.each(prev_page, function(a,b){page = prev_page[i++];alert("num pages="+i+"page="+page);});
}

var set_previous_page = function(page)
{
	
	var i = 0;
	//check. other ways to find length of prev_page
	$.each(prev_page, function(a,b){i++;});
	prev_page[i] = page;
	//alert("num pages="+i+"page="+prev_page[i]);
	
}

var logout = function()
{	
	url = "http://localhost/treasury/?main=auth&action=logout";
	$.get( url, function( data ) {logged_in();},"html");
}

/*
	
 */
var user_modify = function()
{	
	url = "http://localhost/treasury/?main=auth&action=modify";
	$.get( url, function( data ) {
		page = $("#content").html();
		set_previous_page(page);
		$("#content").html(data);
	},"html");
}



$("#logout").click(function(){logout();});
$("#username").click(function(){user_modify();});