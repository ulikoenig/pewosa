<?php
if (file_exists("config.php") && is_readable("config.php")) {
	include_once("config.php");
} else {
	//header("Location: ./index.php?Error=CONFIGMISSING",TRUE,307);
	//echo "Fehler: config.php nicht gefunden";
	//die;  
}
    

function catchSQLInitError($mysql_error){
	header("Location: ./index.php?Error=SQLERROR&Msg=$mysql_error",TRUE,307);
	die("Fehler beim öffnen der Datenbank: ".$mysql_error. " Query: ".$query);
}

error_reporting(E_ERROR);
    $connection = mysql_connect(MYSQLHOST,
        MYSQLUSER,
        MYSQLPASS);
    mysql_select_db(MYSQLDB, $connection)  or catchSQLInitError(mysql_error());
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
error_reporting(E_ERROR | E_WARNING );


//Return all queries in UTF-8
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $connection);
//https://phpmyadmin-srv1.servage.net/phpMyAdmin-3.0.1/
?>
