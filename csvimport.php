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
<form action="csvimport.php" method="post" enctype="multipart/form-data">
    <input type="file" name="dateiupload">
    <input type="submit" name="btn[upload]">
</form>

<form enctype="multipart/form-data" role="form" 
    class="form-horizontal" action=<?php echo $_SERVER['PHP_SELF']; ?> method="post">
<?

     
        
if ($_FILES)
{
    //echo "<pre>\r\n";
    //echo htmlspecialchars(print_r($_FILES,1));
    //echo "</pre>\r\n";

if (move_uploaded_file($_FILES['dateiupload']['tmp_name'], $_FILES['dateiupload']['name'])) {
    echo "Datei ist valide und wurde erfolgreich hochgeladen.\n";
} else {
    echo "Möglicherweise eine Dateiupload-Attacke!\n";
}

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
			$send = "INSERT INTO customer (email, createdate, deleted, notes, company, firstname, lastname, street, streetnumber, zipcode, phone, cellphone, birthdate, city) VALUES ('".$handy."','".$createdate."','0','".$notes."','','','','','','','','','','')";
			$sent = mysql_query($send) or die("Kontakt anlegen leider fehlgeschlagen".mysql_error());
			//Neue id holen
			$query2 = "SELECT id FROM customer ORDER BY id DESC LIMIT 1";
			$checkdata2 = mysql_query($query2);
			while($row2 = mysql_fetch_object($checkdata2))
				{
				$customer=$row2->id;
				}	
			//Und hier gleich die Verknüpfung zum großen Verteiler id 19 herstellen
			$send = "INSERT INTO customerdistribution (customer, distribution) VALUES ('".$customer."','19')";
			$sent = mysql_query($send) or die("Kontakt anlegen leider fehlgeschlagen".mysql_error());
			}
		}
	}
echo "<br> Erfolgreich $i CSV-Dateien migriert.";



}







?>
	</td></tr>
	
		
	</table>
	</div>
	  
<?	
include_once("footer.php");	
