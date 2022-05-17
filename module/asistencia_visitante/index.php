<?php
include_once("../../library/include.php");
if(!isset($_REQUEST["action"]))
    exit;
$action=$_REQUEST["action"];

$access=SIGA::access("asistencia_visitante");

switch($action){
  case "css":
  case "onCss":  
    header('Content-Type: text/css; charset=utf-8');
    asistencia_visitante::css($access);
    break;
  case "js":
  case "javascript":
  case "onJavascript":
    header('Content-Type: text/javascript; charset=utf-8');
    asistencia_visitante::javascript($access);
    break;
  
  case "onList":
        header('Content-Type: text/plain; charset=utf-8');        
        print json_encode(asistencia_visitante::onList( SIGA::paramUpper("text"),
                                                        SIGA::param("start"),
                                                        SIGA::param("limit"),
                                                        SIGA::param("sort",false),
                                                        SIGA::param("fecha_inicio"),
                                                        SIGA::param("fecha_culminacion")));
        break;
      
  case "onGet":
    header('Content-Type: text/plain; charset=utf-8');        
    print json_encode(asistencia_visitante::onGet($access,SIGA::param("id")));
    break;
  
  case "onSalidaPersona":
    header('Content-Type: text/plain; charset=utf-8');        
    print json_encode(asistencia_visitante::onSalidaPersona($access,SIGA::param("id")));
    break;
  
  case "onSave":
    header('Content-Type: text/plain; charset=utf-8');        
    print json_encode(asistencia_visitante::onSave( $access,
                                              SIGA::param("id"),
                                              SIGA::paramUpper("nacionalidad"),
                                              SIGA::param("cedula"),
                                              SIGA::paramUpper("primer_nombre"),
                                              SIGA::paramUpper("segundo_nombre"),
                                              SIGA::paramUpper("primer_apellido"),
                                              SIGA::paramUpper("segundo_apellido"),
                                              SIGA::param("telefono"),
                                              SIGA::param("correo"),
                                              SIGA::param("fecha_nacimiento"),
                                              SIGA::paramUpper("genero"),
                                              SIGA::param("id_unidad_coordinacion"),
                                              SIGA::paramUpper("motivo"),
                                              SIGA::param("atendido"),
                                              SIGA::paramUpper("observacion"),
                                              SIGA::param("imagen",false)));
    break;
  
  case "onDelete":
    header('Content-Type: text/plain; charset=utf-8');        
    print json_encode(asistencia_visitante::onDelete($access,SIGA::param("id")));
    break;
  
  default:
    header('Content-Type: text/html; charset=utf-8');
    print "Acción no encontrada\n$action";
}




class asistencia_visitante{
  public static function css($access){
    if(!$access) return;
    print SIGA::css("main.css");
  }
    
