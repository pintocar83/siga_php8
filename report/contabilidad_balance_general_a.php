<?php
session_start();
if (!$_SESSION["uLogin"]){
    session_destroy();
    die(Header("Location: ../../index.php"));
	}
include ("../../clases/class_ConexionBD.php");

/**
* Sistema para la Gestion Administrativa Fundacite Sucre (SIGAFS)
* Copyright (C) 2009 by FUNDACITE Sucre
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
* @author Carlos J. Pinto B. <pintocar83@gmail.com>
* @date 2009-08-01
* @version 20091113
*/

$FECHA=date("d/m/Y h:i a",time());
include_once("../../include/funciones_php.php");

$bd=new ConexionBD();
$bd->abrir(BD, SERVIDOR, USUARIO, CLAVE, PUERTO);

$ocultar=$_GET["OCULTAR"];

$anio=LimpiarCadena($_SESSION['BDanio_presupuestario']);
//
//
// switch($_GET["mes"]){
// 	case 1: $fecha_final="$anio-01-31"; break;
// 	case 2: if(esBisiesto($anio)) $fecha_final="$anio-02-29"; else $fecha_final="$anio-02-28"; break;
// 	case 3: $fecha_final="$anio-03-31"; break;
// 	case 4: $fecha_final="$anio-04-30"; break;
// 	case 5: $fecha_final="$anio-05-31"; break;
// 	case 6: $fecha_final="$anio-06-30"; break;
// 	case 7: $fecha_final="$anio-07-31"; break;
// 	case 8: $fecha_final="$anio-08-31"; break;
// 	case 9: $fecha_final="$anio-09-30"; break;
// 	case 10: $fecha_final="$anio-10-31"; break;
// 	case 11: $fecha_final="$anio-11-30"; break;
// 	case 12: $fecha_final="$anio-12-31"; break;
// 	default: exit;
// 	}
//
//
//
//

$fecha_final=desformatearFecha($_GET["FF"]);






$sql="SELECT
			MC.id_codigo_contable,
			CCC.denominacion_cta_contable,
			SUM(case when MC.tipo_operacion_mc = 'D' then MC.monto_mc else 0 end) AS total_debe,
			SUM(case when MC.tipo_operacion_mc = 'H' then MC.monto_mc else 0 end) AS total_haber
		FROM
			modulo_contabilidad.movimiento_contable AS MC,
			modulo_presupuesto.comprobante AS C,
			modulo_catalogo_ctas.catalogo_ctas_contable AS CCC
		WHERE
			C.eliminado=false AND
			MC.id_comprobante=C.id_comprobante AND
			MC.id_codigo_contable=CCC.id_codigo_contable AND
			es_acronimo_contable(C.acronimo_c)  AND
			C.fecha_c BETWEEN '$anio-01-01' AND '$fecha_final'
		GROUP BY
			MC.id_codigo_contable,
			CCC.denominacion_cta_contable
		ORDER BY
			MC.id_codigo_contable";
$CC=$bd->consultar($sql,'ARREGLO');
if($CC==0) return;




$N=count($CC);
$N2=$N;
for($i=0;$i<$N2;$i++){
	$CC[$i]["es_padre"]=0;
	//buscar los padres de la cuenta
	$PADRES=RetornarPadres($CC[$i]["id_codigo_contable"]);

	//ver si los padres fueron agregados
	for($j=0;$j<count($PADRES);$j++){
		$sw=false;
		for($k=0;$k<$N;$k++)
			if($PADRES[$j]==$CC[$k]["id_codigo_contable"]){
				$sw=true;
				break;
				}
		//si no lo encuentra, ingresarlo
		if($sw==false){
			$CC[$N]["id_codigo_contable"]=$PADRES[$j];
			$sql="select denominacion_cta_contable from modulo_catalogo_ctas.catalogo_ctas_contable where id_codigo_contable='".$PADRES[$j]."'";
			$AUX=$bd->consultar($sql,'ARREGLO');
			$CC[$N]["denominacion_cta_contable"]=$AUX[0]["denominacion_cta_contable"];
			$CC[$N]["es_padre"]=1;
			$CC[$N]["total_debe"]=0;
			$CC[$N]["total_haber"]=0;
			$N++;
			}
		}
	}



