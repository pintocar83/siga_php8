<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/factura.class.php");

class MODULO extends factura{
  public static function onInit(){
    $access=SIGA::access("factura");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/css; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id")));
        break;
      case "onList":
        header('Content-Type: text/css; charset=utf-8');
        print json_encode(self::onList( SIGA::paramUpper("text"),
                                        SIGA::param("start"),
                                        SIGA::param("limit"),
                                        SIGA::param("sort",false),
                                        SIGA::param("mes")));
        break;
      case "onSave":
        header('Content-Type: text/css; charset=utf-8');
        print json_encode(self::onSave( $access,
                                        SIGA::param("id"),
                                        SIGA::param("id_persona"),
                                        SIGA::param("fecha"),
                                        SIGA::paramUpper("numero_factura"),
                                        SIGA::paramUpper("numero_control"),
                                        SIGA::param("total"),
                                        SIGA::param("informacion_iva",false),
                                        SIGA::param("informacion_islr",false),
                                        SIGA::param("informacion_1x1000",false)));
        break;
      case "onDelete":
        header('Content-Type: text/css; charset=utf-8');
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