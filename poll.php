<?php
session_start(); 
if (version_compare(PHP_VERSION, '5.5.0') < 0) {
    include_once("password.php");
}

require 'lib/PHPMailerAutoload.php';
require("UrlLinker.php");
include_once ("connection.php");
include_once("config.php");


	If (isset($_POST['poll']))
		{
		//Es wird nichts gespeichert, wenn alle Werte auf 1 sind oder die ip schon im System ist
		$onlyones=TRUE;			
		$radios=$_POST['radios'];If ($radios!=1){$onlyones=FALSE;}
		$radios2=$_POST['radios2'];If ($radios2!=1){$onlyones=FALSE;}
		$radios3=$_POST['radios3'];If ($radios3!=1){$onlyones=FALSE;}
		$radios4=$_POST['radios4'];If ($radios4!=1){$onlyones=FALSE;}
		$radios5=$_POST['radios5'];If ($radios5!=1){$onlyones=FALSE;}
		$radios6=$_POST['radios6'];If ($radios6!=1){$onlyones=FALSE;}
		$radios7=$_POST['radios7'];If ($radios7!=1){$onlyones=FALSE;}
		$radios8=$_POST['radios8'];If ($radios8!=1){$onlyones=FALSE;}
		$radios9=$_POST['radios9'];If ($radios9!=1){$onlyones=FALSE;}
		$radios10=$_POST['radios10'];If ($radios10!=1){$onlyones=FALSE;}
		$radios11=$_POST['radios11'];If ($radios11!=1){$onlyones=FALSE;}
		$radios12=$_POST['radios12'];If ($radios12!=1){$onlyones=FALSE;}
		$textout='Es tut uns leid, hier ist wohl ein Fehler passiert.';
		If (!$onlyones)
			{
			$newip=TRUE;
			//Ip ermitteln
			if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
				{
				$ip = $_SERVER['REMOTE_ADDR'];

				}

			else 
				{
	
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

				}

			//Ist die IP schon im system?
			$ip=md5($ip);
			$ip = substr($ip, 0, -2);
			$query = "SELECT ip FROM poll WHERE ip='$ip'";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{
				$newip=FALSE;
				$textout='Es tut uns leid, hier ist wohl ein Fehler passiert.';
				}
			//Erst jetzt eintragen
			If ($newip)
				{
				$createdate= date('Y-m-d G:i:s');
				$send = "INSERT INTO poll (ip, radios, radios2, radios3, radios4, radios5, radios6, radios7, radios8, radios9, radios10, radios11, radios12, createdate) VALUES('".$ip."', '".$radios."', '".$radios2."', '".$radios3."', '".$radios4."', '".$radios5."', '".$radios6."', '".$radios7."', '".$radios8."', '".$radios9."', '".$radios10."', '".$radios11."', '".$radios12."', '".$createdate."')";
				$sent = mysql_query($send) or die("Umfrage eintragen leider fehlgeschlagen".mysql_error());
				$textout='Vielen Dank für die Teilnahme!';				
				}			
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

  <title>Umfrage</title>	
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


<br>

	<div id="mainview">
	
	<?

	If (!isset($_POST['poll']))
		{

			//If ($double){echo "Fehler: Du hast bereits teilgenommen...";}
			?>

			<br>
			<br><table border=0 class="centred" width='60%'>
		
			<tr><td class='cell' colspan=42 >
			<h1>
			Umfrage: Welche Themen sind Dir wichtig?
			</h1></td></tr>

			<tr><td class='cell' colspan=42 bgcolor='#99ccff'><Font size=3>
			Datenschutz ist uns PIRATEN besonders wichtig, deshalb musst Du uns für die Umfrage keine Daten von dir verraten.<br>
			Wir speichern ausschließich Dein Abstimmungsergebnis und einen gekürzten Hashwert Deiner IP (um Doppelabstimmungen zu vermeiden).
			Wir versprechen, Deine Daten nicht an Dritte weiterzugeben. Unsere Server stehen in Deutschland.
			</Font></td></tr>
		
			<tr><form action='poll.php' method='post'>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Transparenz</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-0">
			      <input name="radios" id="radios-0" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-1">
			      <input name="radios" id="radios-1" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-2">
			      <input name="radios" id="radios-2" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-3">
			      <input name="radios" id="radios-3" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-4">
			      <input name="radios" id="radios-4" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr> 
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Bürgerbeteiligung</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-5">
			      <input name="radios2" id="radios-5" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-6">
			      <input name="radios2" id="radios-6" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-7">
			      <input name="radios2" id="radios-7" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-8">
			      <input name="radios2" id="radios-8" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-9">
			      <input name="radios2" id="radios-9" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Datenschutz</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-10">
			      <input name="radios3" id="radios-10" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-11">
			      <input name="radios3" id="radios-11" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-12">
			      <input name="radios3" id="radios-12" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-13">
			      <input name="radios3" id="radios-13" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-14">
			      <input name="radios3" id="radios-14" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Tierschutz</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-15">
			      <input name="radios4" id="radios-15" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-16">
			      <input name="radios4" id="radios-16" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-17">
			      <input name="radios4" id="radios-17" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-18">
			      <input name="radios4" id="radios-18" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-19">
			      <input name="radios4" id="radios-19" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Umweltschutz</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-20">
			      <input name="radios5" id="radios-20" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-21">
			      <input name="radios5" id="radios-21" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-22">
			      <input name="radios5" id="radios-22" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-23">
			      <input name="radios5" id="radios-23" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-24">
			      <input name="radios5" id="radios-24" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Bedingungsloses Grundeinkommen</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-25">
			      <input name="radios6" id="radios-25" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-26">
			      <input name="radios6" id="radios-26" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-27">
			      <input name="radios6" id="radios-27" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-28">
			      <input name="radios6" id="radios-28" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-29">
			      <input name="radios6" id="radios-29" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Energiewende</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-30">
			      <input name="radios7" id="radios-30" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-31">
			      <input name="radios7" id="radios-31" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-32">
			      <input name="radios7" id="radios-32" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-33">
			      <input name="radios7" id="radios-33" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-34">
			      <input name="radios7" id="radios-34" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Legalisierung von Drogen</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-35">
			      <input name="radios8" id="radios-35" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-36">
			      <input name="radios8" id="radios-36" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-37">
			      <input name="radios8" id="radios-37" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-38">
			      <input name="radios8" id="radios-38" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-39">
			      <input name="radios8" id="radios-39" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Steuern</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-40">
			      <input name="radios9" id="radios-40" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-41">
			      <input name="radios9" id="radios-41" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-42">
			      <input name="radios9" id="radios-42" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-43">
			      <input name="radios9" id="radios-43" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-44">
			      <input name="radios9" id="radios-44" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Sozialabgaben/Rente<b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-45">
			      <input name="radios10" id="radios-45" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-46">
			      <input name="radios10" id="radios-46" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-47">
			      <input name="radios10" id="radios-47" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-48">
			      <input name="radios10" id="radios-48" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-49">
			      <input name="radios10" id="radios-49" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Flüchtlinge</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-50">
			      <input name="radios11" id="radios-50" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-51">
			      <input name="radios11" id="radios-51" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-52">
			      <input name="radios11" id="radios-52" value="3" type="radio">
			      durchschnittlich
			    </label>

			    <label for="radios-53">
			      <input name="radios11" id="radios-53" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-54">
			      <input name="radios11" id="radios-54" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>
			</tr><tr><td class='cell' colspan=42 bgcolor='#99ccff' align='center'></td></tr>

			<tr>
			<td class='cell' colspan=1 bgcolor='#cccccc' width='20%'><Font size=4><b>Bildung</b></Font></td><td width='80%' class='cell' colspan=42 bgcolor='#cccccc'><Font size=3>
			<fieldset>
			    <label for="radios-55">
			      <input name="radios12" id="radios-55" value="1" checked="checked" type="radio">
			      gar nicht
			    </label>

			    <label for="radios-56">
			      <input name="radios12" id="radios-56" value="2" type="radio">
			      nur etwas
			    </label>

			    <label for="radios-57">
			      <input name="radios12" id="radios-57" value="3" type="radio">
			      durchschnittlich
			    </label>			

			    <label for="radios-58">
			      <input name="radios12" id="radios-58" value="4" type="radio">
			      schon sehr
			    </label>

			    <label for="radios-59">
			      <input name="radios12" id="radios-59" value="5" type="radio">
			      ganz besonders
			    </label>		
			</fieldset>
			</Font></td>


			</tr><tr><td colspan='42' align='right'>
			<button type='submit' class='btn btn-primary' title='Speichern' name='poll' value='1'>
			<span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Meinung senden</button></td></tr></form>
			<tr><td class='cell' colspan=42 >	
			<a href='http://piratenfraktion-sh.de'><Font Size=3>Zurück zur Hauptseite</Font></a>
			<br><br>
			<a href='regnews.php'><Font Size=3>Du möchtest unseren Newsletter bekommen?</Font></a>
			</td></tr>
			</table>


		<?
		
		}	

	else
		{
		?>			
		<br>
		<br><table border="0" class="centred">
	
		<tr><td class='cell' colspan=42>
		<h1>
		<?
		echo "$textout";
		//Grafische Ausgabe des Ergebnisses
		$radios=0;
		$radios2=0;
		$radios3=0;
		$radios4=0;
		$radios5=0;
		$radios6=0;
		$radios7=0;
		$radios8=0;
		$radios9=0;
		$radios10=0;
		$radios11=0;
		$radios12=0;
		$counter=0;

		$query = "SELECT * FROM poll";
		$checkdata = mysql_query($query);
		if(mysql_num_rows($checkdata)>=1)
			{
			while($row = mysql_fetch_object($checkdata))
				{
				$counter++;
				$dummy=$row->radios;			
				$radios=$radios+$dummy;
				$dummy=$row->radios2;			
				$radios2=$radios2+$dummy;
				$dummy=$row->radios3;			
				$radios3=$radios3+$dummy;
				$dummy=$row->radios4;			
				$radios4=$radios4+$dummy;
				$dummy=$row->radios5;			
				$radios5=$radios5+$dummy;
				$dummy=$row->radios6;			
				$radios6=$radios6+$dummy;
				$dummy=$row->radios7;			
				$radios7=$radios7+$dummy;
				$dummy=$row->radios8;			
				$radios8=$radios8+$dummy;
				$dummy=$row->radios9;			
				$radios9=$radios9+$dummy;
				$dummy=$row->radios10;			
				$radios10=$radios10+$dummy;
				$dummy=$row->radios11;			
				$radios11=$radios11+$dummy;
				$dummy=$row->radios12;			
				$radios12=$radios12+$dummy;
				}	
			}

			//Zahlen aufbereiten
			$radios=round($radios/$counter)*20;
			$radios2=round($radios2/$counter)*20;
			$radios3=round($radios3/$counter)*20;
			$radios4=round($radios4/$counter)*20;
			$radios5=round($radios5/$counter)*20;
			$radios6=round($radios6/$counter)*20;
			$radios7=round($radios7/$counter)*20;
			$radios8=round($radios8/$counter)*20;
			$radios9=round($radios9/$counter)*20;
			$radios10=round($radios10/$counter)*20;
			$radios11=round($radios11/$counter)*20;
			$radios12=round($radios12/$counter)*20;
			
			?>
			<br>
			<div id="mainview">
			<?
			echo "<Font size=4>Bisher haben $counter Teilnehmer mitgemacht</Font>";
			$realvar=array('0',$radios,$radios2,$radios3,$radios4,$radios5,$radios6,$radios7,$radios8,$radios9,$radios10,$radios11,$radios12);
			$realname=array('0','Transparenz','Bürgerbeteiligung','Datenschutz','Tierschutz','Umweltschutz','Bedingungsloses Grundeinkommen','Energiewende','Legalisierung von Drogen','Steuern','Sozialabgaben/Rente','Flüchtlinge','Bildung');
			For ($i=1;$i<=12;$i++)
				{
				
				$titlegiver='Interessiert die Abstimmer gar nicht';$realprogresscolor='progress-bar progress-bar-info progress-bar-striped';			
				If ($realvar[$i]>20){$titlegiver='Interessiert die Abstimmer etwas';$realprogresscolor='progress-bar progress-bar-success progress-bar-striped';}
				If ($realvar[$i]>40){$titlegiver='Interessiert die Abstimmer durchschnittlich';$realprogresscolor='progress-bar progress-bar-success progress-bar-striped';}
				If ($realvar[$i]>60){$titlegiver='Interessiert die Abstimmer sehr';$realprogresscolor='progress-bar progress-bar-warning progress-bar-striped';}
				If ($realvar[$i]>80){$titlegiver='Interessiert die Abstimmer ganz besonders';$realprogresscolor='progress-bar progress-bar-danger progress-bar-striped';}
				$styler="width: ".$realvar[$i]."%";
				echo "<br><Font size=4><b>$i. $realname[$i] </Font><Font size=2>($titlegiver)</Font></b>";
				?>

				<div class="progress">
				<?
				echo "<div class='$realprogresscolor' role='progressbar' aria-valuenow='$realvar[$i]' aria-valuemin='0' aria-valuemax='100' style='$styler'>";				
				?>			
				</div></div>
				<?
				}				
				?>
			</div>
		<?
		
		}
?>
	</div>


</body>
</html>
