<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");

include_once("../library/fpdf/1.84/fpdf.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");


$IDComprobante=explode(",",SIGA::paramGet("id"));




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
		global $COMPROBANTE, $PERSONA_ID, $PERSONA_DENOMINACION, $PERSONA_TIPO, $CABECERA, $ancho, $organismo;
		
		$this->SetFillColor(255,255,255);
		$this->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,215);

		$this->SetFont('helvetica','',8);
		$this->Ln(10);
		$this->Cell($this->ANCHO,10,utf8_decode('Página: '.$this->PageNo().' de {nb}'),'',1,'R');

		$this->Ln(2);
		$this->SetFont('helvetica','B',12);
		$this->Cell($this->ANCHO,5,utf8_decode('COMPROBANTE PRESUPUESTARIO/CONTABLE'),'',1,'C');

		$this->Ln(5);
		$this->SetFont('helvetica','B',9);
		$this->Cell(27,4,utf8_decode('COMPROBANTE'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->Cell(150,4,utf8_decode($COMPROBANTE[0]['tipo']."-".$COMPROBANTE[0]["correlativo"]),'',1,'L');

		$this->SetFont('helvetica','B',9);
		$this->Cell(27,4,utf8_decode('FECHA'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->Cell(150,4,utf8_decode(formatDate($COMPROBANTE[0]['fecha'])),'',1,'L');
		
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
		$this->Cell(27,4,utf8_decode('DENOMINACIÓN'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->MultiCell($ancho-27,4,utf8_decode($COMPROBANTE[0]['concepto']),'','L',1);
		
		
		global $t_acc_pro_mp,$t_cuenta_mp,$t_denom_mp,$t_operacion_mp,$t_monto_mp;
		if($CABECERA=="MP"){
			$this->Ln(3);
			$this->SetFont('helvetica','B',10);
			$this->Cell($this->ANCHO,5,utf8_decode('DETALLES PRESUPUESTARIOS'),'',1,'C');
			$this->Ln(1);
			$this->SetFillColor(216,216,216);
			$this->SetFont('helvetica','B',8);
			$this->Cell($t_acc_pro_mp,4,utf8_decode('ACC/PRO'),'LRTB',0,'C',1);
			$this->Cell($t_cuenta_mp,4,utf8_decode('CUENTA'),'LRTB',0,'C',1);
			$this->Cell($t_denom_mp,4,utf8_decode('DENOMINACIÓN'),'LRTB',0,'C',1);
			$this->Cell($t_operacion_mp,4,utf8_decode('OPERACIÓN'),'LRTB',0,'C',1);
			$this->Cell($t_monto_mp,4,utf8_decode('MONTO'),'LRTB',1,'C',1);
			$this->Ln(1);
		}
		else if($CABECERA=="MC"){
			$this->CabeceraMC();
		}
		
	}
	
	function CabeceraMC(){
		global $t_cuenta_mc,$t_denom_mc,$t_debe_mc,$t_haber_mc;
		$this->Ln(3);
		$this->SetFont('helvetica','B',10);
		$this->Cell($this->ANCHO,5,utf8_decode('DETALLES CONTABLES'),'',1,'C');

		$this->Ln(1);
		$this->SetFillColor(216,216,216);
		$this->SetFont('helvetica','B',8);
		$this->Cell($t_cuenta_mc,4,utf8_decode('CUENTA'),'LRTB',0,'C',1);
		$this->Cell($t_denom_mc,4,utf8_decode('DENOMINACIÓN'),'LRTB',0,'C',1);
		$this->Cell($t_debe_mc,4,utf8_decode('DEBE'),'LRTB',0,'C',1);
		$this->Cell($t_haber_mc,4,utf8_decode('HABER'),'LRTB',1,'C',1);

		$this->SetFillColor(255,255,255);
		$this->Ln(1);
	}
}


$pdf=new PDF_P("P","mm","letter");
$ancho=180;

$pdf->Medidas(17.5,10,$ancho);
$pdf->SetAutoPageBreak(true,10);

$t_acc_pro_mp=25;
$t_cuenta_mp=20;
$t_operacion_mp=30;
$t_monto_mp=20;
$t_denom_mp=$ancho-($t_acc_pro_mp+$t_cuenta_mp+$t_operacion_mp+$t_monto_mp);


$t_cuenta_mc=25;
$t_debe_mc=20;
$t_haber_mc=20;
$t_denom_mc=$ancho-($t_cuenta_mc+$t_debe_mc+$t_haber_mc);



for($i=0;$i<count($IDComprobante);$i++){
	//busco inf del comprobante
	$_i=$i;
	$_id=$IDComprobante[$i];

	$COMPROBANTE=$db->Execute("SELECT														
																tipo,														
																lpad(text(correlativo),10,'0') as correlativo,
																fecha,
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
	
	

	//buscar los movimientos P
	$sql="SELECT
				_formatear_estructura_presupuestaria(DP.id_accion_subespecifica) as estructura_presupuestaria,
				_formatear_cuenta_presupuestaria(DP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
				DP.*,				
				O.*,
				CP.denominacion as denominacion_presupuestaria
			FROM
				modulo_base.detalle_presupuestario AS DP,
				modulo_base.cuenta_presupuestaria AS CP,
				modulo_base.detalle_presupuestario_operacion AS O
			WHERE
				DP.id_comprobante=$_id AND
				DP.operacion=O.operacion AND
				DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria
			ORDER BY
				estructura_presupuestaria, DP.id_cuenta_presupuestaria";

	$MP=$db->Execute($sql);

	//buscar los MC
	$sql="SELECT
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
				operacion, id_cuenta_contable";

	$MC=$db->Execute($sql);
	
	$CABECERA="";
	if(count($MP)>0 and $MP)
		$CABECERA="MP";
	
	$pdf->AddPage();
	if(count($MP)>0 and $MP){
		$SUMA_PRESUP=0;
		$total_operacion["APERTURA"]=0;
		$total_operacion["COMPROMETIDO"]=0;
		$total_operacion["CAUSADO"]=0;
		$total_operacion["PAGADO"]=0;
		//$total_operacion["ASIGNACION"]=0;
		$total_operacion["AUMENTO"]=0;
		$total_operacion["DISMINUCION"]=0;
		//$total_operacion["REINTEGRO"]=0;
		$total_operacion["SINAFECTACION"]=0;
		
		
		$pdf->SetFillColor(255,255,255);
		
		for($j=0;$j<count($MP) and $MP;$j++){
			$pdf->SetFont('helvetica','',7.5);
			$pdf->Cell($t_acc_pro_mp,3,utf8_decode($MP[$j]['estructura_presupuestaria']),'',0,'C');
			$pdf->Cell($t_cuenta_mp,3,utf8_decode($MP[$j]['cuenta_presupuestaria']),'',0,'C');
			$y=$pdf->GetY();
			$x1=$pdf->GetX();
			$pdf->Cell($t_denom_mp,3,'','',0,'C');
			$x2=$pdf->GetX();
			$pdf->Cell($t_operacion_mp,3,'','',0,'C');
			$pdf->Cell($t_monto_mp,3,utf8_decode(number_format($MP[$j]['monto'],2,",",".")),'',1,'R');
			$SUMA_PRESUP+=$MP[$j]['monto'];

			$pdf->SetXY($x1,$y);
			$pdf->MultiCell($t_denom_mp,3,utf8_decode($MP[$j]['denominacion_presupuestaria']."."),'','L',0);
			$yfin_1=$pdf->GetY();

			$aux="";
			
			switch($MP[$j]['operacion']){
				case "AP":
						$aux.="APERTURA\n";
						$total_operacion["APERTURA"]+=$MP[$j]['monto'];
						break;
				case "AU":
						$aux.="AUMENTO\n";
						$total_operacion["AUMENTO"]+=$MP[$j]['monto'];
						break;
				case "C":
						$aux.="COMPROMETIDO\n";
						$total_operacion["COMPROMETIDO"]+=$MP[$j]['monto'];
						break;
				case "CC":
						$aux.="COMPROMETIDO\n";
						$aux.="CAUSADO\n";
						$total_operacion["COMPROMETIDO"]+=$MP[$j]['monto'];
						$total_operacion["CAUSADO"]+=$MP[$j]['monto'];
						break;
				case "CCP":
						$aux.="COMPROMETIDO\n";
						$aux.="CAUSADO\n";
						$aux.="PAGADO\n";
						$total_operacion["COMPROMETIDO"]+=$MP[$j]['monto'];
						$total_operacion["CAUSADO"]+=$MP[$j]['monto'];
						$total_operacion["PAGADO"]+=$MP[$j]['monto'];
						break;
				case "DI":
						$aux.="DISMINUCIÓN\n";
						$total_operacion["DISMINUCION"]+=$MP[$j]['monto'];
						break;
				case "GC":
						$aux.="CAUSADO\n";
						$total_operacion["CAUSADO"]+=$MP[$j]['monto'];
						break;
				case "NN":
						$aux.="SIN AFECTACIÓN\n";
						$total_operacion["SINAFECTACION"]+=$MP[$j]['monto'];
						break;
				case "P":
						$aux.="PAGADO\n";
						$total_operacion["PAGADO"]+=$MP[$j]['monto'];
						break;
			}
			
			
			$pdf->SetXY($x2,$y);
			$pdf->SetFont('helvetica','',6);
			$pdf->MultiCell($t_operacion_mp,2,utf8_decode(trim($aux)),'','C',0);
			$yfin_2=$pdf->GetY();

			if($yfin_1>$yfin_2)
				$pdf->SetY($yfin_1+0.5);
			else
				$pdf->SetY($yfin_2+0.5);
			
			
			
			if($pdf->GetY()>250)
				$pdf->AddPage();
			
			}
		
		$pdf->Cell($ancho,1,'','T',1,'C');
		if($total_operacion["APERTURA"]>0){
				$pdf->Cell($t_acc_pro_mp+$t_cuenta_mp+$t_denom_mp,3,utf8_decode(""),'',0,'C');
				$pdf->Cell($t_operacion_mp,3,utf8_decode("APERTURA"),'',0,'C');
				$pdf->Cell($t_monto_mp,3,utf8_decode(number_format($total_operacion["APERTURA"],2,",",".")),'',1,'R');
		}
		if($total_operacion["COMPROMETIDO"]>0){
				$pdf->Cell($t_acc_pro_mp+$t_cuenta_mp+$t_denom_mp,3,utf8_decode(""),'',0,'C');
				$pdf->Cell($t_operacion_mp,3,utf8_decode("COMPROMETIDO"),'',0,'C');
				$pdf->Cell($t_monto_mp,3,utf8_decode(number_format($total_operacion["COMPROMETIDO"],2,",",".")),'',1,'R');
		}
		if($total_operacion["CAUSADO"]>0){
				$pdf->Cell($t_acc_pro_mp+$t_cuenta_mp+$t_denom_mp,3,utf8_decode(""),'',0,'C');
				$pdf->Cell($t_operacion_mp,3,utf8_decode("CAUSADO"),'',0,'C');
				$pdf->Cell($t_monto_mp,3,utf8_decode(number_format($total_operacion["CAUSADO"],2,",",".")),'',1,'R');
		}
		if($total_operacion["PAGADO"]>0){
				$pdf->Cell($t_acc_pro_mp+$t_cuenta_mp+$t_denom_mp,3,utf8_decode(""),'',0,'C');
				$pdf->Cell($t_operacion_mp,3,utf8_decode("PAGADO"),'',0,'C');
				$pdf->Cell($t_monto_mp,3,utf8_decode(number_format($total_operacion["PAGADO"],2,",",".")),'',1,'R');
		}
		if($total_operacion["AUMENTO"]>0){
				$pdf->Cell($t_acc_pro_mp+$t_cuenta_mp+$t_denom_mp,3,utf8_decode(""),'',0,'C');
				$pdf->Cell($t_operacion_mp,3,utf8_decode("AUMENTO"),'',0,'C');
				$pdf->Cell($t_monto_mp,3,utf8_decode(number_format($total_operacion["AUMENTO"],2,",",".")),'',1,'R');
		}
		if($total_operacion["DISMINUCION"]>0){
				$pdf->Cell($t_acc_pro_mp+$t_cuenta_mp+$t_denom_mp,3,utf8_decode(""),'',0,'C');
				$pdf->Cell($t_operacion_mp,3,utf8_decode("DISMINUCIÓN"),'',0,'C');
				$pdf->Cell($t_monto_mp,3,utf8_decode(number_format($total_operacion["DISMINUCION"],2,",",".")),'',1,'R');
		}
		if($total_operacion["SINAFECTACION"]>0){
				$pdf->Cell($t_acc_pro_mp+$t_cuenta_mp+$t_denom_mp,3,utf8_decode(""),'',0,'C');
				$pdf->Cell($t_operacion_mp,3,utf8_decode("SIN AFECTACIÓN"),'',0,'C');
				$pdf->Cell($t_monto_mp,3,utf8_decode(number_format($total_operacion["SINAFECTACION"],2,",",".")),'',1,'R');
		}
}



	

	$SUMA_DEBE=0;$SUMA_HABER=0;

	if(count($MC)>0 and $MC){		
		$CABECERA="MC";
		$pdf->CabeceraMC();
		for($j=0;$j<count($MC) and $MC;$j++){
			$pdf->SetFont('helvetica','',7.5);
			$pdf->Cell($t_cuenta_mc,3,utf8_decode($MC[$j]['cuenta_contable']),'',0,'C');
			$y=$pdf->GetY();
			$x=$pdf->GetX();
			$pdf->Cell($t_denom_mc,3,'','',0,'C');

			if($MC[$j]['operacion']=="D"){
				$pdf->Cell($t_debe_mc,3,utf8_decode(number_format($MC[$j]['monto'],2,",",".")),'',0,'R');
				$pdf->Cell($t_haber_mc,3,utf8_decode(''),'',1,'R');
				$SUMA_DEBE+=$MC[$j]['monto'];
				}
			else{
				$pdf->Cell($t_debe_mc,3,utf8_decode(''),'',0,'R');
				$pdf->Cell($t_haber_mc,3,utf8_decode(number_format($MC[$j]['monto'],2,",",".")),'',1,'R');
				$SUMA_HABER+=$MC[$j]['monto'];
				}
			$pdf->SetXY($x,$y);
			$pdf->MultiCell($t_denom_mc,3,utf8_decode($MC[$j]['denominacion_contable']."."),'','L',0);
			$pdf->Ln(1);

			}
		$pdf->Cell($ancho,1,utf8_decode(''),'T',1,'C');

		$pdf->Cell($t_cuenta_mc+$t_denom_mc,1,utf8_decode(''),'',0,'C');
		$pdf->Cell($t_debe_mc,4,utf8_decode(number_format($SUMA_DEBE,2,",",".")),'',0,'R');
		$pdf->Cell($t_haber_mc,4,utf8_decode(number_format($SUMA_HABER,2,",",".")),'',1,'R');
		
		}
}


$pdf->AliasNbPages();
$pdf->Output("comprobante.pdf","I");
?>