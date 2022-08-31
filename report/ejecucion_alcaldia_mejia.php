<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once("../library/db.controller.php");
include_once("../library/siga.config.php");
include_once("../library/siga.class.php");
include_once("../library/fpdf/1.84/rotation.php");
include_once("../library/functions/bisiesto.php");

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
$periodo_tipo=SIGA::paramGet("periodotipo");
$periodo=SIGA::paramGet("periodo");



$anio=SIGA::data();



$decimales=2;

$ente_codigo="E6811";
$ente_denominacion="ALCALDÍA BOLIVARIANA DEL MUNICIPIO MEJIA";
$ente_adscripcion="SUCRE";

$trimestre_n=$periodo;


$NOMBRE_PROYECTO_ACCION="";

switch($periodo_tipo){//trimestral
	case "T":
		switch($periodo){
			case 1: $fecha_inicio="$anio-01-01";$fecha_final="$anio-03-31";$trimestre="ENERO - MARZO";$col_prog_trim="monto_trimestre_i";break;
			case 2: $fecha_inicio="$anio-04-01";$fecha_final="$anio-06-30";$trimestre="ABRIL - JUNIO";$col_prog_trim="monto_trimestre_ii";break;
			case 3: $fecha_inicio="$anio-07-01";$fecha_final="$anio-09-30";$trimestre="JULIO - SEPTIEMBRE";$col_prog_trim="monto_trimestre_iii";break;
			case 4: $fecha_inicio="$anio-10-01";$fecha_final="$anio-12-31";$trimestre="OCTUBRE - DICIEMBRE";$col_prog_trim="monto_trimestre_iv";break;
			default: exit;
			}





		break;
	case "M":
		switch($periodo){
			case 1: $fecha_inicio="$anio-01-01";$fecha_final="$anio-01-31";$trimestre="ENERO";$col_prog_trim="monto_trimestre_i";break;
			case 2: $fecha_inicio="$anio-02-01";
					if(bisiesto($anio))$fecha_final="$anio-02-29";
					else $fecha_final="$anio-02-28";
					$trimestre="FEBRERO";$col_prog_trim="monto_trimestre_i";break;
			case 3: $fecha_inicio="$anio-03-01";$fecha_final="$anio-03-31";$trimestre="MARZO";$col_prog_trim="monto_trimestre_i";break;
			case 4: $fecha_inicio="$anio-04-01";$fecha_final="$anio-04-30";$trimestre="ABRIL";$col_prog_trim="monto_trimestre_ii";break;
			case 5: $fecha_inicio="$anio-05-01";$fecha_final="$anio-05-31";$trimestre="MAYO";$col_prog_trim="monto_trimestre_ii";break;
			case 6: $fecha_inicio="$anio-06-01";$fecha_final="$anio-06-30";$trimestre="JUNIO";$col_prog_trim="monto_trimestre_ii";break;
			case 7: $fecha_inicio="$anio-07-01";$fecha_final="$anio-07-31";$trimestre="JULIO";$col_prog_trim="monto_trimestre_iii";break;
			case 8: $fecha_inicio="$anio-08-01";$fecha_final="$anio-08-31";$trimestre="AGOSTO";$col_prog_trim="monto_trimestre_iii";break;
			case 9: $fecha_inicio="$anio-09-01";$fecha_final="$anio-09-30";$trimestre="SEPTIEMBRE";$col_prog_trim="monto_trimestre_iii";break;
			case 10:$fecha_inicio="$anio-10-01";$fecha_final="$anio-10-31";$trimestre="OCTUBRE";$col_prog_trim="monto_trimestre_iv";break;
			case 11:$fecha_inicio="$anio-11-01";$fecha_final="$anio-11-30";$trimestre="NOVIEMBRE";$col_prog_trim="monto_trimestre_iv";break;
			case 12:$fecha_inicio="$anio-12-01";$fecha_final="$anio-12-31";$trimestre="DICIEMBRE";$col_prog_trim="monto_trimestre_iv";break;
			default: exit;
			}   		
		break;
//  case 3:
//		list($FI,$FF)=explode("|",$trimestre_n);
//		
//		$FI=explode("/",$FI);
//		$FF=explode("/",$FF);
//		$fecha_inicio="$anio-".$FI[1]."-".$FI[0];
//		$fecha_final="$anio-".$FF[1]."-".$FF[0];
//		$trimestre="DEL ".$FI[0]."/".$FI[1]."/$anio"." AL ".$FF[0]."/".$FF[1]."/$anio";
//		$col_prog_trim="monto_trimestre_i";
//
//		break;
	default:
		exit;
	}

$condicion1="";
$condicion2="";

