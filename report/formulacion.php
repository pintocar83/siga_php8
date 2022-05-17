<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/fpdf/1.7/fpdf.php");

$db=SIGA::DBController();

$organismo=$db->Execute("SELECT P.identificacion_tipo||P.identificacion_numero as identificacion
                          FROM
                            modulo_base.organismo AS O,
                            modulo_base.persona as P
                          WHERE
                            O.id_persona=P.id");



$tipo=SIGA::paramGet("tipo");
$opcion=SIGA::paramGet("opcion");
$ocultar=SIGA::paramGet("ocultar");
$id_accion_centralizada=SIGA::paramGet("id_accion_centralizada");
$id_accion_especifica=SIGA::paramGet("id_accion_especifica");
$id_accion_subespecifica=SIGA::paramGet("id_accion_subespecifica");
$id_fuente_recursos=SIGA::paramGet("id_fuente_recursos");

$anio=SIGA::data();

$decimales=0;

$ente_codigo="";
$ente_denominacion="";
$ente_adscripcion="";


$OTRA_DESCRIPCION="";

switch($opcion){
		case 1://consolidado general de proyectos y acciones centralizadas
			$TITULO='CONSOLIDADO GENERAL';
			$NOMBRE_PROYECTO_ACCION="PROYECTOS Y ACCIONES CENTRALIZADAS";
			$DESCRIPCION_ESPECIFICA="";
			
			$sql="SELECT
            CP.denominacion,
            CP.padre,
						FD.id_cuenta_presupuestaria,
            sum(FD.monto[1]+FD.monto[2]+FD.monto[3]) as monto_t1,
            sum(FD.monto[4]+FD.monto[5]+FD.monto[6]) as monto_t2,
            sum(FD.monto[7]+FD.monto[8]+FD.monto[9]) as monto_t3,
            sum(FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto_t4,
            sum((FD.monto[1]+FD.monto[2]+FD.monto[3]+FD.monto[4]+FD.monto[5]+FD.monto[6]+FD.monto[7]+FD.monto[8]+FD.monto[9]+FD.monto[10]+FD.monto[11]+FD.monto[12])) as monto,
						sum(monto_real) as monto_real,
						sum(monto_estimado) as monto_estimado
          FROM
            modulo_base.formulacion as F,
            modulo_base.formulacion_detalle as FD,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
						FD.id_cuenta_presupuestaria ilike '4%' AND
            F.id=FD.id_formulacion AND
            FD.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            F.anio='$anio' AND
            F.tipo='$tipo'
          GROUP BY CP.denominacion, CP.padre, FD.id_cuenta_presupuestaria
          ORDER BY
            FD.id_cuenta_presupuestaria";
				
		break;
		case 2://consolidado general de acciones centralizadas
			$TITULO='CONSOLIDADO GENERAL';
			$NOMBRE_PROYECTO_ACCION="ACCIONES CENTRALIZADAS";
			$DESCRIPCION_ESPECIFICA="";
			$sql="SELECT
            --F.*,
            --FD.*,
            CP.denominacion,
            CP.padre,
						FD.id_cuenta_presupuestaria,
            sum(FD.monto[1]+FD.monto[2]+FD.monto[3]) as monto_t1,
            sum(FD.monto[4]+FD.monto[5]+FD.monto[6]) as monto_t2,
            sum(FD.monto[7]+FD.monto[8]+FD.monto[9]) as monto_t3,
            sum(FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto_t4,
            sum((FD.monto[1]+FD.monto[2]+FD.monto[3]+FD.monto[4]+FD.monto[5]+FD.monto[6]+FD.monto[7]+FD.monto[8]+FD.monto[9]+FD.monto[10]+FD.monto[11]+FD.monto[12])) as monto,
						sum(monto_real) as monto_real,
						sum(monto_estimado) as monto_estimado
          FROM
            modulo_base.formulacion as F,
            modulo_base.formulacion_detalle as FD,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
						FD.id_cuenta_presupuestaria ilike '4%' AND
            F.id=FD.id_formulacion AND
            FD.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            F.anio='$anio' AND
            F.tipo='$tipo' AND
						F.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AC.tipo='ACC'
																								)
          GROUP BY CP.denominacion, CP.padre, FD.id_cuenta_presupuestaria
          ORDER BY
            FD.id_cuenta_presupuestaria";
		break;
		case 3://consolidado general de proyectos
			$TITULO='CONSOLIDADO GENERAL';
			$NOMBRE_PROYECTO_ACCION="PROYECTOS";
			$DESCRIPCION_ESPECIFICA="";
			$sql="SELECT
            F.*,
            FD.*,
            CP.denominacion,
            CP.padre,
						FD.id_cuenta_presupuestaria,
            FD.monto[1]+FD.monto[2]+FD.monto[3] as monto_t1,
            FD.monto[4]+FD.monto[5]+FD.monto[6] as monto_t2,
            FD.monto[7]+FD.monto[8]+FD.monto[9] as monto_t3,
            FD.monto[10]+FD.monto[11]+FD.monto[12] as monto_t4,
            (FD.monto[1]+FD.monto[2]+FD.monto[3]+FD.monto[4]+FD.monto[5]+FD.monto[6]+FD.monto[7]+FD.monto[8]+FD.monto[9]+FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto,
						monto_real,
						monto_estimado
          FROM
            modulo_base.formulacion as F,
            modulo_base.formulacion_detalle as FD,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
						FD.id_cuenta_presupuestaria ilike '4%' AND
            F.id=FD.id_formulacion AND
            FD.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            F.anio='$anio' AND
            F.tipo='$tipo' AND
						F.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AC.tipo='PRO'
																								)
          ORDER BY
            FD.id_cuenta_presupuestaria";
		break;
		case 4://consolidado general de proyecto especifico (ID_AC_PRO)
			$sql="SELECT
            --F.*,
            --FD.*,
            CP.denominacion,
            CP.padre,
						FD.id_cuenta_presupuestaria,
            sum(FD.monto[1]+FD.monto[2]+FD.monto[3]) as monto_t1,
            sum(FD.monto[4]+FD.monto[5]+FD.monto[6]) as monto_t2,
            sum(FD.monto[7]+FD.monto[8]+FD.monto[9]) as monto_t3,
            sum(FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto_t4,
            sum(FD.monto[1]+FD.monto[2]+FD.monto[3]+FD.monto[4]+FD.monto[5]+FD.monto[6]+FD.monto[7]+FD.monto[8]+FD.monto[9]+FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto,
						sum(monto_real) as monto_real,
						sum(monto_estimado) as monto_estimado
          FROM
            modulo_base.formulacion as F,
            modulo_base.formulacion_detalle as FD,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
						FD.id_cuenta_presupuestaria ilike '4%' AND
            F.id=FD.id_formulacion AND
            FD.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            F.anio='$anio' AND
            F.tipo='$tipo' AND
						F.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AC.id='$id_accion_centralizada'
																								)
          GROUP BY CP.denominacion, CP.padre, FD.id_cuenta_presupuestaria
          ORDER BY
            FD.id_cuenta_presupuestaria";
			
			$TITULO='CONSOLIDADO';
			$DENOMINACION=$db->Execute("select denominacion_centralizada from modulo_base.accion_centralizada where id='$id_accion_centralizada'");

			$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0][0];
			$DESCRIPCION_ESPECIFICA="";
		break;
		case 5://proyecto y especifica $_GET["ID_AC_PRO"], $_GET["ID_ESPECIFICA"]
			
			$sql="SELECT
            --F.*,
            --FD.*,
            CP.denominacion,
            CP.padre,
						FD.id_cuenta_presupuestaria,
            sum(FD.monto[1]+FD.monto[2]+FD.monto[3]) as monto_t1,
            sum(FD.monto[4]+FD.monto[5]+FD.monto[6]) as monto_t2,
            sum(FD.monto[7]+FD.monto[8]+FD.monto[9]) as monto_t3,
            sum(FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto_t4,
            sum(FD.monto[1]+FD.monto[2]+FD.monto[3]+FD.monto[4]+FD.monto[5]+FD.monto[6]+FD.monto[7]+FD.monto[8]+FD.monto[9]+FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto,
						sum(monto_real) as monto_real,
						sum(monto_estimado) as monto_estimado
          FROM
            modulo_base.formulacion as F,
            modulo_base.formulacion_detalle as FD,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
						FD.id_cuenta_presupuestaria ilike '4%' AND
            F.id=FD.id_formulacion AND
            FD.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            F.anio='$anio' AND
            F.tipo='$tipo' AND
						F.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AE.id='$id_accion_especifica' 
																								)
          GROUP BY CP.denominacion, CP.padre, FD.id_cuenta_presupuestaria
          ORDER BY
            FD.id_cuenta_presupuestaria";
			$TITULO='';
			$DENOMINACION=$db->Execute("select denominacion_centralizada from modulo_base.accion_centralizada where id='$id_accion_centralizada'");
			$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0][0];
			
			$DENOMINACION=$db->Execute("select codigo_especifica, denominacion_especifica from modulo_base.accion_especifica where id='$id_accion_especifica'");
			$DESCRIPCION_ESPECIFICA=$DENOMINACION[0]["codigo_especifica"]." ".$DENOMINACION[0]["denominacion_especifica"].".";

		break;
	case 6://proyecto, especifica y otra especifica    $_GET["ID_EP"]
			$sql="SELECT
            F.*,
            FD.*,
            CP.denominacion,
            CP.padre,
						FD.id_cuenta_presupuestaria,
            FD.monto[1]+FD.monto[2]+FD.monto[3] as monto_t1,
            FD.monto[4]+FD.monto[5]+FD.monto[6] as monto_t2,
            FD.monto[7]+FD.monto[8]+FD.monto[9] as monto_t3,
            FD.monto[10]+FD.monto[11]+FD.monto[12] as monto_t4,
            (FD.monto[1]+FD.monto[2]+FD.monto[3]+FD.monto[4]+FD.monto[5]+FD.monto[6]+FD.monto[7]+FD.monto[8]+FD.monto[9]+FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto,
						monto_real,
						monto_estimado
          FROM
            modulo_base.formulacion as F,
            modulo_base.formulacion_detalle as FD,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
						FD.id_cuenta_presupuestaria ilike '4%' AND
            F.id=FD.id_formulacion AND
            FD.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            F.anio='$anio' AND
            F.tipo='$tipo' AND
						F.id_fuente_recursos='$id_fuente_recursos' AND
						F.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AE.id='$id_accion_especifica' 
																								)
          ORDER BY
            FD.id_cuenta_presupuestaria";
						
			$TITULO='';
			$DENOMINACION=$db->Execute("select
																		denominacion_centralizada, codigo_especifica, denominacion_especifica
																 from modulo_base.accion_centralizada as AC, modulo_base.accion_especifica as AE
																 where AC.id=AE.id_accion_centralizada AND AE.id='$id_accion_especifica'");
			$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0]["denominacion_centralizada"];		
			$DESCRIPCION_ESPECIFICA=$DENOMINACION[0]["codigo_especifica"]." ".$DENOMINACION[0]["denominacion_especifica"].".";
			
			$DENOMINACION=$db->Execute("select codigo_fuente, denominacion_fuente from modulo_base.fuente_recursos where id='$id_fuente_recursos'");
			$DESCRIPCION_ESPECIFICA=$DESCRIPCION_ESPECIFICA."  ".$DENOMINACION[0]["codigo_fuente"]." ".$DENOMINACION[0]["denominacion_fuente"];
			
			//$DENOMINACION=$db->Execute("select codigo_subespecifica, denominacion_subespecifica from modulo_base.accion_subespecifica where id='$id_accion_subespecifica'");
			//$DESCRIPCION_ESPECIFICA=$DESCRIPCION_ESPECIFICA." - ".$DENOMINACION[0]["codigo_subespecifica"].".- ".$DENOMINACION[0]["denominacion_subespecifica"].".";

		break;
		case 7://proyecto, otra especifica por codigo
			$sql="SELECT
            F.*,
            FD.*,
            CP.denominacion,
            CP.padre,
						FD.id_cuenta_presupuestaria,
            FD.monto[1]+FD.monto[2]+FD.monto[3] as monto_t1,
            FD.monto[4]+FD.monto[5]+FD.monto[6] as monto_t2,
            FD.monto[7]+FD.monto[8]+FD.monto[9] as monto_t3,
            FD.monto[10]+FD.monto[11]+FD.monto[12] as monto_t4,
            (FD.monto[1]+FD.monto[2]+FD.monto[3]+FD.monto[4]+FD.monto[5]+FD.monto[6]+FD.monto[7]+FD.monto[8]+FD.monto[9]+FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto,
						monto_real,
						monto_estimado
          FROM
            modulo_base.formulacion as F,
            modulo_base.formulacion_detalle as FD,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
						FD.id_cuenta_presupuestaria ilike '4%' AND
            F.id=FD.id_formulacion AND
            FD.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria AND
            F.anio='$anio' AND
            F.tipo='$tipo' AND
						F.id_fuente_recursos='$id_fuente_recursos' AND
						F.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AC.id='$id_accion_centralizada' 
																								)
          ORDER BY
            FD.id_cuenta_presupuestaria";
				
				$TITULO='';
				$DENOMINACION=$db->Execute("select denominacion_centralizada from modulo_base.accion_centralizada where id='$id_accion_centralizada'");
				$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0][0];
				
				$DENOMINACION=$db->Execute("select codigo_fuente, denominacion_fuente from modulo_base.fuente_recursos where id='$id_fuente_recursos'");
				$DESCRIPCION_ESPECIFICA=$DENOMINACION[0]["codigo_fuente"]." ".$DENOMINACION[0]["denominacion_fuente"];
		break;

	default:
		exit;
	}

$FORMULACION_TMP=$db->Execute($sql);


$FORMULACION=array();
$n=0;

for($i=0;$i<count($FORMULACION_TMP);$i++){
		AgregarEspecifica($FORMULACION_TMP[$i]);
}





function AgregarEspecifica($reg){
		global $FORMULACION, $n, $db;
		
		$FORMULACION[]=$reg;
		$n++;
		
		$aux_codigo=$reg["id_cuenta_presupuestaria"];
		$padre=array();		
		$padre[0]=$aux_codigo;
		$padre[1]=substr($aux_codigo,0,1)."00000000";
		$padre[2]=substr($aux_codigo,0,3)."000000";
		$padre[3]=substr($aux_codigo,0,5)."0000";
		$padre[4]=substr($aux_codigo,0,7)."00";
		if($padre[4]==$aux_codigo)
			$k=4;
		else
			$k=5;
			
		//print_r($FORMULACION);

		for($i=1;$i<$k;$i++){
				$sw=false;
				for($j=0;$j<$n;$j++){
						if($FORMULACION[$j]["id_cuenta_presupuestaria"]==$padre[$i]){
								$FORMULACION[$j]["monto_t1"]+=$reg["monto_t1"];
								$FORMULACION[$j]["monto_t2"]+=$reg["monto_t2"];
								$FORMULACION[$j]["monto_t3"]+=$reg["monto_t3"];
								$FORMULACION[$j]["monto_t4"]+=$reg["monto_t4"];
								$FORMULACION[$j]["monto"]+=$reg["monto"];
								$FORMULACION[$j]["monto_real"]+=$reg["monto_real"];
								$FORMULACION[$j]["monto_estimado"]+=$reg["monto_estimado"];
								$sw=true;
								break;
						}
				}
				//sino la encontro agregarla
				if($sw==false){
					$FORMULACION[$n]=array();			
					$FORMULACION[$n]["id_cuenta_presupuestaria"]=$padre[$i];
					$FORMULACION[$n]["denominacion"]="";
					
					$CUENTA_PRESUPUESTARIA=$db->Execute("select denominacion from modulo_base.cuenta_presupuestaria where id_cuenta_presupuestaria='".$padre[$i]."'");
					$FORMULACION[$n]["denominacion"]=$CUENTA_PRESUPUESTARIA[0][0];
					
					$FORMULACION[$n]["padre"]='t';
					$FORMULACION[$n]["monto_t1"]=$reg["monto_t1"];
					$FORMULACION[$n]["monto_t2"]=$reg["monto_t2"];
					$FORMULACION[$n]["monto_t3"]=$reg["monto_t3"];
					$FORMULACION[$n]["monto_t4"]=$reg["monto_t4"];
					$FORMULACION[$n]["monto"]=$reg["monto"];
					$FORMULACION[$n]["monto_real"]=$reg["monto_real"];
					$FORMULACION[$n]["monto_estimado"]=$reg["monto_estimado"];
					$n++;
					}
		}
}


//sort($FORMULACION);

function formulacion_ordenar($a, $b){
		return strcmp($a["id_cuenta_presupuestaria"],$b["id_cuenta_presupuestaria"]);
}


usort($FORMULACION, 'formulacion_ordenar');





$pdf=new FPDF();

$MARGEN_LEFT=20;
$MARGEN_TOP=25;

$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);

