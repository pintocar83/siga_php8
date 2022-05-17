<?php
include("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
  
$action=$_REQUEST["action"];
$params=$_POST;


$access=SIGA::access("academia_curso_aperturado");//null,r,rw,a

switch($action){
  case "onGet":
  case "get":
    header('Content-Type: text/plain; charset=utf-8');
    academia_curso_aperturado::onGet(SIGA::param("id"));
    break;  
  case "onGet_eSelect":
  case "get_eselect":
    header('Content-Type: text/plain; charset=utf-8');
    academia_curso_aperturado::onGet_eSelect(SIGA::param("id"));
    break;
  
  case "onList":
  case "list":
    header('Content-Type: text/plain; charset=utf-8');
    academia_curso_aperturado::onList(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));//onList($params["text"],$params["start"],$params["limit"],$params["sort"]);
    break;
  case "onList_eSelect":
  case "list_eselect":
    header('Content-Type: text/plain; charset=utf-8');
    academia_curso_aperturado::onList_eSelect(SIGA::paramUpper("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false));//onList($params["text"],$params["start"],$params["limit"],$params["sort"]);
    break;
  
  case "onSave":
  case "save":
    header('Content-Type: text/plain; charset=utf-8');
    academia_curso_aperturado::onSave($access,
                                    SIGA::param("id"),
                                    SIGA::paramUpper("codigo"),
                                    SIGA::param("id_curso"),
                                    SIGA::param("id_turno"),
                                    SIGA::param("fecha_inicio"),
                                    SIGA::param("fecha_culminacion"),
                                    SIGA::param("cupos"),
                                    SIGA::param("id_sala"),
                                    SIGA::param("id_instructor"),
                                    SIGA::param("id_instructor_secundario"),
                                    SIGA::param("id_estado"),
                                    SIGA::param("id_impreso"),
                                    SIGA::param("encuesta_clave"));
    break;
  case "onDelete":
  case "delete":
    header('Content-Type: text/plain; charset=utf-8');
    academia_curso_aperturado::onDelete($access,SIGA::param("id"));
    break;  
  case "onCss":
  case "css":
    header('Content-Type: text/css; charset=utf-8');
    academia_curso_aperturado::onCss($access);
    break;
  case "onJavascript":
  case "js":
  case "javascript":  
    header('Content-Type: text/javascript; charset=utf-8');
    academia_curso_aperturado::onJavascript($access);
    break;
}



class academia_curso_aperturado{
  public static function onCss($access){
  }
    
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();    
    $sql="SELECT * FROM modulo_asl.curso_aperturado WHERE id='$id'";
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
            CA.id,
            CA.codigo,
            C.denominacion as curso,
            TC.denominacion as turno,
            CA.fecha_inicio,
            concat(LPAD(text((select count(*) from modulo_asl.inscrito as I where I.id_curso_aperturado=CA.id)),2,'0'),'/',LPAD(text(CA.cupos),2,'0')) as cupos,
            case when CA.estado=1 then 'ABIERTO' else 'CERRADO' end as estado
          FROM
            modulo_asl.curso_aperturado as CA,
            modulo_asl.curso as C,
            modulo_asl.turno_curso as TC            
          WHERE
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
                                $codigo,
                                $id_curso,
                                $id_turno,
                                $fecha_inicio,
                                $fecha_culminacion,
                                $cupos,
                                $id_sala,
                                $id_instructor,
                                $id_instructor_secundario,
                                $id_estado,
                                $id_impreso,
                                $encuesta_clave){
    $db=SIGA::DBController();
    
    //validación de la información
    if(!$codigo){
      print "{success: false, message: 'Error. El campo código está vacio.'}";
      return;
    }
    $codigo_tmp=explode("-",$codigo);
    
    if(count($codigo_tmp)!=2){
      print "{success: false, message: 'Error. El campo código debe tener el formato ".SIGA::data()."-CURSO#.'}";
      return;
    }
    
    if($codigo_tmp[0]!=SIGA::data()){
      print "{success: false, message: 'Error. El campo código no coincide con el año de trabajo.'}";
      return;
    }
    
    if(!trim($codigo_tmp[1])){
      print "{success: false, message: 'Error. El campo código no coincide con el formato ".SIGA::data()."-CURSO#.'}";
      return;
    }
    
    if(!is_numeric($id_curso)){
      print "{success: false, message: 'Error. El campo curso se encuentra vacío.'}";
      return;
    }
    if(!is_numeric($id_turno)){
      print "{success: false, message: 'Error. El campo turno se encuentra vacío.'}";
      return;
    }
    
    if(!$fecha_inicio){
      print "{success: false, message: 'Error. El campo fecha de inicio está vacío.'}";
      return;
    }
    if(!$fecha_culminacion){
      print "{success: false, message: 'Error. El campo fecha de culminación está vacío.'}";
      return;
    }
    
    if(strtotime($fecha_inicio)>strtotime($fecha_culminacion)){
      print "{success: false, message: 'Error. La fecha de inicio es mayor a la fecha de culminación.'}";
      return;
    }
    
    if(!is_numeric($cupos)){
      print "{success: false, message: 'Error. El campo cupos debe ser numérico.'}";
      return;
    }
    if(!($cupos>0)){
      print "{success: false, message: 'Error. El campo cupos debe ser un número positivo.'}";
      return;
    }
    if(!is_numeric($id_sala)){
      print "{success: false, message: 'Error. El campo sala se encuentra vacío.'}";
      return;
    }
    if(!is_numeric($id_instructor)){
      print "{success: false, message: 'Error. El campo facilitador se encuentra vacío.'}";
      return;
    }
    
    //verificar si existe el codigo
    $existe=$db->Execute("SELECT count(*) FROM modulo_asl.curso_aperturado WHERE codigo='$codigo' AND id<>'$id'");
    if($existe[0][0]>0){
      print "{success: false, message: 'Error. El código $codigo ya existe.'}";
      return;
    }
    
    $data=array("codigo"=>"'$codigo'",
                "id_curso"=>"'$id_curso'",
                "id_turno"=>"'$id_turno'",
                "fecha_inicio"=>"'$fecha_inicio'",
                "fecha_culminacion"=>"'$fecha_culminacion'",
                "cupos"=>"'$cupos'",
                "id_sala"=>"'$id_sala'",
                "id_instructor"=>"'$id_instructor'",
                "id_instructor_secundario"=>($id_instructor_secundario>0?"$id_instructor_secundario":"null"),
                "estado"=>"'$id_estado'",
                "impreso"=>"'$id_impreso'",
                "encuesta_clave"=>"'$encuesta_clave'"
                );
    
    if($id!=""){//si el modificar un registro
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
        return;
      }
      //Modificar registro
      $result=$db->Update("modulo_asl.curso_aperturado",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      //Insertar registro
      $result=$db->Insert("modulo_asl.curso_aperturado",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result){      
      print "{success: false, message: \"Error al guardar en la tabla: modulo_asl.curso_aperturado. Detalle: ".$db->GetMsgError()."\"}";
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
    
    $result=$db->Delete("modulo_asl.curso_aperturado","id='$id'");
    if(!$result){                    
      print "{success: false, message: \"Error al guardar en la tabla: modulo_asl.curso_aperturado. Detalle: ".$db->GetMsgError()."\"}";
      return;
    }
    print "{success: true, message: 'Registro eliminado con éxito.'}";
  }  
}
  
?>
