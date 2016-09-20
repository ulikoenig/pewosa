<?php
session_start(); 
if (version_compare(PHP_VERSION, '5.5.0') < 0) {
    include_once("password.php");
}

include_once("config.php");


$pdo = new PDO("mysql:host=".$host.";dbname=".$dbname, $user, $pass);
 
if(isset($_GET['login'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];

	if (isset($_POST['username'])) 
		{ $username = $_POST['username'];
		} else 	if(isset($_GET['username'])) 	
		{ $username = $_GET['username'];
		};

	if (isset($_POST['password'])) 
		{ $password = $_POST['password'];
		} else 	if(isset($_GET['password'])) 	
		{ $password = $_GET['password'];
		};
		
	$statement = $pdo->prepare("SELECT * FROM users WHERE username = :username");
	$result = $statement->execute(array('username' => $username));
	$user = $statement->fetch();
	
		//Überprüfung des Passworts
	if ($user !== false && password_verify($password, $user['password']) && $user['deleted'] == 0) {
		$_SESSION['userid'] = $user['id'];
		header("Location: ./");
		echo('<meta http-equiv="refresh" content="0;url=./" />');
	} else {
		$errorMessage = "<Font size=3>E-Mail oder Passwort war ung&uuml;ltig<br></Font>";
	}
	
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

  <title>Pewosa - Login</title>	
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
 
<?php 
if(isset($errorMessage)) {
	echo $errorMessage;
}
?>


<table border=0 class="centred">
<tr><td class='cell' colspan=42 >
<h1>
Pewosa - DAS Pressetool
</h1>

</td></tr>

<tr><td class='cell' colspan=42 bgcolor='#99ccff'><Font size=3>
<form action="?login=1" method="post">
Benutzername:<br>
<input type="username" size="40" maxlength="250" name="username"></Font</td></tr><tr><td class='cell' colspan=42 bgcolor='#99ccff'>
<Font size=3> 
Dein Passwort:<br>
<input type="password" size="40"  maxlength="250" name="password"></Font></td></tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='right'>
 
<button type='submit' class='btn btn-primary' title='Speichern' name='new' value='1'> Abschicken </button></td></tr></form>


</td></tr>

<tr><td class='cell' colspan=42 ><Font size=3><br>
<a href='regcust.php'>Du möchtest unsere Pressemitteilungen bekommen?</a>
<br><br>
<a href='regnews.php'>Du möchtest unseren Newsletter bekommen?</a>
<br><br>
<a href='http://www.piratenfraktion-sh.de/impressum/'>Impressum</a>



</Font>
</td></tr>
</table>
</body>
</html>
