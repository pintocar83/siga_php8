<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/fuente_recursos.class.php");

class MODULO extends fuente_recursos{
  public static function onInit(){
    $access=SIGA::access("fuente_recursos");//null,r,rw,a
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id")));
        break;
      case "onList":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
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
        print selt::onJavascript($access);
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