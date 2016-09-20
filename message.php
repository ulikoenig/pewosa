<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$pagetitle= "Pressemitteilung Bearbeiten";
include_once("header.php");
function fetchintvar ($varname,$defaultvalue)
{	
	If (isset($_POST[$varname])) 
		{$returnvar = $_POST[$varname];
		} else 	If(isset($_GET[$varname])) 	
		{ $returnvar = $_GET[$varname];
		};
	if ( ! is_int ($returnvar ) ) 
		{$returnvar = intval($returnvar);
		}
	if (empty($returnvar)){$returnvar = $defaultvalue;}
	if ($returnvar=="" ){$returnvar = $defaultvalue;}
	return  $returnvar;
}


function fetchstringvar ($varname,$defaultvalue)
{
	If (isset($_POST[$varname])) 
		{$returnvar = $_POST[$varname];
		} else 	If(isset($_GET[$varname])) 	
		{ $returnvar = $_GET[$varname];
		};
	if ( ! is_string ($returnvar ) ) 
		{ $returnvar = $defaultvalue;
		}
	return  $returnvar;
}

function fetchboolvar ($varname)
{	
	If (isset($_POST[$varname])) 
		{$returnvar = $_POST[$varname];
		} else 	If(isset($_GET[$varname])) 	
		{ $returnvar = $_GET[$varname];
		};
	//echo "fetchboolvar $varname: $returnvar";
	if (!empty($returnvar)){$returnvar = true;} else {$returnvar = false;}
	return  $returnvar;
}

function userIsPressagend($user){
	if (empty($user)) die("ERROR: function userIsPressagend parameter user must NOT be empty!");
	$sqluser = mysql_real_escape_string ($user);
	$query = "SELECT id FROM `users` WHERE pressagent = 1 AND id = $sqluser";
	$checkdata = mysql_query($query) or die("Fehler Press:$query ".mysql_error());
//	echo "<!--userIsPressagend($user) \n $query	\n checkdata	$checkdata -->"; 
	if(mysql_num_rows($checkdata)==1){
		while($row = mysql_fetch_object($checkdata))
			{
				return true;
			}	
	}
	return false;
}

function userIsDistributor($user){
	if (empty($user)) die("ERROR: function userIsDistributor parameter user must NOT be empty!");
	$sqluser = mysql_real_escape_string ($user);
	$query = "SELECT id FROM `users` WHERE distributor = 1 AND id = $sqluser";
	$checkdata = mysql_query($query) or die("Fehler Dist:".mysql_error());
//	echo "<!-- userIsDistributor($user) \n $query	\n checkdata	$checkdata -->"; 
	if(mysql_num_rows($checkdata)==1){
		while($row = mysql_fetch_object($checkdata))
			{
				return true;
			}	
	}
	return false;
}

function userIsAdmin($user){
	if (empty($user)) die("ERROR: function userIsAdmin parameter user must NOT be empty!");
	$sqluser = mysql_real_escape_string ($user);
	$query = "SELECT ID FROM `users` WHERE admin = 1 AND ID = $sqluser";
	$checkdata = mysql_query($query) or die("Fehler:".mysql_error());
	if(mysql_num_rows($checkdata)==1){
		while($row = mysql_fetch_object($checkdata))
			{
				return true;
			}	
	}
	return false;
}




function userIsDistributorOne($user,$pressreleaseID){
	if (empty($user)) die("ERROR: function userIsDistributorOne parameter user must NOT be empty!");
	if (empty($pressreleaseID)) die("ERROR: function userIsDistributorOne parameter pressreleaseID must NOT be empty!");

	$sqlpressreleaseID = mysql_real_escape_string ($pressreleaseID);
	$sqluser = mysql_real_escape_string ($user);
	$query = "SELECT id FROM `pressreleaseconnection` WHERE pressreleaseID = $sqlpressreleaseID AND userID = $sqluser";
	$checkdata = mysql_query($query) or die("Fehler:".mysql_error());
	if(mysql_num_rows($checkdata)==1){
		while($row = mysql_fetch_object($checkdata))
			{
				return true;
			}	
	}
	return false;
}


function getPmState($pressreleaseID){
	//catch new PMs
	if (empty($pressreleaseID)) die("ERROR: function getPmState parameter pressreleaseID must NOT be empty!");
	if (!is_numeric($pressreleaseID)) die("ERROR: function getPmState parameter pressreleaseID must be a Number!");


	if ($pressreleaseID == -1){ return 0;}
	$sqlpressreleaseID = mysql_real_escape_string ($pressreleaseID);
	$query = "SELECT sendstate FROM `pressrelease` WHERE id = $sqlpressreleaseID";
	$checkdata = mysql_query($query) or die("Fehler:".mysql_error());
	$sendstate = 0;
	//0= Entwurf: -1=pending -2=sent -3=readyforgo
//	echo "<!-- pressreleaseID = $pressreleaseID\n  $query \n	checkdata	$checkdata -->"; 
	if(mysql_num_rows($checkdata)==1){
		while($row = mysql_fetch_object($checkdata))
			{
				$sendstate=$row->sendstate;
			}	
	}
	return intval($sendstate);
}


function getFirstAuth($pressreleaseID){
	//catch new PMs
	if (empty($pressreleaseID)) die("ERROR: function getPmState parameter pressreleaseID must NOT be empty!");
	if (!is_numeric($pressreleaseID)) die("ERROR: function getPmState parameter pressreleaseID must be a Number!");

	$confirmationid1 = -1;
	if ($pressreleaseID == -1){ return -1;}
	$sqlpressreleaseID = mysql_real_escape_string ($pressreleaseID);
	$query = "SELECT confirmationid1 FROM `pressrelease` WHERE id = $sqlpressreleaseID";
	$checkdata = mysql_query($query) or die("Fehler:".mysql_error());
	//0= Entwurf: -1=pending -2=sent -3=readyforgo
//	echo "<!-- pressreleaseID = $pressreleaseID\n  $query \n	checkdata	$checkdata -->"; 
	if(mysql_num_rows($checkdata)==1){
		while($row = mysql_fetch_object($checkdata))
			{
				$confirmationid1=$row->confirmationid1;
			}	
	}
	return $confirmationid1;
}


function canDelete($pressreleaseID,$user){
	if (empty($user)) die("ERROR: function canDelete parameter user must NOT be empty!");
	if (empty($pressreleaseID)) die("ERROR: function canDelete parameter pressreleaseID must NOT be empty!");

	if (getPmState($pressreleaseID)==-2) {
		return false;
	}
	if (userIsPressagend($user)) 	//Presse darf fast immer löschen.
		{return true;}
	else if (userIsDistributorOne($user,$pressreleaseID)& (getPmState($pressreleaseID)==-3)){
		return true;}
	else if (userIsAdmin($user,$pressreleaseID)& (getPmState($pressreleaseID)==0)){
		return true;}
	return false;
}



