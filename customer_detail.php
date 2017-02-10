<?
$pagetitle='Adressat Bearbeiten';
include_once("header.php");


	
	If (isset($_POST['customer']) OR isset($_GET['cust']))
		{
		$take=$_POST['customer'];

		If (isset($_GET['cust'])){$take=$_GET['cust'];}

		If (isset($_POST['safe']) AND $take!='xxx')
			{
			//Erstmal Geburtstag Datenbanktauglich machen
			$teile = explode(".", $_POST['c_birthdate']);
			$jahr=$teile[2];
			$monat=$teile[1];
			$tag=$teile[0];
			$b_show=$jahr."-".$monat."-".$tag;			

			$updatedate = date('Y-m-d G:i:s');

			$updateuserid=$_SESSION['userid'];
			$hand=md5($_POST['c_email'].$take);				
			//Eintrag anpassen, falls bereits vorhanden
			$change = "UPDATE customer Set 
			firstname='".$_POST['c_firstname']."',lastname='".$_POST['c_lastname']."',
			company='".$_POST['c_company']."',phone='".$_POST['c_phone']."',
			cellphone='".$_POST['c_cellphone']."',email='".$_POST['c_email']."',
			street='".$_POST['c_street']."',streetnumber='".$_POST['c_streetnumber']."',			
			zipcode='".$_POST['c_zipcode']."',city='".$_POST['c_city']."',			
			birthdate='".$b_show."',notes='".$_POST['c_notes']."',
			updatedate='".$updatedate."', updateuserid='".$updateuserid."', hash='".$hand."'			
			WHERE id='$take'";

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
			$send = "INSERT INTO customer (firstname, lastname, company, phone, cellphone, email, street, streetnumber, zipcode, city, birthdate, notes, createdate, updatedate, updateuserid) VALUES 
			('".$_POST['c_firstname']."', '".$_POST['c_lastname']."', '".$_POST['c_company']."', '".$_POST['c_phone']."', '".$_POST['c_cellphone']."', '".$_POST['c_email']."', '".$_POST['c_street']."', '".$_POST['c_streetnumber']."', '".$_POST['c_zipcode']."', '".$_POST['c_city']."', '".$_POST['c_birthdate']."', '".$_POST['c_notes']."', '".$createdate."', '".$updatedate."', '".$updateuserid."')";
			$sent = mysql_query($send) or die("Kontakt anlegen leider fehlgeschlagen".mysql_error());			
			echo "<button type='button' class='btn btn-success'>Kontakt angelegt</button>";
			//Neu angelegte ID mit auf den Weg geben
			$query = "SELECT id FROM customer ORDER BY id DESC LIMIT 1";
			$checkdata = mysql_query($query);
			while($row = mysql_fetch_object($checkdata))
				{
				$take=$row->id;
				$hand=md5($_POST['c_email'].$take);
				$change = "UPDATE customer Set hash='".$hand."' WHERE id='$take'";
				$update = mysql_query($change)or die("Speichern leider fehlgeschlagen.".mysql_error());				
				}
			}		

		
		If ($take!='xxx')
			{
			//Daten einlesen
			$query = "SELECT * FROM customer WHERE id=$take";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{	
				while($row = mysql_fetch_object($checkdata))
					{
					$firstname=$row->firstname;
					$lastname=$row->lastname;
					$company=$row->company;
					$phone=$row->phone;
					$cellphone=$row->cellphone;
					$email=$row->email;		
					$street=$row->street;
					$streetnumber=$row->streetnumber;
					$zipcode=$row->zipcode;
					$city=$row->city;			
					$birthdate=$row->birthdate;
					$notes=$row->notes;
					}
				}
			}	
		}
	If ((!isset($_POST['customer']) AND !isset($_GET['cust'])) OR $take=='xxx' )
		{
		//Kein Datensatz festgelegt oder neu? - Erstmal Standardangaben zeigen	
		$firstname='';
		$lastname='';
		$company='';
		$phone='';
		$cellphone='';
		$email='';		
		$street='';
		$streetnumber='';
		$zipcode='';
		$city='';			
		$birthdate='';
		$notes='';			
		}


	//Laden wir mal alle möglichen Verteiler
	$counter2=0;
	$query = "SELECT name, id FROM list WHERE deleted!=1 ORDER BY name";
	$checkdata = mysql_query($query);
	if(mysql_num_rows($checkdata)>=1)
		{	
		while($row = mysql_fetch_object($checkdata))
			{
			$counter2++;
			$ids[$counter2]=$row->id;
			$name[$row->id]=$row->name;
			}
		}	
	$in_club[0]=0;
	If ($take!='xxx')
		{
		//Nun laden wir die Verteilerlisten, in denen der ausgewählte Adressat Mitglied ist
		//Aber nur, wenn er nicht gerade neu angelegt werden soll
		$counter3=0;
		$query = "SELECT distribution FROM customerdistribution WHERE customer=$take";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)>=1)
			{	
			while($row = mysql_fetch_object($checkdata))
				{
				$counter3++;
				$in_club[$counter3]=$row->distribution;
				}
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
			//Ich gehe alle Verteiler-Ids durch, ob sie in der IchbinimClubliste des Adressaten sind
			$taking=$ids[$i];			
			If ((isset($_POST["box$taking"]) AND (!in_array($taking, $in_club))))
				{
				//Eintrag machen	
				$send = "INSERT INTO customerdistribution (customer, distribution) VALUES ('".$take."','".$taking."')";
				$sent = mysql_query($send) or die("Verteiler ändern leider fehlgeschlagen".mysql_error());
				//Da wir die Verteilerlisten schon geladen haben. müssen wir diesen nun manuell ändern
				array_push($in_club,$taking);
				}	
			If ((!isset($_POST["box$taking"]) AND (in_array($taking, $in_club))))
				{
				//Eintrag entfernen
				$send = "DELETE FROM customerdistribution WHERE customer ='$take' AND distribution ='$taking'";
				$sent = mysql_query($send)or die("Verteiler ändern leider fehlgeschlagen.".mysql_error());
				//Da wir die Verteilermitglieder schon geladen haben. müssen wir diesen nun manuell ändern
				unset($in_club[array_search($taking, $in_club)]); 
				}	
			}			
		}		
		
		
	?>
	<div id="mainview">
	
	
	
			<table border=0 class="centred"><tr><td colspan=42>
	<?

	echo "<form action='customer_detail.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-primary' title='Neuen Adressaten anlegen' name='customer' value='xxx'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Neuen Adressaten 		einrichten</button></form>";

	echo " <form action='customer.php' method='post' style='display:inline;'>";
	echo "<button Hspace='5' type='submit' class='btn btn-info' title='Zur Übersicht' name='' value=''><span class='glyphicon glyphicon-share' aria-hidden='true'></span> Zurück zur Übersicht</button></form>";						

	echo " <form action='customer.php' method='post' style='display:inline;'>";
	echo "<INPUT type='hidden' id='customer' name='customer' value='$take'>";
	?><button type='submit' class='btn btn-danger' title='Adressat löschen' name='delete' value='' onclick="return confirm('Adressat wirklich löschen?');"><span class='glyphicon glyphicon-trash' aria-hidden='true'></span> Adressat löschen</button></form><?


				
	echo " <form action='customer_detail.php' method='post' style='display:inline;'>";	
	
	echo "<INPUT type='hidden' id='customer' name='customer' value='$take'>";
	echo "<button type='submit' class='btn btn-primary' title='Speichern' name='safe' value='$take'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Speichern</button>";
	
	?>
	</td></tr></table><br><table border=0 class="centred">
	
	
		<tr height=20>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Name</th>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Unternehmen</th>
			</tr><tr>
			<?		
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='text' name='c_firstname' value='$firstname' placeholder='Vorname' size='50' class='form-control'>";
			echo "</td>";				
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='text' name='c_company' value='$company' placeholder='Unternehmen' size='50' class='form-control'>";			
			echo "</td>";		
			?>	
			</tr><tr>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Nachname</th>		
			<th class='cell' colspan=2 bgcolor='#cccccc'>Telefon</th>
			</tr><tr>
			<?
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='text' name='c_lastname' value='$lastname' placeholder='Nachname' size='50' class='form-control'></td>";
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' name='c_phone' value='$phone' placeholder='Festnetz' class='form-control'></td>";		
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' name='c_cellphone' value='$cellphone' placeholder='Handy' class='form-control'></td>";	
			?>				
			</tr><tr>
			<th class='cell' colspan=2 bgcolor='#cccccc'>E-Mail</th>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Geburtstag</th>
			</tr><tr>
			<?
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='email' required name='c_email' value='$email' placeholder='mail@mail.de' size='50' class='form-control'></td>";
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			$teile = explode("-", $birthdate);
			$jahr=$teile[0];
			$monat=$teile[1];
			$tag=$teile[2];
			$b_show=$tag.".".$monat.".".$jahr;
			echo "<input type='text' name='c_birthdate' value='$b_show' placeholder='TT.MM.JJJJ' size='50' class='form-control'></td>";		
			?>	
			</tr><tr>
			<th class='cell' bgcolor='#cccccc'>Straße</th>
			<th class='cell' bgcolor='#cccccc'>Nr</th>
			<th class='cell' bgcolor='#cccccc'>PLZ</th>			
			<th class='cell' bgcolor='#cccccc'>Stadt</th>	
			</tr><tr>
			<?
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' name='c_street' value='$street' placeholder='Musterstraße' class='form-control'></td>";
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' name='c_streetnumber' value='$streetnumber' placeholder='42b' class='form-control'></td>";		
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' name='c_zipcode' value='$zipcode' placeholder='12345' class='form-control'></td>";	
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' name='c_city' value='$city' placeholder='Musterstadt' class='form-control'></td>";
			?>				
			</tr><tr>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Verteiler</th>	
			<th class='cell' colspan=2 bgcolor='#cccccc'>Notizen</th>
			</tr><tr>
			<?
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			
			//Nun werden alle Verteilerlisten aufgeführt
			echo "<div class='scroll'><form action='customer_detail.php' method='post'>";
			If ($counter2>0)
				{
				Foreach ($ids as $distribs)
					{
					$boxname='box'.$distribs;	
					If (in_array($distribs, $in_club)){echo "<input type='checkbox' checked name='$boxname'> $name[$distribs]<br>";}
					else {echo "<input type='checkbox' name='$boxname'> $name[$distribs]<br>";}
					}
				}	

			echo "</td>";
			echo "<td colspan=2 bgcolor='#99ccff'>";
			echo "<textarea rows='5' cols='35' name='c_notes' class='form-control'>$notes</textarea></td></form>";	
				
			?>
		</tr></table>

	</div>
<?	
include_once("footer.php");	
