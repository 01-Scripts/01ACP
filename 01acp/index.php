<?PHP
/* 
	01ACP - Copyright 2008-2015 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Login-Formular f�r ACP-Bereich
	#fv.131#
*/

$dontshow = true;
$menuecat = "login";
$sitetitle = "Anmelden";
$filename = $_SERVER['SCRIPT_NAME'];
$message = "";
$menge = 0;
$error = 1;
$des_cookie = "";

if(!isset($_POST['username'])) $_POST['username'] = "";

// Config-Dateien
include("system/main.php");

//Logout
if(isset($_GET['action']) && $_GET['action'] == "logout"){
    session_destroy();

	if(isset($_COOKIE[$instnr.'_start_auth_01acp'])){
		$des_cookie = $_COOKIE[$instnr.'_start_auth_01acp'];
		setcookie($instnr."_start_auth_01acp", "", time() - 3600);
		}

	if(!empty($des_cookie))
		$mysqli->query("UPDATE ".$mysql_tables['user']." SET cookiehash='".sha1(mt_rand().time().$salt.$instnr)."', sessionhash='".sha1(mt_rand().time().$salt.$instnr.$filename)."' WHERE (cookiehash='".$mysqli->escape_string($des_cookie)."' OR id = '".$userdata['id']."') AND id != '0' LIMIT 1");
	else
		$mysqli->query("UPDATE ".$mysql_tables['user']." SET sessionhash='".sha1(mt_rand().time().$salt.$instnr.$filename)."' WHERE id = '".$userdata['id']."' AND id != '0' LIMIT 1");
    
	$message = "<script type=\"text/javascript\">redirect(\"index.php?logout=1\");</script>";
    $message .= "<p class=\"meldung_hinweis\"><b>Sie werden weitergeleitet.</b><br />Falls Ihr Browser keine Weiterleitung unterst�tzt klicken Sie bitte <a href=\"index.php?logout=1\">hier</a>.</p>";
    }
if(isset($_GET['logout']) && $_GET['logout'] == 1)
    $message = "<p class=\"meldung_ok\"><b>Sie wurden erfolgreich abgemeldet.</b></p>";


// Cookie vorhanden?
if(isset($_COOKIE[$instnr.'_start_auth_01acp']) && !empty($_COOKIE[$instnr.'_start_auth_01acp']) && strlen($_COOKIE[$instnr.'_start_auth_01acp']) == 40 && !isset($_GET['action']) && !isset($_GET['logout'])){
	$list = $mysqli->query("SELECT id,username,userpassword,startpage,cookiehash,sessionhash FROM ".$mysql_tables['user']." WHERE cookiehash='".$mysqli->escape_string($_COOKIE[$instnr.'_start_auth_01acp'])."' AND id != '0' LIMIT 1");
	$menge = $list->num_rows;
	while($row = $list->fetch_assoc()){
		// Session erstellen/abrufen / Lastlogin speichern
		if(empty($row['sessionhash'])){
			$sessionhash = sha1(mt_rand().time().$salt.$instnr.$row['id'].$row['username'].$row['startpage']);
			$mysqli->query("UPDATE ".$mysql_tables['user']." SET lastlogin='".time()."', sessionhash='".$sessionhash."' WHERE id='".$row['id']."' LIMIT 1");
		}
		else{
			$sessionhash = $row['sessionhash'];
			$mysqli->query("UPDATE ".$mysql_tables['user']." SET lastlogin='".time()."' WHERE id='".$row['id']."' LIMIT 1");
		}

		$_SESSION['01_idsession_'.sha1($salt)] = $row['id'];
		$_SESSION['01_passsession_'.sha1($salt)] = $sessionhash;
		
		//Weiterleiten:
		if($row['startpage'] == "01acp")
			$message = "<script type=\"text/javascript\">redirect(\"acp.php\");</script>";
		else
			$message = "<script type=\"text/javascript\">redirect(\"_loader.php?modul=".$row['startpage']."\");</script>";
		$message .= "<p class=\"meldung_ok\"><b>Login erfolgreich, Sie werden weitergeleitet.</b><br />Falls Ihr Browser keine Weiterleitung unterst&uuml;tzt klicken Sie bitte <a href=\"acp.php\">hier</a>.</p>";
		}

	}
	