sort($CC);




for($i=0;$i<$N;$i++)
	if($CC[$i]["es_padre"])
		for($j=$i+1;$j<$N;$j++)
			if(EsPadreHijo($CC[$i]["id_codigo_contable"],$CC[$j]["id_codigo_contable"])){
				$CC[$i]["total_debe"]+=$CC[$j]["total_debe"];
				$CC[$i]["total_haber"]+=$CC[$j]["total_haber"];
				}







$ACTIVO_TAM=0;
$TOTAL_ACTIVO=0;
$PASIVO_TAM=0;
$TOTAL_PASIVO=0;
$CAPITAL_TAM=0;
$TOTAL_CAPITAL=0;
$TOTAL_INGRESOS=0;
$TOTAL_EGRESOS=0;

for($i=0;$i<$N;$i++){
	$CuentaContable["id_codigo_contable"]=$CC[$i]["id_codigo_contable"];
	$CuentaContable["denominacion_cta_contable"]=$CC[$i]["denominacion_cta_contable"];
	$CuentaContable["es_padre"]=$CC[$i]["es_padre"];

	if(AumentaCuentaContable($CC[$i]["id_codigo_contable"])){
		$CuentaContable["total"]=$CC[$i]["total_debe"]-$CC[$i]["total_haber"];
		}
	else{
		$CuentaContable["total"]=$CC[$i]["total_haber"]-$CC[$i]["total_debe"];
		}

	if(substr($CC[$i]["id_codigo_contable"],0,1)=="1"){//activo
		$ACTIVO[$ACTIVO_TAM]=$CuentaContable;
		if(!$CC[$i]["es_padre"])
			$TOTAL_ACTIVO+=$CuentaContable["total"];
		$ACTIVO_TAM++;
		}
	else if(substr($CC[$i]["id_codigo_contable"],0,1)=="2"){//pasivo
		$PASIVO[$PASIVO_TAM]=$CuentaContable;
		if(!$CC[$i]["es_padre"])
			$TOTAL_PASIVO+=$CuentaContable["total"];
		$PASIVO_TAM++;
		}
	else if(substr($CC[$i]["id_codigo_contable"],0,1)=="3"){//capital
		$CAPITAL[$CAPITAL_TAM]=$CuentaContable;
		if(!$CC[$i]["es_padre"])
			$TOTAL_CAPITAL+=$CuentaContable["total"];
		$CAPITAL_TAM++;
		}
	else if(substr($CC[$i]["id_codigo_contable"],0,1)=="5"){//ingresos
		if(!$CC[$i]["es_padre"])
			$TOTAL_INGRESOS+=$CuentaContable["total"];
		}
	else if(substr($CC[$i]["id_codigo_contable"],0,1)=="6"){//egresos
		if(!$CC[$i]["es_padre"])
			$TOTAL_EGRESOS+=$CuentaContable["total"];
		}
	}

$UTILIDAD=$TOTAL_INGRESOS-$TOTAL_EGRESOS;
if($UTILIDAD!=0){
	$CAPITAL[$CAPITAL_TAM]["id_codigo_contable"]="";
	$CAPITAL[$CAPITAL_TAM]["denominacion_cta_contable"]="RESULTADOS DEL EJERCICIO";
	$CAPITAL[$CAPITAL_TAM]["total"]=$UTILIDAD;
	$TOTAL_CAPITAL+=$UTILIDAD;
	$CAPITAL_TAM++;
	}







require('../../scripts/fpdf/fpdf.php');


