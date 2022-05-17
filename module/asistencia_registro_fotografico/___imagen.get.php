<?php
if(!isset($_REQUEST["id"])||!$_REQUEST["id"]) exit;

$id=$_REQUEST["id"];

include_once("../../library/include.php");
//include_once("../../library/siga.config.php");
//include_once("../../library/siga.class.php");
if(!file_exists(SIGA::databasePath()."/asistencia_registro/$id.png")){
  header("Content-Type: image/png");
  print base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAAAAAA6fptVAAAACklEQVQYV2P4DwABAQEAWk1v8QAAAABJRU5ErkJggg==");
  exit;
}



$finfo = new finfo(FILEINFO_MIME);
$type  = $finfo->file(SIGA::databasePath()."/asistencia_registro/$id.png");
header("Content-Type: $type");
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: inline; filename='$id.png'");
readfile(SIGA::databasePath()."/asistencia_registro/$id.png");
?>