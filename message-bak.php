<?
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
	$query = "SELECT ID FROM `users` WHERE pressagent = 1 AND ID = $user";
	
	$checkdata = mysql_query($query);
	echo "<!--  $query	checkdata	$checkdata -->"; 
	if(mysql_num_rows($checkdata)==1){
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

//Speichern nur ausführen, wenn Speichern oder Senden gedrück wurde
If ($action=='Speichern' OR $action=='Senden')
	{
			
		if ($pressreleaseID == "-1"){
			$query = "INSERT INTO `pewosa`.`pressrelease` ( `subject` , `body` , `pressagentid`, `tags`, `contact`, `sendstate`, `sendnow` ) VALUES ('$sqlBetreff', '$sqlBody', '$sqlpressagent','$sqlTags','$sqlContact','0',$sendnow);";
			$send = mysql_query($query) or die("Fehler:".mysql_error());
			$pressreleaseID = mysql_insert_id();}
		else {
			$query = "UPDATE `pewosa`.`pressrelease` SET 
			subject='$sqlBetreff', body='$sqlBody', pressagentid='$sqlpressagent', tags='$sqlTags', contact='$sqlContact' 
			, confirmationid1='-1', confirmationid2='-1' 
			, confirmationid1bypressagent='-1', confirmationid2bypressagent='-1', sendnow=$sendnow 
			WHERE id='$sqlpressreleaseID';";
			$send = mysql_query($query) or die("Fehler:".mysql_error());}
		//echo "<p>SQLQUERY: $query</p>";
		//echo "SQLResult: $send, id: $pressreleaseID";

		
		//Hier werden alle Verknüpfungen zu Verteilern und Co gelöscht
		$query = "DELETE FROM `pewosa`.`pressreleaseconnection` WHERE pressreleaseID=".$pressreleaseID.";";
		$send = mysql_query($query) or die("Fehler:".mysql_error());
		//echo "<p>SQLResult: $send, $query</p>";

		//Verteilerlisten einfügen
		For ($i=1;$i<=$listmaxid;$i++){
			If (isset($list[$i])) 
				{
					$query = "INSERT INTO `pewosa`.`pressreleaseconnection` ( `pressreleaseID`, `listID`) VALUES (".$pressreleaseID.",".$i.");";
					//echo "<p>Query: $query</p>";
					$send = mysql_query($query) or die("Fehler:".mysql_error());
					//echo "<p>SQLResult: $send</p>";
				}
		}

		//Einzelempfänger einfügen
		For ($i=1;$i<=$singlecustomersmaxid;$i++){
			If (isset($singlecustomer[$i])) 
				{
					$query = "INSERT INTO `pewosa`.`pressreleaseconnection` ( `pressreleaseID`, `customerID`) VALUES (".$pressreleaseID.",".$i.");";
					//echo "<p>Query: $query</p>";
					$send = mysql_query($query) or die("Fehler:".mysql_error());
					//echo "<p>SQLResult: $send</p>";
				}
		}

		//Ansprechpartner einfügen/ Damit werden auch die Abgeordneten festgelegt, die in der PM-Übersicht die 1te Freigabe machen dürfen. Die zweite dürfen alle Abgeordneten!!!
		For ($i=1;$i<=$distributormaxid;$i++){
			If (isset($distributor[$i])) 
				{
					$query = "INSERT INTO `pewosa`.`pressreleaseconnection` ( `pressreleaseID`, `userID`) VALUES (".$pressreleaseID.",".$i.");";
					//echo "<p>Query: $query</p>";
					$send = mysql_query($query) or die("Fehler:".mysql_error());
					//echo "<p>SQLResult: $send</p>";
				}

		}
	//Wenn es abgeschickt wird, soll der Sendagent und Senddate noch nachgetragen werden
	
	//Wir machen aus dem leserlichen Datum ein englisches
	//aber nur wenn nicht sendnow ausgewählt ist, dann nehmen wir einfach die aktuelle Zeit
	If ($sendnow!='sendnow')
		{
		$teile = explode(".", $senddate);
		$jahr=$teile[2];
		$monat=$teile[1];
		$tag=$teile[0];
		$senddate_db=$jahr."-".$monat."-".$tag." ".$sendtime;
		}
	else
		{
		$senddate_db="CURTIME( )";	
		}
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
				$query = "UPDATE `pewosa`.`pressrelease` SET senddate='$senddate_db', sendagent='$loggedinuserid', sendstate='-1' WHERE id='$sqlpressreleaseID';";
				$send = mysql_query($query) or die("Fehler:".mysql_error());
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
	//Erstmal prüfen wir den Sendestatus
	$query = "SELECT sendstate FROM pressrelease WHERE id=$pressreleaseID";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)==1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			$sendstate=$row->sendstate;
			}
		}	
	If ($sendstate==0)
		{
		//Weg damit
		$query = "DELETE FROM `pewosa`.`pressrelease` WHERE id=".$pressreleaseID.";";
		$send = mysql_query($query) or die("Fehler:".mysql_error());
		//Script abbrechen!
		echo "<h3>Pressemitteilung <i>$Betreff</i> wurde gelöscht</h3>";include_once("footer.php");exit();
		}
	$pressreleaseID=-1;	
	}

