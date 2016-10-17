<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$pagetitle= "Pressemitteilung Bearbeiten";
include_once("header.php");
include_once("pushmsg.php");
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
<?
$pagetitle='Pressemitteilungen';
include_once("header.php");
include_once("pushmsg.php");

//Kein Refresh, wenn PeWoSa App aufruft
if($_SERVER['HTTP_USER_AGENT'] != "de.ulikoenig.pewosa/android"){
echo "<meta http-equiv=\"refresh\" content=\"30\" > ";
}

//Wir prüfen erstmal, ob es eine Speicherung für die Sortierung dieser Seite gibt
//1=PMs, 2=Adressatenliste, 3=Verteilerliste, 4=Nutzerliste
	$query = "SELECT * FROM sorting WHERE user_id=$loggedinuserid AND menu_id=1";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)==1)
		{
		//Es gibt eine gespeicherte Sortierung, also lesen wir sie aus
		while($row = mysql_fetch_object($checkdata))
			{
			//Nach diesem Menüpunkt wird sortiert...
			$menu_point=$row->menu_point;
			//Und zwar in diese Richtung
			$menu_direction=$row->menu_direction;
			}
		}
	else
		{
		//Es gibt noch keine Speicherung für diesen Nutzer und dieses Menü? - Dann legen wir eine fest und in der Datenbank an
		$menu_point=1;
		$menu_direction=0;
		$query = "INSERT INTO `pewosa`.`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",1,".$menu_point.",".$menu_direction.")";
		$send = mysql_query($query) or die("Fehler:".mysql_error());
		}

	//Hier schauen wir noch, ob es ein-ausblenden schon gibt
	$query = "SELECT * FROM sorting WHERE user_id=$loggedinuserid AND menu_id=7";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)==1)
		{
		//Es gibt eine gespeicherte Sortierung, also lesen wir sie aus
		while($row = mysql_fetch_object($checkdata))
			{
			//Nach diesem Menüpunkt wird sortiert...
			$menu_check=$row->menu_direction;
			}
		}
	else
		{
		//Es gibt noch keine Speicherung für diesen Nutzer und dieses Menü? - Dann legen wir eine fest und in der Datenbank an
		$query = "INSERT INTO `pewosa`.`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",7,1,0)";
		$send = mysql_query($query) or die("Fehler:".mysql_error());
		$menu_check='0';	
		}



	//Nutzer will Sortierung verändern? -Na dann hier lang!
	If (isset($_POST['sorting']))
		{
		$take=$_POST['sorting'];
		//Nu schauen wir erstmal, auf welchen Menu-Punkt fokussiert ist und in welche Richtung er zeigt
		If ($take==$menu_point AND $menu_direction==0){$dir=1;}
		If (($take==$menu_point AND $menu_direction==1) OR ($take!=$menu_point)){$dir=0;}
		$change = "UPDATE sorting Set menu_point='".$take."',menu_direction='".$dir."' WHERE user_id=$loggedinuserid AND menu_id=1";
		$update = mysql_query($change)or die("Fehler.".mysql_error());
		//Natürlich müssen wir nun auch anpassen
		$menu_point=$take;
		$menu_direction=$dir;					
		}


	//Nutzer will ein-ausblenden? -Na dann hier lang!
	If (isset($_POST['blending']))
		{
		//$take=$_POST['blending'];
		If ($menu_check==1){$take=0;}else{$take=1;}
		$change = "UPDATE sorting Set menu_direction='".$take."' WHERE user_id=$loggedinuserid AND menu_id=7";
		$update = mysql_query($change)or die("Fehler.".mysql_error());
		//Natürlich müssen wir nun auch anpassen
		$menu_check=$take;
		//echo "test $take";				
		}

//Manueller Versand
If (isset($_POST['send_man_id']))
	{
	$send_man_id=$_POST['send_man_id'];
	//Prüfen ob es die Mail gibt und sie nicht schon verschickt wurde
	$query = "SELECT id FROM pressrelease WHERE id=$send_man_id AND sendnow=2 AND sendstate!=-2";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)==1)
		{
		//Gibt es genau eine auf die es stimmt wird diese geupdatet
		$senddate_db=date("Y-m-d H:i:s");
		$change = "UPDATE pressrelease Set sendnow='1',senddate='".$senddate_db."' WHERE id=$send_man_id";
		$update = mysql_query($change)or die("Fehler.".mysql_error());		
		}
	}

		

