<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/banco_cuenta.class.php");

class MODULO extends banco_cuenta{
  public static function onInit(){
    $access=SIGA::access("banco_cuenta");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        if(SIGA::param("id"))
          print json_encode(self::onGet(SIGA::param("id")));
        else if(SIGA::param("numero_cuenta"))
          print json_encode(self::onGet2(SIGA::param("numero_cuenta")));
        break;
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList( SIGA::paramUpper("text"),
                                        SIGA::param("start"),
                                        SIGA::param("limit"),
                                        SIGA::param("sort",false)));
        break;
      case "onSave":
      case "save":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onSave($access,
                                      SIGA::param("id"),
                                      SIGA::param("numero_cuenta"),
                                      SIGA::paramUpper("denominacion"),
                                      SIGA::param("fecha_apertura"),
                                      SIGA::param("fecha_cierre"),
                                      SIGA::param("cuenta_activa"),
                                      SIGA::param("id_banco"),
                                      SIGA::param("id_banco_cuenta_tipo"),
                                      SIGA::param("id_cuenta_contable")));
        break;
      case "onDelete":
      case "delete":
        header('Content-Type: text/html; charset=utf-8');
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