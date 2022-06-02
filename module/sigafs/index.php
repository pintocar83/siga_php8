<?php
include_once("../../library/siga.config.php");
include_once("../../library/siga.class.php");
if(!isset($_GET["q"]))
  exit;

$q=trim(str_replace(array("\"","'","/*","*/","--",";","&"),"",$_GET["q"]));
//$q=$_GET["q"];

$file="core/$q.js";
/*
$file_min="core/$q.min.js";
if(file_exists("$file_min"))
  $file="$file_min";
else{
  exec("java -jar /var/www/siga/library/yuicompressor-2.4.8.jar --type js -o '$file_min' '$file'");
  if(file_exists("$file_min"))
    $file="$file_min";
}
*/

?>
<!doctype html>
<html>
  <head>
    <link type="text/css" rel="stylesheet" href="css/tabpane.css">
    <link type="text/css" rel="stylesheet" href="css/sigafs.css">      
    <link type="text/css" rel="stylesheet" href="library/jscalendar/skins/aqua/theme.css" title="Aqua">
    <script type="text/javascript" src="library/jscalendar/calendar.js"></script>
    <script type="text/javascript" src="library/jscalendar/calendar-es2.js"></script>
    <script type="text/javascript" src="library/jscalendar/jscalendar.js"></script>    
    <script type="text/javascript" src="library/tabpane.js" ></script>    
    <script type="text/javascript" src="library/x.js"></script>
    <script type="text/javascript" src="library/xtrim.js"></script>
    <script type="text/javascript" src="library/manipularDom.js"></script>
    <script type="text/javascript" src="library/AjaxRequest.js"></script>
    <script type="text/javascript" src="library/funciones_generales.js?ver=2.2.6"></script>
    <script type="text/javascript" src="library/sigafs.js?ver=2.2.6"></script>
    <script type='text/javascript' src="<?php print "$file?ver=2.2.6";?>"></script>
  </head>
  <body>
    <input type="hidden" id="FECHA_ACTUAL_SIGAFS" value="<?php print date('d/m/Y');?>" />
    <?php include("core/$q.php");?>
  </body>
</html>
<script type="text/javascript" src="library/sigafs_postform.js?ver=2.2.6"></script>