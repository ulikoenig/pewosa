<?
$pagetitle='Newsletter';
include_once("header.php");

if($loggedinadmin == "1" OR $loggedinpressagent == "1")
	{
	die('Sie sind nicht berechtigt diese Funktion zu nutzen.</a>');
}

//Wir prüfen erstmal, ob es eine Speicherung für die Sortierung dieser Seite gibt
//1=PMs, 2=Adressatenliste, 3=Verteilerliste, 4=Nutzerliste, 5=Newsletter
	$query = "SELECT * FROM sorting WHERE user_id=$loggedinuserid AND menu_id=5";
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
		$query = "INSERT INTO ".MYSQLDB.".`sorting` ( `user_id`, `menu_id`, `menu_point`,`menu_direction`) VALUES (".$loggedinuserid.",5,".$menu_point.",".$menu_direction.")";
		$send = mysql_query($query) or die("Fehler:".mysql_error());	
		}


	//Nutzer will Sortierung verändern? -Na dann hier lang!
	If (isset($_POST['sorting']))
		{
		$take=$_POST['sorting'];
		//Nu schauen wir erstmal, auf welchen Menu-Punkt fokussiert ist und in welche Richtung er zeigt
		If ($take==$menu_point AND $menu_direction==0){$dir=1;}
		If (($take==$menu_point AND $menu_direction==1) OR ($take!=$menu_point)){$dir=0;}
		$change = "UPDATE sorting Set menu_point='".$take."',menu_direction='".$dir."' WHERE user_id=$loggedinuserid AND menu_id=5";
		$update = mysql_query($change)or die("Fehler.".mysql_error());
		//Natürlich müssen wir nun auch anpassen
		$menu_point=$take;
		$menu_direction=$dir;					
		}




	//Sortierung nach Sendedatum
	If ($menu_point==2 AND $menu_direction==0)
		{
		$query = "SELECT * FROM newsletter ORDER BY senddate ASC";
		}
	If ($menu_point==2 AND $menu_direction==1)
		{
		$query = "SELECT * FROM newsletter ORDER BY senddate DESC";
		}
	//Sortierung nach Titel/Betreff
	If ($menu_point==1 AND $menu_direction==0)
		{
		$query = "SELECT * FROM newsletter ORDER BY subject ASC";
		}
	If ($menu_point==1 AND $menu_direction==1)
		{
		$query = "SELECT * FROM newsletter ORDER BY subject DESC";
		}
	$counter=0;
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{
		while($row = mysql_fetch_object($checkdata))
			{
			$counter++;
			$ids[$counter]=$row->id;
			$subject[$row->id]=$row->subject;
			//$body[$row->id]=$row->body;
			$sendstate[$row->id]=$row->sendstate;
			$senddate[$row->id]=$row->senddate;
			}
		}
	?>	
	<div id="mainview"><div >
<?


	
?>
	<table border=0 class="centred">

	<tr><td colspan=42 class='cell' align='center'>
<?
	echo "<form action='newsletter_detail.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-primary' title='Neuen Newsletter anlegen' name='newsletter' value='xxx'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Neuen Newsletter anlegen</button></form>";

	//Nu brauchen wir Buttons um die Sortierung anzuzeigen

	echo " <form action='newsletter.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Alphabetisch sortieren' name='sorting' value='1'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==1 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==1 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=1){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Titel/Betreff </button></form>";

	echo " <form action='newsletter.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Nach Sendedatum sortieren' name='sorting' value='2'>";
	//Wird hiernach sortiert, dann in welche Richtung?
	If ($menu_point==2 AND $menu_direction==0){echo "<span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span>";}
	If ($menu_point==2 AND $menu_direction==1){echo "<span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span>";}
	//If ($menu_point!=2){echo "<span class='glyphicon glyphicon-retweet' aria-hidden='true'></span>";}
	echo " Sendedatum </button></form><br><br>";
?>
	</td></tr></Table>
	

		
<?
	
	For ($i=1;$i<=$counter;$i++)
		{
		$take=$ids[$i];
		echo "<div class='panel panel-default'>";
		If ($color=='#cccccc'){$color='#99ccff';}else{$color='#cccccc';}
		$showsubject=$subject[$take];
		$showsubjectlong="";
		if(strlen($showsubject) <= 59){$showsubject = $showsubject;} 
		else 
			{
			$length=59;
			$showsubjectlong = htmlspecialchars ($showsubject);
			$showsubject = preg_replace("/[^ ]*$/", '.', substr($showsubject, 0, $length));
			}	
		$send_id='newsletter_detail.php?id='.$take;
		echo "<div class='panel-heading'>";	
		echo "<a href='$send_id'><Font size='4'>$showsubject</font></a>";
		echo "<div align='right'><form action='newsletter_detail.php' method='post'>";
		echo "<button type='submit' class='btn btn-primary' title='Zu den Details' name='newsletter' value='$take'><span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span> Details</button>";
		echo "</form></div></div>";
		echo "<div class='panel-body'>";
				
		
		If ($send[$take]!='-1')
			{
			$d_show = date("d.m.Y H:i",strtotime($senddate[$take]));
			echo "<br>Versand: $d_show";
			echo "<div align='right'>";
			If ($sendstate[$take]=='-1'){echo "<button type='button' class='btn btn-warning' title='Warte auf Versand' disabled><span class='glyphicon glyphicon-hourglass' aria-hidden='true'></span></button>";}
			If ($sendstate[$take]=='1'){echo "<button type='button' class='btn btn-success' title='Versand erfolgreich' disabled><span class='glyphicon glyphicon-ok' aria-hidden='true'></span></button>";}
			If ($sendstate[$take]>1){echo "<button type='button' class='btn btn-info' title='Versand wird ausgeführt' disabled><span class='glyphicon glyphicon-log-out' aria-hidden='true' ></span></button>";}
			//echo "</td></td>";
			}
		else
			{
			echo "Nicht versand";	
			}

		echo "</div></div></div>";

		}
		
	?>	
	</table></div>
	</div>
	  
<?	
include_once("footer.php");	
