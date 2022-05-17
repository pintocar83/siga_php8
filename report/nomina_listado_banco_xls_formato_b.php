<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/sql_query_total.php");
include_once("../class/nomina.class.php");
include_once("../library/functions/column_hash.php");
include_once("../library/phpexcel/PHPExcel.php");

function nacionalidad_validar($nac){
	if($nac=="E") return "E";
	if($nac=="P") return "P";
	return "V";
}
    
$db=SIGA::DBController();


$params=$_GET;

$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
if(count($id_periodo)!=1){
	print "Actualmente solo puede seleccionar un periodo.";
	exit;
}

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");

$id_periodo=$id_periodo[0];

$id_nomina=explode(",",SIGA::paramGet("id_nomina"));



$periodo=$db->Execute("SELECT codigo, fecha_inicio, fecha_culminacion, descripcion FROM modulo_nomina.periodo WHERE id=$id_periodo");



$data=[];


$c=0;
$total_neto=0;
for($i=0;$i<count($id_nomina);$i++):
	
	$ficha=nomina::fichas($id_nomina[$i],$id_periodo);	
	
	for($j=0;$j<count($ficha);$j++):
		$data[$c]=[];
		$data[$c]["nacionalidad"]=$ficha[$j]["nacionalidad"];
		$data[$c]["cedula"]=$ficha[$j]["cedula"];
		$data[$c]["cuenta_nomina"]=$ficha[$j]["cuenta_nomina"];
		$data[$c]["total_neto"]=$ficha[$j]["total_neto"];
		
		$c++;
		$total_neto+=$ficha[$j]["total_neto"];
	endfor;	
endfor;


$ln=1;

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);	

$activeSheet = $excel->createSheet(0);

$activeSheet->setCellValueExplicit("A$ln","10",PHPExcel_Cell_DataType::TYPE_STRING);	
$activeSheet->setCellValueExplicit("B$ln","000001",PHPExcel_Cell_DataType::TYPE_STRING);	
$activeSheet->setCellValueExplicit("C$ln","01750301461000129894",PHPExcel_Cell_DataType::TYPE_STRING);	
$activeSheet->setCellValueExplicit("D$ln",date("Ymd"),PHPExcel_Cell_DataType::TYPE_STRING);	
$activeSheet->setCellValueExplicit("E$ln",str_pad($c,6,"0",STR_PAD_LEFT),PHPExcel_Cell_DataType::TYPE_STRING);	
$activeSheet->setCellValueExplicit("F$ln",str_pad(number_format($total_neto,2,"",""),15,"0",STR_PAD_LEFT),PHPExcel_Cell_DataType::TYPE_STRING);	
$ln++;


for($i=0; $i<$c; $i++) { 
	$activeSheet->setCellValueExplicit("A$ln","20",PHPExcel_Cell_DataType::TYPE_STRING);
	$activeSheet->setCellValueExplicit("B$ln",nacionalidad_validar($data[$i]["nacionalidad"]),PHPExcel_Cell_DataType::TYPE_STRING);
	$activeSheet->setCellValueExplicit("C$ln",str_pad($data[$i]["cedula"],9,"0",STR_PAD_LEFT),PHPExcel_Cell_DataType::TYPE_STRING);
	$activeSheet->setCellValueExplicit("D$ln",str_pad($data[$i]["cuenta_nomina"],20,"0",STR_PAD_LEFT),PHPExcel_Cell_DataType::TYPE_STRING);
	$activeSheet->setCellValueExplicit("E$ln",str_pad(number_format($data[$i]["total_neto"],2,"",""),15,"0",STR_PAD_LEFT),PHPExcel_Cell_DataType::TYPE_STRING);

	$ln++;
}

$activeSheet->getColumnDimension("A")->setAutoSize(true);
$activeSheet->getColumnDimension("B")->setAutoSize(true);
$activeSheet->getColumnDimension("C")->setAutoSize(true);
$activeSheet->getColumnDimension("D")->setAutoSize(true);
$activeSheet->getColumnDimension("E")->setAutoSize(true);
$activeSheet->getColumnDimension("F")->setAutoSize(true);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listado_banco_txt.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');

?>