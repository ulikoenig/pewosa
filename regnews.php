<?php
session_start(); 
if (version_compare(PHP_VERSION, '5.5.0') < 0) {
    include_once("password.php");
}

require 'lib/PHPMailerAutoload.php';
require("UrlLinker.php");
include_once ("connection.php");
include_once("config.php");


function sendactivation($subject,$body,$receiverMail,$receiverName){
	$mail = new PHPMailer;
	$mail->CharSet = 'UTF-8';

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'zimap.bytemine.net';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'presse@piratenfraktion-sh.de';                 // SMTP username
	$mail->Password = 'umgrou9ngkdq';                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to

	$mail->setFrom('presse@piratenfraktion-sh.de', 'Piratenfraktion SH: Pressestelle');
	$mail->addAddress($receiverMail, $receiverName);     // Add a recipient

	$mail->isHTML(false);                                  // Set email format to HTML
	include("fontdata.php");

	setlocale (LC_ALL, 'de_DE');

	$htmlFooter = "<p style=\"font-size: 10px; line-height: 12px; color: rgb(33, 33, 33); margin-bottom: 10px;\"><a href=\"mailto:presse@piratenfraktion-sh.de\" style=\"color: rgb(71, 124, 204); text-decoration: none; display: inline;\">presse@piratenfraktion-sh.de</a><span style=\"color: #212121;\"></span></p><p style=\"font-size: 10px; line-height: 12px; margin-bottom: 10px;\"><span style=\"font-weight: bold; color: rgb(33, 33, 33); display: inline;\">Piratenfraktion im Schleswig-Holsteinischen Landtag</span><br><span style=\"color: rgb(33, 33, 33); display: inline;\">0431 988 1337</span><br/><span style=\"color: rgb(33, 33, 33); display: inline;\">Düsternbrooker Weg 70</span><br/><span style=\"color: rgb(33, 33, 33); display: inline;\">24105 Kiel</span><br/><a href=\"https://piratenfraktion-sh.de\" style=\"color: rgb(71, 124, 204); text-decoration: none; display: inline;\">https://piratenfraktion-sh.de</a></p><p style=\"font-size: 0px; line-height: 0; font-family: Helvetica,Arial,sans-serif;\"><a style=\"text-decoration: none; display: inline;\" href=\"https://twitter.com/FraktionSH\">$twitterlogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span><a style=\"text-decoration: none; display: inline;\" href=\"https://www.facebook.com/Piraten-im-Schleswig-Holsteinischen-Landtag-1709680989297612/\">$facebooklogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span><a style=\"text-decoration: none; display: inline;\" href=\"https://www.instagram.com/piratenfraktionsh/\">$instagramlogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span><a style=\"text-decoration: none; display: inline;\" href=\"https://www.youtube.com/user/PiratenFraktionSH\">$youtubelogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span></p>";

	$textFooter = "\n-- \npresse@piratenfraktion-sh.de\nPiratenfraktion im Schleswig-Holsteinischen Landtag\n0431 988 1337\nDüsternbrooker Weg 70\n24105 Kiel\nhttps://piratenfraktion-sh.de";



	$htmlBody = "<!DOCTYPE html><html lang=\"de\"><head><meta charset=\"utf-8\"><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><meta name=\"robots\" content=\"noindex,nofollow\"><title>".$subject."</title><style type=\"text/css\">body{max-width:40em;font-family: \"Open Sans\",\"Sans Serif\";background-color: orange;} h1 {font-family:\"Bebas Neue\",\"Sans Serif\";} .container {background-color: white;padding:1em;}</style></head><body><div class=\"container\">$logo<p></p><h3>".$subject."</h3><p>".strftime("%A, %e %B %G")."</p><p>".$body."</p></div>$htmlFooter</body></html>";


	$textBody = $subject."*\n\n".strftime("%A, %e %B %G")."\n\n".$body.$textFooter;

	$mail->Subject = $subject;
	$mail->Body    = $htmlBody;
	$mail->AltBody = $textBody;

	if(!$mail->send()) {
 		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
		//echo 'Message has been sent';
	}

} // ENDE sendpm


	If (isset($_POST['new']))
		{
		//Checken wir mal ob die E-Mail schon bekannt ist
		$handy=$_POST['c_email'];
		$query = "SELECT email FROM customerNewsletter WHERE email='$handy'";
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
			$createdate= date('Y-m-d G:i:s');
			$c_firstname = mysql_real_escape_string ($_POST['c_firstname']);
			$c_lastname = mysql_real_escape_string ($_POST['c_lastname']);
			$c_email = mysql_real_escape_string ($_POST['c_email']);
			$activationcode=mt_rand(1000000,9999999);
			$receiverName = $c_firstname." ".$c_lastname;
			$send = "INSERT INTO customerNewsletter (firstname, lastname, email, activationcode) VALUES('".$c_firstname."', '".$c_lastname."', '".$c_email."', '".$activationcode."')";
			$sent = mysql_query($send) or die("Speichern leider fehlgeschlagen".mysql_error());
			echo "Fast geschafft! Wir haben Dir eine Aktivierungsmail geschickt. Bitte klicke auf den Link in der Mail, um Dein Newsletterkonto zu aktivieren.";
			//Hier Mail verschicken
			$subject='Aktivierung Deines Newsletter-Kontos';
			$body = "Fast geschafft! Bitte klicke noch auf folgenden Link, um unseren Newsletter zu abonnieren:\n ulikoenig.de/pewosa/regnews.php?activationcode=".$activationcode."\n\nEinen Link zum Abbestellen findest Du in jedem Newsletter selbst.\n\n Viele Grüße\n\n Dein Piratenfraktionsteam";
			sendactivation($subject,$body,$c_email,$receiverName);
			}
		else
			{
			//echo "E-Mail-Adresse existiert bereits...";
			$firstname=$_POST['c_firstname'];
			$lastname=$_POST['c_lastname'];
			}			
		}

	If (isset($_GET['out']))
		{
		//Gucken wir mal ob es die Mail gibt und setzen sie dann ggf. inaktiv
		/*$took=$_GET['out'];
		$query = "SELECT email FROM customerNewsletter WHERE email='$took'";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{
			$change = "UPDATE customerNewsletter Set active='0' WHERE email='$took'";
			$update = mysql_query($change)or die("Fehler.".mysql_error());
			$textout="Abgemacht! Wir schicken keine weitere Newsletter an Deine E-Mail-Adresse $took.";			
			}
		else
			{
			$textout="Hier ist leider ein Fehler passiert. Deine E-Mail-Adresse wurde nicht im System gefunden.";
			}*/

		//Gucken wir mal ob es die Mail gibt und setzen sie dann ggf. inaktiv
		$took=$_GET['out'];

		//Nun gehen wir die Datenbank durch und suchen, ob es die Combi gibt
		$found_id=0;


		$query = "SELECT id,email FROM customerNewsletter";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$hand=md5($row->email.'PeWoSaSalt');
				
				If ($hand == $took)				
					{
					$found_id=$row->id;
					}
				}
			}

		If ($found_id!=0)
			{
			$query = "SELECT id FROM customerNewsletter WHERE id='$found_id'";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)==1)
				{
				$change = "UPDATE customerNewsletter Set active='0' WHERE id='$found_id'";
				$update = mysql_query($change)or die("Fehler.".mysql_error());
				$textout="Abgemacht! Wir schicken keine weiteren Newsletter an Deine E-Mail-Adresse.";
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
		$query = "SELECT activationcode FROM customerNewsletter WHERE activationcode='$took'";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)==1)
			{
			$change = "UPDATE customerNewsletter Set active='1',activationcode='' WHERE activationcode='$took'";
			$update = mysql_query($change)or die("Fehler.".mysql_error());			
			}
		$textout="Vielen Dank für die Aktivierung! Du bekommst ab sofort unseren Newsletter.";	
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

  <title>Pewosa - Newsletteranmeldung</title>	
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
			<br>		
			<table border=0 class="centred">

			<tr><td class='cell' colspan=42 >
			<h1>
			Pewosa - Anmelden für unseren Newsletter
			</h1>
			</td></tr>

			<tr><td class='cell' colspan=42 bgcolor='#99ccff'><Font size=3>
			Datenschutz ist uns PIRATEN besonders wichtig, deshalb musst Du uns Deinen Namen natürlich nicht verraten, wenn Du unseren Newsletter abonnieren möchtest.<br>
			Eine E-Mail-Adresse brauchen wir aber schon. Wir versprechen, Deine Daten nicht an Dritte weiterzugeben. Unsere Server stehen in Deutschland.
			</Font></td></tr>
	
	
			<tr height=20>
				<th class='cell' colspan=1 bgcolor='#cccccc'>Vorname</th>
				<th class='cell' colspan=1 bgcolor='#cccccc'>Nachname</th>
				</tr><tr><form action='regnews.php' method='post'>
				<?
		
				echo "<td class='cell' colspan=1 bgcolor='#99ccff'>";
				echo "<input type='text' name='c_firstname' value='$firstname' placeholder='Vorname' size='50' class='form-control'>";
				echo "</td>";				
				echo "<td class='cell' colspan=1 bgcolor='#99ccff'>";
				echo "<input type='text' name='c_lastname' value='$lastname' placeholder='Nachname' size='50' class='form-control'>";			
				echo "</td>";		
				?>	
				</tr><tr>
				<th class='cell' colspan=2 bgcolor='#cccccc'>E-Mail</th>		
				</tr><tr>
				<?
				echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
				echo "<input type='email' required name='c_email' value='' placeholder='mail@mail.de' size='50' class='form-control'></td>";
		
				?>
			</tr><tr><td colspan='42' align='right'>
			<button type='submit' class='btn btn-primary' title='Speichern' name='new' value='1'>
			<span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Eintragen</button></td></tr></form>

			<tr><td class='cell' colspan=42 >	
			<a href='login.php'><Font Size=3>Zurück zur Hauptseite</Font></a>
			<br><br>
			<a href='regcust.php'><font size=3>Du möchtest unsere Pressemitteilungen bekommen?</font></a>
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
	
		<tr><td class='cell' colspan=42 >
		<h1>
		<?
		echo "$textout";
		?>
		</h1><a href='login.php'><font size=3>Zur Hauptseite</font></a></td></tr></table>
		<?
		}
		?>
	</div>


</body>
</html>
