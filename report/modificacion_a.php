<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/fpdf/1.84/rotation.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT
                            P.identificacion_tipo||P.identificacion_numero as identificacion,
                            P.denominacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");



$IDComprobante=SIGA::paramGet("id");
if(!$IDComprobante)
	exit;

$anio=SIGA::data();
$AnchoPagina=16.5;
$NDecimalesFormatoPresupuesto=2;



$organismo=$db->Execute("SELECT
														P.identificacion_tipo||P.identificacion_numero as identificacion,
                            P.denominacion,
                            P.direccion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");	


$codigo_ente="A0322";
$denominacion_ente=$organismo[0]["denominacion"];

$sql="SELECT C.fecha, C.tipo FROM modulo_base.comprobante AS C WHERE C.id=$IDComprobante";
$comprobante=$db->Execute($sql);
if(!$comprobante or count($comprobante)==0){
	echo "El comprobante no existe.";
	exit;
	}

$bandera_insubsistencia="";
$bandera_reduccion="";
$bandera_credito_adicional="";
$bandera_rectificacion="";
$bandera_gastos_corrientes="";
$bandera_gastos_capital="";

$F=explode("-",$comprobante[0]['fecha']);

$FECHA_DIA=$F[2];
$FECHA_MES=$F[1];
$FECHA_ANIO=$F[0];


//buscar fuente financiera
$comprobante_datos=$db->Execute("select * from modulo_base.comprobante_datos where id_comprobante='$IDComprobante'");



$TITULO_AU="PARTIDAS RECEPTORAS \"PARA\"";
$mov_au=$db->Execute("SELECT
														id_cuenta_presupuestaria,
														monto,
														id_accion_subespecifica,
														_formatear_estructura_presupuestaria(id_accion_subespecifica) AS estructura
												FROM modulo_base.detalle_presupuestario
												WHERE id_comprobante=$IDComprobante AND operacion='AU'
												ORDER BY estructura, id_cuenta_presupuestaria");

												
$TITULO_DI="PARTIDAS CEDENTES \"DE\"";
$mov_di=$db->Execute("SELECT
														id_cuenta_presupuestaria,
														monto,
														id_accion_subespecifica,
														_formatear_estructura_presupuestaria(id_accion_subespecifica) AS estructura
												FROM modulo_base.detalle_presupuestario
												WHERE id_comprobante=$IDComprobante AND operacion='DI'
												ORDER BY estructura, id_cuenta_presupuestaria");

												
$fuente_financiera=array();
$fuente_financiera[0]=array();

for($i=0;$i<count($comprobante_datos);$i++){
		if($comprobante_datos[$i]["dato"]=="tipo_gasto")
				$fuente_financiera[0]["tipo_modificacion"]=$comprobante_datos[$i]["valor"];
		if($comprobante_datos[$i]["dato"]=="fuente_recursos"){
				$fuente_financiera[0]["denominacion"]="";
				$AUX=$db->Execute("select denominacion_fuente from modulo_base.fuente_recursos where id='".$comprobante_datos[$i]["valor"]."'");
				$fuente_financiera[0]["denominacion"]=$AUX[0][0];
		}
}

switch($comprobante[0]["tipo"]){
		case "TR":
				if($fuente_financiera[0]["tipo_modificacion"]=="CA")//gasto de capital
						$bandera_gastos_capital="X";
				else
						$bandera_gastos_corrientes="X";
		break;
		case "CR":
				$bandera_credito_adicional="X";
		break;
		case "RD":
				$bandera_reduccion="X";
		break;
}






function RetornarPadresCtaPresp($cuenta){
	if(!$cuenta)
		return "";
	$a[0]=substr($cuenta,0,3);
	$a[1]=substr($cuenta,3,2);
	$a[2]=substr($cuenta,5,2);
	$a[3]=substr($cuenta,7,2);
	if($a[3]=="00")
		return array(	$a[0]."00"."00"."00",
						$a[0].$a[1]."00"."00");
	return array(	$a[0]."00"."00"."00",
					$a[0].$a[1]."00"."00",
					$a[0].$a[1].$a[2]."00");
	}

$A_au=array();
$k_au=0;
$k=0;
$TOTAL_au=0;
for($i=0;$i<count($mov_au);$i++){
	$padres=RetornarPadresCtaPresp($mov_au[$i]["id_cuenta_presupuestaria"]);
	for($j=0;$j<count($padres);$j++){
		$sw=false;
		for($o=0;$o<$k;$o++)
			if($padres[$j]==$A_au[$o]["id_cuenta_presupuestaria"] and $mov_au[$i]["id_accion_subespecifica"]==$A_au[$o]["id_accion_subespecifica"]){
				$sw=true;
				break;
				}
		if($sw==false){//si es 1era vez que se agrega
			$A_au[$k]["id_cuenta_presupuestaria"]=$padres[$j];
			$A_au[$k]["id_accion_subespecifica"]=$mov_au[$i]["id_accion_subespecifica"];
			$A_au[$k]["estructura"]=$mov_au[$i]["estructura"];
			$A_au[$k]["monto"]=$mov_au[$i]["monto"];
			$k++;
			}
		else{//si ya estaba agregado, acumular suma
			$A_au[$o]["monto"]+=$mov_au[$i]["monto"];
			}
		}
	$A_au[$k]["id_cuenta_presupuestaria"]=$mov_au[$i]["id_cuenta_presupuestaria"];
	$A_au[$k]["id_accion_subespecifica"]=$mov_au[$i]["id_accion_subespecifica"];
	$A_au[$k]["monto"]=$mov_au[$i]["monto"];
	$A_au[$k]["estructura"]=$mov_au[$i]["estructura"];
	$TOTAL_au+=$mov_au[$i]["monto"];
	$k++;
	}
$k_au=$k;


$A_di=array();
$k_di=0;
$k=0;
$TOTAL_di=0;
for($i=0;$i<count($mov_di);$i++){
	$padres=RetornarPadresCtaPresp($mov_di[$i]["id_cuenta_presupuestaria"]);
	for($j=0;$j<count($padres);$j++){
		$sw=false;
		for($o=0;$o<$k;$o++)
			if($padres[$j]==$A_di[$o]["id_cuenta_presupuestaria"] and $mov_di[$i]["id_accion_subespecifica"]==$A_di[$o]["id_accion_subespecifica"]){
				$sw=true;
				break;
				}
		if($sw==false){//si es 1era vez que se agrega
			$A_di[$k]["id_cuenta_presupuestaria"]=$padres[$j];
			$A_di[$k]["id_accion_subespecifica"]=$mov_di[$i]["id_accion_subespecifica"];
			$A_di[$k]["estructura"]=$mov_di[$i]["estructura"];
			$A_di[$k]["monto"]=$mov_di[$i]["monto"];
			$k++;
			}
		else{//si ya estaba agregado, acumular suma
			$A_di[$o]["monto"]+=$mov_di[$i]["monto"];
			}
		}
	$A_di[$k]["id_cuenta_presupuestaria"]=$mov_di[$i]["id_cuenta_presupuestaria"];
	$A_di[$k]["id_accion_subespecifica"]=$mov_di[$i]["id_accion_subespecifica"];
	$A_di[$k]["monto"]=$mov_di[$i]["monto"];
	$A_di[$k]["estructura"]=$mov_di[$i]["estructura"];
	$TOTAL_di+=$mov_di[$i]["monto"];
	$k++;
	}
$k_di=$k;




class PDF_P extends FPDF{
	var $MARGEN_LEFT;
	var $MARGEN_TOP;
	var $ANCHO;
	var $col_montos;
	var $col_codigo;
	var $col_denominacion;
	function Medidas($MARGEN_LEFT, $MARGEN_TOP, $ANCHO, $col_montos, $col_codigo){
		$this->MARGEN_LEFT=$MARGEN_LEFT;
		$this->MARGEN_TOP=$MARGEN_TOP;
		$this->ANCHO=$ANCHO;
		$this->col_montos=$col_montos;
		$this->col_codigo=$col_codigo;
		$this->col_denominacion=$this->ANCHO-(11*$this->col_montos+$this->col_codigo);


		$this->SetLeftMargin($MARGEN_LEFT);
		$this->SetTopMargin($MARGEN_TOP);
		}
	function EncabezadoImagenes(){
		global $organismo;
		//$this->Image("../../images/cintillo_actual.jpg",$GLOBALS['MARGEN_LEFT'],$GLOBALS['MARGEN_TOP']+1,$this->ANCHO);
		//$this->Image("../../images/logo_institucional_02.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,40);
		//$this->Image(SIGA::databasePath()."/config/logo_02.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,40);
		//$this->Image(SIGA::databasePath()."/config/logo_02.jpg",$this->MARGEN_LEFT,$this->MARGEN_TOP,40);
		$this->Image(SIGA::databasePath()."/config/plantilla_vertical.jpg",0,0,215);
		//$this->SetLineWidth(0.4);
		//$this->Cell($this->ANCHO,14,'','LRT',1,'',0);
		$this->ln(14);
		//$this->SetLineWidth(0.2);
		}
	function LineaHorizontal(){
		//$this->SetLineWidth(0.6);
		//$this->Line($this->GetX()+0.6,$this->GetY(),$this->lMargin+$this->ANCHO-0.6,$this->GetY());
		//$this->SetLineWidth(0.2);
		}
	function EncabezadoFS(){
		$pos_y=$this->GetY();
		$pos_x=$this->GetX();

		//$this->LineaHorizontal();
		global $fuente_financiera;

		$this->SetXY($pos_x+1+30,$pos_y+3);
		$this->SetFont('helvetica','B',10);
		$this->Cell(145-30,4,utf8_decode($GLOBALS["denominacion_ente"]),'',0,'L',1);
		$this->SetFont('helvetica','',8);
		$this->Cell(10,4,utf8_decode("COD."),'',0,'L',1);
		$this->Cell(10,4,utf8_decode($GLOBALS["codigo_ente"][0]),'LRTB',0,'C',1);
		$this->Cell(10,4,utf8_decode($GLOBALS["codigo_ente"][1].$GLOBALS["codigo_ente"][2]),'LRTB',0,'C',1);
		$this->Cell(10,4,utf8_decode($GLOBALS["codigo_ente"][3].$GLOBALS["codigo_ente"][4]),'LRTB',1,'C',1);
		$this->SetFont('helvetica','B',10);
		$this->Cell($this->ANCHO,10,utf8_decode("SOLICITUD DE MODIFICACIÓN PRESUPUESTARIA PPTO. ".$GLOBALS["anio"]),'',1,'C',0);
		$this->SetFont('helvetica','',8);
		$this->SetX($pos_x+1);
		$this->Cell(155,3,utf8_decode("FUENTE DE FINANCIAMIENTO: ".$fuente_financiera[0]["denominacion"]),'',0,'L',1);
		$this->Cell(30,3,utf8_decode("FECHA"),'',1,'C',1);
		$this->SetX($pos_x+1);
		$this->Cell(155,4,utf8_decode("UNIDAD ADMINISTRADORA: PROYECTO Y ACCIÓN CENTRALIZADA"),'',0,'L',1);
		$this->Cell(10,4,utf8_decode($GLOBALS["FECHA_DIA"]),'LRTB',0,'C',1);
		$this->Cell(10,4,utf8_decode($GLOBALS["FECHA_MES"]),'LRTB',0,'C',1);
		$this->Cell(10,4,utf8_decode($GLOBALS["FECHA_ANIO"]),'LRTB',1,'C',1);

		$this->SetXY($pos_x,$pos_y);
		$this->SetLineWidth(0.4);
		//$this->Cell($this->ANCHO,26,'','LRB',1,'',0);
		$this->Ln(26);
		$this->SetLineWidth(0.2);
		}
	function EncabezadosX(){
		$pos_y=$this->GetY();
		$pos_x=$this->GetX();

		$this->LineaHorizontal();

		//INSUBSISTENCIA
		$this->SetFont('helvetica','',6);
		$this->SetXY($pos_x+1,$pos_y+7);
		$this->Cell(20,8,utf8_decode("INSUBSISTENCIA"),'',0,'L',1);
		$this->SetFont('helvetica','B',10);
		$this->Cell(7,8,utf8_decode($GLOBALS["bandera_insubsistencia"]),'LRTB',0,'C',1);

		//REDUCCION
		$this->SetXY($pos_x+30,$pos_y+7);
		$this->SetFont('helvetica','',6);
		$this->Cell(15,8,utf8_decode("REDUCCIÓN"),'',0,'L',1);
		$this->SetFont('helvetica','B',10);
		$this->Cell(7,8,utf8_decode($GLOBALS["bandera_reduccion"]),'LRTB',0,'C',1);

		//RECURSOS ADICIONALES
		$this->SetXY($pos_x+55,$pos_y+1);
		$this->SetFont('helvetica','',6);
		$this->Cell(31,3,utf8_decode("RECURSOS ADICIONALES"),'',0,'C',1);
		$this->SetXY($pos_x+55,$pos_y+4);
		$this->Cell(24,8,utf8_decode("CRÉDITO ADICIONAL"),'',0,'L',1);
		$this->SetFont('helvetica','B',10);
		$this->Cell(7,8,utf8_decode($GLOBALS["bandera_credito_adicional"]),'LRTB',0,'C',1);

		$this->SetXY($pos_x+55,$pos_y+4+9);
		$this->SetFont('helvetica','',6);
		$this->Cell(24,8,utf8_decode("RECTIFICACIÓN"),'',0,'L',1);
		$this->SetFont('helvetica','B',10);
		$this->Cell(7,8,utf8_decode($GLOBALS["bandera_rectificacion"]),'LRTB',0,'C',1);


		//FUENTE
		
		$this->SetXY($pos_x+88,$pos_y+7);
		$this->SetFont('helvetica','',6);
		$this->Cell(33,8,utf8_decode("FUENTE DE FINANCIAMINETO"),'',0,'L',1);
		$this->SetFont('helvetica','B',6);
		$this->Cell(33,8,utf8_decode($GLOBALS["fuente_financiera"][0]["denominacion"]),'LRTB',0,'C',1);

		//TRASPASOS
		$this->SetXY($pos_x+155,$pos_y+1);
		$this->SetFont('helvetica','',6);
		$this->Cell(33,3,utf8_decode("TRASPASOS"),'',0,'C',1);
		$this->SetXY($pos_x+155,$pos_y+4);
		$this->Cell(26,8,utf8_decode("GASTOS CORRIENTES"),'',0,'L',1);
		$this->SetFont('helvetica','B',10);
		$this->Cell(7,8,utf8_decode($GLOBALS["bandera_gastos_corrientes"]),'LRTB',0,'C',1);

		$this->SetXY($pos_x+155,$pos_y+4+9);
		$this->SetFont('helvetica','',6);
		$this->Cell(26,8,utf8_decode("GASTOS DE CAPITAL"),'',0,'L',1);
		$this->SetFont('helvetica','B',10);
		$this->Cell(7,8,utf8_decode($GLOBALS["bandera_gastos_capital"]),'LRTB',0,'C',1);


		$this->SetXY($pos_x,$pos_y);
		$this->SetLineWidth(0.4);
		$this->Cell($this->ANCHO,22,'','LRTB',1,'',0);
		$this->SetLineWidth(0.2);
		}
	function CabeceraTabla(){
		$pos_y=$this->GetY();
		$pos_x=$this->GetX();

		$this->LineaHorizontal();
		$this->SetFont('helvetica','B',10);
		$this->SetXY($pos_x,$pos_y+1);
		$this->Cell($this->ANCHO,4,utf8_decode($GLOBALS["TITULO"]),'B',1,'C',0);

		$this->SetFont('helvetica','B',8);
		$this->Cell(20+16+6+8+7+6+7,4,utf8_decode("IMPUTACIÓN PRESUPUESTARIA"),'BTR',1,'C',0);

		$pos_y_temp=$this->GetY();
		$this->SetFont('helvetica','',6);
		$this->MultiCell(20,3,utf8_decode("PROYECTO\nO ACCIÓN\nCENTRALIZADA"),"LRTB","C");
		$this->SetXY($pos_x+20,$pos_y_temp);
		$this->MultiCell(16,4.5,utf8_decode("ACCIÓN\nESPECIFICA"),"LRTB","C");
		$this->SetXY($pos_x+20+16,$pos_y_temp);
		$this->Cell(6,9,utf8_decode('UEL'),'LRTB',0,'',0);
		$this->Cell(8,9,utf8_decode('PART'),'LRTB',0,'',0);
		$this->Cell(7,9,utf8_decode('GEN'),'LRTB',0,'',0);
		$this->Cell(6,9,utf8_decode('ESP'),'LRTB',0,'',0);
		$this->SetXY($pos_x+20+16+6+8+7+6,$pos_y_temp);
		$this->MultiCell(7,4.5,utf8_decode("SUB\nESP"),"LRTB","C");

		$this->SetFont('helvetica','',8);
		$this->SetXY($pos_x+20+16+6+8+7+6+7,$pos_y+1+4);
		$this->Cell(90,9+4,utf8_decode("DENOMINACIÓN"),'LRTB',0,'C',0);
		$this->Cell(30,9+4,utf8_decode("BOLÍVALES"),'LTB',1,'C',0);

		$this->SetXY($pos_x,$pos_y);
		$this->SetLineWidth(0.4);
		$this->Cell($this->ANCHO,18,'','LRT',1,'',0);
		$this->SetLineWidth(0.2);
		}
	function Firmazo(){
		$ancho_firma=$this->ANCHO/5;
		$this->SetFont('helvetica','B',8);
		$this->Cell($this->ANCHO,5,"",'',1,'C',0);
		$this->Cell($ancho_firma*2,7,utf8_decode("INSTITUCIÓN"),'LRTB',0,'C',0);
		$this->Cell($ancho_firma*3,7,utf8_decode("OFICINA CENTRAL DE PRESUPUESTO"),'LRTB',1,'C',0);

		$this->SetFont('helvetica','',5);
		$this->Cell($ancho_firma,2,utf8_decode("ADMINISTRACIÓN Y SERV."),'LRT',0,'C',0);
		$this->Cell($ancho_firma,2,utf8_decode("RESPONSABLE"),'LRT',0,'C',0);
		$this->Cell($ancho_firma,2,utf8_decode("MÁX. AUTORIDAD"),'LRT',0,'C',0);
		$this->Cell($ancho_firma,2,utf8_decode("JEFE SECTOR"),'LRT',0,'C',0);
		$this->Cell($ancho_firma,2,utf8_decode("DIRECTOR G. SECTORIAL"),'LRT',1,'C',0);

		$this->Cell($ancho_firma,2,utf8_decode(""),'LR',0,'C',0);
		$this->Cell($ancho_firma,2,utf8_decode("DEL PROYECTO/ACCIÓN CENTRALIZADA"),'LR',0,'C',0);
		$this->Cell($ancho_firma,2,utf8_decode(""),'LR',0,'C',0);
		$this->Cell($ancho_firma,2,utf8_decode(""),'LR',0,'C',0);
		$this->Cell($ancho_firma,2,utf8_decode(""),'LR',1,'C',0);

		$this->Cell($ancho_firma,45,utf8_decode(""),'LRB',0,'C',0);
		$this->Cell($ancho_firma,45,utf8_decode(""),'LRB',0,'C',0);
		$this->Cell($ancho_firma,45,utf8_decode(""),'LRB',0,'C',0);
		$this->Cell($ancho_firma,45,utf8_decode(""),'LRB',0,'C',0);
		$this->Cell($ancho_firma,45,utf8_decode(""),'LRB',1,'C',0);
		}
	function MarcoAreaFilas($pos_x,$pos_y,$tam){
		$this->SetXY($pos_x,$pos_y);
		$this->SetLineWidth(0.4);
		$this->Cell($this->ANCHO,$tam,utf8_decode(''),'LRB',1,'',0);
		$this->SetLineWidth(0.2);
		}
		
  
	}

	
	



$NMAX_FILAS=26;



$pdf=new PDF_P("P","mm","letter");

$MARGEN_LEFT=15;
$MARGEN_TOP=15;
$ANCHO=190;
$col_montos=17;
$col_codigo=13;
$col_denominacion=$ANCHO-(11*$col_montos+$col_codigo);

$pdf->Medidas($MARGEN_LEFT, $MARGEN_TOP, $ANCHO, $col_montos, $col_codigo);


$pdf->SetAutoPageBreak(false);


$pdf->SetLineWidth(0.2);

$pdf->SetFont('helvetica','',8);
$pdf->SetFillColor(255,255,255);









for($P=0;$P<2;$P++):

		if($P==0){
				$k=$k_di;
				$A=$A_di;
				$TITULO=$TITULO_DI;
				$TOTAL=$TOTAL_di;
		}
		else{
				$k=$k_au;
				$A=$A_au;
				$TITULO=$TITULO_AU;
				$TOTAL=$TOTAL_au;
		}
		
		if($k==0){
				continue;
		}
		
		
		$pdf->AddPage();
		$pdf->EncabezadoImagenes();
		$pdf->EncabezadoFS();
		$pdf->EncabezadosX();
		$pdf->CabeceraTabla();
		
		
		
		$pos_y=$pdf->GetY();
		$pos_x=$pdf->GetX();
		
		for($i=0;$i<$k;$i++){
			$PART=substr($A[$i]["id_cuenta_presupuestaria"],0,3);
			$GEN=substr($A[$i]["id_cuenta_presupuestaria"],3,2);
			$ESP=substr($A[$i]["id_cuenta_presupuestaria"],5,2);
			$SUBESP=substr($A[$i]["id_cuenta_presupuestaria"],7,2);
			$espacio="";
			$pdf->SetFont('helvetica','',7);
			if($ESP=="00"){
				$espacio="      ";
				$pdf->SetFont('helvetica','B',7);
				}
			if($GEN=="00"){
				$espacio="            ";
				$pdf->SetFont('helvetica','B',7);
				}
			if($i+1<$k)
				$SUBESP_SIG=substr($A[$i+1]["id_cuenta_presupuestaria"],7,2);
			else
				$SUBESP_SIG="";
			if($SUBESP_SIG!="00")
				$pdf->SetFont('helvetica','B',7);
			if($SUBESP!="00" or $i==$k-1)
				$pdf->SetFont('helvetica','',7);
		
				//$A[$i]["estructura"]="ACC000001-03-02-01";
		
			$ESTRUCTURA=explode("-",$A[$i]["estructura"]);
			
			
			
			//$ESTRUCTURA[1]=substr($ESTRUCTURA[1],strlen($ESTRUCTURA[1])-2,2);
		
			//if($ESTRUCTURA[0][0]=="A")
			//	$ESTRUCTURA[1]=substr($ESTRUCTURA[0],strlen($ESTRUCTURA[0])-2,2);
				//$ESTRUCTURA[1]=substr($ESTRUCTURA[0],strlen($ESTRUCTURA[0])-2,2);
		
			$sql="SELECT denominacion FROM modulo_base.cuenta_presupuestaria WHERE id_cuenta_presupuestaria='".$A[$i]["id_cuenta_presupuestaria"]."'";
			$denominacion=$db->Execute($sql);
		
			$pdf->Cell(20,4,utf8_decode($ESTRUCTURA[0]),'LRTB',0,'C',0);
			$pdf->Cell(16,4,utf8_decode($ESTRUCTURA[1]),'LRTB',0,'C',0);
			$pdf->Cell(6,4,utf8_decode(''),'LRTB',0,'C',0);
			$pdf->Cell(8,4,utf8_decode($PART),'LRTB',0,'C',0);
			$pdf->Cell(7,4,utf8_decode($GEN),'LRTB',0,'C',0);
			$pdf->Cell(6,4,utf8_decode($ESP),'LRTB',0,'C',0);
			$pdf->Cell(7,4,utf8_decode($SUBESP),'LRTB',0,'C',0);
			$pdf->SetFontSize(6);
			$pdf->Cell(90,4,utf8_decode($denominacion[0][0]),'LRTB',0,'L',1);
			$pdf->SetFontSize(7);
			$pdf->Cell(30,4,utf8_decode(number_format($A[$i]["monto"],$GLOBALS["NDecimalesFormatoPresupuesto"],",",".").$espacio),'LRTB',0,'R',1);
			$pdf->Cell(20,4,'','',1,'L',1);
		
		
			if(($i+1)%$NMAX_FILAS==0){
				$pdf->MarcoAreaFilas($pos_x,$pos_y,$NMAX_FILAS*4);
				$pdf->Firmazo();
		
				$pdf->AddPage();
				$pdf->EncabezadoImagenes();
				$pdf->EncabezadoFS();
				$pdf->EncabezadosX();
				$pdf->CabeceraTabla();
				}
			}
		
		
		for($i=$k%$NMAX_FILAS;$i<$NMAX_FILAS;$i++){
			$pdf->Cell(20,4,'','LRTB',0,'C',0);
			$pdf->Cell(16,4,'','LRTB',0,'C',0);
			$pdf->Cell(6,4,'','LRTB',0,'C',0);
			$pdf->Cell(8,4,'','LRTB',0,'C',0);
			$pdf->Cell(7,4,'','LRTB',0,'C',0);
			$pdf->Cell(6,4,'','LRTB',0,'C',0);
			$pdf->Cell(7,4,'','LRTB',0,'C',0);
			$pdf->Cell(90,4,'','LRTB',0,'L',1);
			$pdf->Cell(30,4,'','LRTB',0,'R',1);
			$pdf->Cell(20,4,'','',1,'L',1);
			}
		
		$pdf->SetFont('helvetica','B',8);
		$pdf->Cell(20+16+6+8+7+6+7+90,5,'TOTAL','RT',0,'C',0);
		$pdf->Cell(30,5,number_format($TOTAL,$GLOBALS["NDecimalesFormatoPresupuesto"],",","."),'LT',1,'R',0);
		
		
		
		
		$pdf->MarcoAreaFilas($pos_x,$pos_y,$NMAX_FILAS*4+5);
		$pdf->Firmazo();

endfor;

$pdf->Output();

?>



