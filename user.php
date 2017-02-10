<?php

$pagetitle='Benutzer Bearbeiten';
include_once("header.php");
if($loggedinadmin < "1")
	{
	die('Sie sind nicht berechtigt diese Funktion zu nutzen.</a>');
}



//Wir prüfen erstmal, ob es eine Speicherung für die Sortierung dieser Seite gibt
//1=PMs, 2=Adressatenliste, 3=Verteilerliste, 4=Nutzerliste
	$query = "SELECT * FROM sorting WHERE user_id=$loggedinuserid AND menu_id=4";
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
		$query = "INSERT INTO ".MYSQLDB.".`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",4,".$menu_point.",".$menu_direction.")";
		$send = mysql_query($query) or die("Fehler:".mysql_error());	
		}


	//Nutzer will Sortierung verändern? -Na dann hier lang!
	If (isset($_POST['sorting']))
		{
		$take=$_POST['sorting'];
		//Nu schauen wir erstmal, auf welchen Menu-Punkt fokussiert ist und in welche Richtung er zeigt
		If ($take==$menu_point AND $menu_direction==0){$dir=1;}
		If (($take==$menu_point AND $menu_direction==1) OR ($take!=$menu_point)){$dir=0;}
		$change = "UPDATE sorting Set menu_point='".$take."',menu_direction='".$dir."' WHERE user_id=$loggedinuserid AND menu_id=4";
		$update = mysql_query($change)or die("Fehler.".mysql_error());
		//Natürlich müssen wir nun auch anpassen
		$menu_point=$take;
		$menu_direction=$dir;					
		}



	If (isset($_POST['user']) AND (isset($_POST['delete']) OR isset($_POST['activate'])))
		{
		$take=$_POST['user'];
		$updatedate = date('Y-m-d G:i:s');
		$deleteuserid=$_SESSION['userid'];
		If (isset($_POST['activate'])){$delete='0';}else{$delete='1';}
		$change = "UPDATE users Set deleted='".$delete."', deleteuserid='".$deleteuserid."',updated_at='".$updatedate."' WHERE id='$take'";
		$update = mysql_query($change)or die("Deaktivieren leider fehlgeschlagen.".mysql_error());
		}


	If (isset($_POST['user']) AND (isset($_POST['ddell']) OR isset($_POST['dactiv'])))
		{
		$take=$_POST['user'];
		$updatedate = date('Y-m-d G:i:s');
		If (isset($_POST['dactiv'])){$d='0';}else{$d='1';}
		$change = "UPDATE users Set distributor='".$d."', updated_at='".$updatedate."' WHERE id='$take'";
		$update = mysql_query($change)or die("Deaktivieren leider fehlgeschlagen.".mysql_error());
		}

	If (isset($_POST['user']) AND (isset($_POST['pdell']) OR isset($_POST['pactiv'])))
		{
		$take=$_POST['user'];
		$updatedate = date('Y-m-d G:i:s');
		If (isset($_POST['pactiv'])){$d='0';}else{$d='1';}
		$change = "UPDATE users Set pressagent='".$d."', updated_at='".$updatedate."' WHERE id='$take'";
		$update = mysql_query($change)or die("Deaktivieren leider fehlgeschlagen.".mysql_error());
		}
		
	If (isset($_POST['user']) AND (isset($_POST['adell']) OR isset($_POST['aactiv'])))
		{
		$take=$_POST['user'];
		$updatedate = date('Y-m-d G:i:s');
		If (isset($_POST['aactiv'])){$d='0';}else{$d='1';}
		$change = "UPDATE users Set admin='".$d."', updated_at='".$updatedate."' WHERE id='$take'";
		$update = mysql_query($change)or die("Deaktivieren leider fehlgeschlagen.".mysql_error());
		}

	$counter=0;
	$query = "SELECT username, firstname, lastname, distributor, pressagent, jobtitle, phone, cellphone, admin, created_at, updated_at, deleted, id FROM users";



	//Standard
	$query = "SELECT username, firstname, lastname, distributor, pressagent, jobtitle, phone, cellphone, admin, created_at, updated_at, deleted, id FROM users ORDER BY lastname";
	//Aber wir haben ja eine gespeicherte Sortierung zur Auswertung
	//Sortierung nach Nachname
	If ($menu_point==1 AND $menu_direction==0)
		{
		$query = "SELECT username, firstname, lastname, distributor, pressagent, jobtitle, phone, cellphone, admin, created_at, updated_at, deleted, id FROM users ORDER BY lastname ASC";
		}
	If ($menu_point==1 AND $menu_direction==1)
		{
		$query = "SELECT username, firstname, lastname, distributor, pressagent, jobtitle, phone, cellphone, admin, created_at, updated_at, deleted, id FROM users ORDER BY lastname DESC";
		}
	//Sortierung nach Vorname
	If ($menu_point==2 AND $menu_direction==0)
		{
		$query = "SELECT username, firstname, lastname, distributor, pressagent, jobtitle, phone, cellphone, admin, created_at, updated_at, deleted, id FROM users ORDER BY firstname ASC";
		}
	If ($menu_point==2 AND $menu_direction==1)
		{
		$query = "SELECT username, firstname, lastname, distributor, pressagent, jobtitle, phone, cellphone, admin, created_at, updated_at, deleted, id FROM users ORDER BY firstname DESC";
		}
	//Sortierung nach Username
	If ($menu_point==3 AND $menu_direction==0)
		{
		$query = "SELECT username, firstname, lastname, distributor, pressagent, jobtitle, phone, cellphone, admin, created_at, updated_at, deleted, id FROM users ORDER BY username ASC";
		}
	If ($menu_point==3 AND $menu_direction==1)
		{
		$query = "SELECT username, firstname, lastname, distributor, pressagent, jobtitle, phone, cellphone, admin, created_at, updated_at, deleted, id FROM users ORDER BY username DESC";
		}



	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			$counter++;
			$ids[$counter]=$row->id;
			$username[$row->id]=$row->username;
			$firstname[$row->id]=$row->firstname;
			$lastname[$row->id]=$row->lastname;
			$distributor[$row->id]=$row->distributor;
			$pressagent[$row->id]=$row->pressagent;
			$admin[$row->id]=$row->admin;			
			$deleted[$row->id]=$row->deleted;
			}
		}
		
	?>
	
	<div id="mainview">
	
	<?

	
	?>		
	
	<table border=0 class="centred">

	<tr><td colspan=42 class='cell' align='center'>
