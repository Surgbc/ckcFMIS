$(".reags_submit_login").click(function(){submit_login();});

var submit_login = function()
{
	var user = $("#login_user").val();
	var pass = $("#login_pass").val();
	
	url = "index.php?main=auth&action=login&submit=submit&user="+user+"&pass="+pass;
	$.get( url, function( data ) {
		if(data.substr(0,3) == 104)
		{
			login_error("Wrong Username and password combination");
			//$("#login_user").val("");
			$("#login_pass").val("");
			$("#login_pass").focus();
		}
		else
		{ if(data.substr(0,1) == 7)
			{
				fill_page(0);
			}
			else
			{
				login_error("Unknown Error. Please try again.");
			}	
		}
	},"html");
}