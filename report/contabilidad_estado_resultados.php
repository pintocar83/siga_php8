<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/unformatDate.php");

include_once("../library/fpdf/1.7/fpdf.php");

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





$FECHA=date("d/m/Y h:i a",time());


$FECHA_I=unformatDate($_GET["FI"]);
$FECHA_F=unformatDate($_GET["FF"]);
$mostrar_fecha=false;
if(isset($_GET["MF"]))
  $mostrar_fecha=$_GET["MF"];




/*$sql="SELECT
			MC.id_codigo_contable,
			CCC.denominacion_cta_contable,
			SUM(case when MC.tipo_operacion_mc = 'D' then MC.monto_mc else 0 end) AS total_debe,
			SUM(case when MC.tipo_operacion_mc = 'H' then MC.monto_mc else 0 end) AS total_haber
		FROM
			modulo_contabilidad.movimiento_contable AS MC,
			modulo_presupuesto.comprobante AS C,
			modulo_catalogo_ctas.catalogo_ctas_contable AS CCC
		WHERE
			C.fecha_c BETWEEN '$FECHA_I' AND '$FECHA_F' AND
			C.eliminado=false AND
			MC.id_comprobante=C.id_comprobante AND
			NOT C.acronimo_c ILIKE 'SCOAAP' AND
			MC.id_codigo_contable=CCC.id_codigo_contable AND
			(MC.id_codigo_contable LIKE '5%' OR MC.id_codigo_contable LIKE '6%') AND
			--NOT MC.id_codigo_contable LIKE '614%' AND
			es_acronimo_contable(C.acronimo_c)
		GROUP BY
			MC.id_codigo_contable,
			CCC.denominacion_cta_contable
		ORDER BY
			MC.id_codigo_contable";*/
      
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
		  C.fecha BETWEEN '$FECHA_I' AND '$FECHA_F' AND
			C.contabilizado AND
			C.id=DC.id_comprobante AND
			DC.id_cuenta_contable=CC.id_cuenta_contable AND
			(CC.id_cuenta_contable like '5%' OR CC.id_cuenta_contable like '6%')
		GROUP BY
			DC.id_cuenta_contable,
			CC.denominacion
		ORDER BY
			DC.id_cuenta_contable";

$CC=$db->Execute($sql);
if($CC==0) return;
$N=count($CC);

//rint_r($CC);exit;

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
			
			
				//$CC[$i]["total_debe"]+=$CC[$j]["total_debe"];
				//$CC[$i]["total_haber"]+=$CC[$j]["total_haber"];
				}
$INGRESO=array();
$EGRESO=array();
$INGRESO_TAM=0;
$TOTAL_INGRESO=0;
$EGRESO_TAM=0;
$TOTAL_EGRESO=0;

