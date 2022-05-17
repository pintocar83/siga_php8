<?php
include("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];
$params=$_POST;


$access=SIGA::access("academia_dependencia");//null,r,rw,a

switch($action){
  case "onGet":
  case "get":
    header('Content-Type: text/html; charset=utf-8');
    dependencia::onGet(SIGA::param("id"));
    break;  
  case "onList":
  case "list":
    header('Content-Type: text/html; charset=utf-8');
    dependencia::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));//dependencia::onList($params["text"],$params["start"],$params["limit"]);
    break;
  case "onSave":
  case "save":
    header('Content-Type: text/html; charset=utf-8');
    dependencia::onSave($access,SIGA::param("id"),SIGA::paramUpper("nombre"));
    break;
  case "onDelete":
  case "delete":
    header('Content-Type: text/html; charset=utf-8');
    dependencia::onDelete($access,SIGA::param("id"));
    break;  
  case "onCss":
  case "css":
    header('Content-Type: text/css; charset=utf-8');
    dependencia::onCss($access);
    break;
  case "onJavascript":
  case "js":
  case "javascript":  
    header('Content-Type: text/javascript; charset=utf-8');
    dependencia::onJavascript($access);
    break;
}



class dependencia{
  public static function onCss($access){
  }
    
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_asl.institucion WHERE id='$id'";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_asl.institucion WHERE UPPER(nombre) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    print json_encode($return);
  }
  
  public static function onSave($access,$id,$nombre){
    $db=SIGA::DBController();
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
        return;
      }
      //Modificar registro
      $result=$db->Update("modulo_asl.institucion",array("nombre"=>"'$nombre'"),"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      //Insertar registro
      $result=$db->Insert("modulo_asl.institucion",array("nombre"=>"'$nombre'"));
    }
    //Si hay error al modificar o insertar
    if(!$result){                    
      print "{success: false, message: 'Error al guardar en la tabla: modulo_asl.institucion.\nDetalle: ".$db->GetMsgError()."'}";
      return;
    }
    print "{success: true, message: 'Datos guardados con exito.'}";
  }
  
  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para eliminar datos.'}";
      return;
    }
    
    $result=$db->Delete("modulo_asl.institucion","id='$id'");
    if(!$result){                    
      print "{success: false, message: 'Error al guardar en la tabla: modulo_asl.institucion.\nDetalle: ".$db->GetMsgError()."'}";
      return;
    }
    print "{success: true, message: 'Registro eliminado con éxito.'}";
  }  
}
  
?>