//Freigaben eintragen
If (isset($_POST['Freigabe1']))
	{
	//Sicherheitshalber müssen wir die gleichen Abfragen wie unten machen
	//$Freigabe1=$_POST['free_id1'];
	$Freigabe1=$_POST['Freigabe1'];
	$youmaygive=FALSE;
	//Wir schauen erstmal, ob der Nutzer in der Liste der Ansprechpartner ist
	$query = "SELECT id FROM pressreleaseconnection WHERE userID=$loggedinuserid AND pressreleaseID=$Freigabe1";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)==1)
		{
		//Wir schauen jetzt noch sicherheitshalber, ob der Nutzer in der Liste der Freigeber ist
		$query = "SELECT id FROM users WHERE id=$loggedinuserid AND deleted !=1 AND distributor=1";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{
			$youmaygive=TRUE;	
			//Uuuuund wir gucken, ober nicht schon die zweite Freigabe erteilt hat:
			$query = "SELECT id FROM pressrelease WHERE confirmationid2=$loggedinuserid AND id=$Freigabe1";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)==1)
				{
				$youmaygive=FALSE;	
				}
			}										
		}
	If ($youmaygive)
		{
		//Freigabe erfolgt
		$query = "UPDATE `pewosa`.`pressrelease` SET confirmationid1='$loggedinuserid' WHERE id='$Freigabe1';";
		$send = mysql_query($query) or die("Fehler:".mysql_error());
		//pushnotification Freigabe 1 löschen
		removePMReleaseRequest ($Freigabe1);
		//pushnotification Freigabe 2 setzen
		newPM2ndReleaseRequest ($Freigabe1);		
		}
	}
If (isset($_POST['Freigabe2']))
	{
	//Sicherheitshalber müssen wir die gleichen Abfragen wie unten machen	
	//$Freigabe2=$_POST['free_id2'];
	$Freigabe2=$_POST['Freigabe2'];
	$youmaygive=FALSE;$youcouldgive=FALSE;
	$query = "SELECT id FROM users WHERE id=$loggedinuserid AND distributor=1 AND deleted !=1";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)==1)
		{
		$youmaygive=TRUE;$youcouldgive=TRUE;	
		//Uuuuund wir gucken, ober nicht schon die erste Freigabe erteilt hat:
		$query = "SELECT id FROM pressrelease WHERE confirmationid1=$loggedinuserid AND id='$Freigabe2'";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{
			$youmaygive=FALSE;	
			}
		}
	If ($youmaygive)
		{
		//2te Freigabe erfolgt
		//Damit wird auch automatisch sendstate auf pending gesetzt
		$query = "UPDATE `pewosa`.`pressrelease` SET confirmationid2='$loggedinuserid', sendstate=-1 WHERE id='$Freigabe2';";
		$send = mysql_query($query) or die("Fehler:".mysql_error());
		//pushnotification Freigabe 2 löschen
		removePM2ndReleaseRequest($Freigabe2);
		}		
	}

//i.A Freigaben eintragen
//Freigabe eins
If (isset($_POST['Freigabe3']))
	{
	$Freigabe3=$_POST['Freigabe3'];	
	$Freigabe4=$_POST['Freigabe4'];
	//Hier müssten eigentlich noch Sicherheitstests geben
	$query = "UPDATE `pewosa`.`pressrelease` SET confirmationid1bypressagent='$loggedinuserid', confirmationid1='$Freigabe3' WHERE id='$Freigabe4';";
	$send = mysql_query($query) or die("Fehler:".mysql_error());
	//pushnotification Freigabe 1 löschen
	removePMReleaseRequest ($Freigabe4);
	//pushnotification Freigabe 2 setzen
	newPM2ndReleaseRequest ($Freigabe4);	
	}

