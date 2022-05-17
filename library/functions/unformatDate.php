<?php
function unformatDate($f){
	list($dia,$mes,$ano)=explode("/",$f);
	return "$ano-$mes-$dia";
}
?>