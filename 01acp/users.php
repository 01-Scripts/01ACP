<?PHP
/* 
	01ACP - Copyright 2008-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Benutzerverwaltung (Benutzer hinzuf�gen und bearbeiten; Eigenes Profil)
	#fv.131#
*/

$menuecat = "01acp_users";
$sitetitle = "Benutzerverwaltung";
$filename = $_SERVER['SCRIPT_NAME'];
$mootools_use = array("moo_core","moo_more","moo_slidev");

// Config-Dateien
include("system/main.php");
include("system/head.php");

// Sicherheitsabfrage: Login
if(isset($userdata['id']) && $userdata['id'] > 0){



// Neuen Benutzer hinzuf�gen
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "add_user" && $userdata['userverwaltung'] >= 1){
?>
<h1>Neuen Benutzer erstellen</h1>

<?PHP
	// Neuen Benutzer in Datenbank eintragen
	if(isset($_POST['send']) && $_POST['send'] == 1 &&
	isset($_POST['username']) && !empty($_POST['username']) &&
	isset($_POST['mail']) && !empty($_POST['mail']) && check_mail($_POST['mail']) &&
	isset($_POST['pwwahl']) && (($_POST['pwwahl'] == "eigen" && isset($_POST['password']) && !empty($_POST['password']) && strlen($_POST['password']) >= PW_LAENGE) || $_POST['pwwahl'] == "random") &&
	isset($_POST['level']) && $_POST['level'] <= $userdata['level']){
		// �berpr�fen ob E-Mail-Adresse oder Benutzername schon vorhanden ist
		$list = $mysqli->query("SELECT id FROM ".$mysql_tables['user']." WHERE username='".$mysqli->escape_string($_POST['username'])."' OR mail='".$mysqli->escape_string($_POST['mail'])."'");
		if($list->num_rows < 1){
			if($_POST['pwwahl'] == "random"){
		        $password = create_NewPassword(8);
		        $passmd5 = pwhashing($password);
				}
			else{
				$password = $_POST['password'];
				$passmd5 = pwhashing($_POST['password']);
				}
			
			// Eintrag in DB vornehmen
			$sql_insert = "INSERT INTO ".$mysql_tables['user']." (username,mail,userpassword,level) VALUES (
							'".$mysqli->escape_string(trim($_POST['username']))."',
							'".$mysqli->escape_string($_POST['mail'])."',
							'".$passmd5."',
							'".$mysqli->escape_string($_POST['level'])."'
							)";
            $result = $mysqli->query($sql_insert) OR die($mysqli->error);
			$lastinsertid = $mysqli->insert_id;
			
			if($lastinsertid > 0){
				// Passwort mit richtigem Passwort aus pwhasing2-Funkion ersetzen
				$mysqli->query("UPDATE ".$mysql_tables['user']." SET userpassword = '".pwhashing2($password, $lastinsertid)."' WHERE id='".$lastinsertid."' LIMIT 1");

				// ggf. E-Mail @ Benutzer versenden
				if($_POST['pwwahl'] == "random" || isset($_POST['emailinfo']) && $_POST['emailinfo'] == 1){
					$header = "From:".$settings['email_absender']."<".$settings['email_absender'].">\n";
			        $email_betreff = $settings['sitename']." - Ein Benutzerkonto wurde f�r Sie angelegt";
			        $emailbody = $settings['sitename']."\n\nF�r Sie wurde ein neues Benutzerkonto angelegt. Sie k�nnen sich unter\n".$settings['absolut_url']."01acp/\nmit folgenden Zugangsdaten einloggen:\n\nBenutzername: ".$_POST['username']."\nPasswort: ".$password."\n\n---\nWebmailer";

			        if(!mail(stripslashes($_POST['mail']),$email_betreff,$emailbody,$header)){
						echo "<p class=\"meldung_error\">Fehler: Es konnte keine E-Mail an den neuen Benutzer versand werden.</p>";
						}
					} 

				if($userdata['userverwaltung'] == 2){
					echo "<script>redirect(\"".$filename."?action=edit_user&userid=".$lastinsertid."\");</script>";
					echo "<p class=\"meldung_ok\">Der Benutzer <b>".$_POST['username']."</b> wurde erfolgreich erstellt.<br />
						Sie werden zur Konfiguration der Benutzerrechte weitergeleitet. Sollten Sie nicht weitergeleitet werden, klicken Sie
						bitte <a href=\"".$filename."?action=edit_user&amp;userid=".$lastinsertid."\">hier</a>.</p>";
					}
				else
					echo "<p class=\"meldung_ok\">Der Benutzer <b>".$_POST['username']."</b> wurde erfolgreich erstellt.</p>";
			
				}
			// Fehler: Beim Eintragen trat ein Fehler auf
			else{
				echo "<p class=\"meldung_error\">Fehler: Es konnte kein neuer Benutzer erstellt werden.<br />
						Beim Eintragen in die Datenbank trat ein unvorhergesehener Fehler auf. Bitte beachten Sie
						die MySQL-Fehlermeldung und setzen sich mit einem technischen Ansprechpartner in Verbindung.</p>";
				}
		}
		// Fehler: Benutzername oder E-Mail existieren schon in der DB
		else{
			echo "<p class=\"meldung_error\">Fehler: Der neue Benutzer konnte nicht erstellt werden. Ein Benutzer mit diesem
					Benutzernamen oder dieser E-Mail-Adresse existiert bereits.<br />
					Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a> und &auml;ndern Sie Ihre Eingaben entsprechend ab.</p>";
		}
	}
	// Fehler: Nicht alle Felder ausgef�llt oder PW zu kurz
	elseif(isset($_POST['send']) && $_POST['send'] == 1){
		echo "<p class=\"meldung_error\">Fehler: Der neue Benutzer konnte nicht erstellt werden. Sie haben nicht
				alle Felder komplett ausgef&uuml;llt oder das von Ihnen eingegebene Passwort ist zu kurz (mind. ".PW_LAENGE." Zeichen).<br />
				Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a> und &auml;ndern Sie Ihre Eingaben entsprechend ab.</p>";
		}
	else{
?>

<form action="<?PHP echo $filename; ?>" method="post">
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">

    <tr>
        <td class="tra" width="30%"><b>Benutzername*</b></td>
        <td class="tra"><input type="text" name="username" size="50" maxlength="50" /></td>
    </tr>
	
    <tr>
        <td class="trb"><b>E-Mail-Adresse*</b></td>
        <td class="trb"><input type="text" name="mail" size="50" maxlength="50" /></td>
    </tr>	
	
    <tr>
        <td class="tra"><b>Passwort*</b></td>
        <td class="tra">
			<input type="radio" name="pwwahl" value="random" checked="checked" /> Zuf&auml;lliges Passwort generieren <span class="small">(Wird per E-Mail an den Benutzer verschickt)</span><br />
			<input type="radio" name="pwwahl" value="eigen" /> Passwort manuell festlegen: <input type="text" name="password" size="20" /> <span class="small"><b>Mindestens <?PHP echo PW_LAENGE; ?> Zeichen</b></span>
		</td>
    </tr>

    <tr>
        <td class="trb"><b>Sicherheitsstufe*</b></td>
        <td class="trb">
			<select name="level" size="1">
				<?PHP
				for($x=1;$x<=$userdata['level'];$x++){
					echo "<option value=\"".$x."\">Sicherheitsstufe ".$x."</option>\n";
					}
				?>
			</select> <b>[ <a href="javascript:hide_unhide('help_level');">?</a> ]</b>
		</td>
    </tr>

    <tr>
        <td class="tra"><b>E-Mail an Benutzer senden</b></td>
        <td class="tra">
			<input type="checkbox" name="emailinfo" value="1" checked="checked" /> Der neue Benutzer wird mit einer E-Mail &uuml;ber das f&uuml;r ihn eingerichtete Benutzerkonto informiert.
		</td>
    </tr>
	
    <tr>
        <td class="tra"><input type="reset" value="Felder zur&uuml;cksetzen" class="input" /></td>
        <td class="tra">
			<input type="hidden" name="action" value="add_user" />
			<input type="hidden" name="send" value="1" />
			<input type="submit" value="Benutzer erstellen &raquo;" class="input" />
		</td>
    </tr>

</table>
</form>

<div class="meldung_hinweis" id="help_level" style="display:none;">
	Benutzer mit einer niedrigeren Sicherheitsstufe k&ouml;nnen Benutzer mit h&ouml;heren Sicherheitsstufe nicht bearbeiten oder l&ouml;schen.<br />
	<br />
	Beispiel:<br />
	Einem Benutzer mit Sicherheitsstufe 2 ist es nicht gestattet einen Benutzer mit Sicherheitsstufe 3 (oder h&ouml;her) anzulegen oder zu bearbeiten.
</div>

<?PHP
		} // else: Formular anzeigen

	}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "add_user")
		$flag_loginerror = true;








