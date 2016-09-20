<?
$pagetitle='Verteiler Bearbeiten';
include_once("header.php");


	//Erstmal laden wir alle möglichen Adressaten
	$counter2=0;
	$query = "SELECT lastname, firstname, company, id, email FROM customer WHERE deleted!=1 ORDER BY lastname";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			$counter2++;
			$ids[$counter2]=$row->id;
			$firstname[$row->id]=$row->firstname;
			$lastname[$row->id]=$row->lastname;
			$company[$row->id]=$row->company;
			$email[$row->id]=$row->email;
			}
		}	
	
	If (isset($_POST['dist']))
		{
		//Hier schnappt er sich die Verteilerlistennummer	
		$take=$_POST['dist'];

		//Nun laden wir alle Mitglieder dieser Gruppe
		$counting=0;
		$customer_id[0]=0;
		If ($take!='xxx')
		{
		$query = "SELECT customer, distribution FROM customerdistribution WHERE distribution=$take";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)>=1)
			{	
			while($row = mysql_fetch_object($checkdata))
				{
				$counting++;	
				$customer_id[$counting]=$row->customer;
				//echo "Eintrag für $row->customer Vetreiler $row->distribution";
				}
			}		
		}	
		If (isset($_POST['safe']) AND $take!='xxx')
			{
			//Eintrag anpassen, falls bereits vorhanden
			$updatedate= date('Y-m-d G:i:s');
			$updateuserid=$_SESSION['userid'];			
			$change = "UPDATE list Set name='".$_POST['c_name']."', updatedate='".$updatedate."', updateuserid='".$updateuserid."' WHERE id='$take'";
			$update = mysql_query($change)or die("Speichern leider fehlgeschlagen.".mysql_error());			
			//echo "Gespeichert";
			echo "<button type='button' class='btn btn-success'>Gespeichert</button>";
			}	
			
		If (isset($_POST['safe']) AND $take=='xxx')
			{	
			//Eintrag neu anlegen falls nicht vorhanden
			$createdate= date('Y-m-d G:i:s');
			$updatedate= date('Y-m-d G:i:s');
			$updateuserid=$_SESSION['userid'];				
			$send = "INSERT INTO list (name, createdate, updatedate, updateuserid) VALUES ('".$_POST['c_name']."', '".$createdate."', '".$updatedate."', '".$updateuserid."')";
			$sent = mysql_query($send) or die("Verteiler anlegen leider fehlgeschlagen".mysql_error());			
			echo "<button type='button' class='btn btn-success'>Verteiler angelegt</button>";
			//Neu angelegte ID mit auf den Weg geben
			$query = "SELECT id FROM list ORDER BY id DESC LIMIT 1";
			$checkdata = mysql_query($query);
			while($row = mysql_fetch_object($checkdata))
				{
				$take=$row->id;
				}
	
			}
		
		//Nun müssen wir im Speicherfalle immer auch die Checkboxes auswerten
		If (isset($_POST['safe']))
		{
		//Erstmal müssen wir rausfinden, welche Häkchen gesetzt sind
		//Oben haben wir ja schon alle möglichen customer durchgezählt, die gehen wir nun anhand der id durch
		//und schauen ob eine box mit deren id aktiviert ist
		//Zusätzlich schauen wir, ob die aktivierte box eines customers nicht bereits als aktiviert gespeichert ist und nicht angelegt werden muss
		//Zudem wird gecheckt ob aktivierte boxen entfernt werden müssen
		For ($i=1;$i<=$counter2;$i++)
			{
			//Ich gehe alle Customer-Ids durch
			$taking=$ids[$i];
			If ((!isset($_POST["box$taking"]) AND (!in_array($taking, $customer_id))))
				{
				//Kein Effekt
				}
			If ((isset($_POST["box$taking"]) AND (in_array($taking, $customer_id))))
				{
				//Kein Effekt	
				}					
			If ((isset($_POST["box$taking"]) AND (!in_array($taking, $customer_id))))
				{
				//Eintrag machen	
				$send = "INSERT INTO customerdistribution (customer, distribution) VALUES ('".$taking."','".$take."')";
				$sent = mysql_query($send) or die("Verteiler ändern leider fehlgeschlagen".mysql_error());
				//Da wir die Verteilermitglieder schon geladen haben. müssen wir diesen nun manuell ändern
				array_push($customer_id,$taking);
				//Zusätzlich müssen wir den Counter anheben
				$counting++;
				}	
			If ((!isset($_POST["box$taking"]) AND (in_array($taking, $customer_id))))
				{
				//Eintrag entfernen
				$send = "DELETE FROM customerdistribution WHERE customer ='$taking' AND distribution ='$take'";
				$sent = mysql_query($send)or die("Verteiler ändern leider fehlgeschlagen.".mysql_error());
				//Da wir die Verteilermitglieder schon geladen haben. müssen wir diesen nun manuell ändern
				unset($customer_id[array_search($taking, $customer_id)]); 
				//Zusätzlich müssen wir den Counter absenken
				$counting--;
				}	
			}			
		}
			
		If ($take!='xxx')
			{
			//Daten einlesen
			$query = "SELECT * FROM list WHERE id=$take";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{	
				while($row = mysql_fetch_object($checkdata))
					{
					$name=$row->name;				
					}
				}
			}	
		}
	If (!isset($_POST['dist']) OR $take=='xxx')
		{
		//Kein Datensatz festgelegt oder neu? - Erstmal Standardangaben zeigen	
		$name='';		
		}
