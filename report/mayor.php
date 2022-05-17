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

//$ID_EP="102,98";
$ID_EP=SIGA::paramGet("ep");
if(!$ID_EP) $ID_EP='*';
//$PARTIDAS="401010100";
$PARTIDAS=SIGA::paramGet("cuenta");
if(!$PARTIDAS) $PARTIDAS='*';

$FECHA_I=unformatDate($_GET["fecha_inicio"]);
$FECHA_F=unformatDate($_GET["fecha_culminacion"]);



if($PARTIDAS=="*"){
	$SW_PARTIDAS=true;
	}
else{
	$SW_PARTIDAS=false;
	$PARTIDAS=explode(",",$PARTIDAS);
	}


if($ID_EP[0]=="*"){//buscar todos los proyectos y acc aperturados
	$sql="select
            id_accion_subespecifica
          from
            modulo_base.formulacion
          where
            anio=".SIGA::data()." and
            tipo='F' and
            not id_comprobante_apertura is null";

	$RETORNO_EP=$db->Execute($sql);
	if(count($RETORNO_EP)==0 or !$RETORNO_EP){
		echo "No existen proyectos aperturados";
		exit;
		}
	$ID_EP="";
	for($i=0;$i<count($RETORNO_EP);$i++)
		$ID_EP[$i]=$RETORNO_EP[$i][0];
	}
else
	$ID_EP=explode(",",$ID_EP);









class PDF_P extends FPDF{
	var $MARGEN_LEFT;
	var $MARGEN_TOP;
	var $t_fecha;
	var $t_comprobante;
	var $t_denominacion;
	var $t_operacion;
	var $t_monto;
	var $t_saldo;
	var $ANCHO;