// Durchf�hren: Benutzer l�schen
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "do_del" && $userdata['userverwaltung'] == 2 && isset($_GET['userid']) && !empty($_GET['userid']) && is_numeric($_GET['userid'])){
	$error = 0;
	
	if($_GET['userid'] == $userdata['id']) $error = 1;
	else{
		$list = $mysqli->query("SELECT id,username,mail,level FROM ".$mysql_tables['user']." WHERE id='".$mysqli->escape_string($_GET['userid'])."' AND id != '0' LIMIT 1");
		while($row = $list->fetch_assoc()){
			$u_id = $row['id'];
			$u_username = stripslashes($row['username']);
			$u_mail = stripslashes($row['mail']);
			$u_level = $row['level'];
			}
		if($u_level >= $userdata['level']) $error = 2;
		}
	
	// Ist ein Fehler aufgetreten?
	if($error == 0){
		// Normale weitere Programmausf�hrung
		$mysqli->query("DELETE FROM ".$mysql_tables['user']." WHERE id='".$mysqli->escape_string($u_id)."' AND id != '0' LIMIT 1");
		
		$mysqli->query("UPDATE ".$mysql_tables['files']." SET uid='0' WHERE uid='".$mysqli->escape_string($u_id)."'");
		
		/*An dieser Stelle alle Modulspezifischen zur Verf�gung gestellten L�sch-Funktionen ausf�hren
		Schema der Funktionsnamen: modulname_DeleteUser()
		�bergebene Parameter: Userid, Username, E-Mail-Adresse
		Erwarteter R�ckgabewert: TRUE / FALSE
		*/

		foreach($module as $modul_akt){
			$modul = $modul_akt['idname'];
			
			if(file_exists($moduldir.$modul_akt['idname']."/_headinclude.php"))
				include_once($moduldir.$modul_akt['idname']."/_headinclude.php");
			if(file_exists($moduldir.$modul_akt['idname']."/_functions.php"))
				include_once($moduldir.$modul_akt['idname']."/_functions.php");
			if(function_exists("_".$modul_akt['modulname']."_DeleteUser"))
				call_user_func("_".$modul_akt['modulname']."_DeleteUser",$u_id,$u_username,$u_mail);
			
			unset($modul);
			}
		
		echo "<p class=\"meldung_ok\">Der Benutzer <i>".$u_username."</i> wurde aus dem System gel&ouml;scht.</p>";
		}
	elseif($error == 1)
		echo "<p class=\"meldung_error\">Fehler: Sie k&ouml;nnen nicht Ihr eigenes Benutzerkonto l&ouml;schen!</p>";
	elseif($error == 2)
		echo "<p class=\"meldung_error\">Fehler: Sie k&ouml;nnen keinen Benutzer l&ouml;schen, der das gleiche oder ein h&ouml;heres Sicherheitslevel hat!</p>";
	else
		echo "<p class=\"meldung_error\">Es trat ein unvorhergesehener Fehler auf.</p>";

	// Variablen so setzen, dass Benutzer Auflistung erfolgt:
	$_REQUEST['action'] = "edit_users";
	$_GET['userid'] = "";

	}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "do_del")
		$flag_loginerror = true;