//Freigabe2	
If (isset($_POST['Freigabe5']))
	{
	$Freigabe5=$_POST['Freigabe5'];	
	$Freigabe6=$_POST['Freigabe6'];
	//Hier müssten eigentlich noch Sicherheitstests geben
	$query = "UPDATE `pewosa`.`pressrelease` SET confirmationid2bypressagent='$loggedinuserid', confirmationid2='$Freigabe5', sendstate=-1 WHERE id='$Freigabe6';";
	$send = mysql_query($query) or die("Fehler:".mysql_error());
	//pushnotification Freigabe 2 löschen
	removePM2ndReleaseRequest($Freigabe6);
	}	
	
	//Laden aller Pressemitteilungen
	$counter=0;

	//Standard
	$query = "SELECT * FROM pressrelease ORDER BY id DESC";
	//Aber wir haben ja eine gespeicherte Sortierung zur Auswertung
	//Sortierung nach neuestem Eintrag
	If ($menu_point==1 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY id ASC";
		}
	If ($menu_point==1 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY id DESC";
		}
	//Sortierung nach Betreff
	If ($menu_point==2 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY subject ASC";
		}
	If ($menu_point==2 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY subject DESC";
		}
	//Sortierung nach erster Freigabe
	//leider nicht vom alphabet, sondern von id abhängig
	//Müsste nach NULL checken und dann entscheiden
	If ($menu_point==3 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY confirmationid1 ASC, confirmationid1bypressagent ASC";
		}
	If ($menu_point==3 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY confirmationid1 DESC, confirmationid1bypressagent DESC";
		}
	//Sortierung nach zweiter Freigabe
	//leider nicht vom alphabet, sondern von id abhängig
	//Müsste nach NULL checken und dann entscheiden
	If ($menu_point==4 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY confirmationid2 ASC, confirmationid2bypressagent ASC";
		}
	If ($menu_point==4 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY confirmationid2 DESC, confirmationid2bypressagent DESC";
		}
	//Sortierung nach Pressagent
	//leider nicht vom alphabet, sondern von id abhängig
	If ($menu_point==5 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY pressagentid ASC";
		}
	If ($menu_point==5 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY pressagentid DESC";
		}
	//Sortierung nach Sendagent
	//leider nicht vom alphabet, sondern von id abhängig
	If ($menu_point==6 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY sendagent ASC";
		}
	If ($menu_point==6 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY sendagent DESC";
		}
	//Sortierung nach tags
	If ($menu_point==7 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY tags ASC";
		}
	If ($menu_point==7 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY tags DESC";
		}
	//Sortierung nach Sendedatum
	If ($menu_point==8 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY senddate ASC";
		}
	If ($menu_point==8 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY senddate DESC";
		}
	//Sortierung nach Sendestatus
	If ($menu_point==9 AND $menu_direction==0)
		{
		$query = "SELECT * FROM pressrelease ORDER BY sendstate ASC";
		}
	If ($menu_point==9 AND $menu_direction==1)
		{
		$query = "SELECT * FROM pressrelease ORDER BY sendstate DESC";
		}

	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			If (($menu_check==1 AND $row->sendstate!=-2) OR ($menu_check==0))
				{
				$counter++;
				$ids[$counter]=$row->id;
				$subject[$row->id]=$row->subject;
				$body[$row->id]=$row->body;
				$senddate[$row->id]=$row->senddate;
				$confirmationid1[$row->id]=$row->confirmationid1;
				$confirmationid2[$row->id]=$row->confirmationid2;
				$confirmationid1bypressagent[$row->id]=$row->confirmationid1bypressagent;
				$confirmationid2bypressagent[$row->id]=$row->confirmationid2bypressagent;			
				$pressagentid[$row->id]=$row->pressagentid;
				$sendagent[$row->id]=$row->sendagent;
				$sendstate[$row->id]=$row->sendstate;
				$sendnow[$row->id]=$row->sendnow;
				$tags[$row->id]=$row->tags;
				}
			}
		}
	

		
	?>
	
	<div id="mainview">
<?

?>
	<table border=0 class="centred">


	<tr><td colspan=42 class='cell' align='center'>
<?

	echo "<form action='message.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-primary' title='Neue PM' name='customer' value='xxx'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Neue PM </button></form></td></tr><tr><td colspan=42 class='cell' align='center'>";

	//Nu brauchen wir Buttons um die Sortierung anzuzeigen

	echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Neu/Alt sortieren' name='sorting' value='1'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==1 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==1 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=1){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Neu/Alt </button></form>";

	echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Betreff sortieren' name='sorting' value='2'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==2 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==2 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=2){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Betreff </button></form>";

	/*echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Freigabe 1 sortieren' name='sorting' value='3'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==3 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==3 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=3){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Freigabe 1 </button></form>";*/

	/*echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Freigabe 2 sortieren' name='sorting' value='4'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==4 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==4 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=4){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Freigabe 2 </button></form>";*/

	/*echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Ansprechpartner bei der Presse sortieren' name='sorting' value='5'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==5 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==5 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=5){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Ansprechpartner, Presse </button></form>";*/

	/*echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Pressefreigeber/in sortieren' name='sorting' value='6'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==6 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==6 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=6){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Presse </button></form>";*/

	/*echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Tags sortieren' name='sorting' value='7'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==7 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==7 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=7){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Tags </button></form>";*/

	echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Absendedatum sortieren' name='sorting' value='8'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==8 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==8 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=8){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Sendedatum </button></form>";

	echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Absendestatus sortieren' name='sorting' value='9'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==9 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==9 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=9){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Status </button></form>";

	echo " <form action='messagelist.php' method='post' style='display:inline;'>";
	//echo "<button type='submit' class='btn btn-info' title='Archiv ein-/ausblenden' name='blending' value='1'>";
	If ($menu_check==0){echo "<input type='checkbox' onchange='submit();'>";}
	If ($menu_check==1){echo "<input type='checkbox' onchange='submit();' checked>";}
	echo "<input type='hidden' name='blending' value='1'> Archiv ein-/ausblenden</form><br><br>";
?>
	</td>	
		</tr></table>
		