class PDF_P extends FPDF{
	function Header(){
		$this->SetFont('helvetica','',10);
		$this->Image("../../img/logo_fundacite_reporte.png",$this->lMargin+2,$this->tMargin-2,50);
 		$this->Cell(180,10,'Página: '.$this->PageNo().' de {nb}','',1,'R');

		$this->SetY(20);
		$this->SetFont('helvetica','B',12);
		$this->Cell($GLOBALS['tam_ancho'],8,"BALANCE GENERAL",'',1,'C',0);
		$this->SetFont('helvetica','',10);
		$this->Cell($GLOBALS['tam_ancho'],5,"AL ".formatearFecha($GLOBALS['fecha_final']),'',1,'C',0);


		$this->Ln(5);
		$this->SetFont('helvetica','B',$GLOBALS['font_size_base']);
		$this->SetFillColor(200,200,200);
		$this->Cell($GLOBALS['tam_codigo'],5,"CUENTA",'LTB',0,'C',1);
		$this->Cell($GLOBALS['tam_denominacion'],5,"DENOMINACIÓN",'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,"",'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,"",'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,"",'TB',0,'C',1);
		$this->Cell($GLOBALS['tam_monto'],5,"",'RTB',1,'C',1);
		$this->SetFont('helvetica','',$GLOBALS['font_size_base']);
		$this->SetFillColor(255,255,255);
		}
	function Footer(){
		$this->Cell($GLOBALS['tam_ancho'],1,"",'T',1,'C',1);
		}
	}

function FormatearPasivo($str){
	if(!$str) return "";
	if($str>=0)			return "(".number_format($str,2,",",".").")";
	else				return number_format($str*-1,2,",",".");
	}

function EscribirFila($pdf,$col1,$col2,$col3,$col4,$col5,$col6){
	$Y=$pdf->GetY();
	if($Y>250){
		$pdf->AddPage();
		$Y=$pdf->GetY();
		}
	$pdf->Cell($GLOBALS["tam_codigo"],5,$col1,'',0,'C',1);
	//$pdf->MultiCell($GLOBALS["tam_denominacion"],5,$col2,'','',0);

	$pdf->Cell($GLOBALS["tam_denominacion"],5,'','',0,'L',0);

	//$Y2=$pdf->GetY();
	//$pdf->SetXY($pdf->lMargin+$GLOBALS["tam_codigo"]+$GLOBALS["tam_denominacion"],$Y);
	$pdf->Cell($GLOBALS["tam_monto"],5,$col3,'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],5,$col4,'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],5,$col5,'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],5,$col6,'',1,'R',0);

//$Y2=$pdf->GetY();	
$pdf->SetXY($pdf->lMargin+$GLOBALS["tam_codigo"],$Y);
$pdf->MultiCell($GLOBALS["tam_denominacion"],5,$col2,'','',0);
$Y2=$pdf->GetY();

 	//$pdf->SetY($Y2);
 	//dibujar lineas verticales
  	$pdf->Line($pdf->lMargin,$Y,$pdf->lMargin,$Y2);
  	$X=$pdf->lMargin+$GLOBALS["tam_codigo"]+$GLOBALS["tam_denominacion"]+$GLOBALS["tam_monto"]*4;
  	$pdf->Line($X,$Y,$X,$Y2);

	}



$pdf=new PDF_P("P","mm","letter");
$tam_ancho=190;
$tam_codigo=23;
$tam_monto=20;
$tam_denominacion=$tam_ancho-($tam_codigo+$tam_monto*4);
$MARGEN_LEFT=13;
$MARGEN_TOP=10;

$font_size_base=9;
$font_size_base2=8;
$alto_fila_base=4;$alto_fila_base2=5;

$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);
$pdf->SetLineWidth(0.2);
$pdf->SetFillColor(255,255,255);
$pdf->SetAutoPageBreak(false);

$pdf->AddPage();

//ACTIVOS
$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'LT',0,'C',1);
$pdf->Cell($tam_denominacion,5,"ACTIVOS",'T',0,'L',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'RT',1,'C',1);
$pdf->SetFont('helvetica','',$font_size_base);

