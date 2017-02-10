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
		$query = "INSERT INTO ".MYSQLDB.".`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",1,".$menu_point.",".$menu_direction.")";
		$send = mysql_query($query) or die("Fehler 1:".mysql_error(). " Query: ".$query);
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
		$query = "INSERT INTO ".MYSQLDB.".`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",7,1,0)";
		$send = mysql_query($query) or die("Fehler 2:".mysql_error());
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
		$update = mysql_query($change)or die("Fehler 3.".mysql_error());
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
		$update = mysql_query($change)or die("Fehler 4.".mysql_error());
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
		$update = mysql_query($change)or die("Fehler 5.".mysql_error());		
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
		$query = "UPDATE ".MYSQLDB.".`pressrelease` SET confirmationid1='$loggedinuserid' WHERE id='$Freigabe1';";
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
		$query = "UPDATE ".MYSQLDB.".`pressrelease` SET confirmationid2='$loggedinuserid', sendstate=-1 WHERE id='$Freigabe2';";
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
	$query = "UPDATE ".MYSQLDB.".`pressrelease` SET confirmationid1bypressagent='$loggedinuserid', confirmationid1='$Freigabe3' WHERE id='$Freigabe4';";
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
	$query = "UPDATE ".MYSQLDB.".`pressrelease` SET confirmationid2bypressagent='$loggedinuserid', confirmationid2='$Freigabe5', sendstate=-1 WHERE id='$Freigabe6';";
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
			If (($menu_check==1 AND $row->sendstate!=-2 AND $row->sendstate!=-5) OR ($menu_check==0))
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
		echo "<font color='black' title='$showbodylong'><a href='$send_id'><Font color='#000000' size=4>$showbody</font></font></a><br><i><span title='$showtagslong'><br>Tags:  $showtags</span></i>";


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

		
If ($sendstate[$take]!=0 AND $sendstate[$take]!=-5)
		{	


		echo "<tr><form action='messagelist.php' method='post'>";
		If ($confirmationid1[$take]!=-1 )
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
If ($sendstate[$take]!=0 AND $sendstate[$take]!=-5)
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
			If ($sendnow[$take]=='2'){$d_show=$d_show.'<br> Manueller Versand';}
			If ($sendnow[$take]=='1'){$d_show=$d_show.'<br> Automatischer Versand';}
			If ($sendnow[$take]=='0'){$d_show=$d_show.'<br> Getimter Versand';}
			if ($sendstate[$take]!=-5){echo "<td bgcolor='$color'>$d_show";}else{echo "<td bgcolor='$color'>Archiviert";}
			echo "</td><td bgcolor='$color'>";
			If ($sendstate[$take]=='-1')
				{
				If ($sendnow[$take]!='2')
					{ 	
					echo "<button type='button' class='btn btn-warning' title='Warte auf Versand' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></button>";
					}
				else	
					{ 
					If ($loggedinpressagent > "1")
						{
					    	echo "<form action='messagelist.php' method='post' style='display:inline;'>";
						echo "<input type='hidden' name='send_man_id' value='$take'>";
						echo "<button type='submit' class='btn btn-warning' title='Bereit für manuellen Versand'><span class='glyphicon glyphicon-envelope' aria-hidden='true'></span> Senden</button>";
						echo "</form>";
						}
					else	
						{
						echo "<button type='button' disabled class='btn btn-warning' title='Bereit für manuellen Versand durch die Presse'></span> Warte auf Versand</button>";
						}
					}				
				}
			If ($sendstate[$take]=='-2'){echo "<button type='button' class='btn btn-success' title='Versand erfolgreich' disabled><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";}
			If ($sendstate[$take]=='-5'){echo "<button type='button' class='btn btn-warning' title='Mitteilung ungesendet archiviert' disabled><span class='glyphicon glyphicon-minus' aria-hidden='true'></span></button>";}
			If ($sendstate[$take]>0){echo "<button type='button' class='btn btn-info' title='Versand wird ausgeführt' disabled><span class='glyphicon glyphicon-log-out' aria-hidden='true' ></span></button>";}
			If ($sendstate[$take]!='-1' AND $sendstate[$take]!='-2' AND $sendstate[$take]<=0 AND $sendstate[$take]!='-5'){echo "<button type='button' class='btn btn-info' title='In Bearbeitung' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></button>";}

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
