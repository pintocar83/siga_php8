<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
    exit;
include("../../class/cuenta_contable.class.php");

class MODULO extends cuenta_contable{
  public static function onInit(){
    $access=SIGA::access("cuenta_contable");    
    switch($_REQUEST["action"]){ 
      case "onGet":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id_cuenta_contable")));
        break;
      case "onList":
      case "list":
        header('Content-Type: text/html; charset=utf-8');
        $filtro="";
        if(isset($_REQUEST["filtro"]))
          $filtro=str_clear($_REQUEST["filtro"]);
        if(!$filtro) $filtro="%";
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),$filtro));
        break;      
      case "onExist":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onExist(SIGA::param("id_cuenta_contable")));
        break;
      case "onSave":
      case "save":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onSave($access,
                                      SIGA::param("id_cuenta_contable"),
                                      SIGA::param("id_cuenta_contable_seleccion"),
                                      SIGA::paramUpper("denominacion")));
        break;
      case "onDelete":
      case "delete":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onDelete($access,SIGA::param("id_cuenta_contable")));
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