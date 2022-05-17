<?php

function letra_mes($n){
	switch($n){
		case '1': case '01': return "enero";
		case '2': case '02': return "febrero";
		case '3': case '03': return "marzo";
		case '4': case '04': return "abril";
		case '5': case '05': return "mayo";
		case '6': case '06': return "junio";
		case '7': case '07': return "julio";
		case '8': case '08': return "agosto";
		case '9': case '09': return "septiembre";
		case '10': return "octubre";
		case '11': return "noviembre";
		case '12': return "diciembre";
	}
	return ""; 
}


?>