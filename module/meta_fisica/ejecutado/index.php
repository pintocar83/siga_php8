<?php
include("../../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
  
$action=$_REQUEST["action"];
$params=$_POST;


$access=SIGA::access("meta_fisica");//null,r,rw,a

switch($action){
  case "onGet":
  case "get":
    header('Content-Type: text/plain; charset=utf-8');
    meta_fisica::onGet(SIGA::param("id"));
    break;  
  case "onGet_eSelect":
  case "get_eselect":
    header('Content-Type: text/plain; charset=utf-8');
    meta_fisica::onGet_eSelect(SIGA::param("id"));
    break;
  
  case "onList":
  case "list":
    header('Content-Type: text/plain; charset=utf-8');
    meta_fisica::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));//onList($params["text"],$params["start"],$params["limit"],$params["sort"]);
    break;
  case "onList_eSelect":
  case "list_eselect":
    header('Content-Type: text/plain; charset=utf-8');
    meta_fisica::onList_eSelect(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));//onList($params["text"],$params["start"],$params["limit"],$params["sort"]);
    break;
  
  case "onSave":
  case "save":
    header('Content-Type: text/plain; charset=utf-8');
    meta_fisica::onSave($access,
                        SIGA::param("id"),
                        SIGA::param("tipo"),
                        SIGA::param("id_accion_especifica"),
                        SIGA::paramUpper("actividad"),
                        SIGA::param("id_unidad_coordinacion"),
                        SIGA::param("id_responsable"),
                        SIGA::param("id_unidad_medida"),
                        SIGA::param("meta",false));
    break;
  case "onDelete":
  case "delete":
    header('Content-Type: text/plain; charset=utf-8');
    meta_fisica::onDelete($access,SIGA::param("id"));
    break;  
  case "onCss":
  case "css":
    header('Content-Type: text/css; charset=utf-8');
    meta_fisica::onCss($access);
    break;
  case "onJavascript":
  case "js":
  case "javascript":  
    header('Content-Type: text/javascript; charset=utf-8');
    meta_fisica::onJavascript($access);
    break;
}



class meta_fisica{
  public static function onCss($access){
  }
    
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();    
    $sql="SELECT 
            MF.*, 
            (select AE.id_accion_centralizada from modulo_base.accion_especifica as AE where AE.id=MF.id_accion_especifica) as id_accion_centralizada
          FROM
            modulo_planificacion.meta_fisica AS MF
          WHERE
            id='$id'";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onGet_eSelect($id){
    $db=SIGA::DBController();    
    
    $sql="SELECT
            CA.id,
            CA.codigo,
            C.denominacion as curso,
            TC.denominacion as turno,
            CONCAT('DEL ',TO_CHAR(CA.fecha_inicio,'DD/MM/YYYY'),' AL ',TO_CHAR(CA.fecha_culminacion,'DD/MM/YYYY')) AS fecha_inicio,
            P.identificacion_tipo ||'-'||P.identificacion_numero||' '||split_part(P.denominacion,';',1)||' '||split_part(P.denominacion,';',3) as nacionalidad_cedula_nombres_apellidos
          FROM
            modulo_asl.curso_aperturado as CA,
            modulo_asl.curso as C,
            modulo_asl.turno_curso as TC,
            modulo_asl.instructor as I,
            modulo_base.persona as P
          WHERE
            CA.id='$id' AND CA.id_curso=C.id AND CA.id_turno=TC.id AND CA.id_instructor=I.id AND I.id_persona=P.id
            ";
    
    $return=$db->Execute($sql);
    
    $return[0]["title"]="<table cellspacing='0' cellpadding='0'>";
    $return[0]["title"].="<tr><td><b>Código: </b></td><td style='padding-left:10px;'>".$return[0]["codigo"]."</td>";
    $return[0]["title"].="<tr><td><b>Curso: </b></td><td style='padding-left:10px;'>".$return[0]["curso"]."</td>";
    $return[0]["title"].="<tr><td><b>Fecha: </b></td><td style='padding-left:10px;'>".$return[0]["fecha_inicio"]."</td>";
    $return[0]["title"].="<tr><td><b>Turno: </b></td><td style='padding-left:10px;'>".$return[0]["turno"]."</td>";
    $return[0]["title"].="<tr><td><b>Facilitador: </b></td><td style='padding-left:10px;'>".$return[0]["nacionalidad_cedula_nombres_apellidos"]."</td>";
    $return[0]["title"].="</table>";
    