function canStore($pressreleaseID,$user){
	if (empty($user)) die("ERROR: function canStore parameter user must NOT be empty!");
	if (empty($pressreleaseID)) die("ERROR: function canStore parameter pressreleaseID must NOT be empty!");

	if (getPmState($pressreleaseID)==0) {
		return true;
	}
	return false;
}

function canRelease($pressreleaseID,$user){
	if (empty($user)) die("ERROR: function canRelease parameter user must NOT be empty!");
	if (empty($pressreleaseID)) die("ERROR: function canRelease parameter pressreleaseID must NOT be empty!");

	//PM muss erst gespeichert werden
	if ($pressreleaseID ==-1) { return false;}

	if 	(
		 (getPmState($pressreleaseID)==0) &
		 (userIsPressagend($user))
		)
		{return true;}
	return false;
}


//Darf absegnen zur Absendung
function canSignoff($pressreleaseID,$user,$signfor){
	if (empty($user)) die("ERROR: function canSignoff parameter user must NOT be empty!");
	if (empty($pressreleaseID)) die("ERROR: function canSignoff parameter pressreleaseID must NOT be empty!");


	//PM muss erst gespeichert sein
	if ($pressreleaseID ==-1) { return false;}

	//Laden wir ob Freigabe 1 besetzt ist und ob Ansprechpartner ausgewählt wurden
	$confirmationid1=-1;
	$confirmationid2=-1;
	$query = "SELECT confirmationid1,confirmationid2 FROM pressrelease WHERE id=$pressreleaseID";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			//Ist es -1 ist noch keine erste Freigabe erfolgt
			$confirmationid1=$row->confirmationid1;
			//Ist es -1 ist noch keine zweite Freigabe erfolgt
			$confirmationid2=$row->confirmationid2;
			}
		}
	$userIDs[0]=-1;$countering=0;
	$query = "SELECT userID FROM pressreleaseconnection WHERE pressreleaseID=$pressreleaseID";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			$countering++;
			$userIDs[$countering]=$row->userID;
			}
		}
	//Muss ne Pressefreigabe haben (State -3)
	//Muss Presseagent für i.A. sein oder
	//Muss Abgeordneter für Direktfreigabe sein
	//Muss für die erste Freigabe einer der Ansprechpartner sein
	//Darf für die zweite Freigabe nicht bereits die erste Freigabe erteilt haben



	//Hier ist der Fehler $user ist komischerweise pewosa und keine zahl


	if 	(
		 (getPmState($pressreleaseID)==-3) AND
		 (userIsPressagend($user) OR userIsDistributor($user)) 
		)
		{
		//Abgeordneter, noch keine Freigabe, er gehört zu den Ansprechpartnern
		If (userIsDistributor($user) AND $confirmationid1==-1 AND in_array($user, $userIDs))
			{
			return true;
			}
		//Abgeordneter, eine Freigabe, er ist nicht der erste Freigebene
		If (userIsDistributor($user) AND $confirmationid1!=-1 AND $confirmationid1!=$user)
			{
			return true;
			}
		//Pressetyp, noch keine Freigabe, die i.A. ist in der Liste der Ansprechpartner
		If (userIsPressagend($user) AND $confirmationid1==-1 AND in_array($signfor, $userIDs))
			{
			return true;
			}
		//Pressetyp, eine Freigabe, die i.A. ist nicht der erster Freigeber
		If (userIsPressagend($user) AND $confirmationid1!=-1 AND $confirmationid1!=$signfor)
			{
			return true;
			}
		return false;
		}
	return false;
}







function canEdit($pressreleaseID,$user){
	if (empty($user)) die("ERROR: function canRelease parameter user must NOT be empty!");
	if (empty($pressreleaseID)) die("ERROR: function canRelease parameter pressreleaseID must NOT be empty!");

	//PM muss erst gespeichert werden
	if ($pressreleaseID ==-1) { return false;};

	if ( getPmState($pressreleaseID) == -3) {
		if (userIsPressagend($user)) {return true;}
		else if (userIsDistributor($user)) {return true;}
		};
	return false;
}

function isReceiverSelected($pressreleaseID){
	if (empty($pressreleaseID)) die("ERROR: function isReceiverSelected parameter pressreleaseID must NOT be empty!");

	//PM muss erst gespeichert werden
	if ($pressreleaseID ==-1) { return false;};

	//Verknüpft mit einer Verteilerliste?
	
	$sqlpressreleaseID = mysql_real_escape_string ($pressreleaseID);
	$query = "SELECT `customer`.email FROM `pressrelease` , `pressreleaseconnection` , `customerdistribution` , `customer` 
					  WHERE `pressrelease`.`id` = $sqlpressreleaseID 
					  AND `pressrelease`.`id` = `pressreleaseconnection`.pressreleaseID 
					  AND `customerdistribution`.distribution = `pressreleaseconnection`.listID 
					  AND `customer`.id = `customerdistribution`.customer 
					  AND `pressreleaseconnection`.`listID` IS NOT NULL GROUP BY `customerdistribution`.customer";
	$checkdata = mysql_query($query) or die("Fehler:".mysql_error());
	if(mysql_num_rows($checkdata)>0){
		return true;
	}
	
	//Verknüpft mit einem Einzeladressaten ?
	$query = "SELECT `customer`.email FROM `pressrelease` , `pressreleaseconnection` , `customer` 
					  WHERE `pressrelease`.id = $sqlpressreleaseID 
					  AND `pressrelease`.id = `pressreleaseconnection`.pressreleaseID 
					  AND `customer`.id = `pressreleaseconnection`.customerID 
					  AND `pressreleaseconnection`.`customerID` IS NOT NULL GROUP BY customer.id";
	$checkdata = mysql_query($query) or die("Fehler:".mysql_error());
	if(mysql_num_rows($checkdata)>0){
		return true;
	}


	return false;
}


function isDistributorSelected($pressreleaseID){
	if (empty($pressreleaseID)) die("ERROR: function isDistributorSelected parameter pressreleaseID must NOT be empty!");

	//PM muss erst gespeichert werden
	if ($pressreleaseID ==-1) { return false;};

	$sqlpressreleaseID = mysql_real_escape_string ($pressreleaseID);
	$query = "SELECT `pressreleaseconnection`.`id` FROM `pressreleaseconnection` WHERE `pressreleaseID` = $sqlpressreleaseID AND `userID` >0";
	$checkdata = mysql_query($query) or die("Fehler:".mysql_error());
	if(mysql_num_rows($checkdata)>0){
		return true;
	}
	return false;
}


