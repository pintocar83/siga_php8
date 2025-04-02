<?php
/*
Proceso para crear la base de datos registro_civil_ve.sqlite que contiene
una parte de los numeros de cedulas, nombres y apellidos.
Esta base de datos es usada por los modulos ficha y beneficiarios a la hora de registrar
nuevas personas y se encuentra configurada en library/siga.config.php en "base".

El proceso de restauracion es lento, y de debe ejecutar en un terminal. La ejecucion desde el
navegador arroja timeout porque supera los 10min.

1)Extrar en la carpeta actual registro_civil_ve.part01.rar, esto arrojará registro_civil_ve.csv

2)Entramos en la consola del contenedor web:
docker compose exec web /bin/bash

3)Una vez dentro, ingresamos a la carpeta data/ para ejecutar script regsitro_civil_ve.php
cd /app/data/
php registro_civil.ve.php

3)Una vez culminado la ejecucion del script, mostrará: 
'-Finalizo' y verificamos la creacion del archivo registro_civil_ve.sqlite con tamaño aproximado a 1.1Gb


El proceso de creación/importación en un 'Intel(R) Core(TM) i7-6700T CPU @ 2.80GHz' con 16Gb de Ram duró aproximadamente 14min.

*/


error_reporting(E_ALL);//error_reporting(E_ALL|E_STRICT);
ini_set("display_errors","On");
set_time_limit(-1);

header("Content-Type: text/plain; charset=UTF-8");
$time_start = microtime(true);
$file_csv="registro_civil_ve.csv";
$file_db="registro_civil_ve.sqlite";

if(file_exists($file_db)){
    print "Ya existe la base de datos registro_civil_ve.sqlite\n\n";
    exit;
}

if(!file_exists($file_csv)){
    print "No existe la data CSV de importación\n\n";
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
    $line=str_replace(["' ", "'"], " ", $line);
    $line=str_replace('"', "", $line);
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


$time_end = microtime(true);
$time = round($time_end - $time_start, 0);
print "\n-Finalizo";
print "\n[Tiempo de ejecución $time segundos]";
print "\n";
?>