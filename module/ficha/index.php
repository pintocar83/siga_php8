<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/ficha.class.php");

class MODULO extends ficha{
  public static function onInit(){
    $access=SIGA::access("ficha");//null,r,rw,a    
    switch($_REQUEST["action"]){      
      case "onList_Agregar":
        header('Content-Type: text/plain; charset=utf-8');        
        print json_encode(self::onList_Agregar( SIGA::param("id_nomina"),
                                                SIGA::param("id_periodo"),
                                                SIGA::paramUpper("text"),
                                                SIGA::param("start"),
                                                SIGA::param("limit"),
                                                SIGA::param("sort",false)));
        break;
      
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');        
        print json_encode(self::onList( SIGA::paramUpper("text"),
                                        SIGA::param("start"),
                                        SIGA::param("limit"),
                                        SIGA::param("sort",false)));
        break;
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("nacionalidad"),SIGA::param("cedula"),SIGA::param("id")));
        break;
      case "onGet_Select":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_Select(SIGA::param("id")));
        break;
      
      case "onAgregar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onAgregar($access, SIGA::param("id_nomina"), SIGA::param("id_periodo"), SIGA::param("id_ficha")));
        break;
      case "onQuitar":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onQuitar($access, SIGA::param("id_nomina"), SIGA::param("id_periodo"), SIGA::param("id_ficha")));
        break;
      
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave($access,
                                       SIGA::param("id"),
                                       SIGA::paramUpper("nacionalidad"),
                                       SIGA::param("cedula"),
                                       SIGA::paramUpper("primer_nombre"),
                                       SIGA::paramUpper("segundo_nombre"),
                                       SIGA::paramUpper("primer_apellido"),
                                       SIGA::paramUpper("segundo_apellido"),
                                       SIGA::param("fecha_nacimiento"),
                                       SIGA::param("genero"),
                                       SIGA::param("telefono"),
                                       SIGA::param("extension"),
                                       SIGA::param("correo"),
                                       SIGA::param("fecha_ingreso",false),
                                       SIGA::param("fecha_egreso",false),
                                       SIGA::param("codigo"),
                                       SIGA::param("activo")));
        break;
      
      case "onGet_ArchivoFoto":
        header('Content-Type: text/plain; charset=utf-8');        
        self::onGet_ArchivoFoto(SIGA::paramGet("archivo"));
        break;
      
      case "onGet_Archivo":
        header('Content-Type: text/plain; charset=utf-8');        
        self::onGet_Archivo($access, SIGA::paramGet("archivo"));
        break;
      
      case "onList_Archivo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_Archivo(SIGA::param("path")));
        break;
      
      case "onUpload_Archivo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onUpload_Archivo($access,SIGA::param("path"),$_FILES["upload"]));
        break;
      
      case "onDelete_Archivo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onDelete_Archivo($access,SIGA::param("archivo")));
        break;
      
      case "onRename_Archivo":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onRename_Archivo($access, SIGA::param("archivo"), SIGA::param("archivo_nuevo")));
        break;
      
      case "onAdd_Carpeta":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onAdd_Carpeta($access, SIGA::param("carpeta")));
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
    return SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    return SIGA::js("main.js");
  }
  
}
MODULO::onInit();
?>