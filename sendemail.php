<?php
require 'lib/PHPMailerAutoload.php';
include_once ("connection.php");
include_once ("pdfcreate.php");
require ("UrlLinker.php");
header ( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
header ( "Cache-Control: post-check=0, pre-check=0", false );
header ( "Pragma: no-cache" );
header ( "Content-Type: text/plain; charset=utf-8" );

define("VERBOSE", "TRUE");

$verbose = TRUE;
$time_start = microtime ( true );
function bcrypt_encode($email, $password, $rounds = '08') {
	$string = hash_hmac ( "CodePass", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $email ), STR_PAD_BOTH ), SALT, true );
	$salt = substr ( str_shuffle ( './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 22 );
	return crypt ( $string, '$2a$' . $rounds . '$' . $salt );
}
function sendpm($subject, $body, $receiverMail, $receiverName, $attachment, $id, $hash) {
	$mail = new PHPMailer ();
	
	// $mail->SMTPDebug = 3; // Enable verbose debug output
	$mail->CharSet = 'UTF-8';
	
	$mail->isSMTP (); // Set mailer to use SMTP
	$mail->Host = 'zimap.bytemine.net'; // Specify main and backup SMTP servers
	$mail->SMTPAuth = true; // Enable SMTP authentication
	$mail->Username = 'presse@piratenfraktion-sh.de'; // SMTP username
	$mail->Password = 'umgrou9ngkdq'; // SMTP password
	$mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587; // TCP port to connect to
	
	$mail->setFrom ( 'presse@piratenfraktion-sh.de', 'Piratenfraktion SH: Pressestelle' );
	$mail->addAddress ( $receiverMail, $receiverName ); // Add a recipient
	                                                 // $mail->addAddress('ellen@example.com'); // Name is optional
	                                                 // $mail->addReplyTo('info@example.com', 'Information');
	                                                 // $mail->addCC('cc@example.com');
	                                                 // $mail->addBCC('bcc@example.com');
	                                                 
	// $mail->addAttachment('/var/tmp/file.tar.gz'); // Add attachments
	                                                 // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name
	
	$mail->isHTML ( false ); // Set email format to HTML
	include ("fontdata.php");
	
	setlocale ( LC_ALL, 'de_DE' );
	
	$htmlFooter = "<p style=\"font-size: 10px; line-height: 12px; color: rgb(33, 33, 33); margin-bottom: 10px;\"><a href=\"mailto:presse@piratenfraktion-sh.de\" style=\"color: rgb(71, 124, 204); text-decoration: none; display: inline;\">presse@piratenfraktion-sh.de</a><span style=\"color: #212121;\"></span></p><p style=\"font-size: 10px; line-height: 12px; margin-bottom: 10px;\"><span style=\"font-weight: bold; color: rgb(33, 33, 33); display: inline;\">Piratenfraktion im Schleswig-Holsteinischen Landtag</span><br><span style=\"color: rgb(33, 33, 33); display: inline;\">0431 988 1337</span><br/><span style=\"color: rgb(33, 33, 33); display: inline;\">Düsternbrooker Weg 70</span><br/><span style=\"color: rgb(33, 33, 33); display: inline;\">24105 Kiel</span><br/><a href=\"https://piratenfraktion-sh.de\" style=\"color: rgb(71, 124, 204); text-decoration: none; display: inline;\">https://piratenfraktion-sh.de</a></p><p style=\"font-size: 0px; line-height: 0; font-family: Helvetica,Arial,sans-serif;\"><a style=\"text-decoration: none; display: inline;\" href=\"https://twitter.com/FraktionSH\">$twitterlogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span><a style=\"text-decoration: none; display: inline;\" href=\"https://www.facebook.com/Piraten-im-Schleswig-Holsteinischen-Landtag-1709680989297612/\">$facebooklogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span><a style=\"text-decoration: none; display: inline;\" href=\"https://www.instagram.com/piratenfraktionsh/\">$instagramlogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span><a style=\"text-decoration: none; display: inline;\" href=\"https://www.youtube.com/user/PiratenFraktionSH\">$youtubelogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span></p>";
	
	// Wir verschlüsseln id und email, um uns gegen Missbrauch abzusichern
	If ($receiverMail != 'presse@piratenfraktion-sh.de' and $receiverMail != 'presseticker@ltsh.de') {
		//$hash = md5 ( $receiverMail . $customerid );
		//hash bleibt hash
	} else {
		// Sollen keinen echten Abmeldelink enthalten
		$hash = 'nocando';
	}
	$imout = "\n <a href='http://www.ulikoenig.de/pewosa/regcust.php?out=" . $hash . "'>Mit einem Klick die Pressemitteilungen abbestellen</a>";
	
	$textFooter = "\n-- \npresse@piratenfraktion-sh.de\nPiratenfraktion im Schleswig-Holsteinischen Landtag\n0431 988 1337\nDüsternbrooker Weg 70\n24105 Kiel\nhttps://piratenfraktion-sh.de";
	
	$htmlBody = "<!DOCTYPE html><html lang=\"de\"><head><meta charset=\"utf-8\"><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><meta name=\"robots\" content=\"noindex,nofollow\"><title>PM: " . $subject . "</title><style type=\"text/css\">body{max-width:40em;font-family: \"Open Sans\",\"Sans Serif\";background-color: orange;} h1 {font-family:\"Bebas Neue\",\"Sans Serif\"; -moz-hyphens: auto; -o-hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto; hyphens: auto; } .container {background-color: white;padding:1em;}</style></head><body><div class=\"container\">$logo<p>Pressemitteilung:</p><h3>" . $subject . "</h3><p>" . strftime ( "%A, %e. %B %G" ) . "</p><p>" . nl2br ( htmlEscapeAndLinkUrls ( $body ) ) . "</p></div>$imout<br><br>$htmlFooter</body></html>";
	
	$textBody = "P r e s s e m i t t e i l u n g :\n*" . $subject . "*\n\n" . strftime ( "%A, %e. %B %G" ) . "\n\n" . $body . $imout . $textFooter;
	
	$mail->Subject = "PM: " . $subject;
	
	// Presseticker presseticker@ltsh.de
	if ($receiverMail == "presseticker@bills-erben.de") {
		$mail->Subject = $subject . " #6Piraten";
		$mail->setFrom ( 'presse@piraten.ltsh.de', 'Piratenfraktion SH: Pressestelle' );
		$mail->addAddress ( 'presseticker@ltsh.de', 'Landtagsticker' );
		$mail->Host = '10.48.156.221';
		$mail->SMTPAuth = false;
		$mail->Port = 25;
	}
	
	$mail->Body = $htmlBody;
	$mail->AltBody = $textBody;
	
	if (! empty ( $attachment )) {
		$filename = strftime ( "%G-%m-%d" ) . "-piraten-pm-" . preg_replace ( '/[^A-Za-z0-9äüöÄÜÖß_\-]/', '_', $subject ) . ".pdf";
		$mail->AddStringAttachment ( $attachment, $filename, "base64", "application/pdf" );
	}
	
	if (! $mail->send ()) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
		// echo 'Message has been sent';
	}
	// echo $htmlBody;
} // ENDE sendpm

