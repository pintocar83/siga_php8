<?php
header("Content-Type: text/plain; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/sql_query_total.php");
include_once("../library/fpdf/1.84/fpdf.php");
include_once("../class/nomina.class.php");



$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
if(count($id_periodo)!=1){
	print "Actualmente solo puede seleccionar un periodo.";
	exit;
}
$id_periodo=$id_periodo[0];

$db=SIGA::DBController();

//buscar detalles del periodo
$periodo=$db->Execute("SELECT id, codigo, fecha_inicio, fecha_culminacion, tipo, descripcion FROM modulo_nomina.periodo WHERE id=$id_periodo");
$periodo=$periodo[0];

$id_nomina=explode(",",SIGA::paramGet("id_nomina"));
$nomina=array();
//buscar las nóminas asociadas al periodo
for($i=0;$i<count($id_nomina);$i++){
	$nomina[$i]=$db->Execute("SELECT id, codigo, nomina FROM modulo_nomina.nomina WHERE id=".$id_nomina[$i]);
	$nomina[$i]=$nomina[$i][0];
}

$retorno=nomina::detalle_presupuestario_contable($periodo,$nomina);

if(!$retorno["success"]){
	print $retorno["message"];
	exit;
}

print $retorno["concepto"];
for($i=0;$i<count($retorno["detalle"]["presupuestario"]);$i++){
	$detalle=$retorno["detalle"]["presupuestario"][$i];
	print $detalle["estructura_presupuestaria"]." ".$detalle["id_cuenta_presupuestaria"]." ".$detalle["operacion"]." ".$detalle["monto"]."\n";
}

for($i=0;$i<count($retorno["detalle"]["contable"]);$i++){
	$detalle=$retorno["detalle"]["contable"][$i];
	print $detalle["id_cuenta_contable"]." ".$detalle["operacion"]." ".$detalle["monto"]."\n";
}
$detalle=$retorno["detalle"]["comprobante_bancario"];
print $detalle["numero_cuenta"]." ".$detalle["operacion"]." ".$detalle["monto"]."\n";


?>