<?
	
	For ($i=1;$i<=$counter;$i++)
		{
		echo "<div class='panel panel-default'>";


		$take=$ids[$i];
		//If ($color=='#cccccc'){$color='#99ccff';}else{$color='#cccccc';}
		$color='#cccccc';
		$showsubject=$subject[$take];
		$showsubjectlong="";
		if(strlen($showsubject) <= 139){$showsubject = $showsubject;} 
		else 
			{
			$length=139;
			$showsubjectlong = htmlspecialchars ($showsubject);
			$showsubject = preg_replace("/[^ ]*$/", '.', substr($showsubject, 0, $length));
			}	
		$send_id='message.php?pressreleaseID='.$take;	
		//echo "<tr><td class='cell' bgcolor='$color'><a href='$send_id'><span class='badge' title='$showsubjectlong'><Font size='3'>$showsubject</font></span>";
		echo "<div class='panel-heading'><a href='$send_id'><Font size='4'><span class='badge' title='Mitteilungs-Nummer $take'>ID $take</span> $showsubject </font></a>";

		echo "<form action='message.php' method='post' style='display:inline;float: right;'>";
		echo "<button align='right'type='submit' class='btn btn-primary' title='Zu den Details' name='pressreleaseID' value='$take'><span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span> Details</button>";
		echo "</form>";

		echo "</div>";
		echo "<div class='panel-body'>";

		$showbody=$body[$take];
		$showbodylong="";
		if(strlen($showbody) <= 260){$showbody = $showbody; } 
		else 
			{
			$length=260;
			$showbodylong = htmlspecialchars ($showbody);
			$showbody = preg_replace("/[^ ]*$/", '.', substr($showbody, 0, $length));
			}			

		$showtags=$tags[$take];
		$showtagslong="";
		if(strlen($showtags) <= 159){$showtags = $showtags; } 
		else 
			{
			$length=159;
			$showtagslong = htmlspecialchars ($showtags);
			$showtags = preg_replace("/[^ ]*$/", '.', substr($showtags, 0, $length));
			}		
		
		//echo "<br><font color='black' title='$showbodylong'>$showbody</font></a><br><i><span title='$showtagslong'>Tags:  $showtags</span></i></td>";
		echo "<font color='black' title='$showbodylong'><a href='$send_id'><Font color='#000000' size=4>$showbody</font></a><br><i><span title='$showtagslong'><br>Tags:  $showtags</span></i>";


		$query = "SELECT firstname, lastname,id,pressagent FROM users";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)>=1)
			{	
			while($row = mysql_fetch_object($checkdata))
				{
				//Wir holen uns die Namen und ordnen sie zu
				//Was wir vorher noch wissen wollen, ist, ob der aktuell angemeldete Nutzer Abgeordneter, Pressevertreter oder sonstiger User ist
				If ($row->id==$loggedinuserid)
					{
					$kindofuser=3; //Sonstiger User
					If ($row->distributor==1){$kindofuser=1;} //Abgeordneter 
					If ($row->pressagent==1){$kindofuser=2;} //Presseschubse 
					}
				If ($row->id==$confirmationid1[$take])
					{					
					$c1_firstname=$row->firstname;
					$c1_lastname=$row->lastname;
					$c1_complete=$c1_firstname.' '.$c1_lastname;
					}
				If ($row->id==$confirmationid2[$take])
					{					
					$c2_firstname=$row->firstname;
					$c2_lastname=$row->lastname;
					$c2_complete=$c2_firstname.' '.$c2_lastname;
					}
				If ($row->id==$confirmationid1bypressagent[$take])
					{					
					$ia1_firstname=$row->firstname;
					$ia1_lastname=$row->lastname;
					$ia1_complete=$ia1_firstname.' '.$ia1_lastname;
					}
				If ($row->id==$confirmationid2bypressagent[$take])
					{					
					$ia2_firstname=$row->firstname;
					$ia2_lastname=$row->lastname;
					$ia2_complete=$ia2_firstname.' '.$ia2_lastname;
					}						
				If ($row->id==$pressagentid[$take])
					{					
					$p1_firstname=$row->firstname;
					$p1_lastname=$row->lastname;
					$p1_complete='Ansprechpartner bei der Presse ist '.$p1_firstname.' '.$p1_lastname;
					}
				If ($row->id==$sendagent[$take])
					{					
					$p2_firstname=$row->firstname;
					$p2_lastname=$row->lastname;
					$p2_complete='Bearbeitet durch '.$p2_firstname.' '.$p2_lastname;
					}					
				}
			}

		echo "</div><br><table border=0 class='table'> <thead>";

?>
		<tr height=20>
			<th >Freigabe 1</th>
			<th >Freigabe 2</th>		
			<th >Presse</th>
			<th >Versand</th>	
			<th >Status</th>		
		</tr></thead><tbody><?

		
