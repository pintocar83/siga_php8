<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");

include_once("../library/fpdf/1.84/fpdf.php");


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
		
		$pdf->SetFont('helvetica','B',8);
		$pdf->Cell($t_acc_pro_mp,4,utf8_decode('ACC/PRO'),'LTB',0,'C',1);
		$pdf->Cell($t_cuenta_mp,4,utf8_decode('CUENTA'),'TB',0,'C',1);
		$pdf->Cell($t_denom_mp,4,utf8_decode('DENOMINACIÓN'),'TB',0,'C',1);
		$pdf->Cell($t_monto_mp,4,utf8_decode('MONTO'),'RTB',1,'C',1);
		$pdf->SetFillColor(255,255,255);
}

function CabeceraITEM(){
		global $pdf, $t_n_i, $t_codigo_i, $t_denom_i, $t_cantidad_i, $t_unidad_i, $t_precio_i, $t_total_i;
		$pdf->SetFillColor(216,216,216);
		$pdf->SetFont('helvetica','B',8);
		$pdf->Cell($t_n_i,4,utf8_decode('Nº'),'LTB',0,'C',1);
		$pdf->Cell($t_codigo_i,4,utf8_decode('CÓDIGO'),'TB',0,'C',1);
		$pdf->Cell($t_denom_i,4,utf8_decode('DENOMINACIÓN'),'TB',0,'C',1);
		$pdf->Cell($t_cantidad_i,4,utf8_decode('CANTIDAD'),'TB',0,'C',1);
		$pdf->Cell($t_unidad_i,4,utf8_decode('PRESENTACIÓN'),'TB',0,'C',1);
		$pdf->Cell($t_precio_i,4,utf8_decode('PRECIO'),'TB',0,'C',1);
		$pdf->Cell($t_total_i,4,utf8_decode('TOTAL'),'RTB',1,'C',1);
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
		//$this->Image("../../images/cintillo_actual.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,180);
		//$this->Image("../../images/plantilla.jpg",0,0,215);
		$this->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,215);
		
		$this->Ln(16);
		$this->SetFont('helvetica','B',18);
		
		$tipo="";
		if($COMPROBANTE[0]["tipo"]=="OC") $tipo="ORDEN DE COMPRA";
		else if($COMPROBANTE[0]["tipo"]=="OS") $tipo="ORDEN DE SERVICIO";
		
		$this->Cell(100,12,utf8_decode($tipo),'',0,'C',0);

		$this->SetX($this->lMargin+100);
		$this->SetFont('helvetica','B',12);
		$this->Cell(20,6,utf8_decode("No.:"),'',0,'L',0);
		$this->Cell(50,6,utf8_decode($COMPROBANTE[0]["correlativo"]),'',1,'C',0);

		$this->SetX($this->lMargin+100);
		$this->Cell(20,6,utf8_decode("Fecha:"),'',0,'L',0);
		$this->Cell(50,6,utf8_decode($COMPROBANTE[0]["fecha"]),'',1,'C',0);



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
		

		}
	}


$pdf=new PDF_P("P","mm","letter");




$pdf->Medidas(17.5,10,180);

$pdf->SetAutoPageBreak(true,10);


