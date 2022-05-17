<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/sql_query_total.php");
include_once("../library/fpdf/1.7/fpdf.php");
include_once("../class/nomina.class.php");


$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
if(count($id_periodo)!=1){
	print "Actualmente solo puede seleccionar un periodo.";
	exit;
}
$id_periodo=$id_periodo[0];

$db=SIGA::DBController();

//buscar detalles del periodo
$periodo=$db->Execute("SELECT id, codigo, fecha_inicio, fecha_culminacion, tipo, descripcion FROM modulo_nomina.periodo WHERE id=$id_periodo");
$periodo=$periodo[0];

$id_nomina=explode(",",SIGA::paramGet("id_nomina"));
$nomina=array();
//buscar las nóminas asociadas al periodo
for($i=0;$i<count($id_nomina);$i++){
	$nomina[$i]=$db->Execute("SELECT id, codigo, nomina FROM modulo_nomina.nomina WHERE id=".$id_nomina[$i]);
	$nomina[$i]=$nomina[$i][0];
}

$retorno=nomina::detalle_presupuestario_contable($periodo,$nomina);

if(!$retorno["success"]){
	print $retorno["message"];
	exit;
}


$sw="P";

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
	function CabeceraDC(){
		global $sw, $ancho, $tc_cuenta, $tc_nombre, $tc_debe, $tc_haber;		
		$this->SetFillColor(200,200,200);
		$this->SetFont('helvetica','B',10);
		$this->Cell($ancho,5,utf8_decode('DETALLE CONTABLE'),'',1,'C');
		$this->SetFont('helvetica','B',8);
		$this->Cell($tc_cuenta,5,utf8_decode('CUENTA'),'',0,'C',true);
		$this->Cell($tc_nombre,5,utf8_decode('DENOMINACIÓN'),'',0,'C',true);
		$this->Cell($tc_debe,5,utf8_decode('DEBE'),'',0,'C',true);
		$this->Cell($tc_haber,5,utf8_decode('HABER'),'',1,'C',true);
		$this->Ln(1);
	} 
	function Header(){
		global $sw, $ancho, $tp_accpro, $tp_cuenta, $tp_nombre, $tp_monto, $periodo, $nomina;
		$this->SetFillColor(200,200,200);
		
		if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
			$this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,180);
		else if(file_exists(SIGA::databasePath()."/config/logo_01.jpg"))
			$this->Image(SIGA::databasePath()."/config/logo_01.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,40);
		
		
		$this->Ln(15);
		$this->SetFont('helvetica','B',12);
		$this->Cell($ancho,5,utf8_decode('RESUMEN PRESUPUESTARIO / CONTABLE'),'',1,'C');
		$this->SetFont('helvetica','',8);
		$this->Cell($ancho,3,utf8_decode($periodo["descripcion"]),'',1,'C');
		$this->Cell($ancho,3,utf8_decode('PERÍODO '.$periodo["codigo"].", DEL ".formatDate($periodo["fecha_inicio"])." AL ".formatDate($periodo["fecha_culminacion"])),'',1,'C');
		
		
		$this->SetFont('helvetica','',6);
		$cad="";
		for($i=0;$i<count($nomina);$i++){
			$cad.=$nomina[$i]["nomina"].($i<count($nomina)-1?", ":".");
		}		
		$this->MultiCell($ancho,3,utf8_decode($cad),'','C');
		$this->Ln(2);
		
		
		if($sw=="P"){
			$this->SetFont('helvetica','B',10);
			$this->Cell($ancho,5,utf8_decode('DETALLE PRESUPUESTARIO'),'',1,'C');
			$this->SetFont('helvetica','B',8);
			$this->Cell($tp_accpro,5,utf8_decode('ACC/PRO'),'',0,'C',true);
			$this->Cell($tp_cuenta,5,utf8_decode('CUENTA'),'',0,'C',true);
			$this->Cell($tp_nombre,5,utf8_decode('DENOMINACIÓN'),'',0,'C',true);
			$this->Cell($tp_monto,5,utf8_decode('MONTO'),'',1,'C',true);
			$this->Ln(1);
		}
		else{
			$this->CabeceraDC();			
		}		
		$this->SetFillColor(255,255,255);
	}
}