$pdf->AddPage();
$pdf->SetFont('helvetica','',5);
$pdf->SetFillColor(255,255,255);


//busco cuantos \n hay en la cadena y cuantos con necesarios si se pasa del tamaño 78
//buscar cual es el max entre las 2 cadena y completarla para que queden del mismo alto.

$STR_PARTE_A="CÓDIGO DEL ENTE: $ente_codigo.\nDENOMINACIÓN: $ente_denominacion.\nORGANISMO DE ADSCRIPCIÓN: $ente_adscripcion.\nPROYECTO O ACCIÓN CENTRALIZADA: $DESCRIPCION_ESPECIFICA\nPRESUPUESTO $anio.\n";
$STR_PARTE_B="$TITULO\n$NOMBRE_PROYECTO_ACCION\n$OTRA_DESCRIPCION";

$ARREGLO_A=explode("\n",$STR_PARTE_A);
$ARREGLO_B=explode("\n",$STR_PARTE_B);
$N_ENTER_A=substr_count($STR_PARTE_A,"\n");
$N_ENTER_B=substr_count($STR_PARTE_B,"\n");



for($i=0;$i<count($ARREGLO_A);$i++){
	$TAM_A=$pdf->GetStringWidth(utf8_decode($ARREGLO_A[$i]));
	$N_LINEA_A=ceil($TAM_A/(80));
	if($N_LINEA_A>1)
		$N_ENTER_A+=$N_LINEA_A-1;
	}