// Werte von Benutzer bearbeiten / Profil speichern
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "do_edit" && $userdata['userverwaltung'] == 2 ||
	isset($_REQUEST['action']) && $_REQUEST['action'] == "save_profile" && $userdata['profil'] == 1){
	$error = 0;
	switch($_REQUEST['action']){
	  case "do_edit":
	    // Weitere �berpr�fungen der �bergebenen Variablen
		if(!isset($_REQUEST['userid']) || isset($_REQUEST['userid']) && empty($_REQUEST['userid']))
			$error = 1;
		else{
			$list = $mysqli->query("SELECT * FROM ".$mysql_tables['user']." WHERE id='".$mysqli->escape_string($_REQUEST['userid'])."' AND id != '0' AND (level < '".$mysqli->escape_string($userdata['level'])."' OR level = '10' AND level = '".$mysqli->escape_string($userdata['level'])."' OR id = '".$mysqli->escape_string($userdata['id'])."') LIMIT 1");
			if($list->num_rows < 1) $error = 2;
		}
			
		$title = "Benutzer bearbeiten";
		$case = "do_edit";
	  break;
	  case "save_profile":
	    $list = $mysqli->query("SELECT * FROM ".$mysql_tables['user']." WHERE id='".$userdata['id']."' LIMIT 1");
		
		$title = "Eigenes Profil";
		$case = "profil";
	  break;
	  }
	
	// Ist ein Fehler aufgetreten?
	if($error == 0){
		// Normale weitere Programmausf�hrung
		switch($case){
		  case "do_edit":
		    if(isset($_POST['username']) && !empty($_POST['username']) && 
				isset($_POST['mail']) && !empty($_POST['mail']) && check_mail($_POST['mail']) &&
				isset($_POST['level']) && !empty($_POST['level']) && $_POST['level'] <= $userdata['level']){
				
				if(isset($_POST['sperre']) && $_POST['sperre'] != 1 || !isset($_POST['sperre'])) $_POST['sperre'] = 0;
				
				// �berpr�fen ob E-Mail-Adresse oder Benutzername schon vorhanden ist
				$list = $mysqli->query("SELECT id FROM ".$mysql_tables['user']." WHERE username='".CleanStr($_POST['username'])."' LIMIT 1");
				$row_u		= $list->fetch_assoc();
				$menge_u	= $list->num_rows;
				$list = $mysqli->query("SELECT id FROM ".$mysql_tables['user']." WHERE mail='".$mysqli->escape_string($_POST['mail'])."' LIMIT 1");
				$row_m		= $list->fetch_assoc();
				$menge_m	= $list->num_rows;
				
				$add2query = "";
				// Level can only be chaned for other users
				if($_POST['userid'] != $userdata['id'])
					$add2query .= "level='".$mysqli->escape_string(intval($_POST['level']))."', ";
				// Only changed username if it is unique
				if($menge_u < 1)
				    $add2query .= "username='".CleanStr($_POST['username'])."', ";
				elseif($row_u['id'] != $_POST['userid'])
					echo "<p class=\"meldung_error\">Der <b>Benutzername</b> wurde nicht ge&auml;ndert, da bereits ein Benutzerkonto mit diesem Namen existiert.</p>";
				// Only changed mail if it is unique
				if($menge_m < 1)
				    $add2query .= "mail='".$mysqli->escape_string($_POST['mail'])."', ";
				elseif($row_m['id'] != $_POST['userid'])
					echo "<p class=\"meldung_error\">Die <b>E-Mail-Adresse</b> wurde nicht ge&auml;ndert, da bereits ein Benutzerkonto mit dieser Adresse existiert.</p>";
				
				$mysqli->query("UPDATE ".$mysql_tables['user']." SET ".$add2query."sperre='".$mysqli->escape_string($_POST['sperre'])."' WHERE id='".$mysqli->escape_string($_POST['userid'])."' AND id != '0' LIMIT 1");

				// Passwort �ndern?
				if(isset($_POST['changepw']) && $_POST['changepw'] == 1){
					$pwerror = true;
					if(isset($_POST['pwwahl']) && $_POST['pwwahl'] == "eigen" && 
						isset($_POST['password']) && !empty($_POST['password']) && 
						strlen($_POST['password']) >= PW_LAENGE){
						
						$mysqli->query("UPDATE ".$mysql_tables['user']." SET userpassword='".pwhashing2($_POST['password'], $_POST['userid'])."', cookiehash='' WHERE id='".$mysqli->escape_string($_POST['userid'])."' AND id != '0' LIMIT 1");
						$pwerror = false;
					}
					elseif(isset($_POST['pwwahl']) && $_POST['pwwahl'] == "random" ||
						isset($_POST['pwwahl']) && $_POST['pwwahl'] == "eigen" && empty($_POST['password'])){
					
						$newpass = create_NewPassword(PW_LAENGE);
				        $newpassHash = pwhashing2($newpass, $_POST['userid']);

				        // Datenbank aktualisieren:
				        $mysqli->query("UPDATE ".$mysql_tables['user']." SET userpassword='".$newpassHash."', cookiehash='' WHERE id='".$mysqli->escape_string($_POST['userid'])."' AND id != '0' LIMIT 1");

				        $header = "From:".$settings['email_absender']."<".$settings['email_absender'].">\n";
				        $email_betreff = $settings['sitename']." - Neues Passwort f�r Administrationsbereich";
				        $emailbody = "Mit dieser E-Mail erhalten Sie ein neues Passwort f�r den Adminbereich\n\nName: ".CleanStr($_POST['username'])."\nE-Mail-Adresse: ".$_POST['mail']."\nNeues Passwort: ".$newpass."\n\n---\nWebmailer";
						$emailbody = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "",$emailbody);

						$empf = preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "",$_POST['mail']);
						$empf = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "",$empf);
				        mail($empf,$email_betreff,$emailbody,$header);
				        $pwerror = false;
					}
				}

				$savequery = "";
				// F�r Benutzer mit Level < 10 nur Einstellungen speichern auf die der User auch Zugriff hat. Function-Felder werden nicht gespeichert
				if($_POST['userid'] != $userdata['id'] && $userdata['level'] < 10){
					$query = "SELECT id,modul,idname,formename FROM ".$mysql_tables['rights']." WHERE formename != 'function' AND is_cat='0' AND hide='0'";
					$list = $mysqli->query($query);
					while($row_save = $list->fetch_assoc()){
						// Nur Einstellungen mit Berechtigungen des Users speichern
						if(!isset($userdata[str_replace("01acp_","",$row_save['modul']."_".$row_save['idname'])]) || $userdata[str_replace("01acp_","",$row_save['modul']."_".$row_save['idname'])] == 0) continue;

						if(isset($_POST[$row_save['modul']."_".$row_save['idname']]) && $_POST[$row_save['modul']."_".$row_save['idname']] <= $userdata[str_replace("01acp_","",$row_save['modul']."_".$row_save['idname'])])
							$savequery .= $row_save['modul']."_".$row_save['idname']."='".$mysqli->escape_string($_POST[$row_save['modul']."_".$row_save['idname']])."', ";
					}
				}
				// F�r Administratoren alle Felder speichern
				elseif($userdata['level'] == 10){
					$query = "SELECT id,modul,idname,formename FROM ".$mysql_tables['rights']." WHERE is_cat='0' AND hide='0'";
					$list = $mysqli->query($query);
					while($row_save = $list->fetch_assoc()){
						if($row_save['formename'] == "function" && isset($_POST[$row_save['modul']."_".$row_save['idname']]))
							$savequery .= $row_save['modul']."_".$row_save['idname']."='".mysql_real_escape_string(call_RightSettingsFunction_Write($row_save['modul'],$row_save['idname'],$_POST[$row_save['modul']."_".$row_save['idname']]))."', ";
						elseif(isset($_POST[$row_save['modul']."_".$row_save['idname']]))
							$savequery .= $row_save['modul']."_".$row_save['idname']."='".$mysqli->escape_string($_POST[$row_save['modul']."_".$row_save['idname']])."', ";
					}
				}
				
				if(isset($savequery) && !empty($savequery))
					$mysqli->query("UPDATE ".$mysql_tables['user']." SET ".substr($savequery,0,strlen($savequery)-2)." WHERE id='".$mysqli->escape_string($_POST['userid'])."' AND id != '0' LIMIT 1");
				
				echo "<p class=\"meldung_ok\">Benutzerdaten wurden gespeichert.<br />
					<a href=\"".BuildURL("users.php",NULL,array('action'=>'edit_user','userid'=>$_POST['userid']))."\">Benutzer erneut bearbeiten &raquo;</a></p>";
				
				if(isset($pwerror) && $pwerror)
				    echo "<p class=\"meldung_error\">Das Passwort wurde <b>nicht</b> ge&auml;ndert.<br />
							Bitte geben Sie ein Passwort mit mindestens ".PW_LAENGE." Zeichen ein!</p>";
				}
			else
				echo "<p class=\"meldung_error\">Sie haben keine Berechtigung diesen Benutzer zu bearbeiten.<br />
				Oder Sie haben keinen Benutzernamen bzw. eine Nicht-valide E-Mail-Adresse eingegeben.<br />
				Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";

		  $_REQUEST['action'] = "edit_users";
		  $_REQUEST['userid'] = "";

		  break;
		  case "profil":
			if(isset($_POST['mail']) && !empty($_POST['mail']) && check_mail($_POST['mail'])){
				// �berpr�fen ob E-Mail-Adresse schon vorhanden ist
				$list = $mysqli->query("SELECT id FROM ".$mysql_tables['user']." WHERE mail='".$mysqli->escape_string($_POST['mail'])."'");

				$add2query = "";
				if($list->num_rows < 1)
				    $add2query .= "mail='".$mysqli->escape_string($_POST['mail'])."', ";
				elseif($userdata['mail'] != $_POST['mail']){
					$save_error = true;
					echo "<p class=\"meldung_error\">Die <b>E-Mail-Adresse</b> wurde nicht ge&auml;ndert, da bereits ein Benutzerkonto mit dieser Adresse existiert.</p>";
					}
				
				$mysqli->query("UPDATE ".$mysql_tables['user']." SET ".$add2query."startpage='".$mysqli->escape_string($_POST['startpage'])."' WHERE id='".$mysqli->escape_string($userdata['id'])."' AND id != '0' LIMIT 1");
				
				
				$query = "SELECT * FROM ".$mysql_tables['rights']." WHERE is_cat='0' AND hide='0' AND in_profile='1' ORDER BY catid,sortid";
				$list = $mysqli->query($query);
				$savequery = "";
				while($row_save = $list->fetch_assoc()){
					if(isset($_POST[$row_save['modul']."_".$row_save['idname']]))
						$savequery .= $row_save['modul']."_".$row_save['idname']."='".$mysqli->escape_string($_POST[$row_save['modul']."_".$row_save['idname']])."', ";
					}
					
				$mysqli->query("UPDATE ".$mysql_tables['user']." SET ".substr($savequery,0,strlen($savequery)-2)." WHERE id='".$mysqli->escape_string($userdata['id'])."' AND id != '0' LIMIT 1");
				
				echo "<p class=\"meldung_ok\">Daten wurden gespeichert.<br />
					<a href=\"users.php?action=profil\">Weiter &raquo;</a></p>";
				if(!isset($save_error))
					echo "<script>redirect(\"users.php?action=profil&show=saved\");</script>";
				}
			else{
				echo "<p class=\"meldung_error\">Fehler: Sie haben keine oder eine fehlerhafte E-Mail-Adresse
				eingegeben. Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";
				}
		  break;
		  }
		
		}
	elseif($error == 1)
		echo "<p class=\"meldung_error\">Fehler: Die &uuml;bergebene Benutzer-ID ist leider fehlerhaft &amp; in dieser
			Form nicht g&uuml;ltig. Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";
	elseif($error == 2)
		echo "<p class=\"meldung_error\">Fehler: Zur &uuml;bergebene Benutzer-ID konnte leider kein passender
			Eintrag in der Datenbank gefunden werden, <b>oder Sie haben keine Berechtigung um diesen Benutzer zu bearbeiten</b>.
			Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";
	else
		echo "<p class=\"meldung_error\">Es trat ein unvorhergesehener Fehler auf. Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";

	}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "do_edit" || 
		isset($_REQUEST['action']) && $_REQUEST['action'] == "save_profile")
		$flag_loginerror = true;








