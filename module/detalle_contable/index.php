<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

class MODULO{
   public static function onInit(){
    $access=SIGA::access("detalle_contable");//null,r,rw,a
    switch($_REQUEST["action"]){
      case "onCss":
      case "css":
        header('Content-Type: text/css; charset=utf-8');
        print self::onCss($access);
        break;
      case "onJavascript":
      case "js":
      case "javascript":  
        header('Content-Type: text/javascript; charset=utf-8');
        print self::onJavascript($access);
        break;
    }
  }
  
  public static function onCss($access){
    if(!$access) return;    
    return SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    return SIGA::js("main.js");
  }  
}

MODULO::onInit();
?>