$pdf->SetFont('helvetica','B',7);
for($i=0;$i<count($ARREGLO_B);$i++){
	$TAM_B=$pdf->GetStringWidth(utf8_decode($ARREGLO_B[$i]));
	$N_LINEA_B=ceil($TAM_B/(80));
	if($N_LINEA_B>1)
		$N_ENTER_B+=$N_LINEA_B-1;
	}



if($N_ENTER_A>$N_ENTER_B)
	$MAX=$N_ENTER_A;
else
	$MAX=$N_ENTER_B;

$DIFF_A=$MAX-$N_ENTER_A;
$DIFF_B=$MAX-$N_ENTER_B;

$AUX_A="";
$AUX_B="";

for($i=0;$i<=$DIFF_A;$i++)
	$AUX_A.="\n";
for($i=0;$i<=$DIFF_B;$i++)
	$AUX_B.="\n";


$pdf->SetFont('helvetica','',5);
$pdf->MultiCell(88,3,utf8_decode($STR_PARTE_A.$AUX_A),'LRTB','J',1);
$pdf->SetXY($MARGEN_LEFT+88,$MARGEN_TOP);
$pdf->SetFont('helvetica','B',7);
$pdf->MultiCell(87,3,utf8_decode($STR_PARTE_B.$AUX_B),'LRTB','C',1);

