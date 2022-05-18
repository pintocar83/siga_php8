<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/fpdf/1.84/rotation.php");
include_once("../class/nomina.class.php");


$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");



$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
if(count($id_periodo)!=1){
	print "Actualmente solo puede seleccionar un periodo.";
	exit;
}
$id_periodo=$id_periodo[0];

$id_nomina=explode(",",SIGA::paramGet("id_nomina"));



$periodo=$db->Execute("SELECT codigo, fecha_inicio, fecha_culminacion, descripcion FROM modulo_nomina.periodo WHERE id=$id_periodo");
$nomina_concepto=$db->Execute("SELECT distinct CP.id_concepto, C.concepto, C.tipo, C.orden
															 FROM modulo_nomina.concepto_periodo as CP, modulo_nomina.concepto as C
															 WHERE C.id=CP.id_concepto AND CP.id_periodo=$id_periodo AND (C.tipo<>'' OR C.identificador='SUELDO_NORMAL')
															 ORDER BY C.orden");
$columna=array();
$n_columna=0;

class PDF_P extends PDF_Rotate{
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
		global $periodo, $columna, $n_columna, $nomina_concepto, $ancho, $t_n, $t_cedula, $t_nombre, $t_cargo, $t_neto, $t_asignaciones, $t_deducciones, $t_separacion, $t_firma, $t_col;
		global $sw_deducciones, $sw_aportes, $organismo;
		$this->SetFillColor(255,255,255);
		
		if(file_exists(SIGA::databasePath()."/config/logo_02.jpg"))
			$this->Image(SIGA::databasePath()."/config/logo_02.jpg",$this->MARGEN_LEFT,2,43);
		
		
		/*if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
			$this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP-3,$ancho,10);
		else if(file_exists(SIGA::databasePath()."/config/plantilla_horizontal.jpg"))
			$this->Image(SIGA::databasePath()."/config/plantilla_horizontal.jpg",0,0,280);
		*/
		
		//$this->SetFont('helvetica','B',12);
		//$this->Cell($this->ANCHO,4,utf8_decode('NOMINA'),'',1,'C');
    
    //$this->SetFont('helvetica','',8);
		/*
		$font1=14;
		$font2=12;
		$font3=9;
		if($t_n+$t_nombre+$t_cargo<45){
			$font1=10.5;
			$font2=9;
			$font3=4.8;
		}
		else if($t_n+$t_nombre+$t_cargo<65){
			$font1=14;
			$font2=12;
			$font3=6;
		}
		*/
		
		$this->SetFont('helvetica','B',10);
		$str=utf8_decode("NÓMINA: ".$periodo[0]["descripcion"]);
		$this->Text($this->MARGEN_LEFT+$t_n+$t_nombre+$t_cargo,$this->MARGEN_TOP+3,$str);
		$str_width=$this->GetStringWidth($str);
		$this->SetFont('helvetica','',8);
		$this->Text($this->MARGEN_LEFT+$t_n+$t_nombre+$t_cargo+$str_width+1,$this->MARGEN_TOP+3,utf8_decode("(".formatDate($periodo[0]["fecha_inicio"])." - ".formatDate($periodo[0]["fecha_culminacion"]).")"));

		$this->Ln(14+5);
		$this->SetFont('helvetica','B',6.5);
		$this->SetFillColor(245,245,245);
		$this->Cell($t_n,5,utf8_decode('Nº'),'LRTB',0,'C',true);
		$this->Cell($t_nombre,5,utf8_decode('NOMBRES Y APELLIDO'),'LRTB',0,'L',true);
		$this->Cell($t_cargo,5,utf8_decode('CARGO'),'LRTB',0,'C',true);
		
		$alto_cab=20;
		
		
		$this->SetFont('helvetica','',4.5);
		
		$pos_y=$this->GetY();
		$pos_x=$this->GetX();
		$this->Rotate(90,$pos_x,$pos_y);
		$n_columna=0;
		$id_sueldo_normal="";
		
		for($i=0;$i<count($nomina_concepto);$i++)
			if($nomina_concepto[$i]["tipo"]=="A" or $nomina_concepto[$i]["tipo"]=="RD"){
				$this->SetFillColor(245,245,245);
				
				$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
				$this->Cell($alto_cab,$t_col,utf8_decode(""),'LRTB',0,'R',true);
				
				$nlineas=$this->GetStringWidth($nomina_concepto[$i]["concepto"])/$alto_cab;
				$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna+intval(($t_col-$nlineas*2.4)/2));				
				$this->MultiCell($alto_cab,2,utf8_decode($nomina_concepto[$i]["concepto"]),'','L',false);
				
				$this->SetFillColor(255,255,255);
				//$this->Cell(20,$t_col,"",'L',0,'L',true);
				$columna[$nomina_concepto[$i]["id_concepto"]]=$n_columna;
				$n_columna++;
			}
			else if($nomina_concepto[$i]["tipo"]==""){
				if($nomina_concepto[$i]["concepto"]=="SUELDO NORMAL"){
					$id_sueldo_normal=$nomina_concepto[$i]["id_concepto"];
				}
			}
		
		//columa del sueldo normal
		$this->SetFont('helvetica','B',6);
		$this->SetFillColor(245,245,245);
		if($id_sueldo_normal){
			$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
			$this->Cell($alto_cab,$t_col,utf8_decode("SUELDO NORMAL"),'LRTB',0,'L',true);
			$columna[$id_sueldo_normal]=$n_columna;
			$n_columna++;
		}
		
		//columa total asignaciones
		$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
		$this->Cell($alto_cab,$t_col,utf8_decode("ASIGNACIONES"),'LRTB',0,'L',true);
		$columna["A"]=$n_columna;
		$n_columna++;
		
		//columna de deducciones
		$sw_deducciones=false;
		$this->SetFont('helvetica','',4.5);
		for($i=0;$i<count($nomina_concepto);$i++)
			if($nomina_concepto[$i]["tipo"]=="D" or $nomina_concepto[$i]["tipo"]=="AP" or $nomina_concepto[$i]["tipo"]=="RA"){
				$this->SetFillColor(245,245,245);

				$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
				$this->Cell($alto_cab,$t_col,utf8_decode(""),'LRTB',0,'R',true);				
				$nlineas=$this->GetStringWidth($nomina_concepto[$i]["concepto"])/$alto_cab;
				$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna+intval(($t_col-$nlineas*2.4)/2));	
				$this->MultiCell($alto_cab,2,utf8_decode($nomina_concepto[$i]["concepto"]),'','L',false);
				
				$this->SetFillColor(255,255,255);
				//$this->Cell(20,$t_col,"",'L',0,'L',true);
				$columna[$nomina_concepto[$i]["id_concepto"]]=$n_columna;
				$n_columna++;
				$sw_deducciones=true;
			}
		
		
		$this->SetFillColor(245,245,245);
		if($sw_deducciones){
			//columa total deducciones
			$this->SetFont('helvetica','B',6);
			$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
			$this->Cell($alto_cab,$t_col,utf8_decode("DEDUCCIONES"),'LRTB',0,'L',true);
			$columna["D"]=$n_columna;
			$n_columna++;
			
			//columa total
			$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
			$this->Cell($alto_cab,$t_col,utf8_decode("TOTAL"),'LRTB',0,'L',true);
			$columna["N"]=$n_columna;
			$n_columna++;
		}
		

		
		
		//columna de aporte patron
		$this->SetFont('helvetica','',4.5);
		$sw_aportes=false;
		for($i=0;$i<count($nomina_concepto);$i++)
			if($nomina_concepto[$i]["tipo"]=="AP"){
				$this->SetFillColor(245,245,245);
				//$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
				//$this->Cell($alto_cab,$t_col,utf8_decode($nomina_concepto[$i]["concepto"]),'LRTB',0,'L',true);
				$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
				$this->Cell($alto_cab,$t_col,utf8_decode(""),'LRTB',0,'R',true);				
				$nlineas=$this->GetStringWidth($nomina_concepto[$i]["concepto"])/$alto_cab;
				$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna+intval(($t_col-$nlineas*2.4)/2));	
				$this->MultiCell($alto_cab,2,utf8_decode($nomina_concepto[$i]["concepto"]),'','L',false);
				
				$this->SetFillColor(255,255,255);
				//$this->Cell(20,$t_col,"",'L',0,'L',true);
				$columna["AP_".$nomina_concepto[$i]["id_concepto"]]=$n_columna;
				$n_columna++;
				$sw_aportes=true;
			}
		
		//columa total
		$this->SetFillColor(245,245,245);
		if($sw_aportes){
			$this->SetFont('helvetica','B',6);
			$this->SetXY($pos_x-5,$pos_y+$t_col*$n_columna);
			$this->Cell($alto_cab,$t_col,utf8_decode("TOTAL APORTES"),'LRTB',0,'L',true);
			$columna["AP"]=$n_columna;
			$n_columna++;
		}
		
		$this->Rotate(0);
		
		$this->SetXY($this->MARGEN_LEFT,$pos_y+5);		
		$this->SetFillColor(255,255,255);		
		}
		
		function Footer(){
			global $ancho, $margen_izq;
			$x=$margen_izq+$ancho/3;
			$y=204;
			$a=(($ancho/2)/4)+9;
			
			$this->SetDrawColor(0,0,0);
			$this->SetFont('helvetica','I',5);
			$this->SetXY($x,$y);
			$this->MultiCell($a,1.8,utf8_decode("ADMINISTRACIÓN\nELABORADOR POR:\nVLADIMIR HERNANDEZ"),"T",'C');
			
			$this->SetXY($x+$a+5,$y);
			$this->MultiCell($a,1.8,utf8_decode("ADMINISTRACIÓN\nREVISADOR POR:\nWILMER FAJARDO"),"T",'C');
			
			$this->SetXY($x+($a+5)*2,$y);
			$this->MultiCell($a,1.8,utf8_decode("PRESUPUESTO\nREVISADOR POR:\nEUCLIDES SERRA"),"T",'C');
			
			$this->SetXY($x+($a+5)*3,$y);
			$this->MultiCell($a,1.8,utf8_decode("PRESIDENCIA\nAUTORIZADOR POR:\nENRIQUE ORTIZ"),"T",'C');
			
		}

	}
	
	
