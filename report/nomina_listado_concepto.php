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



$id_periodo=explode(",",SIGA::paramGet("id_periodo"));
$id_nomina=explode(",",SIGA::paramGet("id_nomina"));

$periodo=$db->Execute("SELECT id, codigo, fecha_inicio, fecha_culminacion, descripcion FROM modulo_nomina.periodo WHERE id IN (".SIGA::paramGet("id_periodo").") ORDER BY codigo");
//cuenta contable periodo nomina

$concepto=array();
$nc=0;

$ficha=array();

//buscar todos los conceptos existentes en la nómina
for($i=0;$i<count($id_nomina);$i++):
	//Buscar nombre de la nómina
	$nomina[$i]=$db->Execute("SELECT codigo, nomina FROM modulo_nomina.nomina WHERE id=".$id_nomina[$i]);
	$nomina[$i]=$nomina[$i][0];
	$nomina[$i]["ficha"]=array();
	
	for($p=0;$p<count($periodo);$p++):	
		$nomina[$i]["ficha"][$p]=nomina::fichas($id_nomina[$i],$id_periodo[$p]);

		//buscar los concepto existentes
		for($j=0;$j<count($nomina[$i]["ficha"][$p]);$j++):
			//agrupar fichas id
			$ficha_existe=false;
			for($k=0;$k<count($ficha);$k++)
				if($nomina[$i]["ficha"][$p][$j]["id"]==$ficha[$k]["id"]){
					$ficha_existe=true;
					break;
				}
			if(!$ficha_existe)
				$ficha[]=array(
											 "id"=>$nomina[$i]["ficha"][$p][$j]["id"],
											 "nacionalidad"=>$nomina[$i]["ficha"][$p][$j]["nacionalidad"],
											 "cedula"=>$nomina[$i]["ficha"][$p][$j]["cedula"],
											 "nombre_apellido"=>$nomina[$i]["ficha"][$p][$j]["nombre_apellido"]
											 );
//print_r($nomina[$i]["ficha"][$p][$j]["concepto"]);exit;
			//para los conceptos de la ficha
			for($k=0;$k<count($nomina[$i]["ficha"][$p][$j]["concepto"]);$k++):
				//print_r($nomina[$i]["ficha"][$p][$j]["concepto"]);
				if($nomina[$i]["ficha"][$p][$j]["concepto"][$k]["tipo"]!="" or $nomina[$i]["ficha"][$p][$j]["concepto"][$k]["identificador"]=="SUELDO_NORMAL"){
				//if($nomina[$i]["ficha"][$p][$j]["concepto"][$k]["tipo"]!="" or $nomina[$i]["ficha"][$p][$j]["concepto"][$k]["identificador"]=="SUELDO_NORMAL"){
					//if(!($nomina[$i]["ficha"][$p][$j]["concepto"][$k]["tipo"]=="AP" or $nomina[$i]["ficha"][$p][$j]["concepto"][$k]["tipo"]=="D"))
					//	continue;
					//buscar si existe en el arreglo $concepto
					$encontro=false;
					for($l=0;$l<$nc;$l++){
						if($nomina[$i]["ficha"][$p][$j]["concepto"][$k]["id"]==$concepto[$l]["id"]){
							$encontro=true;
							break;						
						}
					}
					if(!$encontro){
						$concepto[$nc]=array("id"=>$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["id"],
																 "codigo"=>$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["codigo"],
																 "concepto"=>$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["concepto"],
																 "tipo"=>$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["tipo"],
																 "orden"=>$nomina[$i]["ficha"][$p][$j]["concepto"][$k]["orden"]);
						$nc++;
					}
				}
			endfor;	
		endfor;	
	endfor;	
endfor;

function concepto_ordenar($a,$b){
	return $a["orden"]-$b["orden"];
}

