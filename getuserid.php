<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: text/plain"); 
session_start ();
if (! isset ( $_SESSION ['userid'] )) {
	echo "-1";
} else {
	echo $_SESSION ['userid'];
}
?>