$n_ap=0;
$n_d=0;
for($i=0;$i<count($nomina_concepto);$i++){
	if($nomina_concepto[$i]["tipo"]=="AP") $n_ap++;
	else if($nomina_concepto[$i]["tipo"]=="D") $n_d++;
}
$n_col_totales=1;//columna de asignaciones = 1
if($n_d!=0) $n_col_totales=3;//columna deducciones + total neto = 3
if($n_ap!=0) $n_col_totales=4;//columna deducciones + total neto + total ap = 4	


$numero_columnas=count($nomina_concepto)+$n_col_totales+$n_ap;

$formato="legal";
$ancho=335;
$margen_izq=8;

$t_n=5;
$t_cedula=20;
$t_col=11;
$t_cargo=13;
$font_totales=5.5;
$font_total_totales=4.7;
switch($numero_columnas){
	case 1:
	case 2:
	case 3:
	case 4:
		$t_col=25;
		$t_cargo=25;
		$formato="letter";
		$ancho=260;
		break;
	case 5:
	case 6:	
		$t_col=20;
		$t_cargo=20;
		$formato="letter";
		$ancho=260;
		break;
	case 7:
	case 8:
	case 9:
	case 10:
	case 11:
		$t_col=15;
		$t_cargo=15;
		$font_total_totales=4.8;
		break;
	
	case 29:
	case 30:
	case 31:
		$t_n=3;
		$t_col=10;
		$t_cargo=11;
		$font_totales=5;
		$font_total_totales=4.5;
		break;
}











