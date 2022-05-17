<?php
include("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];

$access=SIGA::access("academia_cursos");//null,r,rw,a

switch($action){
  case "onGet":
    header('Content-Type: text/plain; charset=utf-8');
    academia_cursos::onGet(trim(SIGA::param("id")));
    break;
  case "onList":
  case "list":
    header('Content-Type: text/plain; charset=utf-8');
    academia_cursos::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));
    break;
  
  case "onSave":
  case "save":
    header('Content-Type: text/plain; charset=utf-8');
    //academia_cursos::onSave($access,);
    break;
  case "onDelete":
  case "delete":
    header('Content-Type: text/plain; charset=utf-8');
    //academia_cursos::onDelete($access,SIGA::param("id"));
    break;  
  case "onCss":
  case "css":
    header('Content-Type: text/css; charset=utf-8');
    academia_cursos::onCss($access);
    break;
  case "onJavascript":
  case "js":
  case "javascript":  
    header('Content-Type: text/javascript; charset=utf-8');
    academia_cursos::onJavascript($access);
    break;
}



class academia_cursos{
  public static function onCss($access){   
  }
    
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
       
    $sql="SELECT *, concat(duracion,' Horas') as duracion_h FROM modulo_asl.curso WHERE id='$id'";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
       
    $sql="SELECT id, denominacion, concat(duracion,' Horas') as duracion_h  FROM modulo_asl.curso WHERE UPPER(denominacion) LIKE UPPER('%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
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
      $result=$db->Update("",array("nombre"=>"'$nombre'"),"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      //Insertar registro
      $result=$db->Insert("",array("nombre"=>"'$nombre'"));
    }
    //Si hay error al modificar o insertar
    if(!$result){                    
      print "{success: false, message: 'Error al guardar en la tabla: institucion.\nDetalle: ".$db->GetMsgError()."'}";
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
    
    $result=$db->Delete("","id='$id'");
    if(!$result){                    
      print "{success: false, message: 'Error al guardar en la tabla: institucion.\nDetalle: ".$db->GetMsgError()."'}";
      return;
    }
    print "{success: true, message: 'Registro eliminado con éxito.'}";
  }  
}
  
?>