function isPressreleaseExsiting($pressreleaseID){
	if (empty($pressreleaseID)) die("ERROR: function isPressreleaseExsiting parameter pressreleaseID must NOT be empty!");

	//PM muss erst gespeichert werden
	if ($pressreleaseID ==-1) { return false;};
	
	$sqlpressreleaseID = mysql_real_escape_string ($pressreleaseID);
	$query = "SELECT `id` FROM `pressrelease` WHERE `id` = $sqlpressreleaseID";
	$checkdata = mysql_query($query) or die("Fehler:".mysql_error());
	if(mysql_num_rows($checkdata)>0){
		return true;
	}
	return false;
}



$pressreleaseID = fetchintvar ('pressreleaseID',-1);
$action = fetchstringvar ('action',"");
$sendnow = fetchboolvar ('sendnow');
$time = fetchstringvar ('time',"");
$date = fetchstringvar ('date',"");
$Betreff =  fetchstringvar ('Betreff',"");
$body =  fetchstringvar ('body',"");
$tags =  fetchstringvar ('tags',"");
$contact = fetchstringvar ('contact',"");
$pressagent  = fetchstringvar ('pressagent',"");
$senddate  = fetchstringvar ('senddate',"");
$sendtime  = fetchstringvar ('sendtime',"");
$listmaxid  = fetchstringvar ('listmaxid',"");
$addsinglecustomers = fetchboolvar ('addsinglecustomers');
$singlecustomersmaxid = fetchstringvar ('singlecustomersmaxid',"");
$distributormaxid = fetchstringvar ('distributormaxid',"");

//Nicht existierende PMIDs abfangen
if (!isPressreleaseExsiting($pressreleaseID)){
	$pressreleaseID = -1;
}

/**
* checkLegitimation prüft ob der eingeloggte Benutzer die notwendigen Rechte hat, 
* um eine aktion für eine PM im jeweiligen zustand auszuführen.
*/
function checkLegitimation($action,$pressreleaseID,$loggedinuserid){
	switch ($action) {
	    case "Speichern":
	        echo "\n<!-- ACTION: Speichern -->\n";
		if (canStore($pressreleaseID,$loggedinuserid)) 
			{ return true;}
		else {die_nicely("Speichern nicht möglich bei PM mit sendstate ".getPmState($pressreleaseID));}

	        break;


	    case "Senden":
	        echo "\n<!-- ACTION: Senden -->\n";
	        break;


	    case "delete":
	        echo "\n<!-- ACTION: delete -->\n";
		if(canDelete($pressreleaseID,$loggedinuserid)) { return true;} 
		else {die_nicely("Löschen einer bereits versendeten PM nicht erlaubt.");}
	        break;


	    case "Freigeben":
	        echo "\n<!-- ACTION: [Presse]Freigeben -->\n";
		if (canRelease($pressreleaseID,$loggedinuserid))
			{
				return true;
			} else {
				die_nicely ("Eingeloggter Benutzer hat nicht das Recht Pressefreigaben zu erteilen!");
			}
	        break;


	    case "edit":
	        echo "\n<!-- ACTION: Bearbeiten -->\n";
		if (canEdit($pressreleaseID,$loggedinuserid))
			{
				return true;
			} else {
				die_nicely ("Eingeloggter Benutzer hat nicht das Recht Freigaben aufzuheben!");
			}
	        break;
	    default:
		if ($pressreleaseID == -1){
		        echo "\n<!-- ACTION: neue PM -->\n";
			return true;
		} else {
			if ($pressreleaseID > 0){
			        echo "\n<!-- ACTION: PM Anzeigen -->\n";
				return true;
			} else {
			        echo "\n<!-- ACTION: nicht sicher -->\n";
			}
		}
	}
}

checkLegitimation($action,$pressreleaseID,$loggedinuserid);


For ($i=1;$i<=$listmaxid;$i++){
	If (isset($_POST['list'.$i]))
		{ $list[$i] = true; } 
	else If (isset($_GET['list'.$i])) 
		{ $list[$i] = true; }
	//If (isset($list[$i]))
		//echo "<p>List $i: ".$list[$i]."</p>";
}

//Einzeladressaten ergänzen
For ($i=1;$i<=$singlecustomersmaxid;$i++){
	If (isset($_POST['singlecustomer'.$i]))
		{ $singlecustomer[$i] = true; } else If (isset($_GET['singlecustomer'.$i])) { $singlecustomer[$i] = true; }
	//If (isset($singlecustomer[$i]))
		//echo "<p>singlecustomer $i: ".$singlecustomer[$i]."</p>";
}

//Ansprechpartner analysieren
For ($i=1;$i<=$distributormaxid;$i++){
	If (isset($_POST['distributor'.$i]))
		{ $distributor[$i] = true; } 
	else If (isset($_GET['distributor'.$i])) 
		{ $distributor[$i] = true; }
	//If (isset($distributor[$i]))
		//echo "<p>distributor $i: ".$distributor[$i]."</p>";
}


/*

echo "<p>pressreleaseID: $pressreleaseID</p>";
echo "<p>action: $action</p>";
echo "<p>sendnow: $sendnow</p>";
echo "<p>time: $time</p>";
echo "<p>date: $date</p>";
echo "<p>Betreff: $Betreff</p>";
echo "<p>body: $body</p>";
echo "<p>contact: $contact</p>";
echo "<p>pressagent: $pressagent</p>";
echo "<p>addsinglecustomers: $addsinglecustomers</p>";
echo "<p>singlecustomersmaxid: $singlecustomersmaxid</p>";
echo "<p>distributormaxid: $distributormaxid</p>";
*/
$sqlBetreff = mysql_real_escape_string ($Betreff);
$sqlBody = mysql_real_escape_string ($body);
$sqlpressagent = mysql_real_escape_string ($pressagent);
$sqlpressreleaseID = mysql_real_escape_string ($pressreleaseID);
$sqlTags = mysql_real_escape_string ($tags);
$sqlContact = mysql_real_escape_string ($contact);

	//Wir machen aus dem leserlichen Datum ein englisches
	//aber nur wenn nicht sendnow ausgewählt ist, dann nehmen wir einfach die aktuelle Zeit
	If ($sendnow!=1)
		{
		//echo "ich bekomme $senddate und...";
		$teile = explode(".", $senddate);
		$jahr=$teile[2];
		$monat=$teile[1];
		$tag=$teile[0];
		$senddate_db=$jahr."-".$monat."-".$tag." ".$sendtime;
		//echo "$senddate_db draus gemacht.";
		$sendnow=0;
		}
	else
		{
		$senddate_db=date("Y-m-d H:i:s");
		//$sendnow=1;
		}