for($i=0;$i<count($CC);$i++){
	$CuentaContable["id_codigo_contable"]=$CC[$i]["id_codigo_contable"];
	$CuentaContable["denominacion_cta_contable"]=$CC[$i]["denominacion_cta_contable"];
	$CuentaContable["es_padre"]=$CC[$i]["es_padre"];
	
	//buscar saldo inicial para la cuenta actual
	//$sql="SELECT SUM(MC.monto_mc) AS monto_mc
	//		FROM modulo_contabilidad.movimiento_contable AS MC,	modulo_presupuesto.comprobante AS C
	//		WHERE
	//			MC.id_comprobante=C.id_comprobante AND
	//			MC.id_codigo_contable='".$CuentaContable["id_codigo_contable"]."' AND
	//			C.acronimo_c ILIKE 'SCOAAP'";
  
  $SALDO_INICIAL=$db->Execute("select
                                              sum(DC.monto) as monto_mc
                                            from
                                              modulo_base.comprobante as C,
                                              modulo_base.detalle_contable as DC
                                            where
																							C.contabilizado AND
                                              C.id=DC.id_comprobante AND
                                              EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                                              C.tipo='AC' AND
                                              DC.id_cuenta_contable='".$CuentaContable["id_codigo_contable"]."'
                                            ");

  //print_r($CuentaContable["id_codigo_contable"]);
  //print_r($SALDO_INICIAL);
	//$SALDO_INICIAL=$bd->consultar($sql,'ARREGLO');
	$CC[$i]["saldo_inicial"]=$SALDO_INICIAL[0]['monto_mc'];
//$CC[$i]["AumentaCuentaContable"]=AumentaCuentaContable($CC[$i]["id_codigo_contable"])?"true":"false";
	if(AumentaCuentaContable($CC[$i]["id_codigo_contable"])){
		$CuentaContable["total"]=$SALDO_INICIAL[0]['monto_mc']+($CC[$i]["total_debe"]-$CC[$i]["total_haber"]);
		
		}
	else{
		$CuentaContable["total"]=$SALDO_INICIAL[0]['monto_mc']+($CC[$i]["total_haber"]-$CC[$i]["total_debe"]);
		//$CC[$i]["total2"]=$SALDO_INICIAL[0]['monto_mc']-($CC[$i]["total_haber"]-$CC[$i]["total_debe"]);
		}
	$CC[$i]["total"]=$CuentaContable["total"];

	/*if(substr($CC[$i]["id_codigo_contable"],0,1)=="5"){//ingresos
		$INGRESO[$INGRESO_TAM]=$CuentaContable;
		if(!$CC[$i]["es_padre"])
			$TOTAL_INGRESO+=$CuentaContable["total"];
		$INGRESO_TAM++;
		}
	else if(substr($CC[$i]["id_codigo_contable"],0,1)=="6"){//egresos
		$EGRESO[$EGRESO_TAM]=$CuentaContable;
		if(!$CC[$i]["es_padre"])
			$TOTAL_EGRESO+=$CuentaContable["total"];
		$EGRESO_TAM++;
		}*/
	}

  
for($i=0;$i<$N;$i++)
	if($CC[$i]["es_padre"])
		for($j=$i+1;$j<$N;$j++)
			if(EsPadreHijo($CC[$i]["id_codigo_contable"],$CC[$j]["id_codigo_contable"])){
			
				$CC[$i]["total"]+=$CC[$j]["total"];
				//$CC[$i]["total_debe"]+=$CC[$j]["total_debe"];
				//$CC[$i]["total_haber"]+=$CC[$j]["total_haber"];
				}
for($i=0;$i<count($CC);$i++){
	$CuentaContable["id_codigo_contable"]=$CC[$i]["id_codigo_contable"];
	$CuentaContable["denominacion_cta_contable"]=$CC[$i]["denominacion_cta_contable"];
	$CuentaContable["es_padre"]=$CC[$i]["es_padre"];	
	$CuentaContable["total"]=$CC[$i]["total"];
	
	if(substr($CC[$i]["id_codigo_contable"],0,1)=="5"){//ingresos
		$INGRESO[$INGRESO_TAM]=$CuentaContable;
		if(!$CC[$i]["es_padre"])
			$TOTAL_INGRESO+=$CuentaContable["total"];
		$INGRESO_TAM++;
		}
	else if(substr($CC[$i]["id_codigo_contable"],0,1)=="6"){//egresos
		$EGRESO[$EGRESO_TAM]=$CuentaContable;
		if(!$CC[$i]["es_padre"])
			$TOTAL_EGRESO+=$CuentaContable["total"];
		$EGRESO_TAM++;
		}
	}

class PDF_P extends FPDF{
	function Header(){
		global $organismo;
		$this->SetFont('helvetica','',10);
		
		if(file_exists(SIGA::databasePath()."/config/cintillo_actual.jpg"))
			$this->Image(SIGA::databasePath()."/config/cintillo_actual.jpg",$GLOBALS['MARGEN_LEFT'],$GLOBALS['MARGEN_TOP'],$GLOBALS['tam_ancho']);
		else if(file_exists(SIGA::databasePath()."/config/logo_01.jpg"))
			$this->Image(SIGA::databasePath()."/config/logo_01.jpg",$GLOBALS['MARGEN_LEFT'],$GLOBALS['MARGEN_TOP']-8,40);

		if ($GLOBALS['mostrar_fecha']=="true")
		{
			$this->Cell(155,4,'','',0,'R',0);$this->Cell(35,4,"Fecha: ".date("d/m/Y"),'',1,'L',0);
		}
 		//$this->Cell(155,10,'','',0,'R',0);$this->Cell(35,10,'Página: '.$this->PageNo().' de {nb}','',1,'L');

		$this->SetY(25);
		$this->SetFont('helvetica','B',12);
		$this->Cell($GLOBALS['tam_ancho'],8,utf8_decode("ESTADO DE RESULTADOS"),'',1,'C',0);
		$this->SetFont('helvetica','',10);
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("DEL ".formatDate($GLOBALS['FECHA_I'])." AL ".formatDate($GLOBALS['FECHA_F'])),'',1,'C',0);


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

function EscribirFila($pdf,$col1,$col2,$col3,$col4,$col5,$col6){
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
	$pdf->SetXY($pdf->lMargin+$GLOBALS["tam_codigo"],$Y);
	$pdf->MultiCell($GLOBALS["tam_denominacion"],5,utf8_decode($col2),'','',0);
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

//INGRESOS
$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'LT',0,'C',1);
$pdf->Cell($tam_denominacion,5,"INGRESOS",'T',0,'L',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'T',0,'C',1);
$pdf->Cell($tam_monto,5,"",'RT',1,'C',1);
$pdf->SetFont('helvetica','',$font_size_base);

$es_padre_anterior=1;
for($i=0;$i<$INGRESO_TAM;$i++){
	if(!$INGRESO[$i]["es_padre"]){
		$MONTO=number_format($INGRESO[$i]["total"],2,",",".");
		$pdf->SetFont('helvetica','',$font_size_base2);
		}
	else{
		$MONTO="";
		$pdf->SetFont('helvetica','B',$font_size_base2);
		}
	if(!$es_padre_anterior and $INGRESO[$i]["es_padre"]){
		if($INGRESO[$i-1]["id_codigo_contable"][2]!=$INGRESO[$i]["id_codigo_contable"][2]){
			//buscar xxx000000000
			$cabeza_cuenta=substr($INGRESO[$i-1]["id_codigo_contable"],0,3);
			$cuenta_mostrar=$cabeza_cuenta."000000000";
			for($j=0;$j<$INGRESO_TAM;$j++)
				if($cuenta_mostrar==$INGRESO[$j]["id_codigo_contable"])
					break;
			EscribirFila($pdf,'',"TOTAL ".$INGRESO[$j]["denominacion_cta_contable"],'',number_format($INGRESO[$j]["total"],2,",","."),'','');
			}
		if($INGRESO[$i-1]["id_codigo_contable"][1]!=$INGRESO[$i]["id_codigo_contable"][1]){
			//buscar xx0000000000
			$cabeza_cuenta=substr($INGRESO[$i-1]["id_codigo_contable"],0,2);
			$cuenta_mostrar=$cabeza_cuenta."0000000000";
			for($j=0;$j<$INGRESO_TAM;$j++)
				if($cuenta_mostrar==$INGRESO[$j]["id_codigo_contable"])
					break;
			EscribirFila($pdf,'',"TOTAL ".$INGRESO[$j]["denominacion_cta_contable"],'','',number_format($INGRESO[$j]["total"],2,",","."),'');
			}
		}
	if($INGRESO[$i]["es_padre"] and substr($INGRESO[$i]["id_codigo_contable"],-10)!="0000000000" and substr($INGRESO[$i]["id_codigo_contable"],-9)!="000000000"){
		for($j=0;$j<$INGRESO_TAM;$j++)
			if($INGRESO[$i]["id_codigo_contable"]==$INGRESO[$j]["id_codigo_contable"])
				break;
		$MONTO=number_format($INGRESO[$j]["total"],2,",",".");
	}
	EscribirFila($pdf,$INGRESO[$i]["id_codigo_contable"],$INGRESO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$INGRESO[$i]["es_padre"];
	}

//buscar xxx000000000
if($INGRESO_TAM>0):
	$cabeza_cuenta=substr($INGRESO[$i-1]["id_codigo_contable"],0,3);
	$cuenta_mostrar=$cabeza_cuenta."000000000";
	for($j=0;$j<$INGRESO_TAM;$j++)
		if($cuenta_mostrar==$INGRESO[$j]["id_codigo_contable"])
			break;
	$pdf->SetFont('helvetica','B',$font_size_base2);
	$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
	$pdf->Cell($tam_denominacion,5,"TOTAL ".$INGRESO[$j]["denominacion_cta_contable"],'',0,'',1);
	$pdf->Cell($tam_monto,5,'','',0,'',1);
	$pdf->Cell($tam_monto,5,number_format($INGRESO[$j]["total"],2,",","."),'',0,'R',1);
	$pdf->Cell($tam_monto,5,"",'',0,'R',1);
	$pdf->Cell($tam_monto,5,'','R',1,'R',1);
	
	//buscar xx0000000000
	$cabeza_cuenta=substr($INGRESO[$i-1]["id_codigo_contable"],0,2);
	$cuenta_mostrar=$cabeza_cuenta."0000000000";
	for($j=0;$j<$INGRESO_TAM;$j++)
		if($cuenta_mostrar==$INGRESO[$j]["id_codigo_contable"])
			break;
	
	$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
	$pdf->Cell($tam_denominacion,5,"TOTAL ".$INGRESO[$j]["denominacion_cta_contable"],'',0,'',1);
	$pdf->Cell($tam_monto,5,'','',0,'',1);
	$pdf->Cell($tam_monto,5,'','',0,'R',1);
	$pdf->Cell($tam_monto,5,number_format($INGRESO[$j]["total"],2,",","."),'',0,'R',1);
	$pdf->Cell($tam_monto,5,"",'R',1,'R',1);
endif;
$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL INGRESOS",'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,"",'',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,number_format($TOTAL_INGRESO,2,",","."),'R',1,'R',1);

//EGRESOS
$pdf->Cell($tam_ancho,3,"",'LR',1,'C',1);

$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"EGRESOS",'',0,'L',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'',0,'C',1);
$pdf->Cell($tam_monto,5,"",'R',1,'C',1);
$pdf->SetFont('helvetica','',$font_size_base);

$es_padre_anterior=1;
for($i=0;$i<$EGRESO_TAM;$i++){
	if(!$EGRESO[$i]["es_padre"]){
		$MONTO=number_format($EGRESO[$i]["total"],2,",",".");
		$pdf->SetFont('helvetica','',$font_size_base2);
		}
	else{
		$MONTO="";
		$pdf->SetFont('helvetica','B',$font_size_base2);
		}
	if(!$es_padre_anterior and $EGRESO[$i]["es_padre"]){
		if($EGRESO[$i-1]["id_codigo_contable"][2]!=$EGRESO[$i]["id_codigo_contable"][2]){
			//buscar xxx000000000
			$cabeza_cuenta=substr($EGRESO[$i-1]["id_codigo_contable"],0,3);
			$cuenta_mostrar=$cabeza_cuenta."000000000";
			for($j=0;$j<$EGRESO_TAM;$j++)
				if($cuenta_mostrar==$EGRESO[$j]["id_codigo_contable"])
					break;
			//EscribirFila($pdf,'',"**"."TOTAL ".$EGRESO[$j]["denominacion_cta_contable"],'',number_format($EGRESO[$j]["total"],2,",","."),'','');
			}
		if($EGRESO[$i-1]["id_codigo_contable"][1]!=$EGRESO[$i]["id_codigo_contable"][1]){
			//buscar xx0000000000

			$cabeza_cuenta=substr($EGRESO[$i-1]["id_codigo_contable"],0,2);
			$cuenta_mostrar=$cabeza_cuenta."0000000000";
			for($j=0;$j<$EGRESO_TAM;$j++)
				if($cuenta_mostrar==$EGRESO[$j]["id_codigo_contable"])
 					break;
			EscribirFila($pdf,'',""."TOTAL ".$EGRESO[$j]["denominacion_cta_contable"],'','',number_format($EGRESO[$j]["total"],2,",","."),'');
			}
		}
	if($EGRESO[$i]["es_padre"] and substr($EGRESO[$i]["id_codigo_contable"],-10)!="0000000000" and substr($EGRESO[$i]["id_codigo_contable"],-9)!="000000000"){
		for($j=0;$j<$EGRESO_TAM;$j++)
			if($EGRESO[$i]["id_codigo_contable"]==$EGRESO[$j]["id_codigo_contable"])
				break;
		$MONTO=number_format($EGRESO[$j]["total"],2,",",".");
	}
	EscribirFila($pdf,"".$EGRESO[$i]["id_codigo_contable"],$EGRESO[$i]["denominacion_cta_contable"],$MONTO,'','','');
	$es_padre_anterior=$EGRESO[$i]["es_padre"];
	}

//buscar xxx000000000
$cabeza_cuenta=substr($EGRESO[$i-1]["id_codigo_contable"],0,3);
$cuenta_mostrar=$cabeza_cuenta."000000000";
for($j=0;$j<$EGRESO_TAM;$j++)
	if($cuenta_mostrar==$EGRESO[$j]["id_codigo_contable"])
		break;

$pdf->SetFont('helvetica','B',$font_size_base2);

/*
$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ".$EGRESO[$j]["denominacion_cta_contable"],'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,number_format($EGRESO[$j]["total"],2,",","."),'',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);*/

//buscar xx0000000000
$cabeza_cuenta=substr($EGRESO[$i-1]["id_codigo_contable"],0,2);
$cuenta_mostrar=$cabeza_cuenta."0000000000";
for($j=0;$j<$EGRESO_TAM;$j++)
	if($cuenta_mostrar==$EGRESO[$j]["id_codigo_contable"])
		break;

$pdf->Cell($tam_codigo,5,'','L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL ".$EGRESO[$j]["denominacion_cta_contable"],'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,number_format($EGRESO[$j]["total"],2,",","."),'',0,'R',1);
$pdf->Cell($tam_monto,5,"",'R',1,'R',1);

$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'L',0,'C',1);
$pdf->Cell($tam_denominacion,5,"TOTAL EGRESOS",'',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,'','',0,'R',1);
$pdf->Cell($tam_monto,5,number_format($TOTAL_EGRESO,2,",","."),'R',1,'R',1);

//total ingresos-egresos
$pdf->Cell($tam_ancho,3,"",'LR',1,'C',1);
$pdf->SetFont('helvetica','B',$font_size_base);
$pdf->Cell($tam_codigo,5,"",'LB',0,'C',1);
$pdf->Cell($tam_denominacion,5,"RESULTADOS DEL EJERCICIO",'B',0,'',1);
$pdf->Cell($tam_monto,5,'','B',0,'',1);
$pdf->Cell($tam_monto,5,'','B',0,'R',1);
$pdf->Cell($tam_monto,5,'','B',0,'R',1);
$pdf->Cell($tam_monto,5,number_format($TOTAL_INGRESO-$TOTAL_EGRESO,2,",","."),'RB',1,'R',1);

$pdf->AliasNbPages();
$pdf->Output("Estado_Ingresos_Egresos.pdf","I");
?>