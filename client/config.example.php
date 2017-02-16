<?php
	
	define("MYSQLHOST", "sql.server"); //Server
	define("MYSQLUSER", "benutzername"); //Benutzername
	define("MYSQLPASS", "1234567"); //Passwort
	define("MYSQLDB", "pewosa-db-name"); //Datenbank
	define("PRESSMAIL", "absender@meinedomain.de"); //Absende-Email-Adresse
	
	define("CHECKSSL", FALSE); //Soll Mailaccount auf SSL geprüft werden? TRUE/FALSE
	define("PDFCREATE", FALSE); //Soll ein PDF erzeugt und angehängt werden? VORSICHT: Erfordert Template und Anpassung
	
	define("PRESSSERVER", "mail.server"); //Presse-Server
	define("PRESSPORT", "587"); //Presse-Port	143 / 993
	define("PRESSUSER", "email-benutzer-name"); //Presse-Nutzername
	define("PRESSPASS", "1234567"); //Presse-Passwort
	
	define("IMPRESSUM", "http://meinedomain.de/impressum"); //Link zum Impressum
	define("COMPANYNAME", "Firmenname"); //Unternehmensname
	define("TELEFONEMAIN", "+49 (0)800 33 0 1000"); //Telefonnummer Allgm.
	define("FAX", "+49 (0)800 33 0 1000"); //Telefonnummer FAX
	define("TELEFONEPRESS", "+49 800 33 0 1000"); //Telefonnummer Presse
	define("COMPANYSTREET", "Musterstr. 23"); //Firmenstraße mit Nummer
	define("COMPANYCITY", "24103 Kiel"); //Firmensitz mit vorangehender PLZ
	define("COMPANYCITYNAME", "Kiel"); //Firmensitz mit vorangehender PLZ
	define("FACEBOOKLINK", "https://www.facebook.com/Seite"); //Adresse des Facebookprofils
	define("TWITTERLINK", "https://twitter.com/Twitter"); //Adresse des Twitterprofils
	define("INSTAGRAMMLINK", "noinstagramm"); //Adresse des Instagrammprofils
	define("YOUTUBELINK", "https://www.youtube.com/user/Channel"); //Adresse des Youtubeprofils
	define("COMPANYWEB", "http://www.meinedomain.de/"); //Unternehmens-Webseite
	define("BASEURL", "http://url-zu-pewosa.de/installation"); //Pewosa-Webseite
	define("PRESSFORMAL", "Viele Grüße\n\n Dein Presseteam"); //Abschlussformel für E-Mails
	define("QUITPRESS", "Einen Link zum Abbestellen findest Du in jeder Pressemitteilung selbst"); //Text für PM-Abbestellung
	
	define("LOGOLINK", "http://meinedomain.de/logo.jpg"); //Link zum Logo der Firma
	
	define("PRESSELINK", "https://meinedomain.de/presse/"); //Link zur Pressehauptseite
	
	define('FIREBASE_API_ACCESS_KEY', '1234567890ABCDEFGHIJK' );
	define('PUSH_INSTANZ', 'pewosa' ); //Eindeutiger bezeichner für diese Pewosa instanz


    date_default_timezone_set('Europe/Berlin');
    setlocale(LC_TIME, "de_DE");
    define("SQLSERVERTIMEZONE", "UTC"); //Zeitzone, die im SQL-Server eingestellt ist.
    define("SQLLOCALTIMEZONE", "MET"); //SQL-Zeitzone, mit der gearbeitet werden soll
    $defaultPressesprecherID = 2; 
?>
