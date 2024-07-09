<?php
error_reporting(0);
//error_reporting(E_ALL);
ini_set('display_errors', 'Off');
set_time_limit(-1);
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/unformatDate.php");
include_once("../library/functions/formatDate.php");

include_once("../library/functions/column_hash.php");
include_once("../library/phpexcel/PHPExcel.php");


//SIGA::$DBMode=PGSQL_ASSOC;
$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");


$inicio=SIGA::paramGet("inicio");
$fin=SIGA::paramGet("fin");
$inicio=unformatDate($inicio);
$fin=unformatDate($fin);

$add="";
if($estatus=="inactivo"){
  $add.="NOT F.activo AND";
}
else if($estatus=="activo"){
  $add.="F.activo AND";
}


$sql = "
  SELECT
    C.id,
    C.fecha,
    C.concepto,
    C.contabilizado,
    lpad(text(C.correlativo),10,'0') as correlativo,
    case when (select count(*) from modulo_base.comprobante_previo as CP, modulo_base.comprobante as C2 where C.id=CP.id_comprobante_previo and CP.id_comprobante=C2.id and C2.tipo='CA')>0 then 't' else 'f' end as anulado,
    P.identificacion_tipo,
    P.identificacion_numero,
    replace(P.denominacion,';',' ') as beneficiario
  FROM
    modulo_base.comprobante C
      LEFT JOIN modulo_base.persona P ON P.id = C.id_persona
  WHERE
    C.tipo='OP' AND
    EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
    C.fecha BETWEEN '$inicio' AND '$fin'
  ORDER BY 
    correlativo ASC
";
$CONSULTA=$db->Execute($sql);

if(count($CONSULTA)==0){
  print "No se encontraron datos.";
  exit;
}


for($i=0; $i<count($CONSULTA); $i++) {
  $CONSULTA[$i]["ffecha"]=formatDate($CONSULTA[$i]["fecha"]);
  $estatus="PENDIENTE";
  if($CONSULTA[$i]["anulado"]=='t')
    $estatus="ANULADO";
  else if($CONSULTA[$i]["contabilizado"]=='t')
    $estatus="CONTABILIZADO";
  $CONSULTA[$i]["estatus"]=$estatus;


  $id=$CONSULTA[$i]["id"];
  $sql="select sum(monto) total from modulo_base.detalle_contable where id_comprobante='$id' AND operacion='H'";
  $total_haber=$db->Execute($sql);
  $total_haber=isset($total_haber[0]["total"])?$total_haber[0]["total"]:0;

  $sql="select sum(monto) total from modulo_base.comprobante_tiene_retencion where id_comprobante='$id'";
  $total_retencion=$db->Execute($sql);
  $total_retencion=isset($total_retencion[0]["total"])?$total_retencion[0]["total"]:0;

  $CONSULTA[$i]["total"] = $total_haber;
  //$CONSULTA[$i]["total"] = $total_haber - $total_retencion;
  $CONSULTA[$i]["total_retencion"] = $total_retencion;

}





//print_r($CONSULTA);exit;






$ln=1;

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);  

$activeSheet = $excel->createSheet(0);

$activeSheet->setCellValueExplicit("A$ln","ORDEN PAGO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("B$ln","FECHA",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("C$ln","RIF",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("D$ln","BENEFICIARIO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("E$ln","CONCEPTO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("F$ln","TOTAL",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("G$ln","ESTATUS",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->getStyle("A$ln:K$ln")->getFont()->setBold(true);

$ln++;
$codigo = "";
for($i=0; $i<count($CONSULTA); $i++) {
  $activeSheet->setCellValueExplicit("A$ln",$CONSULTA[$i]["correlativo"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("B$ln",$CONSULTA[$i]["ffecha"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("C$ln",$CONSULTA[$i]["identificacion_tipo"].$CONSULTA[$i]["identificacion_numero"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("D$ln",$CONSULTA[$i]["beneficiario"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("E$ln",$CONSULTA[$i]["concepto"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("F$ln",$CONSULTA[$i]["total"],PHPExcel_Cell_DataType::TYPE_NUMERIC);
  $activeSheet->setCellValueExplicit("G$ln",$CONSULTA[$i]["estatus"],PHPExcel_Cell_DataType::TYPE_STRING);

   $ln++;
}

$activeSheet->getStyle("F2:F$ln")->getNumberFormat()->setFormatCode('#,##0.00');




$activeSheet->getColumnDimension("A")->setAutoSize(true);
$activeSheet->getColumnDimension("B")->setAutoSize(true);
$activeSheet->getColumnDimension("C")->setAutoSize(true);
$activeSheet->getColumnDimension("D")->setWidth(30);
$activeSheet->getColumnDimension("E")->setWidth(50);
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


$activeSheet->freezePaneByColumnAndRow(0,2);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listado_orden_pago.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');

?>
