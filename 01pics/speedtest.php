<?PHP
$filename = "1218213234.jpg";


function getEndung($filestring){

if(!empty($filestring)){
	$endungsarray = explode(".",$filestring);
	$endung = $endungsarray[count($endungsarray)-1];
	
	return strtolower($endung);
	}
else{
	return "";
	}
}


/*for($x=0;$x<2000;$x++){
	echo "<img src=\"showpics.php?img=".$filename."&amp;size=200\" /><br />";
	}*/
	
	
$verz=opendir("."); //Name des Verzeichnisses angeben, welches geöffnet werden soll
                    //Bei einem . wird das Verzeichnis in dem sich die Datei befindet aufgelistet
$linkl = array ("0");
//Hiermit wird das Verzeichnis aufgelistet:
while($file = readdir($verz))
    {
    if($file != "." && $file != ".." && $file != $PHP_SELF && (getEndung($file) == "jpg"))
        {
        //Alle Ordner/Files werden in den Array geschrieben (immer ans Ende):
        array_push ($linkl, $file);
        }
    }

//Es wird gezählt wieviele Elemente im Array sind:
$anzahl = count($linkl); 
$time = microtime();
//Der Array wird ausgegeben:
for($x=1;$x<=$anzahl;$x++)
    {
    echo "<img src=\"showpics.php?img=".$linkl[$x]."&amp;size=200\" />";
    }

	
	
echo microtime()-$time;
closedir($verz);
?>