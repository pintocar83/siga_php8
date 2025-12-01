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

$tipo=SIGA::paramGet("tipo");
if(!($tipo==='OC' || $tipo==='OS')){
  exit;
}

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
    C.tipo='$tipo' AND
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


  $_id=$CONSULTA[$i]["id"];
  



  $COMPROBANTE_DATOS=$db->Execute("SELECT dato, valor FROM modulo_base.comprobante_datos WHERE id_comprobante='$_id'");
      
      
  $ITEM=$db->Execute("SELECT
                          I.id as id_item,
                          I.codigo,
                          I.item,
                          I.id_item_tipo,
                          I.id_cuenta_presupuestaria,
                          CTI.aplica_iva,
                          CTI.cantidad,
                          CTI.costo,
                          CTI.descuento,
                          CTI.id_unidad_medida,
                          UM.medida
                        FROM modulo_base.comprobante_tiene_item AS CTI, modulo_base.item as I, modulo_base.unidad_medida as UM
                        WHERE CTI.id_comprobante='$_id' AND CTI.id_item=I.id AND CTI.id_unidad_medida=UM.id");



  $CARGO=$db->Execute("SELECT
                  C.id as id_cargo,
                  lpad(text(C.id),3,'0') as correlativo,
                  C.denominacion as cargo,
                  C.formula,
                  C.iva,
                  C.id_cuenta_presupuestaria,
                  CTC.monto                                                      
                FROM modulo_base.comprobante_tiene_cargo AS CTC, modulo_base.cargo as C
                WHERE CTC.id_comprobante='$_id' AND CTC.id_cargo=C.id");

  $sw_descuento_item = false;
  if($ITEM){
    for($j=0;$j<count($ITEM) and $ITEM;$j++){
      if($ITEM[$j]["descuento"]){
        $sw_descuento_item = true;
        break;
      }
    }     
  }

  $SUBTOTAL=0;
  $SUBTOTAL_IVA=0;
  $EXENTO=0;
  $IMPONIBLE_IVA=0;

  for($j=0;$j<count($ITEM) and $ITEM;$j++){
    $total_item = $ITEM[$j]["cantidad"]*$ITEM[$j]["costo"];
    $descuento_item = 0;
    $descuento_item_mostrar = "";
    if($ITEM[$j]["descuento"]){
      $tmp = json_decode($ITEM[$j]["descuento"], true);
      if($tmp["porcentaje"])
        $descuento_item_mostrar = number_format($tmp["porcentaje"],2,",",".")."%";
      if($tmp["monto"])
        $descuento_item_mostrar = number_format($tmp["monto"],2,",",".");
      $descuento_item = ($tmp["porcentaje"]*$total_item/100) + $tmp["monto"];
      $total_item = number_format($total_item - $descuento_item,4,".","");
    }

    //$total_item=$ITEM[$j]["cantidad"]*$ITEM[$j]["costo"];
    $SUBTOTAL+=$total_item;
    if($ITEM[$j]["aplica_iva"]=="t")
        $SUBTOTAL_IVA+=$total_item;
    else
      $EXENTO+=$total_item;
  }

  $descuento_p=0;
  $descuento_m=0;
  for($j=0;$j<count($COMPROBANTE_DATOS);$j++){
      if($COMPROBANTE_DATOS[$j]["dato"]=="descuento_porcentaje")
          $descuento_p=$COMPROBANTE_DATOS[$j]["valor"];
      else if($COMPROBANTE_DATOS[$j]["dato"]=="descuento_monto")
          $descuento_m=$COMPROBANTE_DATOS[$j]["valor"];
  }
  $DESCUENTO=($descuento_p*$SUBTOTAL)/100+$descuento_m;
  $DESCUENTO_IVA=($descuento_p*$SUBTOTAL_IVA)/100+$descuento_m;
  $BASE_IMPONIBLE=$SUBTOTAL-$DESCUENTO;
  $BASE_IMPONIBLE_IVA=$SUBTOTAL_IVA-$DESCUENTO_IVA;
  
  $TOTAL_CARGO=0;
  if($CARGO){
      $MONTO=$BASE_IMPONIBLE_IVA;
      for($c=0;$c<count($CARGO) && $CARGO;$c++){
          $CAD=str_replace("MONTO","\$MONTO",$CARGO[$c]["formula"]);
          eval("\$TEMP=$CAD;");
          $TOTAL_CARGO+=$TEMP+$CARGO[$c]["monto"];
      }
  }
  $TOTAL=$BASE_IMPONIBLE+$TOTAL_CARGO;


  $CONSULTA[$i]["total"] = $TOTAL;
  $CONSULTA[$i]["total_retencion"]=0;



  $CONSULTA[$i]["detalle_presupuestario"]=$db->Execute("SELECT
                        _formatear_estructura_presupuestaria(DP.id_accion_subespecifica) as estructura_presupuestaria,
                        _formatear_cuenta_presupuestaria(DP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
                        DP.monto, 
                        CP.denominacion as denominacion_presupuestaria
                      FROM
                        modulo_base.detalle_presupuestario AS DP,
                        modulo_base.cuenta_presupuestaria AS CP
                      WHERE
                        DP.id_comprobante='$_id' AND
                        DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria
                      ORDER BY
                        estructura_presupuestaria, DP.id_cuenta_presupuestaria");


}





//print_r($CONSULTA);exit;






$ln=1;

$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
$excel->removeSheetByIndex(0);  

$activeSheet = $excel->createSheet(0);

$activeSheet->setCellValueExplicit("A$ln",$tipo==='OC' ? "ORDEN COMPRA" : "ORDEN SERVICIO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("B$ln","FECHA",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("C$ln","RIF",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("D$ln","BENEFICIARIO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("E$ln","TOTAL",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("F$ln","CONCEPTO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("G$ln","ACC/PRO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("H$ln","CUENTA",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("I$ln","MONTO",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("J$ln","RETENCIÓN",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->setCellValueExplicit("K$ln","ESTATUS",PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->getStyle("A$ln:K$ln")->getFont()->setBold(true);

$ln++;
$codigo = "";
for($i=0; $i<count($CONSULTA); $i++) {
  $activeSheet->setCellValueExplicit("A$ln",$CONSULTA[$i]["correlativo"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("B$ln",$CONSULTA[$i]["ffecha"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("C$ln",$CONSULTA[$i]["identificacion_tipo"].$CONSULTA[$i]["identificacion_numero"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("D$ln",$CONSULTA[$i]["beneficiario"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("E$ln",$CONSULTA[$i]["total"],PHPExcel_Cell_DataType::TYPE_NUMERIC);
  $activeSheet->setCellValueExplicit("F$ln",$CONSULTA[$i]["concepto"],PHPExcel_Cell_DataType::TYPE_STRING);
  $activeSheet->setCellValueExplicit("J$ln",$CONSULTA[$i]["total_retencion"],PHPExcel_Cell_DataType::TYPE_NUMERIC);
  $activeSheet->setCellValueExplicit("K$ln",$CONSULTA[$i]["estatus"],PHPExcel_Cell_DataType::TYPE_STRING);


  for($j=0; $j<count($CONSULTA[$i]["detalle_presupuestario"]) ; $j++) {
    $activeSheet->setCellValueExplicit("G$ln",$CONSULTA[$i]["detalle_presupuestario"][$j]["estructura_presupuestaria"],PHPExcel_Cell_DataType::TYPE_STRING);
    $activeSheet->setCellValueExplicit("H$ln",$CONSULTA[$i]["detalle_presupuestario"][$j]["cuenta_presupuestaria"],PHPExcel_Cell_DataType::TYPE_STRING);
    $activeSheet->setCellValueExplicit("I$ln",$CONSULTA[$i]["detalle_presupuestario"][$j]["monto"],PHPExcel_Cell_DataType::TYPE_NUMERIC);
    $ln++;
  }
  if(count($CONSULTA[$i]["detalle_presupuestario"])==0){
    $ln++;
  }
}

$activeSheet->getStyle("E2:E$ln")->getNumberFormat()->setFormatCode('#,##0.00');
$activeSheet->getStyle("I2:I$ln")->getNumberFormat()->setFormatCode('#,##0.00');
$activeSheet->getStyle("J2:J$ln")->getNumberFormat()->setFormatCode('#,##0.00');




$activeSheet->getColumnDimension("A")->setAutoSize(true);
$activeSheet->getColumnDimension("B")->setAutoSize(true);
$activeSheet->getColumnDimension("C")->setAutoSize(true);
$activeSheet->getColumnDimension("D")->setWidth(30);
$activeSheet->getColumnDimension("E")->setAutoSize(true);
$activeSheet->getColumnDimension("F")->setWidth(50);
$activeSheet->getColumnDimension("G")->setAutoSize(true);
$activeSheet->getColumnDimension("H")->setAutoSize(true);
$activeSheet->getColumnDimension("I")->setAutoSize(true);
$activeSheet->getColumnDimension("J")->setAutoSize(true);
$activeSheet->getColumnDimension("J")->setVisible(false);
$activeSheet->getColumnDimension("K")->setAutoSize(true);
$activeSheet->getColumnDimension("L")->setAutoSize(true);
$activeSheet->getColumnDimension("M")->setAutoSize(true);
$activeSheet->getColumnDimension("N")->setAutoSize(true);
$activeSheet->getColumnDimension("O")->setAutoSize(true);
$activeSheet->getColumnDimension("P")->setAutoSize(true);


$activeSheet->freezePaneByColumnAndRow(0,2);

$filename = $tipo==='OC' ? "listado_orden_compra_detalle.xlsx" : "listado_orden_servicio_detalle.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
$writer->save('php://output');

?>