$pdf->SetFont('helvetica','',5);

//ancho=175





//2da parte del encabezado
$pdf->Cell(18,9,utf8_decode('CÓDIGO'),'LRBT',0,'C',1);

$pdf->Cell(70,9,utf8_decode('DENOMINACIÓN'),'LRTB',0,'C',1);

$pdf->Cell(15,3,utf8_decode('AÑO REAL'),'LRT',0,'C',1);
$pdf->Cell(15,3,utf8_decode('ÚLTIMO AÑO'),'LRT',0,'C',1);
$pdf->Cell(57,3,utf8_decode(''),'LRTB',1,'C',1);

$pdf->Cell(88,0,utf8_decode(''),0,0,'',1);
$pdf->Cell(15,3,utf8_decode(''),'LR',0,'C',1);
$pdf->Cell(15,3,utf8_decode('ESTIMADO'),'LR',0,'C',1);
$pdf->Cell(57,3,utf8_decode("AÑO PRESUPUESTADO $anio"),'LRTB',1,'C',1);

$pdf->Cell(88,0,'',0,0,'',1);
$pdf->Cell(15,3,($anio-2),'LRB',0,'C',1);
$pdf->Cell(15,3,($anio-1),'LRB',0,'C',1);
$pdf->Cell(11.4,3,'I TRIM','LRTB',0,'C',1);
$pdf->Cell(11.4,3,'II TRIM','LRTB',0,'C',1);
$pdf->Cell(11.4,3,'III TRIM','LRTB',0,'C',1);
$pdf->Cell(11.4,3,'IV TRIM','LRTB',0,'C',1);
$pdf->Cell(11.4,3,'TOTAL','LRTB',1,'C',1);