//$t_nombre=$ancho-($t_n+$t_cargo+$t_col*(count($nomina_concepto)+$n_col_totales+$n_ap));
$t_nombre=$ancho-($t_n+$t_cargo+$t_col*$numero_columnas);
//print count($nomina_concepto)+$n_col_totales+$n_ap;exit;
$t_alto1=3.2;

$pdf=new PDF_P("L","mm","$formato");

$pdf->Medidas($margen_izq,9,$ancho);
$pdf->SetAutoPageBreak(true,5);
$pdf->AddPage();

$notas="";

$c=1;
$total_columna=array();
for($k=0;$k<$n_columna;$k++)
	$total_columna[$k]=0;
		
$pdf->SetDrawColor(150,150,150);
for($i=0;$i<count($id_nomina);$i++):
	//Buscar nombre de la nómina
	$nomina=$db->Execute("SELECT codigo, nomina FROM modulo_nomina.nomina WHERE id=".$id_nomina[$i]);
	$periodo_nota=$db->Execute("SELECT nota FROM modulo_nomina.periodo_nota WHERE id_nomina=".$id_nomina[$i]." and id_periodo=".$id_periodo);
	if(isset($periodo_nota[0]["nota"])){
		$tmp=trim($periodo_nota[0]["nota"],"\n\r ");
		if($tmp)
			$notas.=$tmp."\n";
	}
	
	$pdf->Ln(1);
	$pdf->SetFont('helvetica','B',6.5);
	$pdf->SetFillColor(245,245,245);
	$pdf->Cell($ancho,3,utf8_decode($nomina[0]["codigo"]." ".$nomina[0]["nomina"]),'LRTB',1,'L',true);
	$pdf->SetFillColor(255,255,255);
	
	$ficha=nomina::fichas($id_nomina[$i],$id_periodo);
	//print_r($ficha);exit;

	$suma_columna=array();
	for($k=0;$k<$n_columna;$k++)
		$suma_columna[$k]=0;
	
	for($j=0;$j<count($ficha);$j++):
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('helvetica','',6);
		$pdf->Cell($t_n,$t_alto1,utf8_decode("$c"),'LRTB',0,'C',true);		
		$pdf->Cell($t_nombre,$t_alto1,utf8_decode($ficha[$j]["nombre_apellido"]),'LRTB',0,'L',true);
		$cl=strlen($ficha[$j]["cargo"]);
		if($cl<=5)
			$pdf->SetFont('helvetica','',5);
		else if($cl>5 and $cl<15)
			$pdf->SetFont('helvetica','',4);
		else
			$pdf->SetFont('helvetica','',3);	
		$pdf->Cell($t_cargo,$t_alto1,utf8_decode($ficha[$j]["cargo"]),'LRTB',0,'C',true);
		$pdf->SetFont('helvetica','',6);
		$pos_x=$pdf->GetX();
		
		for($k=0;$k<$n_columna;$k++)
			$pdf->Cell($t_col,$t_alto1,utf8_decode(""),'LRTB',0,'R',true);
		
		
		//para los conceptos
		$pdf->SetFont('helvetica','',5.5);
		for($k=0;$k<count($ficha[$j]["concepto"]);$k++){
			if(!isset($columna[$ficha[$j]["concepto"][$k]["id"]]))
				continue;
			$x=$columna[$ficha[$j]["concepto"][$k]["id"]];
			$suma_columna[$x]+=$ficha[$j]["concepto"][$k]["valor_final"];
			if($ficha[$j]["concepto"][$k]["tipo"]=="AP"){
				$x_ap=$columna["AP_".$ficha[$j]["concepto"][$k]["id"]];
				$suma_columna[$x_ap]+=$ficha[$j]["concepto"][$k]["valor_final_ap"];
				
				$pdf->SetX($pos_x+$x_ap*$t_col);
				$pdf->Cell($t_col,$t_alto1,utf8_decode(number_format($ficha[$j]["concepto"][$k]["valor_final_ap"],2,",",".")),'',0,'R',false);
			}
			$pdf->SetX($pos_x+$x*$t_col);
			$pdf->Cell($t_col,$t_alto1,utf8_decode(number_format($ficha[$j]["concepto"][$k]["valor_final"],2,",",".")),'',0,'R',false);
		}
		
		$pdf->SetFont('helvetica','B',5.5);
		$pdf->SetFillColor(245,245,245);
		//total asignaciones
		$pdf->SetX($pos_x+$columna["A"]*$t_col);
		$pdf->Cell($t_col,$t_alto1,utf8_decode(number_format($ficha[$j]["total_asignacion"],2,",",".")),'LRTB',0,'R',true);
		
		
		if($sw_deducciones){
			//total deducciones
			$pdf->SetX($pos_x+$columna["D"]*$t_col);
			$pdf->Cell($t_col,$t_alto1,utf8_decode(number_format($ficha[$j]["total_deduccion"],2,",",".")),'LRTB',0,'R',true);
			//total neto
			$pdf->SetX($pos_x+$columna["N"]*$t_col);
			$pdf->Cell($t_col,$t_alto1,utf8_decode(number_format($ficha[$j]["total_neto"],2,",",".")),'LRTB',0,'R',true);
		}
			
		
		//total ap
		if($sw_aportes){
			$pdf->SetX($pos_x+$columna["AP"]*$t_col);
			$pdf->Cell($t_col,$t_alto1,utf8_decode(number_format($ficha[$j]["total_ap"],2,",",".")),'LRTB',0,'R',true);
		}
		
		$pdf->Ln($t_alto1);
		$c++;
		$suma_columna[$columna["A"]]+=$ficha[$j]["total_asignacion"];
		if($sw_deducciones){
			$suma_columna[$columna["D"]]+=$ficha[$j]["total_deduccion"];
			$suma_columna[$columna["N"]]+=$ficha[$j]["total_neto"];
		}
		
		if($sw_aportes)
			$suma_columna[$columna["AP"]]+=$ficha[$j]["total_ap"];
	endfor;
	
	$pdf->SetFont('helvetica','B',$font_totales);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell($t_n+$t_nombre+$t_cargo,$t_alto1,utf8_decode(""),'',0,'C',false);
	$pdf->SetFillColor(245,245,245);
	for($k=0;$k<$n_columna;$k++){
		$pdf->Cell($t_col,$t_alto1,utf8_decode(number_format($suma_columna[$k],2,",",".")),'LRTB',0,'R',true);
		$total_columna[$k]+=$suma_columna[$k];
	}
	$pdf->Ln($t_alto1);
	

endfor;

$pdf->SetDrawColor(150,150,150);
$pdf->Ln(2);
$pdf->SetFont('helvetica','B',7);
$pdf->SetFillColor(255,255,255);
$pdf->Cell($t_n+$t_nombre+$t_cargo,$t_alto1,utf8_decode("NOTAS:"),'',0,'L',false);
$pdf->SetFillColor(245,245,245);
$pdf->SetFont('helvetica','B',$font_total_totales);
for($k=0;$k<$n_columna;$k++){
	$pdf->Cell($t_col,$t_alto1,utf8_decode(number_format($total_columna[$k],2,",",".")),'LRTB',0,'R',true);
}

$pdf->SetFont('helvetica','',6);
$pdf->Ln($t_alto1+1);
$pdf->SetX($margen_izq+1);
$pdf->MultiCell($ancho/3,2.5,utf8_decode(trim("$notas"," \n")),'','L',false);





$pdf->Output("nomina.pdf","I");

?>