$es_padre_anterior=1;
for($i=0;$i<$ACTIVO_TAM;$i++){
    
    
    /*if($ocultar){	
	if($MONTO==0)
    continue;}*/ 
    
    
	if(!$ACTIVO[$i]["es_padre"]){
		$MONTO=number_format($ACTIVO[$i]["total"],2,",",".");
		$pdf->SetFont('helvetica','',$font_size_base2);
		}
	else{
		$MONTO="";
		$pdf->SetFont('helvetica','B',$font_size_base2);
		}
	if(!$es_padre_anterior and $ACTIVO[$i]["es_padre"]){
		if($ACTIVO[$i-1]["id_codigo_contable"][2]!=$ACTIVO[$i]["id_codigo_contable"][2]){
			//buscar xxx000000000
			$cabeza_cuenta=substr($ACTIVO[$i-1]["id_codigo_contable"],0,3);
			$cuenta_mostrar=$cabeza_cuenta."000000000";
			for($j=0;$j<$ACTIVO_TAM;$j++)
				if($cuenta_mostrar==$ACTIVO[$j]["id_codigo_contable"])
					break;
			EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'',number_format($ACTIVO[$j]["total"],2,",","."),'','');
			}
		if($ACTIVO[$i-1]["id_codigo_contable"][1]!=$ACTIVO[$i]["id_codigo_contable"][1]){
			//buscar xx0000000000
			$cabeza_cuenta=substr($ACTIVO[$i-1]["id_codigo_contable"],0,2);
			$cuenta_mostrar=$cabeza_cuenta."0000000000";
			for($j=0;$j<$ACTIVO_TAM;$j++)
				if($cuenta_mostrar==$ACTIVO[$j]["id_codigo_contable"])
					break;
			EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'','',number_format($ACTIVO[$j]["total"],2,",","."),'');
			}
		}
	if($ocultar){	
	if(($MONTO)=="") 	
	EscribirFila($pdf,$ACTIVO[$i]["id_codigo_contable"],$ACTIVO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$ACTIVO[$i]["es_padre"];}
	
	if($ocultar){	
	if(($MONTO)==0)
	continue;}
	EscribirFila($pdf,$ACTIVO[$i]["id_codigo_contable"],$ACTIVO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$ACTIVO[$i]["es_padre"];
	
	
	}




//buscar xxx000000000
$cabeza_cuenta=substr($ACTIVO[$i-1]["id_codigo_contable"],0,3);
$cuenta_mostrar=$cabeza_cuenta."000000000";
for($j=0;$j<$ACTIVO_TAM;$j++)



	if($cuenta_mostrar==$ACTIVO[$j]["id_codigo_contable"])
		break;
$pdf->SetFont('helvetica','B',$font_size_base2);
$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,number_format($ACTIVO[$j]["total"],2,",","."),'',0,'R',1);
$pdf->Cell($tam_monto,5,"",'',0,'R',1);
$pdf->Cell($tam_monto,5,'','R',1,'R',1);



//buscar xx0000000000
$cabeza_cuenta=substr($ACTIVO[$i-1]["id_codigo_contable"],0,2);
$cuenta_mostrar=$cabeza_cuenta."0000000000";
for($j=0;$j<$ACTIVO_TAM;$j++)

	if($cuenta_mostrar==$ACTIVO[$j]["id_codigo_contable"])
		break;

$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,number_format($ACTIVO[$j]["total"],2,",","."),'',0,'R',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);



$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ACTIVOS",'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,"",'',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,number_format($TOTAL_ACTIVO,2,",","."),'R',1,'R',1);



//PASIVOS
$pdf->Cell($tam_ancho,3,"",'LR',1,'C',1);

$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"PASIVOS",'',0,'L',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'R',1,'C',1);
$pdf->SetFont('helvetica','',$font_size_base);

