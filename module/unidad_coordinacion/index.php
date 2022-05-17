<?php
include_once("../../library/include.php");
if(!isset($_REQUEST["action"]))
    exit;
$action=$_REQUEST["action"];

$access=SIGA::access("unidad_coordinacion");//null,r,rw,a

switch($action){
  case "onGetSelect":
  case "get->select":
    header('Content-Type: text/html; charset=utf-8');
    unidad_coordinacion::onGetSelect(SIGA::param("id"));
    break;
  case "onGet":
  case "get":
    header('Content-Type: text/html; charset=utf-8');
    unidad_coordinacion::onGet(SIGA::param("id"));
    break;
  case "onListSelect":
  case "list->select":
    header('Content-Type: text/html; charset=utf-8');
    unidad_coordinacion::onListSelect(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));
    break;  
  case "onList":
  case "list":
    header('Content-Type: text/html; charset=utf-8');
    unidad_coordinacion::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));
    break;
  case "onSave":
    header('Content-Type: text/plain; charset=utf-8');
    print json_encode(unidad_coordinacion::onSave($access,SIGA::param("id"),SIGA::paramUpper("coordinacion")));
    break;
  case "onDelete":
    header('Content-Type: text/plain; charset=utf-8');
    print json_encode(unidad_coordinacion::onDelete($access,SIGA::param("id")));
    break;
  
  case "onCss":
  case "css":
    header('Content-Type: text/css; charset=utf-8');
    print unidad_coordinacion::onCss($access);
    break;
  case "onJavascript":
  case "js":
  case "javascript":  
    header('Content-Type: text/javascript; charset=utf-8');
    print unidad_coordinacion::onJavascript($access);
    break;
}

class unidad_coordinacion {
  public static function onCss($access){
    if(!$access) return;
    return SIGA::css("main.css");
  }
  
  public static function onJavascript($access){
    if(!$access) return;
    return SIGA::js("main.js");
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.unidad_coordinacion WHERE id='$id'";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onGetSelect($id){
    $db=SIGA::DBController();
    $sql="SELECT
            *
          FROM
            modulo_base.unidad_coordinacion
          WHERE
            id='$id'";
    $return=$db->Execute($sql);
    print json_encode($return);
  }

  public static function onListSelect($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();  
    $sql="SELECT * FROM modulo_base.unidad_coordinacion WHERE activo AND UPPER(coordinacion) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    print json_encode($return);
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController(); 
    $sql="SELECT * FROM modulo_base.unidad_coordinacion WHERE UPPER(coordinacion) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    print json_encode($return);
  }
  
  public static function onSave($access,$id,$coordinacion){
    $db=SIGA::DBController();
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_base.unidad_coordinacion",array("coordinacion"=>"'$coordinacion'"),"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_base.unidad_coordinacion",array("coordinacion"=>"'$coordinacion'"));
    }
    //Si hay error al modificar o insertar
    if(!$result)                  
      return array("success"=>false,"message"=>"Error al guardar en la tabla: modulo_base.unidad_coordinacion.");
    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos."); 
    $result=$db->Delete("modulo_base.unidad_coordinacion","id='$id'");
    if(!$result)                  
      return array("success"=>false,"message"=>"Error al guardar en la tabla: modulo_base.unidad_coordinacion.");      
    return array("success"=>true,"message"=>"Datos eliminado con exito.");
  }
  
}  
?>