	function Header(){
		global $PROACC, $FUENTE, $organismo;
		$this->SetFillColor(255,255,255);
		//$this->Image("../../images/cintillo_actual.jpg",$this->lMargin,$this->tMargin-8,$this->ANCHO);
		//$this->Image("../../images/logo_institucional_02.jpg",$this->lMargin,$this->tMargin-8,40);
		$this->Image(SIGA::databasePath()."/config/logo_02.jpg",$this->lMargin,$this->tMargin-8,40);

		$this->Ln(8);
		$this->SetFont('helvetica','B',12);
		$this->Cell($this->ANCHO,5,utf8_decode('MAYOR ANALÍTICO'),'',1,'C');

		$this->SetFont('helvetica','',9);
		$this->Cell($this->ANCHO,5,utf8_decode("DEL ".formatDate($GLOBALS["FECHA_I"])." AL ".formatDate($GLOBALS["FECHA_F"])),'',1,'C');

		$this->Ln(2);
		
		//codigo
		$this->SetFont('helvetica','B',9);
		$this->Cell(28,4,utf8_decode('CÓDIGO'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',9);
		$this->Cell(150,4,utf8_decode($PROACC[0]["estructura_presupuestaria"]),'',1,'L');
		//denominacion
		$this->SetFont('helvetica','B',9);
		$this->Cell(28,4,utf8_decode('DENOMINACIÓN'),'',0,'L');
		$this->Cell(2,4,utf8_decode(':'),'',0,'C');
		$this->SetFont('helvetica','',8);
		$this->MultiCell($this->ANCHO-28,3.5,utf8_decode("-".$PROACC[0]["denominacion_centralizada"].".\n".($PROACC[0]["codigo_especifica"]=="00"?"":("-".$PROACC[0]["denominacion_especifica"].".\n")).($PROACC[0]["codigo_subespecifica"]=="00"?"":"-".$PROACC[0]["denominacion_subespecifica"].".")),'','J',1);

		$this->Ln(3);
		}
	function CabeceraTabla(){
		global $CUENTA;
		$this->SetDrawColor(200,200,200);
		$this->SetFillColor(216,216,216);
		$this->SetFont('helvetica','B',9);
		$this->MultiCell($this->ANCHO,4,utf8_decode($CUENTA[0]["cuenta_presupuestaria"]."  ".$CUENTA[0]["denominacion"]."."),'LRT','L',1);
		
		$this->SetFont('helvetica','',7.5);
		$this->Cell($this->t_fecha,4,utf8_decode('FECHA'),'LB',0,'C',1);
		$this->Cell($this->t_comprobante,4,utf8_decode('COMPROBANTE'),'B',0,'C',1);
		$this->Cell($this->t_denominacion,4,utf8_decode('DENOMINACIÓN'),'B',0,'C',1);
		$this->Cell($this->t_operacion,4,utf8_decode('OPERACIÓN'),'B',0,'C',1);
		$this->Cell($this->t_monto,4,utf8_decode('MONTO'),'B',0,'C',1);
		$this->Cell($this->t_saldo,4,utf8_decode('SALDO'),'RB',1,'C',1);
		$this->SetFillColor(255,255,255);
		$this->SetDrawColor(0,0,0);
		}
	}


$pdf=new PDF_P("P","mm","letter");


$pdf->MARGEN_LEFT=10;
$pdf->MARGEN_TOP=15;
$pdf->ANCHO=195;

$pdf->t_fecha=15;
$pdf->t_comprobante=23;
$pdf->t_operacion=25;
$pdf->t_monto=20;
$pdf->t_saldo=20;
$pdf->t_denominacion=$pdf->ANCHO-($pdf->t_fecha+$pdf->t_comprobante+$pdf->t_operacion+$pdf->t_monto+$pdf->t_saldo);

$pdf->SetAutoPageBreak(false);
$pdf->SetLeftMargin($pdf->MARGEN_LEFT);
$pdf->SetTopMargin($pdf->MARGEN_TOP);
$pdf->SetFillColor(255,255,255);





for($p=0;$p<count($ID_EP);$p++){
		//list($id_accion_subespecifica,$id_fuente_recursos)=explode("|",$ID_EP[$p]);
		$id_accion_subespecifica=$ID_EP[$p];

		//mostrar nombre del proyecto
		$PROACC=$db->Execute("select
																*,
																_formatear_estructura_presupuestaria($id_accion_subespecifica) as estructura_presupuestaria
														from
																modulo_base.accion_centralizada as AC,
																modulo_base.accion_especifica as AE,
																modulo_base.accion_subespecifica as ASE
														where
																ASE.id=$id_accion_subespecifica and
																ASE.id_accion_especifica=AE.id and
																AE.id_accion_centralizada=AC.id
																");	

		if(count($PROACC)==0 or !$PROACC)
				continue;

		//$FUENTE=$db->Execute("select * from modulo_base.fuente_recursos where id=$id_fuente_recursos");	
		//if(count($FUENTE)==0 or !$FUENTE)
		//		continue;


	
	
		if($SW_PARTIDAS){//buscar partidas existentes en ese proyecto
				$PARTIDAS_RETORNO=$db->Execute("SELECT DISTINCT
																						DP.id_cuenta_presupuestaria
																					FROM
																						modulo_base.comprobante AS C,
																						modulo_base.detalle_presupuestario AS DP
																					WHERE
																						EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
																						C.id=DP.id_comprobante AND
																						C.contabilizado AND
																						DP.id_accion_subespecifica=$id_accion_subespecifica
																					ORDER BY
																						DP.id_cuenta_presupuestaria");

				if(count($PARTIDAS_RETORNO)==0 or !$PARTIDAS_RETORNO)
				continue;
				//copiar las partidas encontradas en el arreglo $PARTIDAS
				$PARTIDAS=array();
				for($k=0;$k<count($PARTIDAS_RETORNO);$k++)
						$PARTIDAS[$k]=$PARTIDAS_RETORNO[$k]["id_cuenta_presupuestaria"];
		}


		$pdf->AddPage();



		for($c=0;$c<count($PARTIDAS);$c++){
				//mostrar nombre de la partida
				$CUENTA=$db->Execute("select denominacion, _formatear_cuenta_presupuestaria(id_cuenta_presupuestaria) as cuenta_presupuestaria from modulo_base.cuenta_presupuestaria where id_cuenta_presupuestaria ='".$PARTIDAS[$c]."'");
				if(count($CUENTA)==0)
						continue;
				
				if($pdf->GetY()>=250){
						$pdf->AddPage();
				}
	
				$pdf->CabeceraTabla();
	
				//mostrar saldo anterior
				$DISMINUCIONES_ANTERIOR=$db->Execute("SELECT
						SUM(DP.monto) as monto
					FROM
						modulo_base.comprobante AS C,
						modulo_base.detalle_presupuestario AS DP
					WHERE
						C.id=DP.id_comprobante AND
						C.contabilizado AND
						DP.id_cuenta_presupuestaria='".$PARTIDAS[$c]."' AND
						EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
						C.fecha < '$FECHA_I' AND
						DP.id_accion_subespecifica=$id_accion_subespecifica AND
						(DP.operacion='C' OR DP.operacion='CC' OR DP.operacion='CCP' OR DP.operacion='DI')");
	
				$AUMENTOS_ANTERIOR=$db->Execute("SELECT
						SUM(DP.monto) as monto
					FROM
						modulo_base.comprobante AS C,
						modulo_base.detalle_presupuestario AS DP
					WHERE
						C.id=DP.id_comprobante AND
						C.contabilizado AND
						DP.id_cuenta_presupuestaria='".$PARTIDAS[$c]."' AND
						EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
						C.fecha < '$FECHA_I' AND
						DP.id_accion_subespecifica=$id_accion_subespecifica AND
						(DP.operacion='AP' OR DP.operacion='AU')");
	
				$SALDO_INICIAL=$AUMENTOS_ANTERIOR[0]["monto"]-$DISMINUCIONES_ANTERIOR[0]["monto"];
	
				$pdf->Cell($pdf->ANCHO-$pdf->t_saldo,4,utf8_decode("SALDO ANTERIOR"),'',0,'L',1);
				$pdf->Cell($pdf->t_saldo,4,utf8_decode(number_format($SALDO_INICIAL,2,",",".")),'',1,'R',1);
	
	
				//mostrar las cuentas encontradas
				$MOVIMIENTOS=$db->Execute("SELECT
						C.fecha,
						lpad(text(C.correlativo),10,'0') as correlativo,
						C.tipo,
						C.id,
						C.concepto,
						DP.operacion,
						DP.monto,
						(case C.tipo
									when 'AP' then 1
									when 'AC' then 2
									when 'RF' then 3
									when 'PC' then 4
									when 'CR' then 5
									when 'TR' then 6
									when 'RD' then 7
									when 'OC' then 8
									when 'OS' then 9
									when 'OP' then 10
									when 'MB' then 11
							else 99 end) as prioridad
					FROM
						modulo_base.comprobante AS C,
						modulo_base.detalle_presupuestario AS DP
					WHERE
						C.id=DP.id_comprobante AND
						C.contabilizado AND
						DP.id_cuenta_presupuestaria='".$PARTIDAS[$c]."' AND
						C.fecha between '$FECHA_I' AND '$FECHA_F' AND
						DP.id_accion_subespecifica=$id_accion_subespecifica
					ORDER BY
						C.fecha,
						DP.operacion,
						prioridad,					
						C.id");
			
				if(count($MOVIMIENTOS)==0 or !$MOVIMIENTOS){
						if($pdf->GetY()>=250){
								$pdf->AddPage();
								$pdf->CabeceraTabla();
						}
						$pdf->Ln(5);
						continue;
				}
	
	
				$SALDO=$SALDO_INICIAL;
				for($i=0;$i<count($MOVIMIENTOS);$i++){
						$aux="";
						switch($MOVIMIENTOS[$i]['operacion']){
								case "AP":
										$aux="APERTURA";
										break;
								case "AU":
										$aux="AUMENTO";
										break;
								case "DI":
										$aux="DISMINUCIÓN";
										break;
								case "C":
										$aux="COMPROMETIDO";
										break;
								case "CC":
										$aux="COMPROMETIDO\nCAUSADO";
										break;
								case "CCP":
										$aux="COMPROMETIDO\nCAUSADO\nPAGADO";
										break;
								case "P":
										$aux="PAGADO";
										break;
								case "GC":
										$aux="CAUSADO";
										break;
						}
	
						if($aux=="")
								continue;
	
						if($MOVIMIENTOS[$i]['operacion']=="AP" or $MOVIMIENTOS[$i]['operacion']=="AU")
								$SALDO+=$MOVIMIENTOS[$i]["monto"];
						else if($MOVIMIENTOS[$i]['operacion']=="DI" or $MOVIMIENTOS[$i]['operacion']=="C" or $MOVIMIENTOS[$i]['operacion']=="CC" or $MOVIMIENTOS[$i]['operacion']=="CCP")//el causado y el pagado no afecta la disponibilidad
								$SALDO-=$MOVIMIENTOS[$i]["monto"];
	
	
	
						$pdf->SetFont('helvetica','',8);
			
						$pdf->SetDrawColor(200,200,200);
			
						$pdf->Cell($pdf->t_fecha,4,utf8_decode(formatDate($MOVIMIENTOS[$i]["fecha"])),'T',0,'C',1);
						$pdf->Cell($pdf->t_comprobante,4,utf8_decode($MOVIMIENTOS[$i]["tipo"]."-".$MOVIMIENTOS[$i]["correlativo"]),'T',0,'C',1);
						$y=$pdf->GetY();
						$x1=$pdf->GetX();
						$pdf->Cell($pdf->t_denominacion,4,'','T',0,'C',1);
						$x2=$pdf->GetX();
						$pdf->Cell($pdf->t_operacion,4,'','T',0,'C',1);
						$pdf->Cell($pdf->t_monto,4,utf8_decode(number_format($MOVIMIENTOS[$i]["monto"],2,",",".")),'T',0,'R',1);
						$pdf->Cell($pdf->t_saldo,4,utf8_decode(number_format($SALDO,2,",",".")),'T',1,'R',1);
			
			
						$pdf->SetDrawColor(0,0,0);
			
						$pdf->SetXY($x1,$y+0.5);
						$pdf->SetFont('helvetica','',7);
						$pdf->MultiCell($pdf->t_denominacion,3,utf8_decode(trim($MOVIMIENTOS[$i]["concepto"],".")."."),'','L',0);
						$yfin_1=$pdf->GetY();
	
	
	
						$pdf->SetXY($x2,$y+0.5);
						$pdf->SetFont('helvetica','',7);
						$pdf->MultiCell($pdf->t_operacion,3,utf8_decode(trim($aux)),'','C',0);
						$yfin_2=$pdf->GetY();
			
			
						$mayor=max($yfin_1,$yfin_2);
						$pdf->SetY($mayor+1);
				
				
		
						if($mayor>=250){
								$swP=false;
								if($c!=(count($PARTIDAS)-1)){
										$swP=true;
										$pdf->AddPage();
								}
								if($i!=(count($MOVIMIENTOS)-1)){//para el ultimo caso, no mostrar la cabecera de la tabla. si es el caso
										if($swP==false)
												$pdf->AddPage();
										$pdf->CabeceraTabla();
								}
						}
			
				}			
				$pdf->Ln(5);
		}//for($c=0;$c<count($PARTIDAS);$c++)
}



$pdf->Output("libro_mayor.pdf","I");

?>