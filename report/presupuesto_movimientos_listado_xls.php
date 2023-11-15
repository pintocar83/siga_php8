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


SIGA::$DBMode=PGSQL_ASSOC;
$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");


$inicio=SIGA::paramGet("inicio");
$fin=SIGA::paramGet("fin");
$inicio=unformatDate($inicio);
$fin=unformatDate($fin);


$tipo=["TR","CR","RD"];

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);

for($h=0;$h<count($tipo);$h++){

  $sql = "
    SELECT
      C.id,
      C.fecha,
      C.tipo,
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
      C.tipo='".$tipo[$h]."' AND
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

    $CONSULTA[$i]["total"] = 0;

    $CONSULTA[$i]["detalle_presupuestario"]=$db->Execute("SELECT
                          _formatear_estructura_presupuestaria(DP.id_accion_subespecifica) as estructura_presupuestaria,
                          _formatear_cuenta_presupuestaria(DP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
                          DP.operacion,
                          DP.monto, 
                          CP.denominacion as denominacion_presupuestaria
                        FROM
                          modulo_base.detalle_presupuestario AS DP,
                          modulo_base.cuenta_presupuestaria AS CP
                        WHERE
                          DP.id_comprobante='$id' AND
                          DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria
                        ORDER BY
                          DP.operacion, estructura_presupuestaria, DP.id_cuenta_presupuestaria");
    for($m=0; $m<count($CONSULTA[$i]["detalle_presupuestario"]); $m++) { 
      if(($CONSULTA[$i]["tipo"]=="TR" or $CONSULTA[$i]["tipo"]=="RD")){
        if(!($CONSULTA[$i]["detalle_presupuestario"][$m]["operacion"]=="AU" || $CONSULTA[$i]["detalle_presupuestario"][$m]["operacion"]=="AP")){
          $CONSULTA[$i]["detalle_presupuestario"][$m]["monto"]=-$CONSULTA[$i]["detalle_presupuestario"][$m]["monto"];
          $CONSULTA[$i]["total"]+=$CONSULTA[$i]["detalle_presupuestario"][$m]["monto"];
        }
      }
      else{
        $CONSULTA[$i]["total"]+=$CONSULTA[$i]["detalle_presupuestario"][$m]["monto"];
      }
    }

    if($CONSULTA[$i]["tipo"]=="TR"){
      $CONSULTA[$i]["total"]=abs($CONSULTA[$i]["total"]);
    }
  }





  //print_r($CONSULTA);exit;






  $ln=1;

  $activeSheet = $excel->createSheet($h);
  $excel->setActiveSheetIndex($h);
  $activeSheet->setTitle($tipo[$h]);

  $activeSheet->setCellValueExplicit("A$ln","ORDEN PAGO",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("B$ln","FECHA",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("C$ln","TOTAL",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("D$ln","CONCEPTO",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("E$ln","ACC/PRO",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("F$ln","CUENTA",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("G$ln","MONTO",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("H$ln","ESTATUS",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->getStyle("A$ln:H$ln")->getFont()->setBold(true);

  $ln++;
  $codigo = "";
  for($i=0; $i<count($CONSULTA); $i++) {
    $activeSheet->setCellValueExplicit("A$ln",$CONSULTA[$i]["correlativo"],PHPExcel_Cell_DataType::TYPE_STRING);
    $activeSheet->setCellValueExplicit("B$ln",$CONSULTA[$i]["ffecha"],PHPExcel_Cell_DataType::TYPE_STRING);
    $activeSheet->setCellValueExplicit("C$ln",$CONSULTA[$i]["total"],PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $activeSheet->setCellValueExplicit("D$ln",$CONSULTA[$i]["concepto"],PHPExcel_Cell_DataType::TYPE_STRING);
    $activeSheet->setCellValueExplicit("H$ln",$CONSULTA[$i]["estatus"],PHPExcel_Cell_DataType::TYPE_STRING);


    for($j=0; $j<count($CONSULTA[$i]["detalle_presupuestario"]) ; $j++) {
      $activeSheet->setCellValueExplicit("E$ln",$CONSULTA[$i]["detalle_presupuestario"][$j]["estructura_presupuestaria"],PHPExcel_Cell_DataType::TYPE_STRING);
      $activeSheet->setCellValueExplicit("F$ln",$CONSULTA[$i]["detalle_presupuestario"][$j]["cuenta_presupuestaria"],PHPExcel_Cell_DataType::TYPE_STRING);
      $activeSheet->setCellValueExplicit("G$ln",$CONSULTA[$i]["detalle_presupuestario"][$j]["monto"],PHPExcel_Cell_DataType::TYPE_NUMERIC);
      $ln++;
    }
    if(count($CONSULTA[$i]["detalle_presupuestario"])==0){
      $ln++;
    }
  }

  $activeSheet->getStyle("C2:C$ln")->getNumberFormat()->setFormatCode('#,##0.00');
  $activeSheet->getStyle("G2:G$ln")->getNumberFormat()->setFormatCode('#,##0.00');




  $activeSheet->getColumnDimension("A")->setAutoSize(true);
  $activeSheet->getColumnDimension("B")->setAutoSize(true);
  $activeSheet->getColumnDimension("C")->setAutoSize(true);
  $activeSheet->getColumnDimension("D")->setWidth(50);
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


  $activeSheet->freezePaneByColumnAndRow(0,2);

}

$excel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="relacion_traspasos_creditos_reducciones.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');

?>