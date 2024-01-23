<?php
include("../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;
include_once("../../class/persona.class.php");

class MODULO extends persona{
  public static function onInit(){
    $access=SIGA::access("persona");
    switch($_REQUEST["action"]){
      case "onGetSelect":
      case "onGet_Select":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_Select(SIGA::param("id")));
        break;
      case "onGet":
      case "get":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet(SIGA::param("id")));
        break;

      case "onListSelect":
      case "list->select":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onListSelect(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));//persona::onList($params["text"],$params["start"],$params["limit"]);
        break;

      case "onList_Select":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_Select(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),SIGA::param("tipo")));
        break;

      case "onList_OP_pendiente":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_OP_pendiente(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),SIGA::param("tipo")));
        break;

      case "onGet_PersonaNatural":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_PersonaNatural(SIGA::param("id")));
        break;

      case "onGet_PersonaCNE":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_PersonaCNE(SIGA::paramUpper("nacionalidad"),SIGA::param("cedula")));
        break;

      case "onGet_PersonaJuridica":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onGet_PersonaJuridica(SIGA::param("id")));
        break;

      case "onList_PersonaNatural":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_PersonaNatural(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));//persona::onList($params["text"],$params["start"],$params["limit"]);
        break;
      case "onList_PersonaJuridica":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList_PersonaJuridica(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));//persona::onList($params["text"],$params["start"],$params["limit"]);
        break;

      case "onSave_PersonaNatural":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave_PersonaNatural($access,
                                       SIGA::param("id"),
                                       SIGA::paramUpper("identificacion_tipo"),
                                       SIGA::param("identificacion_numero"),
                                       SIGA::paramUpper("primer_nombre"),
                                       SIGA::paramUpper("segundo_nombre"),
                                       SIGA::paramUpper("primer_apellido"),
                                       SIGA::paramUpper("segundo_apellido"),
                                       SIGA::param("telefono"),
                                       SIGA::param("correo"),
                                       NULL,
                                       NULL,
                                       SIGA::paramUpper("cuenta_bancaria_principal"),
                                       SIGA::paramUpper("cuenta_bancaria_secundaria")));
        break;

      case "onSave_PersonaJuridica":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave_PersonaJuridica($access,
                                       SIGA::param("id"),
                                       SIGA::paramUpper("identificacion_tipo"),
                                       SIGA::param("identificacion_numero"),
                                       SIGA::paramUpper("denominacion"),
                                       SIGA::param("telefono"),
                                       SIGA::param("correo"),
                                       SIGA::paramUpper("direccion"),
                                       SIGA::paramUpper("cuenta_bancaria_principal"),
                                       SIGA::paramUpper("cuenta_bancaria_secundaria")));
        break;


      case "onList":
      case "list":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));//persona::onList($params["text"],$params["start"],$params["limit"]);
        break;
      case "onDelete":
      case "delete":
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