//Speichern nur ausführen, wenn Speichern oder Senden gedrück wurde
If ($action=='Speichern' OR $action=='Senden' OR $action=='Freigeben')
	{
			
		if ($pressreleaseID == "-1"){
			$query = "INSERT INTO `pewosa`.`pressrelease` ( `subject` , `body` , `pressagentid`, `tags`, `contact`, `sendstate`,`senddate`, `sendnow` ) VALUES ('$sqlBetreff', '$sqlBody', '$sqlpressagent','$sqlTags','$sqlContact','0','$senddate_db','$sendnow');";
			$send = mysql_query($query) or die("Fehler A:".mysql_error());
			$pressreleaseID = mysql_insert_id();}
		else {
			$query = "UPDATE `pewosa`.`pressrelease` SET 
			subject='$sqlBetreff', body='$sqlBody', pressagentid='$sqlpressagent', tags='$sqlTags', contact='$sqlContact', confirmationid1='-1', confirmationid2='-1' 
			, confirmationid1bypressagent='-1', confirmationid2bypressagent='-1', senddate='$senddate_db', sendnow='$sendnow' WHERE id='$sqlpressreleaseID';";
			//echo "aufgabe $query";
			$send = mysql_query($query) or die("Fehler B:".mysql_error());}
		//echo "<p>SQLQUERY: $query</p>";
		//echo "SQLResult: $send, id: $pressreleaseID";

		
		//Hier werden alle Verknüpfungen zu Verteilern und Co gelöscht
		$query = "DELETE FROM `pewosa`.`pressreleaseconnection` WHERE pressreleaseID=".$pressreleaseID.";";
		$send = mysql_query($query) or die("Fehler C:".mysql_error());
		//echo "<p>SQLResult: $send, $query</p>";

		//Verteilerlisten einfügen
		For ($i=1;$i<=$listmaxid;$i++){
			If (isset($list[$i])) 
				{
					$query = "INSERT INTO `pewosa`.`pressreleaseconnection` ( `pressreleaseID`, `listID`) VALUES (".$pressreleaseID.",".$i.");";
					//echo "<p>Query: $query</p>";
					$send = mysql_query($query) or die("Fehler D:".mysql_error());
					//echo "<p>SQLResult: $send</p>";
				}
		}

		//Einzelempfänger einfügen
		For ($i=1;$i<=$singlecustomersmaxid;$i++){
			If (isset($singlecustomer[$i])) 
				{
					$query = "INSERT INTO `pewosa`.`pressreleaseconnection` ( `pressreleaseID`, `customerID`) VALUES (".$pressreleaseID.",".$i.");";
					//echo "<p>Query: $query</p>";
					$send = mysql_query($query) or die("Fehler Einzel:".mysql_error());
					//echo "<p>SQLResult: $send</p>";
				}
		}

		//Ansprechpartner einfügen/ Damit werden auch die Abgeordneten festgelegt, die in der PM-Übersicht die 1te Freigabe machen dürfen. Die zweite dürfen alle Abgeordneten!!!
		For ($i=1;$i<=$distributormaxid;$i++){
			If (isset($distributor[$i])) 
				{
					$query = "INSERT INTO `pewosa`.`pressreleaseconnection` ( `pressreleaseID`, `userID`) VALUES (".$pressreleaseID.",".$i.");";
					//echo "<p>Query: $query</p>";
					$send = mysql_query($query) or die("Fehler Ansprech:".mysql_error());
					//echo "<p>SQLResult: $send</p>";
				}

		}
	//Wenn es abgeschickt wird, soll der Sendagent und Senddate noch nachgetragen werden
	

	if ($action=='Senden' AND $pressreleaseID!=-1)
		{
		//Sicherheit geht vor	
		$query = "SELECT * FROM pressrelease WHERE id=$pressreleaseID";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{	
			while($row = mysql_fetch_object($checkdata))
				{
				$confirmationid1=$row->confirmationid1;
				$confirmationid2=$row->confirmationid2;
				$sendstate=$row->sendstate;
				}
			}	
		If ($confirmationid1!='-1' AND $confirmationid2!='-1' AND $sendstate==0)
			{
			//Senden dürfen außerdem nur Pressagents!
			$query = "SELECT id FROM users WHERE id=$loggedinuserid AND deleted !=1 AND pressagent=1";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)==1)
				{		
				$query = "UPDATE `pewosa`.`pressrelease` SET sendagent='$loggedinuserid', senddate ='$senddate_db', sendstate='-1' WHERE id='$sqlpressreleaseID';";
				$send = mysql_query($query) or die("Fehler S:".mysql_error());
				//echo "<p>SQLQUERY: $query</p>";
				//echo "SQLResult: $send, id: $pressreleaseID";
				}
			}
		}
	else
		{
		echo "<button type='button' class='btn btn-success'>Gespeichert</button>";	
		}
	}

//Hier wird gelöscht	
If ($action=='delete')
	{
	If (canDelete($pressreleaseID,$loggedinuserid))
		{
		//Weg damit
		$query = "DELETE FROM `pewosa`.`pressrelease` WHERE id=".$pressreleaseID.";";
		$send = mysql_query($query) or die("Fehler DelA:".mysql_error());

		$query = "DELETE FROM `pewosa`.`pressreleaseconnection` WHERE pressreleaseID=".$pressreleaseID.";";
		$send = mysql_query($query) or die("Fehler DelB:".mysql_error());
		//Script abbrechen!
		echo "<h3>Pressemitteilung <i>$Betreff</i> wurde gelöscht</h3>";include_once("footer.php");exit();
		}
	$pressreleaseID=-1;	
	}

//Hier wird die Pressefreigabe abgearbeitet	
If (($action=='Freigeben') && userIsPressagend($loggedinuserid) ){
	if (!isReceiverSelected($pressreleaseID)) {
		//kein Empfänger ausgewählt
		echo "<div class=\"alert alert-danger\"><h1><strong>Fehler:</strong> Kein Empfänger ausgewählt!</h1></div>";
	} else if(!isDistributorSelected($pressreleaseID)) {
		//kein Ansprechpartner ausgewählt
		echo "<div class=\"alert alert-danger\"><h1><strong>Fehler:</strong> Kein Ansprechpartner ausgewählt!</h1></div>";
	} else {
		//Empfänger ausgewählt

		//if ($sendnow){$sendnowtime = date("Y-m-d H:i:s");} else {$sendnowtime="";};

		$query = "UPDATE `pewosa`.`pressrelease` SET sendstate='-3', sendagent=$loggedinuserid WHERE id='$sqlpressreleaseID';";
		$send = mysql_query($query) or die("Fehler X:".mysql_error());
		echo "<button type='button' class='btn btn-success'>Freigegeben</button>";	
	} 

//Die Sendefreigabe wird auf die Messagelist umgeleitet und steht deshalb nicht hier

}


//echo "<p>pressreleaseID = $pressreleaseID</p>";

If ($action=='edit') {
	$query = "UPDATE `pewosa`.`pressrelease` SET sendstate='0', sendagent=-1, confirmationid1=-1, confirmationid1bypressagent=-1, confirmationid2=-1, confirmationid2bypressagent=-1 WHERE id='$sqlpressreleaseID';";
	$send = mysql_query($query) or die("Fehler Edit:".mysql_error());
}


//Alle deaktivieren, wenn man nicht speichern kann.
if (canStore($pressreleaseID,$loggedinuserid)) {$disabled='';}
else {$disabled="disabled";}





