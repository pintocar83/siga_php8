<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/unformatDate.php");

include_once("../library/fpdf/1.7/fpdf.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");

$FI=SIGA::paramGet("FI");
$FF=SIGA::paramGet("FF");
$FI=unformatDate($FI);
$FF=unformatDate($FF);
$id_cuenta_contable=SIGA::paramGet("id_cuenta_contable");

$add_cuenta_contable="";
if($id_cuenta_contable!="")
  $add_cuenta_contable="AND DC.id_cuenta_contable like '$id_cuenta_contable%'";

$CUENTA_CONTABLE=$db->Execute("SELECT
                                    DISTINCT
                                      CC.id_cuenta_contable,
                                      _formatear_cuenta_contable(DC.id_cuenta_contable) as cuenta_contable,
                                      CC.denominacion,
                                      _cuenta_contable_aumenta_debe(CC.id_cuenta_contable) as aumenta_debe
																	FROM
																		modulo_base.comprobante as C,
                                    modulo_base.detalle_contable as DC,
                                    modulo_base.cuenta_contable as CC
																	WHERE
																		C.contabilizado AND
                                    EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                    C.fecha BETWEEN '$FI' AND '$FF' AND
                                    C.id=DC.id_comprobante AND
                                    DC.id_cuenta_contable=CC.id_cuenta_contable
                                    $add_cuenta_contable
                                  ORDER BY
                                    CC.id_cuenta_contable");


for($i=0;$i<count($CUENTA_CONTABLE);$i++){
  $CUENTA_CONTABLE[$i]["saldo_D"]=$db->Execute("select
                                              sum(DC.monto)
                                            from
                                              modulo_base.comprobante as C,
                                              modulo_base.detalle_contable as DC
                                            where
																							C.contabilizado AND
                                              EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                              C.id=DC.id_comprobante AND
                                              C.fecha BETWEEN '$FI' AND '$FF' AND
                                              C.tipo<>'AC' AND
                                              DC.id_cuenta_contable='".$CUENTA_CONTABLE[$i]["id_cuenta_contable"]."' AND
                                              DC.operacion='D'                                             
                                            ");
  $CUENTA_CONTABLE[$i]["saldo_D"]=isset($CUENTA_CONTABLE[$i]["saldo_D"][0][0])?$CUENTA_CONTABLE[$i]["saldo_D"][0][0]:0;
  
  $CUENTA_CONTABLE[$i]["saldo_H"]=$db->Execute("select
                                              sum(DC.monto)
                                            from
                                              modulo_base.comprobante as C,
                                              modulo_base.detalle_contable as DC
                                            where
																							C.contabilizado AND
                                              EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                              C.id=DC.id_comprobante AND
                                              C.fecha BETWEEN '$FI' AND '$FF' AND
                                              C.tipo<>'AC' AND
                                              DC.id_cuenta_contable='".$CUENTA_CONTABLE[$i]["id_cuenta_contable"]."' AND
                                              DC.operacion='H'                                             
                                            ");
  $CUENTA_CONTABLE[$i]["saldo_H"]=isset($CUENTA_CONTABLE[$i]["saldo_H"][0][0])?$CUENTA_CONTABLE[$i]["saldo_H"][0][0]:0;
  
  
  $CUENTA_CONTABLE[$i]["saldo_previo_D"]=$db->Execute("select
                                              sum(DC.monto)
                                            from
                                              modulo_base.comprobante as C,
                                              modulo_base.detalle_contable as DC
                                            where
																							C.contabilizado AND
                                              EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                              C.id=DC.id_comprobante AND
                                              (C.fecha<'$FI' OR C.tipo='AC') AND
                                              DC.id_cuenta_contable='".$CUENTA_CONTABLE[$i]["id_cuenta_contable"]."' AND
                                              DC.operacion='D'                                             
                                            ");
  $CUENTA_CONTABLE[$i]["saldo_previo_D"]=isset($CUENTA_CONTABLE[$i]["saldo_previo_D"][0][0])?$CUENTA_CONTABLE[$i]["saldo_previo_D"][0][0]:0;
  
  $CUENTA_CONTABLE[$i]["saldo_previo_H"]=$db->Execute("select
                                              sum(DC.monto)
                                            from
                                              modulo_base.comprobante as C,
                                              modulo_base.detalle_contable as DC
                                            where
																							C.contabilizado AND
                                              EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                              C.id=DC.id_comprobante AND
                                              (C.fecha<'$FI' OR C.tipo='AC') AND
                                              DC.id_cuenta_contable='".$CUENTA_CONTABLE[$i]["id_cuenta_contable"]."' AND
                                              DC.operacion='H'                                             
                                            ");
  $CUENTA_CONTABLE[$i]["saldo_previo_H"]=isset($CUENTA_CONTABLE[$i]["saldo_previo_H"][0][0])?$CUENTA_CONTABLE[$i]["saldo_previo_H"][0][0]:0;
  
}




class PDF_P extends FPDF{
	public $sw=false;
	function Header(){
		global $organismo, $tam_ancho;
		$this->SetFont('helvetica','',10);
		if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
      $this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$GLOBALS['MARGEN_LEFT'],$GLOBALS['MARGEN_TOP']-8,$tam_ancho);
    elseif(SIGA::databasePath()."/config/logo_02.jpg")
      $this->Image(SIGA::databasePath()."/config/logo_02.jpg",$GLOBALS['MARGEN_LEFT'],$GLOBALS['MARGEN_TOP']-8,40);
		
		$this->Ln(5);
		
		$this->Cell(155,4,'','',0,'R',0);$this->Cell(35,4,utf8_decode("Fecha: ".date("d/m/Y")),'',1,'L',0);
		
 		$this->Cell(155,5,'','',0,'R',0);$this->Cell(35,5,utf8_decode('Página: '.$this->PageNo().' de {nb}'),'',1,'L');

		$this->SetY(23);
		$this->SetFont('helvetica','B',13);
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("BALANCE DE COMPROBANCIÓN"),'',1,'C',0);

		$this->SetFont('helvetica','',10);
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("DEL ".formatDate($GLOBALS['FI'])." AL ".formatDate($GLOBALS['FF'])),'',1,'C',0);
		//$this->SetFont('helvetica','',10);

		$this->Ln(5);
		//$this->sw=false;
/*
		if($this->sw){
			$I=$GLOBALS['i'];
			//cuenta
			$this->SetFont('helvetica','B',10);
			$this->Cell(35,5,"CUENTA: ",'',0,'',1);
			$this->SetFont('helvetica','',10);
			$this->Cell($GLOBALS['tam_ancho'],5,$GLOBALS['CUENTA_CONTABLE'][$I]["id_ci"]." ".$GLOBALS['DENOMINACION_CTA_CONTABLE'][$I][0]["denominacion_cta_contable"],'',1,'',1);

			//saldo
			$this->SetFont('helvetica','B',10);
			$this->Cell(35,5,"SALDO ANTERIOR: ",'',0,'',1);
			$this->SetFont('helvetica','',10);
			$this->Cell($GLOBALS['tam_ancho'],5,number_format($GLOBALS['SALDO'],2,",","."),'',1,'',1);
			}
*/
		$this->CabeceraTabla();
		$this->sw=false;
		}
	function Footer(){return;
		$this->Line($this->lMargin,$this->GetY(),$this->lMargin+$GLOBALS['tam_ancho'],$this->GetY());
		}
    
  
    
	function CabeceraTabla(){
		$this->SetFont('helvetica','B',$GLOBALS['font_size_base2']);
		$this->SetFillColor(200,200,200);
		$this->Cell($GLOBALS['tam_codigo'],5,utf8_decode("CUENTA"),'LTB',0,'C',1);
		$this->Cell($GLOBALS['tam_denominacion'],5,utf8_decode("DENOMINACIÓN"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,utf8_decode("SALDO PREVIO"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,utf8_decode("DEBE"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,utf8_decode("HABER"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,utf8_decode("SALDO"),'RTB',1,'C',1);
		$this->SetFont('helvetica','',$GLOBALS['font_size_base']);
		$this->SetFillColor(255,255,255);
    $this->SetFont('helvetica','',$GLOBALS['font_size_base3']);
		//$this->SetFont('helvetica','',9);
    $this->Ln(1);
		}
	}


$pdf=new PDF_P("P","mm","letter");
$pdf->SetAutoPageBreak(true,15);
$tam_ancho=190;
$tam_codigo=25;
$tam_monto=20;
$tam_denominacion=$tam_ancho-($tam_codigo+$tam_monto*4);
$MARGEN_LEFT=13;
$MARGEN_TOP=15;

$font_size_base=9;
$font_size_base2=7;
$font_size_base3=7.5;
$alto_fila_base=4;$alto_fila_base2=5;

$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);
$pdf->SetLineWidth(0.2);
$pdf->SetFillColor(255,255,255);

$pdf->AddPage();  

  
$TOTAL_DEBE=0;
$TOTAL_HABER=0;


for($i=0;$i<count($CUENTA_CONTABLE);$i++){
  $SALDO_PREVIO=0;
  $SALDO=0;
  
  $MONTO_DEBE=$CUENTA_CONTABLE[$i]["saldo_D"];
  $MONTO_HABER=$CUENTA_CONTABLE[$i]["saldo_H"];
  
  $TOTAL_DEBE+=$MONTO_DEBE;
  $TOTAL_HABER+=$MONTO_HABER;
  
  if($CUENTA_CONTABLE[$i]["aumenta_debe"]==="t"){
		$SALDO_PREVIO=$CUENTA_CONTABLE[$i]["saldo_previo_D"]-$CUENTA_CONTABLE[$i]["saldo_previo_H"];
    $SALDO=$SALDO_PREVIO+$MONTO_DEBE-$MONTO_HABER;
  }
	else{
		$SALDO_PREVIO=$CUENTA_CONTABLE[$i]["saldo_previo_H"]-$CUENTA_CONTABLE[$i]["saldo_previo_D"];
    $SALDO=$SALDO_PREVIO-$MONTO_DEBE+$MONTO_HABER;
	}
    
  
  $pdf->Cell($tam_codigo,4,utf8_decode($CUENTA_CONTABLE[$i]["cuenta_contable"]),'',0,'C',1);
  $pdf->Cell($tam_denominacion,4,utf8_decode(''),'',0,'L',1);
  $pdf->Cell($tam_monto,4,utf8_decode(number_format($SALDO_PREVIO,2,",",".")),'',0,'R',1);
  $pdf->Cell($tam_monto,4,utf8_decode(number_format($MONTO_DEBE,2,",",".")),'',0,'R',1);
  $pdf->Cell($tam_monto,4,utf8_decode(number_format($MONTO_HABER,2,",",".")),'',0,'R',1);
  $pdf->Cell($tam_monto,4,utf8_decode(number_format($SALDO,2,",",".")),'',0,'R',1);
  $pdf->SetX($pdf->lMargin+$tam_codigo);
  $pdf->MultiCell($tam_denominacion,3.5,utf8_decode($CUENTA_CONTABLE[$i]["denominacion"]."."),'','L',1);
  $pdf->Ln(1);  
}

$pdf->Cell($tam_ancho,1,"","T",1);
$pdf->Cell($tam_codigo+$tam_denominacion+$tam_monto,4,utf8_decode(""),'',0,'C',1);
$pdf->Cell($tam_monto,4,utf8_decode(number_format($TOTAL_DEBE,2,",",".")),'',0,'R',1);
$pdf->Cell($tam_monto,4,utf8_decode(number_format($TOTAL_HABER,2,",",".")),'',0,'R',1);


$pdf->AliasNbPages();
$pdf->Output("balance_comprobacion.pdf","I");


?>