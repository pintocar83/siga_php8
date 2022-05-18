<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/unformatDate.php");


include_once("../library/fpdf/1.84/fpdf.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");





$id_banco_cuenta=explode(",",SIGA::paramGet("id_banco_cuenta"));
$FECHA_INICIO=SIGA::paramGet("fecha_inicio");
$FECHA_CULMINACION=SIGA::paramGet("fecha_culminacion");
$SALDO_PREVIO=0;



function CabeceraTabla(){
		global $pdf, $t_fecha, $t_operacion, $t_doc, $t_concepto, $t_persona, $t_ingresos, $t_egresos;
		$pdf->SetFillColor(255,255,255);
				
		$pdf->SetFont('helvetica','B',6);
		$pdf->Cell($t_fecha,4,utf8_decode('FECHA'),'B',0,'C',0);
		$pdf->Cell($t_operacion,4,utf8_decode('OPERACIÓN'),'B',0,'C',0);
		$pdf->Cell($t_concepto,4,utf8_decode('CONCEPTO'),'B',0,'C',0);
		$pdf->Cell($t_persona,4,utf8_decode('PERSONA'),'B',0,'C',0);
		$pdf->Cell($t_ingresos,4,utf8_decode('INGRESOS'),'B',0,'C',0);
		$pdf->Cell($t_egresos,4,utf8_decode('EGRESOS'),'B',1,'C',0);
		$pdf->SetFillColor(255,255,255);
}


class PDF_P extends FPDF{
	var $MARGEN_LEFT;
	var $MARGEN_TOP;
	var $ANCHO;
	function Medidas($MARGEN_LEFT, $MARGEN_TOP, $ANCHO){
		$this->MARGEN_LEFT=$MARGEN_LEFT;
		$this->MARGEN_TOP=$MARGEN_TOP;
		$this->ANCHO=$ANCHO;

		$this->SetLeftMargin($MARGEN_LEFT);
		$this->SetTopMargin($MARGEN_TOP);
		}
	function Header(){
		global $BANCO_CUENTA, $FECHA_INICIO, $FECHA_CULMINACION, $SALDO_PREVIO, $organismo;
		
		$this->SetFillColor(255,255,255);

		$this->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,215);

    $this->Ln(12);
		$this->SetFont('helvetica','',8);
		$this->Cell($this->ANCHO,6,utf8_decode('Página: '.$this->PageNo().' de {nb}'),'',1,'R');

		
		$this->SetFont('helvetica','B',12);
		$this->Cell($this->ANCHO,5,utf8_decode('ESTADO DE CUENTA'),'',1,'C');
    $this->SetFont('helvetica','',9);
		$this->Cell($this->ANCHO,5,utf8_decode("DEL $FECHA_INICIO AL $FECHA_CULMINACION"),'',1,'C');
		
		
		