// ################### Breadcrumbs

$labelentwurf=" class=\"disabled\"";
$labelPressefrei=" class=\"disabled\"";
$labelfirstauth=" class=\"disabled\"";
$labelsecondauth=" class=\"disabled\"";
$labelsending=" class=\"disabled\"";
$pmstate = getPmState($pressreleaseID);
//class="active"
//Entwurf
if ($pmstate == 0) {
	$labelentwurf=" class=\"active\"";
} else if($pmstate == -3) {
	if (getFirstAuth($pressreleaseID) == -1){
		//Pressefreigabe
		//$labelentwurf=" class=\"active\"";
		$labelPressefrei=" class=\"active\"";
	} else {
		//$labelentwurf=" class=\"active\"";
		//$labelPressefrei=" class=\"active\"";
		$labelfirstauth=" class=\"active\"";
	}

	//oder 1. Freigabe
} else if(($pmstate == -1) | ($pmstate > 0 )) {
	//zweite Freigabe
//	$labelentwurf=" class=\"active\"";
//	$labelPressefrei=" class=\"active\"";
//	$labelfirstauth=" class=\"active\"";
	$labelsecondauth=" class=\"active\"";
};
if(($pmstate == -2) | ($pmstate > 0 )) {
	//versand
//	$labelentwurf=" class=\"active\"";
//	$labelPressefrei=" class=\"active\"";
//	$labelfirstauth=" class=\"active\"";
//	$labelsecondauth=" class=\"active\"";
	$labelsending=" class=\"active\"";
}

echo "<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">Status:<nav aria-label=\"Page navigation\"><ul class=\"pagination pagination-lg\"><li $labelentwurf><a>Entwurf</a></li><li $labelPressefrei><a>Pressefreigabe</a></li><li $labelfirstauth><a>1. Freigabe</a></li><li $labelsecondauth><a>2. Freigabe</a></li><li $labelsending><a>Versand</a></li></ul></nav></div>";

// ******************** Ende Breadcrumbs

$paction = $GET['action'];
//echo "POST Action: $paction";
//Was jetzt kommt, darf er nur machen, wenn er grade nicht senden gedrückt hat, stattdessen wird die Mail gesendet und angezeigt "Mail ist raus"
If ($action!='Senden')
	{
	//Ist der Entwurf bereits gespeichert wird das senddatum ausgelesen. Ist es gesetzt, darf nichts mehr bearbeitet werden
	//Abgesehen davon wird natürlich dann erstmal auch alles andere ausgelesen
	If ($pressreleaseID!=-1)
		{
		$query = "SELECT * FROM pressrelease WHERE id=$pressreleaseID";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{	
			while($row = mysql_fetch_object($checkdata))
				{
				$check_senddate=$row->senddate;
				$sqlBetreff=$row->subject;
				$sqlBody=$row->body;
				$sqlTags=$row->tags;
				$sqlpressagent=$row->pressagentid;
				$sqlContact=$row->contact;
				$confirmationid1=$row->confirmationid1;
				$confirmationid2=$row->confirmationid2;
				$sendstate=$row->sendstate;
				}
			}
		//Verlinkungen reinladen

		$query = "SELECT * FROM pressreleaseconnection WHERE pressreleaseID=$pressreleaseID";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)>=1)
			{	
			while($row = mysql_fetch_object($checkdata))
				{
				If (isset($row->listID))
					{
					$list[$row->listID]=TRUE;
					}
				If (isset($row->userID))
					{
					$distributor[$row->userID]=TRUE;
					}	
				If (isset($row->customerID))
					{
					$singlecustomer[$row->customerID]=TRUE;
					}						
				}
			}	
		}
	else
		{
		$sendstate=-5;	
		}
	//If (!isset($check_senddate) OR $check_senddate=='0000-00-00 00:00:00'){$disabled='';}else{$disabled="disabled";}

	echo "<form action='message.php' method='post' id='pmform'>";
	echo "<input type=\"hidden\" name=\"pressreleaseID\" id=\"pressreleaseID\" value=\"$pressreleaseID\">";
// #################### URL Updaten
if ($pressreleaseID > 0) {
	echo "  <script>";
	echo "window.location.hash = $pressreleaseID";
	echo "  </script>";
	} 
else if ($pressreleaseID == -1) {
	echo "  <script>\n";
	echo " pressreleaseID = parseInt(window.location.hash.substring(1));\n";
	echo " if ((pressreleaseID)&&(pressreleaseID > 0)){\n";
//	echo " alert(pressreleaseID);\n";
	echo " formpressreleaseID = document.getElementById(\"pmform\").elements[\"pressreleaseID\"]; \n";
	echo " formpressreleaseID.value = pressreleaseID;\n";
	echo " document.getElementById(\"pmform\").submit();}\n";
	echo "  </script>\n";
	}
