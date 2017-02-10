<?
session_start ();
if (! isset ( $_SESSION ['userid'] )) {
//	die ( 'Bitte zuerst <a href="login.php">einloggen</a>' );
die ( "<meta http-equiv='refresh' content='0; url=login.php' />" );
}
include_once ("connection.php");

function die_nicely($msg) {
	echo "<div class=\"alert alert-danger\"><h1><strong>Fehler!</strong> $msg</h1></div>";
	include_once("footer.php");
	exit;
	die();
}



?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">

<!--	<link rel="stylesheet" type="text/css" href="paint.css"> -->
<?php
// Name des angemeldeten Nutzers laden
$take = $_SESSION ['userid'];
$query = "SELECT * FROM users WHERE id=$take";
$checkdata = mysql_query ( $query );
if (mysql_num_rows ( $checkdata ) >= 1) {
	while ( $row = mysql_fetch_object ( $checkdata ) ) {
		$loggedinuserid = $take;
		$loggedinusername = $row->firstname . " " . $row->lastname;
		$loggedinpressagent = $row->pressagent . " " . $row->pressagent;
		$loggedindistributor = $row->distributor . " " . $row->distributor;
		$loggedinadmin = $row->admin . " " . $row->admin;
		$loggedindeleted = $row->deleted . " " . $row->deleted;
	}
}

echo "<title>PeWoSa - $pagetitle</title>";
?><!-- Latest compiled and minified CSS --><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- Optional theme --><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="paint.css"><!-- jQuery (necessary for Bootstrap's JavaScript plugins) --><script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript --><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<?php
if ($useFirepad){  
	echo "<script src=\"https://www.gstatic.com/firebasejs/3.4.1/firebase.js\"></script>\n";
	echo "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.19.0/codemirror.js\"></script>\n";
	echo "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.19.0/codemirror.css\" />\n";
	echo "<link rel=\"stylesheet\" href=\"https://cdn.firebase.com/libs/firepad/1.4.0/firepad.css\" />\n";
	echo "<script src=\"https://cdn.firebase.com/libs/firepad/1.4.0/firepad.min.js\"></script>\n";
	echo "<script src=\"include/firepad-userlist.js\"></script>";
	echo "<link rel=\"stylesheet\" href=\"include/firepad-userlist.css\" />";
	echo "<style>#firepad-container { width: 100%; height: 30em; } .powered-by-firepad {display: none;}  #userlist { position: absolute; right: 0; top: 0; bottom: 0; height: auto; width: 175px; }</style>\n";
}
?>

<style>
.mainCol {
	width: 100%
}

.rightCol {
	width: 100%
}
<?php
//Menu ausblenden, wenn PeWoSa App aufruft
if($_SERVER['HTTP_USER_AGENT'] == "de.ulikoenig.pewosa/android"){
echo ".navbar {display:none}\n";
} else {
echo "body { padding-top: 50px; }";
}
?>
</style>
</head>
<?php
if ($useFirepad) {
	echo "<body onload=\"init()\">";
} else {
	echo "<body>";
}
?>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target="#navbar" aria-expanded="false"
					aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="messagelist.php">PeWoSa</a>
			</div>
<?php
$scriptName = $_SERVER ['SCRIPT_NAME'];
if (strpos ( $scriptName, 'messagelist.php' ) !== false) {
	$messagelistphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'distributions.php' ) !== false) {
	$distributionsphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'distribution_detail.php' ) !== false) {
	$distributionsphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'message.php' ) !== false) {
	$messagelistphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'customer.php' ) !== false) {
	$customerphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'customer_detail.php' ) !== false) {
	$customerphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'user.php' ) !== false) {
	$userphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'user_detail.php' ) !== false) {
	$userphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'newsletter.php' ) !== false) {
	$newsletterphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'newsletter_detail.php' ) !== false) {
	$newsletterphpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'nlabo_detail.php' ) !== false) {
	$nlabophpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'nlabo.php' ) !== false) {
	$nlabophpclass = "class=\"active\"";
} else if (strpos ( $scriptName, 'csvimport.php' ) !== false) {
	$csvimportphpclass = "class=\"active\"";
}
;
?>

        <div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
<?php
echo "            <li $messagelistphpclass><a href=\"messagelist.php\">PM Übersicht</a></li>";
echo "            <li $distributionsphpclass><a href=\"distributions.php\">Verteiler</a></li>";
echo "            <li $customerphpclass><a href=\"customer.php\">PM Empfänger</a></li>";
if ($loggedinadmin > "1" OR $loggedinpressagent > "1") {
	echo "            <li $newsletterphpclass><a href=\"newsletter.php\">Newsletter</a></li>";
	echo "            <li $nlabophpclass><a href=\"nlabo.php\">NL Empfänger</a></li>";
}


if ($loggedinadmin > "1") {
	echo "            <li $userphpclass><a href=\"user.php\">Nutzer</a></li>";
	echo "            <li $csvimportphpclass><a href=\"csvimport.php\">csv Import</a></li>";
}
?>

            <li><a href="logout.php">Logout</a></li>
				</ul>
			</div>
			<!--/.nav-collapse -->
		</div>
	</nav>
	<div class="container">
		<div class="page-header">
<?php
echo "<span class=\"noPrint\">Angemeldet als <b>$loggedinusername</b><h1>$pagetitle</h1></span>";


?>
</div>
