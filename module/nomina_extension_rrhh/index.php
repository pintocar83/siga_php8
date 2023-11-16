<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

include_once("../../class/nomina_extension_rrhh.class.php");

class MODULO extends nomina_extension_rrhh{
  public static function onInit(){
    $access=SIGA::access("nomina");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGenerar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGenerar($access, SIGA::param("id_hoja")));
        break;
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet($access, SIGA::param("id_hoja")));
        break;


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