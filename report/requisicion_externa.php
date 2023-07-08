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
		$pdf->Cell($t_unidad_i,4,utf8_decode('PRESENTACIÓN'),'RTB',1,'C',1);
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
		$this->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,215);

		
		$this->Ln(16);
		$this->SetFont('helvetica','B',18);
		
		$tipo="";
		if($COMPROBANTE[0]["tipo"]=="OC") $tipo="REQUISICIÓN COMPRA";
		else if($COMPROBANTE[0]["tipo"]=="OS") $tipo="REQUISICIÓN SERVICIO";
		
		$this->Cell(100,12,utf8_decode("    ".$tipo),'',0,'C',0);

		$this->SetX($this->lMargin+100);
		$this->SetFont('helvetica','B',12);
		$this->Cell(20,6,utf8_decode("No.:"),'',0,'L',0);
		$this->Cell(50,6,utf8_decode($COMPROBANTE[0]["correlativo"]),'',1,'C',0);

		$this->SetX($this->lMargin+100);
		$this->Cell(20,6,utf8_decode("Fecha:"),'',0,'L',0);
		$this->Cell(50,6,utf8_decode($COMPROBANTE[0]["fecha"]),'',1,'C',0);



		$this->Ln(3);
		
		$this->SetFont('helvetica','B',9);
		$this->Cell(27,4,utf8_decode('ACC/PRO'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->MultiCell(180-27,4,utf8_decode($COMPROBANTE[0]['estructura_presupuestaria']),'','L',1);
		
		$this->Cell(27+2,4,utf8_decode(''),'',0,'L');
		
		$denominacion=$COMPROBANTE[0]['denominacion_centralizada'].".";
		$database_name=isset(SIGA::$database[SIGA::database()]["name"])?SIGA::$database[SIGA::database()]["name"]:"";
        //CASO ESPECIFICO PARA LA ALCALDIA DE MEJIA (no nreflejar el nombre del proyecto makro)
        if($database_name && preg_grep("/siga_alcaldia_mejia*/i",[$database_name])){
        	$denominacion="";
        }
		if($COMPROBANTE[0]['codigo_especifica']!=="00")
				$denominacion.=" (".$COMPROBANTE[0]['codigo_especifica'].") ".trim($COMPROBANTE[0]['denominacion_especifica'],".").".";
				
		$this->MultiCell(180-27,4,utf8_decode(trim($denominacion)),'','L',1);
		
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
$t_denom_i=180-($t_n_i+$t_codigo_i+$t_cantidad_i+$t_unidad_i);

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
																		RE.id,
																		lpad(text(RE.correlativo),10,'0') as correlativo,
																		to_char(RE.fecha,'DD/MM/YYYY') as fecha,
																		RE.tipo,
																		RE.concepto,
																		_formatear_estructura_presupuestaria(RE.id_accion_subespecifica) as estructura_presupuestaria,
																		RE.id_accion_subespecifica,
																		ASE.id_accion_especifica,
																		AE.id_accion_centralizada,
																		AC.denominacion_centralizada,
																		AE.denominacion_especifica,
																		ASE.denominacion_subespecifica,
																		AE.codigo_especifica
																	FROM
																		modulo_base.requisicion_externa AS RE,
																		modulo_base.accion_subespecifica as ASE,
																		modulo_base.accion_especifica as AE,
																		modulo_base.accion_centralizada as AC
																	WHERE
																		RE.id='$_id' AND
																		RE.id_accion_subespecifica=ASE.id AND
																		ASE.id_accion_especifica=AE.id AND
																		AE.id_accion_centralizada=AC.id");
		
		
		
		if(count($COMPROBANTE)==0)
			continue;
		
		
		
		
		$ITEM=$db->Execute("SELECT
														RETI.id_item,
														I.codigo,
														I.item,
														I.id_item_tipo,
														RETI.cantidad,
														UM.medida
													FROM
														modulo_base.requisicion_externa_tiene_item as RETI,
														modulo_base.item as I,
														modulo_base.unidad_medida as UM
													WHERE
														I.id=RETI.id_item AND
														RETI.id_unidad_medida=UM.id AND
														RETI.id_requisicion_externa='$_id'");
		
		
		
		
		
		
		
		$pdf->AddPage();
	
	
		if(count($ITEM)>0 and $ITEM){
				$pdf->Ln(3);
				$SUBTOTAL=0;
				$SUBTOTAL_IVA=0;
				CabeceraITEM();
				$pdf->SetFillColor(255,255,255);
				$pdf->SetFont('helvetica','',8);
				
				for($j=0;$j<count($ITEM) and $ITEM;$j++){
						$pdf->Cell($t_n_i,4,utf8_decode($j+1),'LTB',0,'C',1);
						$pdf->Cell($t_codigo_i,4,utf8_decode($ITEM[$j]["codigo"]),'TB',0,'C',1);
						$pdf->Cell($t_denom_i,4,utf8_decode($ITEM[$j]["item"]),'TB',0,'L',1);
						$pdf->Cell($t_cantidad_i,4,utf8_decode(number_format($ITEM[$j]["cantidad"],2,",",".")),'TB',0,'C',1);
						$pdf->Cell($t_unidad_i,4,utf8_decode($ITEM[$j]["medida"]),'RTB',1,'C',1);	
				}
				

				
				
				
		}
		$pdf->Ln(5);

		
		

	



	
		
		//zona de firmas
		$tam_ancho=180;
		$tam_firma=$tam_ancho/2;
		$pdf->SetY($MAX_Y);
	
		$pdf->SetFont('helvetica','B',8);
		$pdf->Cell($tam_ancho,5,utf8_decode("OBSERVACIONES:"),'LRT',1,'L',1);
		$pdf->SetFont('helvetica','',8);
		$pdf->MultiCell($tam_ancho,4,"",'LRB','L',1);
		$pdf->SetFont('helvetica','B',7);
		
				
		$pdf->Cell($tam_firma,28,utf8_decode(""),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,28,utf8_decode(""),'LRTB',1,'C',1);
		
		$pdf->Cell($tam_firma,4,utf8_decode("SOLICITADO POR"),'LRTB',0,'C',1);
		$pdf->Cell($tam_firma,4,utf8_decode("ADMINISTRACIÓN"),'LRTB',1,'C',1);
}


$pdf->AliasNbPages();
$pdf->Output("comprobante_".$COMPROBANTE[0]["tipo"].$COMPROBANTE[0]["correlativo"].".pdf","I");
?>