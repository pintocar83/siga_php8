<?php
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
* @author Irving J. Martínez R. <irvingjm@gmail.com>
* @date 2009-08-01
* @version 20091216
*/

function completarCodigoCeros($cadena,$tamano){
    $p="";
    for(;$tamano> strlen($cadena);$tamano--)
        $p.="0";
    return ($p.$cadena);
	}

function formatearFecha($cadena){
        list($ano,$mes,$dia)=explode("-",$cadena);
	return "$dia/$mes/$ano";
	}

function desformatearFecha($cadena){
	list($dia,$mes,$ano)=explode("/",$cadena);
	return "$ano-$mes-$dia";
	}

function esBisiesto($ano){
    return (($ano%4==0 && $ano%100!=0)||($ano%400==0)?true:false);
}

function num_lunes($mes,$anyo)
{
    $diaS=date("w", mktime(0, 0, 0, $mes, 1, $anyo));

    if ($diaS==1)
    {
        $nLunes=5;
        if ($mes==2 && !esBisiesto($anyo))
            $nLunes=4;
    }
    else
    {
        $nLunes=0;
        if ($diaS==0)
            $diaS=7;
        $dia=9-$diaS;
        while(checkdate($mes, $dia, $anyo))
        {
            $nLunes++;
            $dia=$dia+7;
        }
    }
    return $nLunes;
}



function num_lunes_q($mes,$anyo,$desde)
{
    $diaS=date("w", mktime(0, 0, 0, $mes, 1, $anyo));
list($anyoDesde,$mesDesde,$diaDesde)=explode("-",$desde);

$nLunes=0;
if ($diaS==0)
$diaS=7;
elseif ($diaS==1)
$diaS=8;
$dia=9-$diaS;
while(checkdate($mes, $dia, $anyo))
{
if (($diaDesde==1 && $dia<16) || ($diaDesde==16 && $dia>15))
$nLunes++;
$dia=$dia+7;
}

    return $nLunes;
}


function antiguedad($ingreso, $fecha)
{
    list($dia,$mes,$anyo)=explode("/",$ingreso);
    list($dia_act,$mes_act,$anyo_act)=explode("/",$fecha);
    $anyos=$anyo_act-$anyo;
    $meses=$mes_act-$mes;
    if ($anyos==0)
    {
        if ($meses>=0)
        {
            $dias=$dia_act-$dia;
            if ($dias<0)
            {
                $meses--;
                $mes_ant=$mes_act-1;
                $num_dias=date("t", mktime(0, 0, 0, $mes_ant, 1, $anyo_act));
                $dias=$num_dias-$dia+$dia_act;
            }
        }
    }
    elseif ($anyos>0)
    {
        $dias=$dia_act-$dia;
        if ($meses==0)
        {
            if ($dias<0)
            {
                $anyos--;
                $meses=12;
            }
        }
        elseif ($meses<0)
        {
            $anyos--;
            $meses=12+$meses;
        }

        if ($dias<0)
        {
            $meses--;
            $mes_ant=$mes_act-1;
            $num_dias=date("t", mktime(0, 0, 0, $mes_ant, 1, $anyo_act));
            $dias=$num_dias-$dia+$dia_act;
        }
    }
    return "$anyos-$meses-$dias";
}

function FormatearCodigoProgramatico($ACC,$AE,$OAE){
	if(substr($ACC,0,3)=="ACC")
		return $ACC."-".completarCodigoCeros($AE,7)."-".completarCodigoCeros($OAE,2);
	return $ACC."-".substr($ACC,3).completarCodigoCeros($AE,2)."-".completarCodigoCeros($OAE,2);
	}

function ordenar_afectacion_p($a, $b)
{
    $retval = strnatcmp($a['id_estructura_p'], $b['id_estructura_p']);
    if(!$retval)
        return strnatcmp($a['codigo_p'], $b['codigo_p']);
    return $retval;
}

function ordenar_afectacion_c($a, $b)
{
    $retval = strnatcmp($a['id_estructura_p'], $b['id_estructura_p']);
    if(!$retval)
        return strnatcmp($a['codigo_c'], $b['codigo_c']);
    return $retval;
}

function ordenar_afectacion_c_contab($a, $b)
{
    $retval = strnatcmp($a['codigo_c'], $b['codigo_c']);
    if(!$retval)
        return strnatcmp($a['id_estructura_p'], $b['id_estructura_p']);
    return $retval;
}

function ordenar_banco($a, $b)
{
    $retval = strnatcmp($a['codigo_p'], $b['codigo_p']);
    if(!$retval)
        return strnatcmp($a['codigo_c'], $b['codigo_c']);
    return $retval;
}

function ordenar_recibos_pago($a, $b)
{
    $retval = strnatcmp($a['cod_ficha'], $b['cod_ficha']);
    if(!$retval)
        return strnatcmp($a['cod_concepto'], $b['cod_concepto']);
    return $retval;
}

function ordenar_retenciones($a, $b)
{
    $retval = strnatcmp($a['cod_nomina'], $b['cod_nomina']);
    if(!$retval)
        return strnatcmp($a['estructura_p'], $b['estructura_p']);
    return $retval;
}

