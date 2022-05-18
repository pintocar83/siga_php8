<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/unformatDate.php");

include_once("../library/fpdf/1.84/fpdf.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");


function RetornarPadres($cuenta){
	if(!$cuenta)
		return "";
	$a[0]=substr($cuenta,0,3);
	$a[1]=substr($cuenta,3,2);
	$a[2]=substr($cuenta,5,2);
	$a[3]=substr($cuenta,7,2);
	$a[4]=substr($cuenta,9,3);

	if($a[4]=="000"){//xxx.xx.xx.xx.000
		if($a[3]=="00"){//xxx.xx.xx.00.000
			if($a[2]=="00"){//xxx.xx.00.00.000
				if($a[1]=="00"){//xxx.00.00.00.000
					return array(	/*$a[0][0]."0"."0"."00"."00"."00"."000",*/
									$a[0][0].$a[0][1]."0"."00"."00"."00"."000");

					//return "";//no tiene padre, el padre es el mismo
					}
				else{//xxx.xx.00.00.000
					return array(	$a[0]."00"."00"."00"."000",
// 									$a[0][0]."0"."0"."00"."00"."00"."000",
									$a[0][0].$a[0][1]."0"."00"."00"."00"."000");
					}
				}
			else{//xxx.xx.xx.00.000
				return array(	$a[0].$a[1]."00"."00"."000",
								$a[0]."00"."00"."00"."000",
// 								$a[0][0]."0"."0"."00"."00"."00"."000",
								$a[0][0].$a[0][1]."0"."00"."00"."00"."000");
				}
			}
		else{//xxx.xx.xx.xx.000
			return array(	$a[0].$a[1].$a[2]."00"."000",
							$a[0].$a[1]."00"."00"."000",
							$a[0]."00"."00"."00"."000",
// 							$a[0][0]."0"."0"."00"."00"."00"."000",
							$a[0][0].$a[0][1]."0"."00"."00"."00"."000");
			}
		}
	else{//si no es cero de la forma xxx.xx.xx.xx.xxx
		return array(	$a[0].$a[1].$a[2].$a[3]."000",
						$a[0].$a[1].$a[2]."00"."000",
						$a[0].$a[1]."00"."00"."000",
						$a[0]."00"."00"."00"."000",
// 						$a[0][0]."0"."0"."00"."00"."00"."000",
						$a[0][0].$a[0][1]."0"."00"."00"."00"."000");
		}
	}

function EsPadreHijo($padre,$hijo){
	$padres=RetornarPadres($hijo);
	for($k=0;$k<count($padres);$k++)
		if($padre==$padres[$k])
			return true;
	return false;
	}

function AumentaCuentaContable($id_codigo_contable){
	$primer_caracter=substr($id_codigo_contable,0,1);
	switch($primer_caracter){
		case "1":
		case "6":
			return true;
		case "4":
			if(substr($id_codigo_contable,1,1)=="1")//si es 41
				return true;
		}
	return false;
	}




$params=$_GET;

$FF=SIGA::paramGet("FF");
$fecha_final=$FF=unformatDate($FF);



$FECHA=date("d/m/Y h:i a",time());


$TOTAL_ACTIVOS_3N=0;



$ocultar=$_GET["OCULTAR"];

//$anio=LimpiarCadena($_SESSION['BDanio_presupuestario']);
$anio=SIGA::data();
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

//$fecha_final=desformatearFecha($_GET["FF"]);


