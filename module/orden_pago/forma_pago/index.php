<?php
include("../../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

class MODULO {
  public static function onInit(){
    $access="rw";
    if(!$access) exit;
    switch($_REQUEST["action"]){
      case "onCss":
      case "css":
        header('Content-Type: text/css; charset=utf-8');
        print self::onCss();
        break;
      case "onJavascript":
      case "js":
      case "javascript":  
        header('Content-Type: text/javascript; charset=utf-8');
        print self::onJavascript();
        break;
    }    
  }  
  
  public static function onCss(){
    return SIGA::css("main.css");
  }
  
  public static function onJavascript(){
    return SIGA::js("main.js");
  }
}

MODULO::onInit();
?>
