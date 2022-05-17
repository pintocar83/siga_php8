<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/anio_detalle.class.php");

class MODULO extends anio_detalle{
  public static function onInit(){  
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/css; charset=utf-8');
        print json_encode(self::onGet());
        break;
      case "onSave":
        header('Content-Type: text/css; charset=utf-8');
        print json_encode(self::onSave(SIGA::param("mes_cerrado")));
        break;  
    }    
  }  
}
MODULO::onInit();
?>