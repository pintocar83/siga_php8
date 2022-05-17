<?php
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

function letra_numero($num,$decimales=false){
	if($decimales==false)
		return millones($num);
	$num=number_format("$num",2,".","");
	$ArregloNum=explode(".","$num");
	$str=millones($ArregloNum[0]);
	if(count($ArregloNum)==2)//si hay decimal
		$str.=" con ".$ArregloNum[1]."/100";
	return $str;
	}
  
?>