<?php
function siga_yuicompressor_js($file,$regenerate=false){
	
	return file_get_contents("$file.js");
	
	if($regenerate==false)
		if(file_exists("$file.min.js"))
			return file_get_contents("$file.min.js");
	exec("java -jar /var/www/siga/library/yuicompressor-2.4.8.jar --type js -o '$file.min.js' '$file.js'");
	if(file_exists("$file.min.js"))
		return file_get_contents("$file.min.js");
	return file_get_contents("$file.js");
}
?>