usort($concepto,"concepto_ordenar");

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM modulo_base.organismo AS O, modulo_base.persona as P
                          WHERE O.id_persona=P.id");

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
		global $periodo, $ancho, $t_n, $t_cedula, $t_nombre, $t_monto;
		global $c, $concepto, $organismo;
		
		$this->SetFillColor(255,255,255);
		if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
			$this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,180);
		else if(file_exists(SIGA::databasePath()."/config/logo_01.jpg"))
			$this->Image(SIGA::databasePath()."/config/logo_01.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,40);
		
		
		$this->Ln(15);
		$this->SetFont('helvetica','B',12);
		$this->Cell($this->ANCHO,5,utf8_decode('LISTADO DE CONCEPTOS'),'',1,'C');
    
    $this->SetFont('helvetica','',8);
		for($p=0;$p<count($periodo);$p++)
			$this->Cell($this->ANCHO,3,utf8_decode($periodo[$p]["descripcion"].' (PERÍODO '.$periodo[$p]["codigo"]." , DEL ".formatDate($periodo[$p]["fecha_inicio"])." AL ".formatDate($periodo[$p]["fecha_culminacion"]).")"),'',1,'C');
    
		$this->Ln(3);
		
		$this->SetFillColor(200,200,200);
		$this->SetFont('helvetica','B',9);
		$tipo="";
		$font1=7;
		switch($concepto[$c]["tipo"]){
			case "A":  $tipo="ASIGNACIÓN"; break;
			case "RD": $tipo="REINTEGRO DE DEDUCCIÓN"; break;
			case "D":  $tipo="DEDUCCIÓN"; break;
			case "AP": $tipo="APORTE PATRONAL"; $font1=5.5; break;
		}
		$this->Cell($ancho/2,5,utf8_decode($concepto[$c]["codigo"]." ".$concepto[$c]["concepto"]),'',0,'L',true);
		$this->SetFont('helvetica','B',6);
		$this->Cell($ancho/2,5,utf8_decode("$tipo"),'',1,'R',true);
		
		
		$this->SetFont('helvetica','B',7);
		$this->Cell($t_n,5,utf8_decode('Nº'),'',0,'C',true);
		$this->Cell($t_cedula,5,utf8_decode('CÉDULA'),'',0,'C',true);
		$this->Cell($t_nombre,5,utf8_decode('NOMBRES Y APELLIDO'),'',0,'C',true);
		$x=$this->GetX();
		$y=$this->GetY();
		
		$this->SetFont('helvetica','B',$font1);		
		$this->Cell($t_monto,5,utf8_decode('MONTO'),'',1,'C',true);
		if($concepto[$c]["tipo"]=="AP"){
			$this->SetXY($x,$y+3);
			$this->SetFont('helvetica','B',4.2);
			$this->Cell($t_monto/2,2,utf8_decode('TRABAJADOR'),'',0,'C',false);
			$this->Cell($t_monto/2,2,utf8_decode('PATRONO'),'',1,'C',false);		
		}
		
		
		
		$this->SetFillColor(255,255,255);
		
		}
	}

$ancho=180;
$margen_izq=15;

$t_n=5;
$t_cedula=20;
$t_monto=40;
$t_nombre=$ancho-($t_n+$t_cedula+$t_monto);

$t_alto1=3.5;

$pdf=new PDF_P("P","mm","letter");

$pdf->Medidas($margen_izq,10,$ancho);
$pdf->SetAutoPageBreak(true,10);