    $this->Ln(5);
    $t_titulo=35;
		$this->SetFont('helvetica','B',9);
		$this->Cell($t_titulo,4,utf8_decode('BANCO'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->MultiCell($this->ANCHO-($t_titulo+2),4,utf8_decode($BANCO_CUENTA[0]["banco"]),'','L',1);
    
    $this->SetFont('helvetica','B',9);
		$this->Cell($t_titulo,4,utf8_decode('TIPO DE CUENTA'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->MultiCell($this->ANCHO-($t_titulo+2),4,utf8_decode($BANCO_CUENTA[0]["cuenta_tipo"]),'','L',1);
    
    $this->SetFont('helvetica','B',9);
		$this->Cell($t_titulo,4,utf8_decode('NÚMERO DE CUENTA'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->MultiCell($this->ANCHO-($t_titulo+2),4,utf8_decode($BANCO_CUENTA[0]["numero_cuenta"]),'','L',1);
    
    $this->SetFont('helvetica','B',9);
		$this->Cell($t_titulo,4,utf8_decode('SALDO ANTERIOR'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->MultiCell($this->ANCHO-($t_titulo+2),4,utf8_decode(number_format($SALDO_PREVIO,2,",",".")),'','L',1);

		$this->Ln(2);
		}
	}


$pdf=new PDF_P("P","mm","letter");
$pdf->SetAutoPageBreak(false);

$ancho=180;
$pdf->Medidas(17.5,10,$ancho);

$t_fecha=15;
$t_operacion=25;
$t_persona=30;
$t_ingresos=$t_egresos=18;

$t_concepto=$ancho-($t_fecha+$t_operacion+$t_persona+$t_ingresos+$t_egresos);





for($i=0;$i<count($id_banco_cuenta);$i++){
		//busco inf del comprobante
		$_i=$i;
		$_id=$id_banco_cuenta[$i];
  
  
  
		$BANCO_CUENTA=$db->Execute("SELECT
                                BC.*,
                                B.banco,
                                BCT.denominacion as cuenta_tipo,
                                _formatear_cuenta_contable(BC.id_cuenta_contable) as cuenta_contable,
                                CC.denominacion as denominacion_contable
                              FROM
                                modulo_base.banco as B,
                                modulo_base.banco_cuenta as BC,
                                modulo_base.banco_cuenta_tipo as BCT,
                                modulo_base.cuenta_contable as CC
                              WHERE
                                BC.id='$_id' AND
                                BC.id_banco=B.id AND
                                BC.id_banco_cuenta_tipo=BCT.id AND
                                BC.id_cuenta_contable=CC.id_cuenta_contable");
  
	
		$BANCO_MOVIMIENTO=$db->Execute("SELECT
								C.id,
								C.tipo,
								lpad(text(C.correlativo),10,'0') as correlativo,
								to_char(C.fecha,'DD/MM/YYYY') as fecha,
								(split_part(P.denominacion,';',1)||' '||split_part(P.denominacion,';',3)) as persona,
								CB.numero,
								CB.monto,
								BMT.codigo as operacion_codigo,
								BMT.operacion,
								C.concepto,
								C.contabilizado,
								CT.denominacion as tipo_denominacion
						FROM
								modulo_base.comprobante as C LEFT JOIN modulo_base.persona as P ON C.id_persona=P.id,
								modulo_base.comprobante_bancario as CB,
								modulo_base.banco_movimiento_tipo as BMT,
								modulo_base.comprobante_tipo as CT
						WHERE
								CB.id_banco_cuenta='$_id' AND
								C.fecha BETWEEN '".unformatDate($FECHA_INICIO)."' AND '".unformatDate($FECHA_CULMINACION)."' AND
								C.id=CB.id_comprobante AND
								CB.id_banco_movimiento_tipo=BMT.id AND
								C.tipo=CT.tipo AND 
								EXTRACT(YEAR FROM C.fecha)=".SIGA::data()."
						ORDER BY
								C.fecha,
								operacion,
								correlativo
				");
	
		$SALDO_PREVIO=$db->Execute("select sum(case when BMT.operacion = 'D' then CB.monto else -CB.monto end) as monto
                                                  from
                                                    modulo_base.comprobante_bancario as CB,
																										modulo_base.banco_movimiento_tipo as BMT,
                                                    modulo_base.comprobante as C
                                                  where
																										CB.id_banco_cuenta='$_id' AND
                                                    EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                                    C.fecha < '".unformatDate($FECHA_INICIO)."' AND
                                                    C.id=CB.id_comprobante AND
																										CB.id_banco_movimiento_tipo=BMT.id");
	
		$SALDO_PREVIO=is_numeric($SALDO_PREVIO[0][0])?$SALDO_PREVIO[0][0]:0;
		//print_r($BANCO_MOVIMIENTO);exit;
		$SUMA_DEBE=0;
		$SUMA_HABER=0;
		
		$pdf->AddPage();
	
	
		CabeceraTabla();
		$pdf->Ln(1);
		$pdf->SetFont('helvetica','',7);
		for($j=0;$j<count($BANCO_MOVIMIENTO);$j++){
				$pdf->Cell($t_fecha,3,utf8_decode($BANCO_MOVIMIENTO[$j]["fecha"]),'',0,'C',0);
				$pdf->Cell($t_operacion,3,utf8_decode($BANCO_MOVIMIENTO[$j]["operacion_codigo"]." ".$BANCO_MOVIMIENTO[$j]["numero"]),'',0,'L',0);
				$x=$pdf->GetX();
				$y=$pdf->GetY();
				$pdf->Cell($t_concepto,3,utf8_decode(""),'',0,'L',0);
				$pdf->Cell($t_persona,3,utf8_decode($BANCO_MOVIMIENTO[$j]["persona"]),'',0,'L',0);
				
				$monto_debe="";$monto_haber="";
				if($BANCO_MOVIMIENTO[$j]["operacion"]=="D"){
						$monto_debe=number_format($BANCO_MOVIMIENTO[$j]["monto"],2,",",".");
						$SUMA_DEBE+=$BANCO_MOVIMIENTO[$j]["monto"];
				}
				else{
						$monto_haber=number_format($BANCO_MOVIMIENTO[$j]["monto"],2,",",".");
						$SUMA_HABER+=$BANCO_MOVIMIENTO[$j]["monto"];
				}
				$pdf->Cell($t_ingresos,3,utf8_decode("$monto_debe"),'',0,'R',1);
				$pdf->Cell($t_egresos,3,utf8_decode("$monto_haber"),'',0,'R',1);
				$pdf->Cell(50,5,utf8_decode(""),'',0,'',1);
				
				$pdf->SetXY($x,$y);
				$pdf->MultiCell($t_concepto,3,utf8_decode($BANCO_MOVIMIENTO[$j]["concepto"]."."),'','L',0);
				$pdf->Ln(1);
				
				if($pdf->GetY()>260){
						$pdf->Cell($ancho,1,utf8_decode(""),'T',1,'C',0);
						$pdf->AddPage();
						CabeceraTabla();
						$pdf->Ln(1);
						$pdf->SetFont('helvetica','',7);
						
				}
				
		}
		$pdf->SetFont('helvetica','B',7);
		$pdf->Cell($ancho,1,utf8_decode(""),'T',1,'C',1);
		$pdf->Cell($t_fecha+$t_operacion+$t_concepto+$t_persona,3,utf8_decode("TOTAL :"),'',0,'R',0);
		
		$pdf->Cell($t_ingresos,3,utf8_decode(number_format($SUMA_DEBE,2,",",".")),'',0,'R',0);
		$pdf->Cell($t_egresos,3,utf8_decode(number_format($SUMA_HABER,2,",",".")),'',1,'R',0);
		
		$SALDO=$SALDO_PREVIO+$SUMA_DEBE-$SUMA_HABER;
		$pdf->Cell($t_fecha+$t_operacion+$t_concepto+$t_persona,5,utf8_decode("SALDO :"),'',0,'R',0);
		$pdf->Cell($t_ingresos+$t_egresos,5,utf8_decode(number_format($SALDO,2,",",".")),'',1,'R',0);
		
	

}


$pdf->AliasNbPages();
$pdf->Output("estado_cuenta.pdf","I");
?>