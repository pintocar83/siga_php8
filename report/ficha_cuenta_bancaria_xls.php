<?php
error_reporting(0);
ini_set('display_errors', 'Off');
set_time_limit(-1);
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/unformatDate.php");
include_once("../library/functions/formatDate.php");

include_once("../library/functions/column_hash.php");
include_once("../library/phpexcel/PHPExcel.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");






$CONSULTA=$db->Execute("select
                          P.identificacion_tipo as nacionalidad,
                          P.identificacion_numero as cedula,
                          split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',2) as nombres,
                          split_part(P.denominacion,';',3) || ' ' || split_part(P.denominacion,';',4) as apellidos,
                          PN.fecha_nacimiento,
                          PN.genero,
                          substring(COALESCE(F.cuenta_nomina,''),1,4) codigo_banco,
                          F.*
                        from
                          modulo_nomina.ficha F
                            INNER JOIN modulo_base.persona P ON P.id = F.id_persona
                            LEFT JOIN  modulo_base.persona_natural PN ON P.id=PN.id_persona
                        where
                          F.activo
                        order by
                          codigo_banco,
                          cedula
                        ");

if(count($CONSULTA)==0){
  print "No se encontraron datos.";
  exit;
}


$ln=1;

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);  

$activeSheet = $excel->createSheet(0);

$activeSheet->setCellValueExplicit("A$ln","CEDULA",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("B$ln","NOMBRES",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("C$ln","APELLIDOS",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("D$ln","FECHA NACIMIENTO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("E$ln","EDAD",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("F$ln","GENERO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("G$ln","FECHA INGRESO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("H$ln","CUENTA BANCARIA",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->getStyle("A$ln:H$ln")->getFont()->setBold(true);

$ln++;
$codigo = "";
for($i=0; $i<count($CONSULTA); $i++) {
  $CONSULTA[$i]["fecha_ingreso"]=str_replace(["{","}"],"",$CONSULTA[$i]["fecha_ingreso"]);
  if($CONSULTA[$i]["fecha_ingreso"]){
    $tmp=explode(",",$CONSULTA[$i]["fecha_ingreso"]);
    $CONSULTA[$i]["fecha_ingreso"]=end($tmp);
    $CONSULTA[$i]["fecha_ingreso"]=formatDate($CONSULTA[$i]["fecha_ingreso"]);
  }

  $CONSULTA[$i]["edad"]="";
  if($CONSULTA[$i]["fecha_nacimiento"]){
    $dt_inicio       = new DateTime($CONSULTA[$i]["fecha_nacimiento"]);
    $dt_culminacion  = new DateTime(date("Y-m-d"));
    $diff=$dt_inicio->diff($dt_culminacion,true);
    $CONSULTA[$i]["edad"]=$diff->y;
    $CONSULTA[$i]["fecha_nacimiento"]=formatDate($CONSULTA[$i]["fecha_nacimiento"]);
  }



  if($CONSULTA[$i]["codigo_banco"]!=$codigo){
    $codigo=$CONSULTA[$i]["codigo_banco"];

    if($codigo){
      $sql="select * from modulo_base.banco where not eliminado and codigo='".$codigo."'";
      $BANCO=$db->Execute($sql);
      $banco_nombre="";
      if(isset($BANCO[0])){
        $banco_nombre=$BANCO[0]["banco"];
      }
      $activeSheet->setCellValueExplicit("A$ln","$codigo $banco_nombre",PHPExcel_Cell_DataType::TYPE_STRING);
      $activeSheet->getStyle("A$ln")->getFont()->setBold(true);
      $activeSheet->getStyle("A$ln")->getFont()->setSize(16);
      $ln++;
    }

  }


  $activeSheet->setCellValueExplicit("A$ln",$CONSULTA[$i]["nacionalidad"].$CONSULTA[$i]["cedula"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("B$ln",$CONSULTA[$i]["nombres"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("C$ln",$CONSULTA[$i]["apellidos"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("D$ln",$CONSULTA[$i]["fecha_nacimiento"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("E$ln",$CONSULTA[$i]["edad"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("F$ln",$CONSULTA[$i]["genero"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("G$ln",$CONSULTA[$i]["fecha_ingreso"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("H$ln",$CONSULTA[$i]["cuenta_nomina"],PHPExcel_Cell_DataType::TYPE_STRING);
  /*
  $activeSheet->setCellValueExplicit("I$ln",number_format($CONSULTA[$i]["total"],2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("J$ln",number_format($CONSULTA[$i]["monto_base"],2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("K$ln",number_format($CONSULTA[$i]["retencion"],2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("L$ln","0",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("M$ln",$CONSULTA[$i]["periodo_numero"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("N$ln",number_format($exento,2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("O$ln",number_format($CONSULTA[$i]["porcentaje"],2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("P$ln","0",PHPExcel_Cell_DataType::TYPE_STRING);*/
  $ln++;
}



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
$activeSheet->getColumnDimension("P")->setAutoSize(true);

//$activeSheet->getStyle("G1:P{$ln}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


$activeSheet->freezePaneByColumnAndRow(0,2);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listado_fichas_por_cuenta_bancaria.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');

?>