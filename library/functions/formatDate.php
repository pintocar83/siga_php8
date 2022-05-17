<?php
function formatDate($f){
	list($ano,$mes,$dia)=explode("-",$f);
	return "$dia/$mes/$ano";
}
?>