switch($opcion){
	case 1://consolidado general de proyectos y acciones centralizadas
		$condicion1="";
		$condicion2="";
		//$titulo="CONSOLIDADO EJECUCIÓN FINANCIERA TRIMESTRAL DE PROYECTOS Y ACCIONES CENTRALIZADAS POR PARTIDAS";
		$titulo="CONSOLIDADO TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";
	break;
	case 2://consolidado general de acciones centralizadas
		$condicion1=" AND DP.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AC.tipo='ACC'
																								)";
		//$condicion2=" AND F.id_estructura_presupuestaria IN (SELECT
		//															EP.id_estruc_presupuestaria
		//														FROM
		//															modulo_presupuesto.estruc_presupuestaria AS EP,
		//															modulo_presupuesto.accion_centralizada AS AC
		//														WHERE
		//															EP.id_acciones_centralizadas=AC.id_accion_centralizada AND
		//															AC.codigo_centralizada LIKE 'ACC%')";
		//$titulo="CONSOLIDADO EJECUCIÓN FINANCIERA TRIMESTRAL DE ACCIONES CENTRALIZADAS POR PARTIDAS";
		$titulo="CONSOLIDADO TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";
	break;
	case 3://consolidado general de proyectos
		$condicion1=" AND DP.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AC.tipo='PRO'
																								)";
		//$condicion2=" AND F.id_estructura_presupuestaria IN (SELECT
		//															EP.id_estruc_presupuestaria
		//														FROM
		//															modulo_presupuesto.estruc_presupuestaria AS EP,
		//															modulo_presupuesto.accion_centralizada AS AC
		//														WHERE
		//															EP.id_acciones_centralizadas=AC.id_accion_centralizada AND
		//															AC.codigo_centralizada LIKE 'PRO%')";
		//$titulo="CONSOLIDADO EJECUCIÓN FINANCIERA TRIMESTRAL DE PROYECTOS POR PARTIDAS";
		$titulo="CONSOLIDADO TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";
	break;
	case 4://consolidado general de proyecto especifico (ID_AC_PRO)
		$condicion1=" AND DP.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AC.id='$id_accion_centralizada'
																								)";
		//$condicion2=" AND F.id_estructura_presupuestaria IN (SELECT
		//															EP.id_estruc_presupuestaria
		//														FROM
		//															modulo_presupuesto.estruc_presupuestaria AS EP
		//														WHERE
		//															EP.id_acciones_centralizadas=".$_GET["ID_AC_PRO"].")";
		//$titulo="CONSOLIDADO EJECUCIÓN FINANCIERA TRIMESTRAL DE PROYECTOS Y ACCIONES CENTRALIZADAS POR PARTIDAS";
		$titulo="CONSOLIDADO TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";
		//$sql2="SELECT denominacion_centralizada FROM modulo_presupuesto.accion_centralizada WHERE id_accion_centralizada=".$_GET["ID_AC_PRO"]."";
		//$DENOMINACION=$bd->consultar($sql2,'ARREGLO');
		//$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0]["denominacion_centralizada"];
		$DENOMINACION=$db->Execute("select denominacion_centralizada from modulo_base.accion_centralizada where id='$id_accion_centralizada'");
		$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0][0];
		$DESCRIPCION_ESPECIFICA="";
	break;
	case 5://proyecto y especifica $_GET["ID_AC_PRO"], $_GET["ID_ESPECIFICA"]
		$condicion1=" AND DP.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AE.id='$id_accion_especifica' 
																								)";
		
		
		//$condicion1=" AND MP.id_estruc_presupuestaria IN (SELECT
		//														EP.id_estruc_presupuestaria
		//													FROM
		//														modulo_presupuesto.estruc_presupuestaria AS EP
		//													WHERE
		//														EP.id_acciones_centralizadas=".$_GET["ID_AC_PRO"]." AND
		//														EP.id_accion_especifica=".$_GET["ID_ESPECIFICA"].")";
		//$condicion2=" AND F.id_estructura_presupuestaria IN (SELECT
		//															EP.id_estruc_presupuestaria
		//														FROM
		//															modulo_presupuesto.estruc_presupuestaria AS EP
		//														WHERE
		//															EP.id_acciones_centralizadas=".$_GET["ID_AC_PRO"]." AND
		//															EP.id_accion_especifica=".$_GET["ID_ESPECIFICA"].")";
		//$titulo="CONSOLIDADO EJECUCIÓN FINANCIERA TRIMESTRAL DE PROYECTOS Y ACCIONES CENTRALIZADAS POR PARTIDAS";
		$titulo="CONSOLIDADO TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";
		//$sql2="SELECT denominacion_centralizada FROM modulo_presupuesto.accion_centralizada WHERE id_accion_centralizada=".$_GET["ID_AC_PRO"];
		//$DENOMINACION=$bd->consultar($sql2,'ARREGLO');
		//$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0]["denominacion_centralizada"];

		//$sql2="SELECT to_char(codigo_especifico,'00') AS codigo_especifico,denominacion_especifica FROM modulo_presupuesto.accion_especifica WHERE id_accion_especifica=".$_GET["ID_ESPECIFICA"];
		//$DENOMINACION=$bd->consultar($sql2,'ARREGLO');
		//$DESCRIPCION_ESPECIFICA=$DENOMINACION[0]["codigo_especifico"].".- ".$DENOMINACION[0]["denominacion_especifica"].".";
		$DENOMINACION=$db->Execute("select denominacion_centralizada from modulo_base.accion_centralizada where id='$id_accion_centralizada'");
		$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0][0];
		
		$DENOMINACION=$db->Execute("select codigo_especifica, denominacion_especifica from modulo_base.accion_especifica where id='$id_accion_especifica'");
		$DESCRIPCION_ESPECIFICA=$DENOMINACION[0]["codigo_especifica"]." ".$DENOMINACION[0]["denominacion_especifica"].".";
	break;
	case 6://proyecto y especifica $_GET["ID_AC_PRO"], $_GET["ID_ESPECIFICA"]
		//$condicion1=" AND MP.id_estruc_presupuestaria = ".$_GET["ID_EP"];
		////$condicion2=" AND F.id_estructura_presupuestaria = ".$_GET["ID_EP"];
		//$titulo="CONSOLIDADO TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";
		//$sql2="SELECT AC.denominacion_centralizada
		//			FROM modulo_presupuesto.estruc_presupuestaria AS EP, modulo_presupuesto.accion_centralizada AS AC
		//			WHERE EP.id_estruc_presupuestaria=".$_GET["ID_EP"]." AND EP.id_acciones_centralizadas=AC.id_accion_centralizada";
		//$DENOMINACION=$bd->consultar($sql2,'ARREGLO');
		//$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0]["denominacion_centralizada"];
		//
		//$sql2="SELECT to_char(codigo_especifico,'00') AS codigo_especifico, denominacion_especifica
		//		FROM modulo_presupuesto.accion_especifica AE, modulo_presupuesto.estruc_presupuestaria AS EP
		//		WHERE AE.id_accion_especifica=EP.id_accion_especifica AND EP.id_estruc_presupuestaria=".$_GET["ID_EP"];
		//$DENOMINACION=$bd->consultar($sql2,'ARREGLO');
		//$DESCRIPCION_ESPECIFICA=$DENOMINACION[0]["codigo_especifico"].".- ".$DENOMINACION[0]["denominacion_especifica"];
		//
		//$sql2="SELECT OAE.denominacion_otras
		//		FROM modulo_presupuesto.otras_acciones_especificas OAE, modulo_presupuesto.estruc_presupuestaria AS EP
		//		WHERE OAE.id_otras_acciones_especificas=EP.id_otras_acciones_especificas AND EP.id_estruc_presupuestaria=".$_GET["ID_EP"];
		//$DENOMINACION=$bd->consultar($sql2,'ARREGLO');
		//$DESCRIPCION_ESPECIFICA=$DESCRIPCION_ESPECIFICA." - ".$DENOMINACION[0]["denominacion_otras"];
		$titulo="CONSOLIDADO TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";
		/*$condicion1=" AND DP.id_fuente_recursos='$id_fuente_recursos' AND DP.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AE.id='$id_accion_especifica' 
																								)";*/
		$condicion1=" AND DP.id_accion_subespecifica = '$id_accion_subespecifica'";
		$DENOMINACION=$db->Execute("select
																		denominacion_centralizada,
																		codigo_especifica, denominacion_especifica,
																		codigo_subespecifica, denominacion_subespecifica
																 from
																		modulo_base.accion_centralizada as AC,
																		modulo_base.accion_especifica as AE,
																		modulo_base.accion_subespecifica as ASE
																 where
																		AC.id=AE.id_accion_centralizada AND
																		AE.id=ASE.id_accion_especifica AND
																		ASE.id='$id_accion_subespecifica'");
		$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0]["denominacion_centralizada"].". ";		
		$DESCRIPCION_ESPECIFICA=$DENOMINACION[0]["codigo_especifica"]." ".$DENOMINACION[0]["denominacion_especifica"].".\n".$DENOMINACION[0]["codigo_subespecifica"]." ".$DENOMINACION[0]["denominacion_subespecifica"].".";
		
		//$DENOMINACION=$db->Execute("select codigo_subespecifica, denominacion_subespecifica from modulo_base.accion_subespecifica where id='$id_accion_subespecifica'");
		//$DESCRIPCION_ESPECIFICA=$DESCRIPCION_ESPECIFICA."  \n".$DENOMINACION[0]["codigo_subespecifica"]." ".$DENOMINACION[0]["denominacion_subespecifica"];
		
	break;
	case 7://proyecto y especifica $_GET["ID_AC_PRO"], $_GET["COD_OAE"]
		$condicion1=" AND DP.id_fuente_recursos='$id_fuente_recursos' AND DP.id_accion_subespecifica IN (select
																								ASE.id
																						from
																								modulo_base.accion_centralizada as AC,
																								modulo_base.accion_especifica as AE,
																								modulo_base.accion_subespecifica as ASE
																						where
																								ASE.id_accion_especifica=AE.id and AE.id_accion_centralizada=AC.id and AC.id='$id_accion_centralizada' 
																								)";
		//$condicion1=" AND MP.id_estruc_presupuestaria IN (select
		//														EP.id_estruc_presupuestaria
		//													from
		//														modulo_presupuesto.estruc_presupuestaria AS EP,
		//														modulo_presupuesto.otras_acciones_especificas AS OAE
		//													where
		//														EP.id_acciones_centralizadas=".$_GET["ID_AC_PRO"]." AND
		//														EP.id_otras_acciones_especificas = OAE.id_otras_acciones_especificas AND
		//														OAE.codigo_otras='".$_GET["COD_OAE"]."')";
		////$condicion2=" AND F.id_estructura_presupuestaria IN (select
		////														EP.id_estruc_presupuestaria
		////													from
		////														modulo_presupuesto.estruc_presupuestaria AS EP,
		////														modulo_presupuesto.otras_acciones_especificas AS OAE
		////													where
		////														EP.id_acciones_centralizadas=".$_GET["ID_AC_PRO"]." AND
		////														EP.id_otras_acciones_especificas = OAE.id_otras_acciones_especificas AND
		////														OAE.codigo_otras='".$_GET["COD_OAE"]."')";
		$titulo="CONSOLIDADO EJECUCIÓN FINANCIERA TRIMESTRAL DE PROYECTOS Y ACCIONES CENTRALIZADAS POR PARTIDAS";
		//$sql2="SELECT denominacion_centralizada FROM modulo_presupuesto.accion_centralizada WHERE id_accion_centralizada=".$_GET["ID_AC_PRO"];
		//$DENOMINACION=$bd->consultar($sql2,'ARREGLO');
		//$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0]["denominacion_centralizada"];
		//
		//$sql2="select
		//			OAE.denominacion_otras
		//		from
		//			modulo_presupuesto.estruc_presupuestaria AS EP,
		//			modulo_presupuesto.otras_acciones_especificas AS OAE
		//		where
		//			EP.id_acciones_centralizadas=".$_GET["ID_AC_PRO"]." AND
		//			EP.id_otras_acciones_especificas = OAE.id_otras_acciones_especificas AND
		//			OAE.codigo_otras='".$_GET["COD_OAE"]."'";
		//$DENOMINACION_OTRAS=$bd->consultar($sql2,'ARREGLO');
		//$DESCRIPCION_ESPECIFICA=$NOMBRE_PROYECTO_ACCION.", ".$_GET["COD_OAE"]." ".$DENOMINACION_OTRAS[0]["denominacion_otras"].".";
		$DENOMINACION=$db->Execute("select denominacion_centralizada from modulo_base.accion_centralizada where id='$id_accion_centralizada'");
		$NOMBRE_PROYECTO_ACCION=$DENOMINACION[0][0];
		$DENOMINACION=$db->Execute("select codigo_fuente, denominacion_fuente from modulo_base.fuente_recursos where id='$id_fuente_recursos'");
		$DESCRIPCION_ESPECIFICA=$DENOMINACION[0]["codigo_fuente"]." ".$DENOMINACION[0]["denominacion_fuente"];
	break;
	default:
		exit;
	}