// ******************** Ende URL Updaten
	?>
	<div class="row"><div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">


	<!-- ********************** VERTEILER ********************************************* -->
	<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Verteiler:</h3></div>
	  <div class="panel-body">
	<?php
		$counter=0;
		$query = "SELECT name, id FROM list WHERE deleted!=1 ORDER BY name";
		$checkdata = mysql_query($query);
		$distributionHTML ="";
		$listmaxid = 0;
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$counter++;
				$listmaxid = max($listmaxid,$row->id);
				$ids[$counter]=$row->id;
				$name[$row->id]=$row->name;
				$boxname='list'.$row->id;
				if ($list[$row->id]==true) { $checked = " checked "; } else {$checked="";};
				//Fürs Mouseover laden wir hier noch alle enthaltenen E-Mailadressen
				//dafür müssen wir erstmal alle Mitglieder-ids des Verteilers einlesen
				$countdist=0;
				$query2 = "SELECT customer FROM customerdistribution WHERE distribution=$row->id";
				$checkdata2 = mysql_query($query2);
				if(mysql_num_rows($checkdata2)>=1)
					{
					while($row2 = mysql_fetch_object($checkdata2))
						{
						$countdist++;
						$allcust[$countdist]=$row2->customer;
						//echo "<br>$row->id. $allcust[$countdist]";
						}
					}
				If ($countdist!=0)
					{
					$givetitle='';
					$counters=0;
					For($i=1;$i<=$countdist;$i++)
						{					
						$query3 = "SELECT email FROM customer WHERE deleted !=1 AND id=$allcust[$i]";
						$checkdata3 = mysql_query($query3);
						if(mysql_num_rows($checkdata3)>=1)
							{
							while($row3 = mysql_fetch_object($checkdata3))
								{
								$counters++;
								If ($counters==1)	
									{
									$givetitle=$row3->email;
									
									}
								else
									{
									$givetitle=$givetitle.", ".$row3->email;
									//echo "Dinge: $givetitle<br>";
									}
								}
							}						
						}
					If ($counters==0){$givetitle='Enthält keine Empfänger';}
					}
				else
					{
					$givetitle='Enthält keine Empfänger';
					}



				$distributionHTML =  $distributionHTML."<span title='$givetitle'><INPUT $checked $disabled type='checkbox' name=\"".$boxname."\"> ".$row->name."</span><br> ";
				}
			}
	//echo "<select name=\"distribution\" size=\"".$counter."\" multiple class=\"rightCol\">";
	echo "<input type=\"hidden\" name=\"listmaxid\" value=\"".$listmaxid."\">";
	echo $distributionHTML;
	$distributionHTML ="";

	//</select>
	//Wir wollen jede Mail nur einmal anzeigen lassen
	//Deshalb legen wir ein Array an
	$all_mails[0]="no@real.mail";
	?>



	<br>Einzelne Empfänger hinzufügen:<br/>
	<?php if ($addsinglecustomers==true) { $checked = " checked ";$display=" "; } else {$checked="";$display=" style=\"display:none\" ";};	
	echo "<input $checked type=\"checkbox\" name=\"addsinglecustomers\" id=\"addsinglecustomers\"><br/>";
	echo "<span id=\"singlecustomersarea\" $display>";?>
	<?php
		$counter=0;
		$query = "SELECT email, firstname, lastname, company, id FROM customer WHERE deleted !=1 ORDER BY email";
		$checkdata = mysql_query($query);
		$distributionHTML ="";
		$singlecustomersmaxid= 0;
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$counter++;
				$ids[$counter]=$row->id;
				$singlecustomersmaxid = max ($singlecustomersmaxid, $row->id);
				$name[$row->id]=$row->name;
				$boxname='singlecustomer'.$row->id;	
				if ($singlecustomer[$row->id]==true) { $checked = " checked "; } else {$checked="";};			
				//$distributionHTML =  $distributionHTML."<INPUT type='checkbox' $checked $disabled name=\"".$boxname."\"> ".$row->firstname." ".$row->lastname." (".$row->company.")"."<br>";
				
				//Givetitle bearbeiten
				$givetitle='';$noinfo=TRUE;
				If ($row->firstname!=''){$givetitle=$row->firstname; $noinfo=FALSE;}
				If ($row->lastname!='' AND $noinfo){$givetitle=$row->lastname; $noinfo=FALSE;}
				If ($row->lastname!='' AND !$noinfo){$givetitle=$givetitle." ".$row->lastname; $noinfo=FALSE;}
				If ($row->company!='' AND $noinfo){$givetitle=$row->company; $noinfo=FALSE;}
				If ($row->company!='' AND !$noinfo){$givetitle=$givetitle." (".$row->company.")"; $noinfo=FALSE;}
				If ($noinfo){$givetitle='Keine weiteren Informationen';}

				If (!in_array($row->email,$all_mails))
					{
					//Mail wird im Array gespeichert um Dopplunge zu vermeiden
					array_push($all_mails,$row->email);
					//Dann Ausgabe
					$distributionHTML =  $distributionHTML."<span title='$givetitle'><INPUT type='checkbox' $checked $disabled name=\"".$boxname."\"> ".$row->email." </span><br>";
					}
				}
			}
	//echo "<select name=\"distribution\" size=\"".$counter."\" multiple class=\"rightCol\">";
	echo "<input type=\"hidden\" name=\"singlecustomersmaxid\" value=\"".$singlecustomersmaxid."\">";
	echo $distributionHTML;
	$distributionHTML ="";
	//</select>
	?>
	</span> <!-- singlecustomersarea -->
	 <script>
	var singlecustomersarea = document.getElementById('singlecustomersarea');
	document.getElementById('addsinglecustomers').onchange = function() {
		singlecustomersarea.style.display = this.checked ? 'block' : 'none';
	};
	</script>
	</div></div>
	<!-- ########################## ENDE VERTEILER ############################################# -->


<!-- ************** Anfang Ansprechpartner: *************************************************** -->
	<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Ansprechpartner:</h3></div>
	  <div class="panel-body">
	<?php
		$counter=0;
		$query = "SELECT firstname, lastname, id, phone, jobtitle FROM users WHERE deleted !=1 AND distributor =1 ORDER BY lastname";
		$checkdata = mysql_query($query);
		$distributorHTML ="";
		$distributormaxid = 0;
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$counter++;
				$ids[$counter]=$row->id;
				$distributormaxid = max ($distributormaxid, $row->id);
				$boxname='distributor'.$row->id;
				$jobtitle = $row->jobtitle; 
				$phone = $row->phone;
				if ($pressreleaseID == -1) { //Automatisch eingeloggten User auswählen
					if ($loggedinuserid == $row->id) { $checked = " checked "; } else {$checked="";};
				} else if ($distributor[$row->id]==true) { $checked = " checked "; } else {$checked="";};
				

				$distributorHTML =  $distributorHTML."<INPUT type='checkbox' $checked $disabled name=\"".$boxname."\" id=\"".$boxname."\" phone=\"".$phone."\" naturalname=\"".$row->firstname." ".$row->lastname."\" jobtitle=\"".$jobtitle."\"> ".$row->firstname." ".$row->lastname."<br>";
				}
			}
	echo "<input type=\"hidden\" name=\"distributormaxid\" id=\"distributormaxid\" value=\"".$distributormaxid."\">";		
	//echo "<select name=\"distributor\" size=\"".$counter."\" multiple class=\"rightCol\">";
	echo $distributorHTML;
	$distributorHTML ="";
	//</select>
	?>

	</div></div>
<!-- ########################## ENDE Ansprechpartner ############################################# -->

