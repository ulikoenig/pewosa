
<?
function sendactivation($subject,$body,$receiverMail,$receiverName){
	$mail = new PHPMailer;
	$mail->CharSet = 'UTF-8';

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = PRESSSERVER;  							// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = PRESSUSER;                 		// SMTP username
	$mail->Password = PRESSPASS;                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = PRESSPORT;                                    // TCP port to connect to
	If (!CHECKSSL)
		{
		$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true	));
		}

	$mail->setFrom(PRESSMAIL, COMPANYNAME);
	$mail->addAddress($receiverMail, $receiverName);     // Add a recipient

	$mail->isHTML(false);                                  // Set email format to HTML
	include("fontdata.php");

	setlocale (LC_ALL, 'de_DE');

	$htmlFooter = "<p style=\"font-size: 10px; line-height: 12px; color: rgb(33, 33, 33); margin-bottom: 10px;\"><a href=\"mailto:".PRESSMAIL."\" style=\"color: rgb(71, 124, 204); text-decoration: none; display: inline;\">";
	$htmlFooter .= PRESSMAIL."</a><span style=\"color: #212121;\"></span></p><p style=\"font-size: 10px; line-height: 12px; margin-bottom: 10px;\">";
	$htmlFooter .= "<span style=\"font-weight: bold; color: rgb(33, 33, 33); display: inline;\">".COMPANYNAME."</span><br><span style=\"color: rgb(33, 33, 33); display: inline;\">";
	$htmlFooter .= TELEFONEPRESS."</span><br/><span style=\"color: rgb(33, 33, 33); display: inline;\">".COMPANYSTREET."</span><br/><span style=\"color: rgb(33, 33, 33); display: inline;\">";
	$htmlFooter .= COMPANYCITY."</span><br/><a href=\"".COMPANYWEB."\" style=\"color: rgb(71, 124, 204); text-decoration: none; display: inline;\">";
	$htmlFooter .= COMPANYWEB."</a></p><p style=\"font-size: 0px; line-height: 0; font-family: Helvetica,Arial,sans-serif;\">";
	If (TWITTERLINK!='notwitter') 
		{
		$htmlFooter .= "<a style=\"text-decoration: none; display: inline;\" href=\"".TWITTERLINK."\">$twitterlogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span>";
		}
	If (FACEBOOKLINK!='nofacebook') 
		{		
		$htmlFooter .= "<a style=\"text-decoration: none; display: inline;\" href=\"".FACEBOOKLINK."\">$facebooklogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span>";
		}
	If (INSTAGRAMMLINK!='noinstagramm') 
		{		
		$htmlFooter .= "<a style=\"text-decoration: none; display: inline;\" href=\"".INSTAGRAMMLINK."\">$instagramlogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span>";
		}
	If (YOUTUBELINK!='noyoutube') 
		{	
		$htmlFooter .= "<a style=\"text-decoration: none; display: inline;\" href=\"".YOUTUBELINK."\">$youtubelogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span>";
		}
	$htmlFooter .= "</p>";
		
	$textFooter = "\n-- \n".PRESSMAIL."\n".COMPANYNAME."\n".TELEFONEPRESS."\n".COMPANYSTREET."\n".COMPANYCITY."\n".COMPANYWEB;


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

} // ENDE sendactivation