// Login wurde abgeschickt
if(isset($_POST['send']) && $_POST['send'] == 1){

	if($settings['acp_captcha4login'] == 0 || $settings['acp_captcha4login'] == 1 && ($settings['spamschutz'] == 1 && isset($_POST['antispam']) && md5($_POST['antispam']) == $_SESSION['antispam01'] || $settings['spamschutz'] == 2 && CheckReCAPTCHA($_POST['g-recaptcha-response'])) ){

		$list = $mysqli->query("SELECT id,username,userpassword,startpage,cookiehash,sessionhash FROM ".$mysql_tables['user']." WHERE username='".$mysqli->escape_string($_POST['username'])."' AND id != '0' LIMIT 1");
		while($row = $list->fetch_assoc()){
			// Check password for this user:
			if(pwhashing2($_POST['password'],$row['id']) != $row['userpassword']){
				break;
			}
			else
				$error = 0;

			// Session erstellen/abrufen / Lastlogin speichern
			if(empty($row['sessionhash'])){
				$sessionhash = sha1(mt_rand().time().$salt.$instnr.$row['id'].$row['username'].$row['startpage']);
				$mysqli->query("UPDATE ".$mysql_tables['user']." SET lastlogin='".time()."', sessionhash='".$sessionhash."' WHERE id='".$row['id']."' LIMIT 1");
			}
			else{
				$sessionhash = $row['sessionhash'];
				$mysqli->query("UPDATE ".$mysql_tables['user']." SET lastlogin='".time()."' WHERE id='".$row['id']."' LIMIT 1");
			}
			$_SESSION['01_idsession_'.sha1($salt)] = $row['id'];
			$_SESSION['01_passsession_'.sha1($salt)] = $sessionhash;
			
			// Cookie erstellen
			if(isset($_POST['setcookie']) && $_POST['setcookie'] == 1){
				if(empty($row['cookiehash'])){
					$cookiehash = sha1(mt_rand().time().$salt.$instnr);
					
					$mysqli->query("UPDATE ".$mysql_tables['user']." SET cookiehash='".$cookiehash."' WHERE id='".$row['id']."' LIMIT 1");
					$row['cookiehash'] = $cookiehash;
					}
				setcookie($instnr."_start_auth_01acp",$row['cookiehash'],time()+60*60*24*14);
				}
			
			//Weiterleiten:
			if($row['startpage'] == "01acp")
				$message = "<script type=\"text/javascript\">redirect(\"acp.php\");</script>";
			else
				$message = "<script type=\"text/javascript\">redirect(\"_loader.php?modul=".$row['startpage']."\");</script>";
			$message .= "<p class=\"meldung_ok\"><b>Login erfolgreich, Sie werden weitergeleitet.</b><br />Falls Ihr Browser keine Weiterleitung unterst&uuml;tzt klicken Sie bitte <a href=\"acp.php\">hier</a>.</p>";
			}
			
		if($error == 1)
			$message = "<p class=\"meldung_error\"><b>Login war nicht erfolgreich!</b><br />Bitte &uuml;berpr&uuml;fen Sie
				Benutzernamen und Passwort und probieren Sie es erneut.</p>";
		}
	else
		$message = "<p class=\"meldung_error\"><b>Login nicht m&ouml;glich!</b><br />
			Bitte f&uuml;llen Sie das Captcha korrekt aus!</p>"; 
    }
// �berpr�fen des Passwort-Zusenden-Formulars
elseif(isset($_POST['send']) && $_POST['send'] == 2){
	$list = $mysqli->query("SELECT id,username,mail FROM ".$mysql_tables['user']." WHERE (username='".$mysqli->escape_string($_POST['username'])."' OR mail='".$mysqli->escape_string($_POST['email'])."') AND id != '0' LIMIT 1");
	$menge = $list->num_rows;
	while($row = $list->fetch_assoc()){
        $newpass = create_NewPassword(PW_LAENGE);
        $newpassmd5 = pwhashing2($newpass, $row['id']);

        // Datenbank aktualisieren:
        $mysqli->query("UPDATE ".$mysql_tables['user']." SET userpassword='".$newpassmd5."' WHERE id='".$row['id']."'");

        $header = "From:".$settings['email_absender']."<".$settings['email_absender'].">\n";
        $email_betreff = $settings['sitename']." - Neues Passwort f�r Administrationsbereich";
        $emailbody = "Mit dieser E-Mail erhalten Sie ein neues Passwort f�r den Administrationsbereich\n\nName: ".stripslashes($row['username'])."\nE-Mail-Adresse: ".stripslashes($row['mail'])."\nNeues Passwort: ".$newpass."\n\n---\nWebmailer";

        mail(stripslashes($row['mail']),$email_betreff,$emailbody,$header);

        $message = "<p class=\"meldung_ok\"><b>Ein neues Passwort wurde an Ihre E-Mail-Adresse verschickt.</b></p>";
		}

	if($menge < 1)
		$message = "<p class=\"meldung_error\"><b>Passwort konnte nicht zugestellt werden!</b><br />Bitte &uuml;berpr&uuml;fen Sie
			Benutzernamen / E-Mail-Adresse und probieren Sie es erneut.</p>";
    }