$cuenta_contable_depreciacion_amortizacion["123010100000"]="225010100000";//DEPRECIACIÓN ACUMULADA DE EDIFICIOS E INSTALACIONES
$cuenta_contable_depreciacion_amortizacion["123010200000"]="225010200000";//DEPRECIACIÓN ACUMULADA DE MAQUINARIA Y DEMÁS EQUIPOS DE CONSTRUCCIÓN, CAMPO, INDUSTRIA Y TALLER
$cuenta_contable_depreciacion_amortizacion["123010300000"]="225010300000";
$cuenta_contable_depreciacion_amortizacion["123010400000"]="225010400000";
$cuenta_contable_depreciacion_amortizacion["123010500000"]="225010500000";
$cuenta_contable_depreciacion_amortizacion["123010600000"]="225010600000";
$cuenta_contable_depreciacion_amortizacion["123010700000"]="225010700000";
$cuenta_contable_depreciacion_amortizacion["123010800000"]="225010800000";
$cuenta_contable_depreciacion_amortizacion["123010900000"]="225010900000";
$cuenta_contable_depreciacion_amortizacion["123011900000"]="225011900000";


$cuenta_contable_depreciacion_amortizacion["124010000000"]="225020100000";//AMORTIZACIÓN ACUMULADA DE MARCAS DE FÁBRICA Y PATENTES DE INVENCIÓN
$cuenta_contable_depreciacion_amortizacion["124020000000"]="225020200000";//AMORTIZACIÓN ACUMULADA DE DERECHOS DE AUTOR
$cuenta_contable_depreciacion_amortizacion["124030000000"]="225020300000";//AMORTIZACIÓN ACUMULADA DE GASTOS DE ORGANIZACIÓN
$cuenta_contable_depreciacion_amortizacion["124040000000"]="225020400000";//AMORTIZACIÓN ACUMULADA DE PAQUETES Y PROGRAMAS DE COMPUTACIÓN
$cuenta_contable_depreciacion_amortizacion["124050000000"]="225020500000";//AMORTIZACIÓN ACUMULADADE ESTUDIOS Y PROYECTOS
$cuenta_contable_depreciacion_amortizacion["124190000000"]="225021900000";//AMORTIZACIÓN ACUMULADA DE OTROS ACTIVOS INTANGIBLES









$sql="SELECT
			DC.id_cuenta_contable as id_codigo_contable,
			CC.denominacion as denominacion_cta_contable,
			SUM(case when DC.operacion = 'D' then DC.monto else 0 end) AS total_debe,
			SUM(case when DC.operacion = 'H' then DC.monto else 0 end) AS total_haber
		FROM
			modulo_base.detalle_contable AS DC,
			modulo_base.comprobante AS C,
			modulo_base.cuenta_contable AS CC
		WHERE
		  C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' AND
			C.contabilizado AND
			EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
			C.id=DC.id_comprobante AND
			DC.id_cuenta_contable=CC.id_cuenta_contable AND
			NOT CC.id_cuenta_contable like '2250%'
		GROUP BY
			DC.id_cuenta_contable,
			CC.denominacion
		ORDER BY
			DC.id_cuenta_contable";
$CC=$db->Execute($sql);
if($CC==0) return;
$N=count($CC);



$n_add=0;
$add=array();
foreach($cuenta_contable_depreciacion_amortizacion as $clave => $valor){
		$sw=0;
	  for($i=0;$i<$N;$i++){
				if($CC[$i]["id_codigo_contable"]==$clave)
						$sw=1;
						//break;
				}
		//sino existe, agregarlo al final del arreglo
		if($sw==0){
				$CC[$N]["id_codigo_contable"]=$clave;
				$CC[$N]["denominacion_cta_contable"]="";
				$sql="select denominacion from modulo_base.cuenta_contable where id_cuenta_contable='$clave'";
			  $AUX=$db->Execute($sql);
			  $CC[$N]["denominacion_cta_contable"]=$AUX[0]["denominacion"];
				$CC[$N]["total_debe"]=0;
				$CC[$N]["total_haber"]=0;
				$N++;
				}
		}




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
			$sql="select denominacion from modulo_base.cuenta_contable where id_cuenta_contable='".$PADRES[$j]."'";
			$AUX=$db->Execute($sql);
			$CC[$N]["denominacion_cta_contable"]=$AUX[0]["denominacion"];
			$CC[$N]["es_padre"]=1;
			$CC[$N]["total_debe"]=0;
			$CC[$N]["total_haber"]=0;
			$N++;
			}
		}
	}



