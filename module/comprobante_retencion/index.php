<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/comprobante_retencion.class.php");

class MODULO extends comprobante_retencion{
  public static function onInit(){
    $access=SIGA::access("comprobante_retencion");//null,r,rw,a
    switch($_REQUEST["action"]){
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
                                        SIGA::param("id_retencion_tipo")));
        break;
      case "onList_Factura":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_Factura( SIGA::paramUpper("text"),
                                                SIGA::param("start"),
                                                SIGA::param("limit"),
                                                SIGA::param("sort",false),
                                                SIGA::param("id")));
        break;
      case "onList_FacturaNoAsociada":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_FacturaNoAsociada( SIGA::paramUpper("text"),
                                                          SIGA::param("start"),
                                                          SIGA::param("limit"),
                                                          SIGA::param("sort",false),
                                                          SIGA::param("id")));
        break;
      case "onNew":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onNew($access, SIGA::param("id_retencion_tipo")));
        break;
      case "onUpdate_Numero":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onUpdate_Numero($access, SIGA::param("id"), SIGA::param("numero")));
        break;
      case "onUpdate_Fecha":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onUpdate_Fecha($access, SIGA::param("id"), SIGA::param("fecha")));
        break;
      case "onDelete_Factura":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onDelete_Factura($access, SIGA::param("id"), SIGA::param("id_factura")));
        break;
      case "onAdd_Factura":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onAdd_Factura($access, SIGA::param("id"), SIGA::param("id_factura")));
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