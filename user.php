<?
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
		$query = "INSERT INTO `pewosa`.`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",4,".$menu_point.",".$menu_direction.")";
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



	If (isset($_POST['user']) AND isset($_POST['delete']))
		{
		$take=$_POST['user'];
		$updatedate = date('Y-m-d G:i:s');
		$deleteuserid=$_SESSION['userid'];			
		$change = "UPDATE users Set deleted='1', deleteuserid='".$deleteuserid."',updated_at='".$updatedate."' WHERE id='$take'";
		$update = mysql_query($change)or die("Deaktivieren leider fehlgeschlagen.".mysql_error());
		}

	If (isset($_POST['user']) AND isset($_POST['activate']))
		{
		$take=$_POST['user'];
		$updatedate = date('Y-m-d G:i:s');
		$deleteuserid=$_SESSION['userid'];			
		$change = "UPDATE users Set deleted='0', deleteuserid='".$deleteuserid."',updated_at='".$updatedate."' WHERE id='$take'";
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
	</td></tr>	
		<tr height=30>
			<th class='cell'>Name</th>
			<th class='cell'>Benutzername</th>
			<th class='cell'>Freigabe</th>
			<th class='cell'>Versand</th>
			<th class='cell'>Admin</th>			
			<th class='cell'>Aktiviert</th>
		</tr><td colspan=42 ></td></tr>
		

<?	
	For ($i=1;$i<=$counter;$i++)
		{
		$take=$ids[$i];
		If ($color=='#cccccc'){$color='#99ccff';}else{$color='#cccccc';}
		echo "<tr><td class='cell' bgcolor='$color'><span class='badge'><Font size='3'>$lastname[$take], $firstname[$take] </Font></span></td>";
		echo "<td class='cell' bgcolor='$color'>$username[$take]</td>";
		If ($distributor[$take]==1)
			{
			$show="<button type='button' class='btn btn-success' title='Darf freigeben'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";		
			}
		else
			{
			$show="<button type='button' class='btn btn-info' title='Darf nicht freigeben'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			}
		echo "<td class='cell' bgcolor='$color'>$show</td>";
		If ($pressagent[$take]==1)
			{
			$show="<button type='button' class='btn btn-success' title='Darf versenden'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";
			}
		else
			{
			$show="<button type='button' class='btn btn-info' title='Darf nicht versenden'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			}
		echo "<td class='cell' bgcolor='$color'>$show</td>";
		If ($admin[$take]==1)
			{
			$show="<button type='button' class='btn btn-success' title='Hat Adminstatus'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";
			}
		else
			{
			$show="<button type='button' class='btn btn-info' title='Hat keinen Adminstatus'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			}		
		echo "<td class='cell' bgcolor='$color'>$show</td>";
		If ($deleted[$take]==1)
			{
			$show="<button type='button' class='btn btn-danger' title='Ist deaktiviert'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>";
			}
		else
			{
			$show="<button type='button' class='btn btn-success' title='Ist aktiviert'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";
			}
		echo "<td class='cell' bgcolor='$color'>$show</td>";		
		echo "<td class='cell' bgcolor='$color'>";
		echo "<form action='user_detail.php' method='post'>";
		echo "<button type='submit' class='btn btn-primary' title='Zu den Details' name='user' value='$take'><span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span> Details</button></form></td></tr>";
		echo "</td></tr><tr height=10>";		
		echo "<td colspan=42 align='center'></td></tr>";
		}
		
	?>	
	</table><br><table border=0 class="centred">
	<tr><td colspan=42 >

	</td></tr>
	</table>
	</div>
	  
<?	
include_once("footer.php");	
