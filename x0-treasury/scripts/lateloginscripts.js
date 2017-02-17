$(".reags_submit_login").click(function(){submit_login();});

var submit_login = function()
{
	var user = $("#login_user").val();
	var pass = $("#login_pass").val();
	
	url = "index.php?main=auth&action=login&submit=submit&user="+user+"&pass="+pass;
	$.get( url, function( data ) {
		if(data.substr(0,3) == 104)
		{
			$("#login_user").val("");
			$("#login_pass").val("");
		}
		else
		{
			logged_in();
			vlogged_in = 1;
			load_page();
			
		}//$("#content").html(data);
	},"html");
}	