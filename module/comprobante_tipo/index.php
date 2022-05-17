<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/comprobante_tipo.class.php");

class MODULO extends comprobante_tipo{
  public static function onInit(){
    $access=SIGA::access("comprobante_tipo");
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/css; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("tipo")));
        break;
    }    
  }  
}

MODULO::onInit();
?>