    $return[0]["display"]="".$return[0]["codigo"]." | ".$return[0]["curso"]." | ".$return[0]["fecha_inicio"]." ".$return[0]["turno"]." | ".$return[0]["nacionalidad_cedula_nombres_apellidos"];
    
    print json_encode($return);
  }
  
  public static function onList($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
        
    $sql="SELECT
            MF.id,
            MF.tipo,
            _formatear_estructura_presupuestaria_nivel_especifica(MF.id_accion_especifica) as accion_codigo,
            MF.actividad,
            UC.coordinacion
          FROM
            modulo_planificacion.meta_fisica as MF,
            modulo_base.unidad_coordinacion as UC
          WHERE
            MF.id_unidad_coordinacion=UC.id        
            ";/*
               *
               CA.codigo LIKE '".SIGA::data()."-%' AND
            CA.id_curso=C.id AND
            CA.id_turno=TC.id AND
            (
              UPPER(CA.codigo) LIKE UPPER('%$text%') OR
              UPPER(C.denominacion) LIKE UPPER('%$text%') OR
              UPPER(TO_CHAR(CA.fecha_inicio,'DD/MM/YYYY')) LIKE UPPER('%$text%')
            )
               *
               **/
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    print json_encode($return);
  }
  
  public static function onList_eSelect($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();
        
    $sql="SELECT
            CA.id,
            CA.codigo,
            C.denominacion as curso,
            TC.denominacion as turno,
            CA.fecha_inicio
          FROM
            modulo_asl.curso_aperturado as CA,
            modulo_asl.curso as C,
            modulo_asl.turno_curso as TC            
          WHERE
            CA.estado=1 AND
            CA.codigo LIKE '".SIGA::data()."-%' AND
            CA.id_curso=C.id AND
            CA.id_turno=TC.id AND
            (
              UPPER(CA.codigo) LIKE UPPER('%$text%') OR
              UPPER(C.denominacion) LIKE UPPER('%$text%') OR
              UPPER(TO_CHAR(CA.fecha_inicio,'DD/MM/YYYY')) LIKE UPPER('%$text%')
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    print json_encode($return);
  }
  
  public static function onSave($access,
                                $id,
                                $tipo,
                                $id_accion_especifica,
                                $actividad,
                                $id_unidad_coordinacion,
                                $id_responsable,
                                $id_unidad_medida,
                                $meta){
    
    
    $db=SIGA::DBController();
    
    //validación de la información
    if(!$actividad){
      print "{success: false, message: 'Error. El campo actividad está vacio.'}";
      return;
    }    

    if(!is_numeric($id_responsable)){
      print "{success: false, message: 'Error. El campo responsable se encuentra vacío.'}";
      return;
    }
    

    $data=array("tipo"=>"'$tipo'",
                "id_accion_especifica"=>(($tipo==1 or $tipo=="1")?"'$id_accion_especifica'":"null"),
                "actividad"=>"'$actividad'",
                "id_unidad_coordinacion"=>"'$id_unidad_coordinacion'",
                "id_responsable"=>"'$id_responsable'",
                "id_unidad_medida"=>"'$id_unidad_medida'",
                "meta"=>"$meta"
                );
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
        return;
      }
      //Modificar registro
      $result=$db->Update("modulo_planificacion.meta_fisica",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      //Insertar registro
      $result=$db->Insert("modulo_planificacion.meta_fisica",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result){      
      print "{success: false, message: \"Error al guardar en la tabla: modulo_planificacion.meta_fisica. Detalle: ".$db->GetMsgError()."\"}";
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
    
    $result=$db->Delete("modulo_planificacion.meta_fisica","id='$id'");
    if(!$result){                    
      print "{success: false, message: \"Error al guardar en la tabla: modulo_planificacion.meta_fisica. Detalle: ".$db->GetMsgError()."\"}";
      return;
    }
    print "{success: true, message: 'Registro eliminado con éxito.'}";
  }  
}
  
?>
