<?php
require 'lib/PHPMailerAutoload.php';

require ("UrlLinker.php");
header ( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
header ( "Cache-Control: post-check=0, pre-check=0", false );
header ( "Pragma: no-cache" );
header ( "Content-Type: text/plain; charset=utf-8" );

include_once ("connection.php");
include_once ("pdfcreate.php");
include_once ("functions.php");

define("VERBOSE", "TRUE");

$verbose = TRUE;
$time_start = microtime ( true );
function bcrypt_encode($email, $password, $rounds = '08') {
	$string = hash_hmac ( "CodePass", str_pad ( $password, strlen ( $password ) * 4, sha1 ( $email ), STR_PAD_BOTH ), SALT, true );
	$salt = substr ( str_shuffle ( './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 22 );
	return crypt ( $string, '$2a$' . $rounds . '$' . $salt );
}

/**
 * Gesendete PM in der Datenbank vermerken
 */
function setpmsending($pmid, $state) {
	// $senddate_db="CURTIME( )";
	$senddate_db = date ( "Y-m-d H:i:s" );
	// Sendestatus und Sendedatum eintragen
	$queryB = "UPDATE ".MYSQLDB.".`pressrelease` SET sendstate = $state, senddate = '$senddate_db' WHERE `pressrelease`.`id` = $pmid;";
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
		$email = 'presse@ulikoenig.de';
		$name = '';
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

