<?
$pagetitle='Adressaten Übersicht';
include_once("header.php");


//Wir prüfen erstmal, ob es eine Speicherung für die Sortierung dieser Seite gibt
//1=PMs, 2=Adressatenliste, 3=Verteilerliste, 4=Nutzerliste
	$query = "SELECT * FROM sorting WHERE user_id=$loggedinuserid AND menu_id=2";
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
		$query = "INSERT INTO ".MYSQLDB.".`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",2,".$menu_point.",".$menu_direction.")";
		$send = mysql_query($query) or die("Fehler:".mysql_error());	
		}


	//Nutzer will Sortierung verändern? -Na dann hier lang!
	If (isset($_POST['sorting']))
		{
		$take=$_POST['sorting'];
		//Nu schauen wir erstmal, auf welchen Menu-Punkt fokussiert ist und in welche Richtung er zeigt
		If ($take==$menu_point AND $menu_direction==0){$dir=1;}
		If (($take==$menu_point AND $menu_direction==1) OR ($take!=$menu_point)){$dir=0;}
		$change = "UPDATE sorting Set menu_point='".$take."',menu_direction='".$dir."' WHERE user_id=$loggedinuserid AND menu_id=2";
		$update = mysql_query($change)or die("Fehler.".mysql_error());
		//Natürlich müssen wir nun auch anpassen
		$menu_point=$take;
		$menu_direction=$dir;					
		}


	If (isset($_POST['customer']) AND isset($_POST['delete']))
		{
		$take=$_POST['customer'];
		$updatedate = date('Y-m-d G:i:s');
		$deleteuserid=$_SESSION['userid'];			
		$change = "UPDATE customer Set deleted='2', deleteuserid='".$deleteuserid."',updatedate='".$updatedate."' WHERE id='$take'";
		$update = mysql_query($change)or die("Löschen leider fehlgeschlagen.".mysql_error());			
		}

	If (isset($_POST['cust']) AND isset($_POST['change']))
		{

		$take=$_POST['cust'];
		$take2=$_POST['change'];
		If ($take2==0){$changer='2';}
		If ($take2==1 OR $take2==2){$changer='0';}	
		$change = "UPDATE customer Set deleted='$changer' WHERE id='$take'";
		$update = mysql_query($change)or die("Aktivierung/Deaktivieren leider fehlgeschlagen.".mysql_error());
		}
		
	//Alle Adressaten laden
	$counter=0;
	//Standard
	$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY lastname";
	//Aber wir haben ja eine gespeicherte Sortierung zur Auswertung
	//Sortierung nach Nachname
	If ($menu_point==1 AND $menu_direction==0)
		{
		$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY lastname ASC";
		}
	If ($menu_point==1 AND $menu_direction==1)
		{
		$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY lastname DESC";
		}
	//Sortierung nach Vorname
	If ($menu_point==2 AND $menu_direction==0)
		{
		$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY firstname ASC";
		}
	If ($menu_point==2 AND $menu_direction==1)
		{
		$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY firstname DESC";
		}
	//Sortierung nach Unternehmen
	If ($menu_point==3 AND $menu_direction==0)
		{
		$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY company ASC";
		}
	If ($menu_point==3 AND $menu_direction==1)
		{
		$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY company DESC";
		}
	//Sortierung nach Email
	If ($menu_point==4 AND $menu_direction==0)
		{
		$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY email ASC";
		}
	If ($menu_point==4 AND $menu_direction==1)
		{
		$query = "SELECT lastname, firstname, company, phone, cellphone, email, id, deleted FROM customer ORDER BY email DESC";
		}

	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			$counter++;
			$ids[$counter]=$row->id;
			$firstname[$row->id]=$row->firstname;
			$lastname[$row->id]=$row->lastname;
			$company[$row->id]=$row->company;
			$phone[$row->id]=$row->phone;
			$cellphone[$row->id]=$row->cellphone;
			$email[$row->id]=$row->email;
			$deleted[$row->id]=$row->deleted;
			}
		}
		
	?>

	
	<div id="mainview">
	<?

	?>
	<table border=0 class="centred" >


	<tr><td colspan=42 class='cell' align='center'>