/**
 * Gesendete PM in der Datenbank vermerken
 */
function setpmsending($pmid, $state) {
	// $senddate_db="CURTIME( )";
	$senddate_db = date ( "Y-m-d H:i:s" );
	// Sendestatus und Sendedatum eintragen
	$queryB = "UPDATE `pewosa`.`pressrelease` SET sendstate = $state, senddate = '$senddate_db' WHERE `pressrelease`.`id` = $pmid;";
	$send = mysql_query ( $queryB ) or die ( "SQL Fehler :" . mysql_error ()."\n\n".$queryB );
}
function runtime() {
	return microtime ( true ) - $_SERVER ["REQUEST_TIME_FLOAT"];
}



If ($verbose) {
	echo "Versand der E-Mails vorbereiten...\n";
}
$stillsending = true;

define("LOCKFILENAME", "send.lock");

function updateLock($lockTime){
	$handle = fopen (LOCKFILENAME, "w");
	$clockout=date("Y-m-d H:i:s", time() + $lockTime);
	fwrite ($handle, $clockout);
	fclose ($handle);
}
/**
* Verhindert, dass mehrere Instanzen gleichzeitig arbeiten. MUTEX
*/
function getLock($lockTime){
	//Wir wollen verhindern, dass Parallelprozesse starten
	
	//Wird gearbeitet wird eine einzelne Instanz gestartet / läuft bereits eine, wird der Prozess abgebrochen
	$handle = fopen (LOCKFILENAME, "rb");
	If (filesize (LOCKFILENAME)>0)
		{
		$inhalt = fread ($handle, filesize (LOCKFILENAME));
		}
	else
		{
		$inhalt='';	
		}
	fclose ($handle);

	If ($inhalt!='')
		{
		If (VERBOSE) 
			{
			echo "Prozess läuft maximal bis $inhalt\n";
			}			
		//Hier testen wir auf toten Prozess
		$date1 = date ( "Y-m-d H:i:s" );
		$dateTimestampaktuell = strtotime ( $date1 );
		$dateTimestampdatei = strtotime ( $inhalt );	
		If ($dateTimestampaktuell > $dateTimestampdatei) 
			{
			//Instanz ist abgelaufen und wird löschen
			$handle = fopen(LOCKFILENAME, "w"); 
			fclose($handle);	
			If (VERBOSE) 
				{
				echo "Prozess wurde offensichtlich ungeplant beendet -  Prozess zurückgesetzt\n";
				}			
			}
		else
			{
			//Hier gehts normal weiter
			echo "Fehler: Es läuft bereits ein Prozess -> keine weitere Instanz zugelassen\n";
			exit ( - 1 );
			}
		}
		echo "Neuer Prozess gestartet\n";	
		updateLock($lockTime);
}