// Benutzer auflisten
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit_users" && $userdata['userverwaltung'] == 2){
?>
<h1>Benutzer bearbeiten</h1>

<form action="<?PHP echo $filename; ?>" method="get">
	<p>
	<a href="users.php?action=add_user" class="actionbutton"><img src="images/icons/add.gif" alt="Plus-Zeichen" title="Neuen Benutzer erstellen" style="border:0; margin-right:10px;" />Neuen Benutzer erstellen</a>
	<input type="text" name="search" value="Nach Benutzername oder E-Mail" size="30" onfocus="clearField(this);" onblur="checkField(this);" class="input_search" /> <input type="submit" value="Benutzer suchen" class="input" /></p>
	<input type="hidden" name="action" value="edit_users" />
</form>

<?PHP
	if(isset($_GET['sort']) && $_GET['sort'] == "desc") $sortorder = "DESC";
	else{ $sortorder = "ASC"; $_GET['sort'] = "ASC"; }
	
	if(isset($_GET['search']) && !empty($_GET['search'])) $where = " WHERE (username LIKE '%".$mysqli->escape_string($_GET['search'])."%' OR mail LIKE '%".$mysqli->escape_string($_GET['search'])."%') AND id != '0'";
	else{ $where = " WHERE id != '0'"; $_GET['search'] = ""; }
	
	if(!isset($_GET['orderby'])) $_GET['orderby'] = "";
	switch($_GET['orderby']){
	  case "sperre":
	    $orderby = "sperre,username";
	  break;
	  case "username":
	    $orderby = "username";
	  break;
	  case "mail":
	    $orderby = "mail";
	  break;
	  case "level":
	    $orderby = "level ".$sortorder.", username";
	  break;
	  case "lastlogin":
	    $orderby = "lastlogin,username";
	  break;	  
	  default:
	    $orderby = "username";
	  break;
	  }

	$sites = 0;
	$query = "SELECT * FROM ".$mysql_tables['user']."".$where." ORDER BY ".$orderby." ".$sortorder;
	$query = makepages($query,$sites,"site",ACP_PER_PAGE);

	// Fehlermeldung bei erfolgloser Suche
	if($sites == 0 && isset($_GET['search']) && !empty($_GET['search']))
		echo "<p class=\"meldung_error\">Es konnte leider kein Benutzer zu Ihrer Sucheingabe \"".stripslashes($_GET['search'])."\" gefunden werden!<br />
			Bitte probieren Sie es erneut.</p>";

