<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/str_clear.php");
include_once("../library/fpdf/1.84/fpdf.php");
include_once("../class/nomina.class.php");


$db=SIGA::DBController();


$pdf=new FPDF("P","mm","letter");
$pdf->AddPage();
$pdf->SetFillColor(255,255,255);

$nomina=$db->Execute("SELECT * FROM modulo_nomina.nomina ORDER BY codigo");

$t["ancho"]=195;
$t["concepto"]=40;
$t["cuenta"]=$t["ancho"]-$t["concepto"];


for($i=0;$i<count($nomina);$i++):
  $pdf->SetFont('helvetica','B',9);
  $pdf->Cell($t["ancho"],5,utf8_decode($nomina[$i]["nomina"]),'LRTB',1,'C');
  $concepto=$db->Execute("SELECT *
                         FROM modulo_nomina.concepto AS C, modulo_nomina.concepto_presupuesto_contabilidad as CPC
                         WHERE C.id=CPC.id_concepto and id_nomina=".$nomina[$i]["id"]."
                         ORDER BY C.orden");
  
  $pdf->SetFont('helvetica','',7.5);
  for($j=0;$j<count($concepto);$j++):

    $pdf->Cell($t["concepto"],3.5,utf8_decode($concepto[$j]["concepto"]),'LRTB',0,'L',1);
    $cuenta=!($concepto[$j]["id_cuenta_presupuestaria"]=="" or $concepto[$j]["id_cuenta_presupuestaria"]==NULL)?$concepto[$j]["id_cuenta_presupuestaria"]:$concepto[$j]["id_cuenta_presupuestaria_ap"];
    $cuenta_denominacion="";
    if($cuenta){
      $cuenta_denominacion=$db->Execute("SELECT denominacion, _formatear_cuenta_presupuestaria(id_cuenta_presupuestaria) as cuenta_presupuestaria FROM modulo_base.cuenta_presupuestaria WHERE id_cuenta_presupuestaria='$cuenta'");
      $cuenta=$cuenta_denominacion[0][1];
      $cuenta_denominacion=$cuenta_denominacion[0][0];
      
    }
    $pdf->SetFont('helvetica','',7);
    
    $pdf->Cell($t["cuenta"],3.5,utf8_decode($cuenta." ".$cuenta_denominacion),'LRTB',1,'L',1);
    
    
  endfor;
endfor;

$pdf->Output();
?>