<!-- ************** Anfang Presseverantwortlicher: *************************************************** -->
	<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Presseverantwortlicher:</h3></div>
	  <div class="panel-body">
	<?php

		echo "<!-- pressagent = $pressagent,$pressreleaseID -->";
		$foundloggedinuserid = 0;
		if ($pressreleaseID == -1) {		
			//Prüfen ob Presseverantwortlicher eingelogged ist.
			echo "<!-- Prüfen ob Presseverantwortlicher eingelogged ist. -->";
			$query = "SELECT id FROM users WHERE deleted !=1 AND pressagent =1 ORDER BY lastname";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1) {
				while($row = mysql_fetch_object($checkdata))
					{
					if ($row->id == $loggedinuserid) {
						$foundloggedinuserid = 1;
					}
				}
			}
		} else if (empty($pressagent) OR ($pressagent=="") ){
			echo "<!-- Pressagent laden. -->";
			$query = "SELECT pressagentid FROM pressrelease WHERE ID = $pressreleaseID";
			echo "<!-- $query -->";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1) {
				while($row = mysql_fetch_object($checkdata))
					{
						$pressagent = $row->pressagentid;
					}
			}

		}

		echo "<!-- pressagent = $pressagent -->";
		

		$counter=0;
		$query = "SELECT firstname, lastname, id, phone, jobtitle FROM users WHERE deleted !=1 AND pressagent =1 ORDER BY lastname";
		$checkdata = mysql_query($query);
		$pressagentHTML ="";
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$counter++;
				$ids[$counter]=$row->id;
				$phone = $row->phone;
				$jobtitle = $row->jobtitle;
				if ($pressreleaseID == -1) {
					//Automatisch eingeloggten User auswählen
					if($foundloggedinuserid == 1) 
						{if ($loggedinuserid == $row->id) { $checked = " selected "; } else {$checked="";};}
					//Standart Presseverantwortlichen auswählen
						else {if (intval ($defaultPressesprecherID) == intval ($row->id)) { $checked = " selected "; } else { $checked = " "; } };
				//Gespeicherten Presseverantwortlichen auswählen
				} else {if ($pressagent==$row->id) { $checked = " selected "; } else {$checked="";};};
				$pressagentHTML =  $pressagentHTML."<option $checked $disabled value=\"".$row->id."\" phone=\"".$phone."\" jobtitle=\"".$jobtitle."\">".$row->firstname." ".$row->lastname."</option>";
				}
			}	
	echo "<select name=\"pressagent\" id=\"pressagent\" class=\"rightCol\">";
	echo $pressagentHTML;
	$pressagentHTML ="";
	?>
	</select>
	</div></div>
<!-- ############ ENDE Presseverantwortlicher: ################################################ -->



<?
If ($disabled!='disabled')
	{
?>
	<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Sendezeit:</h3></div>
	  <div class="panel-body">
	Sofort Versenden:<br/>

	<?php

if ($pressreleaseID == -1) {
	//Neu
	$senddisplay='display:none';
	$rightnow=date("d.m.Y");$rightnow2=date("H:i");
	echo "	<input checked type=\"checkbox\" name=\"sendnow\" id=\"sendnow\" value=\"sendnow\">";
	} else{
		$counter=0;
		$query = "SELECT sendnow,senddate FROM pressrelease WHERE id = $pressreleaseID";
		$checkdata = mysql_query($query);
		$distributorHTML ="";
		$distributormaxid = 0;
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$counter++;
				if($row->sendnow==1)
					{
					$sendnowchecked = " checked ";
					$rightnow=date("d.m.Y");$rightnow2=date("H:i");
					$senddisplay='display:none'; 
					}
				else 
					{	
					$sendnowchecked = "";
					$senddate = $row->senddate;
					$teile = explode("-", $senddate);
					$jahr=$teile[0];
					$monat=$teile[1];
					$tag=$teile[2];
					//echo "alles $senddate / tag $tag / monat $monat / jahr $jahr / rest $rest";
					//echo "teile 0 $teile[0] 1 $teile[1] 2 $teile[2]";
					$rest = substr($tag,0, -9);
					$rightnow=$rest.".".$monat.".".$jahr;

					$rightnow2=substr($tag, -8,-3);
					$senddisplay='';
					}
				}
			}
		echo "	<input ".$sendnowchecked." type=\"checkbox\" name=\"sendnow\" id=\"sendnow\" value=\"sendnow\">";
	}

	echo "<span id='sendlater' style='$senddisplay'>";
?>
	<p class="rightCol"><span class="title">Sendedatum:</span><br/>
	<? 
	echo "<input class='rightCol' type='date' name='senddate' value='$rightnow'><br/>";
	echo "<span class='title'>Uhrzeit:</span><br/><input class='rightCol' type='time' name='sendtime' value='$rightnow2'><br/></span>";
	?>

	</p>
	 </div></div>   
	 <script>
	var elem = document.getElementById('sendlater');
	document.getElementById('sendnow').onchange = function() {
		elem.style.display = this.checked ? 'none' : 'block';
	};
	</script>


	<!--</div>--> <!--sidebar -->  
<?
	}
?>
	</div><!--Ende Seitenleiste-->


	 
	<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">

	<div class="panel panel-default">
	  <div class="panel-heading"><h3 class="panel-title">Betreff:</h3></div>
	  <div class="panel-body">
	 <? If ($disabled=='disabled'){$readonly='readonly';}else{$readonly='';}
	 echo "<input name='Betreff' type='text' class='mainCol' value='$sqlBetreff' $readonly style='font-size: 16pt'>"; ?>

	 </div>
	</div>


	<div class="panel panel-default">
	  <div class="panel-heading"><h3 class="panel-title">Text der Pressemitteilung:</h3></div>
	  <div class="panel-body">
	<? If ($disabled=='disabled'){$readonly='readonly';}else{$readonly='';}
	  echo "<textarea name='body'  rows='30' class='mainCol' $readonly style='font-size: 16pt'>$sqlBody</textarea>";?>
	  
	  <br/>
	<?echo "<textarea name='contact' id='contact' class='mainCol' rows='4' $readonly>$sqlContact</textarea>";?>
	</div>
	</div>

<!-- ************************** Ansprechpartner Update Script ************************************ -->
	 <script>

	function updateContactInfo(){
		var elemcontact = document.getElementById('contact');
		//elemcontact.disabled = true;
		mdlCounter = 0;
		var ansprechpartnername = [];
		var ansprechpartnerphone = [];
		var ansprechpartnerjobtitle = [];

		for (idist = 1; idist <= distributormaxid; idist++) {
			idistelem = document.getElementById('distributor'+idist);
			if (idistelem) {
				if (idistelem.checked) {
					ansprechpartnername[mdlCounter]=idistelem.attributes.naturalname.textContent;
					ansprechpartnerphone[mdlCounter]=idistelem.attributes.phone.textContent;
					ansprechpartnerjobtitle[mdlCounter]=idistelem.attributes.jobtitle.textContent;
					mdlCounter++;
				};
			}
		}

		selected = document.getElementById('pressagent').selectedIndex;
		presseverantwortlichername = document.getElementById('pressagent').options[selected].text;
		presseverantwortlicherjobtitle = document.getElementById('pressagent').options[selected].attributes.jobtitle.textContent;
		presseverantwortlichertelefon = document.getElementById('pressagent').options[selected].attributes.phone.textContent;
		output = "Ansprechpartner:"
//Ansprechpartner
		for (imdl = 0; imdl < mdlCounter; imdl++) { 
			output = output+"\n"+ansprechpartnername[imdl];
			// Kein Job abfangen
			if (ansprechpartnerphone[imdl] != "0"){
				output = output+ " - "+ansprechpartnerjobtitle[imdl];
			}
			// Kein Telefon abfangen
			if (ansprechpartnerphone[imdl] != "0"){
				output = output+ ", Telefon: "+ansprechpartnerphone[imdl];
			}
		}
//Presseverantwortlicher
		output = output+"\n"+presseverantwortlichername+" - "+presseverantwortlicherjobtitle+", Telefon: "+presseverantwortlichertelefon;
//Ende
		elemcontact.value = output;
	}