<?
	echo "<form action='customer_detail.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-primary' title='Neuen Adressaten anlegen' name='customer' value='xxx'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Neuen Adressaten 		einrichten</button></form>";
	//Nu brauchen wir Buttons um die Sortierung anzuzeigen
	echo " <form action='customer.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Vorname sortieren' name='sorting' value='2'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==2 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==2 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=2){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Vorname </button></form>";

	echo " <form action='customer.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Nachname sortieren' name='sorting' value='1'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==1 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==1 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=1){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Nachname </button></form>";

	echo " <form action='customer.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Unternehmen sortieren' name='sorting' value='3'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==3 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==3 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=3){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Unternehmen </button></form>";

	echo " <form action='customer.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Unternehmen sortieren' name='sorting' value='4'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==4 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==4 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=4){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " E-mail </button></form>";

	echo "<br><br>";

?>
	</td></tr></table>
	

	<?
	For ($i=1;$i<=$counter;$i++)
		{	
		$take=$ids[$i];
		$send_id='customer_detail.php?cust='.$take;
		echo "<div class='panel panel-default'>";
		If (($loggedinadmin < "1" AND $deleted[$take]!=1 AND $deleted[$take]!=2) OR ($loggedinadmin > "1"))
			{		
			//If ($color=='cell-even'){$color='cell-uneven';}else{$color='cell-even';}
			//echo "<tr><td class='cell $color'><span class='badge'><Font size='3'>";
			echo "<div class='panel-heading'><a href='$send_id'><Font size='4'>";
			If ($lastname[$take]!=''){echo "$lastname[$take], ";}
			If ($firstname[$take]!=''){echo "$firstname[$take]";}
			If ($lastname[$take]=='' AND $firstname[$take]==''){echo "Kein Name gespeichert";}
			echo "</font></a>";
			echo "<div align='right'><form action='customer_detail.php' method='post'>";
			echo "<button type='submit' class='btn btn-primary' title='Zu den Details' name='customer' value='$take'><span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span> Details</button>";
			echo "</form></div>";
			echo "</div>";
			echo "<div class='panel-body'>";
			echo "<br>Unternehmen: $company[$take]";
			//echo "<br>";
			If ($phone[$take]!=''){echo "<a href='tel:$phone[$take]'>$phone[$take]</a> ";}else{echo " - ";}
			If ($cellphone[$take]!=''){echo "| <a href='tel:$cellphone[$take]'>$cellphone[$take]</a>";}
			//echo"</td>";
			echo "<br>Email: $email[$take]";




			echo "<br><br>Verteiler: ";
			//Hier laden wir zunächst die Verteiler-Ids in denen der Adressat ist und dann direkt den Namen des Verteilers
			$counting=0;
			$query = "SELECT customer, distribution FROM customerdistribution WHERE customer=$take";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{	
				while($row = mysql_fetch_object($checkdata))
					{	
					$counting++;	
					$dist_id[$counting]=$row->distribution;
					}
				}			
			If ($counting>0)
				{
				$dist_list='';	
				Foreach ($dist_id as $name_it)
					{
					$query = "SELECT name FROM list WHERE id=$name_it";
					$checkdata = mysql_query($query);
					if(mysql_num_rows($checkdata)>=1)
						{	
						while($row = mysql_fetch_object($checkdata))
							{
							$dist_list=$dist_list.$row->name.', ';	
							}
						}
					}
				//Nun bereinigen wir das letzte Komma
				$rest = substr($dist_list, 0, -2);
				echo $rest;
				}
			else
				{
				echo "Keiner";
				}
			unset($dist_id);	
			//echo "kommt noch";
			//echo "</td></tr><tr height=10>";		
			//echo "<td colspan=42 align='center'></td></tr>";


			if ($loggedinadmin > "1") {
			echo "<div align='right'><form action='customer.php' method='post'>";
			If ($deleted[$take]!=1 AND $deleted[$take]!=2)
				{
				$show="<input type='submit' class='btn btn-success' title='Bekommt Pressemitteilungen' value='Im Verteiler'>";		
				}
			else
				{
				If ($deleted[$take]==1){$show="<input type='submit' class='btn btn-warning' title='Benutzer hat sich selbstständnig abgemeldet' value='Wünscht keine Mails'>";}
				If ($deleted[$take]==2){$show="<input type='submit' class='btn btn-warning' title='Benutzer wurde durch Nutzer abgemeldet' value='Deaktiviert'>";}
				}
		
			echo "$show";
			echo "<Input type='hidden' name='change' value='$deleted[$take]'>";
			echo "<Input type='hidden' name='cust' value='$take'>";
			echo "</form></div>";
			}

			echo "</div></div>";
			}
		}
	?>	
	</table><br><table border=0 class="centred">
	<tr><td colspan=42 align='right'>
	</td></tr>
	</table>
	</div>
	  
<?	
include_once("footer.php");	