?>
<table border="0" align="center" width="100%" cellpadding="3" cellspacing="5" class="rundrahmen">
    <tr>
		<td class="tra" width="25" align="center"><a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'asc','orderby'=>'sperre')); ?>"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren (ASC)" /></a><!--Benutzer ist gesperrt--></td>
        <td class="tra"><b>Benutzername</b>
			<a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'asc','orderby'=>'username')); ?>"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'desc','orderby'=>'username')); ?>"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra"><b>E-Mail-Adresse</b>			
			<a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'asc','orderby'=>'mail')); ?>"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'desc','orderby'=>'mail')); ?>"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra" width="130"><b>Sicherheitsstufe</b>			
			<a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'asc','orderby'=>'level')); ?>"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'desc','orderby'=>'level')); ?>"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra" width="145"><b>Letzte Anmeldung</b>			
			<a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'asc','orderby'=>'lastlogin')); ?>"><img src="images/icons/sort_asc.gif" alt="Icon: Pfeil nach oben" title="Aufsteigend sortieren" /></a>
			<a href="<?PHP echo BuildURL($filename,NULL,array('sort'=>'desc','orderby'=>'lastlogin')); ?>"><img src="images/icons/sort_desc.gif" alt="Icon: Pfeil nach unten" title="Absteigend sortieren (DESC)" /></a>
		</td>
		<td class="tra nosort" width="25">&nbsp;<!--Bearbeiten--></td>
		<td class="tra nosort" width="25" align="center"><!--L�schen--><img src="images/icons/icon_trash.gif" alt="M&uuml;lleimer" title="Datei l&ouml;schen" /></td>
    </tr>
