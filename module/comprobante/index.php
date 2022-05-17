<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/comprobante.class.php");

class MODULO extends comprobante{
  public static function onInit(){
    $access=SIGA::access("comprobante");//null,r,rw,a    
    switch($_REQUEST["action"]){
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        $detalle=SIGA::param("detalle")===""?true:SIGA::param("detalle");
        print json_encode(self::onGet(SIGA::param("id"), $detalle));
        break;
      case "onGet_Archivo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_Archivo($access, SIGA::paramGet("archivo",false)));
        break;
      case "onGet_Correlativo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_Correlativo(SIGA::param("tipo")));
        break;
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        $mostrar=json_decode(SIGA::param("mostrar",false),true);
        print json_encode(self::onList( SIGA::paramUpper("text"),
                                        SIGA::param("start"),
                                        SIGA::param("limit"),
                                        SIGA::param("sort",false),
                                        $mostrar));
        break;
      case "onList_OC":
      case "onList_OS":
      case "onList_OP":
      case "onList_OC_OS":
      case "onList_OC_OS_OP":
        header('Content-Type: text/plain; charset=utf-8');
        $mostrar=json_decode(SIGA::param("mostrar",false),true);
        print json_encode(self::onList_OC_OS_OP(SIGA::paramUpper("text"),
                                                SIGA::param("start"),
                                                SIGA::param("limit"),
                                                SIGA::param("sort",false),
                                                $mostrar));
        break;
      case "onList_OP_pendiente":
        header('Content-Type: text/plain; charset=utf-8');
        $mostrar=json_decode(SIGA::param("mostrar",false),true);
        print json_encode(self::onList_OP_pendiente(SIGA::paramUpper("text"),
                                                    SIGA::param("start"),
                                                    SIGA::param("limit"),
                                                    SIGA::param("sort",false),
                                                    $mostrar));
        break;
      case "onList_OP_cheque":
        header('Content-Type: text/plain; charset=utf-8');
        $mostrar=json_decode(SIGA::param("mostrar",false),true);
        print json_encode(self::onList_OP_cheque( SIGA::paramUpper("text"),
                                                  SIGA::param("start"),
                                                  SIGA::param("limit"),
                                                  SIGA::param("sort",false),
                                                  $mostrar));
        break;
      case "onSet_Contabilizar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSet_Contabilizar($access,SIGA::param("id"),SIGA::param("contabilizado")));
        break;
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        
        $detalle=json_decode(SIGA::param("detalle",false),true);
        print json_encode(self::onSave( $access,
                                        SIGA::param("id"),
                                        SIGA::paramUpper("tipo"),
                                        SIGA::param("fecha"),
                                        SIGA::paramUpper("concepto"),
                                        SIGA::param("contabilizado"),
                                        SIGA::param("id_persona"),
                                        $detalle));
        break;
      case "onAnular":
        header('Content-Type: text/plain; charset=utf-8');
        $detalle=json_decode(SIGA::param("detalle",false),true);
        print json_encode(self::onAnular($access,SIGA::param("id"),SIGA::param("fecha"),$detalle));
        break;
      case "onDelete":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onDelete($access,SIGA::param("id")));
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