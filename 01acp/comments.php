<?PHP
/* 
	01ACP - Copyright 2008-2013 by Michael Lorer - 01-Scripts.de
	Lizenz: Creative-Commons: Namensnennung-Keine kommerzielle Nutzung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland
	Weitere Lizenzinformationen unter: http://www.01-scripts.de/lizenz.php
	
	Modul:		01ACP
	Dateiinfo:	Kommentare verwalten
	#fv.130#
*/

$menuecat = "01acp_comments";
$sitetitle = "Kommentare verwalten";
$mootools_use = array("moo_core","moo_more","moo_slideh","moo_request");

// Config-Dateien
include("system/main.php");
include("system/head.php");

$filename = $_SERVER['SCRIPT_NAME']."?modul=".$modul."";

// Sicherheitsabfrage: Login
if(isset($userdata['id']) && $userdata['id'] > 0 && $userdata['editcomments'] == 1){

echo "<h1>Kommentare verwalten</h1>";

echo "<p class=\"meldung_erfolg\" id=\"del_erfolgsmeldung\" style=\"display:none;\">
	Der Kommentar wurde erfolgreich gel&ouml;scht.
</p>";

if($modul != "01acp"){

	// Selektierte Kommentare l�schen
	if(isset($_POST['cdelid']) && !empty($_POST['cdelid']) && 
	   isset($_POST['delselected']) && $_POST['delselected'] == 1){
		$cup = 0;
		foreach($_POST['cdelid'] as $delid){
			$mysqli->query("DELETE FROM ".$mysql_tables['comments']." WHERE modul='".$mysqli->escape_string($modul)."' AND id='".$mysqli->escape_string($delid)."' LIMIT 1");
			$cup++;
			}
		echo "<p class=\"meldung_erfolg\">Es wurden ".$cup." Kommentare gel&ouml;scht</p>";
		}
	
	// Alle Kommentare eines Beitrags l�schen
	if(isset($_GET['delpostid']) && !empty($_GET['delpostid']) && isset($_GET['do']) && $_GET['do'] == "dodelall"){
		if(isset($_GET['delsubpostid']) && !empty($_GET['delsubpostid']) && $_GET['delsubpostid'] != 0)
			delSubPostComments($_GET['delpostid'],$_GET['delsubpostid']);
		else
			delComments($_GET['delpostid']);
		
		echo "<p class=\"meldung_erfolg\">Kommentare wurden gel&ouml;scht</p>";
		}
		
	
	if(isset($_GET['postid']) && !empty($_GET['postid']) && isset($_GET['do']) && $_GET['do'] == "deleteall"){
		echo "<p class=\"meldung_frage\">M&ouml;chten Sie wirklich alle zum Eintrag <i>".stripslashes($_GET['ptitel'])."</i>
			geh&ouml;renden <b>Kommentare komplett l&ouml;schen?</b><br />
			<br />
			<a href=\"".$filename."&amp;do=dodelall&amp;delsubpostid=".$_GET['subpostid']."&amp;delpostid=".$_GET['postid']."&amp;commentsites=".$_GET['commentsites']."\">JA</a> | <a href=\"javascript:history.back();\">Nein</a></p>";
		}
	// Kommentare zu einem bestimmten Post / Subpost auflisten
	elseif(isset($_GET['postid']) && !empty($_GET['postid'])){
		// Kommentare zu Sub-Posts auflisten:
		if(isset($_GET['subpostid']) && !empty($_GET['subpostid'])){
			$sites = 0;
			$query = "SELECT id,postid,subpostid,frei,utimestamp,ip,autor,email,message,smilies,bbc FROM ".$mysql_tables['comments']." WHERE postid='".$mysqli->escape_string($_GET['postid'])."' AND subpostid='".$mysqli->escape_string($_GET['subpostid'])."' AND modul='".$mysqli->escape_string($modul)."' ORDER BY utimestamp DESC";
			$query = makepages($query,$sites,"site",ACP_PER_PAGE);		
			$list = $mysqli->query($query);
			
			echo "<h2>Kommentare bearbeiten <img src=\"images/icons/icon_edit.gif\" alt=\"Stift + Block\" title=\"bearbeiten\" /></h2>";
			echo "<p><a href=\"".$filename."&amp;showsub=1&amp;postid=".$_GET['postid']."\">&laquo; Zur&uuml;ck</a></p>";
			
			echo "<p><a href=\"".$filename."&amp;postid=".$_GET['postid']."&amp;subpostid=".$_GET['subpostid']."&amp;do=deleteall&amp;ptitel=\">Alle zu diesem Eintrag geh&ouml;renden Kommentare l&ouml;schen &raquo;</a></p>";
			
			echo getCommentList($query,"free");
			
			echo echopages($sites,"80%","site","modul=".$modul."&amp;postid=".$_GET['postid']."&amp;subpostid=".$_GET['subpostid']);
			}
		// Sub-POSTS selber auflisten:
		elseif(isset($_GET['showsub']) && $_GET['showsub'] == 1 && function_exists("_".$module[$modul]['modulname']."_getCommentChildTitle")){
			$csites = 0;
			$menge = 0;
			$query = "SELECT DISTINCT subpostid FROM ".$mysql_tables['comments']." WHERE frei = '1' AND modul='".$mysqli->escape_string($modul)."' AND postid = '".$mysqli->escape_string($_GET['postid'])."' ORDER BY (subpostid*'1') DESC";
			$query = makepages($query,$csites,"commentsites",ACP_PER_PAGE);
			
			echo "<h2>Kommentare bearbeiten <img src=\"images/icons/icon_edit.gif\" alt=\"Stift + Block\" title=\"bearbeiten\" /></h2>";
			
			echo "<p><a href=\"".$filename."\">&laquo; Zur&uuml;ck</a></p>";
			
			echo getCommentPostList($query,"child");
			
			echo echopages($csites,"80%","commentsites","modul=".$modul."&amp;postid=".$_GET['postid']."&amp;showsub=1");
			}
		// Kommentare zu Posts auflisten:
		else{
			$sites = 0;
			$query = "SELECT id,postid,frei,utimestamp,ip,autor,email,message,smilies,bbc FROM ".$mysql_tables['comments']." WHERE postid='".$mysqli->escape_string($_GET['postid'])."' AND modul='".$mysqli->escape_string($modul)."' ORDER BY utimestamp DESC";
			$query = makepages($query,$sites,"site",ACP_PER_PAGE);		
			$list = $mysqli->query($query);
			
			echo "<h2>Kommentare bearbeiten <img src=\"images/icons/icon_edit.gif\" alt=\"Stift + Block\" title=\"bearbeiten\" /></h2>";
			echo "<p><a href=\"".$filename."\">&laquo; Zur&uuml;ck</a></p>";
			
			echo "<p><a href=\"".$filename."&amp;postid=".$_GET['postid']."&amp;do=deleteall&amp;ptitel=\">Alle zu diesem Eintrag geh&ouml;renden Kommentare l&ouml;schen &raquo;</a></p>";
			
			echo getCommentList($query,"free");
			
			echo echopages($sites,"80%","site","modul=".$modul."&amp;postid=".$_GET['postid']);
			}
		}
	else{
		// Kommentare, die freigeschaltet werden m�ssen auflisten:
		$sites = 0;
		$menge = 0;
		$query = "SELECT id,postid,utimestamp,ip,autor,email,message,smilies,bbc FROM ".$mysql_tables['comments']." WHERE frei = '0' AND modul='".$mysqli->escape_string($modul)."' ORDER BY utimestamp DESC";
		$query = makepages($query,$sites,"commentfreesite",ACP_PER_PAGE);
		
		$list = $mysqli->query($query);
		$menge = $list->num_rows;
		
		if($menge > 0){
		
			echo "<h2>Kommentare freischalten <img src=\"images/icons/ok.gif\" alt=\"OK\" title=\"freischalten\" /></h2>";
			echo getCommentList($query,"free");
			
			echo echopages($sites,"80%","commentfreesite","modul=".$modul."");
			}

		// Posts mit Kommentaren auflisten:
		$csites = 0;
		$menge = 0;
		$query = "SELECT DISTINCT postid FROM ".$mysql_tables['comments']." WHERE frei = '1' AND modul='".$mysqli->escape_string($modul)."' ORDER BY (postid*'1') DESC";
		$query = makepages($query,$csites,"commentsites",ACP_PER_PAGE);
		
		echo "<h2>Kommentare bearbeiten <img src=\"images/icons/icon_edit.gif\" alt=\"Stift + Block\" title=\"bearbeiten\" /></h2>";
		
		echo getCommentPostList($query);
		
		echo echopages($csites,"80%","commentsites","modul=".$modul."");
		}// Ende: Auflistung der Kommentare zu einem bestimmten Post
	}
else{
	echo "<div class=\"meldung_hinweis\"><p><b>Bitte w&auml;hlen Sie ein Modul</b></p>".
		create_ModulForm($_SERVER['SCRIPT_NAME']."?","input",TRUE)."</div>";
	}


}else $flag_loginerror = true;
include("system/foot.php");

?>