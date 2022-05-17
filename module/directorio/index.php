<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include("../../class/directorio.class.php");

class MODULO extends directorio{
  public static function onInit(){
    $access="r"; 
    switch($_REQUEST["action"]){
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),SIGA::paramUpper("tipo")));
        break;    
      case "onCss":
      case "css":
        header('Content-Type: text/css; charset=utf-8');
        self::onCss($access);
        break;
      case "onJavascript":
      case "js":
      case "javascript":  
        header('Content-Type: text/javascript; charset=utf-8');
        self::onJavascript($access);
        break;
    }    
  }
  
  public static function onCss($access){
    print SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }  
}

MODULO::onInit();
?>