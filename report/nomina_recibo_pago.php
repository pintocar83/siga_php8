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

//indica si se generan los recibos de pago en la carpeta de cada persona
$generar=SIGA::paramGet("generar");

$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
if(count($id_periodo)!=1){
	print "Actualmente solo puede seleccionar un periodo.";
	exit;
}

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");

$id_periodo=$id_periodo[0];





if(!SIGA::paramGet("id_nomina")){
	$nomina=$db->Execute("SELECT id FROM modulo_nomina.nomina WHERE activo");
	$id_nomina=array();
	for($i=0;$i<count($nomina);$i++)
		$id_nomina[]=$nomina[$i]["id"];
}
else{
	$id_nomina=explode(",",SIGA::paramGet("id_nomina"));
}

$periodo=$db->Execute("SELECT codigo, fecha_inicio, fecha_culminacion, descripcion, cerrado FROM modulo_nomina.periodo WHERE id=$id_periodo");

if($generar and $periodo[0]["cerrado"]=="f"){
	print "No puede publicar los recibos de pago mientras el periodo siga abierto.";
	exit;
}

$ficha=array();
for($i=0;$i<count($id_nomina);$i++){
	$tmp=nomina::fichas($id_nomina[$i],$id_periodo);
	for($j=0;$j<count($tmp);$j++)
		$ficha[]=$tmp[$j];
}
usort($ficha, array("nomina",'ficha_ordenar'));
	


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
		}
	}

$ancho=180;
$margen_izq=15;

$t_cedula=25;
$t_cuenta=50;
$t_cargo=40;
$t_nombre=$ancho-($t_cedula+$t_cuenta+$t_cargo);
$t_asignaciones=$t_deducciones=(180/2)-10;

$t_alto1=4;
$t_alto2=4;

//print_r($ficha);exit;
if(!$generar):
	$pdf=new PDF_P("P","mm","letter");	
	$pdf->Medidas($margen_izq,10,$ancho);
	$pdf->SetAutoPageBreak(false,10);
endif;


