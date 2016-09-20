<?php
include_once("config.php");
    
    $connection = mysql_connect("$host",
        "$user",
        "$pass");
    mysql_select_db("$dbname", $connection);

//Return all queries in UTF-8
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $connection);
//https://phpmyadmin-srv1.servage.net/phpMyAdmin-3.0.1/
?>
