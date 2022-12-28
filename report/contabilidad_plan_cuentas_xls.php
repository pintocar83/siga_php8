<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
error_reporting(0);
ini_set('display_errors', 'Off');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/sql_query_total.php");
include_once("../class/nomina.class.php");
include_once("../library/functions/column_hash.php");
include_once("../library/phpexcel/PHPExcel.php");


$db=SIGA::DBController();




$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");



$cuenta_contable=$db->Execute("
	SELECT
		_formatear_cuenta_contable(id_cuenta_contable) cuenta_contable,
		denominacion
	FROM modulo_base.cuenta_contable
	ORDER BY id_cuenta_contable
");

//print_r($cuenta_contable);exit;
$ln=1;

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);

$activeSheet = $excel->createSheet(0);

$activeSheet->setCellValueExplicit("A$ln","CUENTA",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("B$ln","DENOMINACION",PHPExcel_Cell_DataType::TYPE_STRING);
$ln++;


for($i=0; $i<count($cuenta_contable); $i++) {
	$activeSheet->setCellValueExplicit("A$ln",$cuenta_contable[$i]["cuenta_contable"],PHPExcel_Cell_DataType::TYPE_STRING);
	$activeSheet->setCellValueExplicit("B$ln",$cuenta_contable[$i]["denominacion"],PHPExcel_Cell_DataType::TYPE_STRING);

	$ln++;
}

$activeSheet->getColumnDimension("A")->setAutoSize(true);
$activeSheet->getColumnDimension("B")->setAutoSize(true);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="contabilidad_plan_cuentas.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');

?>