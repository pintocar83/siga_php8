<?php
include("../../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../../class/nomina_extension_rrhh_hoja.class.php");


class MODULO extends nomina_extension_rrhh_hoja{
  public static function onInit(){
    $access=SIGA::access("nomina_extension_rrhh_hoja");//null,r,rw,a
    $access="rw";
    switch($_REQUEST["action"]){
      case "onInit":
        header('Content-Type: text/plain; charset=utf-8');
        SIGA::$DBMode=PGSQL_ASSOC;
        include("../../../class/nomina.class.php");
        include("../../../class/nomina_periodo.class.php");
        include("../../../class/nomina_periodo_tipo.class.php");
        $return=[];
        $return["nomina"]          = nomina::onList("","","",0,"ALL",'[{"property": "tipo", "direction": "ASC"},{"property": "codigo_nomina", "direction": "ASC"}]')["result"];
        $return["periodo"]         = nomina_periodo::onList(NULL,NULL,"",0,"ALL",'[{"property": "codigo", "direction": "ASC"}]')["result"];
        $return["periodo_tipo"]    = nomina_periodo_tipo::onList_Activo("",0,"ALL",'[{"property": "denominacion", "direction": "ASC"}]')["result"];

        print json_encode($return);
        break;
      case "onCorrelativo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onCorrelativo());
        break;
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id")));
        break;
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList( SIGA::paramUpper("text"),
                                        SIGA::param("start"),
                                        SIGA::param("limit"),
                                        SIGA::param("sort",false),
                                        SIGA::param("tipo")
                                      ));
        break;
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave($access,
                                       SIGA::param("id"),
                                       SIGA::paramUpper("codigo"),
                                       SIGA::paramUpper("descripcion"),
                                       SIGA::paramUpper("tipo"),
                                       SIGA::param("id_periodo"),
                                       SIGA::param("id_nomina"),
                                       SIGA::param("activo")
                                       ));
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