//sort($CC);

function funcion_ordenar($a, $b){
		return strcmp($a["id_codigo_contable"],$b["id_codigo_contable"]);
}


usort($CC, 'funcion_ordenar');


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

//print_r($TOTAL_EGRESOS);exit;

$UTILIDAD=$TOTAL_INGRESOS-$TOTAL_EGRESOS;
if($UTILIDAD!=0){
	$CAPITAL[$CAPITAL_TAM]["id_codigo_contable"]="";
	$CAPITAL[$CAPITAL_TAM]["denominacion_cta_contable"]="RESULTADOS DEL EJERCICIO";
	$CAPITAL[$CAPITAL_TAM]["es_padre"]=1;
	$CAPITAL[$CAPITAL_TAM]["total"]=$UTILIDAD;
	$TOTAL_CAPITAL+=$UTILIDAD;
	$CAPITAL_TAM++;
	}








class PDF_P extends FPDF{
	function Header(){
		global $organismo, $tam_ancho;
		$this->SetFont('helvetica','',10);

		if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
      $this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$GLOBALS['MARGEN_LEFT'],$GLOBALS['MARGEN_TOP']-4,$tam_ancho);
    elseif(SIGA::databasePath()."/config/logo_02.jpg")
      $this->Image(SIGA::databasePath()."/config/logo_02.jpg",$GLOBALS['MARGEN_LEFT'],$GLOBALS['MARGEN_TOP']-4,40);

		$this->SetY(18);
 		$this->Cell(180,10,utf8_decode('Página: '.$this->PageNo().' de {nb}'),'',1,'R');

		$this->SetY(20);
		$this->SetFont('helvetica','B',12);
		$this->Cell($GLOBALS['tam_ancho'],8,utf8_decode("BALANCE GENERAL"),'',1,'C',0);
		$this->SetFont('helvetica','',10);
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("AL ".formatDate($GLOBALS['fecha_final'])),'',1,'C',0);
		$this->SetFont('helvetica','',8);
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("(EN BOLIVARES)"),'',1,'C',0);


		$this->Ln(5);
		$this->SetFont('helvetica','B',$GLOBALS['font_size_base']);
		$this->SetFillColor(200,200,200);
		$this->Cell($GLOBALS['tam_codigo'],5,utf8_decode("CUENTA"),'LTB',0,'C',1);
		$this->Cell($GLOBALS['tam_denominacion'],5,utf8_decode("DENOMINACIÓN"),'TB',0,'C',1);
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

