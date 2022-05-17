<?php
function column_hash($n){
	$valor=chr(65+($n%26));
	$vuelta=intval($n/26);
	if($vuelta>0)
		return column_hash($vuelta-1).$valor;
	return $valor;
}
?>