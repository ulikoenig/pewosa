<?
$pagetitle='Newsletter-Abonnenten Bearbeiten';
include_once("header.php");
if($loggedinadmin == "1" OR $loggedinpressagent == "1")
	{
	die('Sie sind nicht berechtigt diese Funktion zu nutzen.</a>');
}



//Wir prüfen erstmal, ob es eine Speicherung für die Sortierung dieser Seite gibt
//1=PMs, 2=Adressatenliste, 3=Verteilerliste, 4=Nutzerliste
	$query = "SELECT * FROM sorting WHERE user_id=$loggedinuserid AND menu_id=6";
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
		$query = "INSERT INTO `pewosa`.`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",6,".$menu_point.",".$menu_direction.")";
		$send = mysql_query($query) or die("Fehler:".mysql_error());	
		}


	//Nutzer will Sortierung verändern? -Na dann hier lang!
	If (isset($_POST['sorting']))
		{
		$take=$_POST['sorting'];
		//Nu schauen wir erstmal, auf welchen Menu-Punkt fokussiert ist und in welche Richtung er zeigt
		If ($take==$menu_point AND $menu_direction==0){$dir=1;}
		If (($take==$menu_point AND $menu_direction==1) OR ($take!=$menu_point)){$dir=0;}
		$change = "UPDATE sorting Set menu_point='".$take."',menu_direction='".$dir."' WHERE user_id=$loggedinuserid AND menu_id=6";
		$update = mysql_query($change)or die("Fehler.".mysql_error());
		//Natürlich müssen wir nun auch anpassen
		$menu_point=$take;
		$menu_direction=$dir;					
		}



	If (isset($_POST['cust']) AND isset($_POST['change']))
		{

		$take=$_POST['cust'];
		$take2=$_POST['change'];
		If ($take2==1){$changer='-1';}
		If ($take2==-1 OR $take2==0){$changer='1';}
		$change = "UPDATE customerNewsletter Set active='$changer' WHERE id='$take'";
		$update = mysql_query($change)or die("Deaktivieren leider fehlgeschlagen.".mysql_error());
		}

		
	$counter=0;


	//Standard
	$query = "SELECT firstname, lastname, email FROM customerNewsletter ORDER BY lastname";
	//Aber wir haben ja eine gespeicherte Sortierung zur Auswertung
	//Sortierung nach Nachname
	If ($menu_point==1 AND $menu_direction==0)
		{
		$query = "SELECT id, firstname, lastname, email, active FROM customerNewsletter ORDER BY lastname ASC";
		}
	If ($menu_point==1 AND $menu_direction==1)
		{
		$query = "SELECT id, firstname, lastname, email, active FROM customerNewsletter ORDER BY lastname DESC";
		}
	//Sortierung nach Vorname
	If ($menu_point==2 AND $menu_direction==0)
		{
		$query = "SELECT id, firstname, lastname, email, active FROM customerNewsletter ORDER BY firstname ASC";
		}
	If ($menu_point==2 AND $menu_direction==1)
		{
		$query = "SELECT id, firstname, lastname, email, active FROM customerNewsletter ORDER BY firstname DESC";
		}
	//Sortierung nach email
	If ($menu_point==3 AND $menu_direction==0)
		{
		$query = "SELECT id, firstname, lastname, email, active FROM customerNewsletter ORDER BY email ASC";
		}
	If ($menu_point==3 AND $menu_direction==1)
		{
		$query = "SELECT id, firstname, lastname, email, active FROM customerNewsletter ORDER BY email DESC";
		}



	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			$counter++;
			$ids[$counter]=$row->id;
			$email[$row->id]=$row->email;
			$firstname[$row->id]=$row->firstname;
			$lastname[$row->id]=$row->lastname;			
			$active[$row->id]=$row->active;
			}
		}
		
	?>
	
	<div id="mainview">
	
	<?

	
	?>		
	
	<table border=0 class="centred">

	<tr><td colspan=42 class='cell' align='center'>
<?
	echo "<form action='nlabo_detail.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-primary' title='Neuen Abonennten anlegen' name='cust' value='xxx'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Neuen Abonnenten anlegen</button></form>";

	//Nu brauchen wir Buttons um die Sortierung anzuzeigen
	echo " <form action='nlabo.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Vorname sortieren' name='sorting' value='2'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==2 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==2 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=2){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Vorname </button></form>";

	echo " <form action='nlabo.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Nachname sortieren' name='sorting' value='1'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==1 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==1 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=1){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Nachname </button></form>";

	echo " <form action='nlabo.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach E-Mail sortieren' name='sorting' value='3'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==3 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==3 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=3){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " E-Mail </button></form><br><br>";
?>
	</td></tr></table>	

		

<?	
	For ($i=1;$i<=$counter;$i++)
		{
		$take=$ids[$i];
		echo "<div class='panel panel-default'>";
		//If ($color=='#cccccc'){$color='#99ccff';}else{$color='#cccccc';}
		$send_id='nlabo_detail.php?user='.$take;
		echo "<div class='panel-heading'><a href='$send_id'><Font size='4'>";
		echo "$i. ";
		If ($lastname[$take]!=''){echo "$lastname[$take], ";}
		If ($firstname[$take]!=''){echo "$firstname[$take]";}
		If ($lastname[$take]=='' AND $firstname[$take]==''){echo "Kein Name gespeichert";}
		//echo "$lastname[$take], $firstname[$take] ";
		echo "</Font></a><div align='right'>";
		echo "<form action='nlabo_detail.php' method='post' style='display:inline;'><button type='submit' class='btn btn-primary' title='Zu den Details' name='user' value='$take'><span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span> Details</button></form></div></div>";	
		
		echo "<div class='panel-body'>";

		echo "$email[$take]";
		echo "<div align='right'><form action='nlabo.php' method='post'>";
		If ($active[$take]==1)
			{
			$show="<input type='submit' class='btn btn-success' title='Bekommt den Newsletter' value='Im Verteiler'>";		
			}
		else
			{
			If ($active[$take]==0){$show="<input type='submit' class='btn btn-warning' title='Möchte keinen Newsletter' value='Möchte keinen Newsletter'>";}
			If ($active[$take]==-1){$show="<input type='submit' class='btn btn-warning' title='Bekommt keinen Newsletter' value='Deaktiviert'>";}
			}
		

		echo "<Input type='hidden' name='change' value='$active[$take]'>";
		echo "<Input type='hidden' name='cust' value='$take'>$show";
		echo "</form></div>";
		
				
		echo "</div></div>";
		}
		
	?>	
	</table><br><table border=0 class="centred">
	<tr><td colspan=42 >
	<?

	?>	
	</td></tr>
	</table>
	</div>
	  
<?	
include_once("footer.php");	