function EscribirFila($pdf,$col1,$col2,$col3,$col4,$col5,$col6,$tab=0){
	$Y=$pdf->GetY();
	if($Y>250){
		$pdf->AddPage();
		$Y=$pdf->GetY();
		}
	$pdf->Cell($GLOBALS["tam_codigo"],5,utf8_decode($col1),'',0,'C',1);
	//$pdf->MultiCell($GLOBALS["tam_denominacion"],5,$col2,'','',0);

	$pdf->Cell($GLOBALS["tam_denominacion"],5,'','',0,'L',0);

	//$Y2=$pdf->GetY();
	//$pdf->SetXY($pdf->lMargin+$GLOBALS["tam_codigo"]+$GLOBALS["tam_denominacion"],$Y);
	$pdf->Cell($GLOBALS["tam_monto"],5,utf8_decode($col3),'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],5,utf8_decode($col4),'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],5,utf8_decode($col5),'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],5,utf8_decode($col6),'',1,'R',0);

//$Y2=$pdf->GetY();
$tab_factor=0;
if($tab==1) $tab_factor=5;

$pdf->SetXY($pdf->lMargin+$GLOBALS["tam_codigo"]+$tab_factor,$Y);
$pdf->MultiCell($GLOBALS["tam_denominacion"],4,utf8_decode($col2),'','',0);
$pdf->Ln(1);
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
$pdf->Cell($tam_denominacion,5,utf8_decode("ACTIVOS"),'T',0,'L',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'RT',1,'C',1);
$pdf->SetFont('helvetica','',$font_size_base);

$TOTAL_D_A=0;
$TOTAL_ACTIVOS_1N=0;
$TOTAL_ACTIVOS_2N=0;
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
		if(isset($ACTIVO[$i-1]["id_codigo_contable"][2]) && isset($ACTIVO[$i]["id_codigo_contable"][2]) && $ACTIVO[$i-1]["id_codigo_contable"][2]!=$ACTIVO[$i]["id_codigo_contable"][2]){
			//buscar xxx000000000
			$cabeza_cuenta=substr($ACTIVO[$i-1]["id_codigo_contable"],0,3);
			$cuenta_mostrar=$cabeza_cuenta."000000000";
			for($j=0;$j<$ACTIVO_TAM;$j++)
				if($cuenta_mostrar==$ACTIVO[$j]["id_codigo_contable"])
					break;

			//EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'',number_format($ACTIVO[$j]["total"],2,",","."),'','');
			//if($cabeza_cuenta=="123000000000"){
				$ACTIVO[$j]["total"]=$ACTIVO[$j]["total"]-$TOTAL_D_A;
				$TOTAL_D_A=0;
				EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'',number_format($ACTIVO[$j]["total"],2,",","."),'','');

				$TOTAL_ACTIVOS_3N+=$ACTIVO[$j]["total"];


			//  }
			}
		if(isset($ACTIVO[$i-1]["id_codigo_contable"][1]) && isset($ACTIVO[$i]["id_codigo_contable"][1]) && $ACTIVO[$i-1]["id_codigo_contable"][1]!=$ACTIVO[$i]["id_codigo_contable"][1]){
			//buscar xx0000000000
			$cabeza_cuenta=substr($ACTIVO[$i-1]["id_codigo_contable"],0,2);
			$cuenta_mostrar=$cabeza_cuenta."0000000000";
			for($j=0;$j<$ACTIVO_TAM;$j++)
				if($cuenta_mostrar==$ACTIVO[$j]["id_codigo_contable"])
					break;
			//EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'','',number_format($ACTIVO[$j]["total"],2,",","."),'');

			EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'','',number_format($TOTAL_ACTIVOS_3N,2,",","."),'');
			$TOTAL_ACTIVOS_2N+=$TOTAL_ACTIVOS_3N;
			$TOTAL_ACTIVOS_3N=0;
			}
		}

		/*
	if($ocultar){
	  if(($MONTO)=="")
	    EscribirFila($pdf,$ACTIVO[$i]["id_codigo_contable"],$ACTIVO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	    $es_padre_anterior=$ACTIVO[$i]["es_padre"];
			}*/
	/*
	if($ocultar){
	  if(($MONTO)==0)
	    continue;
			}

	EscribirFila($pdf,"".$ACTIVO[$i]["id_codigo_contable"],$ACTIVO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$ACTIVO[$i]["es_padre"];
	*/
	$MONTO_DEP_AMO=0;
	$MONTO_NETO=$MONTO;
	$sw_da=false;
	//si existe la cuenta de amortizacion/depreciacion buscar en la bd si existe algo registrado
	if(array_key_exists($ACTIVO[$i]["id_codigo_contable"],$cuenta_contable_depreciacion_amortizacion)){
		$cuenta_d_a=$cuenta_contable_depreciacion_amortizacion[$ACTIVO[$i]["id_codigo_contable"]];
		$sql="SELECT
			DC.id_cuenta_contable as id_codigo_contable,
			CC.denominacion as denominacion_cta_contable,
			SUM(case when DC.operacion = 'D' then DC.monto else 0 end) AS total_debe,
			SUM(case when DC.operacion = 'H' then DC.monto else 0 end) AS total_haber
		FROM
			modulo_base.detalle_contable AS DC,
			modulo_base.comprobante AS C,
			modulo_base.cuenta_contable AS CC
		WHERE
		  C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' AND
			C.contabilizado AND
			EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
			C.id=DC.id_comprobante AND
			DC.id_cuenta_contable=CC.id_cuenta_contable AND
			CC.id_cuenta_contable like '$cuenta_d_a'
		GROUP BY
			DC.id_cuenta_contable,
			CC.denominacion
		ORDER BY
			DC.id_cuenta_contable";
		//$sql="SELECT
		//		MC.id_codigo_contable,
		//		CCC.denominacion_cta_contable,
		//		SUM(case when MC.tipo_operacion_mc = 'D' then MC.monto_mc else 0 end) AS total_debe,
		//		SUM(case when MC.tipo_operacion_mc = 'H' then MC.monto_mc else 0 end) AS total_haber
		//	FROM
		//		modulo_contabilidad.movimiento_contable AS MC,
		//		modulo_presupuesto.comprobante AS C,
		//		modulo_catalogo_ctas.catalogo_ctas_contable AS CCC
		//	WHERE
		//		C.eliminado=false AND
		//		MC.id_comprobante=C.id_comprobante AND
		//		MC.id_codigo_contable=CCC.id_codigo_contable AND
		//		es_acronimo_contable(C.acronimo_c)  AND
		//		C.fecha_c BETWEEN '$anio-01-01' AND '$fecha_final' AND
		//		CCC.id_codigo_contable like '$cuenta_d_a'
		//	GROUP BY
		//		MC.id_codigo_contable,
		//		CCC.denominacion_cta_contable
		//	ORDER BY
		//		MC.id_codigo_contable";
	  $retorno=$db->Execute($sql);

		if(!$retorno){
				$sql="select id_cuenta_contable as id_codigo_contable, denominacion as denominacion_cta_contable from modulo_base.cuenta_contable where id_cuenta_contable like '$cuenta_d_a'";
				//$sql="SELECT
				//		CCC.id_codigo_contable,
				//		CCC.denominacion_cta_contable
				//	FROM
				//		modulo_catalogo_ctas.catalogo_ctas_contable AS CCC
				//	WHERE
				//		CCC.id_codigo_contable like '$cuenta_d_a'
				//	";
	  $retorno=$db->Execute($sql);
		$retorno[0]["total_debe"]=0;
		$retorno[0]["total_haber"]=0;
		}

		if(AumentaCuentaContable($retorno[0]["id_codigo_contable"]))
				$retorno[0]["total"]=$retorno[0]["total_debe"]-$retorno[0]["total_haber"];
		else
				$retorno[0]["total"]=$retorno[0]["total_haber"]-$retorno[0]["total_debe"];
		$sw_da=true;
		$MONTO_DEP_AMO=$retorno[0]["total"];
		$MONTO_NETO=$ACTIVO[$i]["total"]-$retorno[0]["total"];
		$TOTAL_D_A+=$retorno[0]["total"];


	  }


	if($ocultar){
	  if($MONTO_NETO=="0,00")
	    continue;
			}

	EscribirFila($pdf,"".$ACTIVO[$i]["id_codigo_contable"],$ACTIVO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$ACTIVO[$i]["es_padre"];

	if($sw_da==true){
		//$pdf->SetFont('helvetica','',7);
		//mostrar la fila depreciacion/amortizacion corresponciente a la cuenta
		EscribirFila($pdf,$cuenta_contable_depreciacion_amortizacion[$ACTIVO[$i]["id_codigo_contable"]],"MENOS: ".$retorno[0]["denominacion_cta_contable"],FormatearPasivo($MONTO_DEP_AMO),'','','');
		$pdf->SetFont('helvetica','I',$font_size_base2);
		//mostrar el neto
		EscribirFila($pdf,"","".$ACTIVO[$i]["denominacion_cta_contable"]." NETO",'',number_format($MONTO_NETO,2,',','.'),'','',1);


		}




	}




