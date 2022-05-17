<?php
include_once("../../library/include.php");
if(!isset($_REQUEST["action"]))
    exit;
$action=$_REQUEST["action"];

class MODULO{
  public static function onInit(){
    $access=SIGA::access("ficha_consulta");
    switch($_REQUEST["action"]){   
      case "onGet":
        header('Content-Type: text/plain; charset=utf-8');        
        print json_encode(self::onGet($access));
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
  
  public static function onGet($access){
    if(!$access) return;
    include_once("../../class/ficha.class.php");
    $db=SIGA::DBController();
    $sql="select
            identificacion_tipo,
            identificacion_numero
          from
            modulo_base.usuario as u,
            modulo_base.persona as p
          where
            u.usuario like '".SIGA::user()."' and
            u.id_persona_responsable=p.id";
    $persona=$db->Execute($sql);    
    return ficha::onGet($persona[0]["identificacion_tipo"],$persona[0]["identificacion_numero"]);
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
