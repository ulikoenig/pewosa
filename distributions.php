<?
$pagetitle='Verteiler Übersicht';
include_once("header.php");


//Wir prüfen erstmal, ob es eine Speicherung für die Sortierung dieser Seite gibt
//1=PMs, 2=Adressatenliste, 3=Verteilerliste, 4=Nutzerliste
	$query = "SELECT * FROM sorting WHERE user_id=$loggedinuserid AND menu_id=3";
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
		$query = "INSERT INTO `pewosa`.`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",3,".$menu_point.",".$menu_direction.")";
		$send = mysql_query($query) or die("Fehler:".mysql_error());	
		}


	//Nutzer will Sortierung verändern? -Na dann hier lang!
	If (isset($_POST['sorting']))
		{
		$take=$_POST['sorting'];
		//Nu schauen wir erstmal, auf welchen Menu-Punkt fokussiert ist und in welche Richtung er zeigt
		If ($take==$menu_point AND $menu_direction==0){$dir=1;}
		If (($take==$menu_point AND $menu_direction==1) OR ($take!=$menu_point)){$dir=0;}
		$change = "UPDATE sorting Set menu_point='".$take."',menu_direction='".$dir."' WHERE user_id=$loggedinuserid AND menu_id=3";
		$update = mysql_query($change)or die("Fehler.".mysql_error());
		//Natürlich müssen wir nun auch anpassen
		$menu_point=$take;
		$menu_direction=$dir;					
		}

	
	If (isset($_POST['dist']) AND isset($_POST['delete']))
		{
		$take=$_POST['dist'];
		$updatedate = date('Y-m-d G:i:s');
		$deleteuserid=$_SESSION['userid'];			
		$change = "UPDATE list Set deleted='1', deleteuserid='".$deleteuserid."',updatedate='".$updatedate."' WHERE id='$take'";
		$update = mysql_query($change)or die("Löschen leider fehlgeschlagen.".mysql_error());
		//Mitgliederverlinkungen löschen???
		}	
	
	$counter=0;



	//Standard
	$query = "SELECT name, id FROM list WHERE deleted!=1 ORDER BY name";
	//Aber wir haben ja eine gespeicherte Sortierung zur Auswertung
	//Sortierung nach Name
	If ($menu_point==1 AND $menu_direction==0)
		{
		$query = "SELECT name, id FROM list WHERE deleted!=1 ORDER BY name ASC";
		}
	If ($menu_point==1 AND $menu_direction==1)
		{
		$query = "SELECT name, id FROM list WHERE deleted!=1 ORDER BY name DESC";
		}
	//Sortierung nach Mitgliederanzahl ist mir jetzt zu stressig, kommt später

	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			$counter++;
			$ids[$counter]=$row->id;
			$name[$row->id]=$row->name;
			
			//Mitglieder Zählen und speichern
			$counting[$row->id]=0;
			$query2 = "SELECT customer, distribution FROM customerdistribution WHERE distribution=$row->id";
			$checkdata2 = mysql_query($query2);
			if(mysql_num_rows($checkdata2)>=1)
				{	
				while($row2 = mysql_fetch_object($checkdata2))
					{
					//Zählung der Mitglieder abhängig von der Verteiler-ID	
					$counting[$row->id]++;
					$halter=$counting[$row->id];
					//Speichern der Mitglieder-Ids mit fortlaufender Nummer und abhängig von der Verteiler_id
					$customer_id[$row->id][$halter]=$row2->customer;
					}
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
	echo "<form action='distribution_detail.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-primary' title='Neuen Verteiler anlegen' name='dist' value='xxx'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Neuen Verteiler anlegen</button></form>";
	echo " <form action='distributions.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Vorname sortieren' name='sorting' value='1'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==1 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==1 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=1){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Name </button></form><br><br>";

?>

	</td></tr>

	
		<tr height=20>
			<th class='cell'>Verteilername</th>
			<th class='cell'>Adressaten</th>
			<th class='cell'></th>
			<th class='cell'></th>			
		</tr><td colspan=42 ></td></tr></table>
	
	<?
	For ($i=1;$i<=$counter;$i++)
		{
		$take=$ids[$i];
		If ($color=='#cccccc'){$color='#99ccff';}else{$color='#cccccc';}
		echo "<div class='panel panel-default'>";
		$send_id='distribution_detail.php?'.$take;
		echo "<div class='panel-heading'><a href='$send_id'><Font size='4'>$name[$take]</font></a>";
		echo "<form action='distribution_detail.php' method='post' style='display:inline;float: right;'>";
		echo "<button type='submit' class='btn btn-primary' title='Zu den Details' name='dist' value='$take'>";
		echo "<span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span> Details</button></form>";
		//echo "<tr><td class='cell' bgcolor='$color'><span class='badge'><Font size='3'>$name[$take]</Font></span></td>";
		//echo "<td class='cell' bgcolor='$color'>";
		//Hier listen wir die Mitglieder auf
		echo "</div>";
		echo "<div class='panel-body'>";
		echo "<Font size='2'>";
		If ($counting[$take]>0)
			{
			$countit=0;	
			Foreach ($customer_id[$take] as $name_it)
				{	
				$query = "SELECT firstname, lastname, email FROM customer WHERE id=$name_it";
				$checkdata = mysql_query($query);
				if(mysql_num_rows($checkdata)>=1)
					{	
					while($row = mysql_fetch_object($checkdata))
						{
						$countit++;	
						echo "$countit. $row->firstname $row->lastname ($row->email)<br>";
						}
					}
				}
			}
		else
			{
			echo "Niemand";
			}

		$give_titel=$counting[$take].' Adressaten';
		echo "</Font><br><span class='badge' title='$give_titel'>$counting[$take]</span></div></div>";
		

		}
	?>	

	
	  

<?	
include_once("footer.php");	
