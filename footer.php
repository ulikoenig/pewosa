     <footer>
<?php 
$file = './lastSendEMail.txt';
$lastSendEMail = file_get_contents($file);
$lastSendEMailTime = new DateTime($lastSendEMail);
$PHPTime = new DateTime(date("d.m.Y H:i:s"));
$TimeOffsetSent = abs($lastSendEMailTime->format('U') - $PHPTime->format('U'));
//echo "<p>Letzter E-Mail-Versand vor ".$TimeOffsetSent." Sekunden (".$lastSendEMail.")</p>";
echo "<p>Letzter E-Mail-Versand vor ".$TimeOffsetSent." Sekunden</p>";
?>


		<br>
        <p>&copy; 2016 by Uli, Jan und Christian</p>
<?php

if ($loggedinadmin > "1"){
	//check config
	$query = "SELECT CONVERT_TZ( NOW( ) , '" . SQLSERVERTIMEZONE . "', '" . SQLLOCALTIMEZONE . "' ) AS `now`;";
	$checkdata = mysql_query ( $query )  or die ( "SQL Fehler Verteilerlisten:" . mysql_error ()."\n\n".$query );
	if (mysql_num_rows ( $checkdata ) >= 1) {

	while ( $row = mysql_fetch_object ( $checkdata ) ) {
		$counter ++;
		$SQLTime= new DateTime($row->now);
	}
	$PHPTime = new DateTime(date("d.m.Y H:i:s"));
	$TimeOffset = ($SQLTime->format('U') - $PHPTime->format('U'));
//	echo "<p>ZeitOffset PHP - SQL Server: ".$TimeOffset->format("%H:%I:%S")."</p>" ;
	$MaxTimeOffset = 15; //15 Sekunden
if (abs($TimeOffset) > $MaxTimeOffset) {
		echo "<h2 style='color: red;'>WARNUNG: Uhr von Webserver und SQL-Server laufen NICHT Synchron!</h1><p>Die Uhrzeit des Webservers weicht mehr als ".$MaxTimeOffset." Sekunden von der des SQL-Servers ab. Dies kann zu Fehlern bei dem Versand von Pressemittilungen führen.
Bitte überprüfen sie die Uhrzeit ihrere Zeitzoneneinstellungen und die Uhrzeiten von Web- und SQL-Server.</p>";
		echo "<p>SQL: ".$SQLTime->format("d.m.Y H:i:s")."</p>";
		echo "<p>PHP: ".date("d.m.Y H:i:s")."</p>";
		echo "Abweichung in Sekunden: " .abs($TimeOffset)  ."</p>";
	}
	}
}

?>
      </footer>
</div> <!--class="container"-->
</body>
</html>
