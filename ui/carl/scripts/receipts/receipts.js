$(document).ready(function(){
if(ckcreceiptvar == 0)$(".viewreceiptdelete").hide(); //cant delete ckc receipts
	$(".start").focus();
	$(".viewreceiptyr").focus();
});

$(".viewreceiptbtn").click(function(){viewreceiptnav_go();});
$(".viewreceiptfoward").click(function(){viewreceiptnav_next();});
$(".viewreceiptback").click(function(){viewreceiptnav_prev();});
$(".viewreceiptdelete").click(function(){viewreceiptdelete();});
$(".viewreceiptupdate").click(function(){viewreceiptupdate();});

/*      */
$(".viewreceiptprintbtn").click(function(){viewreceiptprintbtn();});