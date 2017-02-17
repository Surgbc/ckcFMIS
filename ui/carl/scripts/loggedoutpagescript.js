var txt = "";
var requiredtxt = "JKUSDA";
var objs = {}
var i = 0;

$(".login-button").click(function(){play($(this));});
$(".login-button1").click(function(){play($(this));});
$(".login-button2").click(function(){play($(this));});
$(".login-button3").click(function(){play($(this));});
$(".login-button4").click(function(){play($(this));});
$(".login-button5").click(function(){play($(this));});
$(".login-button-help").click(function(){user_manual();});


var user_manual = function()
{
	alert("requesting for manual");
}

var play = function (obj)
{
	var letter = obj.attr("alt");
	obj.css("background", "white");
	obj.html(letter);
	txt += letter;
	objs[i] = obj;
	i++;
	$(".loggedouthelp").html(txt+"<br><br><br>");
	if(i === 6)
	{
		if(txt == requiredtxt){push("loggedout");load_login_form();}
		else 
		{
			$.each(objs, function(a, obj){obj.html("?&nbsp;"); obj.css("background", "cyan");});
			$(".loggedouthelp").html("Wrong Passcode. Try again. <br><br><br>");
			txt = "";
			i = 0;
		}
	}
}

var load_login_form = function()
{
	$("#content").html($("#loginform").html());
}