If ($sendstate[$take]!=0)
		{	


		echo "<tr><form action='messagelist.php' method='post'>";
		If ($confirmationid1[$take]!=-1)
			{
			If ($confirmationid1bypressagent[$take]>0)
				{
				//Wenn im Auftrag, wollen wir wissen vom wem genau
				$give_titel='Freigegeben von '.$ia1_complete.' i.A. von '.$c1_complete;
				echo "<td bgcolor='$color'><button type='button' title='$give_titel' class='btn btn-success buttonbright' disabled><span class='glyphicon glyphicon-check' aria-hidden='true'></span> i.A. $c1_lastname</button></td>";
				}
			else
				{
				$give_titel='Freigegeben von '.$c1_complete;					
				echo "<td bgcolor='$color'><button type='button' title='$give_titel' class='btn btn-success buttonbright' disabled><span class='glyphicon glyphicon-check' aria-hidden='true'></span> $c1_lastname</button></td>";
				}				
			}
		else
			{
			//Darf er freigeben, kann er hier freigeben, sonst erscheint 'Noch offen'
			//Freigeben darf hier nur einer der Ansprechpartner der Mail, die zweite Freigabe dürfen alle Abgeordneten geben
			//Natürlich dürfen Freigabe 1 und 2 nicht von dem gleichen Abgeordneten vergeben werden.
			$youmaygive=FALSE;$youcouldgive=FALSE;
			//Wir schauen erstmal, ob der Nutzer in der Liste der Ansprechpartner ist
			$query = "SELECT id FROM pressreleaseconnection WHERE userID=$loggedinuserid AND pressreleaseID=$take";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)==1)
				{
				//Wir schauen jetzt noch sicherheitshalber, ob der Nutzer in der Liste der Freigeber ist
				$query = "SELECT id FROM users WHERE id=$loggedinuserid AND deleted !=1 AND distributor=1 ";
				$checkdata = mysql_query($query);
				if(mysql_num_rows($checkdata)==1)
					{
					$youmaygive=TRUE;$youcouldgive=TRUE;	
					//Uuuuund wir gucken, ober nicht schon die zweite Freigabe erteilt hat:
					$query = "SELECT id FROM pressrelease WHERE confirmationid2=$loggedinuserid AND id=$take";
					$checkdata = mysql_query($query);
					if(mysql_num_rows($checkdata)==1)
						{
						$youmaygive=FALSE;	
						}
					}										
				}						
			If (!$youcouldgive AND !$youmaygive AND $kindofuser==3){echo "<td bgcolor='$color'><button type='button' class='btn btn-warning buttonbright' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Noch offen</button></td>";}
			If ($youcouldgive AND !$youmaygive AND ($kindofuser==2 OR $kindofuser==1)){echo "<td bgcolor='$color'><button type='button' class='btn btn-warning buttonbright' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Bitte warten</button></td>";}
			If ($youcouldgive AND $youmaygive)
				{
				echo "<td bgcolor='$color'>";
				echo "<button type='submit' name='Freigabe1' value='$take' class='btn btn-primary buttonbright'><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> Freigeben</button>";
				echo "</td>";
				}
			//Sonderregel wenn er Pressagent ist und im Auftrag das OK erteilt
			$overwrite=FALSE;
			If (!$youmaygive AND !$youcouldgive)
				{
				//Wir schauen jetzt noch sicherheitshalber, ob der Nutzer in der Liste der Freigeber ist
				$query = "SELECT id FROM users WHERE id=$loggedinuserid AND pressagent=1";
				$checkdata = mysql_query($query);
				if(mysql_num_rows($checkdata)==1)
					{
					echo "<td bgcolor='$color'>";
					
					//Dann brauchen wir jetzt aber die möglichen Ansprechpartner für die erste Freigabe
					$counter2=0;
					$query = "SELECT userID FROM pressreleaseconnection WHERE pressreleaseID=$take";
					$checkdata = mysql_query($query);
					if(mysql_num_rows($checkdata)>=1)
						{	
						while($row = mysql_fetch_object($checkdata))
							{	
							If (isset($row->userID))
								{
								$counter2++;	
								$right_distributor[$counter2]=$row->userID;
								//echo "hab dich! $row->userID<br>";
								}
							}
						}
					//Hier jetzt in wessen Namen man freigeben möchte
					If ($counter2>0)
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
								If (in_array($row->id, $right_distributor))
									{
									echo "<option value='$row->id' title='$d_complete'>$row->lastname</option>";	
									}
									
								}
							}
						echo "</select><br><button type='submit' name='Freigabe4' value='$take' class='btn btn-primary buttonbright'><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> i.A. Freigeben</button>";
						}
					echo "</td>";	
					$overwrite=TRUE;
					}	
				}
			If (!$youcouldgive AND !$overwrite){echo "<td bgcolor='$color'><button type='button' class='btn btn-warning buttonbright' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Noch offen</button></td>";}				
			}	
		}
	else
		{
		echo "<td bgcolor='$color' colspan=1><button type='button' class='btn btn-info ' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> In Bearbeitung</button></td>";
		echo "<td bgcolor='$color' colspan=1><button type='button' class='btn btn-info ' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> In Bearbeitung</button></td>";	
		}