?>

	
	<div id="mainview">
	
	
	
		<table border=0 class="centred"><tr><td align='left'>
	<?
	echo "<form action='distributions.php' method='post' style='display:inline;'>";
	echo "<button Hspace='5' type='submit' class='btn btn-info' title='Zur Übersicht' name='' value=''><span class='glyphicon glyphicon-share' aria-hidden='true'></span> Zurück zur Übersicht</button></form></td><td  colspan=2 bgcolor='#99ccff' align='right'></form>";						
	echo "</td><td width='20'></td><td align='right'>";
	echo "<form action='distributions.php' method='post' style='display:inline;'>";
	echo "<INPUT type='hidden' id='dist' name='dist' value='$take'>";
	?><button type='submit' class='btn btn-danger' title='Verteiler löschen' name='delete' value='' onclick="return confirm('Verteiler wirklich löschen?');"><span class='glyphicon glyphicon-trash' aria-hidden='true'></span> Verteiler löschen</button></form><?
				
	echo "<td width='20'></td><td align='right'><form action='distribution_detail.php' method='post'>";	
	
	echo "<INPUT type='hidden' id='dist' name='dist' value='$take'>";
	echo "<button type='submit' class='btn btn-primary' title='Speichern' name='safe' value='$take'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Speichern</button>";
	
	?>
	</td></tr></table><br><table border=0 class="centred">
	
	
	<?
/*	echo " <form action='distributions.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-info' title='Zur Übersicht' name='' value=''><span class='glyphicon glyphicon-share' aria-hidden='true'></span> Zurück zur Übersicht</button></form></td><td  colspan=2 bgcolor='#99ccff' align='right'>";
	echo "<form action='distribution_detail.php' method='post'>";?>
	<table border=0 class="centred">
	<?
			echo "<tr><td colspan=2 bgcolor='#99ccff' align='left'>";
			echo " <form action='distributions.php' method='post' style='display:inline;'>";
			echo "<INPUT type='hidden' id='dist' name='dist' value='$take'>";
			?><button Hspace="5" type='submit' class='btn btn-danger' title='Verteiler löschen' name='delete' value='' onclick="return confirm('Verteiler wirklich löschen?');"><span class='glyphicon glyphicon-trash' aria-hidden='true'></span> Löschen</button></form><?			
			
			echo "<td colspan=42 bgcolor='#99ccff' align='right'>";
			echo "<INPUT type='hidden' id='dist' name='dist' value='$take'>";
			echo "<button type='submit' class='btn btn-primary' title='Speichern' name='safe' value='$take'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Speichern</button></form></td></tr>";
			
			?>*/
		?>
		<tr height=20>
			<th class='cell' colspan=42 bgcolor='#cccccc'>Verteilername</th>
			</tr><tr>
			<?		
			echo "<td class='cell' colspan=42 bgcolor='#99ccff'>";
			echo "<input type='text' name='c_name' value='$name' placeholder='Verteilername' class='form-control' size='50'>";
			echo "</td>";				
			?>	
			</tr><tr height=10></tr><tr>
			<th class='cell' colspan=42 bgcolor='#cccccc'>Adressaten</th>		
			</tr>
			<?

			For ($i=1;$i<=$counter2;$i++)
				{
				If ($color=='#99ccff'){$color='#f5f5dc';}else{$color='#99ccff';}
				echo "<tr><td class='cell' bgcolor='$color'>";
				$taking=$ids[$i];	
				//wir müssen die Namen der Checkboxes definieren
				$boxname='box'.$taking;	
				If ($counting>0)
					{
					If (in_array($taking, $customer_id))
						{
						{echo "<input type='checkbox' checked name='$boxname'></td><td bgcolor='$color'><b><Span class='badge'>$firstname[$taking] $lastname[$taking]</span></b></td><td bgcolor='$color'><b>$company[$taking]</b></td><td bgcolor='$color'><b>$email[$taking]</b>";}
						}
					else
						{
						{echo "<input type='checkbox' name='$boxname'></td><td bgcolor='$color'>$firstname[$taking] $lastname[$taking]</td><td bgcolor='$color'>$company[$taking]</td><td bgcolor='$color'> $email[$taking]";}	
						}
					}
				else
					{echo "<input type='checkbox' name='$boxname'></td><td bgcolor='$color'>$firstname[$taking] $lastname[$taking]</td><td bgcolor='$color'>$company[$taking]</td><td bgcolor='$color'>$email[$taking]";}
				echo "</td></tr><tr height=10></tr>";
				}
			
			/*echo "<tr><td colspan=2 bgcolor='#99ccff' align='left'>";
			echo " <form action='distributions.php' method='post' style='display:inline;'>";
			echo "<INPUT type='hidden' id='dist' name='dist' value='$take'>";
			?><button type='submit' class='btn btn-danger' title='Verteiler löschen' name='delete' value='' onclick="return confirm('Verteiler wirklich löschen?');"><span class='glyphicon glyphicon-trash' aria-hidden='true'></span> Löschen</button></form><?			
			
			echo "<td colspan=42 bgcolor='#99ccff' align='right'>";
			echo "<INPUT type='hidden' id='dist' name='dist' value='$take'>";
			echo "<button type='submit' class='btn btn-primary' title='Speichern' name='safe' value='$take'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Speichern</button></form>";
			*/
			?>
		</table>

	</div>
<?	
include_once("footer.php");	
