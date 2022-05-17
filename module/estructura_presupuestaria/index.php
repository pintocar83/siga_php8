<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/estructura_presupuestaria.class.php");

class MODULO extends estructura_presupuestaria{
  public static function onInit(){
    $access=SIGA::access("estructura_presupuestaria");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGet_Codigo":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onGet_Codigo(SIGA::param("id_accion_subespecifica")));
        break;
      case "onGet_IdCodigo":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onGet_IdCodigo(SIGA::param("codigo")));
        break;
      case "onList_AP":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_AP(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      case "onList_APNomina":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_APNomina(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      //ACCION CENTRALIZADA
      case "onList_AccionCentralizada":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_AccionCentralizada(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      case "onList_AccionCentralizada_AP":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_AccionCentralizada_AP(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      case "onSave_AccionCentralizada":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onSave_AccionCentralizada( $access,
                                                              SIGA::param("id_accion_centralizada"),
                                                              SIGA::paramUpper("tipo"),
                                                              SIGA::paramUpper("codigo"),
                                                              SIGA::paramUpper("denominacion")));
        break;
      case "onDelete_AccionCentralizada":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onDelete_AccionCentralizada($access,SIGA::param("id_accion_centralizada")));
        break;
      //ACCION ESPECIFICA
      case "onList_AccionEspecifica":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_AccionEspecifica(SIGA::param("id_accion_centralizada"),SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      case "onList_AccionEspecifica_AP":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_AccionEspecifica_AP(SIGA::param("id_accion_centralizada"),SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      case "onSave_AccionEspecifica":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onSave_AccionEspecifica( $access,
                                                            SIGA::param("id_accion_centralizada"),
                                                            SIGA::param("id_accion_especifica"),
                                                            SIGA::paramUpper("codigo"),
                                                            SIGA::paramUpper("denominacion")));
        break;
      case "onDelete_AccionEspecifica":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onDelete_AccionEspecifica($access,SIGA::param("id_accion_especifica")));
        break;      
      //ACCION SUBESPECIFICA
      case "onList_AccionSubEspecifica":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_AccionSubEspecifica(SIGA::param("id_accion_especifica"),SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      case "onList_AccionSubEspecifica_AP":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_AccionSubEspecifica_AP(SIGA::param("id_accion_especifica"),SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;
      case "onSave_AccionSubEspecifica":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onSave_AccionSubEspecifica(  $access,
                                                                SIGA::param("id_accion_especifica"),
                                                                SIGA::param("id_accion_subespecifica"),
                                                                SIGA::paramUpper("codigo"),
                                                                SIGA::paramUpper("denominacion")));
        break;
      case "onDelete_AccionSubEspecifica":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onDelete_AccionSubEspecifica($access,SIGA::param("id_accion_subespecifica")));
        break;      
      
      case "onCss":
      case "css":
        header('Content-Type: text/css; charset=utf-8');
        self::onCss($access);
        break;
      case "onJavascript":
      case "js":
      case "javascript":  
        header('Content-Type: text/javascript; charset=utf-8');
        self::onJavascript($access);
        break;
    }    
  }  
  
  public static function onCss($access){
    if(!$access) return;
    print SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
}

MODULO::onInit();
?>