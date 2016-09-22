<?php
    $host = "mysql.server.net";
    $user = "pewosa";
    $pass = "123456789";
    $dbname = "pewosa";
    date_default_timezone_set('Europe/Berlin');
    setlocale(LC_TIME, "de_DE");
    define("SQLSERVERTIMEZONE", "UTC"); //Zeitzone, die im SQL-Server eingestellt ist.
    define("SQLLOCALTIMEZONE", "MET"); //SQL-Zeitzone, mit der gearbeitet werden soll
    $defaultPressesprecherID = 2; 
?>
