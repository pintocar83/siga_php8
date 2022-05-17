<?php
include("../../../library/include.php");
if(!isset($_REQUEST["action"]))
  exit;


class MODULO{
  public static function onInit(){
    $access=SIGA::access("meta_fisica/unidad_medida");//null,r,rw,a    
    switch($_REQUEST["action"]){      
      case "onList":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false)));
        break;      
      
      case "onSave":
        header('Content-Type: text/plain; charset=utf-8');
        print json_encode(self::onSave($access,SIGA::param("id"),SIGA::paramUpper("unidad_medida")));
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
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController(); 
    $sql="SELECT * FROM modulo_planificacion.unidad_medida WHERE UPPER(unidad_medida) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,$id,$unidad_medida){
    $db=SIGA::DBController();
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_planificacion.unidad_medida",array("unidad_medida"=>"'$unidad_medida'"),"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_planificacion.unidad_medida",array("unidad_medida"=>"'$unidad_medida'"));
    }
    //Si hay error al modificar o insertar
    if(!$result)                  
      return array("success"=>false,"message"=>"Error al guardar en la tabla: modulo_planificacion.unidad_medida.");
    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos."); 
    $result=$db->Delete("modulo_planificacion.unidad_medida","id='$id'");
    if(!$result)                  
      return array("success"=>false,"message"=>"Error al guardar en la tabla: modulo_planificacion.unidad_medida.");      
    return array("success"=>true,"message"=>"Datos eliminado con exito.");
  } 
}

MODULO::onInit();
?>