//buscar xxx000000000
$cabeza_cuenta=substr($ACTIVO[$i-1]["id_codigo_contable"],0,3);
$cuenta_mostrar=$cabeza_cuenta."000000000";
for($j=0;$j<$ACTIVO_TAM;$j++)



	if($cuenta_mostrar==$ACTIVO[$j]["id_codigo_contable"])
		break;

$pdf->SetFont('helvetica','B',$font_size_base2);
//$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
//$pdf->Cell($tam_denominacion,5,"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'',0,'',1);
//$pdf->Cell($tam_monto,5,'','',0,'',1);
//$pdf->Cell($tam_monto,5,number_format($ACTIVO[$j]["total"],2,",","."),'',0,'R',1);
//$pdf->Cell($tam_monto,5,"",'',0,'R',1);
//$pdf->Cell($tam_monto,5,'','R',1,'R',1);



$ACTIVO[$j]["total"]=$ACTIVO[$j]["total"]-$TOTAL_D_A;
$TOTAL_D_A=0;
EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'',number_format($ACTIVO[$j]["total"],2,",","."),'','');

$TOTAL_ACTIVOS_3N+=$ACTIVO[$j]["total"];










//buscar xx0000000000
$cabeza_cuenta=substr($ACTIVO[$i-1]["id_codigo_contable"],0,2);
$cuenta_mostrar=$cabeza_cuenta."0000000000";
for($j=0;$j<$ACTIVO_TAM;$j++)
	if($cuenta_mostrar==$ACTIVO[$j]["id_codigo_contable"])
		break;

