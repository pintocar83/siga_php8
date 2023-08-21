<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/letra_numero.php");

include_once("../library/fpdf/1.84/fpdf.php");


$config_report = SIGA::configuration(["ciudad","report/pago/ciudad"]);
$config = [
	"ciudad"          => "CUMANÁ"
];

if($config_report){
	if(isset($config_report["ciudad"]) && $config_report["ciudad"])	                         $config["ciudad"] = $config_report["ciudad"];
	if(isset($config_report["report/pago/ciudad"]) && $config_report["report/pago/ciudad"])	 $config["ciudad"] = $config_report["report/pago/ciudad"];
}



$db=SIGA::DBController();


$id=explode(",",SIGA::paramGet("id"));



class PDF_P extends FPDF{
	function Header(){
		global $CHEQUE, $ORDEN_PAGO, $config;
		//monto
		$this->SetFont('times','B',17);
		$this->Cell(141-8,1,utf8_decode(""),'',0,'',1);
		$this->Cell(54,1,utf8_decode("***".number_format($CHEQUE[0]["monto"],2,",",".")."***"),'',1,'L',1);


		$this->SetFont('times','B',13);
		//paguese a la orden de
		$Nombre=$CHEQUE[0]["persona"];
		$this->Ln(9+2);
		$this->Cell(10,6,utf8_decode(""),'',0,'',1);
		$this->Cell(160,6,utf8_decode("                 ".$Nombre),'',1,'L',1);

		$this->SetFont('times','B',13);
		//la cantidad de
		$cantidad="                 ".strtoupper(letra_numero($CHEQUE[0]["monto"],true));
		$this->Ln(1);
		$this->Cell(10,4,utf8_decode(""),'',0,'',1);
		$this->MultiCell(160,4,utf8_decode($cantidad),'','L');



		//fecha
		$this->SetY(38-4+2);
		$FECHA_CHEQUE=explode("/",$CHEQUE[0]["fecha"]);
		$this->Cell(25+10,12,utf8_decode(""),'',0,'',1);
		$this->Cell(52,12,utf8_decode($config["ciudad"].", ".$FECHA_CHEQUE[0]."/".$FECHA_CHEQUE[1]),'',0,'',1);
		$this->Cell(27,12,utf8_decode($FECHA_CHEQUE[2]),'',1,'',1);
		$Y3=$this->GetY();
		$Y3=$Y3-11;


		$this->SetFont('helvetica','B',13);
		//$this->Image("../../images/logo_institucional_01.jpg",$this->lMargin+10,$Y3+40,40);
		$this->Image(SIGA::databasePath()."/config/logo_01.jpg",$this->lMargin+10,$Y3+40,40);

		$this->Text($this->lMargin+150,$Y3+48,utf8_decode($CHEQUE[0]["tipo"]."-".$CHEQUE[0]["correlativo"]));

		$this->SetY($Y3);
  	$this->SetFont('helvetica','',10);

		//area del recibo
		$separacion_comprobante=53;
		$this->Ln($separacion_comprobante);


		//cheque nº
		$this->SetFont('helvetica','B',13);
		$this->Cell(15,6,"",'',0,'',1);$this->Cell(45,6,utf8_decode($CHEQUE[0]["numero"]),'',0,'C',0);

		$Y=$this->GetY();
		//banco
		$this->SetFont('helvetica','B',11);
		$this->SetXY($this->lMargin+60,$Y-2);
		$this->MultiCell(77,3,utf8_decode($CHEQUE[0]["banco"]."\n".$CHEQUE[0]["numero_cuenta"]),'','C',1);


		//fecha
		$this->SetFont('helvetica','',10);
		$this->SetXY($this->lMargin+140,$Y);
		$this->Cell(20,6,utf8_decode($CHEQUE[0]["fecha"]),'',1,'L',1);

		//pagado a
		$this->Ln(1);
		$this->Cell(23,4,"",'',0,'',1);$this->Cell(170,4,utf8_decode($Nombre),'',1,'L',1);

		//concepto
		$this->Ln(2);
		$this->Cell(23,4,"",'',0,'',1);$this->MultiCell(170,4,utf8_decode($CHEQUE[0]["concepto"]),'','',1);

		//ordenes de pago involucradas
		if($ORDEN_PAGO){
			for($m=0;$m<count($ORDEN_PAGO);$m++){
				$this->Cell(23,4,"",'',0,'',1);
				$this->Cell(30,4,utf8_decode($ORDEN_PAGO[$m]["tipo"]."-".$ORDEN_PAGO[$m]["correlativo"]),'',0,'L',1);
				$this->Cell(35,4,utf8_decode("FECHA: ".$ORDEN_PAGO[$m]["fecha"]),'',0,'L',1);
				$this->Cell(15,4,utf8_decode("TOTAL: "),'',0,'L',1);
				$this->Cell(23,4,utf8_decode(number_format($ORDEN_PAGO[$m]["monto"],2,",",".")),'',0,'R',1);
				$this->Cell(10,4,"",'',0,'',1);
				$this->Cell(33,4,utf8_decode("MONTO PAGADO: "),'',0,'L',1);
				$this->Cell(23,4,utf8_decode(number_format($ORDEN_PAGO[$m]["monto_pagado"],2,",",".")),'',1,'R',1);
				}
			}

		//AREA DE DETALLES PRESUPUESTARIOS/CONTABLES
		$altura_detalles=128;
		$this->SetY($this->tMargin+$altura_detalles);

		}
	}


$pdf=new PDF_P("P","mm","letter");


$MARGEN_LEFT=1;
$MARGEN_TOP=9;

$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);


