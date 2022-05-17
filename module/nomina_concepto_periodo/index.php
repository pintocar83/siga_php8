<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/nomina_concepto_periodo.class.php");

class MODULO extends nomina_concepto_periodo{
  public static function onInit(){
    $access=SIGA::access("nomina_concepto_periodo");//null,r,rw,a    
    switch($_REQUEST["action"]){      
      case "onList_Agregar":
        header('Content-Type: text/plain; charset=utf-8');
        $ids=json_decode(SIGA::param("ids",false),true);
        print json_encode(self::onList_Agregar( $ids,
                                                SIGA::paramUpper("text"),
                                                SIGA::param("start"),
                                                SIGA::param("limit"),
                                                SIGA::param("sort",false)));
        break;      
      case "onAgregar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onAgregar($access, SIGA::param("id_nomina"), SIGA::param("id_periodo"), SIGA::param("id_concepto")));
        break;
      case "onQuitar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onQuitar($access, SIGA::param("id_nomina"), SIGA::param("id_periodo"), SIGA::param("id_concepto")));
        break;
    }    
  }  
}

MODULO::onInit();
?>