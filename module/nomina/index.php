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
      case "onInit":
        header('Content-Type: text/plain; charset=utf-8');
        SIGA::$DBMode=PGSQL_ASSOC;
        include("../../class/nomina_periodo.class.php");
        include("../../class/nomina_periodo_tipo.class.php");
        include("../../class/nomina_escala_salarial.class.php");
        $return=[];
        $return["periodo"]         = nomina_periodo::onList(NULL,NULL,"",0,"ALL",'[{"property": "codigo", "direction": "ASC"}]')["result"];
        $return["periodo_tipo"]    = nomina_periodo_tipo::onList_Activo("",0,"ALL",'[{"property": "denominacion", "direction": "ASC"}]')["result"];
        $return["nomina"]          = self::onList("","","",0,"ALL",'[{"property": "tipo", "direction": "ASC"},{"property": "codigo_nomina", "direction": "ASC"}]')["result"];
        $return["cargo"]           = self::onList_Cargo("",0,"ALL",'[{"property": "cargo", "direction": "ASC"}]')["result"];
        $return["escala_salarial"] = nomina_escala_salarial::onList("",0,"ALL",'[{"property": "escala", "direction": "ASC"}]')["result"];
        $return["concepto_identificadores"] = self::onListConceptoIdentificadores();

        print json_encode($return);
        break;
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
      case "onAddEscala":
        $ids_ficha=json_decode(SIGA::param("id_ficha",false),true);
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onAddEscala($access,SIGA::param("id_nomina"),SIGA::param("id_periodo"),$ids_ficha,SIGA::param("id_concepto")));
        break;
      case "onAddValorFicha":
        $ids_ficha=json_decode(SIGA::param("id_ficha",false),true);
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onAddValorFicha($access,SIGA::param("id_nomina"),SIGA::param("id_periodo"),$ids_ficha,SIGA::param("id_concepto")));
        break;
      case "onAddConceptoExcelPreCargar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onAddConceptoExcelPreCargar($access,SIGA::param("id_nomina"),SIGA::param("id_periodo"),SIGA::param("archivo_extension",false),SIGA::param("archivo_contenido",false)));
        break;
      case "onAddConceptoExcelAplicar":
        header('Content-Type: text/plain; charset=utf-8');
        $data=json_decode(SIGA::param("data",false),true);
        print json_encode(self::onAddConceptoExcelAplicar($access,$data));
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
      case "onReversar":
        header('Content-Type: text/plain; charset=utf-8');
        $access=SIGA::access("nomina_contabilizar");
        print json_encode(self::onReversar($access,SIGA::param("id_periodo")));
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
      case "onPersona_QuitarInactivo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onPersona_QuitarInactivo($access,SIGA::param("id_periodo"),SIGA::param("id_nomina")));
        break;
      case "onPersona_Quitar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onPersona_Quitar($access,SIGA::param("id_ficha"),SIGA::param("id_periodo"),SIGA::param("id_nomina")));
        break;
      case "onPersona_CambiarNominaInactivo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onPersona_CambiarNominaInactivo($access,SIGA::param("id_periodo"),SIGA::param("id_nomina"),SIGA::param("id_nomina_anterior")));
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
        $filtro_busqueda=json_decode(SIGA::param("filtro_busqueda",false),true);
        print json_encode(self::onListFichaPeriodo(SIGA::param("id_nomina"),
                                                   SIGA::param("id_periodo"),
                                                   SIGA::param("start"),
                                                   SIGA::param("limit"),
                                                   $filtro_busqueda
                                                 ));
        break;
      case "onListConceptoPeriodo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onListConceptoPeriodo($access,
                                                      SIGA::param("id_nomina"),
                                                      SIGA::param("id_periodo")));
        break;
      case "onConfiguracionProyeccion_Get":
        header('Content-Type: text/plain; charset=utf-8');
          print json_encode(self::onConfiguracionProyeccion_Get($access));
        break;
      case "onConfiguracionProyeccion_Save":
        header('Content-Type: text/plain; charset=utf-8');
          print json_encode(self::onConfiguracionProyeccion_Save($access,SIGA::param("identificador",false),SIGA::param("porcentaje")));
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
