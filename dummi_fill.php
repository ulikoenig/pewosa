<?php
$pagetitle= "Datenbank füllen";
include_once("header.php");


$max = 0;
$verteilerID = 8;

For ($i=1;$i<=$max;$i++){
$query = "INSERT INTO ".MYSQLDB.".`customer` ( `firstname` , `lastname` , `email` , `createdate` , `updatedate`) VALUES ( 'X$i', 'X', 'Test$i@bills-erben.de', CURRENT_TIMESTAMP , 'CURRENT_TIMESTAMP');";
$send = mysql_query($query) or die("Fehler:".mysql_error());
$newuserID = mysql_insert_id();

$query = "INSERT INTO ".MYSQLDB.".`customerdistribution` (`customer` , `distribution` ) VALUES ( '$newuserID', '$verteilerID' );";
$send = mysql_query($query) or die("Fehler:".mysql_error());
}


include_once("footer.php");
?>

