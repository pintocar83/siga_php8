<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
  
include_once("../../class/comprobante.class.php");

class MODULO extends comprobante{
  public static function onInit(){
    $access=SIGA::access("orden_servicio");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGet_Correlativo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_Correlativo("OS"));
        break;
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id")));
        break;
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList( SIGA::paramUpper("text"),
                                        SIGA::param("start"),
                                        SIGA::param("limit"),
                                        SIGA::param("sort",false),
                                        SIGA::param("mes"),
                                        "",
                                        '["OS"]'));
        break;
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        $items=json_decode(SIGA::param("items",false),true);
        print json_encode(self::onSave( $access,
                                        SIGA::param("id"),
                                        SIGA::param("id_item_tipo"),
                                        SIGA::param("fecha"),
                                        SIGA::paramUpper("concepto"),
                                        SIGA::param("id_accion_subespecifica"),
                                        SIGA::param("id_fuente_recursos"),
                                        $items));
        break;
      case "onDelete":
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