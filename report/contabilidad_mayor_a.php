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

$anio=SIGA::data();

if(substr_compare($FI,$anio,0,4)!=0){
  print "La fecha de inicio con coincide con el año de trabajo.";exit;
}
if(substr_compare($FF,$anio,0,4)!=0){
  print "La fecha de culminacion con coincide con el año de trabajo.";exit;
}


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
  
  $orden="DESC";
  if($CUENTA_CONTABLE[$i]["aumenta_debe"]==='t'){
    $orden="ASC";
  }
  
  $CUENTA_CONTABLE[$i]["detalle"]=$db->Execute("select
                                              C.tipo,
                                              lpad(text(C.correlativo),10,'0') as correlativo,
                                              to_char(C.fecha,'DD/MM/YYYY') as fecha,
                                              C.concepto,
                                              DC.operacion,
                                              DC.monto
                                            from
                                              modulo_base.comprobante as C,
                                              modulo_base.detalle_contable as DC
                                            where
                                              C.contabilizado AND
                                              EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                              C.fecha BETWEEN '$FI' AND '$FF' AND
                                              C.tipo<>'AC' AND
                                              C.id=DC.id_comprobante AND
                                              DC.id_cuenta_contable='".$CUENTA_CONTABLE[$i]["id_cuenta_contable"]."'
                                            order by
                                              C.fecha,
                                              DC.operacion $orden,
                                              C.tipo,
                                              C.correlativo
                                            ");
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
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("LIBRO MAYOR"),'',1,'C',0);

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
		//$this->CabeceraTabla();
		$this->sw=false;
		}
	function Footer(){return;
		$this->Line($this->lMargin,$this->GetY(),$this->lMargin+$GLOBALS['tam_ancho'],$this->GetY());
		}
    
  function CuentaContable(){
    global $CUENTA_CONTABLE, $i, $SALDO;
    //cuenta
    $this->SetFont('helvetica','B',10);
    $this->Cell(35,5,"CUENTA: ",'',0,'',1);
    $this->SetFont('helvetica','',10);
    //$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode($CUENTA_CONTABLE[$i]["cuenta_contable"]." ".$CUENTA_CONTABLE[$i]["denominacion"]),'',1,'',1);
    //$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode($CUENTA_CONTABLE[$i]["cuenta_contable"]." ".$CUENTA_CONTABLE[$i]["denominacion"]),'',1,'',1);
    $this->MultiCell($GLOBALS['tam_ancho']-35,5,utf8_decode($CUENTA_CONTABLE[$i]["cuenta_contable"]." ".$CUENTA_CONTABLE[$i]["denominacion"]."."),'','L',1);
    //saldo
    $this->SetFont('helvetica','B',10);
    $this->Cell(35,5,utf8_decode("SALDO ANTERIOR: "),'',0,'',1);
    $this->SetFont('helvetica','',10);
    $this->Cell($GLOBALS['tam_ancho'],5,utf8_decode(number_format($SALDO,2,",",".")),'',1,'',1);
  }
    
	function CabeceraTabla(){
		$this->SetFont('helvetica','B',$GLOBALS['font_size_base']);
		$this->SetFillColor(200,200,200);
		$this->Cell($GLOBALS['tam_codigo'],5,utf8_decode("FECHA"),'LTB',0,'C',1);
		$this->Cell($GLOBALS['tam_denominacion'],5,utf8_decode("DENOMINACIÓN"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,utf8_decode("DEBE"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,utf8_decode("HABER"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,utf8_decode("SALDO"),'RTB',1,'C',1);
		$this->SetFont('helvetica','',$GLOBALS['font_size_base']);
		$this->SetFillColor(255,255,255);
		$this->SetFont('helvetica','',9);
		}
	}


$pdf=new PDF_P("P","mm","letter");
$pdf->SetAutoPageBreak(true,15);
$tam_ancho=190;
$tam_codigo=20;
$tam_monto=25;
$tam_denominacion=$tam_ancho-($tam_codigo+$tam_monto*3);
$MARGEN_LEFT=13;
$MARGEN_TOP=15;

$font_size_base=9;
$font_size_base2=7;
$alto_fila_base=4;$alto_fila_base2=5;

$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);
$pdf->SetLineWidth(0.2);
$pdf->SetFillColor(255,255,255);




for($i=0;$i<count($CUENTA_CONTABLE);$i++){
  $SALDO=0;
  
  if($CUENTA_CONTABLE[$i]["aumenta_debe"]==="t")
		$SALDO=$CUENTA_CONTABLE[$i]["saldo_previo_D"]-$CUENTA_CONTABLE[$i]["saldo_previo_H"];
	else
		$SALDO=$CUENTA_CONTABLE[$i]["saldo_previo_H"]-$CUENTA_CONTABLE[$i]["saldo_previo_D"];
  
  $pdf->AddPage();  
  $pdf->CuentaContable();
  
  $pdf->CabeceraTabla();
  
  
  for($y=0;$y<count($CUENTA_CONTABLE[$i]["detalle"]);$y++){
    $FECHA_ASIENTO=$CUENTA_CONTABLE[$i]["detalle"][$y]["fecha"];
		$MONTO_DEBE="";
		$MONTO_HABER="";
		if($CUENTA_CONTABLE[$i]["detalle"][$y]["operacion"]=="D"){
			$MONTO_DEBE=number_format($CUENTA_CONTABLE[$i]["detalle"][$y]["monto"],2,",",".");
			if($CUENTA_CONTABLE[$i]["aumenta_debe"]==="t")
				$SALDO+=$CUENTA_CONTABLE[$i]["detalle"][$y]["monto"];
			else
				$SALDO-=$CUENTA_CONTABLE[$i]["detalle"][$y]["monto"];
			}
		else{
			$MONTO_HABER=number_format($CUENTA_CONTABLE[$i]["detalle"][$y]["monto"],2,",",".");
			if($CUENTA_CONTABLE[$i]["aumenta_debe"]==="t")
				$SALDO-=$CUENTA_CONTABLE[$i]["detalle"][$y]["monto"];
			else
				$SALDO+=$CUENTA_CONTABLE[$i]["detalle"][$y]["monto"];
			}

		$pdf->Cell($tam_codigo,4,utf8_decode($FECHA_ASIENTO),'T',0,'C',1);
		$pdf->Cell($tam_denominacion,4,utf8_decode(''),'',0,'L',1);
		$pdf->Cell($tam_monto,4,utf8_decode($MONTO_DEBE),'T',0,'R',1);
		$pdf->Cell($tam_monto,4,utf8_decode($MONTO_HABER),'T',0,'R',1);
		$pdf->Cell($tam_monto,4,utf8_decode(number_format($SALDO,2,",",".")),'T',0,'R',1);
		$pdf->SetX($pdf->lMargin+$tam_codigo);
		$pdf->MultiCell($tam_denominacion,4,utf8_decode($CUENTA_CONTABLE[$i]["detalle"][$y]["tipo"]."-".$CUENTA_CONTABLE[$i]["detalle"][$y]["correlativo"]."\n".$CUENTA_CONTABLE[$i]["detalle"][$y]["concepto"]."."),'T','L',1);

    
    
  }
  
  
  
  
  
}



$pdf->AliasNbPages();
$pdf->Output("libro_mayor.pdf","I");


?>