If ($sendstate[$take]!=0)
		{	
		If ($confirmationid2[$take]!=-1)
			{
			If ($confirmationid2bypressagent[$take]>0)
				{
				//Wenn im Auftrag, wollen wir wissen vom wem genau
				$give_titel='Freigegeben von '.$ia2_complete.' i.A. von '.$c2_complete;
				echo "<td bgcolor='$color'><button type='button' title='$give_titel' class='btn btn-success buttonbright' disabled><span class='glyphicon glyphicon-check' aria-hidden='true'></span> i.A. $c2_lastname</button></td>";
				}
			else
				{
				$give_titel='Freigegeben von '.$c2_complete;					
				echo "<td bgcolor='$color'><button type='button' title='$give_titel' class='btn btn-success buttonbright' disabled><span class='glyphicon glyphicon-check' aria-hidden='true'></span> $c2_lastname</button></td>";
				}	
			}
		else
			{
			//Nur möglich, wenn erste Freigabe bereits erteilt!!!	
			//Zweite Freigabe darf jeder Abgeordnete, aber natürlich nicht, wenn er schon die erste Freigab
			$youmaygive=FALSE;$youcouldgive=FALSE;
			$query = "SELECT id FROM users WHERE id=$loggedinuserid AND deleted !=1 AND distributor=1";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)==1)
				{
				$youmaygive=TRUE;$youcouldgive=TRUE;	
				//Uuuuund wir gucken, ober nicht schon die erste Freigabe erteilt hat:
				$query = "SELECT id FROM pressrelease WHERE confirmationid1=$loggedinuserid AND id=$take";
				$checkdata = mysql_query($query);
				if(mysql_num_rows($checkdata)==1)
					{
					$youmaygive=FALSE;	
					}
				}
			If ($confirmationid1[$take]==-1){$youmaygive=FALSE;}
			If ($youcouldgive AND !$youmaygive AND ($kindofuser==1 OR $kindofuser==2))
				{
				echo "<td bgcolor='$color'><button type='button' class='btn btn-warning buttonbright' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Bitte warten</button></td>";
				//echo "<td bgcolor='$color'><button type='button' class='btn btn-warning buttonbright' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Bitte warten</button></td>";
				}

			If (!$youcouldgive AND !$youmaygive AND $kindofuser==3){echo "<td class='cell' bgcolor='$color'><button type='button' class='btn btn-warning buttonbright' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Noch offen</button></td>";}

			If ($youcouldgive AND $youmaygive){echo "<td bgcolor='$color'><button type='submit' name='Freigabe2' value='$take' class='btn btn-primary buttonbright'><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> Freigeben</button></td>";}
			//Sonderregel wenn er Pressagent ist und im Auftrag das OK erteilt
			$overwrite=FALSE;
			If (!$youmaygive AND !$youcouldgive AND $confirmationid1[$take]!=-1)
				{
				$overwrite=TRUE;
				//Wir schauen jetzt noch sicherheitshalber, ob der Nutzer in der Liste der Freigeber ist
				$query = "SELECT id FROM users WHERE id=$loggedinuserid AND deleted !=1 AND pressagent=1";
				$checkdata = mysql_query($query);
				if(mysql_num_rows($checkdata)==1)
					{
					echo "<td bgcolor='$color'>";
					
					//Dann brauchen wir jetzt den ersten Ansprechpartner für die erste Freigabe
					$query = "SELECT confirmationid1 FROM pressrelease WHERE id=$take";
					$checkdata = mysql_query($query);
					if(mysql_num_rows($checkdata)==1)
						{	
						while($row = mysql_fetch_object($checkdata))
							{	
							$icant=$row->confirmationid1;
							}
						}
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
							If ($row->id!=$icant)
								{
								echo "<option value='$row->id' title='$d_complete'>$row->lastname </option>";	
								}
								
							}
						}
					
					echo "</select><br><button type='submit' name='Freigabe6' value='$take' class='btn btn-primary buttonbright'><span class='glyphicon glyphicon-thumbs-up' aria-hidden='true'></span> i.A. Freigeben</button>";
					echo "</td>";					
					}
				}
			If (!$youcouldgive AND !$overwrite){echo "<td bgcolor='$color'><button type='button' class='btn btn-warning buttonbright' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span> Noch offen</button></td>";}				
			}	
		//echo "<td class='cell' bgcolor='$color'><button type='button' title='$p1_complete' class='btn btn-info buttonbright2' disabled> $p1_lastname</button></td>";
		echo "</form>";
		}
		If ($sendagent[$take]!='-1')
			{		
			echo "<td bgcolor='$color'><button type='button' title='$p2_complete' class='btn btn-success buttonbright4' disabled><span class='glyphicon glyphicon-check' aria-hidden='true'></span> $p2_lastname</button></td>";
			}
		else
			{
			echo "<td bgcolor='$color'><button type='button' class='btn btn-warning buttonbright4' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true' disabled></span> Noch offen</button></td>";
			}				
		
		If ($senddate[$take]!='')
			{
			//0 Entwurf
			//-1 pending
			//>0 sending 
			//-2 sent	
			$d_show = date("d.m.Y H:i",strtotime($senddate[$take]));
			echo "<td bgcolor='$color'>$d_show";
			echo "</td><td bgcolor='$color'>";
			If ($sendstate[$take]=='-1')
				{
				If ($sendnow[$take]!='2')
					{ 	
					echo "<button type='button' class='btn btn-warning' title='Warte auf Versand' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></button>";
					}
				else	
					{ 	
				    echo "<form action='messagelist.php' method='post' style='display:inline;'>";
					echo "<input type='hidden' name='send_man_id' value='$take'>";
					echo "<button type='submit' class='btn btn-warning' title='Bereit für manuellen Versand'><span class='glyphicon glyphicon-envelope' aria-hidden='true'></span> Senden</button>";
					echo "</form>";
					}				
				}
			If ($sendstate[$take]=='-2'){echo "<button type='button' class='btn btn-success' title='Versand erfolgreich' disabled><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";}
			If ($sendstate[$take]>0){echo "<button type='button' class='btn btn-info' title='Versand wird ausgeführt' disabled><span class='glyphicon glyphicon-log-out' aria-hidden='true' ></span></button>";}
			If ($sendstate[$take]!='-1' AND $sendstate[$take]!='-2' AND $sendstate[$take]<=0){echo "<button type='button' class='btn btn-info' title='In Bearbeitung' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></button>";}

			echo "</td></td>";
			}
		else
			{
			echo "<td bgcolor='$color'>Nicht versand</td><td class='cell' bgcolor='$color'></td>";	
			}
		




		
		echo "</tr></tbody></table></div>";
		}
		
	?>	
	</div>
	  
