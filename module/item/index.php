<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

if(isset($_GET["id_item_tipo"]))
  $_POST["id_item_tipo"]=$_GET["id_item_tipo"];
if(isset($_GET["disponibilidad"]))
  $_POST["disponibilidad"]=$_GET["disponibilidad"]; 

include_once("../../class/item.class.php");

class MODULO extends item{
  public static function onInit(){
    switch(SIGA::param("id_item_tipo")){
      case "1": $access=SIGA::access("materiales"); break;//null,r,rw,a
      case "2": $access=SIGA::access("bienes"); break;//null,r,rw,a
      case "3": $access=SIGA::access("servicios"); break;//null,r,rw,a
      default:
        $access=SIGA::access("item");
    }  
    
    switch($_REQUEST["action"]){
      case "onGetCodigo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGetCodigo(SIGA::param("id_item_tipo")));
        break;
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id")));
        break;      
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList(SIGA::param("id_item_tipo",false),SIGA::param("disponibilidad",false),SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;      
      case "onSave":
      case "save":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave( $access,
                                        SIGA::param("id_item_tipo"),
                                        SIGA::param("id"),
                                        SIGA::paramUpper("codigo"),
                                        SIGA::paramUpper("denominacion"),
                                        SIGA::param("id_cuenta_presupuestaria"),
                                        SIGA::param("aplica_iva")));
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