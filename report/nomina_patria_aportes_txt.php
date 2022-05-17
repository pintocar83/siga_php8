<?php
header ('Content-type: text/plain; charset=utf-8');
error_reporting(0);
ini_set('display_errors', 0);
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/sql_query_total.php");
include_once("../class/nomina.class.php");

$db=SIGA::DBController();


$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
$id_nomina=explode(",",SIGA::paramGet("id_nomina"));
$id_concepto=explode(",",SIGA::paramGet("id_concepto"));



$periodo=$db->Execute("SELECT id, codigo, fecha_inicio, fecha_culminacion, descripcion FROM modulo_nomina.periodo WHERE id IN (".SIGA::paramGet("id_periodo").") ORDER BY codigo");
//cuenta contable periodo nomina

$concepto=array();
$nc=0;

$ficha=array();

$DATA_D=[];
$DATA_AP=[];

$TOTAL_D=0;
$TOTAL_AP=0;

//buscar todos los conceptos existentes en la n¨®mina
for($i=0;$i<count($id_nomina);$i++):
	//Buscar nombre de la n¨®mina
	$nomina[$i]=$db->Execute("SELECT codigo, nomina FROM modulo_nomina.nomina WHERE id=".$id_nomina[$i]);
	$nomina[$i]=$nomina[$i][0];
	$nomina[$i]["ficha"]=array();
	
	for($p=0;$p<count($periodo);$p++):	
		$nomina[$i]["ficha"][$p]=nomina::fichas($id_nomina[$i],$id_periodo[$p]);

		//buscar los concepto existentes
		for($j=0;$j<count($nomina[$i]["ficha"][$p]);$j++):
			//agrupar fichas id
			$ficha_existe=false;
			for($k=0;$k<count($ficha);$k++)
				if($nomina[$i]["ficha"][$p][$j]["id"]==$ficha[$k]["id"]){
					$ficha_existe=true;
					break;
				}
			if(!$ficha_existe)
				$ficha[]=array(
											 "id"=>$nomina[$i]["ficha"][$p][$j]["id"],
											 "nacionalidad"=>$nomina[$i]["ficha"][$p][$j]["nacionalidad"],
											 "cedula"=>$nomina[$i]["ficha"][$p][$j]["cedula"],
											 "nombre_apellido"=>$nomina[$i]["ficha"][$p][$j]["nombre_apellido"]
											 );

			//para los conceptos de la ficha
			for($k=0;$k<count($nomina[$i]["ficha"][$p][$j]["concepto"]);$k++):		
				if($nomina[$i]["ficha"][$p][$j]["concepto"][$k]["tipo"]!=""){
					if(!(in_array($nomina[$i]["ficha"][$p][$j]["concepto"][$k]["id"], $id_concepto)))
						continue;
					if(!($nomina[$i]["ficha"][$p][$j]["concepto"][$k]["tipo"]=="AP" or $nomina[$i]["ficha"][$p][$j]["concepto"][$k]["tipo"]=="D"))
						continue;

					$identificacion=$nomina[$i]["ficha"][$p][$j]["nacionalidad"].str_pad($nomina[$i]["ficha"][$p][$j]["cedula"], 8, '0', STR_PAD_LEFT);
					if(!isset($DATA_D["$identificacion"])){
						$DATA_D["$identificacion"]=0;
					}

					if(!isset($DATA_AP["$identificacion"])){
						$DATA_AP["$identificacion"]=0;
					}

					$DATA_D["$identificacion"] +=$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["valor_final"];
					$DATA_AP["$identificacion"]+=$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["valor_final_ap"];

					$TOTAL_D +=$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["valor_final"];
					$TOTAL_AP+=$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["valor_final_ap"];
					//buscar si existe en el arreglo $concepto
				}
			endfor;	
		endfor;	
	endfor;	
endfor;

ksort($DATA_D);
ksort($DATA_AP);

$rif="G200076399";
$cantidad=count($DATA_D);
$cantidad=str_pad($cantidad, 7, '0', STR_PAD_LEFT);

$total=number_format($TOTAL_D,2,"","");
$total=str_pad($total, 15, '0', STR_PAD_LEFT);

print "RETENCIONES: \r\n";
print "ONT401{$rif}{$cantidad}{$total}VES\r\n";
foreach($DATA_D as $key => $value) {
	$monto=number_format($value,2,"","");
	$monto=str_pad($monto, 11, '0', STR_PAD_LEFT);
	print "{$key}{$monto}\r\n";
}


print "\r\n";
print "\r\n";
print "\r\n";
$cantidad=count($DATA_AP);
$cantidad=str_pad($cantidad, 7, '0', STR_PAD_LEFT);

$total=number_format($TOTAL_AP,2,"","");
$total=str_pad($total, 15, '0', STR_PAD_LEFT);

print "APORTES: \r\n";
print "ONT401{$rif}{$cantidad}{$total}VES\r\n";
foreach($DATA_AP as $key => $value) {
	$monto=number_format($value,2,"","");
	$monto=str_pad($monto, 11, '0', STR_PAD_LEFT);
	print "{$key}{$monto}\r\n";
}

?>