$ancho=185;
$margen_izq=15;

$tp_accpro=30;
$tp_cuenta=20;
$tp_monto=20;
$tp_nombre=$ancho-($tp_accpro+$tp_cuenta+$tp_monto);

$tc_cuenta=25;
$tc_debe=$tc_haber=20;
$tc_nombre=$ancho-($tc_cuenta+$tc_debe+$tc_haber);


$t_alto=4;


$pdf=new PDF_P("P","mm","letter");

$pdf->Medidas($margen_izq,10,$ancho);
$pdf->SetAutoPageBreak(true,10);

$pdf->AddPage();
$pdf->SetFillColor(255,255,255);

$pdf->SetFont('helvetica','',7);

//detalles presupuestarios
$suma=0;
for($i=0;$i<count($retorno["detalle"]["presupuestario"]);$i++){
	$detalle=$retorno["detalle"]["presupuestario"][$i];
	//print $detalle["estructura_presupuestaria"]." ".$detalle["id_cuenta_presupuestaria"]." ".$detalle["operacion"]." ".$detalle["monto"]."\n";
	
	$pdf->Cell($tp_accpro,$t_alto,utf8_decode($detalle["estructura_presupuestaria"]),'',0,'C',true);
	$pdf->Cell($tp_cuenta,$t_alto,utf8_decode($detalle["cuenta_presupuestaria"]),'',0,'C',true);
	$x=$pdf->GetX();
	$pdf->Cell($tp_nombre,$t_alto,utf8_decode(""),'',0,'L',true);
	$pdf->Cell($tp_monto,$t_alto,utf8_decode(number_format($detalle["monto"],2,",",".")),'',0,'R',true);
	
	$pdf->SetX($x);
	$pdf->MultiCell($tp_nombre,$t_alto,utf8_decode($detalle["denominacion"]."."),'','L',true);
	
	$suma+=$detalle["monto"];
}

$pdf->SetFont('helvetica','B',7);
$pdf->Cell($ancho,$t_alto,utf8_decode(number_format($suma,2,",",".")),'T',0,'R',true);

$sw="C";

$pdf->Ln(10);
$pdf->CabeceraDC();

$pdf->SetFillColor(255,255,255);
$pdf->SetFont('helvetica','',7);

$suma_debe=0;
$suma_haber=0;

for($i=0;$i<count($retorno["detalle"]["contable"]);$i++){
	$detalle=$retorno["detalle"]["contable"][$i];
	$pdf->Cell($tc_cuenta,$t_alto,utf8_decode($detalle["cuenta_contable"]),'',0,'C',true);
	$x=$pdf->GetX();
	$pdf->Cell($tc_nombre,$t_alto,utf8_decode(""),'',0,'C',true);
	
	if($detalle["operacion"]=="D"){
		$pdf->Cell($tc_debe,$t_alto,utf8_decode(number_format($detalle["monto"],2,",",".")),'',0,'R',true);
		$pdf->Cell($tc_haber,$t_alto,"",'',0,'R',true);
		$suma_debe+=$detalle["monto"];
	}
	
	if($detalle["operacion"]=="H"){
		$pdf->Cell($tc_debe,$t_alto,"",'',0,'R',true);
		$pdf->Cell($tc_haber,$t_alto,utf8_decode(number_format($detalle["monto"],2,",",".")),'',0,'R',true);
		$suma_haber+=$detalle["monto"];
	}
	$pdf->SetX($x);
	$pdf->MultiCell($tc_nombre,$t_alto,utf8_decode($detalle["denominacion"]."."),'','L',true);
}


$pdf->SetFont('helvetica','B',7);
$pdf->Cell($ancho-$tc_debe-$tc_haber,$t_alto,"",'T',0,'R',true);
$pdf->Cell($tc_debe,$t_alto,utf8_decode(number_format($suma_debe,2,",",".")),'T',0,'R',true);
$pdf->Cell($tc_haber,$t_alto,utf8_decode(number_format($suma_haber,2,",",".")),'T',0,'R',true);


	

$pdf->Output("resumen_presupuesatario_contable.pdf","I");

?>