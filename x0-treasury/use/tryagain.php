<?php

   $url = "http://ckcfinancialsystem.org/login.php";

    //Open the Curl session
    $session = curl_init($url);

    // If it's a GET, put the GET data in the body
	//login.php
	//username
	//password
	//login
   // if ($_GET['service']) {
     //   //Iterate Over GET Vars
      //  $postvars = '';
        //foreach($_GET as $key=>$val) {
          //  if($key!='service') {
            //    $postvars.="$key=$val&";
            //}
        //}
		$postvars = "username=Jkusda&password=3490jk&login=login";
        curl_setopt ($session, CURLOPT_POST, true);
        curl_setopt ($session, CURLOPT_POSTFIELDS, $postvars);
    //}


    //Create And Save Cookies
    $tmpfname = dirname(__FILE__).'/cookie.txt';
    curl_setopt($session, CURLOPT_COOKIEJAR, $tmpfname);
   // curl_setopt($session, CURLOPT_COOKIEFILE, $tmpfname);

    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_FOLLOWLOCATION, false);

    // EXECUTE
    $json = curl_exec($session);
		$json = sprintf("echo %s echo", $json);
		//var_dump($json);
        //echo $json;
    curl_close($session);
	exit();