<?
$pagetitle='Newsletter Bearbeiten';
include_once("header.php");
require 'lib/PHPMailerAutoload.php';
require("UrlLinker.php");
if($loggedinadmin == "1" OR $loggedinpressagent == "1")
	{
	die('Sie sind nicht berechtigt diese Funktion zu nutzen.</a>');
}
if (version_compare(PHP_VERSION, '5.5.0') < 0) {
    include_once("password.php");
}

	$imout = "\n <a href='http://www.ulikoenig.de/pewosa/regnews.php?out=Test'>Mit einem Klick den Newsletter abbestellen</a>";
	
	//$textFooter = "\n \npresse@piratenfraktion-sh.de\nPiratenfraktion im Schleswig-Holsteinischen Landtag\n0431 988 1337\nDüsternbrooker Weg 70\n24105 Kiel\nhttps://piratenfraktion-sh.de";

function sendnewsletter($subject,$body,$receiverMail,$receiverName){
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

	
	$hash=md5($receiverMail.'PeWoSaSalt');

	$imout = "\n <a href='http://www.ulikoenig.de/pewosa/regnews.php?out=".$hash."'>Mit einem Klick den Newsletter abbestellen</a>";
	
	//$textFooter = "\n \npresse@piratenfraktion-sh.de\nPiratenfraktion im Schleswig-Holsteinischen Landtag\n0431 988 1337\nDüsternbrooker Weg 70\n24105 Kiel\nhttps://piratenfraktion-sh.de";

	
	$htmlBody = "<!DOCTYPE html><html lang=\"de\"><head><meta charset=\"utf-8\"><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><meta name=\"robots\" content=\"noindex,nofollow\"><title>".$subject."</title><style type=\"text/css\">body{max-width:40em;font-family: \"Open Sans\",\"Sans Serif\";background-color: orange;} h1 {font-family:\"Bebas Neue\",\"Sans Serif\";} .container {background-color: white;padding:1em;}</style></head><body><div class=\"container\"><p></p><h3>".$subject."</h3><p>".strftime("%A, %e %B %G")."</p><p>".$body."</p></div>$imout<br><br>$htmlFooter</body></html>";



	$textBody = $imout." ".$subject."*\n\n".strftime("%A, %e %B %G")."\n\n"." ".$body;

	$mail->Subject = $subject;

	$mail->Body    = $htmlBody;
	$mail->AltBody = $textBody;

	if(!$mail->send()) {
 		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
		//echo 'Message has been sent';
	}

} // ENDE sendnewsletter


	
	If (isset($_POST['newsletter']))
		{
		$take=$_POST['newsletter'];

		If (isset($_POST['safe']) AND $take!='xxx')
			{
			//Eintrag anpassen, falls bereits vorhanden
			$htmlcode=mysql_real_escape_string ($_POST['c_body']);
			$change = "UPDATE newsletter Set 
			subject='".$_POST['c_subject']."',body='".$htmlcode."'					
			WHERE id='$take'";
			$update = mysql_query($change)or die("Speichern leider fehlgeschlagen.".mysql_error());			
			echo "<button type='button' class='btn btn-success'>Gespeichert</button>";
			}	
			
		If (isset($_POST['safe']) AND $take=='xxx')
			{	
			//Eintrag neu anlegen falls nicht vorhanden
			$htmlcode=mysql_real_escape_string ($_POST['c_body']);
			$send = "INSERT INTO newsletter (subject, body) VALUES 
			('".$_POST['c_subject']."', '".$htmlcode."')";
			$sent = mysql_query($send) or die("Newsletter anlegen leider fehlgeschlagen".mysql_error());			
			echo "<button type='button' class='btn btn-success'>Newsletter angelegt</button>";
			//Neu angelegte ID mit auf den Weg geben
			$query = "SELECT id FROM newsletter ORDER BY id DESC LIMIT 1";
			$checkdata = mysql_query($query);
			while($row = mysql_fetch_object($checkdata))
				{
				$take=$row->id;
				}
			}		

		
		If ($take!='xxx')
			{
			//Daten einlesen
			$query = "SELECT * FROM newsletter WHERE id=$take";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{	
				while($row = mysql_fetch_object($checkdata))
					{
					$subject=$row->subject;
					$body=$row->body;
					$sendstate=$row->sendstate;
					$senddate=$row->senddate;
					}
				}
			}
		If (isset($_POST['send']))
			{
			//Initialisieren von dem Array in dem wir alle angeschriebenen Mailadressen speichern
			$all_mails[0]="no@real.mail";
			//Wir gehen also alle Empfänger durch, die den Newsletter bekommen sollen
			$query =  "SELECT * FROM customerNewsletter WHERE active = 1";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{		
				$counter = 0;
				while($row = mysql_fetch_object($checkdata))
					{
					$counter++;
					/*if ($counter == 1) 
						{
						//Unlock Mutex
						flock($fp, LOCK_UN);
						fclose($fp);
						}*/
					//Jetzt schicken, aber nur wenn Mailadresse unique ist
					If (!in_array($row->email,$all_mails))
						{
						$name = $row->firstname." ".$row->lastname;
						sendnewsletter($subject,$body,$row->email,$name,$pdf);
						//Wir müssen alle benutzten Mails speichern, um zu vermeiden, dass wir an eine Mailadresse doppelt rausschicken
						array_push($all_mails,$email);
						}
					}
				}			
			
			//Wir setzen abschließend noch den Sendestatus auf 1 und das Sendedatum auf jetzt
			$senddate_db=date("Y-m-d H:i:s");
			$queryB = "UPDATE newsletter SET sendstate = '1', senddate = '$senddate_db' WHERE id = $take;";
			$send = mysql_query($queryB) or die("Fehler:".mysql_error());
			//Ganz Wichtig: 
			$sendstate='1';			
			}	
		}
	If (!isset($_POST['newsletter']) OR $take=='xxx')
		{
		//Kein Datensatz festgelegt oder neu? - Erstmal Standardangaben zeigen	
		$subject='';
		$body='';
		$sendstate='-1';
		$senddate='';
		}


	
	?>
	<div id="mainview" >
	<table border=0 class="centred"><tr><td align='left'>
	<?
	echo "<form action='newsletter.php' method='post' style='display:inline;'>";
	echo "<button Hspace='5' type='submit' class='btn btn-info' title='Zur Übersicht' name='' value=''><span class='glyphicon glyphicon-share' aria-hidden='true'></span> Zurück zur Übersicht</button></form></td><td  colspan=2 bgcolor='#99ccff' align='right'></form>";						
	echo "</td><td width='20'></td><td align='right'>";
			
	If ($sendstate==-1)
		{$disabled='';}
	else
		{$disabled='disabled';}	
					
	echo "<form action='newsletter_detail.php' method='post'>";	
	?>
	</table><br><table border=0 class="centred">		
	<tr height=20>
	<th class='cell' colspan=2 bgcolor='#cccccc'>Titel/Betreff</th>
	</tr><tr>
	<?		
	echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
	echo "<input type='text' class='form-control' $disabled name='c_subject' value='$subject' placeholder='Betreffzeile der Newsletter-Mail' size='150'>";
	echo "</td>";					
	?>	
	</tr><tr>
	<th class='cell' colspan=1 bgcolor='#cccccc'>HTML Code</th>
	<th class='cell' colspan=1 bgcolor='#cccccc'>Und so sieht es aus</th>		
	</tr><tr>
	<?
	echo "<td class='cell' colspan=1 bgcolor='#99ccff'>";
	echo "<textarea class='form-control' $disabled name='c_body' value='' placeholder='Inhalt des Newsletters' rows='20'>$body</textarea></td>";
	echo "<td class='cell' colspan=1 bgcolor='#99ccff'>";
	echo "<span class='form-control' style='overflow: auto;height:430px;'>$imout $body</span></td>";
	?>


		
	</tr><tr>
	<th class='cell' colspan=1 bgcolor='#cccccc'>Sendedatum</th>
	<th class='cell' colspan=1 bgcolor='#cccccc'>Sendestatus</th>
	</tr><tr>
	<?
	If ($sendstate==-1){$d_show='Noch nicht versendet';$s_show='Muss noch versendet werden';}
	else 
	{
	$d_show = date("d.m.Y H:i",strtotime($senddate));
	$s_show='Ist raus';
	}
	echo "<td class='cell' colspan=1 bgcolor='#99ccff'>";
	echo "<input type='email' disabled class='form-control' name='c_senddate' value='$d_show' placeholder='' size='75'></td>";
	echo "<td class='cell' colspan=1 bgcolor='#99ccff'>";
	echo "<input type='text' disabled class='form-control' name='c_sendstate' value='$s_show' placeholder='' size='75'></td>";

	echo "</tr><tr><td colspan=42  align='right'>";
	echo "<INPUT type='hidden' id='newsletter' name='newsletter' value='$take'>";
	If ($sendstate==-1)
		{
		echo "<button type='submit' class='btn btn-primary' title='Abschicken' name='send' value='$take'><span class='glyphicon glyphicon-envelope' aria-hidden='true'></span> Abschicken</button> ";
		
	echo "<button type='submit' class='btn btn-primary' title='Speichern' name='safe' value='$take'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Speichern</button>";		
		}	

	echo "</form></td>";
	
	
	
	?>	

</tr></table>

</div>
<?	
include_once("footer.php");	
