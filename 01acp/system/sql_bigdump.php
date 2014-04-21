<?PHP
// Based on BigDump ver. 0.27b from 2006-11-24:
// Author:       Alexey Ozerov (alexey at ozerov dot de)
// Copyright:    GPL (C) 2003-2006
// More Infos:   http://www.ozerov.de/bigdump.php

// This program is free software; you can redistribute it and/or modify it under the
// terms of the GNU General Public License as published by the Free Software Foundation; 
// either version 2 of the License, or (at your option) any later version.

// Settings
$linespersession = 3000;               	// Lines to be executed per one import session
$delaypersession = 0;                  	// You can specify a sleep time in milliseconds after each session

// Allowed comment delimiters: lines starting with these strings will be dropped by BigDump
$comment[] = '#';           			// Standard comment lines are dropped by default
$comment[] = '-- ';

// Connection character set should be the same as the dump file character set (utf8, latin1, cp1251, koi8r etc.)
// See http://dev.mysql.com/doc/refman/5.0/en/charset-charsets.html for the full list
$db_connection_charset = 'latin1';

$sql_start = 1;
$sql_totalqueries = 0;
$sql_foffset = 0;
define ('DATA_CHUNK_LENGTH',16384);  // How many chars are read per time
define ('MAX_QUERY_LINES',300);      // How many lines may be considered to be one query (except text lines)

$error = FALSE;
$file = FALSE;

// max_upload_size kalkulieren
$upload_max_filesize = ini_get("upload_max_filesize");
if(preg_match("/([0-9]+)K/i",$upload_max_filesize,$tempregs)) $upload_max_filesize = $tempregs[1]*1024;
if(preg_match("/([0-9]+)M/i",$upload_max_filesize,$tempregs)) $upload_max_filesize = $tempregs[1]*1024*1024;
if(preg_match("/([0-9]+)G/i",$upload_max_filesize,$tempregs)) $upload_max_filesize = $tempregs[1]*1024*1024*1024;

//Charset setzten
$result = $mysqli->get_charset();
if(!$error && $db_connection_charset !== "" && $result->charset != "latin1") $mysqli->set_charset($db_connection_charset);

// sql-file öffnen
if(!$error && isset($sql_filename)){
    if(!$file = fopen($sql_filename,"rt")){
		echo "<p class=\"meldung_error\">Die Datei ".$sql_filename." konnte nicht gefunden werden!</p>\n";
		$error = TRUE;
		}
    elseif(fseek($file, 0, SEEK_END)==0)
        $filesize = ftell($file);
    else{
        echo "<p class=\"meldung_error\">Fehler: Die Dateigr&ouml;&szlig;e von ".$sql_filename." ist zu gro&szlig;!</p>\n";
        $error = TRUE;
        }
    }

	
	
	
	
// ****************************************************
// START IMPORT SESSION HERE
// ****************************************************

if(!$error && isset($sql_start) && isset($sql_foffset) && preg_match("/(\.(sql|gz))$/i",$sql_filename)){
    $sql_start = floor($sql_start);
    $sql_foffset = floor($sql_foffset);
    }

// Check $_REQUEST["foffset"] upon $filesize
if(!$error && $sql_foffset > $filesize){
    echo "<p class=\"meldung_error\">Fehler: Dateianzeiger kann nicht hinter das Ende der Datei gesetzt werden.</p>\n";
    $error = TRUE;
    }
else{
    // Set file pointer to $_REQUEST["foffset"]
    fseek($file, $sql_foffset);
    }


	
	
// Start processing queries from $file
if(!$error){
    $query = "";
    $queries = 0;
    $totalqueries = $sql_totalqueries;
    $linenumber = $sql_start;
    $querylines = 0;
    $inparents = false;

// Stay processing as long as the $linespersession is not reached or the query is still incomplete
    while($linenumber < $sql_start+$linespersession || $query != ""){
        // Read the whole next line
        $dumpline = "";
        while(!feof($file) && substr($dumpline, -1) != "\n"){
            $dumpline .= fgets($file, DATA_CHUNK_LENGTH);
            }
        if($dumpline === "") break;
      
        // Diverse ereg_replace-Operationen
        $dumpline = parse_SQLLines($dumpline,$sql_modulnr,$sql_modulidname);

        //Skip comments and blank lines only if NOT in parents
        if(!$inparents){
            $skipline = FALSE;
            reset($comment);
			
            foreach($comment as $comment_value){
                if(!$inparents && (trim($dumpline) == "" || strpos ($dumpline, $comment_value) === 0)){
                    $skipline = TRUE;
                    break;
                    }
                }
            if($skipline){
                $linenumber++;
                continue;
                }
            }

        // Remove double back-slashes from the dumpline prior to count the quotes ('\\' can only be within strings)
        $dumpline_deslashed = str_replace("\\\\","",$dumpline);

        // Count ' and \' in the dumpline to avoid query break within a text field ending by ;
        // Please don't use double quotes ('"')to surround strings, it wont work
        $parents = substr_count($dumpline_deslashed, "'")-substr_count($dumpline_deslashed, "\\'");
        if($parents % 2 != 0) $inparents =! $inparents;

        //Add the line to query
        $query .= $dumpline;

        //Don't count the line if in parents (text fields may include unlimited linebreaks)
        if(!$inparents) $querylines++;
      
        //Stop if query contains more lines as defined by MAX_QUERY_LINES
        if($querylines > MAX_QUERY_LINES){
            echo "<p class=\"meldung_error\">Fehler: Gestoppt in Zeile ".$linenumber."
            At this place the current query includes more than ".MAX_QUERY_LINES." dump lines. That can happen if your dump file was
            created by some tool which doesn't place a semicolon followed by a linebreak at the end of each query, or if your dump contains
            extended inserts.</p>\n";
            $error = TRUE;
            break;
            }

        //Execute query if end of query detected (; as last character) AND NOT in parents
        if(preg_match("/;$/",trim($dumpline)) && !$inparents){
            if(!$mysqli->query(trim($query))){
                echo "<p class=\"meldung_error\">Fehler in Zeile ".$linenumber.": ". trim($dumpline)."<br /><br />\n";
                echo "Query: ".trim(nl2br(htmlentities($query),$htmlent_flags,$htmlent_encoding_acp))."<br />\n";
                echo "MySQL: ".$mysqli->error."</p>\n";
                $error = TRUE;
                break;
                }
            $totalqueries++;
            $queries++;
            $query = "";
            $querylines = 0;
            }
        $linenumber++;
        }
    }

// Get the current file position
if(!$error){
    $foffset = ftell($file);
    if(!$foffset){
        echo "<p class=\"meldung_error\">Fehler: Filepointer Offset kann nicht gelesen werden.</p>\n";
        $error = true;
        }
    }

if(!$error){
    // Finish message
    if($linenumber < $sql_start+$linespersession){
        echo "<p class=\"meldung_erfolg\">MySQL-Querys erfolgreich ausgeführt!\n";
        echo $sql_erfolglink."</p>";
        }
    }
else echo "<p class=\"meldung_error\">Fehler: gestoppt</p>\n";
fclose($file);
?>