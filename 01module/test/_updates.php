<?PHP
if(isset($_REQUEST['update']) && $_REQUEST['update'] == "3000_zu_30001"){

mysql_query("UPDATE ".$mysql_tables['module']." SET version = '3.0.0.1' WHERE idname = '".mysql_real_escape_string($modul)."' LIMIT 1");
?>
<h2>Update Version 3.0.0.0 nach 3.0.0.1</h2>

<div class="meldung_erfolg">
	Das Update von Version 3.0.0.0 auf Version 3.0.0.1 wurde erfolgreich durchgef&uuml;hrt.<br />
	<br />
	<a href="module.php">Zur&uuml;ck zur Modul-&Uuml;bersicht &raquo;</a>
</div>
<?PHP
	}
?>