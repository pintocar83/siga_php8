<?php
if(!isset($_GET["persona"]) or !isset($_GET["certificado"])) exit;

$persona=$_GET["persona"];
$certificado=$_GET["certificado"];

$archivo="../../data/siga/persona/$persona/certificado/$certificado.pdf";
header("Content-Type: application/pdf; charset=binary;");
header("Content-Length: ".filesize($archivo));
header("Content-Transfer-Encoding: Binary");
header("Content-Disposition: inline; filename=\"CERTIFICADO_$certificado_($persona).pdf\"");
readfile($archivo);
?>