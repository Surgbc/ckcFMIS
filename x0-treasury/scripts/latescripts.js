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
	/*var letter = obj.html();
	var requiredletter = txt[i];
	
	//alert(requiredletter+"=="+letter);
	if(letter == requiredletter)
	{
		//alert(requiredletter+"=="+letter);
		obj.hide();
		objs[i] = obj;//obj.attr("class");;
		i++;
	}
	else
	{
		$.each(objs, function(a, obj){obj.show();});
		i = 0;
	}
	
	if(i === 6)load_login_form();
	//alert("log in");
	*/
	var letter = obj.attr("alt");
	obj.css("background", "white");
	obj.html(letter);
	txt += letter;
	objs[i] = obj;
	i++;
	if(i === 6)
	{
		if(txt == requiredtxt)load_login_form();
		else 
		{
			$.each(objs, function(a, obj){obj.html("&nbsp;"); obj.css("background", "cyan");});
			txt = "";
			i = 0;
		}
	}
}

var load_login_form = function()
{
	loading();
	url = "index.php?main=page&sub=loginform";
	$.get( url, function( data ) {
		$("#content").html(data);
	},"html");
}	