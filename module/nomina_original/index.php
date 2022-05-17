<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../library/functions/dias_meses.php");
include_once("../../class/nomina.class.php");

class MODULO extends nomina{
  public static function onInit(){
    $access=SIGA::access("nomina");
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet($access,SIGA::param("id_nomina"),SIGA::param("id_periodo")));
        break;
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList(SIGA::param("id_periodo"),SIGA::param("tipo"),SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;      
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        $data=json_decode(SIGA::param("data",false),true);
        print json_encode(self::onSave($access,SIGA::param("id_nomina"),SIGA::param("id_periodo"),$data));
        break;
      case "onAdd":
        $ids_ficha=json_decode(SIGA::param("id_ficha",false),true);
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onAdd($access,SIGA::param("id_nomina"),SIGA::param("id_periodo"),$ids_ficha,SIGA::param("id_concepto")));
        break;
      case "onRemove":
        $ids_ficha=json_decode(SIGA::param("id_ficha",false),true);
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onRemove($access,SIGA::param("id_nomina"),SIGA::param("id_periodo"),$ids_ficha,SIGA::param("id_concepto")));
        break;      
      case "onClose":
        header('Content-Type: text/plain; charset=utf-8');
        $access=SIGA::access("nomina_cerrar_periodo");
        print json_encode(self::onClose($access,SIGA::param("id_periodo")));
        break;
      case "onContabilizar":
        header('Content-Type: text/plain; charset=utf-8');
        $access=SIGA::access("nomina_contabilizar");
        print json_encode(self::onContabilizar($access,SIGA::param("id_periodo"),SIGA::param("fecha"),SIGA::param("tipo")));
        break;
      case "onList_Cargo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_Cargo(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      case "onPersona_CambiarCargo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onPersona_CambiarCargo($access,SIGA::param("id_ficha"),SIGA::param("id_periodo"),SIGA::param("id_cargo")));
        break;
      case "onPersona_CambiarEP":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onPersona_CambiarEP($access,SIGA::param("id_ficha"),SIGA::param("id_periodo"),SIGA::param("id_accion_subespecifica")));
        break;
      case "onPersona_CambiarNomina":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onPersona_CambiarNomina($access,SIGA::param("id_ficha"),SIGA::param("id_periodo"),SIGA::param("id_nomina"),SIGA::param("id_nomina_anterior")));
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
      
      case "onListFichaPeriodo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onListFichaPeriodo(SIGA::param("id_nomina"),
                                                   SIGA::param("id_periodo"),
                                                   SIGA::param("start"),
                                                   SIGA::param("limit")));
        break;
      case "onListConceptoPeriodo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onListConceptoPeriodo($access,
                                                      SIGA::param("id_nomina"),
                                                      SIGA::param("id_periodo")));
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