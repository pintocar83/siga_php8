<?php
function sql_sort($sort){
	if(!$sort) return "";
	$sort=json_decode($sort,true);
	$return="";
	for($i=0;$i<count($sort);$i++){
		$return.=$sort[$i]["property"]." ".$sort[$i]["direction"];
		if($i<count($sort)-1)
			$return.=", ";
	}
	if(!$return) return "";
	return "ORDER BY ".$return;
}
?>
