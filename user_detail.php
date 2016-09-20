<?
$pagetitle='Benutzer Bearbeiten';
include_once("header.php");
if($loggedinadmin < "1")
	{
	die('Sie sind nicht berechtigt diese Funktion zu nutzen.</a>');
}
if (version_compare(PHP_VERSION, '5.5.0') < 0) {
    include_once("password.php");
}
	
	If (isset($_POST['user']))
		{
		$take=$_POST['user'];

		If (isset($_POST['safe']) AND $take!='xxx')
			{
			//Eintrag anpassen, falls bereits vorhanden

			$password_raw=$_POST['c_password'];
			If ($password_raw!='')
				{
				//echo "Echtes pass $password_raw";
				$password=password_hash($password_raw, PASSWORD_DEFAULT);
				//echo "Hash des Passworts: $password";
				$change = "UPDATE users Set password='".$password."' WHERE id='$take'";	
				$update = mysql_query($change)or die("Speichern leider fehlgeschlagen.".mysql_error());					
				}
			$updated_at = date('Y-m-d G:i:s');	
			$change = "UPDATE users Set 
			firstname='".$_POST['c_firstname']."',lastname='".$_POST['c_lastname']."',
			jobtitle='".$_POST['c_jobtitle']."',phone='".$_POST['c_phone']."',
			cellphone='".$_POST['c_cellphone']."',email='".$_POST['c_email']."',
			username='".$_POST['c_username']."',distributor='".$_POST['c_distributor']."',			
			pressagent='".$_POST['c_pressagent']."',admin='".$_POST['c_admin']."',		
			updated_at='".$updated_at."'			
			WHERE id='$take'";
			$update = mysql_query($change)or die("Speichern leider fehlgeschlagen.".mysql_error());			
			//echo "Gespeichert";
			echo "<button type='button' class='btn btn-success'>Gespeichert</button>";
			}	
			
		If (isset($_POST['safe']) AND $take=='xxx')
			{	
			//Eintrag neu anlegen falls nicht vorhanden
			$created_at = date('Y-m-d G:i:s');
			$updated_at = date('Y-m-d G:i:s');
			
			$password_raw=$_POST['c_password'];
			$password=password_hash($password_raw, PASSWORD_DEFAULT);		
			$send = "INSERT INTO users (firstname, lastname, jobtitle, phone, cellphone, email, username, distributor, pressagent, admin, created_at, updated_at, password) VALUES 
			('".$_POST['c_firstname']."', '".$_POST['c_lastname']."', '".$_POST['c_jobtitle']."', '".$_POST['c_phone']."', '".$_POST['c_cellphone']."', '".$_POST['c_email']."', '".$_POST['c_username']."', '".$_POST['c_distributor']."', '".$_POST['c_pressagent']."', '".$_POST['c_admin']."', '".$created_at."', '".$updated_at."', '".$password."')";
			$sent = mysql_query($send) or die("Kontakt anlegen leider fehlgeschlagen".mysql_error());			
			echo "<button type='button' class='btn btn-success'>Nutzer angelegt</button>";
			//Neu angelegte ID mit auf den Weg geben
			$query = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
			$checkdata = mysql_query($query);
			while($row = mysql_fetch_object($checkdata))
				{
				$take=$row->id;
				}
			}		

		
		If ($take!='xxx')
			{
			//Daten einlesen
			$query = "SELECT * FROM users WHERE id=$take";
			$checkdata = mysql_query($query);
			if(mysql_num_rows($checkdata)>=1)
				{	
				while($row = mysql_fetch_object($checkdata))
					{
					$firstname=$row->firstname;
					$lastname=$row->lastname;
					$jobtitle=$row->jobtitle;
					$phone=$row->phone;
					$cellphone=$row->cellphone;
					$email=$row->email;		
					$username=$row->username;
					$distributor=$row->distributor;
					$pressagent=$row->pressagent;
					$admin=$row->admin;			
					$created_at=$row->created_at;
					$updated_at=$row->updated_at;
					$deleted=$row->deleted;
					}
				}
			}	
		}
	If (!isset($_POST['user']) OR $take=='xxx')
		{
		//Kein Datensatz festgelegt oder neu? - Erstmal Standardangaben zeigen	
		$firstname='';
		$lastname='';
		$jobtitle='';
		$phone='';
		$cellphone='';
		$email='';		
		$username='';
		$distributor='';
		$pressagent='';
		$admin='';			
		$created_at='';
		$updated_at='';
		$deleted=0;
		}


	
	?>
	<div id="mainview" >
	<table border=0 class="centred"><tr><td align='left'>
	<?
	echo "<form action='user.php' method='post' style='display:inline;'>";
	echo "<button Hspace='5' type='submit' class='btn btn-info' title='Zur Übersicht' name='' value=''><span class='glyphicon glyphicon-share' aria-hidden='true'></span> Zurück zur Übersicht</button></form></td><td  colspan=2 bgcolor='#99ccff' align='right'></form>";						
	echo "</td><td width='20'></td><td align='right'>";
	If ($deleted==0)
		{
		echo "<form action='user.php' method='post' style='display:inline;'>";
		echo "<INPUT type='hidden' id='user' name='user' value='$take'>";
		?><button type='submit' class='btn btn-danger' title='Nutzer deaktivieren' name='delete' value='' onclick="return confirm('Nutzer wirklich deaktivieren?');"><span class='glyphicon glyphicon-trash' aria-hidden='true'></span> Nutzer deaktivieren</button></form><?

		}
	else
		{
		echo "<form action='user.php' method='post' style='display:inline;'>";
		echo "<INPUT type='hidden' id='user' name='user' value='$take'>";
		?><button type='submit' class='btn btn-success' title='Nutzer widerherstellen' name='activate' value='' onclick="return confirm('Nutzer wirklich reaktivieren?');"><span class='glyphicon glyphicon-star' aria-hidden='true'></span> Nutzer reaktivieren</button></form><?				
		}	
				
			echo "<form action='user_detail.php' method='post'>";	
			?>
			</table><br><table border=0 class="centred">		
			<tr height=20>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Name</th>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Jobtitel</th>
			</tr><tr>
			<?		
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='text' class='form-control' name='c_firstname' value='$firstname' placeholder='Vorname' size='50'>";
			echo "</td>";				
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='text' class='form-control' name='c_jobtitle' value='$jobtitle' placeholder='Jobtitel' size='50'>";			
			echo "</td>";		
			?>	
			</tr><tr>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Nachname</th>		
			<th class='cell' colspan=2 bgcolor='#cccccc'>Telefon</th>
			</tr><tr>
			<?
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='text' class='form-control' name='c_lastname' value='$lastname' placeholder='Nachname' size='50'></td>";
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' class='form-control' name='c_phone' value='$phone' placeholder='Festnetz'></td>";		
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' class='form-control' name='c_cellphone' value='$cellphone' placeholder='Handy'></td>";	
			?>				
			</tr><tr>
			<th class='cell' colspan=2 bgcolor='#cccccc'>E-Mail</th>
			<th class='cell' colspan=2 bgcolor='#cccccc'>Abgeordneter</th>
			</tr><tr>
			<?
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<input type='email' class='form-control' required name='c_email' value='$email' placeholder='mail@mail.de' size='50'></td>";
			echo "<td class='cell' colspan=2 bgcolor='#99ccff'>";
			echo "<select name='c_distributor' class='form-control'>";
			If ($distributor==1){echo "<option selected value='1'>Ja</option>";}
			else{echo "<option value='1'>Ja</option>";}
			If ($distributor==0){echo "<option selected value='0'>Nein</option>";}
			else{echo "<option value='0'>Nein</option>";}
			echo "</select></td>";			
			
			//echo "<input type='text' name='c_distributor' value='$distributor' placeholder='1=ja/0=nein' size='50'></td>";		
			?>	
			</tr><tr>
			<th class='cell' bgcolor='#cccccc' >Username</th>
			<th class='cell' bgcolor='#cccccc' >Passwort</th>
			<th class='cell' bgcolor='#cccccc'>Pressemitarbeiter</th>
			<th class='cell' bgcolor='#cccccc'>admin</th>			
			</tr><tr>
			<?
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='text' class='form-control' name='c_username' value='$username' placeholder='Login-Name'></td>";
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<input type='password' class='form-control' name='c_password' value='' placeholder='Passwort'></td>";
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<select name='c_pressagent' class='form-control'>";
			If ($pressagent==1){echo "<option selected value='1'>Ja</option>";}
			else{echo "<option value='1'>Ja</option>";}
			If ($pressagent==0){echo "<option selected value='0'>Nein</option>";}
			else{echo "<option value='0'>Nein</option>";}
			echo "</select></td>";
			//echo "<input type='text' name='c_pressagent' value='$pressagent' placeholder='1=ja/0=nein'></td>";		
			echo "<td class='cell' bgcolor='#99ccff'>";
			echo "<select name='c_admin' class='form-control'>";
			If ($admin==1){echo "<option selected value='1'>Ja</option>";}
			else{echo "<option value='1'>Ja</option>";}
			If ($admin==0){echo "<option selected value='0'>Nein</option>";}
			else{echo "<option value='0'>Nein</option>";}
			echo "</select></td></tr><tr height=10></tr><tr>";		

		
			
			//echo "<input type='submit' name='safe' value='Speichern' class='form-control'></form>";	
			//echo "</tr><tr height=25><td colspan=42 bgcolor='#000000'></td></tr></td></tr><tr><td colspan=42 bgcolor='#cccccc' align='right'>";	
			//echo "<form action='user.php' method='post' style='display:inline;'>";
			//echo "<button type='submit' class='btn btn-info' title='Zur Übersicht' name='' value=''><span class='glyphicon glyphicon-share' aria-hidden='true'></span> Zurück zur Übersicht</button></form></td><td  colspan=2 bgcolor='#99ccff' align='right'></form>";						
			//echo "<input type='submit' value='Zur Übersicht' class='form-control'></form></td>";			
			
			echo "<td colspan=42  align='right'>";
			//echo "<form action='user_detail.php' method='post' style='display:inline;'>";
			echo "<INPUT type='hidden' id='user' name='user' value='$take'>";	
			echo "<button type='submit' class='btn btn-primary' title='Speichern' name='safe' value='$take'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span> Speichern</button></form>";			

			echo "</td>";
			
			
			
			?>	

		</tr></table>

	</div>
<?	
include_once("footer.php");	