<?PHP
	// Ausgabe der Datens�tze (Liste) aus DB
	$count = 0;
	$list = $mysqli->query($query);
	while($row = $list->fetch_assoc()){
		// Zusatzinformationen/Statistiken �ber Benutzer zusammentragen
		$userstats = getUserstats($row['id']);

		if(is_array($module)){
			foreach($module as $modul_akt){
				$modul = $modul_akt['idname'];
				
				if(file_exists($moduldir.$modul_akt['idname']."/_headinclude.php"))
					include_once($moduldir.$modul_akt['idname']."/_headinclude.php");
				if(file_exists($moduldir.$modul_akt['idname']."/_functions.php"))
					include_once($moduldir.$modul_akt['idname']."/_functions.php");
				if(function_exists("_".$modul_akt['modulname']."_getUserstats"))
					$addstats = call_user_func("_".$modul_akt['modulname']."_getUserstats",$row['id']);
				
				if(isset($addstats) && is_array($addstats))
					$userstats = array_merge($userstats,$addstats);
				
				unset($modul);
				unset($addstats);
				}
			}

		if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
		
		if($row['sperre'] == 1){
			$sperricon = "<img src=\"images/icons/icon_gesperrt.gif\" alt=\"Icon: Schlo&szlig;\" title=\"Benutzer ist gesperrt\" />";
			$class = "tr_red";
			}
		else $sperricon = "";
		
		if($row['lastlogin'] > 0) $lastlogin = date("d.m.Y",$row['lastlogin']);
		else $lastlogin = "-";
		
		echo "    <tr>
		<td class=\"".$class."\" align=\"center\">".$sperricon."</td>
		<td class=\"".$class."\" onclick=\"hide_unhide_tr('id".$row['id']."');\" style=\"cursor: pointer;\">".stripslashes($row['username'])."</td>
		<td class=\"".$class."\"><a href=\"mailto:".stripslashes($row['mail'])."\">".stripslashes($row['mail'])."</a></td>
		<td class=\"".$class."\" align=\"center\">".$row['level']."</td>
		<td class=\"".$class."\" align=\"center\">".$lastlogin."</td>
		<td class=\"".$class."\" align=\"center\"><a href=\"".BuildURL($filename,NULL,array('action'=>'edit_user','userid'=>$row['id']))."\"><img src=\"images/icons/icon_edit.gif\" alt=\"Bearbeiten - Stift\" title=\"Benutzer bearbeiten\" style=\"border:0;\" /></a></td>
		<td class=\"".$class."\" align=\"center\"><a href=\"".BuildURL($filename,NULL,array('action'=>'ask_del','userid'=>$row['id']))."\"><img src=\"images/icons/icon_delete.gif\" alt=\"L&ouml;schen - rotes X\" title=\"Benutzer l&ouml;schen\" style=\"border:0;\" /></a></td>
	</tr>";
		echo "<tr id=\"id".$row['id']."\" style=\"display:none;\">
		<td class=\"".$class."\" colspan=\"7\">";
		foreach($userstats as $data){
			echo "<b>".$data['statcat']."</b> ".$data['statvalue']."<br />\n";
			}
		echo "\n		</td>
	</tr>";
		}
	
	echo "</table>";

	echo echopages($sites,"80%","site","action=edit_users&amp;search=".$_GET['search']."&amp;sort=".$_GET['sort']."&amp;orderby=".$_GET['orderby']."");

	}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit_users" && $userdata['userverwaltung'] == 1){
		echo "<h1>Benutzer erstellen</h1>";
		echo "<p><a href=\"users.php?action=add_user\" class=\"actionbutton\"><img src=\"images/icons/add.gif\" alt=\"Plus-Zeichen\" title=\"Neuen Benutzer erstellen\" style=\"border:0; margin-right:10px;\" />Neuen Benutzer erstellen</a></p>";
	}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit_users")
		$flag_loginerror = true;








// Abfrage: Benutzer l�schen
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "ask_del" && $userdata['userverwaltung'] == 2){
	$error = 0;
	
	if($_GET['userid'] == $userdata['id']) $error = 1;
	else{
		$list = $mysqli->query("SELECT username,level FROM ".$mysql_tables['user']." WHERE id='".$mysqli->escape_string($_GET['userid'])."' AND id != '0' LIMIT 1");
		while($row = $list->fetch_assoc()){
			$u_username = $row['username'];
			$u_level = $row['level'];
			}
		if($u_level >= $userdata['level']) $error = 2;
		}

	// Ist ein Fehler aufgetreten?
	if($error == 0){
		// Normale weitere Programmausf�hrung
		echo "<p class=\"meldung_frage\">M&ouml;chten Sie den Benutzer <i>".$u_username."</i> wirklich l&ouml;schen?<br />
			Vom Benutzer erstellte Beitr&auml;ge und hochgeladene Dateien etc. bleiben davon unber&uuml;hrt.<br />
			<b><a href=\"".BuildURL("users.php",NULL,array('action'=>'do_del','userid'=>$_GET['userid']))."\">JA</a> | 
			<a href=\"".BuildURL("users.php",NULL,array('action'=>'edit_users','userid'=>''))."\">NEIN</a></b></p>";
		}
	elseif($error == 1)
		echo "<p class=\"meldung_error\">Fehler: Sie k&ouml;nnen nicht Ihr eigenes Benutzerkonto l&ouml;schen!<br />
			<a href=\"".BuildURL("users.php",NULL,array('action'=>'edit_users','userid'=>''))."\">Zur&uuml;ck</a>.</p>";
	elseif($error == 2)
		echo "<p class=\"meldung_error\">Fehler: Sie k&ouml;nnen keinen Benutzer l&ouml;schen, der das gleiche oder ein h&ouml;heres
			Sicherheitslevel hat!
			Bitte gehen Sie <a href=\"".BuildURL("users.php",NULL,array('action'=>'edit_users','userid'=>''))."\">zur&uuml;ck</a>.</p>";
	else
		echo "<p class=\"meldung_error\">Es trat ein unvorhergesehener Fehler auf. Bitte gehen Sie <a href=\"".BuildURL("users.php",NULL,array('action'=>'edit_users','userid'=>''))."\">zur&uuml;ck</a>.</p>";

	}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "ask_del")
		$flag_loginerror = true;