//generar filas, desde la pos 1, la pos 0 tiene los totales generales
for($i=1;$i<$n;$i++){
	if($ocultar)
		if($FORMULACION[$i]["monto_real"]==0 and $FORMULACION[$i]["monto_estimado"]==0 and $FORMULACION[$i]["monto_t1"]==0 and $FORMULACION[$i]["monto_t2"]==0 and $FORMULACION[$i]["monto_t3"]==0 and $FORMULACION[$i]["monto_t4"]==0 and $FORMULACION[$i]["monto"]==0)
			continue;

	if($FORMULACION[$i]["padre"]=="t")
		$pdf->SetFont('helvetica','B',5);
	else
		$pdf->SetFont('helvetica','',5);

	$CODIGO=$FORMULACION[$i]["id_cuenta_presupuestaria"];
	$pdf->Cell(4.8,3,substr($CODIGO,0,3),'LRTB',0,'C',1);
	$pdf->Cell(4.4,3,substr($CODIGO,3,2),'LRTB',0,'C',1);
	$pdf->Cell(4.4,3,substr($CODIGO,5,2),'LRTB',0,'C',1);
	$pdf->Cell(4.4,3,substr($CODIGO,7,2),'LRTB',0,'C',1);

	$pdf->Cell(69,3,utf8_decode($FORMULACION[$i]["denominacion"]),'LTB',0,'L',1);
	$pdf->Cell(1,3,($FORMULACION[$i]["denominacion"]),'RTB',0,'L',1);

	$pdf->Cell(15,3,number_format($FORMULACION[$i]["monto_real"],$decimales,",","."),'LRTB',0,'R',1);
	$pdf->Cell(15,3,number_format($FORMULACION[$i]["monto_estimado"],$decimales,",","."),'LRTB',0,'R',1);
	$pdf->Cell(11.4,3,number_format($FORMULACION[$i]["monto_t1"],$decimales,",","."),'LRTB',0,'R',1);
	$pdf->Cell(11.4,3,number_format($FORMULACION[$i]["monto_t2"],$decimales,",","."),'LRTB',0,'R',1);
	$pdf->Cell(11.4,3,number_format($FORMULACION[$i]["monto_t3"],$decimales,",","."),'LRTB',0,'R',1);
	$pdf->Cell(11.4,3,number_format($FORMULACION[$i]["monto_t4"],$decimales,",","."),'LRTB',0,'R',1);
	$pdf->Cell(11.4,3,number_format($FORMULACION[$i]["monto"],$decimales,",","."),'LRTB',0,'R',1);
	$pdf->Cell(20,3,'','L',1,'R',1);
	}

