<?php
include("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];
$params=$_POST;

switch($action){
  case "logout":    
  case "onLogout":
  	header('Content-Type: text/plain; charset=utf-8');
    logout::onLogout();
    break;
  case "css":
  case "onCss":  
    header('Content-Type: text/css; charset=utf-8');
    logout::css();
    break;
  case "js":
  case "javascript":
  case "onJavascript":
    header('Content-Type: text/javascript; charset=utf-8');
    logout::javascript();
    break;
  default:
    header('Content-Type: text/html; charset=utf-8');
    print "Acción no encontrada\n$action";
}

class logout{
  public static function css(){    
    print SIGA::css("main.css");
  }
    
  public static function javascript(){
    print SIGA::js("main.js");
  }
  
  public static function onLogout(){
    SIGA::close();
    print '{"success": true, "message": ""}';
  }
}
  
?>