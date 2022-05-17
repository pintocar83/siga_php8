<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include("../../class/usuario_preferencias.class.php");

class MODULO extends usuario_preferencias{
  public static function onInit(){
    $access="rw"; 
    switch($_REQUEST["action"]){      
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave($access,SIGA::param("background")));
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