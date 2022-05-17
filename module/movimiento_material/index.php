<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("class.php");

class MODULO extends movimiento_material{
  public static function onInit(){
    $access=SIGA::access("movimiento_material");//null,r,rw,a
    switch($_REQUEST["action"]){
      case "onGetCorrelativo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGetCorrelativo(SIGA::paramUpper("tipo")));
        break;
      case "onGet":
      case "get":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id")));
        break;
      case "onGetByRequisicion":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGetByRequisicion(SIGA::param("id_requisicion")));
        break;
      case "onList":
      case "list":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList( SIGA::paramUpper("text"),
                                        SIGA::param("start"),
                                        SIGA::param("limit"),
                                        SIGA::param("sort",false)));
        break;
      case "onSave":
      case "save":
        header('Content-Type: text/plain; charset=utf-8');
        //actualizar lista de articulos
        $items=json_decode(SIGA::param("items",false),true);
        print json_encode(self::onSave(
          $access,
          SIGA::param("id"),
          SIGA::paramUpper("tipo"),
          SIGA::param("fecha"),
          SIGA::paramUpper("concepto"),
          $items));
        break;
      case "onSaveByRequisicion":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSaveByRequisicion(
          $access,
          SIGA::param("id_requisicion"),
          SIGA::param("fecha"),
          SIGA::paramUpper("concepto"),
          SIGA::param("items"),
          SIGA::param("aprobar")));
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