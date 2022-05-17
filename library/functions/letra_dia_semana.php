<?php
function letra_dia_semana($n){
	switch($n){
		case '1': return "lunes";
		case '2': return "martes";
		case '3': return "miércoles";
		case '4': return "jueves";
		case '5': return "viernes";
		case '6': return "sábado";
		case '7': return "domingo";
	}
	return "";
}

?>