for($i=0;$i<count($id);$i++){
		$_id=$id[$i];

		$CHEQUE=$db->Execute("SELECT
														C.tipo,
														lpad(text(C.correlativo),10,'0') as correlativo,
														to_char(C.fecha,'DD/MM/YYYY') as fecha,
														C.concepto,
														(case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as persona_identificacion,
														replace(P.denominacion,';',' ') as persona,
														CB.numero,
														CB.monto,
														BC.numero_cuenta,
														B.banco
												FROM
														modulo_base.comprobante as C,
														modulo_base.persona as P,
														modulo_base.comprobante_bancario as CB,
														modulo_base.banco_cuenta as BC,
														modulo_base.banco as B
												WHERE
														C.id=$_id AND
														C.id_persona=P.id AND
														C.id=CB.id_comprobante AND
														CB.id_banco_cuenta=BC.id AND
                            BC.id_banco=B.id");


		if(count($CHEQUE)==0)
				continue;


		$ORDEN_PAGO=$db->Execute("SELECT
																		C.tipo,
																		lpad(text(C.correlativo),10,'0') as correlativo,
																		to_char(C.fecha,'DD/MM/YYYY') as fecha,
																		CP.monto_pagado,
																		(select sum(DC.monto) from modulo_base.detalle_contable as DC where DC.id_comprobante=C.id and DC.operacion='H') as monto
																FROM
																		modulo_base.comprobante_previo_monto_pagado CP,
																		modulo_base.comprobante as C
																WHERE
																		CP.id_comprobante='$_id' AND
																		C.id=CP.id_comprobante_previo");



		$DETALLE_PRESUPUESTARIO=$db->Execute("SELECT
																						*,
																						_formatear_estructura_presupuestaria(DP.id_accion_subespecifica) as estructura_presupuestaria,
																						_formatear_cuenta_presupuestaria(DP.id_cuenta_presupuestaria) as cuenta_presupuestaria
																					FROM modulo_base.detalle_presupuestario AS DP, modulo_base.cuenta_presupuestaria as CP
																					WHERE DP.id_comprobante='$_id' AND DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria order by cuenta_presupuestaria");

    $DETALLE_CONTABLE=$db->Execute("SELECT
																				*,
																				_formatear_cuenta_contable(DC.id_cuenta_contable) as cuenta_contable
																			FROM modulo_base.detalle_contable AS DC, modulo_base.cuenta_contable as CC
																			WHERE DC.id_comprobante='$_id' AND DC.id_cuenta_contable=CC.id_cuenta_contable order by operacion, cuenta_contable");



	$pdf->SetFillColor(255,255,255);
	$pdf->AddPage();


	$tam_ancho=175;
	$tam_cuenta=48;
	$tam_montos1=20;
	$tam_montos2=27;
	$tam_montos3=27;
	$tam_denominacion=$tam_ancho-($tam_cuenta+$tam_montos1+$tam_montos2+$tam_montos3);

	for($j=0;$j<count($DETALLE_PRESUPUESTARIO);$j++){
		$pdf->Ln(2);
		$pdf->SetFont('helvetica','',8.5);

		$pdf->Cell(5,4,"",'',0,'',1);

		$Y=$pdf->GetY();
		$pdf->Cell($tam_cuenta,4,utf8_decode($DETALLE_PRESUPUESTARIO[$j]["estructura_presupuestaria"]." / ".$DETALLE_PRESUPUESTARIO[$j]["cuenta_presupuestaria"]),'',0,'R',1);
		$pdf->MultiCell($tam_denominacion,4,utf8_decode($DETALLE_PRESUPUESTARIO[$j]["denominacion"]."."),'','',1);
		$Y2=$pdf->GetY();

		$pdf->SetXY($pdf->lMargin+5+$tam_cuenta+$tam_denominacion,$Y);

		$pdf->SetFont('helvetica','',11);
		$pdf->Cell($tam_montos1,4,utf8_decode(number_format($DETALLE_PRESUPUESTARIO[$j]["monto"],2,",",".")),'',0,'R',1);
		$pdf->Cell($tam_montos2,4,utf8_decode(""),'',0,'R',1);
		$pdf->Cell($tam_montos3,4,utf8_decode(""),'',1,'R',1);
		$pdf->SetY($Y2);
	}

	for($j=0;$j<count($DETALLE_CONTABLE);$j++){
		$pdf->Ln(2);
		$pdf->SetFont('helvetica','',8.5);

		$pdf->Cell(5,4,"",'',0,'',1);

		$Y=$pdf->GetY();
		$pdf->Cell($tam_cuenta,4,utf8_decode($DETALLE_CONTABLE[$j]["cuenta_contable"]),'',0,'R',1);
		$X=$pdf->GetX();
		$pdf->Cell($tam_denominacion,4,utf8_decode(""),'',0,'R',1);
		$pdf->Cell($tam_montos1,4,utf8_decode(""),'',0,'R',1);
		$pdf->SetFont('helvetica','',11);
		$monto_debe="";
		$monto_haber="";
		if($DETALLE_CONTABLE[$j]["operacion"]=="D") $monto_debe=number_format($DETALLE_CONTABLE[$j]["monto"],2,",",".");
		else                                        $monto_haber=number_format($DETALLE_CONTABLE[$j]["monto"],2,",",".");

		$pdf->Cell($tam_montos2,4,utf8_decode("$monto_debe"),'',0,'R',1);
		$pdf->Cell($tam_montos3,4,utf8_decode("$monto_haber"),'',1,'R',1);

		$pdf->SetFont('helvetica','',8.5);
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell($tam_denominacion,4,utf8_decode($DETALLE_CONTABLE[$j]["denominacion"]."."),'','',1);

	}

}//for($i=0;$i<count($IDCheque);$i++)


$pdf->Output("cheque.pdf","I");

?>