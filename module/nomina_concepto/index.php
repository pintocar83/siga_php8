<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/nomina_concepto.class.php");

class MODULO extends nomina_concepto{
  public static function onInit(){
    $access=SIGA::access("nomina_concepto");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGet_Correlativo":
        header('Content-Type: text/plain; charset=utf-8');        
        print json_encode(self::onGet_Correlativo());        
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
      case "onList_Afectacion":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_Afectacion(SIGA::paramUpper("id_concepto"),
                                                  SIGA::param("start"),
                                                  SIGA::param("limit"),
                                                  SIGA::param("sort",false)));
        break;
      
      case "onList_Formula":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_Formula(SIGA::paramUpper("id_concepto"),
                                                SIGA::param("start"),
                                                SIGA::param("limit"),
                                                SIGA::param("sort",false)));
        break;
      
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');        
        print json_encode(self::onSave($access,
                                       SIGA::param("id"),
                                       SIGA::paramUpper("codigo"),
                                       SIGA::paramUpper("concepto"),
                                       SIGA::paramUpper("identificador"),
                                       SIGA::paramUpper("tipo"),
                                       SIGA::param("orden")
                                       ));        
        break;
      
      case "onSave_Afectacion":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave_Afectacion($access,
                                                  SIGA::param("id_concepto"),
                                                  SIGA::param("id_nomina"),
                                                  SIGA::param("fecha"),
                                                  SIGA::param("id_cuenta_presupuestaria"),
                                                  SIGA::param("id_cuenta_presupuestaria_ap"),
                                                  SIGA::param("id_cuenta_contable"),
                                                  SIGA::param("id_cuenta_contable_ap")
                                                  ));
        break;
      
      case "onSave_Formula":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave_Formula($access,
                                                  SIGA::param("id_concepto"),
                                                  SIGA::param("fecha"),
                                                  SIGA::paramUpper("formula_tipo"),
                                                  SIGA::paramUpper("definicion"),
                                                  SIGA::paramUpper("definicion_ap")
                                                  ));
        break;
      
      case "onDelete_Afectacion":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onDelete_Afectacion($access,
                                                    SIGA::param("id_concepto"),
                                                    SIGA::param("id_nomina"),
                                                    SIGA::param("fecha")
                                                    ));
        break;
      case "onDelete_Formula":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onDelete_Formula( $access,
                                                  SIGA::param("id_concepto"),
                                                  SIGA::param("fecha"),
                                                  SIGA::paramUpper("formula_tipo")
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