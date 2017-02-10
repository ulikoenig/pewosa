<?php
session_start(); 
if (version_compare(PHP_VERSION, '5.5.0') < 0) {
    include_once("password.php");
}

require 'lib/PHPMailerAutoload.php';
require("UrlLinker.php");
include_once ("connection.php");
include_once("config.php");
include_once("functions.php");



	If (isset($_POST['new']))
		{
		//Checken wir mal ob die E-Mail schon bekannt ist
		$handy=$_POST['c_email'];
		$query = "SELECT email FROM customer WHERE email='$handy'";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{
			$double=TRUE;
			}
		else
			{
			$double=FALSE;
			}
		If (!$double)
			{
			//Erstmal Geburtstag Datenbanktauglich machen
			$teile = explode(".", $_POST['c_birthdate']);
			$jahr=$teile[2];
			$monat=$teile[1];
			$tag=$teile[0];
			$b_show=$jahr."-".$monat."-".$tag;
			$notes= mysql_real_escape_string ($_POST['c_notes']);
			//Wir müssen noch die gewünschten Verteiler in die Notizen setzen
			If (isset($_POST['box1']))
				{
				$notes=$notes." Möchte in den großen Verteiler";
				}
			If (isset($_POST['box2']))
				{
				$notes=$notes." Möchte in den Fachverteiler";
				}
			If (isset($_POST['box3']))
				{
				$notes=$notes." Möchte in den Regionalverteiler";
				}
			If (isset($_POST['box4']))
				{
				$notes=$notes." Möchte in den Spezialverteiler";
				}					
			$createdate= date('Y-m-d G:i:s');
			$updatedate= date('Y-m-d G:i:s');


			$c_firstname = mysql_real_escape_string ($_POST['c_firstname']);
			$c_lastname = mysql_real_escape_string ($_POST['c_lastname']);
			$c_company = mysql_real_escape_string ($_POST['c_company']);
			$c_phone = mysql_real_escape_string ($_POST['c_phone']);
			$c_cellphone = mysql_real_escape_string ($_POST['c_cellphone']);
			$c_email = mysql_real_escape_string ($_POST['c_email']);
			$c_street = mysql_real_escape_string ($_POST['c_street']);
			$c_streetnumber = mysql_real_escape_string ($_POST['c_streetnumber']);
			$c_zipcode = mysql_real_escape_string ($_POST['c_zipcode']);
			$c_city = mysql_real_escape_string ($_POST['c_city']);
			$activationcode=mt_rand(1000000,9999999);
			$receiverName = $c_firstname." ".$c_lastname;
	
			$send = "INSERT INTO customer (firstname, lastname, company, phone, cellphone, email, street, streetnumber, zipcode, city, birthdate, notes, createdate, updatedate, updateuserid, activationcode, deleted) VALUES('".$c_firstname."', '".$c_lastname."', '".$c_company."', '".$c_phone."', '".$c_cellphone."', '".$c_email."', '".$c_street."', '".$c_streetnumber."', '".$c_zipcode."', '".$c_city."', '".$b_show."', '".$notes."', '".$createdate."', '".$updatedate."', '0', '".$activationcode."','1')";
			$sent = mysql_query($send) or die("Kontakt anlegen leider fehlgeschlagen".mysql_error());
			$take = mysql_insert_id();
			$hand=md5($c_email.$take);
			$change = "UPDATE customer Set hash='".$hand."' WHERE id='$take'";	
			$sent = mysql_query($change) or die("Hash-Speichern leider fehlgeschlagen".mysql_error());
			
			echo "Fast geschafft! Wir haben Dir eine Aktivierungsmail geschickt. Bitte klicke auf den Link in der Mail, um Dein Pressekonto zu aktivieren.";
			//Hier Mail verschicken
			$subject='Aktivierung Deines Presse-Kontos';
			$body = "Fast geschafft! Bitte klicke noch auf folgenden Link, um unsere Pressemitteilungen zu abonnieren:\n ".BASEURL."/regcust.php?activationcode=".$activationcode."\n\n".QUITPRESS."\n\n ".PRESSFORMAL;
			$body = htmlEscapeAndLinkUrls ( $body );
			sendactivation($subject,$body,$c_email,$receiverName);
		
			}
		else
			{
			//echo "E-Mail-Adresse existiert bereits...";
			$firstname=$_POST['c_firstname'];
			$lastname=$_POST['c_lastname'];
			$company=$_POST['c_company'];
			$phone=$_POST['c_phone'];
			$cellphone=$_POST['c_cellphone'];
			$street=$_POST['c_street'];
			$streetnumber=$_POST['c_streetnumber'];
			$zipcode=$_POST['c_zipcode'];
			$city=$_POST['c_city'];			
			$birthdate=$_POST['c_birthday'];
			$notes=$_POST['c_notes'];
			}			
		}