//Hier wird freigegeben	
If (($action=='Freigeben') && userIsPressagend($loggedinuserid) )
	{
	if ($sendnow){$sendnowtime = " ,senddate=CURTIME() ";} else {$sendnowtime="";};

	$query = "UPDATE `pewosa`.`pressrelease` SET sendstate='-3'$sendnowtime, sendagent=$loggedinuserid WHERE id='$sqlpressreleaseID';";
	$send = mysql_query($query) or die("Fehler:".mysql_error());
	echo "<button type='button' class='btn btn-success'>Freigegeben</button>";	
	}


//echo "<p>pressreleaseID = $pressreleaseID</p>";


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
	If (!isset($check_senddate) OR $check_senddate=='0000-00-00 00:00:00'){$disabled='';}else{$disabled="disabled";}
	?>

	<!--<div class="row row-offcanvas row-offcanvas-right">-->



	<?echo "<form action='message.php' method='post'>";?>
	<?php
	echo "<input type=\"hidden\" name=\"pressreleaseID\" value=\"$pressreleaseID\">";
	?>






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
				$distributionHTML =  $distributionHTML."<INPUT $checked $disabled type='checkbox' name=\"".$boxname."\"> ".$row->name."<br> ";
				}
			}
	//echo "<select name=\"distribution\" size=\"".$counter."\" multiple class=\"rightCol\">";
	echo "<input type=\"hidden\" name=\"listmaxid\" value=\"".$listmaxid."\">";
	echo $distributionHTML;
	$distributionHTML ="";

	//</select>
	?>


	<br>Einzelne Empfänger hinzufügen:<br/>
	<?php if ($addsinglecustomers==true) { $checked = " checked ";$display=" "; } else {$checked="";$display=" style=\"display:none\" ";};	
	echo "<input $checked type=\"checkbox\" name=\"addsinglecustomers\" id=\"addsinglecustomers\"><br/>";
	echo "<span id=\"singlecustomersarea\" $display>";?>
	<?php
		$counter=0;
		$query = "SELECT firstname, lastname, company, id FROM customer WHERE deleted !=1 ORDER BY lastname";
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
				$distributionHTML =  $distributionHTML."<INPUT type='checkbox' $checked $disabled name=\"".$boxname."\"> ".$row->firstname." ".$row->lastname." (".$row->company.")"."<br>";
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
	echo "	<input checked type=\"checkbox\" name=\"sendnow\" id=\"sendnow\" value=\"sendnow\">";
	} else{
		$counter=0;
		$query = "SELECT sendnow FROM pressrelease WHERE id = $pressreleaseID";
		$checkdata = mysql_query($query);
		$distributorHTML ="";
		$distributormaxid = 0;
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$counter++;
				if($row->sendnow){
				$sendnowchecked = " checked ";}
				else {$sendnowchecked = "";}
				}
			}
		echo "	<input ".$sendnowchecked." type=\"checkbox\" name=\"sendnow\" id=\"sendnow\" value=\"sendnow\">";
	}