function ordenar_listado_banco($a, $b)
{
    $retval = strnatcmp($a['tipo'], $b['tipo']);
    if(!$retval)
        return strnatcmp($a['cod_ficha'], $b['cod_ficha']);
    return $retval;
}

function CadenaCeros($tamano){
	$cadena="";
	for($i=0;$i<$tamano;$i++)
		$cadena.="0";
	return $cadena;
	}



function base($num){
	$end=$num-floor($num/10)*10;
	switch ($end){
		case 1: 	return "uno";
		case 2: 	return "dos";
		case 3: 	return "tres";
		case 4: 	return "cuatro";
		case 5: 	return "cinco";
		case 6: 	return "seis";
		case 7: 	return "siete";
		case 8: 	return "ocho";
		case 9: 	return "nueve";
		case 0:
			if($num==0)	return "cero";
			else		return "";
		}
	return $end;
	}

//LA FUNCION DECIMOS ES PARA 99 -> 0 Y LLAMA A LA BASE
function decimos($num){
	if($num<10)
		return base($num);
	$ends=$num-floor($num/100)*100;
	$end=$ends-($num-floor($num/10)*10);
	$endd=floor($ends);
	switch ($end){
		case 10:
			if($ends<16)
				switch($endd){
					case 10: return "diez";
					case 11: return "once ";
					case 12: return "doce";
					case 13: return "trece";
					case 14: return "catorce";
					case 15: return "quince";
					}
			else
				return "dieci".base($num);
		case 20:
			if($ends==20)	return "veinte";
			else 			return "veinti".base($num);
		case 30:
			if($ends==30)	return "treinta";
			else			return "treinta y ".base($num);
		case 40:
			if($ends==40)	return "cuarenta";
			else			return "cuarenta y ".base($num);
		case 50:
			if($ends==50)	return "cincuenta";
			else			return "cincuenta y ".base($num);
		case 60:
			if($ends==60)	return "sesenta";
			else			return "sesenta y ".base($num);
		case 70:
			if($ends==70)	return "setenta";
			else			return "setenta y ".base($num);
		case 80:
			if($ends==80)	return "ochenta";
			else			return "ochenta y ".base($num);
		case 90:
			if($ends==90)	return "noventa";
			else			return "noventa y ".base($num);
		case 0:
			return base($num);
		}
	}

//LA FUNCION CIENTOS ES PARA 99 -> 0 Y LLAMA A DECIMOS
function cientos($num){
	if ($num<100) return decimos($num);
	$ends=$num-floor($num/1000)*1000;
	$end=$ends-($num-floor($num/100)*100);
	switch($end){
		case 100:
			if($ends==100)	return "cien";
			else			return "ciento ".decimos($num);
		case 500:			return "quinientos ".decimos($num);
		case 900:			return "novecientos ".decimos($num);
		case 700:			return "setecientos ".decimos($num);
		case 0:				return decimos($num);
		default:			return base($end/100)."cientos ".decimos($num);
		}
	}
//CIENTOSX es para los miles que terminane en 1
function cientos_x($num){
	$endd=$num-floor($num/10)*10;
	$ends=$endd-floor($endd/10)*10;
	$resultado=cientos($num);
	if($ends==1 && $endd!=11)	return substr($resultado,0,strlen($resultado)-1);
	else						return $resultado;
	}

function miles($num){
	if ($num<1000) 	return cientos($num);
	$ends=$num-floor($num/10000)*10000;
	$end=$ends-($num-floor($num/1000)*1000);
	switch ($end){
		case 1000:
			if($ends==1000)	return "mil";
			else			return "mil ".cientos($num);
		default:
			$mil=base(floor($num/1000));
			if ($mil==0)		return cientos($num);
			else				return $mil." mil ".+cientos($num);
		}
	}

function cientos_de_miles($num){
	if($num<1000) return miles($num);
	$ends=floor(($num-floor($num/1000000)*1000000)/1000);
	if($ends==0)
		return cientos($num);
// 	else if($ends==1)
// 		return "mil ".cientos($num);
	return cientos_x($ends)." mil ".cientos($num);
	}

function millones($num){
	if($num<pow(10,6)) return cientos_de_miles($num);
	$ends=floor($num/pow(10,6));
	$end=$ends-floor($ends/10)*10;
	$resultado=cientos_de_miles($ends);
	if($end==1){
		$parcial=substr($resultado,0,strlen($resultado)-1);
		if($ends<2)	return $parcial." millón ".cientos_de_miles($num);
		else		return $parcial." millones ".cientos_de_miles($num);
		}
	return $resultado." millones ".cientos_de_miles($num);
	}

