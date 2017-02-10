<?php
include_once ("connection.php");
define("NEWPMRELEASEREQUEST","newFirstReleaseRequest");
define("REMOVEFIRSTRELEASEREQUEST","removeFirstReleaseRequest");
define("NEWSECONDRELEASEREQUEST","newSecondReleaseRequest");
define("REMOVESECONDRELEASEREQUEST","removeSecondReleaseRequest");


function getFirstReleaser($pmid){
	$query = "SELECT  `users`.`username` FROM `pressreleaseconnection`,  `users` WHERE `pressreleaseID` = ".$pmid." AND `userID` =  `users`.`id`;";
	$checkdata = mysql_query ( $query );
	$username = array();
	if(mysql_num_rows($checkdata)>=1){
		while($row = mysql_fetch_object($checkdata))
			{
			array_push($username,$row->username);
			}	
	}
	return $username;
}

function getSecondReleaser($pmid){
	$query = "SELECT `username` FROM `users`, `pressrelease` WHERE `pressrelease`.`id` = ".$pmid." AND `users`.`distributor` =1 AND `pressrelease`.`confirmationid1` <> `users`.`id`;";
	$checkdata = mysql_query ( $query );
	$username = array();
	if(mysql_num_rows($checkdata)>=1){
		while($row = mysql_fetch_object($checkdata))
			{
			array_push($username,$row->username);
			}	
	}
	return $username;
}

function newPMReleaseRequest ($pmid){
	$query = "SELECT subject FROM `pressrelease` WHERE id =".$pmid;
	$checkdata = mysql_query($query);
	$subject = 'leer';
	$usernameArr = getFirstReleaser($pmid);


	while($row = mysql_fetch_object($checkdata))
		{	
		$subject=$row->subject;
		}

	$msg = array
	(   'id' => $pmid,
	    'pmTitle' => $subject,
	    'msgtype' => NEWPMRELEASEREQUEST
	);

	foreach ($usernameArr as &$username) {
		$fields = array
		( 'to'=> '/topics/user'.$username,
		  'data'            => $msg);
		pushMsgFirefox($fields);
		pushMsg($fields);
	}
}


function removePMReleaseRequest ($pmid){
	$usernameArr = getFirstReleaser($pmid);

	$msg = array
	(   'id' => $pmid,
	     'msgtype' => REMOVEFIRSTRELEASEREQUEST
	);
	foreach ($usernameArr as &$username) {
		$fields = array
		( 'to'=> '/topics/user'.$username,
		  'data'            => $msg);
		pushMsgFirefox($fields);
		pushMsg($fields);
	}
}

function newPM2ndReleaseRequest ($pmid){
	$query = "SELECT subject FROM `pressrelease` WHERE id =".$pmid;
	$checkdata = mysql_query($query);
	$subject = 'leer';
	$usernameArr = getSecondReleaser($pmid);
	while($row = mysql_fetch_object($checkdata))
		{	
		$subject=$row->subject;
		}

	$msg = array
	(   'id' => $pmid,
	    'pmTitle' => $subject,
	    'msgtype' => NEWSECONDRELEASEREQUEST,
	    'firstReleaseingUsername' =>  getFirstReleaser($pmid)
	);

	foreach ($usernameArr as &$username) {
		echo "<!-- USERNAME2 $username -->";
		$fields = array
		( 'to'=> '/topics/user'.$username,
		  'data'            => $msg);
		pushMsgFirefox($fields);
		pushMsg($fields);
	}
}

function removePM2ndReleaseRequest ($pmid){
	$usernameArr = getSecondReleaser($pmid);
	$msg = array
	(   'id' => $pmid,
	     'msgtype' => REMOVESECONDRELEASEREQUEST
	);
	foreach ($usernameArr as &$username) {
	echo "\n<!-- REMOVE USERNAME: $username -->\n";
		$fields = array
		( 'to'=> '/topics/user'.$username,
		  'data'            => $msg);
		pushMsgFirefox($fields);
		pushMsg($fields);
	}
}


function pushMsg($fields){
	$headers = array ('Content-Type: application/json','Authorization: key=' . FIREBASE_API_ACCESS_KEY);
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch );
	curl_close( $ch );
	return $result;
}

function pushMsgFirefox($fields){
$fields2 = array( 'action' => 'chatMsg',
		'name' => 'obj.name',
		'msg' => 'obj.msg');
	$query = "SELECT pushEndpoint FROM `pushClients` WHERE `type`=2;";
	$checkdata = mysql_query ( $query );
	if (mysql_num_rows ( $checkdata ) >= 1) {
		while ( $row = mysql_fetch_object ( $checkdata ) ) {
			$loggedinuserid = $take;
			$pushEndpoint = $row->pushEndpoint;
			$ch = curl_init();  
			$url = $pushEndpoint;
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('ttl: 60','Content-Length: 0'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields2 ) );
			$result = curl_exec($ch);
			curl_close($ch);
		}
	}
return $result;
}

//echo newPMReleaseRequest (67);


?>