<?	
include_once("footer.php");	

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
$sendnow = fetchintvar ('sendnow',"");
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
	If ($sendnow==0)
		{
		//echo "ich bekomme $senddate und...";
		$teile = explode(".", $senddate);
		$jahr=$teile[2];
		$monat=$teile[1];
		$tag=$teile[0];
		$senddate_db=$jahr."-".$monat."-".$tag." ".$sendtime;
		//echo "$senddate_db draus gemacht.";
		//$sendnow=0;
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
		//pushnotification
		newPMReleaseRequest($pressreleaseID);
	} 

//Die Sendefreigabe wird auf die Messagelist umgeleitet und steht deshalb nicht hier

}


//echo "<p>pressreleaseID = $pressreleaseID</p>";

If ($action=='edit') {
	$query = "UPDATE `pewosa`.`pressrelease` SET sendstate='0', sendagent=-1, confirmationid1=-1, confirmationid1bypressagent=-1, confirmationid2=-1, confirmationid2bypressagent=-1 WHERE id='$sqlpressreleaseID';";
	
	//pushnotification löschen
	removePMReleaseRequest ($pressreleaseID);
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

echo "<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\"><nav aria-label=\"Page navigation\"><ul class=\"pagination pagination-lg\"><li $labelentwurf><a>Entwurf</a></li><li $labelPressefrei><a>Pressefreigabe</a></li><li $labelfirstauth><a>1. Freigabe</a></li><li $labelsecondauth><a>2. Freigabe</a></li><li $labelsending><a>Versand</a></li></ul></nav></div>";

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


				if ($disabled) {
					//nichts mehr zu ändern
					if ($checked){
						$distributionHTML =  $distributionHTML."<p>".$row->name."</p>\n";
					}
				} else {
					//Benutzer kann noch auswählen
					$distributionHTML =  $distributionHTML."<span title='$givetitle'><INPUT $checked $disabled type='checkbox' name=\"".$boxname."\"> ".$row->name."</span><br> ";}
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
					if ($disabled) {
						//nichts mehr zu ändern
						if ($checked){
							$distributionHTML =  $distributionHTML."<p>".$row->email."</p>";
						}
					} else {
						//Benutzer kann noch ändern				
						$distributionHTML =  $distributionHTML."<span title='$givetitle'><INPUT type='checkbox' $checked $disabled name=\"".$boxname."\"> ".$row->email." </span><br>";
					} 
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
				
				if ($disabled){
					if ($checked){
						//Benutzer kann nicht ändern
						$distributorHTML =  $distributorHTML."<p> ".$row->firstname." ".$row->lastname."</p>";
					}
				} else {
					//Benutzer kann noch ändern
					$distributorHTML =  $distributorHTML."<INPUT type='checkbox' $checked $disabled name=\"".$boxname."\" id=\"".$boxname."\" phone=\"".$phone."\" naturalname=\"".$row->firstname." ".$row->lastname."\" jobtitle=\"".$jobtitle."\"> ".$row->firstname." ".$row->lastname."<br>";
				}
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
				if ($disabled!='disabled'){
					$pressagentHTML =  $pressagentHTML."<option $checked $disabled value=\"".$row->id."\" phone=\"".$phone."\" jobtitle=\"".$jobtitle."\">".$row->firstname." ".$row->lastname."</option>";
				} else {
					if ($checked != '') {
						$pressagentHTML =  $pressagentHTML."<p value=\"".$row->id."\" phone=\"".$phone."\" jobtitle=\"".$jobtitle."\">".$row->firstname." ".$row->lastname."</p>";
					}
				}



				}
			}
	if ($disabled!='disabled'){echo "<select name=\"pressagent\" id=\"pressagent\" class=\"rightCol\">";}
	echo $pressagentHTML;
	$pressagentHTML ="";
	if ($disabled!='disabled'){echo"</select>";}?>
	</div></div>
