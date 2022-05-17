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
$mostrar_fecha=0;

$COMPROBANTE=$db->Execute("SELECT
                                    C.id,
																		C.tipo,
																		lpad(text(C.correlativo),10,'0') as correlativo,
																		to_char(C.fecha,'DD/MM/YYYY') as fecha,
																		C.concepto
																	FROM
																		modulo_base.comprobante as C
																	WHERE
																		C.contabilizado AND
                                    C.fecha BETWEEN '$FI' AND '$FF'
                                  ORDER BY
                                    C.fecha,
                                    C.tipo,
                                    C.correlativo");

for($i=0;$i<count($COMPROBANTE);$i++){
  $COMPROBANTE[$i]["detalle_contable"]=$db->Execute("SELECT
                                                      DC.id_cuenta_contable,
                                                      DC.operacion,
                                                      DC.monto,
                                                      CC.denominacion,
                                                      _formatear_cuenta_contable(DC.id_cuenta_contable) as cuenta_contable
                                                    FROM modulo_base.detalle_contable AS DC, modulo_base.cuenta_contable as CC
                                                    WHERE DC.id_comprobante='".$COMPROBANTE[$i]["id"]."' AND DC.id_cuenta_contable=CC.id_cuenta_contable");


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
		if ($GLOBALS['mostrar_fecha']=="true")
		{
			$this->Cell(155,4,'','',0,'R',0);$this->Cell(35,4,utf8_decode("Fecha: ".date("d/m/Y")),'',1,'L',0);
		}
 		$this->Cell(155,5,'','',0,'R',0);$this->Cell(35,5,utf8_decode('Página: '.$this->PageNo().' de {nb}'),'',1,'L');

		$this->SetY(23);
		$this->SetFont('helvetica','B',13);
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("LIBRO DIARIO"),'',1,'C',0);

		$this->SetFont('helvetica','',10);
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("DEL ".formatDate($GLOBALS['FI'])." AL ".formatDate($GLOBALS['FF'])),'',1,'C',0);
		//$this->SetFont('helvetica','',10);

		$this->Ln(5);
		//$this->sw=false;
		}

	function CabeceraTabla(){
    global $tam_fecha, $tam_monto, $tam_denominacion, $tam_monto, $font_size_base;
		$this->SetFont('helvetica','B',7);
		$this->SetFillColor(200,200,200);
		$this->Cell($GLOBALS['tam_fecha'],4,utf8_decode("FECHA"),'LTB',0,'C',1);
		$this->Cell($GLOBALS['tam_codigo'],4,utf8_decode("CUENTA"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_denominacion'],4,utf8_decode("DENOMINACIÓN"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],4,utf8_decode("DEBE"),'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],4,utf8_decode("HABER"),'RTB',1,'C',1);
		$this->SetFont('helvetica','',$GLOBALS['font_size_base']);
		$this->SetFillColor(255,255,255);
		}
	}

$pdf=new PDF_P("P","mm","letter");
$pdf->SetAutoPageBreak(true,15);
$tam_ancho=195;
$tam_fecha=20;
$tam_codigo=25;
$tam_monto=25;
$tam_denominacion=$tam_ancho-($tam_fecha+$tam_codigo+$tam_monto*2);
$MARGEN_LEFT=10;
$MARGEN_TOP=15;

$font_size_base=8;
$font_size_base2=6;
$alto_fila_base=4;$alto_fila_base2=5;

$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);
$pdf->SetLineWidth(0.2);
$pdf->SetFillColor(255,255,255);
$Contador=0;
$Lineas=10;
$SUB_TOTAL=0;
$i=0;
$k=0;
$y=0;
$SUMA_D=0;
$SUMA_H=0;
$SUMA_D_COMPROBANTE=0;
$SUMA_H_COMPROBANTE=0;

$pdf->sw=true;
$pdf->AddPage();

//while(1){

//$pdf->CabeceraTabla();