/**
 * PM Senden und sicherstellen, dass keine E-Mails doppelt raus gehen
 */
function sendPmAfterCheck($counter, $sendstate, $email, &$all_mails, $pmid, $subject, $body, $name, $pdf, $verbose, $distribution_id, $hash) {
	// Bereits gesendete Mails nicht noch mal senden
	//$sendstate = -1;
	if ($counter > $sendstate) {
		// Nur schicken wenn Mailadresse unique
		If (! in_array ( $email, $all_mails )) {
			// echo "sendpm(PM: $subject,$body,$email,$name,PDF);";
			setpmsending ( $pmid, $counter );
			sendpm ( $subject, $body, $email, $name, $pdf, $pmid, $hash );
			// Wir müssen alle benutzten Mails speichern, um zu vermeiden, dass wir an eine Mailadresse doppelt rausschicken
			array_push ( $all_mails, $email );
			If ($verbose) {
				echo "PM $pmid wurde an $email aus dem Verteiler/Empfänger $distribution_id verschickt...\n";
				//print_r ($all_mails);
			}
		}
	} else {
		If ($verbose) {
			echo "Resume: Überspringe $email in PM $pmid: Counter = $counter; Sendstate = $sendstate\n";
		}
	}
	updateLock(10);
}


getLock(10);