$es_padre_anterior=1;
for($i=0;$i<$PASIVO_TAM;$i++){
	if(!$PASIVO[$i]["es_padre"]){
		$MONTO=FormatearPasivo($PASIVO[$i]["total"]);
		$pdf->SetFont('helvetica','',$font_size_base2);
		}
	else{
		$MONTO="";
		$pdf->SetFont('helvetica','B',$font_size_base2);
		}
	if(!$es_padre_anterior and $PASIVO[$i]["es_padre"]){
		if($PASIVO[$i-1]["id_codigo_contable"][2]!=$PASIVO[$i]["id_codigo_contable"][2]){
			//buscar xxx000000000
			$cabeza_cuenta=substr($PASIVO[$i-1]["id_codigo_contable"],0,3);
			$cuenta_mostrar=$cabeza_cuenta."000000000";
			for($j=0;$j<$PASIVO_TAM;$j++)
				if($cuenta_mostrar==$PASIVO[$j]["id_codigo_contable"])
					break;
			EscribirFila($pdf,'',"TOTAL ".$PASIVO[$j]["denominacion_cta_contable"],'',FormatearPasivo($PASIVO[$j]["total"]),'','');
			}
		if($PASIVO[$i-1]["id_codigo_contable"][1]!=$PASIVO[$i]["id_codigo_contable"][1]){
			//buscar xx0000000000

			$cabeza_cuenta=substr($PASIVO[$i-1]["id_codigo_contable"],0,2);
			$cuenta_mostrar=$cabeza_cuenta."0000000000";
			for($j=0;$j<$PASIVO_TAM;$j++)
				if($cuenta_mostrar==$PASIVO[$j]["id_codigo_contable"])
 					break;
			EscribirFila($pdf,'',"TOTAL ".$PASIVO[$j]["denominacion_cta_contable"],'','',FormatearPasivo($PASIVO[$j]["total"]),'');
			}
		}
	       
	if($ocultar){	
	if(($MONTO)=="" and !$es_padre_anterior )	    
	continue;}
    
	EscribirFila($pdf,$PASIVO[$i]["id_codigo_contable"],$PASIVO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$PASIVO[$i]["es_padre"];
	
	/*if($ocultar){	
	if(($MONTO)==0)	    
	continue;}
    
	
	EscribirFila($pdf,$PASIVO[$i]["id_codigo_contable"],$PASIVO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$PASIVO[$i]["es_padre"];*/
	
	}

//buscar xxx000000000
$cabeza_cuenta=substr($PASIVO[$i-1]["id_codigo_contable"],0,3);
$cuenta_mostrar=$cabeza_cuenta."000000000";
for($j=0;$j<$PASIVO_TAM;$j++)
	if($cuenta_mostrar==$PASIVO[$j]["id_codigo_contable"])
		break;


$pdf->SetFont('helvetica','B',$font_size_base2);
$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ".$PASIVO[$j]["denominacion_cta_contable"],'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,FormatearPasivo($PASIVO[$j]["total"]),'',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);


//buscar xx0000000000
$cabeza_cuenta=substr($PASIVO[$i-1]["id_codigo_contable"],0,2);
$cuenta_mostrar=$cabeza_cuenta."0000000000";
for($j=0;$j<$PASIVO_TAM;$j++)
	if($cuenta_mostrar==$PASIVO[$j]["id_codigo_contable"])
		break;

$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ".$PASIVO[$j]["denominacion_cta_contable"],'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,FormatearPasivo($PASIVO[$j]["total"]),'',0,'R',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);




$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL PASIVOS",'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,FormatearPasivo($TOTAL_PASIVO),'R',1,'R',1);























//CAPITAL
$pdf->Cell($tam_ancho,3,"",'LR',1,'C',1);

$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"PATRIMINIO",'',0,'L',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'R',1,'C',1);
$pdf->SetFont('helvetica','',$font_size_base);

