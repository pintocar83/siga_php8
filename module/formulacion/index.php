<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/formulacion.class.php");

class MODULO extends formulacion{
  public static function onInit(){
    $access=SIGA::access("formulacion");//null,r,rw,a
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("anio"),
                                      SIGA::paramUpper("tipo"),
                                      SIGA::param("id_accion_subespecifica")));
        break;


      case "onSave":
      case "save":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave( $access,
                                        SIGA::param("anio"),
                                        SIGA::paramUpper("tipo"),
                                        SIGA::param("id_accion_subespecifica"),
                                        SIGA::param("data",false),
                                        SIGA::param("asignar")));
        break;
      case "onDelete_Asignacion":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onDelete_Asignacion( $access,
                                        SIGA::param("anio"),
                                        SIGA::paramUpper("tipo"),
                                        SIGA::param("id_accion_subespecifica")));
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