function bcrypt_check ( $email, $password, $stored )
{
    $string = hash_hmac ( "CodePass", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $email ), STR_PAD_BOTH ), SALT, true );
    return crypt ( $string, substr ( $stored, 0, 30 ) ) == $stored;
}

	If (isset($_GET['out']))
		{
		//Gucken wir mal ob es die Mail gibt und setzen sie dann ggf. inaktiv
		$took=$_GET['out'];

		//Nun gehen wir die Datenbank durch und suchen, ob es die Combi gibt
		$found_id=0;



		$query = "SELECT id, hash FROM customer WHERE email='christian.lewin@piratenfraktion-sh.de' LIMIT 1";
		//echo "query: $query";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				echo "hash: $row->hash<br><br>";
				}
			}



		$query = "SELECT id,email FROM customer WHERE hash='$took' LIMIT 1";
		//echo "query: $query";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$found_id=$row->id;
				}
			}

		If ($found_id!=0)
			{
			$query = "SELECT id FROM customer WHERE id='$found_id'";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)==1)
				{
				$change = "UPDATE customer Set deleted='1' WHERE id='$found_id'";
				$update = mysql_query($change)or die("Fehler.".mysql_error());
				$textout="Abgemacht! Wir schicken keine weitere Pressemitteilungen an Deine E-Mail-Adresse.";
				}
			else
				{
				$textout="Hier ist leider ein Fehler passiert. Deine E-Mail-Adresse wurde nicht im System gefunden.";
				}
			}
		else
			{
			$textout="Hier ist leider ein Fehler passiert. Deine E-Mail-Adresse wurde nicht im System gefunden.";
			}
		
		}

	If (isset($_GET['activationcode']))
		{
		//Gucken wir mal ob es den Code gibt und setzen den Nutzer dann ggf. aktiv
		$took=$_GET['activationcode'];
		$query = "SELECT activationcode FROM customer WHERE activationcode='$took'";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{
			$change = "UPDATE customer Set deleted='0',activationcode='0' WHERE activationcode='$took'";
			$update = mysql_query($change)or die("Fehler.".mysql_error());
			$textout="Vielen Dank für die Aktivierung! Du bekommst ab sofort unsere Pressemitteilungen.";
			}
		else
			{
			$textout="Hier ist leider ein Fehler passiert. Deine Konto wurde nicht im System gefunden.";
			}			
		}

?>


<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">

<!--	<link rel="stylesheet" type="text/css" href="paint.css"> -->

  <title>Pewosa - Presseanmeldung</title>	
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

<link rel="stylesheet" href="paint.css">


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script
	src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>
body {
	padding-top: 50px;
}

.mainCol {
	width: 100%
}

.rightCol {
	width: 100%
}
</style>
</head> 
<body>
 
<?php 
if(isset($errorMessage)) {
	echo $errorMessage;
}
?>


<br>

	<div id="mainview">
	
	<?

	If (!isset($_GET['out']) AND !isset($_GET['activationcode']))
		{

		If ((!isset($_POST['new'])) OR ((isset($_POST['new'])) AND $double))
			{
			If ($double){echo "E-Mail-Adresse existiert bereits...";}
			?>

			<br>
			<br><table border=0 class="centred">
		
			<tr><td class='cell' colspan=42 >
			<h1>
			Pewosa - Anmelden für Presseverteiler
			</h1></td></tr>

			<tr><td class='cell' colspan=42 bgcolor='#99ccff'><Font size=3>
			<? registertext(); ?>
			</Font></td></tr>
		
			<tr height=20>
				<th class='cell' colspan=2 bgcolor='#cccccc'>Name</th>
				<th class='cell' colspan=2 bgcolor='#cccccc'>Unternehmen</th>
				</tr><tr><form action='regcust.php' method='post'>
				<?
		
				echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
				echo "<input type='text' required name='c_firstname' value='$firstname' placeholder='Vorname' size='50' class='form-control'>";
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
				echo "<input type='text' required name='c_lastname' value='$lastname' placeholder='Nachname' size='50' class='form-control'></td>";
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
				//echo "<input type='text' name='c_birthdate' value='$b_show' placeholder='TT.MM.JJJJ' size='50' class='form-control'></td>";		
				echo "<input type='text' name='c_birthdate' placeholder='TT.MM.JJJJ' size='50' class='form-control'></td>";
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
				echo "<div class='scroll'>";
				echo "<input type='checkbox' name='box1' checked> Großer Verteiler (Alle PMs) <br>";
				echo "<input type='checkbox' name='box2'> Fach-Verteiler <br>";
				echo "<input type='checkbox' name='box3'> Regionalverteiler <br>";
				echo "<input type='checkbox' name='box4'> Spezialverteiler (Info in Notizen) <br>";

				echo "</td>";
				echo "<td colspan=2 bgcolor='#99ccff'>";
				echo "<textarea rows='5' cols='35' name='c_notes' class='form-control'>$notes</textarea></td>";	

				
				?>
			</tr><tr><td colspan='42' align='right'>
			<button type='submit' class='btn btn-primary' title='Speichern' name='new' value='1'>
			<span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Eintragen</button></td></tr></form>
			<tr><td class='cell' colspan=42 >	
			<?
			echo "<a href=".PRESSELINK."><Font Size=3>Zurück zur Hauptseite</Font></a>";
			?>
			<br><br>
			<a href='regnews.php'><Font Size=3>Du möchtest unseren Newsletter bekommen?</Font></a>
			</td></tr>
			</table>
			<?
			}
			else
			{
			echo "Vielen Dank!";
			}
			
		}
	else
		{
		?>			
		<br>
		<br><table border=0 class="centred">
	
		<tr><td class="cell" colspan=42 >
		<h1>
		<?
		echo "$textout";
		echo "</h1><a href=".PRESSELINK."><font size=3>Zur Hauptseite</font></a></td></tr></table>";
		}
?>
	</div>


</body>
</html>
