$(".modifyeditsubmitpromote").click(function(){modifyeditsubmitpromote();});
$(".modifyeditsubmitdemote").click(function(){modifyeditsubmitdemote();});
$(".modifyeditsubmitdelete").click(function(){modifyeditsubmitdelete();});

var modifyeditsubmitpromote = function()
{
	url = "index.php?main=auth&action=promote&user="+modifyedituid;
	$.get( url, function( data ) {
		var tmp = data.split(":");
		var code = tmp[0];
		if(code == 7)
		{
			$("#selectusers").html("");
			load_users();
		}else alert("error! please try again.");		
	},"html");
}

var modifyeditsubmitdemote = function()
{
	url = "index.php?main=auth&action=demote&user="+modifyedituid;
	$.get( url, function( data ) {
		var tmp = data.split(":");
		var code = tmp[0];
		if(code == 7)
		{
			$("#selectusers").html("");
			load_users();
		}else alert("error! please try again.");		
	},"html");
}

var modifyeditsubmitdelete = function()
{
	url = "index.php?main=auth&action=drop&user="+modifyedituid;
	$.get( url, function( data ) {
		var tmp = data.split(":");
		var code = tmp[0];
		if(code == 7)
		{
			$("#selectusers").html("");
			load_users();
		}else alert("error! please try again.");		
	},"html");
}

var load_users = function()
{
	url = "index.php?main=auth&action=getusers";
		//alert(url);
	$.get( url, function( data ) {
		//alert(data)
			$.each(data, function(a,b){
			//alert(a);
				
				/*$.each(a, function(c,d){
					//alert(c);
					});
					*/
			var tmp = "<tr><td class = \"selectuserstd\" uid="+a+" dep="+b["group"]+">"+b["name"]+"</td></tr>";
			//alert(tmp);
			$("#selectusers").append(tmp);
			});
			$("#content	").append("<script type=text/javascript src=\"scripts/modifyeditlaterscripts.js\"></script>");
	},"json");
}

load_users();

