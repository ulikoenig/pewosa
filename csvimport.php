<?
$pagetitle='CSV import';
include_once("header.php");


function csv_in_array($url,$delm=";",$encl="\"",$head=false) { 
    
    $csvxrow = file($url);   // ---- csv rows to array ----
    
    $csvxrow[0] = chop($csvxrow[0]); 
    $csvxrow[0] = str_replace($encl,'',$csvxrow[0]); 
    $keydata = explode($delm,$csvxrow[0]); 
    $keynumb = count($keydata); 
    
    if ($head === true) { 
    $anzdata = count($csvxrow); 
    $z=0; 
    for($x=1; $x<$anzdata; $x++) { 
        $csvxrow[$x] = chop($csvxrow[$x]); 
        $csvxrow[$x] = str_replace($encl,'',$csvxrow[$x]); 
        $csv_data[$x] = explode($delm,$csvxrow[$x]); 
        $i=0; 
        foreach($keydata as $key) { 
            $out[$z][$key] = $csv_data[$x][$i]; 
            $i++;
            }    
        $z++;
        }
    }
    else { 
        $i=0;
        foreach($csvxrow as $item) { 
            $item = chop($item); 
            $item = str_replace($encl,'',$item); 
            $csv_data = explode($delm,$item); 
            for ($y=0; $y<$keynumb; $y++) { 
               $out[$i][$y] = $csv_data[$y]; 
            }
        $i++;
        }
    }

return array ($out,$z); 
}


?>
	
	<div id="mainview">

	<table border=0 class="centred" >


	<tr><td colspan=42 class='cell' align='center'>

		


<legend>CSV-Import</legend>
<br>Verteiler auswählen<br>
<style type="text/css">
<!--
#a {height:150px;overflow:auto;}
// -->
</style>
<?
//laden wir erstmal alle möglichen verteilerlisten, denen die Daten zugeordnet werden könnten
$distcounter=0;
$query = "SELECT name, id FROM list WHERE deleted!=1 ORDER BY name DESC";
$checkdata = mysql_query($query);
if(mysql_num_rows($checkdata)>1)
	{

	while($row = mysql_fetch_object($checkdata))
		{
		$distcounter++;
		$ids[$distcounter]=$row->id;
		$name[$row->id]=$row->name;
		}
	}



//machen wir die verteilerlisten wählbar
echo "<form action='csvimport.php' method='post' enctype='multipart/form-data'>";
echo "<div id='a' align='left'>";
For ($i=1;$i<=$distcounter;$i++)
	{
	$take=$ids[$i];
	$boxname='box'.$take;
        echo "<br><label><input type='checkbox' name='$boxname'> $name[$take]</label>";	
	}
echo "</div><br><br>";
?> 

    <input type="file" name="dateiupload">
    <input type="submit" name="btn[upload]">
</form>


<?

     
        
if ($_FILES)
	{
	//echo "test";

	if (move_uploaded_file($_FILES['dateiupload']['tmp_name'], $_FILES['dateiupload']['name'])) 
		{
    		echo "Datei ist valide und wurde erfolgreich hochgeladen.\n";

	    	// Kontrolle, ob Dateityp zulässig ist
		if ($_FILES['dateiupload']['type']!='text/csv')
			{
			$handi=$_FILES['dateiupload']['type'];
			echo "<p>...aaaaber der Dateitype $handi ist NICHT zugelassen. Nur csv Import möglich.</p>";
			}
		else
			{
			$hemd=$_FILES['dateiupload']['name'];

			list($csvdata,$counter) = csv_in_array($hemd, ",", "\"", true ); 

			//echo "<pre>\r\n"; 
			//print_r($csvdata);
			//echo "</pre>\r\n"; 

			For ($i=0;$i<=$counter;$i++)
				{
				//$hand=$csvdata[$i]['Mail'];
				//echo "<br>$i $hand";
				//Kommt jetzt also in die Datenbank als customer
				$createdate= date('Y-m-d G:i:s');
				$notes='Autoimport';
				//Nur eintragen, wenn es die Email noch nicht gibt
				$handy=$csvdata[$i]['Mail'];
				$query = "SELECT email FROM customer WHERE email='$handy'";
				$checkdata = mysql_query($query);
				//geht wohl nicht, weil idealfall 0 und dann fehler
				if(mysql_num_rows($checkdata)<1)
					{
					//Leere Mails ignorieren
					If ($csvdata[$i]['Mail']!='')
						{
						//echo "<br>Hier hätte ich einen neuen customer mit email $handy eingetragen";
						$send = "INSERT INTO customer (email, createdate, deleted, notes, company, firstname, lastname, street, streetnumber, zipcode, phone, cellphone, birthdate, city) VALUES ('".$handy."','".$createdate."','0','".$notes."','','','','','','','','','','')";
						//echo "<br> $send";
						$sent = mysql_query($send) or die("Kontakt anlegen leider fehlgeschlagen".mysql_error());
						//Neue id holen
						$query2 = "SELECT id FROM customer ORDER BY id DESC LIMIT 1";
						$checkdata2 = mysql_query($query2);
						while($row2 = mysql_fetch_object($checkdata2))
							{
							$customer=$row2->id;
							}	
						//Und hier gleich die Verknüpfung zu den gewünschten Verteilern herstellen
						If ($distcounter>0)		
							{
							//Nur eintragen für die gewählten Verteiler
							For ($y=1;$y<=$distcounter;$y++)
								{
								$take=$ids[$y];
								If (isset($_POST["box$take"]))
									{
									//echo "<br>Hier hätte ich nun die Verknüpfung zu Verteiler $ids[$y] eingetragen.";
									$send = "INSERT INTO customerdistribution (customer, distribution) VALUES ('".$customer."','".$ids[$y]."')";
									//echo "<br>$send";
									$sent = mysql_query($send) or die("Kontakt anlegen leider fehlgeschlagen".mysql_error());
									}
								}

							}
						}
					}
				else
					{
					echo "<br> Die Emailadresse $handy ist bereits im System und wurde nicht übernommen...";
					}
				}
				echo "<br> $i CSV-Dateien bearbeitet.";
				}
			} 
		else 
			{
    			echo "Möglicherweise eine Dateiupload-Attacke!\n";
			}

		}







?>
	</td></tr>
	
		
	</table>
	</div>
	  
<?	
include_once("footer.php");	