// Einzelnen Benutzer bearbeiten / Profil
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit_user" && $userdata['userverwaltung'] == 2 ||
isset($_REQUEST['action']) && $_REQUEST['action'] == "profil" && $userdata['profil'] == 1){
	$error = 0;
	switch($_REQUEST['action']){
	  case "edit_user":
	    // Weitere �berpr�fungen der �bergebenen Variablen
		if(!isset($_REQUEST['userid']) || isset($_REQUEST['userid']) && empty($_REQUEST['userid'])){
			$error = 1;
			}
		else{
			$list = $mysqli->query("SELECT * FROM ".$mysql_tables['user']." WHERE id='".$mysqli->escape_string($_REQUEST['userid'])."' AND id != '0' AND (level < '".$mysqli->escape_string($userdata['level'])."' OR level = '10' AND level = '".$mysqli->escape_string($userdata['level'])."' OR id = '".$mysqli->escape_string($userdata['id'])."') LIMIT 1");
			if($list->num_rows < 1) $error = 2;
			}
			
		$title = "Benutzer bearbeiten";
		$case = "edit";
		$readonly = "";

		echo "<h1>".$title."</h1>";
		
		echo "<form action=\"".BuildURL($filename,NULL,array('action'=>'','userid'=>''))."\" method=\"post\" name=\"post\">";
	  break;
	  case "profil":
	    $list = $mysqli->query("SELECT * FROM ".$mysql_tables['user']." WHERE id='".$userdata['id']."' AND id != '0' LIMIT 1");
		
		$title = "Eigenes Profil";
		$case = "profil";
		$readonly = " readonly=\"readonly\"";

		echo "<h1>".$title."</h1>";
		
		echo "<form action=\"".$filename."\" method=\"post\" name=\"post\">";
		
		if(isset($_GET['show']) && $_GET['show'] == "saved"){
			echo "<p class=\"meldung_ok\">Daten wurden gespeichert.</p>";
			}
	  break;
	  }
	
	// Ist ein Fehler aufgetreten?
	if($error == 0){
		//Normale weitere Programmausf�hrung
		
		if($userdata['level'] < 10)
			echo "<p class=\"meldung_hinweis\">Sie k&ouml;nnen nur Einstellungen vornehmen f&uuml;r die Sie selbst &uuml;ber die entsprechenden Berechtigungen verf&uuml;gen.</p>";

		echo "<table border=\"0\" align=\"center\" width=\"100%\" cellpadding=\"3\" cellspacing=\"5\" class=\"rundrahmen\">";
		
		// Datensatz aus Datenbank holen und "besondere" Felder (Name, Passw etc.) ausgeben
		while($datarow = $list->fetch_assoc()){
			// WIRD AUF PROFIL & USER-BEARBEITEN-SEITE ANGEZEIGT
			echo "<tr>
				<td colspan=\"2\" class=\"tra\"><h3>Stammdaten</h3></td>
			</tr>
			
			<tr>
				<td class=\"trb\" width=\"60%\"><b>Benutzername</b></td>
				<td class=\"trb\"><input type=\"text\" name=\"username\" value=\"".stripslashes($datarow['username'])."\" size=\"50\"".$readonly." /></td>
			</tr>
			
			<tr>
				<td class=\"tra\"><b>E-Mail-Adresse</b></td>
				<td class=\"tra\"><input type=\"text\" name=\"mail\" value=\"".stripslashes($datarow['mail'])."\" size=\"50\" /></td>
			</tr>";
		
			$count = 0;
			if($case == "edit"){
// CASE: BENUTZER BEARBEITEN
				echo "<tr>
					<td class=\"trb\"><b>Passwort &auml;ndern</b></td>
					<td class=\"trb\">
						<input type=\"checkbox\" name=\"changepw\" value=\"1\" /> <span class=\"small\"><b>Bitte Checkbox markieren um das Passwort zu &auml;ndern.</b></span><br />
						<input type=\"radio\" name=\"pwwahl\" value=\"random\" checked=\"checked\" /> Zuf�lliges Passwort generieren <span class=\"small\">(Wird per E-Mail an den Benutzer verschickt)</span><br />
						<input type=\"radio\" name=\"pwwahl\" value=\"eigen\" /> Passwort festlegen: <input type=\"text\" name=\"password\" size=\"20\" /> <br /><span class=\"small\"><b>Mindestens ".PW_LAENGE." Zeichen</b></span>
					</td>
				</tr>
				
				<tr>
					<td class=\"tra\"><b>Sicherheitsstufe</b></td>
					<td class=\"tra\">";
					
				if($userdata['id'] != $datarow['id']){
					echo "<select name=\"level\" size=\"1\">";
					for($x=1;$x<=$userdata['level'];$x++){
						if($datarow['level'] == $x) $sel = " selected=\"selected\"";
						else $sel = "";
						
						echo "<option value=\"".$x."\"".$sel.">Sicherheitsstufe ".$x."</option>\n";
						}
					echo "</select>";
					}
				else
					echo "<input type=\"text\" size=\"5\" name=\"level\" value=\"".$userdata['level']."\" readonly=\"readonly\" />";
				
				echo "</td>
				</tr>";
				
				// Benutzer sperren / entsperren
				if($userdata['id'] != $datarow['id'] && $datarow['level'] <= $userdata['level']){ "";
					if($datarow['sperre'] == 1){
						$s_text = "Benutzer ist gesperrt";
						$s_color = "tr_red";
						$s_checked = " checked=\"checked\"";
						}
					else{
						$s_text = "Benutzer sperren";
						$s_color = "trb";
						$s_checked = "";
						}
					echo "<tr>
						<td class=\"".$s_color."\"><b>".$s_text."</b></td>
						<td class=\"".$s_color."\">
							<input type=\"checkbox\" name=\"sperre\" value=\"1\"".$s_checked." /> Benutzer sperren
						</td>
					</tr>";
				}
				
				// Zugriff eingesch�nkt?
				if($datarow['id'] != $userdata['id'] || $userdata['level'] == 10){
					$tempmodarray = array("01acp");
					$tempmodarray = array_merge($tempmodarray,$inst_module);

					foreach($tempmodarray as $modul_akt){
						$cats_rights = getSettingCats($modul_akt,$mysql_tables['rights']);
						
						// Berechtigungen f�r die einzelnen Kategorien vorhanden?
						if(isset($cats_rights) && !empty($cats_rights) && ($userdata['level'] == 10 || ($userdata[$modul_akt] == 1 || $modul_akt == "01acp"))){

							foreach($cats_rights as $catvalue){
								if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
								if($modul_akt != "01acp") $echomodulname = $module[$modul_akt]['instname']." &raquo; ";
								else $echomodulname = "";
							
								if($catvalue['modul'] != "01acp")
									echo "<tr>
								<td colspan=\"2\" class=\"".$class."\"><h3><a href=\"javascript:moo_hide_unhide_tr('".$catvalue['catid'].$catvalue['modul']."');\" title=\"Berechtigungen einblenden\">".$echomodulname.$catvalue['name']."</a></h3></td>
								</tr>";
								else
									echo "<tr>
								<td colspan=\"2\" class=\"".$class."\"><h3>".$echomodulname.$catvalue['name']."</h3></td>
								</tr>";

								$query = "SELECT * FROM ".$mysql_tables['rights']." WHERE modul='".$mysqli->escape_string($modul_akt)."' AND is_cat='0' AND catid='".$catvalue['catid']."' AND hide='0' ORDER BY sortid";
								$list = $mysqli->query($query);
								while($row = $list->fetch_assoc()){
									// Nur Einstellungen mit Berechtigungen des Users anzeigen und function-Felder prinzipiell �berspringen
									if($userdata['level'] < 10 && ($userdata[str_replace("01acp_","",$modul_akt.'_'.$row['idname'])] == 0 || $row['formename'] == "function")) continue;

						            if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
									
									$row['idname'] = $modul_akt."_".$row['idname'];
									$row['wert'] = $datarow[$row['idname']];
									
									// MySQL-Daten verarbeiten
									echo parse_dynFieldtypes($row,$class,$count,$catvalue['catid'].$catvalue['modul']);
						        }
							}
						}
					}
				}
				else{
					if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
					echo "<tr>
			        <td class=\"".$class."\" colspan=\"2\"><b class=\"red\">Das eigene Benutzerkonto kann nur eingesch&auml;nkt bearbeitet werden!</b></td>
			    </tr>";
				}
					
				if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
				echo "<tr>
		        <td class=\"".$class."\"><input type=\"reset\" value=\"Reset\" class=\"input\" /></td>
		        <td class=\"".$class."\" align=\"right\">
					<input type=\"hidden\" name=\"action\" value=\"do_edit\" />
					<input type=\"hidden\" name=\"userid\" value=\"".$_REQUEST['userid']."\" />
					<input type=\"submit\" value=\"Benutzer bearbeiten &raquo;\" class=\"input\" />
				</td>
		    </tr>";
			}
			else{
// CASE: PROFIL
				$modul_temp = $modul;
				$modul = $userdata['startpage'];

				echo "<tr>
				<td class=\"trb\"><b>Passwort &auml;ndern</b></td>
				<td class=\"trb\"><img src=\"images/icons/icon_pw.gif\" alt=\"Icon: Schl�&szlig;el\" title=\"Passwort &auml;ndern\" /> <a href=\"javascript:popup('change_pw','','','',330,280);\">Passwort �ndern</a></td>
				</tr>
				
				<tr>
				<td colspan=\"2\" class=\"tra\"><h3>Startseite w&auml;hlen</h3></td>
				</tr>
				
				<tr>
				<td class=\"trb\"><b>Gew&uuml;nschte Startseite ausw&auml;hlen</b></td>
				<td class=\"trb\"><select name=\"startpage\" size=\"1\" class=\"input_select\">
					".create_ModulDropDown(TRUE)."
				</select></td>
				</tr>
				
				<tr>
				<td colspan=\"2\" class=\"tra\"><h3>Weitere Profilfelder</h3></td>
				</tr>";
				$modul = $modul_temp;
				
				$query = "SELECT * FROM ".$mysql_tables['rights']." WHERE is_cat='0' AND hide='0' AND in_profile='1' ORDER BY catid,sortid";
				$list = $mysqli->query($query);
				while($row = $list->fetch_assoc()){
					if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
					
					$row['idname'] = $row['modul']."_".$row['idname'];
					$row['wert'] = $datarow[$row['idname']];
					
					// MySQL-Daten verarbeiten
					echo parse_dynFieldtypes($row,$class,$count,"01acp");
					}

				if($count == 1){ $class = "tra"; $count--; }else{ $class = "trb"; $count++; }
				echo "<tr>
		        <td class=\"".$class."\"><input type=\"reset\" value=\"Reset\" class=\"input\" /></td>
		        <td class=\"".$class."\">
					<input type=\"hidden\" name=\"action\" value=\"save_profile\" />
					<input type=\"submit\" value=\"Speichern &raquo;\" class=\"input\" />
				</td>
		    </tr>";
			}
		}
		
		echo "</table>";
		echo "</form><br />";
	}
	elseif($error == 1)
		echo "<p class=\"meldung_error\">Fehler: Die &uuml;bergebene Benutzer-ID ist leider fehlerhaft &amp; in dieser
			Form nicht g&uuml;ltig. Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";
	elseif($error == 2)
		echo "<p class=\"meldung_error\">Fehler: Zur &uuml;bergebene Benutzer-ID konnte leider kein passender
			Eintrag in der Datenbank gefunden werden, <b>oder Sie haben keine Berechtigung um diesen Benutzer zu bearbeiten</b>.
			Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";
	else
		echo "<p class=\"meldung_error\">Es trat ein unvorhergesehener Fehler auf. Bitte gehen Sie <a href=\"javascript:history.back();\">zur&uuml;ck</a>.</p>";

}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "edit_user" || isset($_REQUEST['action']) && $_REQUEST['action'] == "profil")
	$flag_loginerror = true;

}else $flag_loginerror = true;
include("system/foot.php");

?>