include("system/head.php");
echo $message;
?>

<form action="<?PHP echo $filename; ?>" method="post">
<div class="box_centered" id="loginform" style="display:block;">
	<h2>Login</h2>
	
	<p class="big"><label for="input_username">Benutzername:</label> <input type="text" name="username" value="<?PHP echo $_POST['username']; ?>" id="input_username" size="20" /></p>
	<p class="big"><span style="margin-right:38px;"><label for="input_password">Passwort:</label></span> <input type="password" name="password" id="input_password" size="20" /></p>
<?php if($settings['acp_captcha4login'] == 1 && $settings['spamschutz'] == 2 && !empty($settings['ReCaptcha_PubKey']) &&  !empty($settings['ReCaptcha_PrivKey'])){ ?>
	<p class="big"><span style="margin-right:18px;"><?php echo create_Captcha($settings['spamschutz']); ?></span></p>
<?php }elseif($settings['acp_captcha4login'] == 1 && $settings['spamschutz'] == 1){ ?>
	<p class="big"><span style="margin-right:18px;"><?php echo create_Captcha(); ?></span> <input type="text" name="antispam" size="20" /></p>
<?php } ?>
	<p class="big"><span style="margin-right:18px;"><label for="input_cookie">2 Wochen eingeloggt bleiben?</label></span> <input type="checkbox" name="setcookie" id="input_cookie" value="1" /></p>
	<p class="small"><a href="javascript: hide_unhide('loginform'); hide_unhide('passwordbox');">Passwort vergessen?</a></p>
	<p align="right"><input type="submit" name="absenden" class="input" value="Einloggen &raquo;" /></p>

	<input type="hidden" name="send" value="1" />
</div>
</form>

<form action="<?PHP echo $filename; ?>" method="post">
<div class="box_centered" id="passwordbox" style="display:none;">
	<h2>Passwort vergessen?</h2>
	<p>Benutzername <b>ODER</b> E-Mail-Adresse eingeben:</p>
	
	<p class="big"><label for="forgotpw_username">Benutzername:</label> <input type="text" name="username" value="<?PHP if(isset($_POST['username'])){ echo $_POST['username']; } ?>" id="forgotpw_username" size="20" /></p>
	<p class="big"><label for="forgotpw_email">E-Mail-Adresse:</label> <input type="text" name="email" value="<?PHP if(isset($_POST['email'])){ echo $_POST['email']; } ?>" id="forgotpw_email" size="20" /></p>
	<p class="small"><a href="javascript: hide_unhide('loginform'); hide_unhide('passwordbox');">&laquo; Zur&uuml;ck zum Login</a></p>
	<p align="right"><input type="submit" name="absenden" class="input" value="Neues Passwort anfordern &raquo;" /></p>

	<input type="hidden" name="send" value="2" />
</div>
</form>

<p align="center">Um den Administrationsbereich nutzen zu k�nnen m�ssen Sie 
<a href="http://de.wikipedia.org/wiki/HTTP-Cookie" target="_blank">Cookies</a> aktiviert haben.</p>

<img src="images/layout/img04.gif" alt="Layout-Bild" style="display:none;" />

<?PHP
if($menge < 1 && isset($_POST['send']) && $_POST['send'] == 2){
	echo "<script type=\"text/javascript\">
		<!--
		hide_unhide('loginform');
		hide_unhide('passwordbox');
		-->
		</script>";
	}
?>

<?PHP
include("system/foot.php");
?>