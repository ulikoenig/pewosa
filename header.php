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
?> 
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

<link rel="stylesheet" href="paint.css">


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script
	src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>
body {
	padding-top: 50px;
}

.mainCol {
	width: 100%
}

.rightCol {
	width: 100%
}
</style>
</head>
<body>
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
echo "Angemeldet als <b>$loggedinusername</b><h1>$pagetitle</h1>";
?>
</div>