$es_padre_anterior=1;
for($i=0;$i<$CAPITAL_TAM;$i++){
	if(!$CAPITAL[$i]["es_padre"]){
		$MONTO=FormatearPasivo($CAPITAL[$i]["total"]);
		$pdf->SetFont('helvetica','',$font_size_base2);
		}
	else{
		$MONTO="";
		$pdf->SetFont('helvetica','B',$font_size_base2);
		}
	if(!$es_padre_anterior and $CAPITAL[$i]["es_padre"]){
		if($CAPITAL[$i-1]["id_codigo_contable"][2]!=$CAPITAL[$i]["id_codigo_contable"][2]){
			//buscar xxx000000000
			$cabeza_cuenta=substr($CAPITAL[$i-1]["id_codigo_contable"],0,3);
			$cuenta_mostrar=$cabeza_cuenta."000000000";
			for($j=0;$j<$CAPITAL_TAM;$j++)
				if($cuenta_mostrar==$CAPITAL[$j]["id_codigo_contable"])
					break;
			EscribirFila($pdf,'',"TOTAL ".$CAPITAL[$j]["denominacion_cta_contable"],'',FormatearPasivo($CAPITAL[$j]["total"]),'','');
			}
		if($CAPITAL[$i-1]["id_codigo_contable"][1]!=$CAPITAL[$i]["id_codigo_contable"][1]){
			//buscar xxx000000000
			$cabeza_cuenta=substr($CAPITAL[$i-1]["id_codigo_contable"],0,2);
			$cuenta_mostrar=$cabeza_cuenta."0000000000";
			for($j=0;$j<$CAPITAL_TAM;$j++)
				if($cuenta_mostrar==$CAPITAL[$j]["id_codigo_contable"])
					break;
			EscribirFila($pdf,'',"TOTAL ".$CAPITAL[$j]["denominacion_cta_contable"],'','',FormatearPasivo($CAPITAL[$j]["total"]),'');
			}
		}
	EscribirFila($pdf,$CAPITAL[$i]["id_codigo_contable"],$CAPITAL[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$CAPITAL[$i]["es_padre"];
	}



//buscar xxx000000000
$cabeza_cuenta=substr($CAPITAL[$i-1]["id_codigo_contable"],0,3);
$cuenta_mostrar=$cabeza_cuenta."000000000";
for($j=0;$j<$CAPITAL_TAM;$j++)
	if($cuenta_mostrar==$CAPITAL[$j]["id_codigo_contable"])
		break;


$pdf->SetFont('helvetica','B',$font_size_base2);
$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ".$CAPITAL[$j]["denominacion_cta_contable"],'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,FormatearPasivo($CAPITAL[$j]["total"]),'',0,'C',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);




//buscar xx0000000000
$cabeza_cuenta=substr($CAPITAL[$i-1]["id_codigo_contable"],0,2);
$cuenta_mostrar=$cabeza_cuenta."0000000000";
for($j=0;$j<$CAPITAL_TAM;$j++)
	if($cuenta_mostrar==$CAPITAL[$j]["id_codigo_contable"])
		break;

$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ".$CAPITAL[$j]["denominacion_cta_contable"],'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,FormatearPasivo($CAPITAL[$j]["total"]),'',0,'R',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);






$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL PATRIMINIO",'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,FormatearPasivo($TOTAL_CAPITAL),'R',1,'R',1);









//total pasivo + capital
$pdf->Cell($tam_ancho,3,"",'LR',1,'C',1);
$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'LB',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL PASIVO MAS PATRIMONIO",'B',0,'',1);
$pdf->Cell($tam_monto,5,'','B',0,'',1);
$pdf->Cell($tam_monto,5,'','B',0,'R',1);
$pdf->Cell($tam_monto,5,'','B',0,'R',1);
$pdf->Cell($tam_monto,5,number_format($TOTAL_PASIVO+$TOTAL_CAPITAL,2,",","."),'RB',1,'R',1);













$pdf->AliasNbPages();
$pdf->Output("balance_general.pdf","I");

?>

