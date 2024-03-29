<?PHP
/* 
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Startseite des ACP-Bereich
	#fv.130#
*/

$menuecat = "01acp_start";
$sitetitle = "Startseite";
$mootools_use = array("moo_core","moo_more","moo_slideh","moo_request");
$filename = $_SERVER['SCRIPT_NAME'];


// Config-Dateien
include("system/main.php");
include("system/head.php");

// Sicherheitsabfrage: Login
if(isset($userdata['id']) && $userdata['id'] > 0){
include_once("system/includes/tt.php");

// 01-Scripts.de RSS-Feed neu vom Server holen
if($settings['cachetime']+CACHE_TIME_01RSS < time() && $flag_showacpRSS){
	getXML2Cache(ACPSTART_RSSFEED_URL,RSS_CACHEFILE);
	getXML2Cache(HTTP_VERSIONSINFO_DATEILINK,LOKAL_VERSIONSINFO_DATEILINK);
	}

// Neue Versions-XML vom 01-Scripts.de-Server holen
if($settings['cachetime']+CACHE_TIME < time() && $userdata['module'] == 1){
	getXML2Cache(HTTP_VERSIONSINFO_DATEILINK,LOKAL_VERSIONSINFO_DATEILINK);
	}

$xml = @simplexml_load_file(LOKAL_VERSIONSINFO_DATEILINK);
if($xml && $xml->_01acp->version > $settings['acpversion'] && $userdata['module'] == 1)
	$newerversion = " <a href=\"".$xml->_01acp->updateurl."\" target=\"_blank\"><b class=\"yellow\">Update verf�gbar!</b></a>";
else
	$newerversion = ""; 

?>

<div class="acp_startbox">
<p align="center">Herzlich Willkommen im <b class="yellow">A</b>dmin <b class="yellow">C</b>ontrol <b class="yellow">P</b>anel</p>

<div class="acp_doubleinnerbox">
	<p style="float:left; margin-right:15px; margin-top:17px;"><b>Was m�chten Sie tun?</b></p>
	<br />
	<form action="_loader.php" method="get">
		<select name="modul" size="1" class="input_select2" onchange="location.href='_loader.php?modul='+this.options[this.selectedIndex].value+''">
			<?PHP echo create_ModulDropDown(TRUE); ?>
		</select>
		<input name="input2" type="submit" class="input2" value="Modul ausw&auml;hlen und arbeiten &raquo;" />
	</form>
</div>

<div class="acp_innerbox">
	<h4>Informationen</h4>
	<p>
		<b>01ACP-Version:</b> <?PHP echo $settings['acpversion'].$newerversion; ?><br />
		<br />
		<b>Benutzer:</b> <?PHP list($usermenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['user']." WHERE id!='0'")->fetch_array(MYSQLI_NUM); echo $usermenge; ?><br />
		<b>Dateien:</b> <?PHP list($filemenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['files']." WHERE filetype='file'")->fetch_array(MYSQLI_NUM); echo $filemenge; ?><br />
		<b>Bilder:</b> <?PHP list($picmenge) = $mysqli->query("SELECT COUNT(*) FROM ".$mysql_tables['files']." WHERE filetype='pic'")->fetch_array(MYSQLI_NUM); echo $picmenge; ?>
		<br /><br />
		<a href="http://board.01-scripts.de" target="_blank">Supportforum &amp; FAQ &raquo;</a><br />
		<a href="http://www.01-scripts.de/contact.php" target="_blank">Kontakt &raquo;</a><br />
		<br />
		<a href="http://www.01-scripts.de/lizenz.php" target="_blank">Lizenzbestimmungen &raquo;</a>
	</p>
</div>
<div class="acp_innerbox">
<?PHP 
if($flag_showacpRSS && ACPSTART_RSSFEED_URL != "")
	$rss01 = @simplexml_load_file(RSS_CACHEFILE);
else
	$rss01 = FALSE;

if($flag_showacpRSS && ACPSTART_RSSFEED_URL != "" && $rss01){  
	echo "<h4>01-Scripts.de - News</h4>";
	for($x=0;$x<4;$x++){
		echo "<p>".substr($rss01->channel->item->$x->pubDate,0,25)."<br />";
		echo "<a href=\"".$rss01->channel->item->$x->link."\" target=\"_blank\"><b>";
		echo utf8_decode($rss01->channel->item->$x->title);
		echo "</b></a></p>";
		}
	}
else{
	echo "<h4>Tipps &amp; Tricks</h4>";
	
	$rand_tt = array_rand($tt, 3);
	
	echo "<ul>\n";
	foreach($rand_tt as $tippnr){
		echo "<li>".$tt[$tippnr]."</li>\n";
	}
	echo "\n</ul>";
	}
 ?>
</div>
<br />

</div>
<?PHP
$rand_tt = array_rand($tt, 1);
echo "<p align=\"center\"><b>Tipp:</b> ".$tt[$rand_tt]."</p>";

}else $flag_loginerror = true;
include("system/foot.php");
?>