$comprometido=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='C' OR DP.operacion='CC' OR DP.operacion='CCP') AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$fecha_inicio' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");

																
//print_r($comprometido);exit;

$comprometido_acumulado=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='C' OR DP.operacion='CC' OR DP.operacion='CCP') AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");


$causado=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='GC' OR DP.operacion='CC' OR DP.operacion='CCP') AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$fecha_inicio' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");

$causado_acumulado=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='GC' OR DP.operacion='CC' OR DP.operacion='CCP') AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");


$pagado=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='P' OR DP.operacion='CCP') AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$fecha_inicio' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");

$pagado_acumulado=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='P' OR DP.operacion='CCP') AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");


$asignado=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																DP.operacion='AP' AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND
																EXTRACT(YEAR FROM C.fecha)=$anio 
																$condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");

$en_uso=$db->Execute("
select 
	distinct id_cuenta_presupuestaria
FROM
	modulo_base.detalle_presupuestario AS DP,
	modulo_base.comprobante AS C
WHERE
	DP.id_comprobante=C.id AND
	C.contabilizado AND
	EXTRACT(YEAR FROM C.fecha)=$anio 
	$condicion1
ORDER BY
	DP.id_cuenta_presupuestaria
");




$credito=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																DP.operacion='AU' AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND		
																C.tipo='CR' AND												
																C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");
/*
print "SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																DP.operacion='AU' AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND		
																C.tipo='CR' AND												
																C.fecha BETWEEN '$fecha_inicio' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria";exit;*/

$modificado=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(case when DP.operacion='DI' then -DP.monto else DP.monto end) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='AP' OR DP.operacion='AU' OR DP.operacion='DI') AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");

$modificado_au=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(case when DP.operacion='DI' then -DP.monto else DP.monto end) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='AU') AND
																C.tipo<>'CR' AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");

