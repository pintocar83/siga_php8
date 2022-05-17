<?php
include("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];

$access=SIGA::access("academia_facilitador");//null,r,rw,a

switch($action){
  case "onGet":
    header('Content-Type: text/plain; charset=utf-8');
    academia_facilitador::onGet(SIGA::param("id"));
    break;  
  case "onList":
  case "list":
    header('Content-Type: text/plain; charset=utf-8');
    academia_facilitador::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));
    break;
  
  case "onSave":
  case "save":
    header('Content-Type: text/plain; charset=utf-8');
    academia_facilitador::onSave($access,SIGA::param("id"),SIGA::paramUpper("id_persona"),SIGA::paramUpper("notas"));
    break;
  case "onDelete":
  case "delete":
    header('Content-Type: text/plain; charset=utf-8');
    academia_facilitador::onDelete($access,SIGA::param("id"));
    break;  
  case "onCss":
  case "css":
    header('Content-Type: text/css; charset=utf-8');
    academia_facilitador::onCss($access);
    break;
  case "onJavascript":
  case "js":
  case "javascript":  
    header('Content-Type: text/javascript; charset=utf-8');
    academia_facilitador::onJavascript($access);
    break;
}



class academia_facilitador{
  public static function onCss($access){
  }
    

  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
       
    $sql="SELECT
            I.id,
            P.identificacion_tipo || '-' || P.identificacion_numero as nacionalidad_cedula,
            replace(P.denominacion,';',' ') as nombres_apellidos
          FROM
            modulo_asl.instructor as I,
            modulo_base.persona as P
          WHERE
            I.id_persona=P.id AND
            I.id='$id'";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
       
    $sql="SELECT
            I.id,
            I.id_persona,
            I.notas,
            P.identificacion_tipo || '-' || P.identificacion_numero as nacionalidad_cedula,
            replace(P.denominacion,';',' ') as nombres_apellidos
          FROM
            modulo_asl.instructor as I,
            modulo_base.persona as P
          WHERE
            I.id_persona=P.id AND
            (
              P.identificacion_tipo || '-' || P.identificacion_numero ILIKE '%$text%' OR
              replace(P.denominacion,';',' ') ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    print json_encode($return);
  }
  
  public static function onSave($access,$id,$id_persona,$notas){
    $db=SIGA::DBController();
    
    if(!$id_persona){
      print "{success: false, message: 'Error. Debe seleccionar la persona.'}";
      return;
    }
    
    
    
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
        return;
      }
      //Modificar registro
      $result=$db->Update("modulo_asl.instructor",array("id_persona"=>"'$id_persona'","notas"=>"'$notas'"),"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      //Insertar registro
      $result=$db->Insert("modulo_asl.instructor",array("id_persona"=>"'$id_persona'","notas"=>"'$notas'"));
    }
    //Si hay error al modificar o insertar
    if(!$result){                    
      print "{success: false, message: 'Error al guardar en la tabla: instructor.\nDetalle: ".$db->GetMsgError()."'}";
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
    
    $result=$db->Delete("modulo_asl.instructor","id='$id'");
    if(!$result){                    
      print "{success: false, message: 'Error al guardar en la tabla: instructor.\nDetalle: ".$db->GetMsgError()."'}";
      return;
    }
    print "{success: true, message: 'Registro eliminado con éxito.'}";
  }  
}
  
?>
