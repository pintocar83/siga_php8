<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
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

    
$db=SIGA::DBController();
$data=SIGA::data();

$params=$_GET;

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");

$tipo="Q";

$sql = "
  SELECT 
    id,
    EXTRACT(MONTH FROM fecha_inicio) mes 
  FROM modulo_nomina.periodo 
  WHERE 
    tipo='$tipo' AND 
    EXTRACT(YEAR FROM fecha_inicio)=$data
";
$periodo=$db->Execute($sql);

$id_periodo=[];
$periodo_mes=[];
for ($i=0; $i<count($periodo); $i++) {
  $id_periodo[]=$periodo[$i]["id"];
  $periodo_mes[$periodo[$i]["mes"]][]=$periodo[$i]["id"];
}


$sql = "
  SELECT DISTINCT
    FILA.id_nomina,
    FILA.id_ficha,
    N.codigo || ' ' || N.nomina as nomina,
    P.identificacion_tipo as nacionalidad,
    P.identificacion_numero as cedula,
    split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) as nombre_apellido,
    replace(P.denominacion,';',' ') as nombres_apellidos,
    F.fecha_ingreso,
    F.activo,
    PN.genero
  FROM
    modulo_nomina.ficha_concepto FILA
      INNER JOIN modulo_nomina.nomina          N ON N.id = FILA.id_nomina
      INNER JOIN modulo_nomina.ficha           F ON F.id = FILA.id_ficha
      LEFT JOIN modulo_base.persona            P ON F.id_persona = P.id
      LEFT JOIN modulo_base.persona_natural   PN ON P.id = PN.id_persona
  WHERE
    id_periodo IN (".implode(",",$id_periodo).")
  ORDER BY nomina, cedula
";

$fila=$db->Execute($sql);

//print_r($periodo_mes);
$concepto_identificador=["SUELDO_NORMAL"];


function suma_conceptos_periodo($concepto_periodo, $concepto_identificador){
  $sum=0;
  for($c=0; $c<count($concepto_periodo["concepto"]); $c++) {
    if(in_array($concepto_periodo["concepto"][$c]["identificador"], $concepto_identificador)){
      //print "entro";
      //print_r($concepto_periodo["concepto"][$c]);
      $sum += $concepto_periodo["concepto"][$c]["valor_final"];
    }
  }
  return $sum;
}

for($f=0; $f<count($fila); $f++){
  $fila[$f]["concepto_x_mes"] = [0,0,0,0,0,0,0,0,0,0,0,0];
  $fila[$f]["total"] = 0;

  for($m=0; $m<12; $m++) {
    $mes=$m+1;
    $suma_concepto = 0;
    for($p=0; $p<count($periodo_mes[$mes]); $p++){
      $concepto_periodo = nomina::ficha_concepto($fila[$f]["id_nomina"],$periodo_mes[$mes][$p],$fila[$f]["id_ficha"]);
      $suma_concepto+=suma_conceptos_periodo($concepto_periodo, $concepto_identificador);
    }
    $fila[$f]["concepto_x_mes"][$m] = $suma_concepto;
    $fila[$f]["total"]+=$suma_concepto;
  }
}





$ln=1;

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);  

$activeSheet = $excel->createSheet(0);

$nomina = "";

$col_mes = ["C","D","E","F","G","H","I","J","K","L","M","N"];

$activeSheet->setCellValueExplicit("A$ln","CEDULA",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("B$ln","NOMBRE / APELLIDO",PHPExcel_Cell_DataType::TYPE_STRING);

$activeSheet->setCellValueExplicit("{$col_mes[0]}{$ln}", "ENERO", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[1]}{$ln}", "FEBRERO", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[2]}{$ln}", "MARZO", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[3]}{$ln}", "ABRIL", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[4]}{$ln}", "MAYO", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[5]}{$ln}", "JUNIO", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[6]}{$ln}", "JULIO", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[7]}{$ln}", "AGOSTO", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[8]}{$ln}", "SEPTIEMBRE", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[9]}{$ln}", "OCTUBRE", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[10]}{$ln}", "NOVIEMBRE", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("{$col_mes[11]}{$ln}", "DICIEMBRE", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("O{$ln}", "TOTAL", PHPExcel_Cell_DataType::TYPE_STRING);

$activeSheet->getStyle("A1:O1")->getFont()->setBold(true);

$ln++;

for($i=0; $i<count($fila); $i++) {
  if($fila[$i]["total"]==0) continue;

  if($nomina!=$fila[$i]["nomina"]){
    $nomina=$fila[$i]["nomina"];
    $activeSheet->setCellValueExplicit("A$ln","$nomina",PHPExcel_Cell_DataType::TYPE_STRING);
    $activeSheet->getStyle("A$ln")->getFont()->setBold(true);
    $ln++;
  }
  $activeSheet->setCellValueExplicit("A$ln",$fila[$i]["cedula"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("B$ln",$fila[$i]["nombre_apellido"],PHPExcel_Cell_DataType::TYPE_STRING);

  for($m=0; $m<12; $m++) {
    $activeSheet->setCellValueExplicit("{$col_mes[$m]}{$ln}", number_format($fila[$i]["concepto_x_mes"][$m],2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
  }
  $activeSheet->setCellValueExplicit("O{$ln}", number_format($fila[$i]["total"],2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);


  $ln++;
}

$activeSheet->getStyle("C2:O$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$activeSheet->getStyle("C2:O$ln")->getNumberFormat()->setFormatCode('#,##0.00');
$activeSheet->getStyle("O2:O$ln")->getFont()->setBold(true);

$activeSheet->getColumnDimension("A")->setAutoSize(true);
$activeSheet->getColumnDimension("B")->setAutoSize(true);
$activeSheet->getColumnDimension("C")->setAutoSize(true);
$activeSheet->getColumnDimension("D")->setAutoSize(true);
$activeSheet->getColumnDimension("E")->setAutoSize(true);
$activeSheet->getColumnDimension("F")->setAutoSize(true);
$activeSheet->getColumnDimension("G")->setAutoSize(true);
$activeSheet->getColumnDimension("H")->setAutoSize(true);
$activeSheet->getColumnDimension("I")->setAutoSize(true);
$activeSheet->getColumnDimension("J")->setAutoSize(true);
$activeSheet->getColumnDimension("K")->setAutoSize(true);
$activeSheet->getColumnDimension("L")->setAutoSize(true);
$activeSheet->getColumnDimension("M")->setAutoSize(true);
$activeSheet->getColumnDimension("N")->setAutoSize(true);
$activeSheet->getColumnDimension("O")->setAutoSize(true);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listado_concepto_x_mes.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');

?>