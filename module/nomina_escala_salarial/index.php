<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;


include_once("escala_salarial.class.php");

class MODULO extends escala_salarial{
  public static function onInit(){    
    $access=SIGA::access("nomina");   
    
    switch($_REQUEST["action"]){      
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id")));
        break;      
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;      
      case "onSave":
      case "save":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave( $access,
                                        SIGA::param("id"),
                                        SIGA::paramUpper("escala"),
                                        SIGA::paramUpper("sueldo_basico")));
        break;
      case "onDelete":
      case "delete":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onDelete($access,SIGA::param("id")));
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