//Initialisierung
	//Change listener auf den Presseverantwortlichen legen
	document.getElementById('pressagent').onchange = function(a){ updateContactInfo();};
	
	//Change listener auf die Ansprechpartner legen
	distributormaxid = document.getElementById('distributormaxid').value;
	for (idist = 1; idist <= distributormaxid; idist++) { 
		if (document.getElementById('distributor'+idist)) {
			document.getElementById('distributor'+idist).onchange = function(a){ updateContactInfo();};}
	}
	
	//Status Quo anzeigen
	updateContactInfo();

	</script>
<!-- ########################## ENDE Ansprechpartner Update Script ################################ -->


	<div class="panel panel-default">
	  <div class="panel-heading"><h3 class="panel-title">Tags:</h3></div>
	  <div class="panel-body">
	  <?echo "<input name='tags' type='text' class='mainCol' value='$sqlTags' placeholder='Transparenzgesetz, Gesetzenwurf, Gemüsesuppe' $disabled>";?>
	  </div>
	</div>



	<div class="panel panel-default">
	<div class="panel-body">

	<button type="button" name="action" value="" class="btn btn-info" onclick="window.location.href='./';"><span class="glyphicon glyphicon-share" aria-hidden="true"'></span> Zur Übersicht ohne Speichern</button>
	<?
	//Hier muss ich checken ob alle wichtigen Daten ausgefüllt sind
	//Das geht aber nur im Javascript???
	//$complete=TRUE;
	//If ($sqlBody==''){$complete=FALSE;}
	//If ($sqlContact==''){$complete=FALSE;}
	
	//Darf speichern
	If (canStore($pressreleaseID,$loggedinuserid))
		{
		?>
		<button type="submit" name="action" value="Speichern" class="btn btn-info"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"'></span> Speichern</button>
		<?}
	//Darf Pressefreigabe machen
	If (canRelease($pressreleaseID,$loggedinuserid))
		{?>
		<button type="submit" name="action" value="Freigeben" class="btn btn-success"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"'></span> Pressefreigabe </button>&nbsp;
		<?}

	//Darf ändern und mit Bearbeiten die Freigaben aufheben
	if(canEdit($pressreleaseID,$loggedinuserid))
		{
		?><button type="submit" id="editbutton" name="action" value="edit" class="btn btn-warning" onclick="return confirm('Freigaben wirklich löschen?');"><span class="glyphicon glyphicon-trash" aria-hidden="true"'></span>Bearbeiten und Freigaben löschen</button>&nbsp;<?
		}

	//Darf löschen
	if(canDelete($pressreleaseID,$loggedinuserid))
		{
		?><button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('PM wirklich löschen?');"><span class="glyphicon glyphicon-trash" aria-hidden="true"'></span> Löschen</button>&nbsp;<?
		}


	//Ende des Formulares für die anderen Buttons
	//Weil die Freigaben an messagelist.php gehen sollen
	echo "</form>";
	echo "<form action='messagelist.php' method='post'>";

	//Weiche: ist er Pressefuzi oder Abgeordneter
	If (userIsDistributor($loggedinuserid))
		{
		//Abgeordneter wenn erste Freigabe fehlt
		if(canSignoff($pressreleaseID,$loggedinuserid,$loggedinuserid) AND $confirmationid1==-1)
			{
			echo "<button type='submit' name='Freigabe1' value='$pressreleaseID' class='btn btn-primary buttonbright'><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> Freigeben</button>";
			}
		//Abgeordneter wenn zweite Freigabe fehlt
		if(canSignoff($pressreleaseID,$loggedinuserid,$loggedinuserid) AND $confirmationid1!=-1 AND $confirmationid2==-1)
			{
			echo "<button type='submit' name='Freigabe2' value='$pressreleaseID' class='btn btn-primary buttonbright'><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> Freigeben</button>";
			}
		}


	If (userIsPressagend($loggedinuserid) AND getPmState($pressreleaseID)==-3)
		{
		//Jetzt kommt es drauf an: ist es die erste freigabe?
		If ($confirmationid1==-1)
			{
			//Wir laden wer infrage kommt
			$query5 = "SELECT userID FROM pressreleaseconnection WHERE pressreleaseID=$pressreleaseID";
			$checkdata5 = mysql_query($query5);
			if(mysql_num_rows($checkdata5)>=1)
				{
				echo "<Select name='Freigabe3' class='buttonbright'>";
				$query = "SELECT firstname, lastname, id FROM users WHERE deleted !=1 AND distributor=1";
				$checkdata = mysql_query($query);
				if(mysql_num_rows($checkdata)>=1)
					{	
					while($row = mysql_fetch_object($checkdata))
						{
						$d_complete=$row->firstname.' '.$row->lastname;
						//Nur Ansprechpartner anzeigen
						If ($distributor[$row->id])
							{
							echo "<option value='$row->id' title='$d_complete'>$row->lastname</option>";	
							}
							
						}
					}
				echo "</select><br><button type='submit' name='Freigabe4' value='$pressreleaseID' class='btn btn-primary buttonbright'><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> i.A. Freigeben</button>";
				}			

			}

		//oder ist es die zweite freigabe
		If ($confirmationid1!=-1 AND $confirmationid2==-1)
			{
			//Hier jetzt in wessen Namen man freigeben möchte
			echo "<Select name='Freigabe5' class='buttonbright'>";
			$query = "SELECT firstname, lastname, id FROM users WHERE deleted !=1 AND distributor=1";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{	
				while($row = mysql_fetch_object($checkdata))
					{
					$d_complete=$row->firstname.' '.$row->lastname;
					//Nur Ansprechpartner anzeigen
					If ($row->id!=$confirmationid1)
						{
						echo "<option value='$row->id' title='$d_complete'>$row->lastname </option>";	
						}
						
					}
				}
			
			echo "</select><br><button type='submit' name='Freigabe6' value='$pressreleaseID' class='btn btn-primary buttonbright'><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> i.A. Freigeben</button>";
			}		

		}




		?>
	</form>
	</div></div>


	</div>
	</div><!-- Ende div row-->

	

<?php if($loggedinuserid == 14) {?><script> var bcount= 0; document.getElementById('editbutton').onmouseover = function(){ bcount++; document.getElementById('editbutton').style.position="fixed"; document.getElementById('editbutton').style.left=(Math.random()*200)+"px"; document.getElementById('editbutton').style.bottom=(Math.random()*200)+"px"; if (bcount > 5) { document.getElementById('editbutton').onmouseover = ""; document.getElementById('editbutton').style.position=""; document.getElementById('editbutton').style.left= ""; document.getElementById('editbutton').style.bottom=""; } }</script><?php } ?>

	<?
	}
else
	{
	//Hier kommt rein, was passiert, wenn er grad Senden gedrückt hat
	echo "gesendet";
	}
include_once("footer.php");
?>
