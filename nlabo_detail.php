<?
$pagetitle='Benutzer Bearbeiten';
include_once("header.php");
if($loggedinadmin == "1" OR $loggedinpressagent == "1")
	{
	die('Sie sind nicht berechtigt diese Funktion zu nutzen.</a>');
}
if (version_compare(PHP_VERSION, '5.5.0') < 0) {
    include_once("password.php");
}
	
	If (isset($_POST['user']) OR isset($_GET['user']))
		{
		$take=$_POST['user'];
		If (isset($_GET['user'])){$take=$_GET['user'];}

		If (isset($_POST['safe']) AND $take!='xxx')
			{
			$change = "UPDATE customerNewsletter Set 
			firstname='".$_POST['c_firstname']."',lastname='".$_POST['c_lastname']."',
			email='".$_POST['c_email']."'		
			WHERE id='$take'";
			$update = mysql_query($change)or die("Speichern leider fehlgeschlagen.".mysql_error());			
			echo "<button type='button' class='btn btn-success'>Gespeichert</button>";
			}	
			
		If (isset($_POST['safe']) AND $take=='xxx')
			{
	
			//Eintrag neu anlegen falls nicht vorhanden
			$created_at = date('Y-m-d G:i:s');
			
	
			$send = "INSERT INTO customerNewsletter (firstname, lastname, email, active, createdate) VALUES 
			('".$_POST['c_firstname']."', '".$_POST['c_lastname']."', '".$_POST['c_email']."','1','".$created_at."')";
			$sent = mysql_query($send) or die("Kontakt anlegen leider fehlgeschlagen".mysql_error());			
			echo "<button type='button' class='btn btn-success'>Nutzer angelegt</button>";
			//Neu angelegte ID mit auf den Weg geben
			$query = "SELECT id FROM customerNewsletter ORDER BY id DESC LIMIT 1";
			$checkdata = mysql_query($query);
			while($row = mysql_fetch_object($checkdata))
				{
				$take=$row->id;
				}
			}		

		
		If ($take!='xxx')
			{
			//Daten einlesen
			$query = "SELECT * FROM customerNewsletter WHERE id=$take";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{	
				while($row = mysql_fetch_object($checkdata))
					{
					$firstname=$row->firstname;
					$lastname=$row->lastname;
					$email=$row->email;		
					$active=$row->active;
					}
				}
			}	
		}
	else
		{
		$take='xxx';
		}
	
	?>
	<div id="mainview" >

	<table border=0 class="centred"><tr><td align='left'>
	<?
	echo "<form action='nlabo_detail.php' method='post' style='display:inline;'>";
	echo "<button type='submit' class='btn btn-primary' title='Neuen Abonennten anlegen' name='cust' value='xxx'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Neuen Abonnenten anlegen</button></form>";
	echo " <form action='nlabo.php' method='post' style='display:inline;'>";
	echo "<button Hspace='5' type='submit' class='btn btn-info' title='Zur Übersicht' name='' value=''><span class='glyphicon glyphicon-share' aria-hidden='true'></span> Zurück zur Übersicht</button></form></td><td  colspan=2 bgcolor='#99ccff' align='right'></form>";						
	echo "</td><td width='20'></td><td align='right'></table><br>";
	
	echo "<div class='panel panel-default'>";
	echo "<div class='panel-heading'><Font size='4'>$firstname $lastname</Font></div>";
	echo "<div class='panel-body'>";

				
			echo "<form action='nlabo_detail.php' method='post'>";	
			?>
			<br><table border=0 class="centred">		
			<tr height=20>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Name</th>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Nachname</th>
			</tr><tr>
			<?		
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='text' class='form-control' name='c_firstname' value='$firstname' placeholder='Vorname' size='50'>";
			echo "</td>";				
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='text' class='form-control' name='c_lastname' value='$lastname' placeholder='Nachname' size='50'>";			
			echo "</td>";		
			?>	
			</tr><tr>
			<th class='cell' colspan=4 bgcolor='#cccccc'>Email</th>		
			</tr><tr>
			<?
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='email'required class='form-control' name='c_email' value='$email' placeholder='mail@mail.de' size='100'></td>";
			
				
				
			
			
			echo "<td colspan=42  align='right'>";
			echo "<INPUT type='hidden' id='user' name='user' value='$take'>";	
			echo "<button type='submit' class='btn btn-primary' title='Speichern' name='safe' value='$take'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Speichern</button></form>";			

			echo "</td>";
			
			
			
			?>	

		</tr></table></div></div>

	</div>
<?	
include_once("footer.php");	