//$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
//$pdf->Cell($tam_denominacion,5,"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'',0,'',1);
//$pdf->Cell($tam_monto,5,'','',0,'',1);
//$pdf->Cell($tam_monto,5,'','',0,'R',1);
//$pdf->Cell($tam_monto,5,number_format($ACTIVO[$j]["total"],2,",","."),'',0,'R',1);
//$pdf->Cell($tam_monto,5,"",'R',1,'R',1);

//EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'','',number_format($ACTIVO[$j]["total"],2,",","."),'');

EscribirFila($pdf,'',"TOTAL ".$ACTIVO[$j]["denominacion_cta_contable"],'','',number_format($TOTAL_ACTIVOS_3N,2,",","."),'');
$TOTAL_ACTIVOS_2N+=$TOTAL_ACTIVOS_3N;
$TOTAL_ACTIVOS_3N=0;


$pdf->SetFont('helvetica','B',$font_size_base);
//$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
//$pdf->Cell($tam_denominacion,5,"TOTAL ACTIVOS",'',0,'',1);
//$pdf->Cell($tam_monto,5,'','',0,'R',1);
//$pdf->Cell($tam_monto,5,"",'',0,'R',1);
//$pdf->Cell($tam_monto,5,'','',0,'R',1);
//$pdf->Cell($tam_monto,5,number_format($TOTAL_ACTIVO,2,",","."),'R',1,'R',1);