for(;$k<count($COMPROBANTE);$k++){
    if(count($COMPROBANTE[$k]["detalle_contable"])==0) continue;
  
		$SUMA_D_COMPROBANTE=0;
		$SUMA_H_COMPROBANTE=0;
		//if(!$COMPROBANTE[$k]["detalle_contable"]) continue;

		
    //if($pdf->GetY()<250-5){      
      $pdf->CabeceraTabla();
    //}



    
		for(;$y<count($COMPROBANTE[$k]["detalle_contable"]);$y++){
      //if($pdf->GetY()>250){
      //  $pdf->Cell($tam_ancho,1,utf8_decode(""),'T',1,'C',1);
      //  $pdf->AddPage();
      //  $pdf->CabeceraTabla();
      //}
      
			$FECHA_ASIENTO="";
			if($y==0)
				$FECHA_ASIENTO=$COMPROBANTE[$k]["fecha"];
			$MONTO_DEBE=0;
			$MONTO_HABER=0;
			
			if($COMPROBANTE[$k]["detalle_contable"][$y]["operacion"]=="D"){
				$MONTO_DEBE=$COMPROBANTE[$k]["detalle_contable"][$y]["monto"];
				$SUMA_D+=$COMPROBANTE[$k]["detalle_contable"][$y]["monto"];
				$SUMA_D_COMPROBANTE+=$COMPROBANTE[$k]["detalle_contable"][$y]["monto"];
				}
			else{
				$MONTO_HABER=$COMPROBANTE[$k]["detalle_contable"][$y]["monto"];
				$SUMA_H+=$COMPROBANTE[$k]["detalle_contable"][$y]["monto"];
				$SUMA_H_COMPROBANTE+=$COMPROBANTE[$k]["detalle_contable"][$y]["monto"];
				}

			$pdf->Cell($tam_fecha,4,utf8_decode($FECHA_ASIENTO),0,0,'C',0);
			$pdf->Cell($tam_codigo,4,utf8_decode($COMPROBANTE[$k]["detalle_contable"][$y]["cuenta_contable"]),0,0,'C',0);
			$pdf->Cell($tam_denominacion,4,utf8_decode(''),0,0,'L',0);
			$pdf->Cell($tam_monto,4,utf8_decode(number_format($MONTO_DEBE,2,",",".")),0,0,'R',0);
			$pdf->Cell($tam_monto,4,utf8_decode(number_format($MONTO_HABER,2,",",".")),0,0,'R',0);
			$pdf->SetX($pdf->lMargin+$tam_fecha+$tam_codigo);
			$pdf->MultiCell($tam_denominacion,4,utf8_decode($COMPROBANTE[$k]["detalle_contable"][$y]["denominacion"]."."),0,'L',0);

			$Contador++;
		}//for($y=0;$y<count($COMPROBANTE[$k]["detalle_contable"]);$y++)
    
    
    
    
		if($Contador==0){
			break;
			}
      
    //if($pdf->GetY()>250){
    //  $pdf->Cell($tam_ancho,1,utf8_decode(""),'T',1,'C',1);
    //  $pdf->AddPage();
    //  //$pdf->CabeceraTabla();
    //}
    
    
		$y=0;

		$pdf->SetFont('helvetica','B',$GLOBALS['font_size_base']);
		$pdf->Cell($tam_fecha+$tam_codigo+$tam_denominacion,4,utf8_decode($COMPROBANTE[$k]["tipo"].'-'.$COMPROBANTE[$k]["correlativo"]),'T',0,'L',0);
		$pdf->Cell($tam_monto,4,utf8_decode(number_format($SUMA_D_COMPROBANTE,2,",",".")),'T',0,'R',0);
		$pdf->Cell($tam_monto,4,utf8_decode(number_format($SUMA_H_COMPROBANTE,2,",",".")),'T',1,'R',0);
		$pdf->SetX($pdf->lMargin);
		$pdf->MultiCell($tam_ancho,4,utf8_decode($COMPROBANTE[$k]["concepto"]),0,'L',0);
		$pdf->SetFont('helvetica','',$GLOBALS['font_size_base']);
		
		$pdf->SetFillColor(0,0,0);
		$pdf->Cell($tam_ancho,0.5,utf8_decode(''),1,1,'',1);
		$pdf->SetFillColor(255,255,255);

		$pdf->Ln(5);

		if($k==count($COMPROBANTE)-1) //si es el último comprobante imprimimos los totales
		{
			$pdf->Ln(5);
			$pdf->SetFont('helvetica','B',$GLOBALS['font_size_base']);
			$pdf->SetFillColor(200,200,200);
			$pdf->Cell($tam_fecha,5,utf8_decode(''),'TB',0,'C',1);
			$pdf->Cell($tam_codigo,5,utf8_decode(''),'TB',0,'L',1);
			$pdf->Cell($tam_denominacion,5,utf8_decode('TOTALES'),'TB',0,'R',1);
			$pdf->Cell($tam_monto,5,utf8_decode(number_format($SUMA_D,2,",",".")),'TB',0,'R',1);
			$pdf->Cell($tam_monto,5,utf8_decode(number_format($SUMA_H,2,",",".")),'TB',1,'R',1);
		}

	}//for($k=0;$k<count($COMPROBANTE);$k++)



$pdf->AliasNbPages();
$pdf->Output("libro_diario.pdf","I");





?>