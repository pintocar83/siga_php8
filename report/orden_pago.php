<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/letra_numero.php");

include_once("../library/fpdf/1.7/fpdf.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");


$IDComprobante=explode(",",SIGA::paramGet("id"));



$MONTO_TOTAL=0;


function CabeceraDP(){
		global $pdf, $t_acc_pro_mp, $t_cuenta_mp, $t_denom_mp, $t_monto_mp;
		$pdf->SetFillColor(216,216,216);
		$pdf->SetFont('helvetica','B',10);
		$pdf->Cell($pdf->ANCHO,6,utf8_decode('DETALLES PRESUPUESTARIOS'),'LRT',1,'C',1);
		
		$pdf->SetFont('helvetica','B',9);
		$pdf->Cell($t_acc_pro_mp,4,utf8_decode('ACC/PRO'),'LB',0,'C',1);
		$pdf->Cell($t_cuenta_mp,4,utf8_decode('CUENTA'),'B',0,'C',1);
		$pdf->Cell($t_denom_mp,4,utf8_decode('DENOMINACIÓN'),'B',0,'C',1);
		$pdf->Cell($t_monto_mp,4,utf8_decode('MONTO'),'RB',1,'C',1);
		$pdf->SetFillColor(255,255,255);
}

function CabeceraDC(){
		global $pdf, $t_cuenta_mc, $t_denom_mc, $t_debe_mc, $t_haber_mc;
		$pdf->SetFillColor(216,216,216);
		$pdf->SetFont('helvetica','B',10);
		$pdf->Cell($pdf->ANCHO,6,utf8_decode('DETALLES CONTABLES'),'LRT',1,'C',1);
		$pdf->SetFont('helvetica','B',8);
		$pdf->Cell($t_cuenta_mc,4,utf8_decode('CUENTA'),'LB',0,'C',1);
		$pdf->Cell($t_denom_mc,4,utf8_decode('DENOMINACIÓN'),'B',0,'C',1);
		$pdf->Cell($t_debe_mc,4,utf8_decode('DEBE'),'B',0,'C',1);
		$pdf->Cell($t_haber_mc,4,utf8_decode('HABER'),'RB',1,'C',1);
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
		global $COMPROBANTE, $PERSONA_ID, $PERSONA_DENOMINACION, $PERSONA_TIPO, $MONTO_TOTAL, $organismo;
		
		$this->SetFillColor(255,255,255);
		//$this->Image("../../images/logo_institucional_01.jpg",$this->MARGEN_LEFT+2,$this->MARGEN_TOP,45);
		//$this->Image("../../images/cintillo_actual.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,180);
		//$this->Image("../../images/plantilla.jpg",0,0,215);
		$this->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,215);

		//$this->SetFont('helvetica','',8);
		//$this->Cell($this->ANCHO,10,utf8_decode('Página: '.$this->PageNo().' de {nb}'),'',1,'R');

		
		$this->Ln(13);
		$this->SetFont('helvetica','B',18);
		$this->Cell(100,18,utf8_decode("ORDEN DE PAGO"),'',0,'C',0);

		$this->SetX($this->lMargin+100);
		$this->SetFont('helvetica','B',12);
		$this->Cell(20,6,utf8_decode("No.:"),'',0,'L',0);
		$this->Cell(50,6,utf8_decode($COMPROBANTE[0]["correlativo"]),'',1,'C',0);

		$this->SetX($this->lMargin+100);
		$this->Cell(20,6,utf8_decode("Fecha:"),'',0,'L',0);
		$this->Cell(50,6,utf8_decode($COMPROBANTE[0]["fecha"]),'',1,'C',0);

		$this->SetX($this->lMargin+100);
		$this->Cell(20,6,utf8_decode("Monto:"),'',0,'L',0);
		$this->Cell(50,6,utf8_decode(number_format($MONTO_TOTAL,2,",",".")),'',1,'C',0);

		$this->Ln(3);
		
		
		if($PERSONA_TIPO=="N" or $PERSONA_TIPO=="J"){
				$this->SetFont('helvetica','B',9);
				$this->Cell(27,4,utf8_decode($PERSONA_TIPO=="N"?"CÉDULA":"RIF"),'',0,'L');
				$this->Cell(2,4,utf8_decode(':'),'',0,'C');
				$this->SetFont('helvetica','',9);
				$this->Cell(150,4,utf8_decode($PERSONA_ID),'',1,'L');
				
				$this->SetFont('helvetica','B',9);
				$this->Cell(27,4,utf8_decode($PERSONA_TIPO=="N"?"BENEFICIARIO":"PROVEEDOR"),'',0,'L');
				$this->Cell(2,4,utf8_decode(':'),'',0,'C');
				$this->SetFont('helvetica','',9);
				$this->Cell(150,4,utf8_decode($PERSONA_DENOMINACION),'',1,'L');
		}
		
		$this->SetFont('helvetica','B',9);
		$this->Cell(27,4,utf8_decode('CONCEPTO'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->MultiCell(180-27,4,utf8_decode($COMPROBANTE[0]['concepto']),'','L',1);
		
		$this->SetFont('helvetica','B',9);
		$this->Cell(27,4,utf8_decode('MONTO'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->MultiCell(180-27,4,utf8_decode(strtoupper(letra_numero($MONTO_TOTAL,true))),'','L',1);

		}
	}


$pdf=new PDF_P("P","mm","letter");




$pdf->Medidas(17.5,10,180);

$pdf->SetAutoPageBreak(true,10);

$t_codigo_c=15;
$t_base_c=35;
$t_monto_c=25;
$t_denom_c=180-($t_codigo_c+$t_base_c+$t_monto_c);

$t_codigo_r=15;
$t_base_r=1;
$t_monto_r=25;
$t_denom_contable_r=80;
$t_denom_r=180-($t_codigo_r+$t_base_r+$t_monto_r+$t_denom_contable_r);

$t_acc_pro_mp=30;
$t_cuenta_mp=20;
$t_operacion_mp=0;
$t_monto_mp=20;
$t_denom_mp=180-($t_acc_pro_mp+$t_cuenta_mp+$t_operacion_mp+$t_monto_mp);


$t_cuenta_mc=30;
$t_debe_mc=20;
$t_haber_mc=20;
$t_denom_mc=180-($t_cuenta_mc+$t_debe_mc+$t_haber_mc);

$MAX_Y=210;

for($i=0;$i<count($IDComprobante);$i++){
		//busco inf del comprobante
		$_i=$i;
		$_id=$IDComprobante[$i];
	
		$COMPROBANTE=$db->Execute("SELECT
																		id,
																		tipo,														
																		lpad(text(correlativo),10,'0') as correlativo,
																		to_char(fecha,'DD/MM/YYYY') as fecha,
																		concepto,
																		id_persona
															FROM modulo_base.comprobante WHERE id=$_id");
		if(count($COMPROBANTE)==0)
			continue;
		
		$PERSONA=$db->Execute("SELECT														
																	(case when identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
																	replace(P.denominacion,';',' ') as denominacion,
																	P.tipo
															FROM modulo_base.persona as P WHERE P.id='".$COMPROBANTE[0]['id_persona']."'");
		$PERSONA_ID="";
		$PERSONA_DENOMINACION="";
		$PERSONA_TIPO="";
		
		if(isset($PERSONA[0])){
			$PERSONA_ID=$PERSONA[0][0];
			$PERSONA_DENOMINACION=$PERSONA[0][1];
			$PERSONA_TIPO=$PERSONA[0][2];
		}
		
		
		//comprobantes previos
		$COMPROBANTE_PREVIO=$db->Execute("SELECT
																				C.id,
																				C.tipo,														
																				lpad(text(C.correlativo),10,'0') as correlativo,
																				to_char(C.fecha,'DD/MM/YYYY') as fecha
																		 FROM
																				modulo_base.comprobante_previo as CP,
																				modulo_base.comprobante as C
																		WHERE
																				C.id=CP.id_comprobante_previo AND
																				CP.id_comprobante='$_id'");
		
		if(count($COMPROBANTE_PREVIO)==0){
				$COMPROBANTE_PREVIO=$COMPROBANTE;
		}
		
		
		
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
		
		$RETENCION=$db->Execute("SELECT
                                                      R.id as id_retencion,
                                                      lpad(text(R.id),3,'0') as correlativo,
                                                      R.denominacion as retencion,
                                                      R.formula,
                                                      R.id_cuenta_contable,
                                                      CTR.monto,
                                                      _formatear_cuenta_contable(R.id_cuenta_contable) as cuenta_contable,
                                                      CC.denominacion as denominacion_contable
                                                    FROM
                                                      modulo_base.comprobante_tiene_retencion AS CTR,
                                                      modulo_base.retencion as R,
                                                      modulo_base.cuenta_contable as CC
                                                    WHERE
                                                      CTR.id_comprobante='$_id' AND
                                                      CTR.id_retencion=R.id AND
                                                      R.id_cuenta_contable=CC.id_cuenta_contable");
		
		


		$MP=$db->Execute("SELECT
												_formatear_estructura_presupuestaria(DP.id_accion_subespecifica) as estructura_presupuestaria,
												_formatear_cuenta_presupuestaria(DP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
												DP.*,	
												CP.denominacion as denominacion_presupuestaria
											FROM
												modulo_base.detalle_presupuestario AS DP,
												modulo_base.cuenta_presupuestaria AS CP
											WHERE
												DP.id_comprobante=$_id AND
												DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria
											ORDER BY
												estructura_presupuestaria, DP.id_cuenta_presupuestaria");
		
		$MC=$db->Execute("SELECT
				DC.id_cuenta_contable,
				_formatear_cuenta_contable(DC.id_cuenta_contable) as cuenta_contable,
				CC.denominacion as denominacion_contable,
				DC.operacion,
				SUM(DC.monto) as monto
			FROM
				modulo_base.detalle_contable AS DC,
				modulo_base.cuenta_contable AS CC
			WHERE
				DC.id_comprobante=$_id AND
				DC.id_cuenta_contable=CC.id_cuenta_contable
			GROUP BY
				DC.id_cuenta_contable,
				CC.denominacion,
				DC.operacion
			ORDER BY
				operacion, id_cuenta_contable");
		
		//calcular el monto de la orden
		//1ero sumar el total por el haber
		$TOTAL_H=0;
		$TOTAL_R=0;
		if(count($MC)>0 and $MC){
				for($j=0;$j<count($MC) and $MC;$j++)
						if($MC[$j]['operacion']=="H")
								$TOTAL_H+=$MC[$j]['monto'];
		}
		
		if(count($RETENCION)>0 and $RETENCION){
				for($j=0;$j<count($RETENCION) and $RETENCION;$j++){
						//$MONTO=1;
						//$formula=str_replace("MONTO","\$MONTO",$RETENCION[$j]["formula"]);
						//eval("\$BASE_CALCULO=(".$RETENCION[$j]['monto']."/($formula));");
						$TOTAL_R+=$RETENCION[$j]['monto'];
				}
		}
		
		$MONTO_TOTAL=$TOTAL_H-$TOTAL_R;
		
		$pdf->AddPage();
	
		if(count($COMPROBANTE_PREVIO)>0 and $COMPROBANTE_PREVIO){
				$pdf->Ln(3);
				
				$t_codigo_cp=180/6;
				$t_fecha_cp=180/6;
				$t_monto_cp=180/6;
				$t_cargo_cp=180/6;
				$t_retencion_cp=180/6;
				$t_total_cp=180/6;
				
				
				
				$pdf->SetFillColor(216,216,216);
				
				
				$pdf->SetFont('helvetica','B',9);
				$pdf->Cell($t_codigo_cp,4,utf8_decode('DOCUMENTO'),'LTB',0,'C',1);
				$pdf->Cell($t_fecha_cp,4,utf8_decode('FECHA'),'TB',0,'C',1);
				$pdf->Cell($t_monto_cp,4,utf8_decode('MONTO'),'TB',0,'C',1);
				$pdf->Cell($t_cargo_cp,4,utf8_decode('CARGO'),'TB',0,'C',1);
				$pdf->Cell($t_retencion_cp,4,utf8_decode('RETENCIÓN'),'TB',0,'C',1);
				$pdf->Cell($t_total_cp,4,utf8_decode('TOTAL'),'RTB',1,'C',1);
				
				$y_inicial=$pdf->GetY();
				$x_inicial=$pdf->GetX();
				
				$pdf->SetFillColor(255,255,255);
				for($j=0;$j<count($COMPROBANTE_PREVIO) and $COMPROBANTE_PREVIO;$j++){
						$COMPROBANTE_PREVIO_MP_NOCARGO=$db->Execute("SELECT sum(DP.monto) as monto
																														FROM
																															modulo_base.detalle_presupuestario AS DP,
																															modulo_base.cuenta_presupuestaria AS CP
																														WHERE
																															DP.id_comprobante=".$COMPROBANTE_PREVIO[$j]["id"]." AND
																															DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
																															DP.id_cuenta_presupuestaria not in
																																(
																																select CG.id_cuenta_presupuestaria
																																from modulo_base.cargo as CG, modulo_base.comprobante_tiene_cargo as CTC
																																where CG.id=CTC.id_cargo and CTC.id_comprobante=DP.id_comprobante
																																)
																														");
						$monto=0;
						if(isset($COMPROBANTE_PREVIO_MP_NOCARGO[0][0]))
								$monto=$COMPROBANTE_PREVIO_MP_NOCARGO[0][0];
								
						$COMPROBANTE_PREVIO_MP_CARGO=$db->Execute("SELECT sum(DP.monto) as monto
																														FROM
																															modulo_base.detalle_presupuestario AS DP,
																															modulo_base.cuenta_presupuestaria AS CP
																														WHERE
																															DP.id_comprobante=".$COMPROBANTE_PREVIO[$j]["id"]." AND
																															DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
																															DP.id_cuenta_presupuestaria in
																																(
																																select CG.id_cuenta_presupuestaria
																																from modulo_base.cargo as CG, modulo_base.comprobante_tiene_cargo as CTC
																																where CG.id=CTC.id_cargo and CTC.id_comprobante=DP.id_comprobante
																																)
																														");
						
						$cargo=0;
						if(isset($COMPROBANTE_PREVIO_MP_CARGO[0][0]))
								$cargo=$COMPROBANTE_PREVIO_MP_CARGO[0][0];
						
						$pdf->SetFont('helvetica','',8);
						$pdf->Cell($t_codigo_cp,4,utf8_decode($COMPROBANTE_PREVIO[$j]["tipo"].$COMPROBANTE_PREVIO[$j]["correlativo"]),'L',0,'C');
						$pdf->Cell($t_fecha_cp,4,utf8_decode($COMPROBANTE_PREVIO[$j]["fecha"]),'',0,'C');
						$pdf->Cell($t_monto_cp,4,utf8_decode(number_format($monto,2,",",".")),'',0,'R');
						$pdf->Cell($t_cargo_cp,4,utf8_decode(number_format($cargo,2,",",".")),'',1,'R');
				}
				$y_final=$pdf->GetY();
				
				
				//colocar el total de retenciones
				$pdf->SetXY($x_inicial+$t_codigo_cp+$t_fecha_cp+$t_monto_cp+$t_cargo_cp,$y_inicial);
				$pdf->Cell($t_cargo_cp,4*count($COMPROBANTE_PREVIO),utf8_decode(number_format($TOTAL_R,2,",",".")),'',0,'R');
				$pdf->SetFont('helvetica','B',10);
				$pdf->Cell($t_cargo_cp,4*count($COMPROBANTE_PREVIO),utf8_decode(number_format($MONTO_TOTAL,2,",",".")),'R',1,'R');
				
				$pdf->Cell(180,1,'','T',1,'C');
				
		}
		
	
		
		if(count($CARGO)>0 and $CARGO){
				$pdf->Ln(3);
			
				$pdf->SetFillColor(216,216,216);
				$pdf->SetFont('helvetica','B',10);
				$pdf->Cell($pdf->ANCHO,6,utf8_decode('CARGOS'),'LRT',1,'C',1);
				
				$pdf->SetFont('helvetica','B',9);
				$pdf->Cell($t_codigo_c,4,utf8_decode('CODIGO'),'LB',0,'C',1);
				$pdf->Cell($t_denom_c,4,utf8_decode('DENOMINACIÓN'),'B',0,'C',1);
				//$pdf->Cell($t_base_c,4,utf8_decode('BASE DE CALCULO'),'B',0,'C',1);
				$pdf->Cell($t_base_c,4,utf8_decode(''),'B',0,'C',1);
				$pdf->Cell($t_monto_c,4,utf8_decode('MONTO'),'RB',1,'C',1);
				
				$SUMA_CARGO=0;
				
				$y_inicial=$pdf->GetY();
				$x_inicial=$pdf->GetX();
				
				$pdf->SetFillColor(255,255,255);
				$pdf->SetFont('helvetica','',8);
				for($j=0;$j<count($CARGO) and $CARGO;$j++){
					
					$pdf->Cell($t_codigo_c,4,utf8_decode($CARGO[$j]['correlativo']),'',0,'C');
					$y=$pdf->GetY();
					$x=$pdf->GetX();
					$pdf->Cell($t_denom_c,4,'','',0,'C');
					
					//$MONTO=1;
					//$formula=str_replace("MONTO","\$MONTO",$CARGO[$j]["formula"]);
					
					//eval("\$BASE_CALCULO=(".$CARGO[$j]['monto']."/($formula));");
					
					//$pdf->Cell($t_base_c,4,utf8_decode(number_format($BASE_CALCULO,2,",",".")),'',0,'R');
					$pdf->Cell($t_base_c,4,utf8_decode(""),'',0,'R');
					$pdf->Cell($t_monto_c,4,utf8_decode(number_format($CARGO[$j]['monto'],2,",",".")),'',1,'R');
					$pdf->SetXY($x,$y);
					$pdf->MultiCell($t_denom_c,4,utf8_decode($CARGO[$j]['cargo']."."),'','L',0);
					$SUMA_CARGO+=$CARGO[$j]['monto'];
				}
				$y_final=$pdf->GetY();
				
				$pdf->Line($x_inicial,$y_inicial,$x_inicial,$y_final);
				$pdf->Line($x_inicial+180,$y_inicial,$x_inicial+180,$y_final);
				
				
				$pdf->Cell(180,1,'','T',1,'C');
				$pdf->Cell($t_codigo_c+$t_denom_c+$t_base_c,4,utf8_decode(""),'',0,'C');
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell($t_monto_c,4,utf8_decode(number_format($SUMA_CARGO,2,",",".")),'',1,'R');
		}
	
	//print_r($RETENCION);
	
		
		if(count($RETENCION)>0 and $RETENCION){
				$pdf->Ln(3);
			
				$pdf->SetFillColor(216,216,216);
				$pdf->SetFont('helvetica','B',10);
				$pdf->Cell($pdf->ANCHO,6,utf8_decode('RETENCIONES'),'LRT',1,'C',1);
				
				$pdf->SetFont('helvetica','B',9);
				$pdf->Cell($t_codigo_r,4,utf8_decode('CODIGO'),'LB',0,'C',1);
				$pdf->Cell($t_denom_r,4,utf8_decode('DENOMINACIÓN'),'B',0,'C',1);
				$pdf->Cell($t_denom_contable_r,4,utf8_decode('CUENTA CONTABLE'),'B',0,'C',1);
				//$pdf->Cell($t_base_r,4,utf8_decode('BASE DE CALCULO'),'B',0,'C',1);
				$pdf->Cell($t_base_r,4,utf8_decode(''),'B',0,'C',1);
				$pdf->Cell($t_monto_r,4,utf8_decode('MONTO'),'RB',1,'C',1);
				
				$SUMA_RETENCION=0;
				
				$y_inicial=$pdf->GetY();
				$x_inicial=$pdf->GetX();
				
				$pdf->SetFillColor(255,255,255);
				for($j=0;$j<count($RETENCION) and $RETENCION;$j++){
					$pdf->SetFont('helvetica','',8);
					$pdf->Cell($t_codigo_r,4,utf8_decode($RETENCION[$j]['correlativo']),'',0,'C');
					$y=$pdf->GetY();
					$x=$pdf->GetX();
					$pdf->Cell($t_denom_r,4,'','',0,'C');
					$pdf->Cell($t_denom_contable_r,4,'','',0,'C');
					
					//$MONTO=1;
					//$formula=str_replace("MONTO","\$MONTO",$RETENCION[$j]["formula"]);
					//eval("\$BASE_CALCULO=(".$RETENCION[$j]['monto']."/($formula));");
					
					//$pdf->Cell($t_base_r,4,utf8_decode(number_format($BASE_CALCULO,2,",",".")),'',0,'R');
					$pdf->Cell($t_base_r,4,utf8_decode(""),'',0,'R');
					$pdf->Cell($t_monto_r,4,utf8_decode(number_format($RETENCION[$j]['monto'],2,",",".")),'',1,'R');
					$pdf->SetXY($x,$y);
					$pdf->MultiCell($t_denom_r,4,utf8_decode($RETENCION[$j]['retencion']."."),'','L',0);
					$pdf->SetXY($x+$t_denom_r,$y);
					$pdf->MultiCell($t_denom_contable_r,4,utf8_decode($RETENCION[$j]['cuenta_contable']." ".$RETENCION[$j]['denominacion_contable']."."),'','L',0);
					$SUMA_RETENCION+=$RETENCION[$j]['monto'];
				}
				$y_final=$pdf->GetY();
				
				$pdf->Line($x_inicial,$y_inicial,$x_inicial,$y_final);
				$pdf->Line($x_inicial+180,$y_inicial,$x_inicial+180,$y_final);
				
				
				$pdf->Cell(180,1,'','T',1,'C');
				$pdf->Cell($t_codigo_r+$t_denom_r+$t_denom_contable_r+$t_base_r,4,utf8_decode(""),'',0,'C');
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell($t_monto_r,4,utf8_decode(number_format($SUMA_RETENCION,2,",",".")),'',1,'R');
		}
//print_r($SUMA_RETENCION);
	//buscar los movimientos P
	

	if(count($MP)>0 and $MP){
		$pdf->Ln(3);
		$sw=false;
		
		CabeceraDP();

		$SUMA_PRESUP=0;
		
		$y_inicial=$pdf->GetY();
		$x_inicial=$pdf->GetX();
		
		$pdf->SetFillColor(255,255,255);
		for($j=0;$j<count($MP) and $MP;$j++){
				$y_final=$pdf->GetY();
				if($y_final>=$MAX_Y){
						$pdf->Line($x_inicial,$y_inicial,$x_inicial,$y_final);
						$pdf->Line($x_inicial+180,$y_inicial,$x_inicial+180,$y_final);
						$pdf->Cell(180,1,'','T',0,'');
						$pdf->AddPage();
						CabeceraDP();
						$y_inicial=$pdf->GetY();
						$x_inicial=$pdf->GetX();
				}
				
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_acc_pro_mp,4,utf8_decode($MP[$j]['estructura_presupuestaria']),'',0,'C');
				$pdf->Cell($t_cuenta_mp,4,utf8_decode($MP[$j]['cuenta_presupuestaria']),'',0,'C');
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->Cell($t_denom_mp,4,'','',0,'C');
				$pdf->Cell($t_monto_mp,4,utf8_decode(number_format($MP[$j]['monto'],2,",",".")),'',1,'R');
				$pdf->SetXY($x,$y);
				$pdf->MultiCell($t_denom_mp,4,utf8_decode($MP[$j]['denominacion_presupuestaria']."."),'','L',0);
				$SUMA_PRESUP+=$MP[$j]['monto'];
		}
		$y_final=$pdf->GetY();
		
		$pdf->Line($x_inicial,$y_inicial,$x_inicial,$y_final);
		$pdf->Line($x_inicial+180,$y_inicial,$x_inicial+180,$y_final);
		
		
		$pdf->Cell(180,1,'','T',1,'C');
		$pdf->Cell($t_acc_pro_mp+$t_cuenta_mp+$t_denom_mp,4,utf8_decode(""),'',0,'C');
		$pdf->SetFont('helvetica','B',8);
		$pdf->Cell($t_monto_mp,4,utf8_decode(number_format($SUMA_PRESUP,2,",",".")),'',1,'R');
}



	//buscar los MC
	
	

	$SUMA_DEBE=0;$SUMA_HABER=0;

	if(count($MC)>0 and $MC){
		$pdf->Ln(3);
		$y_final=$pdf->GetY();
		if($y_final>=$MAX_Y)
				$pdf->AddPage();
		CabeceraDC();
		$pdf->SetFillColor(255,255,255);
		$y_inicial=$pdf->GetY();
		$x_inicial=$pdf->GetX();
		for($j=0;$j<count($MC) and $MC;$j++){
				$y_final=$pdf->GetY();
				if($y_final>=$MAX_Y){
						$pdf->Line($x_inicial,$y_inicial,$x_inicial,$y_final);
						$pdf->Line($x_inicial+180,$y_inicial,$x_inicial+180,$y_final);
						$pdf->Cell(180,1,'','T',0,'');
						$pdf->AddPage();
						CabeceraDC();
						$y_inicial=$pdf->GetY();
						$x_inicial=$pdf->GetX();
				}
				
				
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_cuenta_mc,4,utf8_decode($MC[$j]['cuenta_contable']),'',0,'C');
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->Cell($t_denom_mc,4,'','',0,'C');
	
				if($MC[$j]['operacion']=="D"){
					$pdf->Cell($t_debe_mc,4,utf8_decode(number_format($MC[$j]['monto'],2,",",".")),'',0,'R');
					$pdf->Cell($t_haber_mc,4,utf8_decode(''),'',1,'R');
					$SUMA_DEBE+=$MC[$j]['monto'];
					}
				else{
					$pdf->Cell($t_debe_mc,4,utf8_decode(''),'',0,'R');
					$pdf->Cell($t_haber_mc,4,utf8_decode(number_format($MC[$j]['monto'],2,",",".")),'',1,'R');
					$SUMA_HABER+=$MC[$j]['monto'];
					}
				$pdf->SetXY($x,$y);
				$pdf->MultiCell($t_denom_mc,4,utf8_decode($MC[$j]['denominacion_contable']."."),'','L',0);

		}
		$y_final=$pdf->GetY();
		$pdf->Line($x_inicial,$y_inicial,$x_inicial,$y_final);
		$pdf->Line($x_inicial+180,$y_inicial,$x_inicial+180,$y_final);
		
		
		$pdf->Cell(180,1,utf8_decode(''),'T',1,'C');

		$pdf->Cell($t_cuenta_mc+$t_denom_mc,1,utf8_decode(''),'',0,'C');
		$pdf->SetFont('helvetica','B',8);
		$pdf->Cell($t_debe_mc,4,utf8_decode(number_format($SUMA_DEBE,2,",",".")),'',0,'R');
		$pdf->Cell($t_haber_mc,4,utf8_decode(number_format($SUMA_HABER,2,",",".")),'',1,'R');
		}
		
		/*
		//zona de firmas
		$tam_ancho=180;
		$tam_firma=$tam_ancho/3;
		$pdf->SetY($MAX_Y);
	
	
		$pdf->Cell($tam_ancho,5,utf8_decode("OBSERVACIONES:"),'LRT',1,'L',1);
		$pdf->SetFont('helvetica','',8);
		$pdf->MultiCell($tam_ancho,4,"",'LRB','L',1);
		$pdf->SetFont('helvetica','B',7);
	
		$pdf->Cell($tam_firma,4,utf8_decode("PRESUPUESTO"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("ADMINISTRACIÓN"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("PRESIDENCIA"),'LRTB',1,'C',1);
	
		$pdf->Cell($tam_firma,4,utf8_decode("FECHA:          /        /"),'LRTB',0,'L',1);
		$pdf->Cell($tam_firma,4,utf8_decode("FECHA:          /        /"),'LRTB',0,'L',1);
		$pdf->Cell($tam_firma,4,utf8_decode("FECHA:          /        /"),'LRTB',1,'L',1);
	
		$pdf->Cell($tam_firma,20,utf8_decode(""),'LRT',0,'C',1);
		$pdf->Cell($tam_firma,20,utf8_decode(""),'LRT',0,'C',1);
		$pdf->Cell($tam_firma,20,utf8_decode(""),'LRT',1,'C',1);
	
		$pdf->Cell($tam_firma,4,"FIRMA",'LRB',0,'C',1);
		$pdf->Cell($tam_firma,4,"FIRMA",'LRB',0,'C',1);
		$pdf->Cell($tam_firma,4,"FIRMA",'LRB',1,'C',1);*/
		//zona de firmas
		$tam_ancho=180;
		$tam_firma=$tam_ancho/4;
		$pdf->SetY($MAX_Y);
	
	
		$pdf->Cell($tam_ancho,5,utf8_decode("OBSERVACIONES:"),'LRT',1,'L',1);
		$pdf->SetFont('helvetica','',8);
		$pdf->MultiCell($tam_ancho,4,"",'LRB','L',1);
		$pdf->SetFont('helvetica','B',7);
		
		$pdf->Cell($tam_firma,4,utf8_decode("ELABORADO POR:"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("REVISADO POR:"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("VERIFICADO POR:"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("AUTORIZADO POR:"),'LRTB',1,'C',1);
		
		$pdf->Cell($tam_firma,24,utf8_decode(""),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,24,utf8_decode(""),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,24,utf8_decode(""),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,24,utf8_decode(""),'LRTB',1,'C',1);
		
		$pdf->Cell($tam_firma,4,utf8_decode("ADMINISTRACIÓN"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("PRESUPUESTO"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("ADMINISTRACIÓN"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("PRESIDENCIA"),'LRTB',1,'C',1);
}


$pdf->AliasNbPages();
$pdf->Output("comprobante_".$COMPROBANTE[0]["tipo"].$COMPROBANTE[0]["correlativo"].".pdf","I");
?>