EscribirFila($pdf,'',"TOTAL ACTIVOS",'','','',number_format($TOTAL_ACTIVOS_2N,2,",","."));









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
$pdf->Cell($tam_denominacion,5,utf8_decode("TOTAL ".$PASIVO[$j]["denominacion_cta_contable"]),'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,utf8_decode(FormatearPasivo($PASIVO[$j]["total"])),'',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);


//buscar xx0000000000
$cabeza_cuenta=substr($PASIVO[$i-1]["id_codigo_contable"],0,2);
$cuenta_mostrar=$cabeza_cuenta."0000000000";
for($j=0;$j<$PASIVO_TAM;$j++)
	if($cuenta_mostrar==$PASIVO[$j]["id_codigo_contable"])
		break;

$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,utf8_decode("TOTAL ".$PASIVO[$j]["denominacion_cta_contable"]),'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,utf8_decode(FormatearPasivo($PASIVO[$j]["total"])),'',0,'R',1);
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
	if(!$CAPITAL[$i]["es_padre"] or $CAPITAL[$i]["id_codigo_contable"]==""){
		$MONTO=FormatearPasivo($CAPITAL[$i]["total"]);
		$pdf->SetFont('helvetica','',$font_size_base2);
		}
	else{
		$MONTO="";
		$pdf->SetFont('helvetica','B',$font_size_base2);
		}
	if(!$es_padre_anterior and $CAPITAL[$i]["es_padre"]){
		//print $CAPITAL[$i-1]["id_codigo_contable"]."  ".$CAPITAL[$i]["id_codigo_contable"]."<br>";

		if($i-1>=0 and $CAPITAL[$i]["id_codigo_contable"]!="")
		if($CAPITAL[$i-1]["id_codigo_contable"][2]!=$CAPITAL[$i]["id_codigo_contable"][2]){
			//buscar xxx000000000
			$cabeza_cuenta=substr($CAPITAL[$i-1]["id_codigo_contable"],0,3);
			$cuenta_mostrar=$cabeza_cuenta."000000000";
			for($j=0;$j<$CAPITAL_TAM;$j++)
				if($cuenta_mostrar==$CAPITAL[$j]["id_codigo_contable"])
					break;
			EscribirFila($pdf,'',"TOTAL ".$CAPITAL[$j]["denominacion_cta_contable"],'',FormatearPasivo($CAPITAL[$j]["total"]),'','');
			}
		if($i-1>=0 and $CAPITAL[$i]["id_codigo_contable"]!="")
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
$cuenta_mostrar="";
if(isset($CAPITAL[$i-2]["id_codigo_contable"])):
	$cabeza_cuenta=substr($CAPITAL[$i-2]["id_codigo_contable"],0,3);
	$cuenta_mostrar=$cabeza_cuenta."000000000";
endif;
//print $cuenta_mostrar."   $i<br>";
//print_r($CAPITAL);

for($j=0;$j<$CAPITAL_TAM;$j++)
	if($cuenta_mostrar==$CAPITAL[$j]["id_codigo_contable"])
		break;


$pdf->SetFont('helvetica','B',$font_size_base2);
$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,utf8_decode("TOTAL ".$CAPITAL[$j]["denominacion_cta_contable"]),'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,utf8_decode(FormatearPasivo($CAPITAL[$j]["total"]+$UTILIDAD)),'',0,'C',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);



/*
//buscar xx0000000000
$cabeza_cuenta=substr($CAPITAL[$i-2]["id_codigo_contable"],0,2);
$cuenta_mostrar=$cabeza_cuenta."0000000000";
for($j=0;$j<$CAPITAL_TAM;$j++)
	if($cuenta_mostrar==$CAPITAL[$j]["id_codigo_contable"])
		break;

$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,utf8_decode("TOTAL ".$CAPITAL[$j]["denominacion_cta_contable"]),'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,utf8_decode(FormatearPasivo($CAPITAL[$j]["total"])),'',0,'R',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);

*/




$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,utf8_decode("TOTAL PATRIMINIO"),'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,utf8_decode(FormatearPasivo($TOTAL_CAPITAL)),'R',1,'R',1);









//total pasivo + capital
$pdf->Cell($tam_ancho,3,"",'LR',1,'C',1);
$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'LB',0,'C',1);
$pdf->Cell($tam_denominacion,5,utf8_decode("TOTAL PASIVO MAS PATRIMONIO"),'B',0,'',1);
$pdf->Cell($tam_monto,5,'','B',0,'',1);
$pdf->Cell($tam_monto,5,'','B',0,'R',1);
$pdf->Cell($tam_monto,5,'','B',0,'R',1);
$pdf->Cell($tam_monto,5,utf8_decode(number_format($TOTAL_PASIVO+$TOTAL_CAPITAL,2,",",".")),'RB',1,'R',1);













$pdf->AliasNbPages();
$pdf->Output("balance_general.pdf","I");

?>

