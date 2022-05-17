<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
/*include_once("library/db.controller.php");
include_once("library/siga.config.php");
include_once("library/siga.class.php");
include_once("library/functions/formatDate.php");
include_once("library/functions/sql_query_total.php");
include_once("library/fpdf/1.7/fpdf.php");
include_once("class/nomina.class.2.php");

$db=SIGA::DBController();



//$tmp=nomina::fichas(1,83);

$tmp=nomina::ficha_concepto(1,83,5);

//
print_r($tmp);
*/

$fecha_inicio="2018-10-08";
$fecha_culminacion="2018-10-14";

$n_dias=abs(floor((strtotime($fecha_culminacion)-strtotime($fecha_inicio))/(60*60*24))+1);


print $ndias;

$fecha_inicio_nueva=date("Y-m-d",strtotime($fecha_culminacion)+60*60*24);
$fecha_culminacion_nueva=date("Y-m-d",strtotime($fecha_culminacion)+60*60*24*7);

print "<br>";
print $fecha_inicio_nueva;
print "<br>";
print $fecha_culminacion_nueva;

$fecha=explode("-",$fecha_inicio_nueva);
$n=intval(($fecha[2]*1/7)+1);
print "<br>";
print $n;
?>