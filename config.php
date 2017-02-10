<?php
	
	define("MYSQLHOST", "mysql1067.servage.net"); //Server
	define("MYSQLUSER", "pewosappsh"); //Benutzername
	define("MYSQLPASS", "69qeigVHqMPP"); //Passwort
	define("MYSQLDB", "pewosappsh"); //Datenbank
	define("PRESSMAIL", "presse@piratenpartei-sh.de"); //Absende-Email-Adresse
	
	define("CHECKSSL", FALSE); //Soll Mailaccount auf SSL geprüft werden? TRUE/FALSE
	define("PDFCREATE", FALSE); //Soll ein PDF erzeugt und angehängt werden? VORSICHT: Erfordert Template und Anpassung
	
	define("PRESSSERVER", "mx1.srv-net.de"); //Presse-Server
	define("PRESSPORT", "587"); //Presse-Port	143 / 993
	define("PRESSUSER", "presse@piratenpartei-sh.de"); //Presse-Nutzername
	define("PRESSPASS", "tiep8WugquibVes"); //Presse-Passwort
	
	define("IMPRESSUM", "LINK"); //Link zum Impressum
	define("COMPANYNAME", "Piratenpartei Schleswig-Holstein"); //Unternehmensname
	define("TELEFONEMAIN", "+49 (0)431 55 68 69 74"); //Telefonnummer Allgm.
	define("FAX", "+49 (0)431 55 68 66 79"); //Telefonnummer FAX
	define("TELEFONEPRESS", "+49 431 55 68 66 71"); //Telefonnummer Presse
	define("COMPANYSTREET", "Ringstraße 58"); //Firmenstraße mit Nummer
	define("COMPANYCITY", "24103 Kiel"); //Firmensitz mit vorangehender PLZ
	define("COMPANYCITYNAME", "Kiel"); //Firmensitz mit vorangehender PLZ
	define("FACEBOOKLINK", "https://www.facebook.com/PiratenparteiSH"); //Adresse des Facebookprofils
	define("TWITTERLINK", "https://twitter.com/#!/PiratenparteiSH"); //Adresse des Twitterprofils
	define("INSTAGRAMMLINK", "noinstagramm"); //Adresse des Instagrammprofils
	define("YOUTUBELINK", "https://www.youtube.com/user/PiratenSH"); //Adresse des Youtubeprofils
	define("COMPANYWEB", "http://www.piratenpartei-sh.de/"); //Unternehmens-Webseite
	define("BASEURL", "http://piraten.pewosa.de"); //Pewosa-Webseite
	define("PRESSFORMAL", "Viele Grüße\n\n Dein Piratenteam"); //Abschlussformel für E-Mails
	define("QUITPRESS", "Einen Link zum Abbestellen findest Du in jeder Pressemitteilung selbst"); //Text für PM-Abbestellung
	
	define("PRESSELINK", "https://www.piratenpartei.de/presse/"); //Link zur Pressehauptseite
	
	define('FIREBASE_API_ACCESS_KEY', 'AIzaSyA7JQPRRlPhbCRJkq2VfidqWJXZ39vPB3s' );
	define('PUSH_INSTANZ', 'pewosappsh' ); //Eindeutiger bezeichner für diese Pewosa instanz


    date_default_timezone_set('Europe/Berlin');
    setlocale(LC_TIME, "de_DE");
    define("SQLSERVERTIMEZONE", "UTC"); //Zeitzone, die im SQL-Server eingestellt ist.
    define("SQLLOCALTIMEZONE", "MET"); //SQL-Zeitzone, mit der gearbeitet werden soll
    $defaultPressesprecherID = 2; 
?>