?>



	<span id="sendlater" style="display:none">
	<p class="rightCol"><span class="title">Sendedatum:</span><br/>
	<? 
	$rightnow=date("d.m.Y"); 
	echo "<input class='rightCol' type='date' name='senddate' value='$rightnow'><br/>";
	$rightnow2=date("H:i");
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


	 
	<div class="jumbotron">

	<div class="panel panel-default">
	  <div class="panel-heading"><h3 class="panel-title">Betreff:</h3></div>
	  <div class="panel-body">
	  
	<? echo "<input name='Betreff' type='text' class='mainCol' value='$sqlBetreff' $disabled>"; ?>

	 </div>
	</div>


	<div class="panel panel-default">
	  <div class="panel-heading"><h3 class="panel-title">Text der Pressemitteilung:</h3></div>
	  <div class="panel-body">
	  
	  <?echo "<textarea name='body'  rows='30' class='mainCol' $disabled>$sqlBody</textarea>";?>
	  
	  <br/>
	<?echo "<textarea name='contact' id='contact' class='mainCol' rows='4' $disabled>$sqlContact</textarea>";?>
	</div>
	</div>

<!-- ************************** Ansprechpartner Update Script ************************************ -->
	 <script>

	function updateContactInfo(){
		var elemcontact = document.getElementById('contact');
		elemcontact.disabled = true;
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
	<?
	//Hier muss ich checken ob alle wichtigen Daten ausgefüllt sind
	//Das geht aber nur im Javascript???
	//$complete=TRUE;
	//If ($sqlBody==''){$complete=FALSE;}
	//If ($sqlContact==''){$complete=FALSE;}
	
	
	If ($disabled!='disabled' AND ($sendstate==0 OR $sendstate=-3))
		{
		?>
		<button type="submit" name="action" value="Speichern" class="btn btn-info"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"'></span> Speichern</button>
		<?
		If ($sendstate!=-3 AND $pressreleaseID!=-1 AND userIsPressagend($loggedinuserid))
			{?>
			<button type="submit" name="action" value="Freigeben" class="btn btn-success"><span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"'></span> Pressefreigabe </button>
			<?}


		}
		
	//<input type='button' value='Zur Übersicht ohne Speichern' onclick="window.location='./';">
	//Geht scheinbar nicht
	?>
	
	<button type="button" name="action" value="" class="btn btn-info" onclick="window.location.href='./';"><span class="glyphicon glyphicon-share" aria-hidden="true"'></span> Zur Übersicht ohne Speichern</button>


	<?
	/*If ($confirmationid1!='-1' AND $confirmationid2!='-1' AND $pressreleaseID!=-1 AND $sendstate==-3)
		{
		//Senden dürfen nur Pressagents!
		$query = "SELECT id FROM users WHERE id=$loggedinuserid AND deleted !=1 AND pressagent=1";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{		
			?>
			<input type='submit' name="action" value='Senden' style="background:green;">
			<?
			}
		}*/
	//Löschen wir ausgeblendet wenn der sendstate nicht null ist oder die pm neu und ungespeichert ist
	If ($sendstate==0)
		{
		?>

		<button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('PM wirklich löschen?');"><span class="glyphicon glyphicon-trash" aria-hidden="true"'></span> Löschen</button>
		<?
		}
		?>
	</div></div>


	</div>

	</form>
	<!--</div>--><!--/row-->


	<?
	}
else
	{
	//Hier kommt rein, was passiert, wenn er grad Senden gedrückt hat
	echo "gesendet";
	}
include_once("footer.php");
?>
