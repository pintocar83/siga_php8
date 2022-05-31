<?php
error_reporting(E_ALL);//error_reporting(E_ALL|E_STRICT);
ini_set("display_errors","On");
set_time_limit(-1);

header("Content-Type: text/plain; charset=UTF-8");
$file_csv="registro_civil_ve.csv";
$file_db="registro_civil_ve.sqlite";

if(file_exists($file_db)){
    print "Ya existe la base de datos registro_civil_ve.sqlite";
    exit;
}

if(!file_exists($file_csv)){
    print "No existe la data CSV de importación";
    exit;
}


$db = new SQLite3($file_db);
print "\n-Creando tabla";
$db->exec("CREATE TABLE persona (
    nacionalidad     CHAR (1),
    cedula           INTEGER,
    primer_apellido  VARCHAR (50),
    segundo_apellido VARCHAR (50),
    primer_nombre    VARCHAR (50),
    segundo_nombre   VARCHAR (50),
    PRIMARY KEY (
        nacionalidad,
        cedula
    )
)");

print "\n-Importando...";
$f=@fopen($file_csv, "r");

$n=1;
$block="";
while(($line=fgets($f)) !== false) {
    $line=explode(";",trim($line));
    $block.="(\"".$line[0]."\",".$line[1].",\"".$line[2]."\",\"".$line[3]."\",\"".$line[4]."\",\"".$line[5]."\"),";
    if($n%10000==0){
        $db->exec(SQLite3::escapeString("INSERT INTO persona VALUES ".substr($block, 0, -1)));
        $block="";
    }
    $n++;
}
$db->exec(SQLite3::escapeString("INSERT INTO persona VALUES ".substr($block, 0, -1)));
$block="";
//18903143 Registros
fclose($f);
$db->close();
print "\n-Finalizo";
?>