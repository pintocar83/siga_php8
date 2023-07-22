<?php
error_reporting(0);
ini_set('display_errors', 'Off');
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/functions/formatDate.php");
include_once("../library/functions/unformatDate.php");

include_once("../library/fpdf/1.84/fpdf.php");

$config_report = SIGA::configuration(["moneda","report/moneda","report/contabilidad/moneda"]);
$config = [
	"moneda" => "BOLIVARES"
];

if($config_report){
	if(isset($config_report["moneda"]) && $config_report["moneda"])	                                          $config["moneda"] = $config_report["moneda"];
	if(isset($config_report["report/moneda"]) && $config_report["report/moneda"])	                            $config["moneda"] = $config_report["report/moneda"];
	if(isset($config_report["report/contabilidad/moneda"]) && $config_report["report/contabilidad/moneda"])	  $config["moneda"] = $config_report["report/contabilidad/moneda"];
}

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
					return array(	$a[0][0]."0"."0"."00"."00"."00"."000",
									$a[0][0].$a[0][1]."0"."00"."00"."00"."000");

					//return "";//no tiene padre, el padre es el mismo
					}
				else{//xxx.xx.00.00.000
					return array(	$a[0]."00"."00"."00"."000",
 									$a[0][0]."0"."0"."00"."00"."00"."000",
									$a[0][0].$a[0][1]."0"."00"."00"."00"."000");
					}
				}
			else{//xxx.xx.xx.00.000
				return array(	$a[0].$a[1]."00"."00"."000",
								$a[0]."00"."00"."00"."000",
 								$a[0][0]."0"."0"."00"."00"."00"."000",
								$a[0][0].$a[0][1]."0"."00"."00"."00"."000");
				}
			}
		else{//xxx.xx.xx.xx.000
			return array(	$a[0].$a[1].$a[2]."00"."000",
							$a[0].$a[1]."00"."00"."000",
							$a[0]."00"."00"."00"."000",
 							$a[0][0]."0"."0"."00"."00"."00"."000",
							$a[0][0].$a[0][1]."0"."00"."00"."00"."000");
			}
		}
	else{//si no es cero de la forma xxx.xx.xx.xx.xxx
		return array(	$a[0].$a[1].$a[2].$a[3]."000",
						$a[0].$a[1].$a[2]."00"."000",
						$a[0].$a[1]."00"."00"."000",
						$a[0]."00"."00"."00"."000",
 						$a[0][0]."0"."0"."00"."00"."00"."000",
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


$anio=SIGA::data();


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


$GENERAL=[];
$GENERAL["325020000000"]=['denominacion'=>'RESULTADOS DEL EJERCICIO', 'padre'=>false, 'monto'=>0];

function AddContable($cuenta, $denominacion, $debe, $haber){
	global $GENERAL, $ocultar, $db;
	$monto=AumentaCuentaContable($cuenta)?($debe-$haber):($haber-$debe);

	if($ocultar==1 and $monto==0){
		return;
	}

	if($cuenta[0]=="5"){
		$cuenta="325020000000";
	}
	if($cuenta[0]=="6"){
		$cuenta="325020000000";
		$monto=-$monto;
	}

	$PADRE=RetornarPadres($cuenta);
	$neto=$monto;

	//buscar si esta presente en amortizacion/depreciacion
	$depreciacion=BuscarDepreciacion($cuenta);
	if($depreciacion!==NULL){
		$neto-=$depreciacion['monto'];
	}


	//agregar a GENERAL padres y sumar el monto
	for($i=0; $i<count($PADRE); $i++){
		$cuenta_padre=$PADRE[$i];
		if(!isset($GENERAL[$cuenta_padre])){
			$tmp=$db->Execute("select denominacion from modulo_base.cuenta_contable where id_cuenta_contable='$cuenta_padre'");
			$denominacion_padre=isset($tmp[0]['denominacion'])?$tmp[0]['denominacion']:'';
			$GENERAL["$cuenta_padre"]=["denominacion"=>$denominacion_padre, 'padre'=>true, 'monto'=>$neto];
		}
		else{
			$GENERAL["$cuenta_padre"]["monto"]+=$neto;
		}
	}

	//agregar la cta original
	if(isset($GENERAL["$cuenta"])){
		$GENERAL["$cuenta"]["monto"]+=$monto;
	}
	else{
		$GENERAL["$cuenta"]=["denominacion"=>$denominacion, 'padre'=>false, 'monto'=>$monto, 'depreciacion'=>$depreciacion];
	}

	//print_r($GENERAL);
}

function BuscarDepreciacion($cuenta){
	global $CC, $cuenta_contable_depreciacion_amortizacion;
	if(isset($cuenta_contable_depreciacion_amortizacion[$cuenta])){
		$cuenta_depreciacion_amortizacion=$cuenta_contable_depreciacion_amortizacion[$cuenta];
		for($i=0; $i<count($CC); $i++){
			if($CC[$i]["cuenta"]==$cuenta_depreciacion_amortizacion){
				$monto=AumentaCuentaContable($CC[$i]["cuenta"])?($CC[$i]["debe"]-$CC[$i]["haber"]):($CC[$i]["haber"]-$CC[$i]["debe"]);
				return ["cuenta"=>$CC[$i]["cuenta"], "denominacion"=>$CC[$i]["denominacion"], "monto"=>$monto];
			}
		}
	}
	return NULL;
}


$listado_cuentas_depreciacion=implode("','",$cuenta_contable_depreciacion_amortizacion);

$sql="SELECT
			DC.id_cuenta_contable as cuenta,
			CC.denominacion as denominacion,
			SUM(case when DC.operacion = 'D' then DC.monto else 0 end) AS debe,
			SUM(case when DC.operacion = 'H' then DC.monto else 0 end) AS haber,
			(case when DC.id_cuenta_contable in ('$listado_cuentas_depreciacion') then 't' else 'f' end) as depreciacion
		FROM
			modulo_base.detalle_contable AS DC,
			modulo_base.comprobante AS C,
			modulo_base.cuenta_contable AS CC
		WHERE
		  C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' AND
			C.contabilizado AND
			EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
			C.id=DC.id_comprobante AND
			DC.id_cuenta_contable=CC.id_cuenta_contable
		GROUP BY
			DC.id_cuenta_contable,
			CC.denominacion,
			depreciacion
		ORDER BY
			DC.id_cuenta_contable";
//print($sql);exit;
$CC=$db->Execute($sql);
if($CC==0) return;
$N=count($CC);


for($i=0; $i<count($CC); $i++){
	if($CC[$i]["depreciacion"]=='f'){
		AddContable($CC[$i]["cuenta"], $CC[$i]["denominacion"], $CC[$i]["debe"], $CC[$i]["haber"]);
	}
}


ksort($GENERAL);
//print "GENERAL";
//print_r($GENERAL);





class PDF_P extends FPDF{
	function Header(){
		global $organismo, $tam_ancho, $config;
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
		$this->Cell($GLOBALS['tam_ancho'],5,utf8_decode("(EN ".$config["moneda"].")"),'',1,'C',0);

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

function esPasivo($cuenta){
	if($cuenta && ($cuenta[0]=="2" || $cuenta[0]=="3"))
		return true;
	return false;
}

function FormatearPasivo($str){
	if(!$str) 			return "";
	if($str>=0)			return "(".number_format($str,2,",",".").")";
	else						return number_format($str*-1,2,",",".");
}

function FormatearCta($cuenta){
	if(!$cuenta) return "";
	return $cuenta[0].".".$cuenta[1].".".$cuenta[2].".".$cuenta[3].$cuenta[4].".".$cuenta[5].$cuenta[6].".".$cuenta[7].$cuenta[8].".".$cuenta[9].$cuenta[10].$cuenta[11];
}

function EscribirFila($pdf,$col1,$col2,$col3,$col4,$col5,$col6,$tab=0, $pasivo=false){
	$Y=$pdf->GetY();
	if($Y>250){
		$pdf->AddPage();
		$Y=$pdf->GetY();
	}

	$col1="$col1";


	if($col1 && ($col1[0]=="2" || $col1[0]=="3") || $pasivo){
		if($col3)	$col3=FormatearPasivo($col3);
		if($col4)	$col4=FormatearPasivo($col4);
		if($col5)	$col5=FormatearPasivo($col5);
		if($col6)	$col6=FormatearPasivo($col6);
	}
	else{
		if($col3)	$col3=number_format($col3,2,",",".");
		if($col4)	$col4=number_format($col4,2,",",".");
		if($col5)	$col5=number_format($col5,2,",",".");
		if($col6)	$col6=number_format($col6,2,",",".");
	}

	$pdf->Cell($GLOBALS["tam_codigo"],4,utf8_decode(FormatearCta($col1)),'',0,'C',0);
	$pdf->Cell($GLOBALS["tam_denominacion"],4,'','',0,'L',0);
	$pdf->Cell($GLOBALS["tam_monto"],4,utf8_decode($col3),'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],4,utf8_decode($col4),'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],4,utf8_decode($col5),'',0,'R',0);
	$pdf->Cell($GLOBALS["tam_monto"],4,utf8_decode($col6),'',1,'R',0);


	$tab_factor=0;
	if($tab==1) $tab_factor=5;

	$pdf->SetXY($pdf->lMargin+$GLOBALS["tam_codigo"]+$tab_factor,$Y);
	$pdf->MultiCell($GLOBALS["tam_denominacion"],4,utf8_decode($col2),'','',0);
	$pdf->Ln(1);
	$Y2=$pdf->GetY();

	$pdf->Line($pdf->lMargin,$Y,$pdf->lMargin,$Y2);
	$X=$pdf->lMargin+$GLOBALS["tam_codigo"]+$GLOBALS["tam_denominacion"]+$GLOBALS["tam_monto"]*4;
	$pdf->Line($X,$Y,$X,$Y2);
}



$pdf=new PDF_P("P","mm","letter");
$tam_ancho=190;
$tam_codigo=25;
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


$cuenta_nivel1="";
$cuenta_nivel2="";
$cuenta_nivel3="";

foreach($GENERAL as $cuenta => $row){
	$cuenta="$cuenta";;
	$nivel=NULL;

	if(substr($cuenta,1)=="00000000000"){
		$nivel=1;
		if($cuenta_nivel1){
			$fontsize=8;
			$fontstyle="B";
			$pdf->SetFont('helvetica',$fontstyle,$fontsize);
			EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel3]["denominacion"],"",$GENERAL[$cuenta_nivel3]["monto"],"","",0,esPasivo($cuenta_nivel3));
			EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel2]["denominacion"],"","",$GENERAL[$cuenta_nivel2]["monto"],"",0,esPasivo($cuenta_nivel2));

			$fontsize=9;
			$pdf->SetFont('helvetica',$fontstyle,$fontsize);
			EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel1]["denominacion"],"","","",$GENERAL[$cuenta_nivel1]["monto"],0,esPasivo($cuenta_nivel1));
			EscribirFila($pdf,"","","","","","");
			$cuenta_nivel2=$cuenta_nivel3="";
		}
		$cuenta_nivel1=$cuenta;
	}
	else if(substr($cuenta,2)=="0000000000"){
		$nivel=2;
		if($cuenta_nivel2){
			$fontsize=8;
			$fontstyle="B";
			$pdf->SetFont('helvetica',$fontstyle,$fontsize);
			EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel3]["denominacion"],"",$GENERAL[$cuenta_nivel3]["monto"],"","",0,esPasivo($cuenta_nivel3));
			EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel2]["denominacion"],"","",$GENERAL[$cuenta_nivel2]["monto"],"",0,esPasivo($cuenta_nivel2));
			$cuenta_nivel3="";
		}
		$cuenta_nivel2=$cuenta;
	}
	else if(substr($cuenta,3)=="000000000"){
		$nivel=3;
		if($cuenta_nivel3){
			$fontsize=8;
			$fontstyle="B";
			$pdf->SetFont('helvetica',$fontstyle,$fontsize);
			EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel3]["denominacion"],"",$GENERAL[$cuenta_nivel3]["monto"],"","",0,esPasivo($cuenta_nivel3));
		}
		$cuenta_nivel3=$cuenta;
	}

	$col1=$cuenta;
	$col2=$row["denominacion"];
	$col3="";
	$col4="";
	$col5="";
	$col6="";

	$fontsize=8;
	$fontstyle="";
	if($row["padre"]){
		$fontstyle="B";
	}
	else{
		$col3=$row["monto"];
	}

	if($nivel==1){
		$fontsize=10;
		$col1="";
	}

	$pdf->SetFont('helvetica',$fontstyle,$fontsize);
	EscribirFila($pdf,$col1,$col2,$col3,$col4,$col5,$col6);

	if(isset($row["depreciacion"])){
		$col1=$row["depreciacion"]["cuenta"];
		$col2="MENOS: ".$row["depreciacion"]["denominacion"];
		$col3=$row["depreciacion"]["monto"];
		$col4="";
		$col5="";
		$col6="";

		EscribirFila($pdf,$col1,$col2,$col3,$col4,$col5,$col6);

		$col1="";
		$col2=$row["denominacion"]." NETO";
		$col3="";
		$col4=$row["monto"]-$row["depreciacion"]["monto"];
		$col5="";
		$col6="";

		$fontstyle="I";
		$pdf->SetFont('helvetica',$fontstyle,$fontsize);
		EscribirFila($pdf,$col1,$col2,$col3,$col4,$col5,$col6,1);
	}

}


$fontsize=8;
$fontstyle="B";
$pdf->SetFont('helvetica',$fontstyle,$fontsize);
EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel3]["denominacion"],"",$GENERAL[$cuenta_nivel3]["monto"],"","",0,esPasivo($cuenta_nivel3));
EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel2]["denominacion"],"","",$GENERAL[$cuenta_nivel2]["monto"],"",0,esPasivo($cuenta_nivel2));

$fontsize=9;
$pdf->SetFont('helvetica',$fontstyle,$fontsize);
EscribirFila($pdf,"","TOTAL ".$GENERAL[$cuenta_nivel1]["denominacion"],"","","",$GENERAL[$cuenta_nivel1]["monto"],0,esPasivo($cuenta_nivel1));

EscribirFila($pdf,"","","","","","");
EscribirFila($pdf,"","TOTAL PASIVO MAS PATRIMONIO","","","",$GENERAL["200000000000"]["monto"]+$GENERAL["300000000000"]["monto"]);



$pdf->AliasNbPages();
ob_clean();
$pdf->Output("balance_general.pdf","I");
?>