for($i=0;$i<count($ficha);$i++):
	if($generar):
		$pdf=new PDF_P("P","mm","letter");	
		$pdf->Medidas($margen_izq,10,$ancho);
		$pdf->SetAutoPageBreak(false,10);
	endif;	
	$pdf->AddPage();
	
	
	for($q=0;$q<2;$q++):
		$y=$pdf->GetY();
		if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
			$pdf->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$margen_izq,$y,180);
		else if(file_exists(SIGA::databasePath()."/config/logo_01.jpg"))
			$pdf->Image(SIGA::databasePath()."/config/logo_01.jpg",$margen_izq,$y,40);
			
		$pdf->Ln(15);
		
		$pdf->SetFont('helvetica','B',12);
		$pdf->Cell($ancho,5,utf8_decode('RECIBO DE PAGO'),'',1,'C');
		
		$pdf->SetFont('helvetica','B',8);
		$pdf->Cell($ancho,3.5,utf8_decode($periodo[0]["descripcion"]),'',1,'C');
		$pdf->SetFont('helvetica','',7);
		$pdf->Cell($ancho,2.5,utf8_decode('PERÍODO '.$periodo[0]["codigo"].", DEL ".formatDate($periodo[0]["fecha_inicio"])." AL ".formatDate($periodo[0]["fecha_culminacion"])),'',1,'C');
		
		$pdf->Ln(4);
		
		$pdf->SetFont('helvetica','B',7);
		$pdf->Cell($t_cedula,$t_alto1,utf8_decode("CÉDULA"),'LRT',0,'C');
		$pdf->Cell($t_nombre,$t_alto1,utf8_decode("NOMBRE Y APELLIDO"),'LRT',0,'C');
		$pdf->Cell($t_cargo,$t_alto1,utf8_decode("CARGO"),'LRT',0,'C');
		$pdf->Cell($t_cuenta,$t_alto1,utf8_decode("CUENTA NÓMINA"),'LRT',1,'C');
		
		$pdf->SetFont('helvetica','',8);
		$pdf->Cell($t_cedula,$t_alto1,utf8_decode($ficha[$i]["nacionalidad"]."-".$ficha[$i]["cedula"]),'LRB',0,'C');
		$pdf->Cell($t_nombre,$t_alto1,utf8_decode($ficha[$i]["nombre_apellido"]),'LRB',0,'C');
		$pdf->Cell($t_cargo,$t_alto1,utf8_decode($ficha[$i]["cargo_denominacion"]),'LRB',0,'C');
		$pdf->Cell($t_cuenta,$t_alto1,utf8_decode(""),'LRB',1,'C');
		
		$pdf->Ln(4);
		$pdf->SetFont('helvetica','',7);
		$pdf->Cell(5,$t_alto1,utf8_decode(""),'',0,'C');
		$pdf->Cell($t_asignaciones,$t_alto1,utf8_decode("ASIGNACIONES"),'B',0,'C');
		$pdf->Cell(10,$t_alto1,utf8_decode(""),'',0,'C');
		$pdf->Cell($t_deducciones,$t_alto1,utf8_decode("DEDUCCIONES"),'B',1,'C');
		
		$y=$pdf->GetY();
		
		$c_asig=0;
		$c_deduc=0;
		$y_asig=$y;
		$y_deduc=$y;
		for($j=0;$j<count($ficha[$i]["concepto"]);$j++){
			$concepto=$ficha[$i]["concepto"][$j];
			$pdf->SetTextColor(0,0,0);
			switch($concepto["tipo"]){
				case "_":
					$pdf->SetTextColor(150,150,150);
				case "A":
				case "RD":
					/*$pdf->SetXY($margen_izq+5,$y+$c_asig*$t_alto2);
					$pdf->Cell($t_asignaciones-20,$t_alto2,utf8_decode($concepto["concepto"]),'',0,'L');
					$pdf->Cell(20,$t_alto2,utf8_decode(number_format($concepto["valor_final"],2,",",".")),'',0,'R');*/
					
					$pdf->SetXY($margen_izq+5+$t_asignaciones-20,$y_asig);
					$pdf->Cell(20,$t_alto2,utf8_decode(number_format($concepto["valor_final"],2,",",".")),'',0,'R');					
					$pdf->SetXY($margen_izq+5,$y_asig);
					
					if($concepto["identificador"]=="BONO_ALIMENTACION")
						$concepto["concepto"].="\n(NO GENERA INCIDENCIA SALARIAL)";
					
					
					$pdf->MultiCell($t_asignaciones-20,$t_alto2,utf8_decode($concepto["concepto"]."."),'','L');
					$y_asig=$pdf->GetY();
					
					$c_asig++;
					break;
				case "D":
				case "AP":
				case "RA":
					/*$pdf->SetXY($margen_izq+5+$t_asignaciones+10,$y+$c_deduc*$t_alto2);
					$pdf->Cell($t_deducciones-20,$t_alto2,utf8_decode($concepto["concepto"]),'',0,'L');
					$pdf->Cell(20,$t_alto2,utf8_decode(number_format($concepto["valor_final"],2,",",".")),'',0,'R');*/
					
					$pdf->SetXY($margen_izq+5+$t_asignaciones*2-10,$y_deduc);
					$pdf->Cell(20,$t_alto2,utf8_decode(number_format($concepto["valor_final"],2,",",".")),'',0,'R');					
					$pdf->SetXY($margen_izq+5+$t_asignaciones+10,$y_deduc);
					
					$pdf->MultiCell($t_asignaciones-20,$t_alto2,utf8_decode($concepto["concepto"]."."),'','L');
					$y_deduc=$pdf->GetY();
					
					
					$c_deduc++;
					break;
			}
		}
	
		$pdf->SetXY($margen_izq,$y+60);
		$pdf->Cell(5,$t_alto1,utf8_decode(""),'',0,'C');
		$pdf->Cell($t_asignaciones,$t_alto1,utf8_decode(number_format($ficha[$i]["total_asignacion"],2,",",".")),'T',0,'R');
		$pdf->Cell(10,$t_alto1,utf8_decode(""),'',0,'C');
		$pdf->Cell($t_deducciones,$t_alto1,utf8_decode(number_format($ficha[$i]["total_deduccion"],2,",",".")),'T',1,'R');
		
		
		$pdf->SetFont('helvetica','',7);
		$pdf->Cell($ancho-30,8,utf8_decode("(ASIGNACIONES - DEDUCCIONES) ="),'',0,'R');
		$pdf->SetFont('helvetica','B',10);
		$pdf->Cell(25,8,utf8_decode("".number_format($ficha[$i]["total_neto"],2,",",".")),'',1,'R');
		
		if($q==0){
			$pdf->Ln(10);
			$pdf->SetFont('helvetica','',14);
			$pdf->SetDrawColor(150,150,150);
			$pdf->SetTextColor(150,150,150);
			$pdf->Cell($ancho,10,utf8_decode('C  O  P  I  A'),'T',1,'C');
			$pdf->SetDrawColor(0,0,0);
			$pdf->SetTextColor(0,0,0);
			$pdf->Ln(1);
		}
		
		
	endfor;
	
	
	if($generar):	
		$anio=explode("-",$periodo[0]["fecha_inicio"])[0];
		$codigo=$periodo[0]["codigo"];
		$nombre="${codigo}_".str_replace(" $anio - QUINCENA #","_",$periodo[0]["descripcion"]).".pdf";		
		$nombre=str_replace("#"," ",$nombre);
		
		$ruta_base=SIGA::databasePath()."/persona/".$ficha[$i]["nacionalidad"].$ficha[$i]["cedula"];
		if(!file_exists($ruta_base))	mkdir($ruta_base);
		
		$ruta_base="${ruta_base}/nomina_recibos";
		if(!file_exists($ruta_base))	mkdir($ruta_base);
		
		$ruta_base="${ruta_base}/${anio}";
		if(!file_exists($ruta_base)) 	mkdir($ruta_base);
		
		$archivo="$ruta_base/${nombre}";
    $pdf->Output($archivo,"F");
		
		print "(".($i+1).") Generando archivo: $archivo<br>";
	endif;	
endfor;	
	
if(!$generar):
	$pdf->Output("recibos_pago.pdf","I");
endif;
?>