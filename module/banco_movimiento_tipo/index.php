<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/banco_movimiento_tipo.class.php");

class MODULO extends banco_movimiento_tipo{
  public static function onInit(){  
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/css; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("tipo")));
        break;
      case "onList":
        header('Content-Type: text/css; charset=utf-8');
        print json_encode(self::onList( SIGA::paramUpper("text"),
                                        SIGA::param("start"),
                                        SIGA::param("limit"),
                                        SIGA::param("sort",false)));
        break;
    }    
  }  
}

MODULO::onInit();
?>