//Fila de totales
$pdf->SetFont('helvetica','B',5);
$pdf->Cell(88,3,"TOTALES",'LRTB',0,'C',1);
$pdf->Cell(15,3,number_format($FORMULACION[0]["monto_real"],$decimales,",","."),'LRTB',0,'R',1);
$pdf->Cell(15,3,number_format($FORMULACION[0]["monto_estimado"],$decimales,",","."),'LRTB',0,'R',1);
$pdf->Cell(11.4,3,number_format($FORMULACION[0]["monto_t1"],$decimales,",","."),'LRTB',0,'R',1);
$pdf->Cell(11.4,3,number_format($FORMULACION[0]["monto_t2"],$decimales,",","."),'LRTB',0,'R',1);
$pdf->Cell(11.4,3,number_format($FORMULACION[0]["monto_t3"],$decimales,",","."),'LRTB',0,'R',1);
$pdf->Cell(11.4,3,number_format($FORMULACION[0]["monto_t4"],$decimales,",","."),'LRTB',0,'R',1);
$pdf->Cell(11.4,3,number_format($FORMULACION[0]["monto"],$decimales,",","."),'LRTB',1,'R',1);

$salida="formulacion";
if($tipo!="F")
		$salida="reformulacion";

$pdf->Output("$salida.pdf","I");

?>