$modificado_di=$db->Execute("SELECT
																DP.id_cuenta_presupuestaria,
																SUM(DP.monto) AS monto
															FROM
																modulo_base.detalle_presupuestario AS DP,
																modulo_base.comprobante AS C
															WHERE
																(DP.operacion='DI') AND
																DP.id_comprobante=C.id AND
																C.contabilizado AND														
																C.fecha BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
															GROUP BY
																DP.id_cuenta_presupuestaria
															ORDER BY
																DP.id_cuenta_presupuestaria");

$database_name=isset(SIGA::$database[SIGA::database()]["name"])?SIGA::$database[SIGA::database()]["name"]:"";
//CASO ESPECIFICO PARA LA ALCALDIA DE MEJIA
if($database_name && preg_grep("/siga_alcaldia_mejia*/i",[$database_name])){
	$pagado=$causado;
	$pagado_acumulado=$causado_acumulado;
}
/*exit;


$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(MP.monto_mp) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			(O.pre_compr_operacion OR O.compr_operacion) AND
			MP.id_comprobante=C.id_comprobante AND
			C.fecha_c BETWEEN '$fecha_inicio' AND '$fecha_final' $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$compromiso=$bd->consultar($sql,'ARREGLO');

$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(MP.monto_mp) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			O.causa_operacion AND
			MP.id_comprobante=C.id_comprobante AND
			C.fecha_c BETWEEN '$fecha_inicio' AND '$fecha_final' $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$causado=$bd->consultar($sql,'ARREGLO');

$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(MP.monto_mp) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			O.paga_operacion AND
			MP.id_comprobante=C.id_comprobante AND
			C.fecha_c BETWEEN '$fecha_inicio' AND '$fecha_final' $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$pagado=$bd->consultar($sql,'ARREGLO');

$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(MP.monto_mp) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			(O.pre_compr_operacion OR O.compr_operacion) AND
			MP.id_comprobante=C.id_comprobante AND
			C.fecha_c BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$acumulado_compromiso=$bd->consultar($sql,'ARREGLO');

$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(MP.monto_mp) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			O.causa_operacion AND
			MP.id_comprobante=C.id_comprobante AND
			C.fecha_c BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$acumulado_causado=$bd->consultar($sql,'ARREGLO');

$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(MP.monto_mp) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			O.paga_operacion AND
			MP.id_comprobante=C.id_comprobante AND
			C.fecha_c BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$acumulado_pagado=$bd->consultar($sql,'ARREGLO');

$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(MP.monto_mp) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			MP.id_comprobante=C.id_comprobante AND
			O.asign_operacion $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$asignado=$bd->consultar($sql,'ARREGLO');

$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(case when O.dismin_operacion=true then -MP.monto_mp else MP.monto_mp end) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			MP.id_comprobante=C.id_comprobante AND
			(O.asign_operacion OR ((O.aument_operacion OR O.dismin_operacion) AND C.fecha_c BETWEEN '01-01-$anio' AND '$fecha_final')) $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$modificado=$bd->consultar($sql,'ARREGLO');



$sql="SELECT
			MP.id_cuenta_presupuestaria,
			SUM(MP.monto_mp) AS monto
		FROM
			modulo_presupuesto.movimiento_presupuestario AS MP,
			modulo_presupuesto.operacion AS O,
			modulo_presupuesto.comprobante AS C
		WHERE
			C.eliminado=false AND
			MP.id_operacion=O.id_operacion AND
			O.reintegra_operacion AND
			MP.id_comprobante=C.id_comprobante AND
			C.fecha_c BETWEEN '$anio-01-01' AND '$fecha_final' $condicion1
		GROUP BY
			MP.id_cuenta_presupuestaria
		ORDER BY
			MP.id_cuenta_presupuestaria";
$reintegro=$bd->consultar($sql,'ARREGLO');

*/


switch($periodo_tipo){
	case "T"://trimestral		
		$programado_trimestre=$db->Execute("SELECT								
																								FD.id_cuenta_presupuestaria,
																								sum(FD.monto[1]+FD.monto[2]+FD.monto[3]) as monto_trimestre_i,
																								sum(FD.monto[4]+FD.monto[5]+FD.monto[6]) as monto_trimestre_ii,
																								sum(FD.monto[7]+FD.monto[8]+FD.monto[9]) as monto_trimestre_iii,
																								sum(FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto_trimestre_iv
																						FROM
																							modulo_base.formulacion AS F,
																							modulo_base.formulacion_detalle AS FD
																						WHERE
																								F.anio='$anio' AND
																								F.tipo='F' AND
																								F.id=FD.id_formulacion AND
																								FD.id_cuenta_presupuestaria ilike '4%'
																								$condicion2
																						GROUP BY
																								FD.id_cuenta_presupuestaria
																						ORDER BY
																								FD.id_cuenta_presupuestaria");
		
		$A_trim=Array("monto_trimestre_i","monto_trimestre_ii","monto_trimestre_iii","monto_trimestre_iv");
		$programado_trimestre2=$programado_trimestre;
		break;
	case "M"://mensual
		
		$programado_trimestre=$db->Execute("SELECT								
																								FD.id_cuenta_presupuestaria,
																								SUM(FD.monto_ene) as monto_ene,
																								SUM(FD.monto_feb) as monto_feb,
																								SUM(FD.monto_mar) as monto_mar,
																								SUM(FD.monto_abr) as monto_abr,
																								SUM(FD.monto_may) as monto_may,
																								SUM(FD.monto_jun) as monto_jun,
																								SUM(FD.monto_jul) as monto_jul,
																								SUM(FD.monto_ago) as monto_ago,
																								SUM(FD.monto_sep) as monto_sep,
																								SUM(FD.monto_oct) as monto_oct,
																								SUM(FD.monto_nov) as monto_nov,
																								SUM(FD.monto_dic) as monto_dic
																						FROM
																							modulo_base.formulacion AS F,
																							modulo_base.formulacion_detalle AS FD
																						WHERE
																								F.anio='$anio' AND
																								F.tipo='F' AND
																								F.id=FD.id_formulacion AND
																								FD.id_cuenta_presupuestaria ilike '4%'
																								$condicion2
																						GROUP BY
																								FD.id_cuenta_presupuestaria
																						ORDER BY
																								FD.id_cuenta_presupuestaria");
		
		$A_trim=Array(	"monto_ene",
						"monto_feb",
						"monto_mar",
						"monto_abr",
						"monto_may",
						"monto_jun",
						"monto_jul",
						"monto_ago",
						"monto_sep",
						"monto_oct",
						"monto_nov",
						"monto_dic");
		
		$programado_trimestre2=$db->Execute("SELECT								
																								FD.id_cuenta_presupuestaria,
																								sum(FD.monto[1]+FD.monto[2]+FD.monto[3]) as monto_trimestre_i,
																								sum(FD.monto[4]+FD.monto[5]+FD.monto[6]) as monto_trimestre_ii,
																								sum(FD.monto[7]+FD.monto[8]+FD.monto[9]) as monto_trimestre_iii,
																								sum(FD.monto[10]+FD.monto[11]+FD.monto[12]) as monto_trimestre_iv
																						FROM
																							modulo_base.formulacion AS F,
																							modulo_base.formulacion_detalle AS FD
																						WHERE
																								F.anio='$anio' AND
																								F.tipo='F' AND
																								F.id=FD.id_formulacion AND
																								FD.id_cuenta_presupuestaria ilike '4%'
																								$condicion2
																						GROUP BY
																								FD.id_cuenta_presupuestaria
																						ORDER BY
																								FD.id_cuenta_presupuestaria");
		
		break;
//  case 3://fecha
//		
//		break;
	default:
		exit;
	}
 




//busca el monto en el arreglo para el codigo dado
function bm($A,$id_cuenta_presupuestaria,$col_monto="monto"){
	if(!$A) return 0;
	for($i=0;$i<count($A) and $A;$i++)
		if($A[$i]["id_cuenta_presupuestaria"]==$id_cuenta_presupuestaria)
				return $A[$i][$col_monto];
	return 0;
	}

$PARTIDAS_POSIBLES=Array(0=>"401",1=>"402",2=>"403",3=>"404",4=>"407",5=>"408",6=>"411");
$PARTIDAS_POSIBLES_DEM=Array(0=>"GASTOS DE PERSONAL",1=>"MATERIALES, SUMINISTROS Y MERCANCIAS",2=>"SERVICIOS NO PERSONALES",3=>"ACTIVOS REALES",4=>"TRANSFERENCIAS Y DONACIONES",5=>"OTROS GASTOS",6=>"DISMINUCIÓN DE PASIVOS");












class PDF_Rotate_AutoBreakPage extends PDF_Rotate{
	var $MARGEN_LEFT;
	var $MARGEN_TOP;
	var $ANCHO;
	var $col_montos;
	var $col_codigo;
	var $col_denominacion;
	var $trimestre_n;

	function AcceptPageBreak(){
		global $ANCHO;
		$this->Line($this->GetX(),$this->GetY(),$this->lMargin+$ANCHO,$this->GetY());		
		return true;
		}
	function Header(){
		if($this->page>1)
    		$this->CabeceraTabla();
    	$pos_y=$this->GetY();
		$pos_x=$this->GetX();

		$this->SetFont('helvetica','',8);
		$this->SetXY($this->ANCHO-57,10);
		$this->Cell(50,5,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'R');

		$this->SetXY($pos_x,$pos_y);
		}
	function CabeceraTabla(){
		global $periodo_tipo, $col_codigo,$col_denominacion, $col_montos, $trimestre_n;
		$pos_y=$this->GetY();
		$pos_x=$this->GetX();

		//$col_codigo=$this->col_codigo;
		//$col_denominacion=$this->col_denominacion;
		//$col_montos=$this->col_montos;
		//$trimestre_n=$this->trimestre_n;


		$this->SetFont('helvetica','B',5);
		$this->Cell($col_codigo,4,utf8_decode("(2)"),'LRT',0,'C',1);
		$this->Cell($col_denominacion,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);//ASIGNADO
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);//CREDITOS
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);//MODIFICADO
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);//MODIFICADO
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);//MODIFICADO
		//$this->Cell($col_montos,4,"PROGRAMADO",'LRT',0,'C',1);
		if($periodo_tipo=="T"){
			$this->Cell($col_montos*4,4,utf8_decode("EJECUTADO EN EL TRIMESTRE Nº ".$trimestre_n),'LRTB',0,'C',1);
			$this->Cell($col_montos*5,4,utf8_decode("ACUMULADO AL TRIMESTRE Nº ".$trimestre_n),'LRTB',0,'C',1);
			}
		else{
			$this->Cell($col_montos*4,4,utf8_decode("EJECUTADO"),'LRTB',0,'C',1);
			$this->Cell($col_montos*5,4,utf8_decode("ACUMULADO"),'LRTB',0,'C',1);
			}
// 		$this->Cell($col_montos*3,4,"EJECUTADO EN EL TRIMESTRE Nº ".$trimestre_n,'LRTB',0,'C',1);
// 		$this->Cell($col_montos*4,4,"ACUMULADO AL TRIMESTRE Nº ".$trimestre_n,'LRTB',0,'C',1);
		//$this->Cell($col_montos,4,"",'LRT',0,'C',1);
		$this->Cell($col_montos*2,4,utf8_decode("DISPONIBILIDAD PRESUPUESTO"),'LRTB',1,'C',1);

		$this->SetFont('helvetica','B',5);
		$this->Cell($col_codigo,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_denominacion,4,utf8_decode("DENOMINACIÓN"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("PRESUPUESTO"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("CREDITO"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("TRASPASO"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("TRASPASO"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("PRESUPUESTO"),'LR',0,'C',1);
		//$this->Cell($col_montos,4,"EN EL",'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
//		$this->Cell($col_montos,4,"",'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRT',1,'C',1);

		$this->SetFont('helvetica','B',5);
		$this->Cell($col_codigo,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_denominacion,4,utf8_decode("(3)"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("APROBADO"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("ADICIONAL"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("AUMENTOS"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("DISMINUCIONES"),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("MODIFICADO"),'LR',0,'C',1);
		//$this->Cell($col_montos,4,"TRIMESTRE",'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
//		$this->Cell($col_montos,4,"",'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LR',1,'C',1);

		$this->Cell($col_codigo,4,utf8_decode(""),'LRB',0,'C',1);
		$this->Cell($col_denominacion,4,(""),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("(4)"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode(""),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("(5)"),'LRB',0,'C',1);
		//$this->Cell($col_montos,4,"Nº ".$trimestre_n,'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("COMPROMETIDO"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("CAUSADO"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("PAGADO"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("%"),'LRB',0,'C',1);
//		$this->Cell($col_montos,4,"POR PAGAR",'LRB',0,'C',1);
		//$this->Cell($col_montos,4,"PROGRAMADO",'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("COMPROMETIDO"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("CAUSADO"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("PAGADO"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("POR PAGAR"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("%"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("DISPONIBLE"),'LRB',0,'C',1);
		$this->Cell($col_montos,4,utf8_decode("%"),'LRB',1,'C',1);


		$pos_y_final=$this->GetY();
		$pos_x_final=$this->GetX();


		$this->SetFont('helvetica','B',4);
		$this->Rotate(90,$pos_x,$pos_y+13+3);
		$this->SetXY($pos_x,$pos_y+13+3);
		$this->Cell(13,4,utf8_decode('PARTIDA'),'LRTB',1,'L',1);
		$this->Cell(13,3,utf8_decode('GENERICA'),'LRTB',1,'L',1);
		$this->Cell(13,3,utf8_decode('ESPECIFICA'),'LRTB',1,'L',1);
		$this->Cell(13,3,utf8_decode('SUBESPECIFICA'),'LRTB',1,'L',1);
		$this->Rotate(0);


		$this->SetXY($pos_x_final,$pos_y_final);
		}

	}





$pdf=new PDF_Rotate_AutoBreakPage("L","mm","Legal");

$MARGEN_LEFT=7;
$MARGEN_TOP=15;
$ANCHO=340;
$col_montos=18;
$col_codigo=13;
$col_denominacion=$ANCHO-(16*$col_montos+$col_codigo);



$pdf->SetLeftMargin($MARGEN_LEFT);
$pdf->SetTopMargin($MARGEN_TOP);

$pdf->AddPage();





$pdf->SetFont('helvetica','',8);
$pdf->SetFillColor(255,255,255);


//imprime la cabecera
$ENCABEZADO="(1) CÓDIGO DEL ENTE: $ente_codigo.\nDENOMINACIÓN DEL ENTE: $ente_denominacion.\nENTIDAD FEDERAL: $ente_adscripcion.\nPERÍODO PRESUPUESTADO: $anio.\n";
$pdf->MultiCell($ANCHO,3,utf8_decode($ENCABEZADO),'','J',1);

//titulo
$pdf->Cell($ANCHO,5,'',0,1,'',1);
$pdf->SetFont('helvetica','B',11);
$pdf->MultiCell($ANCHO,5,utf8_decode($titulo),'','C',1);


//bolivares fuertes
$pdf->SetFont('helvetica','',7);
$pdf->MultiCell($ANCHO,3,utf8_decode(""),'','C',1);

if($NOMBRE_PROYECTO_ACCION){
	$pdf->SetFont('helvetica','B',7);
	$pdf->MultiCell($ANCHO,3,utf8_decode($NOMBRE_PROYECTO_ACCION),'','L',1);
	if($DESCRIPCION_ESPECIFICA){
		$pdf->SetFont('helvetica','',7);
		$pdf->MultiCell($ANCHO,3,utf8_decode($DESCRIPCION_ESPECIFICA),'','L',1);
		}
	$pdf->Cell($ANCHO,3,'',0,1,'',1);
	}



switch($periodo_tipo){
	case "T"://trimestral
		$pdf->SetFont('helvetica','',7);
		$pdf->Cell($ANCHO,5,"TRIMESTRE: $trimestre",0,1,'',1);
		$pdf->Cell($ANCHO,2,'',0,1,'',1);
		break;
	case "M"://mensual
		$pdf->SetFont('helvetica','',7);
		$pdf->Cell($ANCHO,5,"MES: $trimestre",0,1,'',1);
		$pdf->Cell($ANCHO,2,'',0,1,'',1);
		break;
//  case 3://fecha
//		$pdf->SetFont('helvetica','',7);
//		$pdf->Cell($ANCHO,5,"FECHA: $trimestre",0,1,'',1);
//		$pdf->Cell($ANCHO,2,'',0,1,'',1);
//		break;
	default:
		exit;
	}





$suma_a=0;
$suma_b=0;
$suma_c=0;
$suma_d=0;
$suma_e=0;
$suma_f=0;
$suma_g=0;
$suma_h=0;
$suma_i=0;
$suma_j=0;
$suma_k=0;
$suma_l=0;
$suma_m=0;
$suma_n=0;



$pdf->CabeceraTabla();




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



$MATRIZ_SIN_PADRES=array();

$CMSP=0;
for($iii=0;$iii<count($en_uso);$iii++){
	$PARTIDA=$en_uso[$iii]["id_cuenta_presupuestaria"];
 	$a=bm($asignado,$PARTIDA);
 	$b=bm($modificado,$PARTIDA);
 	$c=bm($programado_trimestre2,$PARTIDA,$col_prog_trim);
 	$d=bm($comprometido,$PARTIDA);
 	$e=bm($causado,$PARTIDA);
 	$f=bm($pagado,$PARTIDA);
	$g=0;
	for($cont=0;$cont<$trimestre_n;$cont++)
  		$g+=bm($programado_trimestre,$PARTIDA,$A_trim[$cont]);
    $h=bm($comprometido_acumulado,$PARTIDA);//-bm($reintegro,$PARTIDA);//retornar el reintegro al acumulado ccp (no se puede reintegrar mas de lo ccp)
	$i=bm($causado_acumulado,$PARTIDA);//-bm($reintegro,$PARTIDA);
	$j=bm($pagado_acumulado,$PARTIDA);//-bm($reintegro,$PARTIDA);
	$k=$b-$h;
	$l=bm($credito,$PARTIDA);
	$m=bm($modificado_au,$PARTIDA);
	$n=bm($modificado_di,$PARTIDA);

	$suma_a+=$a;
	$suma_b+=$b;
	$suma_c+=$c;
	$suma_d+=$d;
	$suma_e+=$e;
	$suma_f+=$f;
	$suma_g+=$g;
	$suma_h+=$h;
	$suma_i+=$i;
	$suma_j+=$j;
	$suma_k+=$k;
	$suma_l+=$l;
	$suma_m+=$m;
	$suma_n+=$n;

	if($a==0 and $b==0 and $c==0 and $d==0 and $e==0 and $f==0 and $g==0 and $h==0 and $i==0 and $j==0 and $k==0 and $l==0 and $m==0 and $n==0)
		continue;


	$MATRIZ_SIN_PADRES[$CMSP]["id_cuenta_presupuestaria"]=$PARTIDA;
	$MATRIZ_SIN_PADRES[$CMSP]["a"]=$a;
	$MATRIZ_SIN_PADRES[$CMSP]["b"]=$b;
	$MATRIZ_SIN_PADRES[$CMSP]["c"]=$c;
	$MATRIZ_SIN_PADRES[$CMSP]["d"]=$d;
	$MATRIZ_SIN_PADRES[$CMSP]["e"]=$e;
	$MATRIZ_SIN_PADRES[$CMSP]["f"]=$f;
	$MATRIZ_SIN_PADRES[$CMSP]["g"]=$g;
	$MATRIZ_SIN_PADRES[$CMSP]["h"]=$h;
	$MATRIZ_SIN_PADRES[$CMSP]["i"]=$i;
	$MATRIZ_SIN_PADRES[$CMSP]["j"]=$j;
	$MATRIZ_SIN_PADRES[$CMSP]["k"]=$k;
	$MATRIZ_SIN_PADRES[$CMSP]["l"]=$l;
	$MATRIZ_SIN_PADRES[$CMSP]["m"]=$m;
	$MATRIZ_SIN_PADRES[$CMSP]["n"]=$n;
	$CMSP++;
	}


$A=array();




$k=0;
$TOTAL=0;
for($i=0;$i<$CMSP;$i++){
	$padres=RetornarPadresCtaPresp($MATRIZ_SIN_PADRES[$i]["id_cuenta_presupuestaria"]);
	for($j=0;$j<count($padres);$j++){
		$sw=false;
		for($o=0;$o<$k;$o++)
			if($padres[$j]==$A[$o]["id_cuenta_presupuestaria"]){
				$sw=true;
				break;
				}
		if($sw==false){//si es 1era vez que se agrega
			$A[$k]["id_cuenta_presupuestaria"]=$padres[$j];
			$A[$k]["a"]=$MATRIZ_SIN_PADRES[$i]["a"];
			$A[$k]["b"]=$MATRIZ_SIN_PADRES[$i]["b"];
			$A[$k]["c"]=$MATRIZ_SIN_PADRES[$i]["c"];
			$A[$k]["d"]=$MATRIZ_SIN_PADRES[$i]["d"];
			$A[$k]["e"]=$MATRIZ_SIN_PADRES[$i]["e"];
			$A[$k]["f"]=$MATRIZ_SIN_PADRES[$i]["f"];
			$A[$k]["g"]=$MATRIZ_SIN_PADRES[$i]["g"];
			$A[$k]["h"]=$MATRIZ_SIN_PADRES[$i]["h"];
			$A[$k]["i"]=$MATRIZ_SIN_PADRES[$i]["i"];
			$A[$k]["j"]=$MATRIZ_SIN_PADRES[$i]["j"];
			$A[$k]["k"]=$MATRIZ_SIN_PADRES[$i]["k"];
			$A[$k]["l"]=$MATRIZ_SIN_PADRES[$i]["l"];
			$A[$k]["m"]=$MATRIZ_SIN_PADRES[$i]["m"];
			$A[$k]["n"]=$MATRIZ_SIN_PADRES[$i]["n"];
			$k++;
			}
		else{//si ya estaba agregado, acumular suma
			$A[$o]["a"]+=$MATRIZ_SIN_PADRES[$i]["a"];
			$A[$o]["b"]+=$MATRIZ_SIN_PADRES[$i]["b"];
			$A[$o]["c"]+=$MATRIZ_SIN_PADRES[$i]["c"];
			$A[$o]["d"]+=$MATRIZ_SIN_PADRES[$i]["d"];
			$A[$o]["e"]+=$MATRIZ_SIN_PADRES[$i]["e"];
			$A[$o]["f"]+=$MATRIZ_SIN_PADRES[$i]["f"];
			$A[$o]["g"]+=$MATRIZ_SIN_PADRES[$i]["g"];
			$A[$o]["h"]+=$MATRIZ_SIN_PADRES[$i]["h"];
			$A[$o]["i"]+=$MATRIZ_SIN_PADRES[$i]["i"];
			$A[$o]["j"]+=$MATRIZ_SIN_PADRES[$i]["j"];
			$A[$o]["k"]+=$MATRIZ_SIN_PADRES[$i]["k"];
			$A[$o]["l"]+=$MATRIZ_SIN_PADRES[$i]["l"];
			$A[$o]["m"]+=$MATRIZ_SIN_PADRES[$i]["m"];
			$A[$o]["n"]+=$MATRIZ_SIN_PADRES[$i]["n"];

			}
		}
	$A[$k]["id_cuenta_presupuestaria"]=$MATRIZ_SIN_PADRES[$i]["id_cuenta_presupuestaria"];
	$A[$k]["a"]=$MATRIZ_SIN_PADRES[$i]["a"];
	$A[$k]["b"]=$MATRIZ_SIN_PADRES[$i]["b"];
	$A[$k]["c"]=$MATRIZ_SIN_PADRES[$i]["c"];
	$A[$k]["d"]=$MATRIZ_SIN_PADRES[$i]["d"];
	$A[$k]["e"]=$MATRIZ_SIN_PADRES[$i]["e"];
	$A[$k]["f"]=$MATRIZ_SIN_PADRES[$i]["f"];
	$A[$k]["g"]=$MATRIZ_SIN_PADRES[$i]["g"];
	$A[$k]["h"]=$MATRIZ_SIN_PADRES[$i]["h"];
	$A[$k]["i"]=$MATRIZ_SIN_PADRES[$i]["i"];
	$A[$k]["j"]=$MATRIZ_SIN_PADRES[$i]["j"];
	$A[$k]["k"]=$MATRIZ_SIN_PADRES[$i]["k"];
	$A[$k]["l"]=$MATRIZ_SIN_PADRES[$i]["l"];
	$A[$k]["m"]=$MATRIZ_SIN_PADRES[$i]["m"];
	$A[$k]["n"]=$MATRIZ_SIN_PADRES[$i]["n"];
	$k++;
	}







for($iii=0;$iii<count($A);$iii++){
	$PART=substr($A[$iii]["id_cuenta_presupuestaria"],0,3);
	$GEN=substr($A[$iii]["id_cuenta_presupuestaria"],3,2);
	$ESP=substr($A[$iii]["id_cuenta_presupuestaria"],5,2);
	$SUBESP=substr($A[$iii]["id_cuenta_presupuestaria"],7,2);
	
	if($tipo==1)//solo partidas
		if(!($GEN=="00" and $ESP=="00" and $SUBESP=="00"))
				continue;

	if($tipo==2)//solo partidas|generales
		if(!($ESP=="00" and $SUBESP=="00"))
				continue;

  $NEGRITA=false;
  $pdf->SetFont('helvetica','',6);
  if($tipo==2){
		if($GEN=="00" and $ESP=="00" and $SUBESP=="00")
		  $NEGRITA=true;
		}
  else{
		if($ESP=="00" or $GEN=="00")
				$NEGRITA=true;
		}
  if($NEGRITA)
		$pdf->SetFont('helvetica','B',6);
	//$pdf->SetFont('helvetica','',6);
	//if($ESP=="00" or $GEN=="00")
	//	$pdf->SetFont('helvetica','B',6);

	$CUENTA_PRESUPUESTARIA=$db->Execute("select denominacion from modulo_base.cuenta_presupuestaria where id_cuenta_presupuestaria='".$A[$iii]["id_cuenta_presupuestaria"]."'");
	$denominacion_cta=$CUENTA_PRESUPUESTARIA[0][0];

	$pdf->Cell(4,4,$PART,'LR',0,'C',1);
	$pdf->Cell(3,4,$GEN,'LR',0,'C',1);
	$pdf->Cell(3,4,$ESP,'LR',0,'C',1);
	$pdf->Cell(3,4,$SUBESP,'LR',0,'C',1);

	$pdf->SetFont('helvetica','',5.5);
	//if($ESP=="00" or $GEN=="00")
	if($NEGRITA)
		$pdf->SetFont('helvetica','B',5.5);
		//$pdf->SetFont('helvetica','B',5.5);
		
		
		
	$pdf->Cell($col_denominacion,4,utf8_decode(ucfirst(mb_convert_case($denominacion_cta, MB_CASE_LOWER, "UTF-8"))),'LR',0,'L',1);


	$pdf->SetFont('helvetica','',5.5);
	//if($ESP=="00" or $GEN=="00")
	//	$pdf->SetFont('helvetica','B',6);
	if($NEGRITA)
		$pdf->SetFont('helvetica','B',5.5);

	$pdf->Cell($col_montos,4,number_format($A[$iii]["a"],$decimales,",","."),'LR',0,'R',1);//aprobado
	$pdf->Cell($col_montos,4,number_format($A[$iii]["l"],$decimales,",","."),'LR',0,'R',1);//creditos
	$pdf->Cell($col_montos,4,number_format($A[$iii]["m"],$decimales,",","."),'LR',0,'R',1);//modificado (aumentos)
	$pdf->Cell($col_montos,4,number_format($A[$iii]["n"],$decimales,",","."),'LR',0,'R',1);//modificado (disminuciones)
	$pdf->Cell($col_montos,4,number_format($A[$iii]["b"],$decimales,",","."),'LR',0,'R',1);//modificado
	//$pdf->Cell($col_montos,4,number_format($A[$iii]["c"],$decimales,",","."),'LR',0,'R',1);//programado
	$pdf->Cell($col_montos,4,number_format($A[$iii]["d"],$decimales,",","."),'LR',0,'R',1);//comprometido
	$pdf->Cell($col_montos,4,number_format($A[$iii]["e"],$decimales,",","."),'LR',0,'R',1);//causado
	$pdf->Cell($col_montos,4,number_format($A[$iii]["f"],$decimales,",","."),'LR',0,'R',1);//pagado
	$pdf->Cell($col_montos,4,number_format($A[$iii]["b"]==0?0:($A[$iii]["d"]/$A[$iii]["b"]*100),$decimales,",",".")."%",'LR',0,'R',1);//%
	//por pagar
	//$pdf->Cell($col_montos,4,number_format($A[$iii]["e"]-$A[$iii]["f"],$decimales,",","."),'LR',0,'R',1);

	//$pdf->Cell($col_montos,4,number_format($A[$iii]["g"],$decimales,",","."),'LR',0,'R',1);//acumulado programado
	$pdf->Cell($col_montos,4,number_format($A[$iii]["h"],$decimales,",","."),'LR',0,'R',1);//acumlulado comprometido
	$pdf->Cell($col_montos,4,number_format($A[$iii]["i"],$decimales,",","."),'LR',0,'R',1);//acumulado causado
	$pdf->Cell($col_montos,4,number_format($A[$iii]["j"],$decimales,",","."),'LR',0,'R',1);//acumulado pagado
	
	//por pagar acumulado
	$pdf->Cell($col_montos,4,number_format($A[$iii]["i"]-$A[$iii]["j"],$decimales,",","."),'LR',0,'R',1);//por pagar
	$pdf->Cell($col_montos,4,number_format($A[$iii]["b"]==0?0:($A[$iii]["h"]/$A[$iii]["b"]*100),$decimales,",",".")."%",'LR',0,'R',1);//%

	$pdf->Cell($col_montos,4,number_format($A[$iii]["k"],$decimales,",","."),'LR',0,'R',1);//disponible
	$pdf->Cell($col_montos,4,number_format($A[$iii]["b"]==0?0:($A[$iii]["k"]/$A[$iii]["b"]*100),$decimales,",",".")."%",'LR',1,'R',1);//%
	}



$pdf->SetFont('helvetica','B',5.5);
$pdf->Cell($col_codigo,4,"",'LRTB',0,'C',1);
$pdf->Cell($col_denominacion,4,"TOTAL",'LRTB',0,'L',1);
$pdf->Cell($col_montos,4,number_format($suma_a,$decimales,",","."),'LRTB',0,'R',1);//apro
$pdf->Cell($col_montos,4,number_format($suma_l,$decimales,",","."),'LRTB',0,'R',1);//creditos
$pdf->Cell($col_montos,4,number_format($suma_m,$decimales,",","."),'LRTB',0,'R',1);//mod (aumentos)
$pdf->Cell($col_montos,4,number_format($suma_n,$decimales,",","."),'LRTB',0,'R',1);//mod (disminucion)
$pdf->Cell($col_montos,4,number_format($suma_b,$decimales,",","."),'LRTB',0,'R',1);//mod
//$pdf->Cell($col_montos,4,number_format($suma_c,$decimales,",","."),'LRTB',0,'R',1);//prog
$pdf->Cell($col_montos,4,number_format($suma_d,$decimales,",","."),'LRTB',0,'R',1);//comp
$pdf->Cell($col_montos,4,number_format($suma_e,$decimales,",","."),'LRTB',0,'R',1);//causa
$pdf->Cell($col_montos,4,number_format($suma_f,$decimales,",","."),'LRTB',0,'R',1);//pago
$pdf->Cell($col_montos,4,number_format($suma_b==0?0:($suma_d/$suma_b*100),$decimales,",",".")."%",'LRTB',0,'R',1);//%

//por pagar
//$pdf->Cell($col_montos,4,number_format($suma_e-$suma_f,$decimales,",","."),'LRTB',0,'R',1);
//$pdf->Cell($col_montos,4,number_format($suma_g,$decimales,",","."),'LRTB',0,'R',1);//a prog
$pdf->Cell($col_montos,4,number_format($suma_h,$decimales,",","."),'LRTB',0,'R',1);//a comp
$pdf->Cell($col_montos,4,number_format($suma_i,$decimales,",","."),'LRTB',0,'R',1);//a causa
$pdf->Cell($col_montos,4,number_format($suma_j,$decimales,",","."),'LRTB',0,'R',1);//a pago


//por pagar acumulado
$pdf->Cell($col_montos,4,number_format($suma_i-$suma_j,$decimales,",","."),'LRTB',0,'R',1);//por pagar
$pdf->Cell($col_montos,4,number_format($suma_b==0?0:($suma_h/$suma_b*100),$decimales,",",".")."%",'LRTB',0,'R',1);//%

$pdf->Cell($col_montos,4,number_format($suma_k,$decimales,",","."),'LRTB',0,'R',1);//disp
$pdf->Cell($col_montos,4,number_format($suma_b==0?0:($suma_k/$suma_b*100),$decimales,",",".")."%",'LRTB',1,'R',1);//%

$pdf->AliasNbPages();

$pdf->Output();


?>