<!-- ############ ENDE Presseverantwortlicher: ################################################ -->



<?
If ($disabled!='disabled')
	{
?>
	<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Sendezeit:</h3></div>
	  <div class="panel-body">
 <script>
	function showsendlater () {
		var div = document.getElementById("sendlater");
		document.getElementById("sendlater").style.display="block";
	}
	
	function hidesendlater () {
		var div = document.getElementById("sendlater");
     	document.getElementById("sendlater").style.display="none";
		
		
	}
	
	</script>

	<?php
	
	$sendnowchecked1 = " checked ";
	$sendnowchecked2 = " ";
	$sendnowchecked3 = " ";
	$rightnow=date("d.m.Y");$rightnow2=date("H:i");
	$senddisplay='display:none'; 
	
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
				$sendnowchecked1 = " checked ";
				$sendnowchecked2 = " ";
				$sendnowchecked3 = " ";
				$rightnow=date("d.m.Y");$rightnow2=date("H:i");
				$senddisplay='display:none'; 
				}
			if($row->sendnow==2)
				{
				$sendnowchecked1 = " ";
				$sendnowchecked2 = " checked ";
				$sendnowchecked3 = " ";
				$rightnow=date("d.m.Y");$rightnow2=date("H:i");
				$senddisplay='display:none'; 
				}					
			if($row->sendnow==0) 
				{	
				$sendnowchecked1 = " ";
				$sendnowchecked2 = " ";
				$sendnowchecked3 = " checked ";
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
	echo "Sofort <input ".$sendnowchecked1." type='Radio' name='sendnow' id='sendnow1' value='1' onclick='hidesendlater()' onkeypress='hidesendlater()'> ";
	echo "Manuell <input ".$sendnowchecked2." type='Radio' name='sendnow' id='sendnow2' value='2' onclick='hidesendlater()' onkeypress='hidesendlater()'> ";
	echo "Später <input ".$sendnowchecked3." type='Radio' name='sendnow' id='sendnow3' value='0' onclick='showsendlater()' onkeypress='showsendlater()'><br><br> ";
	
	echo "<span id='sendlater' style='$senddisplay'>";
?>
	<p class="rightCol"><span class="title">Sendedatum:</span><br/>
	<? 
	echo "<input class='rightCol' type='date' name='senddate' value='$rightnow'><br/>";
	echo "<span class='title'>Uhrzeit:</span><br/><input class='rightCol' type='time' name='sendtime' value='$rightnow2'><br/></span>";
	?>

	</p>
	 </div></div>   


	<!--</div>--> <!--sidebar -->  
<?
	}
?>
	</div><!--Ende Seitenleiste-->


	 
	<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">

	<div class="panel panel-default">
	  <div class="panel-heading"><h3 class="panel-title">Betreff:</h3></div>
	  <div class="panel-body">
	 <? If ($disabled=='disabled'){
		$readonly='readonly';
		echo "<p name='Betreff' style='font-size: 16pt'>$sqlBetreff</p>";}
	   else	{
		$readonly='';
		echo "<input name='Betreff' type='text' class='mainCol' value='$sqlBetreff' $readonly style='font-size: 16pt'>";}
	  ?>

	 </div>
	</div>


	<div class="panel panel-default">
	  <div class="panel-heading"><h3 class="panel-title">Text der Pressemitteilung:</h3></div>
	  <div class="panel-body">
	<? If ($disabled=='disabled'){
		$readonly='readonly';
		echo "<p name='body' style='font-size: 16pt'>".nl2br($sqlBody)."</p>";
		 }
	else{
		$readonly='';
		echo "<textarea name='body'  rows='30' class='mainCol' $readonly style='font-size: 16pt'>$sqlBody</textarea>";}
	 ?>
	  
	  <br/>
	<?If ($disabled=='disabled'){
		echo "<p name='contact' style='font-size: 16pt'>".nl2br($sqlContact)."</p>";
	} else {
		echo "<textarea name='contact' id='contact' class='mainCol' rows='4' $readonly>$sqlContact</textarea>";
	}?>
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
	<?php if ($disabled=='disabled'){
		echo "<p name='tags' style='font-size: 16pt'>".nl2br($sqlTags)."</p>";
	} else {
		echo "<input name='tags' type='text' class='mainCol' value='$sqlTags' placeholder='Transparenzgesetz, Gesetzenwurf, Gemüsesuppe' $disabled>";
	}?>
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
