<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/sql_query_total.php");
include_once("../library/fpdf/1.84/fpdf.php");
include_once("../class/nomina.class.php");

$db=SIGA::DBController();


$params=$_GET;

$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
if(count($id_periodo)!=1){
	print "Actualmente solo puede seleccionar un periodo.";
	exit;
}

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");

$id_periodo=$id_periodo[0];

$id_nomina=explode(",",SIGA::paramGet("id_nomina"));



$periodo=$db->Execute("SELECT codigo, fecha_inicio, fecha_culminacion, descripcion FROM modulo_nomina.periodo WHERE id=$id_periodo");


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
		global $periodo, $ancho, $t_n, $t_cedula, $t_nombre, $t_neto, $t_asignaciones, $t_deducciones, $t_separacion, $t_firma;
		global $organismo;
		$this->SetFillColor(255,255,255);

		if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
			$this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,180);
		else if(file_exists(SIGA::databasePath()."/config/logo_01.jpg"))
			$this->Image(SIGA::databasePath()."/config/logo_01.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,40);
		
		$this->Ln(15);
		$this->SetFont('helvetica','B',12);
		$this->Cell($this->ANCHO,5,utf8_decode('LISTADO DE FIRMAS'),'',1,'C');
    
    $this->SetFont('helvetica','',8);
		$this->Cell($this->ANCHO,5,utf8_decode($periodo[0]["descripcion"].' (PERÍODO '.$periodo[0]["codigo"].", DEL ".formatDate($periodo[0]["fecha_inicio"])." AL ".formatDate($periodo[0]["fecha_culminacion"]).")"),'',1,'C');
    
		$this->Ln(3);
		$this->SetFont('helvetica','B',7);
		$this->SetFillColor(200,200,200);
		$this->Cell($t_n,5,utf8_decode('Nº'),'',0,'C',true);
		$this->Cell($t_cedula,5,utf8_decode('CÉDULA'),'',0,'C',true);
		$this->Cell($t_nombre,5,utf8_decode('NOMBRES Y APELLIDO'),'',0,'C',true);
		$this->Cell($t_asignaciones,5,utf8_decode('ASIGNACIONES'),'',0,'C',true);
		$this->Cell($t_deducciones,5,utf8_decode('DEDUCCIONES'),'',0,'C',true);
		$this->Cell($t_neto,5,utf8_decode('NETO'),'',0,'C',true);
		$this->Cell($t_separacion+$t_firma,5,utf8_decode('FIRMA'),'',1,'C',true);
		$this->SetFillColor(255,255,255);
		
		}
	}

$ancho=180;
$margen_izq=15;

$t_n=5;
$t_cedula=20;
$t_neto=$t_asignaciones=$t_deducciones=23;
$t_separacion=3;
$t_firma=25;
$t_nombre=$ancho-($t_n+$t_cedula+$t_neto+$t_asignaciones+$t_deducciones+$t_separacion+$t_firma);

$t_alto1=4;

$pdf=new PDF_P("P","mm","letter");

$pdf->Medidas($margen_izq,10,$ancho);
$pdf->SetAutoPageBreak(true,10);
$pdf->AddPage();

$c=1;
$total_asig=0;
$total_deduc=0;
$total_neto=0;
for($i=0;$i<count($id_nomina);$i++):
	//Buscar nombre de la nómina
	$nomina=$db->Execute("SELECT codigo, nomina FROM modulo_nomina.nomina WHERE id=".$id_nomina[$i]);
	$pdf->Ln(1);
	$pdf->SetFont('helvetica','B',6);
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell($ancho,3,utf8_decode($nomina[0]["codigo"]." ".$nomina[0]["nomina"]),'',1,'L',true);
	$pdf->SetFillColor(255,255,255);
	
	$ficha=nomina::fichas($id_nomina[$i],$id_periodo);
	
	$suma_asig=0;
	$suma_deduc=0;
	$suma_neto=0;
	$pdf->SetFont('helvetica','',7);
	for($j=0;$j<count($ficha);$j++):
		$pdf->Cell($t_n,$t_alto1,utf8_decode("$c"),'',0,'C',true);
		$pdf->Cell($t_cedula,$t_alto1,utf8_decode($ficha[$j]["nacionalidad"]."-".$ficha[$j]["cedula"]),'',0,'L',true);
		$pdf->Cell($t_nombre,$t_alto1,utf8_decode($ficha[$j]["nombre_apellido"]),'',0,'L',true);
		$pdf->Cell($t_asignaciones,$t_alto1,utf8_decode(number_format($ficha[$j]["total_asignacion"],2,",",".")),'',0,'R',true);
		$pdf->Cell($t_deducciones,$t_alto1,utf8_decode(number_format($ficha[$j]["total_deduccion"],2,",",".")),'',0,'R',true);
		$pdf->Cell($t_neto,$t_alto1,utf8_decode(number_format($ficha[$j]["total_neto"],2,",",".")),'',0,'R',true);
		$pdf->Cell($t_separacion,$t_alto1,utf8_decode(""),'',0,'C',true);
		$pdf->SetDrawColor(200,200,200);
		$pdf->Cell($t_firma,$t_alto1,utf8_decode(''),'B',1,'C',true);
		$pdf->SetDrawColor(0,0,0);
		$pdf->Ln(1);
		$c++;
		$suma_asig+=$ficha[$j]["total_asignacion"];
		$suma_deduc+=$ficha[$j]["total_deduccion"];
		$suma_neto+=$ficha[$j]["total_neto"];
	endfor;
	if(count($ficha)==0) $pdf->Ln(1);
	$pdf->SetFont('helvetica','B',7);
	$pdf->Cell($t_n+$t_cedula+$t_nombre,$t_alto1,utf8_decode(""),'',0,'C',true);
	$pdf->Cell($t_asignaciones,$t_alto1,utf8_decode(number_format($suma_asig,2,",",".")),'',0,'R',true);
	$pdf->Cell($t_deducciones,$t_alto1,utf8_decode(number_format($suma_deduc,2,",",".")),'',0,'R',true);
	$pdf->Cell($t_neto,$t_alto1,utf8_decode(number_format($suma_neto,2,",",".")),'',1,'R',true);
	$total_asig+=$suma_asig;
	$total_deduc+=$suma_deduc;
	$total_neto+=$suma_neto;
endfor;

$pdf->Ln(2);
$pdf->SetFont('helvetica','B',8);
$pdf->SetFillColor(200,200,200);
$pdf->Cell($t_n+$t_cedula+$t_nombre,$t_alto1,utf8_decode("TOTAL GENERAL"),'',0,'C',true);
$pdf->Cell($t_asignaciones,$t_alto1,utf8_decode(number_format($total_asig,2,",",".")),'',0,'R',true);
$pdf->Cell($t_deducciones,$t_alto1,utf8_decode(number_format($total_deduc,2,",",".")),'',0,'R',true);
$pdf->Cell($t_neto,$t_alto1,utf8_decode(number_format($total_neto,2,",",".")),'',0,'R',true);
$pdf->Cell($t_separacion+$t_firma,$t_alto1,utf8_decode(""),'',0,'C',true);
$pdf->SetFillColor(255,255,255);


$pdf->Output("listado_firma.pdf","I");

?>