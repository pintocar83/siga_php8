<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
    exit;
include("../../class/cuenta_presupuestaria.class.php");

class MODULO extends cuenta_presupuestaria{
  public static function onInit(){
    $access=SIGA::access("cuenta_presupuestaria");    
    switch($_REQUEST["action"]){      
      case "onGet":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id_cuenta_presupuestaria")));
        break;
      case "onList":
      case "list":
        header('Content-Type: text/html; charset=utf-8');
        $filtro="";
        if(isset($_REQUEST["filtro"]))
          $filtro=str_clear($_REQUEST["filtro"]);//temporalmente (usado por el SIGAFS) ventana listar cuentas parametro envia por get con la url   
        if(!$filtro) $filtro="4%";
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),$filtro));
        break;
      case "onList_AP":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList_AP(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;      
      case "onList_401":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),"401%"));
        break; 
      case "onList_402":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),"402%"));
        break;
      case "onList_402_4010710_4010726":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),"402%|401000000|401070000|401071000|401072600"));
        break;
      case "onList_403":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),"403%"));
        break;
      case "onList_404":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),"404%"));
        break;      
      case "onExist":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onExist(SIGA::param("id_cuenta_presupuestaria")));
        break;
      case "onSave":
      case "save":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onSave($access,
                                      SIGA::param("id_cuenta_presupuestaria"),
                                      SIGA::param("id_cuenta_presupuestaria_seleccion"),
                                      SIGA::paramUpper("denominacion"),
                                      SIGA::param("padre")));
        break;
      case "onDelete":
      case "delete":
        header('Content-Type: text/html; charset=utf-8');
        print json_encode(self::onDelete($access,SIGA::param("id_cuenta_presupuestaria")));
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