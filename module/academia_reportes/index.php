<?php
include_once("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];


$access=SIGA::access("academia_reportes");//null,r,rw,a

switch($action){
  case "css":
  case "onCss":  
    header('Content-Type: text/css; charset=utf-8');
    academia_reportes::onCss();
    break;
  case "js":
  case "javascript":
  case "onJavascript":
    header('Content-Type: text/javascript; charset=utf-8');
    academia_reportes::onJavascript();
    break;
  default:
    header('Content-Type: text/html; charset=utf-8');
    print "Acción no encontrada\n$action";
}




class academia_reportes{
  public static function onCss(){        
  }
  
  public static function onJavascript(){
    print SIGA::js("main.js");
  }  
}

  
?>