  public static function javascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
  }
  
  public static function onGet($access,$id){
    if(!$access) return;
    
    $db=SIGA::DBController();
    
    $sql="SELECT
            AV.id,
            AV.fecha,
            AV.motivo,
            AV.hora_salida,
            AV.id_unidad_coordinacion,
            P.identificacion_tipo as nacionalidad,
            P.identificacion_numero as cedula,
            split_part(P.denominacion,';',1) as primer_nombre,
            split_part(P.denominacion,';',2) as segundo_nombre,
            split_part(P.denominacion,';',3) as primer_apellido,
            split_part(P.denominacion,';',4) as segundo_apellido,
            P.correo,
            P.telefono,
            P.direccion,
            PN.fecha_nacimiento,
            PN.genero
          FROM
            modulo_base.persona as P LEFT JOIN modulo_base.persona_natural as PN ON P.id=PN.id_persona,
            modulo_asistencia.asistencia_visitante as AV
          WHERE
            AV.id=$id AND
            AV.id_persona=P.id";
    
    $return=$db->Execute($sql);
    $return[0]["imagen"]="";
    $imagen=SIGA::databasePath()."/persona/".$return[0]["nacionalidad"].$return[0]["cedula"]."/registro_visitante/".$return[0]["fecha"].".jpg";
    if(file_exists($imagen)){
      $return[0]["imagen"]="data:image/jpg;base64,".base64_encode(file_get_contents($imagen));
    }
    
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort,$fecha_inicio,$fecha_culminacion){
    $db=SIGA::DBController();
    
    $sql="SELECT
            AV.id,
            AV.fecha as fecha_hora,
            to_char(AV.fecha,'DD/MM/YYYY') as fecha,
            to_char(AV.fecha,'HH12:MI:SS AM') as hora_entrada,
            to_char(AV.hora_salida,'HH12:MI:SS AM') as hora_salida,
            P.identificacion_tipo || P.identificacion_numero as nacionalidad_cedula,
            replace(P.denominacion,';',' ') as nombres_apellidos
          FROM
            modulo_base.persona as P,
            modulo_asistencia.asistencia_visitante as AV
          WHERE
            P.id=AV.id_persona AND
            AV.fecha::date BETWEEN '$fecha_inicio' AND '$fecha_culminacion' AND
            (
              P.identificacion_tipo || P.identificacion_numero ILIKE '%$text%' OR
              replace(P.denominacion,';',' ') ILIKE '%$text%'
            )
            ";
    
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,
                                $id,
                                $nacionalidad,
                                $cedula,
                                $primer_nombre,
                                $segundo_nombre,
                                $primer_apellido,
                                $segundo_apellido,
                                $telefono,
                                $correo,
                                $fecha_nacimiento,
                                $genero,
                                $id_unidad_coordinacion,
                                $motivo,
                                $atendido,
                                $observacion,
                                $imagen){
    
    if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos."); 
    
    $db=SIGA::DBController();    
    
    $id_persona=$db->Execute("SELECT
                                id
                              FROM
                                modulo_base.persona as P
                              WHERE
                                P.tipo='N' and
                                P.identificacion_tipo='$nacionalidad' and
                                P.identificacion_numero='$cedula'
                              ");

    $id_persona=isset($id_persona[0][0])?$id_persona[0][0]:"";
    
    include_once("../../class/persona.class.php");
    
    $result=persona::onSave_PersonaNatural( $access,
                                            $id_persona,
                                            $nacionalidad,
                                            $cedula,
                                            $primer_nombre,
                                            $segundo_nombre,
                                            $primer_apellido,
                                            $segundo_apellido,
                                            $telefono,
                                            $correo,
                                            $fecha_nacimiento,
                                            $genero);    
    
    if(!$result["success"]) return $result;    
    $id_persona=$result["id"];
    $fecha="";
    
    if($id!=""){//si es modificar un registro
      $data=array("id_persona"=>"$id_persona",
                  "id_unidad_coordinacion"=>"$id_unidad_coordinacion",
                  "motivo"=>"'$motivo'",
                  "atendido"=>"'$atendido'",
                  "observacion"=>"'$observacion'");
      //Modificar registro      
      $result=$db->Update("modulo_asistencia.asistencia_visitante",$data,"id=$id");      
      if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_asistencia.asistencia_visitante","messageDB"=>$db->GetMsgErrorClear());
    }
    else{//si es nuevo
      //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_asistencia.asistencia_visitante(id_persona,id_unidad_coordinacion,motivo,atendido,observacion)
                            VALUES($id_persona,$id_unidad_coordinacion,'$motivo','$atendido','$observacion') RETURNING id, fecha");
    
      //Si hay error al modificar o insertar
      if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_asistencia.asistencia_visitante","messageDB"=>$db->GetMsgErrorClear());
      
      if(!isset($result[0]["id"]))
        return array("success"=>false, "message"=>"Error al obtener el identificador del registro.");
      $id=$result[0]["id"];
      
      if(!isset($result[0]["fecha"]))
        return array("success"=>false, "message"=>"Error al obtener la fecha del registro.");
      $fecha=$result[0]["fecha"];
      
    }
    
    //guardar imagen
    list($type, $imagen_data) = explode(',', $imagen);
  
    if($type=="data:image/jpeg;base64" and $fecha){
      //$carpeta_base="../../data/persona/";
      $carpeta_base=SIGA::databasePath()."/persona/";
      $path=$nacionalidad.$cedula."/";
      
      if(!file_exists($carpeta_base.$path))
        mkdir($carpeta_base.$path,0755);
      if(!file_exists($carpeta_base.$path."registro_visitante/"))
        mkdir($carpeta_base.$path."registro_visitante/",0755);
      
      //crear imagen jpg en el servidor
      $imagen_data = str_replace(' ','+',$imagen_data);
      $imagen_data = base64_decode($imagen_data);
      
      file_put_contents($carpeta_base.$path."registro_visitante/$fecha.jpg",$imagen_data);
    }
    
    return array("success"=>true, "message"=>'Datos guardados con exito.');
  }
  
  public static function onDelete($access,
                                  $id){
    if(!($access=="rw"))//solo el acceso 'rw' y 'a' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    
    $db=SIGA::DBController();
    
    $return=$db->Execute("SELECT P.identificacion_tipo as nacionalidad, P.identificacion_numero as cedula, AV.fecha
                          FROM modulo_asistencia.asistencia_visitante as AV, modulo_base.persona as P
                          WHERE AV.id=$id AND AV.id_persona=P.id");
    
    
    $imagen=SIGA::databasePath()."/persona/".$return[0]["nacionalidad"].$return[0]["cedula"]."/registro_visitante/".$return[0]["fecha"].".jpg";
    
    if(file_exists($imagen))
      unlink($imagen);

    //Modificar registro      
    $result=$db->Delete("modulo_asistencia.asistencia_visitante","id=$id");      
    if(!$result)
      return array("success"=>false, "message"=>"Error al eliminar en la tabla: modulo_asistencia.asistencia_visitante","messageDB"=>$db->GetMsgErrorClear());
    
    return array("success"=>true, "message"=>'Datos guardados con exito.');
  }
  
  public static function onSalidaPersona( $access,
                                          $id){
    if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
    
    $db=SIGA::DBController();

    //Modificar registro      
    $result=$db->Update("modulo_asistencia.asistencia_visitante",array("hora_salida"=>"now()"),"id=$id");      
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_asistencia.asistencia_visitante","messageDB"=>$db->GetMsgErrorClear());
    
    return array("success"=>true, "message"=>'Datos guardados con exito.');
  }
  
}

?>
