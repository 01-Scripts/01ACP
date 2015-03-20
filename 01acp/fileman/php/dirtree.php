<?php
/*
  RoxyFileman - web based file manager. Ready to use with CKEditor, TinyMCE. 
  Can be easily integrated with any other WYSIWYG editor or CMS.

  Copyright (C) 2013, RoxyFileman.com - Lyubomir Arsov. All rights reserved.
  For licensing, see LICENSE.txt or http://RoxyFileman.com/license

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  Contact: Lyubomir Arsov, liubo (at) web-lobby.com
*/
$subfolder = "../../../";
include("../../system/headinclude.php");
include '../system.inc.php';
include 'functions.inc.php';

$shasalt = sha1($salt);
if(isset($_SESSION['01_idsession_'.$shasalt]) && isset($_SESSION['01_passsession_'.$shasalt]) && !empty($_SESSION['01_passsession_'.$shasalt]) && strlen($_SESSION['01_passsession_'.$shasalt]) == 40){
    $userdata = getUserdata("",TRUE);
    if($userdata['sperre'] == 1){
        $flag_loginerror = true;
        break;
    }
}

verifyAction('DIRLIST');
checkAccess('DIRLIST');

$type = (empty($_GET['type'])?'':strtolower($_GET['type']));
if($type == "image")
    $type = "pic";
elseif($type == "files" || $type == "flash")
    $type = "file";
else
    $type = "all";

/* $parentid          Verzeichnis-ID des übergeordneten Verzeichnisses
   $deep              Aktuelle Tiefe
   $callfunction      Funktion zur eigentlichen Ausgabe der Daten
   $givedeeperparam   Parameter wird zum Aufbau des Verzeichnispfads verwendet
*/
function getFileVerz_Rek2($parentid,$deep=0,$callfunction="",$givedeeperparam="Dateimanager"){
global $mysqli,$mysql_tables;
$return = "";

$list = $mysqli->query("SELECT * FROM ".$mysql_tables['filedirs']." WHERE parentid = '".$mysqli->escape_string($parentid)."' ORDER BY name");
while($row = $list->fetch_assoc()){
    if(!empty($callfunction) && function_exists($callfunction)) $return .= call_user_func($callfunction,$row,$deep,$givedeeperparam);

    // Rekursion
    $return .= getFileVerz_Rek2($row['id'],($deep+1),$callfunction,$givedeeperparam."/".$row['name']);
    }

return $return;
}

function echo_FileVerz_4roxy($row,$deep,$para=""){
    return ',{"p":"'.  mb_ereg_replace('"', '\\"', $para."/".$row['name']).'","f":"'.echoFileCount($row['id']).'","d":"'.echoDirCount($row['id']).'"}';
}

function echoFileCount($dir){
    global $filecount;

    if(isset($filecount[$dir])) return $filecount[$dir];
    else return 0;
}

function echoDirCount($dir){
    global $dircount;

    if(isset($dircount[$dir])) return $dircount[$dir];
    else return 0;
}

// Count files:
if($type == "all")
    $list = $mysqli->query("SELECT dir, COUNT(*) FROM ".$mysql_tables['files']." GROUP BY dir");
else
    $list = $mysqli->query("SELECT dir, COUNT(*) FROM ".$mysql_tables['files']." WHERE filetype = '".$type."' GROUP BY dir");
while($row = $list->fetch_assoc()){
    $filecount[$row['dir']] = $row['COUNT(*)'];
}

// Count directories:
$list = $mysqli->query("SELECT parentid, COUNT(*) FROM ".$mysql_tables['filedirs']." GROUP BY parentid");
while($row = $list->fetch_assoc()){
    $dircount[$row['parentid']] = $row['COUNT(*)'];
}

echo "[\n";
echo '{"p":"Dateimanager","f":"'.echoFileCount(0).'","d":"'.echoDirCount(0).'"}';
echo getFileVerz_Rek2(0,0,"echo_FileVerz_4roxy");
echo "\n]";
?>