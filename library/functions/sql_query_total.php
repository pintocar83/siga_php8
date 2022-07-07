<?php
function sql_query_total($sql){
	$cs=0;
	$i_select=-1;
	
	for($i=0;$i<strlen($sql);$i++){
		$sw=false;
		if(strcasecmp(substr($sql,$i,6),"select")==0){
			if($i_select==-1)
				$i_select=$i;
			$cs++;
			$sw=true;
		}
		if(strcasecmp(substr($sql,$i,4),"from")==0){
			$cs--;
			$sw=true;
		}
		if($sw==true and $cs==0)
			return substr_replace($sql," count(*) total ",$i_select+6,$i-($i_select+6));
	}
	return null;	
}
?>