while ( $stillsending ) {
	
	$counter = 0;
	$counterPMFound = 0;
	$pmid = 0;
	$sendstate = 0;
	$query = "SELECT `pressrelease`.*, `users`.firstname, `users`.lastname, `users`.jobtitle, `users`.phone FROM `pressrelease`, `users` WHERE `pressrelease`.pressagentid = `users`.id AND senddate IS NOT NULL AND sendnow <> 2 AND senddate >0 AND ((sendstate = -1) OR (sendstate > 0)) AND senddate < CONVERT_TZ( NOW( ) , '" . SQLSERVERTIMEZONE . "', '" . SQLLOCALTIMEZONE . "' )  Order by id ASC LIMIT 1";
	$checkdata = mysql_query ( $query ) or die ( "SQL Fehler Dist:" . mysql_error ()."\n\n".$query );
	if (mysql_num_rows ( $checkdata ) >= 1) {
		while ( $row = mysql_fetch_object ( $checkdata ) ) {
			// Hier darf nur dazugezählt werden, was sendnow oder ein vergangenes senddate hat
			$date1 = date ( "Y-m-d H:i:s" );
			$date2 = $row->senddate;
			$dateTimestamp1 = strtotime ( $date1 );
			$dateTimestamp2 = strtotime ( $date2 );
			$sendstate = $row->sendstate;
			if ($sendstate > 0) {
				echo "Resume: Überspringe die ersten $sendstate Empfänger\n";
			}
			If ($dateTimestamp2 <= $dateTimestamp1) {
				$counterPMFound ++;
				echo "RowID " . $row->id . "\n";
				$pmid = $row->id;
				$subject = $row->subject;
				$body = $row->body . "\n\n" . $row->contact;
				$pdf = converttopdf ( $row->firstname, $row->lastname, $row->jobtitle, $row->phone, $email, $subject, $body );
			}
		}
	} else
	{
		$stillsending = false; // KEINE PMs mehr gefunden
		$pmid = 0;
		$counter = 0;
		$counterPMFound = 0;
		$sendstate = 0;
		unset ($date1);
		unset ($date2);
		unset ($dateTimestamp1);
		unset ($dateTimestamp2);
		unset ($subject);
		unset ($body);
		unset ($pdf);
		//Instanz freigeben
		$handle = fopen(LOCKFILENAME, "w"); 
		fclose($handle);	
		If ($verbose) 
			{
			echo "Prozess wurde beendet...\n";
			}		
		}
	
	If ($verbose) {
		echo "Habe $counterPMFound zu versendende PMs gefunden...\n";
	}
	
	// Initialisieren von dem Array in dem wir alle angeschriebenen Mailadressen speichern
	unset ($all_mails);
	$all_mails [0] = "no@real.mail";
	
	if ($pmid > 0) {
		// Checken: soll es an Verteilerlisten raus, soll es an Einzelempfänger raus
		$send_distribution = FALSE;
		$send_customer = FALSE;
		$query = "SELECT listID,customerID FROM pressreleaseconnection WHERE pressreleaseID = $pmid AND (listID IS NOT NULL OR customerID IS NOT NULL)";
		$checkdata = mysql_query ( $query );
		if (mysql_num_rows ( $checkdata ) >= 1) {
			while ( $row = mysql_fetch_object ( $checkdata ) ) {
				If (! empty ( $row->listID )) {
					$send_distribution = TRUE;
				}
				If (! empty ( $row->customerID )) {
					$send_customer = TRUE;
				}
			}
		}
		// Erstmal an Verteilerlisten schicken
		If ($send_distribution) {
			// Eingefügt AND `customer`.`deleted`!='1', damit nur aktive angeschrieben werden + !=2 weil wir ja nun unterscheiden unter selbstabgemeldet und von uns deaktiviert
			// Hoffe das klappt
			$query = "SELECT  `customer`.id,`customer`.hash,`customer`.email, `customer`.firstname, `customer`.lastname,`pressreleaseconnection`.listID FROM `pressrelease`, `pressreleaseconnection`, `customerdistribution`, `customer` WHERE `pressrelease`.`id` = $pmid and `pressreleaseconnection`.pressreleaseID = $pmid  AND `customerdistribution`.distribution = `pressreleaseconnection`.listID AND `customer`.id = `customerdistribution`.customer  AND `customer`.`deleted`!='1' AND `customer`.`deleted`!='2' AND `pressreleaseconnection`.`listID` IS NOT NULL GROUP BY `customer`.id ORDER BY `customer`.id";
			// echo "Abfrage 1: $query";
			$checkdata = mysql_query ( $query )  or die ( "SQL Fehler Verteilerlisten:" . mysql_error ()."\n\n".$query );
			if (mysql_num_rows ( $checkdata ) >= 1) {
				// $counter = 0;
				while ( $row = mysql_fetch_object ( $checkdata ) ) {
					$counter ++;
					$distribution_id = $row->listID;
					// echo "<p>EMail ".$row->email."</p>";
					$email = $row->email;
					$name = $row->firstname . " " . $row->lastname;
					$hash = $row->hash;
					sendPmAfterCheck ( $counter, $sendstate, $email, $all_mails, $pmid, $subject, $body, $name, $pdf, $verbose, $distribution_id, $hash );
				}
			}
		}
		If ($send_customer) {
			// Hier kommen die Einzelempfänger
			$query = "SELECT customerID FROM pressreleaseconnection WHERE pressreleaseID = $pmid AND customerID IS NOT NULL";
			$checkdata = mysql_query ( $query )  or die ( "SQL Fehler Einzelempfänger:" . mysql_error ()."\n\n".$query );
			if (mysql_num_rows ( $checkdata ) >= 1) {
				// $counter = 0;
				while ( $row = mysql_fetch_object ( $checkdata ) ) {
					$counter ++;
					// Ich hole mir zu jedem Einzelempfänger die Daten
					If (! empty ( $row->customerID )) {
						$query2 = "SELECT email,firstname,lastname,id,hash FROM customer WHERE id = $row->customerID AND deleted!=1 AND deleted!=2";
						$checkdata2 = mysql_query ( $query2 ) or die ( "SQL Fehler Einzelempfänger:" . mysql_error ()."\n\n".$query );
						if (mysql_num_rows ( $checkdata2 ) == 1) {
							while ( $row2 = mysql_fetch_object ( $checkdata2 ) ) {
								// echo "<p>EMail ".$row2->email."</p>";
								$email = $row2->email;
								$name = $row2->firstname . " " . $row2->lastname;
								$hash = $row2->hash;
							}
							sendPmAfterCheck ( $counter, $sendstate, $email, $all_mails, $pmid, $subject, $body, $name, $pdf, $verbose,$row->customerID, $hash );
						} 
					}
				}
			}
		}				
		// Mitteilung an die Presse
		$email = 'presse@piratenfraktion-sh.de';
		$name = 'Christian Lewin';
		sendpm ( $subject." - KontrollPM", $body, $email, $name, $pdf, '0', '0' );
		If ($verbose) {
			echo "PM an Presse als Kopie verschickt...\n";
		}
	
		// PM als gesendet markieren
		setpmsending ( $pmid, - 2 );
	}

	unset ( $all_mails );
}
If ($verbose) {
	echo "Ich bin hier fertig.\n";
}



$file = './lastSendEMail.txt';
$current = date("d.m.Y H:i:s");
// Schreibt den Inhalt in die Datei zurück
file_put_contents($file, $current);

?>

