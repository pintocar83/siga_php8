<?php
error_reporting(0);
ini_set('display_errors', 'Off');
set_time_limit(-1);
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/unformatDate.php");

include_once("../library/functions/column_hash.php");
include_once("../library/phpexcel/PHPExcel.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");


$id_persona=SIGA::paramGet("id_persona");
$add="";
if($id_persona)
  $add=" AND P.id=$id_persona";
$tipo=SIGA::paramGet("tipo");
$fecha_inicio=SIGA::paramGet("fecha_inicio");
$fecha_culminacion=SIGA::paramGet("fecha_culminacion");

$tipo_col="";
if($tipo==1) $tipo_col="iva";
else if($tipo==2) $tipo_col="islr";
if($tipo_col=="") exit;



$CONSULTA=$db->Execute("select
                          P.identificacion_tipo || lpad(text(P.identificacion_numero),9,'0') as identificacion,
                          replace(P.denominacion,';',' ') as persona,
                          to_char(RC.fecha,'YYYYMM') || lpad(text(RC.numero),8,'0') as periodo_numero,
                          lpad(text(RC.numero),8,'0') as numero,
                          to_char(RC.fecha,'YYYYMM') as periodo,
                          to_char(F.fecha,'DD/MM/YYYY') as ffecha,
                          F.fecha fecha_factura ,
                          F.numero_factura,
                          F.numero_control,
                          F.total,
                          F.informacion_".$tipo_col."[1] as monto_base,
                          F.informacion_".$tipo_col."[2] as porcentaje,
                          F.informacion_".$tipo_col."[3] as monto,
                          F.informacion_".$tipo_col."[4] as retencion
                        from
                          modulo_base.persona as P,
                          modulo_base.retencion_comprobante as RC,
                          modulo_base.retencion_comprobante_tiene_factura as RCTF,
                          modulo_base.factura as F
                        where
                          P.id=RC.id_persona AND
                          RC.id_retencion_tipo=$tipo AND
                          RC.id=RCTF.id_retencion_comprobante AND
                          RCTF.id_factura=F.id AND
                          RC.fecha BETWEEN '".unformatDate($fecha_inicio)."' AND '".unformatDate($fecha_culminacion)."'
                          $add
                        order by
                          periodo_numero
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



for($i=0; $i<count($CONSULTA); $i++) {
  $exento = $CONSULTA[$i]["total"] - $CONSULTA[$i]["monto_base"] - $CONSULTA[$i]["retencion"];
  $activeSheet->setCellValueExplicit("A$ln",$organismo[0]["identificacion"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("B$ln",$CONSULTA[$i]["periodo"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("C$ln",$CONSULTA[$i]["fecha_factura"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("D$ln","C",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("E$ln","01",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("F$ln",$CONSULTA[$i]["identificacion"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("G$ln",$CONSULTA[$i]["numero_factura"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("H$ln",$CONSULTA[$i]["numero_control"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("I$ln",number_format($CONSULTA[$i]["total"],2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("J$ln",number_format($CONSULTA[$i]["monto_base"],2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("K$ln",number_format($CONSULTA[$i]["retencion"],2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("L$ln","0",PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("M$ln",$CONSULTA[$i]["periodo_numero"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("N$ln",number_format($exento,2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("O$ln",number_format($CONSULTA[$i]["porcentaje"],2,".",""),PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("P$ln","0",PHPExcel_Cell_DataType::TYPE_STRING);
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

$activeSheet->getStyle("G1:P{$ln}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);





header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="retenciones_emitidas_'.$tipo_col.'.xlsx"');

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');
/*
$MARGEN_LEFT=10;
$MARGEN_TOP=5;
$ANCHO=260;

$TITULO_PRINCIPAL="LISTADO DE RETENCIONES EMITIDAS (".strtoupper($tipo_col).")";
$TITULO_SECUNDARIO="DEL $fecha_inicio AL $fecha_culminacion";


$t_n=5;
$t_rif=25;
$t_comprobante=30;
$t_fecha=18;
$t_numero=18;
$t_control=18;
$t_total=15;
$t_base=15;
$t_porcentaje=12;
$t_monto=15;
$t_retencion=15;
$t_persona=$ANCHO-($t_n+$t_rif+$t_comprobante+$t_fecha+$t_numero+$t_control+$t_total+$t_base+$t_porcentaje+$t_monto+$t_retencion);




$CABECERA[0]=array(
                   array("nombre"=>"","ancho"=>($t_n+$t_rif+$t_persona+$t_comprobante),"borde"=>"","fondo"=>array(255,255,255)),
                   array("nombre"=>"FACTURA","ancho"=>($t_fecha+$t_numero+$t_control+$t_total+$t_base+$t_porcentaje+$t_monto))
                  );

$CABECERA[1]=array(
                   array("id"=>"#","nombre"=>"Nº","ancho"=>$t_n,"alinear"=>"C"),
                   array("id"=>"identificacion","nombre"=>"RIF","ancho"=>$t_rif,"alinear"=>"L"),
                   array("id"=>"persona","nombre"=>"Proveedor","ancho"=>$t_persona,"alinear"=>"L"),
                   array("id"=>"numero","nombre"=>"Comprobante","ancho"=>$t_comprobante,"alinear"=>"C"),
                   array("id"=>"fecha","nombre"=>"Fecha","ancho"=>$t_fecha,"alinear"=>"C"),
                   array("id"=>"numero_factura","nombre"=>"Número","ancho"=>$t_numero,"alinear"=>"C"),
                   array("id"=>"numero_control","nombre"=>"Nº Control","ancho"=>$t_control,"alinear"=>"C"),
                   array("id"=>"total","nombre"=>"Total","ancho"=>$t_total,"alinear"=>"R","formato"=>"numerico"),
                   array("id"=>"monto_base","nombre"=>"Base Imp.","ancho"=>$t_base,"alinear"=>"R","formato"=>"numerico"),
                   array("id"=>"porcentaje","nombre"=>"% ".strtoupper($tipo_col),"ancho"=>$t_porcentaje,"alinear"=>"R","formato"=>"numerico"),
                   array("id"=>"monto","nombre"=>strtoupper($tipo_col),"ancho"=>$t_monto,"alinear"=>"R","formato"=>"numerico"),
                   array("id"=>"retencion","nombre"=>"Retención","ancho"=>$t_retencion,"alinear"=>"R","formato"=>"numerico")
                  );

include("template/pdf_reporte_1.class.php");


$pdf=new PDF_REPORTE_1("L","mm","letter");
$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);
$pdf->SetAutoPageBreak(true,10);
$pdf->AddPage();
$pdf->PrintData();

$pdf->SetFont('helvetica','B',7.5);
$pdf->Cell($t_n+$t_rif+$t_persona+$t_comprobante+$t_fecha+$t_numero+$t_control+$t_total+$t_base+$t_porcentaje+$t_monto,5,"","",0,"R");
$pdf->Cell($t_retencion,5,number_format($SUMA["retencion"],2,",","."),"",1,"R");



$pdf->AliasNbPages();
$pdf->Output("retenciones_emitidas_$tipo_col.pdf","I");
*/
?>