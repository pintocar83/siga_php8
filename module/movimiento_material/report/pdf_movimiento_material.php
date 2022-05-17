<?php
include_once("../../../library/include.php");
include_once("../../../library/functions/letra_dia_semana.php");
include_once("../../../library/functions/letra_mes.php");
include_once("../../../library/functions/rango_paginas.php");


include_once("../../../library/fpdf17/WriteTag.php");

include_once("functions/generar_movimiento_material.php");
include_once("functions/generar_requisicion.php");

$db=new DBController();
$db->ConnectQuick("siga");


$params=$_GET;
$id=array();
$id[]=getParamClear("id");

$pdf = new PDF_WriteTag("P","mm","Letter");


$pdf->SetStyle("negrita", "helvetica", "B", 10, "0, 0, 0");
$pdf->SetStyle("parrafo", "helvetica", "", 10, "0, 0, 0", 0);


$pdf->SetAutoPageBreak(true);
$pdf->SetTopMargin(25);

$ancho=195;
$ancho_interno=$ancho-10;
$alto=125;

$t1_fila=5;

$t1_col_1=8;
$t1_col_2=20;
$t1_col_4=50;
$separacion_2=45;

$t1_col_3=$ancho_interno-($t1_col_1+$t1_col_2+$t1_col_4);

for($i=0;$i<count($id);$i++){
  //buscar si el comprobante tiene requisicion
  $sql="select id_requisicion from modulo_inventario.movimiento_material_tiene_requisicion where id_movimiento_material='".$id[$i]."'";
  $id_requisicion=$db->Execute($sql);
  if(isset($id_requisicion[0][0])){
    $pdf->AddPage("P");
    generar_movimiento_material($id[$i],10);
    generar_requisicion($id_requisicion[0][0], 10+10+$alto);
  }
  else{
    if($i%2==0){
      $pdf->AddPage("P");
      generar_movimiento_material($id[$i],10);
    }
    else{
      generar_movimiento_material($id[$i],10+10+$alto);
    }
  }
}

$pdf->Output();
?>