<?
	echo "<form action='user_detail.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-primary' title='Neuen Benutzer anlegen' name='user' value='xxx'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Neuen Benutzer anlegen</button></form>";

	//Nu brauchen wir Buttons um die Sortierung anzuzeigen
	echo " <form action='user.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Vorname sortieren' name='sorting' value='2'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==2 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==2 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=2){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Vorname </button></form>";

	echo " <form action='user.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Nachname sortieren' name='sorting' value='1'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==1 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==1 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=1){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Nachname </button></form>";

	echo " <form action='user.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Benutzername sortieren' name='sorting' value='3'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==3 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==3 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=3){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Benutzername </button></form><br><br>";

?>
	</td></tr></table>	

		

<?	
	For ($i=1;$i<=$counter;$i++)
		{
		$take=$ids[$i];
		$send_id='user_detail.php?user='.$take;
		echo "<div class='panel panel-default'>";
		echo "<div class='panel-heading'><a href='$send_id'><Font size='4'>$lastname[$take], $firstname[$take] ($username[$take])</font></a>";

		echo "<div align='right'><form action='user_detail.php' method='post'>";
		echo "<button type='submit' class='btn btn-primary' title='Zu den Details' name='user' value='$take'><span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span> Details</button></form></div></div>";

		echo "<div class='panel-body'>";


		echo "<form action='user.php' method='post' style='display:inline;'>";
		echo "<INPUT type='hidden' id='user' name='user' value='$take'>";
		If ($distributor[$take]==1)
			{
			$show="Freigabe <button type='submit' class='btn btn-success' name='dactiv' title='Darf freigeben'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";		
			}
		else
			{
			$show="Freigabe <button type='submit' class='btn btn-info' name='ddell' title='Darf nicht freigeben'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			}
		echo "$show </form>";
		echo "<form action='user.php' method='post' style='display:inline;'>";
		echo "<INPUT type='hidden' id='user' name='user' value='$take'>";
		If ($pressagent[$take]==1)
			{
			$show="Versand <button type='submit' class='btn btn-success' name='pactiv' title='Darf versenden'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";
			}
		else
			{
			$show="Versand <button type='submit' class='btn btn-info' name='pdell' title='Darf nicht versenden'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			}
		echo "$show </form>";
		echo "<form action='user.php' method='post' style='display:inline;'>";
		echo "<INPUT type='hidden' id='user' name='user' value='$take'>";
		If ($admin[$take]==1)
			{
			$show="Admin <button type='submit' class='btn btn-success' name='aactiv' title='Hat Adminstatus'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";
			}
		else
			{
			$show="Admin <button type='submit' class='btn btn-info' name='adell' title='Hat keinen Adminstatus'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			}		
		echo "$show </form>";
		echo "<form action='user.php' method='post'>";
		echo "<INPUT type='hidden' id='user' name='user' value='$take'>";
		If ($deleted[$take]==1)
			{
			$show="Deaktiviert <button type='submit' class='btn btn-danger' name='activate' title='Ist deaktiviert'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			}
		else
			{
			$show="Aktiviert <button type='submit' class='btn btn-success' name='delete' title='Ist aktiviert'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";
			}
		echo "<div align='right'>$show </div></form>";			
		echo "</div></div>";
		}
		
	?>	





	</div>
	  
<?	
include_once("footer.php");	
