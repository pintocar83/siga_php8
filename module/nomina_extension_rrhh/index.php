<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;

include_once("../../class/nomina_extension_rrhh.class.php");

class MODULO extends nomina_extension_rrhh{
  public static function onInit(){
    $access=SIGA::access("nomina");//null,r,rw,a
    if($access!="rw")
      $access=SIGA::access("nomina_extension_rrhh");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGenerar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGenerar($access, SIGA::param("id_hoja"), SIGA::param("id_hoja_plantilla")));
        break;
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet($access, SIGA::param("id_hoja")));
        break;
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        $data=json_decode(SIGA::param("data",false),true);
        $ag_grid_state=json_decode(SIGA::param("ag_grid_state",false),true);
        print json_encode(self::onSave($access, SIGA::param("id_hoja"), $data, $ag_grid_state));
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