function buscar_valor($i_nomina,$id_periodo,$id_ficha,$id_concepto){
	$suma=0;
	$suma_ap=0;
	
	global $nomina, $periodo, $db, $id_nomina, $detalle;
	
	
	$encontro=false;
	
	for($p=0;$p<count($id_periodo);$p++):
		$f=$nomina[$i_nomina]["ficha"][$p];
		//para cada ficha
		for($j=0;$j<count($f);$j++):
			if($id_ficha!=$f[$j]["id"])
				continue;
		
			$encontro=true;
			//para cada concepto de la ficha
			for($k=0;$k<count($f[$j]["concepto"]);$k++):			
				//si coincide con 
				if($f[$j]["concepto"][$k]["id"]==$id_concepto){
					
					//buscar cuenta contable para el periodo
					//buscar fecha del periodo
					$fecha="";
					for($x=0;$x<count($periodo);$x++)
						if($periodo[$x]["id"]==$id_periodo[$p])
							$fecha=$periodo[$x]["fecha_culminacion"];
		
					if(!isset($f[$j]["concepto"][$k]["valor_final_ap"])) $f[$j]["concepto"][$k]["valor_final_ap"]=0;
					
					$suma+=floatval($f[$j]["concepto"][$k]["valor_final"]);
					$suma_ap+=floatval($f[$j]["concepto"][$k]["valor_final_ap"]);
					
					
					$cuenta_contable=$db->Execute("SELECT id_cuenta_contable, id_cuenta_contable_ap
																					FROM modulo_nomina.concepto_presupuesto_contabilidad
																					WHERE fecha<='$fecha' and id_nomina=".$id_nomina[$i_nomina]." and id_concepto=".$id_concepto."
																					ORDER BY fecha
																					DESC LIMIT 1");
					if(!isset($cuenta_contable[0])) continue;
					
					if(!isset($detalle[$cuenta_contable[0]["id_cuenta_contable"]])) $detalle[$cuenta_contable[0]["id_cuenta_contable"]]=0;
					$detalle[$cuenta_contable[0]["id_cuenta_contable"]]+=floatval($f[$j]["concepto"][$k]["valor_final"]);
					
					if(!isset($detalle[$cuenta_contable[0]["id_cuenta_contable_ap"]])) $detalle[$cuenta_contable[0]["id_cuenta_contable_ap"]]=0;
					$detalle[$cuenta_contable[0]["id_cuenta_contable_ap"]]+=floatval($f[$j]["concepto"][$k]["valor_final_ap"]);
					
				}
				
				
				
			endfor;
		endfor;
	
	
	endfor;
	
	
	if(!$encontro) return false;
	return array($suma,$suma_ap);
}


for($c=0;$c<$nc;$c++):
	$pdf->AddPage();
	$f=1;
	$total=0;
	$total_ap=0;
	
	$detalle=array();
	
	for($i=0;$i<count($id_nomina);$i++):
		$mostrar_nomina=true;
		$suma_monto=0;
		$suma_monto_ap=0;
		
		
		
		
		
		for($j=0;$j<count($ficha);$j++):
			//buscar ficha en la nomina actual para cada periodo
			$valor=buscar_valor($i,$id_periodo,$ficha[$j]["id"],$concepto[$c]["id"]);
			
			if($valor===false or ($valor[0]==0 and  $valor[1]==0)) continue;
			if($mostrar_nomina==true){
				//buscar la cuenta presupuestaria asociada al concepto ($concepto[$c]["id"]), nomina ($id_nomina[$i]), fecha ()
				$cuenta_presupuestaria="";
				for($pi=0;$pi<count($periodo);$pi++){
					$tmp=$db->Execute("SELECT id_cuenta_presupuestaria, id_cuenta_presupuestaria_ap
																					FROM modulo_nomina.concepto_presupuesto_contabilidad
																					WHERE fecha<='".$periodo[$pi]["fecha_culminacion"]."' and id_nomina=".$id_nomina[$i]." and id_concepto=".$concepto[$c]["id"]."
																					ORDER BY fecha
																					DESC LIMIT 1");
					for($tmp_i=0;$tmp_i<count($tmp);$tmp_i++){
						//$cuenta
						
						
						//$cuenta_presupuestaria.=$tmp[$tmp_i]["id_cuenta_presupuestaria"]." ".$tmp[$tmp_i]["id_cuenta_presupuestaria_ap"]." ";
						
						
						
					}
					
				}
				
				
				
				
				$pdf->Ln(1);
				$pdf->SetFont('helvetica','B',6);
				$pdf->SetFillColor(200,200,200);
				//$pdf->Cell($ancho,3,utf8_decode($nomina[$i]["codigo"]." ".$nomina[$i]["nomina"]),'',1,'L',true);
				$pdf->Cell($ancho,3,utf8_decode($nomina[$i]["codigo"]." ".$nomina[$i]["nomina"]." ".$cuenta_presupuestaria),'',1,'L',true);
				//$pdf->MultiCell($ancho,2,utf8_decode("hola\nhola2"),0,"L",1);
				
				$pdf->SetFillColor(255,255,255);
				$pdf->SetFont('helvetica','',7);
				$mostrar_nomina=false;
			}
			
			$pdf->Cell($t_n,$t_alto1,utf8_decode("$f"),'',0,'C',true);
			$pdf->Cell($t_cedula,$t_alto1,utf8_decode($ficha[$j]["nacionalidad"]."-".$ficha[$j]["cedula"]),'',0,'L',true);
			$pdf->Cell($t_nombre,$t_alto1,utf8_decode($ficha[$j]["nombre_apellido"]),'',0,'L',true);
			
			if($concepto[$c]["tipo"]=="AP"){
				$pdf->Cell($t_monto/2,$t_alto1,utf8_decode(number_format($valor[0],2,",",".")),'',0,'R',true);
				$pdf->Cell($t_monto/2,$t_alto1,utf8_decode(number_format($valor[1],2,",",".")),'',0,'R',true);
			}
			else{
				$pdf->Cell($t_monto,$t_alto1,utf8_decode(number_format($valor[0],2,",",".")),'',0,'R',true);
			}
			
			
			$pdf->Ln($t_alto1);
			
			$pdf->Ln(0.5);
			$f++;
			
			$suma_monto+=$valor[0];
			$suma_monto_ap+=$valor[1];
			
			
			
			
			
			
			
		endfor;
		
		
		if($mostrar_nomina==false){
			$pdf->SetFont('helvetica','B',7);
			if($concepto[$c]["tipo"]=="AP"){
				$pdf->Cell($ancho-$t_monto/2,$t_alto1,utf8_decode(number_format($suma_monto,2,",",".")),'',0,'R',true);
				$pdf->Cell($t_monto/2,$t_alto1,utf8_decode(number_format($suma_monto_ap,2,",",".")),'',1,'R',true);
			}
			else{
				$pdf->Cell($ancho,$t_alto1,utf8_decode(number_format($suma_monto,2,",",".")),'',1,'R',true);
			}
			$total+=$suma_monto;
			$total_ap+=$suma_monto_ap;
		}		
		
	endfor;
	
	$pdf->SetFont('helvetica','B',8);
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell($ancho-$t_monto,$t_alto1,utf8_decode("TOTAL GENERAL"),'',0,'L',true);
	
	if($concepto[$c]["tipo"]!="AP"){
		$pdf->Cell($t_monto,$t_alto1,utf8_decode(number_format($total,2,",",".")),'',1,'R',true);
	}
	else{
		$pdf->Cell($t_monto/2,$t_alto1,utf8_decode(number_format($total,2,",",".")),'',0,'R',true);
		$pdf->Cell($t_monto/2,$t_alto1,utf8_decode(number_format($total_ap,2,",",".")),'',1,'R',true);
	}
	
	
	/*
	if(isset($detalle)):
		//mostrar detalles contables
		$pdf->SetFont('helvetica','B',7);
		$pdf->Ln(5);
		$pdf->Cell($ancho-$t_monto/2,$t_alto1,utf8_decode("CUENTA CONTABLE"),'',0,'L',true);
		$pdf->Cell($t_monto/2,$t_alto1,utf8_decode("MONTO"),'',1,'L',true);
		
		$pdf->SetFont('helvetica','',7);
		$pdf->SetFillColor(255,255,255);
		foreach($detalle as $clave => $valor){
			if($clave=="") continue;
			$cuenta=$db->Execute("SELECT id_cuenta_contable, denominacion,  _formatear_cuenta_contable(id_cuenta_contable) as cuenta_contable FROM modulo_base.cuenta_contable WHERE id_cuenta_contable='$clave'");
			$pdf->SetFillColor(240,240,240);
			$pdf->Cell($ancho-$t_monto/2,$t_alto1,utf8_decode($cuenta[0]["cuenta_contable"]." ".$cuenta[0]["denominacion"]."."),'',0,'L',true);
			$pdf->Cell($t_monto/2,$t_alto1,utf8_decode(number_format($valor,2,",",".")),'',0,'R',true);
			$pdf->SetFillColor(255,255,255);
			$pdf->Cell(30,$t_alto1+0.5,"",'',1,'R',true);
		}
	endif;*/
	
endfor;
$pdf->Output("listado_concepto.pdf","I");

?>