function sendpm($subject, $body, $receiverMail, $receiverName, $attachment, $id, $hash) {
	$mail = new PHPMailer ();
	
	// $mail->SMTPDebug = 3; // Enable verbose debug output
	$mail->CharSet = 'UTF-8';
	
	$mail->isSMTP (); // Set mailer to use SMTP
	$mail->Host = PRESSSERVER; // Specify main and backup SMTP servers
	$mail->SMTPAuth = true; // Enable SMTP authentication
	$mail->Username = PRESSUSER; // SMTP username
	$mail->Password = PRESSPASS; // SMTP password
	$mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
	$mail->Port = PRESSPORT; // TCP port to connect to
	
	If (!CHECKSSL)
		{
		$mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true	));
		}
	
	$mail->setFrom ( PRESSMAIL, COMPANYNAME );
	$mail->addAddress ( $receiverMail, $receiverName ); // Add a recipient

	$mail->isHTML ( false ); // Set email format to HTML
	include ("fontdata.php");
	
	setlocale ( LC_ALL, 'de_DE' );
	
	$htmlFooter = "<p style=\"font-size: 10px; line-height: 12px; color: rgb(33, 33, 33); margin-bottom: 10px;\"><a href=\"mailto:".PRESSMAIL."\" style=\"color: rgb(71, 124, 204); text-decoration: none; display: inline;\">";
	$htmlFooter .= PRESSMAIL."</a><span style=\"color: #212121;\"></span></p><p style=\"font-size: 10px; line-height: 12px; margin-bottom: 10px;\">";
	$htmlFooter .= "<span style=\"font-weight: bold; color: rgb(33, 33, 33); display: inline;\">".COMPANYNAME."</span><br><span style=\"color: rgb(33, 33, 33); display: inline;\">";
	$htmlFooter .= TELEFONEPRESS."</span><br/><span style=\"color: rgb(33, 33, 33); display: inline;\">".COMPANYSTREET."</span><br/><span style=\"color: rgb(33, 33, 33); display: inline;\">";
	$htmlFooter .= COMPANYCITY."</span><br/><a href=\"".COMPANYWEB."\" style=\"color: rgb(71, 124, 204); text-decoration: none; display: inline;\">";
	$htmlFooter .= COMPANYWEB."</a></p><p style=\"font-size: 0px; line-height: 0; font-family: Helvetica,Arial,sans-serif;\">";
	If (TWITTERLINK!='notwitter') 
		{
		$htmlFooter .= "<a style=\"text-decoration: none; display: inline;\" href=\"".TWITTERLINK."\">$twitterlogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span>";
		}
	If (FACEBOOKLINK!='nofacebook') 
		{		
		$htmlFooter .= "<a style=\"text-decoration: none; display: inline;\" href=\"".FACEBOOKLINK."\">$facebooklogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span>";
		}
	If (INSTAGRAMMLINK!='noinstagramm') 
		{		
		$htmlFooter .= "<a style=\"text-decoration: none; display: inline;\" href=\"".INSTAGRAMMLINK."\">$instagramlogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span>";
		}
	If (YOUTUBELINK!='noyoutube') 
		{	
		$htmlFooter .= "<a style=\"text-decoration: none; display: inline;\" href=\"".YOUTUBELINK."\">$youtubelogo</a><span style=\"white-space: nowrap; display: inline;\">$spacerlogo</span>";
		}
	$htmlFooter .= "</p>";
	
	// Wir verschlüsseln id und email, um uns gegen Missbrauch abzusichern
	If ($receiverMail == PRESSMAIL) {
		// Sollen keinen echten Abmeldelink enthalten
		$hash = 'nocando';
	}
	$imout = "\n <a href='".BASEURL."/regcust.php?out=" . $hash . "'>Mit einem Klick die Pressemitteilungen abbestellen</a>";
	$imouttxt = "\nMit einem Klick die Pressemitteilungen abbestellen \n".BASEURL."/regcust.php?out=" . $hash;
	
	$textFooter = "\n-- \n".PRESSMAIL."\n".COMPANYNAME."\n".TELEFONEPRESS."\n".COMPANYSTREET."\n".COMPANYCITY."\n".COMPANYWEB;
	
	$htmlBody = "<!DOCTYPE html><html lang=\"de\"><head><meta charset=\"utf-8\"><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><meta name=\"robots\" content=\"noindex,nofollow\"><title>PM: " . $subject . "</title><style type=\"text/css\">body{max-width:40em;font-family: \"Open Sans\",\"Sans Serif\";background-color: orange;} h1 {font-family:\"Bebas Neue\",\"Sans Serif\"; -moz-hyphens: auto; -o-hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto; hyphens: auto; } .container {background-color: white;padding:1em;}</style></head><body><div class=\"container\">$logo<p>Pressemitteilung:</p><h3>" . $subject . "</h3><p>" . strftime ( "%A, %e. %B %G" ) . "</p><p>" . nl2br ( htmlEscapeAndLinkUrls ( $body ) ) . "</p></div>$imout<br><br>$htmlFooter</body></html>";
	
	$textBody = "P r e s s e m i t t e i l u n g :\n*" . $subject . "*\n\n" . strftime ( "%A, %e. %B %G" ) . "\n\n" . $body . $imouttxt . $textFooter;
	
	$mail->Subject = "PM: " . $subject;
	$mail->Body = $htmlBody;
	$mail->AltBody = $textBody;
	
	if (! empty ( $attachment ) AND PDFCREATE) {
		$filename = strftime ( "%G-%m-%d" ) . "-pm-" . preg_replace ( '/[^A-Za-z0-9äüöÄÜÖß_\-]/', '_', $subject ) . ".pdf";
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



function registertext()
{
	echo "Datenschutz ist uns PIRATEN besonders wichtig, deshalb musst Du uns für eine Anmeldung für unseren Presseverteiler nicht alle Daten verraten.<br>Eine E-Mail-Adresse und Deinen Namen brauchen wir aber schon. Teile uns ggf. auch gern in den Notizen mit, warum Du nur in bestimmte Verteiler möchtest.<br> Wir versprechen, Deine Daten nicht an Dritte weiterzugeben. Unsere Server stehen in Deutschland.";

}

function registertextnewsletter()
{
	echo "Datenschutz ist uns PIRATEN besonders wichtig, deshalb musst Du uns Deinen Namen natürlich nicht verraten, wenn Du unseren Newsletter abonnieren möchtest.<br> Eine E-Mail-Adresse brauchen wir aber schon. Wir versprechen, Deine Daten nicht an Dritte weiterzugeben. Unsere Server stehen in Deutschland.";
}


?>