function Numero2Letras($num){
	$num=number_format("$num",2,".","");
	$ArregloNum=explode(".","$num");
	$str=millones($ArregloNum[0]);
	if(count($ArregloNum)==2)//si hay decimal
		$str.=" con ".$ArregloNum[1]."/100";
	return $str;
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

function mesEnLetras($mes)
{
	if ($mes==1)
		$mes="ENERO";
	elseif ($mes==2)
		$mes="FEBRERO";
	elseif ($mes==3)
		$mes="MARZO";
	elseif ($mes==4)
		$mes="ABRIL";
	elseif ($mes==5)
		$mes="MAYO";
	elseif ($mes==6)
		$mes="JUNIO";
	elseif ($mes==7)
		$mes="JULIO";
	elseif ($mes==8)
		$mes="AGOSTO";
	elseif ($mes==9)
		$mes="SEPTIEMBRE";
	elseif ($mes==10)
		$mes="OCTUBRE";
	elseif ($mes==11)
		$mes="NOVIEMBRE";
	elseif ($mes==12)
		$mes="DICIEMBRE";

	return $mes;
}
/*
$ACRONIMOS_CONTABILIDAD="'BNC%','SCO%','CXPSOP','SNO%'";

function CondicionSQLMovimientosContabilidad(){
	$ARREGLO_ACRONIMOS_CONTABILIDAD=explode(",",$GLOBALS["ACRONIMOS_CONTABILIDAD"]);
	$TEMP="C.acronimo_c LIKE ".$ARREGLO_ACRONIMOS_CONTABILIDAD[0];
	for($i=1;$i<count($ARREGLO_ACRONIMOS_CONTABILIDAD);$i++)
		$TEMP.=" OR C.acronimo_c LIKE ".$ARREGLO_ACRONIMOS_CONTABILIDAD[$i];
	return $TEMP;
	}//C.acronimo_c LIKE 'BNC%' OR C.acronimo_c LIKE 'SCO%'
*/

function InformacionAniosPresupuestarios(){
	$archivo=fopen("../modulo_administrador/anio_presupuestario.inf","r");
	if (!$archivo)
		return -1;
	$C=0;
	while (!feof($archivo)){
		$linea = fgets($archivo, 2048);
		if(trim($linea)=="")
			break;
		$AUX=explode(";",$linea);
		if($C==0){//1er caso, es la cabecera
			$CABECERA=$AUX;
			}
		else{
			for($i=0;$i<count($CABECERA);$i++)
				$RETORNO[$C-1][$CABECERA[$i]]=$AUX[$i];
			}
		$C++;
		}
	fclose($archivo);
	return $RETORNO;
	}

$NDigitos_Codigo_Articulo=4;
$NDigitos_Codigo_Requisicion=10;
$NDigitos_Codigo_Orden_Compra=10;
$NDigitos_Codigo_Documento=10;
$NDigitos_Codigo_SolicitudPago=10;
$NDigitos_Codigo_Comprobante=10;
$NDigitos_Codigo_IB=10;
$NDecimalesFormatoPresupuesto=0;
$NDigitos_Codigo_ComprobanteCargoRetencion=8;
$NDigitos_Codigo_VoucherCheque=8;
$NDigitos_Codigo_InventarioBienes=10;

function LimpiarCadena($str){
	return preg_replace("/[^A-Za-z0-9 &aacute;ÁéÉíÍóÓúÚñÑ]/","",$str);
	}

function replaceSpaceToHTML($str){
	//return ereg_replace("[ ]","&nbsp;",$str);
  return preg_replace("/ /","&nbsp;",$str);
	}


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

function FormatearContable($cadena){
	switch(strlen($cadena)){
		case 1: return $cadena;
		case 2: return $cadena[0].".".$cadena[1];
		case 3: return $cadena[0].".".$cadena[1].".".$cadena[2];
		case 5: return $cadena[0].".".$cadena[1].".".$cadena[2].".".$cadena[3].$cadena[4];
		case 7: return $cadena[0].".".$cadena[1].".".$cadena[2].".".$cadena[3].$cadena[4].".".$cadena[5].$cadena[6];
		case 9: return $cadena[0].".".$cadena[1].".".$cadena[2].".".$cadena[3].$cadena[4].".".$cadena[5].$cadena[6].".".$cadena[7].$cadena[8];
		case 10:return $cadena[0].".".$cadena[1].".".$cadena[2].".".$cadena[3].$cadena[4].".".$cadena[5].$cadena[6].".".$cadena[7].$cadena[8].".".$cadena[9].$cadena[10].$cadena[11];
		}
	return $cadena;
	}


function FormatearPartida($c){
	return $c[0].$c[1].$c[2].".".$c[3].$c[4].".".$c[5].$c[6].".".$c[7].$c[8];
	}


function deepLower($texto){
    //Letras minúsculas con acentos
    $texto = strtr($texto, "
    ĄĆĘŁŃÓŚŹŻABCDEFGHIJKLMNOPRSTUWYZQ
    XVЁЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ
    ÂÀÁÄÃÊÈÉËÎÍÌÏÔÕÒÓÖÛÙÚÜÇ
    ", "
    ąćęłńóśźżabcdefghijklmnoprstuwyzq
    xvёйцукенгшщзхъфывапролджэячсмитьбю
    âàáäãêèéëîíìïôõòóöûùúüç
    ");
    return strtolower($texto);
   	}


?>
