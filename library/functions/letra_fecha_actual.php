<?php
include_once("letra_mes.php");
include_once("letra_dia_semana.php");
function letra_fecha_actual(){
	return letra_dia_semana(date("N")).", ".date("d")." de ".letra_mes(date("m"))." de ".date("Y");
}
?>