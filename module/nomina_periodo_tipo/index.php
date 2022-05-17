<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include("../../class/nomina_periodo_tipo.class.php");

class MODULO extends nomina_periodo_tipo{
  public static function onInit(){
    $access=SIGA::access("nomina_periodo_tipo");
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("tipo")));
        break;
      case "onList_Activo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_Activo(SIGA::param("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
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