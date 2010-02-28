	<?PHP
	if($flag_loginerror && $userdata['sperre'] == 1)
		echo "<p class=\"meldung_error\"><b>Fehler: Ihr Benutzeraccount wurde gesperrt. Sie können sich nicht mehr einloggen.</b><br />
			Bitte wenden Sie sich an Ihren zuständigen Ansprechpartner. [<a href=\"index.php?action=logout\">Logout</a>]</p>";
	elseif($flag_loginerror)
		echo "<div class=\"meldung_error\"><b>Fehler: Fehlende Berechtigung</b><br />
			Sie haben keine Berechtigung diesen Bereich zu betreten. Dies kann an einem der folgenden Gr&uuml;nde liegen:
			<ul>
		        <li>Sie haben sich nicht angemeldet. Diese Funktion steht ausschlie&szlig;lich angemeldeten Benutzern zur Verf&uuml;gung, bitte melden Sie sich <a href=\"index.php\">im System an</a>.</li>
		        <li>Sie konnten nicht eingeloggt werden, weil Ihr Browser keine Cookies akzeptiert. Bitte akzeptieren Sie f&uuml;r diese Seite Cookies.</li>
		        <li>Es liegt ein Fehler im System vor. Verst&auml;ndigen Sie in diesem Fall den Administrator der Seite.</li>
			</ul>
	        </div>";
	        
	if($flag_credits || $flag_feedback)
	    echo "<p class=\"small\" align=\"right\">";
	if($flag_credits)
		echo "<a href=\"javascript:popup('credits','','','',500,450);\">Credits</a>";
    if($flag_feedback)
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:popup('feedback','','','',500,350);\">Feedback</a>";
	if($flag_credits || $flag_feedback)
	    echo "</p>";
	?>
	</div>
</div>

<div id="footer">
	<p>&copy; 2006-<?PHP echo date("Y"); ?> by <a href="http://www.01-scripts.de" target="_blank">01-Scripts.de</a></p>
</div>

</body>
</html>