$t_n_i=5;
$t_codigo_i=15;
$t_cantidad_i=15;
$t_unidad_i=30;
$t_precio_i=20;
$t_total_i=20;
$t_denom_i=180-($t_n_i+$t_codigo_i+$t_cantidad_i+$t_unidad_i+$t_precio_i+$t_total_i);

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
		
		
		
		
		
		
		
		$pdf->AddPage();
	
	
		if(count($ITEM)>0 and $ITEM){
				$pdf->Ln(3);
				$SUBTOTAL=0;
				$SUBTOTAL_IVA=0;
				$EXENTO=0;
				$IMPONIBLE_IVA=0;
				CabeceraITEM();
				$pdf->SetFillColor(255,255,255);
				$pdf->SetFont('helvetica','',8);
				//print_r($ITEM);exit;
				for($j=0;$j<count($ITEM) and $ITEM;$j++){
						$total_item=$ITEM[$j]["cantidad"]*$ITEM[$j]["costo"];
						$SUBTOTAL+=$total_item;
						if($ITEM[$j]["aplica_iva"]=="t")
								$SUBTOTAL_IVA+=$total_item;
						else
							$EXENTO+=$total_item;

						$pdf->Cell($t_n_i,4,utf8_decode($j+1),'LTB',0,'C',1);
						$pdf->Cell($t_codigo_i,4,utf8_decode($ITEM[$j]["codigo"]),'TB',0,'C',1);
						$pdf->Cell($t_denom_i,4,utf8_decode($ITEM[$j]["item"]),'TB',0,'L',1);
						$pdf->Cell($t_cantidad_i,4,utf8_decode(number_format($ITEM[$j]["cantidad"],2,",",".")),'TB',0,'C',1);
						$pdf->Cell($t_unidad_i,4,utf8_decode($ITEM[$j]["medida"]),'TB',0,'C',1);						
						$pdf->Cell($t_precio_i,4,utf8_decode(number_format($ITEM[$j]["costo"],2,",",".")),'TB',0,'R',1);
						$pdf->Cell($t_total_i,4,utf8_decode(number_format($total_item,2,",",".")),'RTB',1,'R',1);
				}
				
				$t_post_i=160;
				$t_post_total=180-$t_post_i;
				
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
				
				$pdf->Ln(1);
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell($t_post_i,4,utf8_decode("SUB-TOTAL:"),'',0,'R',0);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_post_total,4,utf8_decode(number_format($SUBTOTAL,2,",",".")),'',1,'R',1);
				
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_post_i-20,4,utf8_decode(number_format($descuento_p,2,",",".")."%"),'',0,'R',0);
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell(20,4,utf8_decode("DESCUENTO:"),'',0,'R',0);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_post_total,4,utf8_decode(number_format($descuento_m,2,",",".")),'',1,'R',1);
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell($t_post_i,4,utf8_decode("EXENTO:"),'',0,'R',0);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_post_total,4,utf8_decode(number_format($EXENTO,2,",",".")),'',1,'R',1);
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell($t_post_i,4,utf8_decode("BASE IMPONIBLE:"),'',0,'R',0);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_post_total,4,utf8_decode(number_format($BASE_IMPONIBLE_IVA,2,",",".")),'',1,'R',1);
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell($t_post_i,4,utf8_decode("IMPUESTOS:"),'',0,'R',0);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_post_total,4,utf8_decode(number_format($TOTAL_CARGO,2,",",".")),'',1,'R',1);
				$pdf->SetFont('helvetica','B',8);
				$pdf->Cell($t_post_i,4,utf8_decode("TOTAL:"),'',0,'R',0);
				$pdf->SetFont('helvetica','',8);
				$pdf->Cell($t_post_total,4,utf8_decode(number_format($TOTAL,2,",",".")),'',1,'R',1);
				
		}
		$pdf->Ln(5);

		
		

		//buscar los movimientos Presupuestarios
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
						$pdf->Cell($t_acc_pro_mp,4,utf8_decode($MP[$j]['estructura_presupuestaria']),'',0,'C',1);
						$pdf->Cell($t_cuenta_mp,4,utf8_decode($MP[$j]['cuenta_presupuestaria']),'',0,'C',1);
						$y=$pdf->GetY();
						$x=$pdf->GetX();
						$pdf->Cell($t_denom_mp,4,'','',0,'C');
						$pdf->Cell($t_monto_mp,4,utf8_decode(number_format($MP[$j]['monto'],2,",",".")),'',1,'R',1);
						$pdf->SetXY($x,$y);
						$pdf->MultiCell($t_denom_mp,4,utf8_decode($MP[$j]['denominacion_presupuestaria']."."),'','L',1);
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