<?php
include("../../library/include.php");

if(!isset($_REQUEST["action"]))
  exit;
$action=$_REQUEST["action"];

$access=SIGA::access("academia_inscribir");//null,r,rw,a

switch($action){
  case "onGet":
  case "get":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onGet(SIGA::param("id"));
    break;    
  case "onGetPersona":
  case "get_persona":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onGetPersona($access,SIGA::paramUpper("nacionalidad"),SIGA::param("cedula"));
    break;
  
  case "onAgregarPersona":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onAgregarPersona($access,
                                    SIGA::param("id_curso_aperturado"),
                                    SIGA::paramUpper("nacionalidad"),
                                    SIGA::param("cedula"),
                                    SIGA::paramUpper("primer_nombre"),
                                    SIGA::paramUpper("segundo_nombre"),
                                    SIGA::paramUpper("primer_apellido"),
                                    SIGA::paramUpper("segundo_apellido"),
                                    SIGA::paramUpper("telefono"),
                                    SIGA::param("correo"),
                                    SIGA::param("id_institucion"));
    
    break;
  case "onAprobar":
  case "aprobar":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onAprobar($access,SIGA::param("id_curso_aperturado"),SIGA::param("id_inscrito"),SIGA::param("calificacion"));
    break;
  case "onReprobar":
  case "reprobar":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onReprobar($access,SIGA::param("id_curso_aperturado"),SIGA::param("id_inscrito"),SIGA::param("calificacion"));
    break;
  case "onRestaurar":
  case "restaurar":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onRestaurar($access,SIGA::param("id_curso_aperturado"),SIGA::param("id_inscrito"));
    break; 
  
  case "onList":
  case "list":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onList(SIGA::param("text"),SIGA::param("start"),SIGA::param("limit"),SIGA::param("sort",false),SIGA::param("id"));//onList($params["text"],$params["start"],$params["limit"],$params["sort"]);
    break;
  case "onSave":
  case "save":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onSave($access,
                                    SIGA::param("id"),
                                    SIGA::paramUpper("codigo"),
                                    SIGA::param("id_curso"),
                                    SIGA::param("id_turno"),
                                    SIGA::param("fecha_inicio"),
                                    SIGA::param("fecha_culminacion"),
                                    SIGA::param("cupos"),
                                    SIGA::param("id_sala"),
                                    SIGA::param("id_instructor"),
                                    SIGA::param("id_estado"),
                                    SIGA::param("id_impreso"));
    break;
  case "onDelete":
  case "delete":
    header('Content-Type: text/plain; charset=utf-8');
    academia_inscribir::onDelete($access,SIGA::param("id_curso_aperturado"),SIGA::param("id_inscrito"));
    break;  
  case "onCss":
  case "css":
    header('Content-Type: text/css; charset=utf-8');
    academia_inscribir::onCss($access);
    break;
  case "onJavascript":
  case "js":
  case "javascript":  
    header('Content-Type: text/javascript; charset=utf-8');
    academia_inscribir::onJavascript($access);
    break;
}



class academia_inscribir{
  public static function onCss($access){
    print SIGA::css("main.css");
  }
    
  public static function onJavascript($access){
    if(!$access) return;
    print SIGA::js("main.js");
    print SIGA::js("agregar_persona.js");
  }
  
  public static function onGet($id){
    $db=SIGA::DBController();
    
    $sql="SELECT * FROM modulo_asl.curso_aperturado WHERE id='$id'";
    $return=$db->Execute($sql);
    print json_encode($return);
  }
  
  public static function onList($text,$start,$limit,$sort='',$id){
    $db=SIGA::DBController();
    
    $sort="";
    $sql="select 
            I.id,
            P.identificacion_tipo || '-' || lpad(text(P.identificacion_numero),9,'0')||' '||split_part(P.denominacion,';',1)||' '||split_part(P.denominacion,';',3) as persona,
            I2.nombre as institucion,
            E.denominacion as estado,
            I.calificacion_final as calificacion,
            P.identificacion_tipo as nacionalidad,
            P.identificacion_numero as cedula
          from 
            modulo_asl.inscrito as I,
            modulo_asl.institucion as I2,
            modulo_asl.estado as E,
            modulo_base.persona as P
          where 
            I.id_curso_aperturado='$id' and
            I.id_institucion=I2.id and
            I.id_estado=E.id and
            P.id=I.id_persona
          order by
            P.identificacion_tipo,
            P.identificacion_numero";

    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    print json_encode($return);
  }
  
  public static function onDelete($access,$id_curso_aperturado,$id_inscrito){
    $db=SIGA::DBController();
     
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para eliminar datos.'}";
      return;
    }
    
    $id_inscrito=json_decode($id_inscrito,true);
    for($i=0;$i<count($id_inscrito);$i++){
      $result=$db->Delete("modulo_asl.inscrito","id='".$id_inscrito[$i]."'");
      if(!$result){                    
        print "{success: false, message: \"Error al borrar datos en la tabla: inscrito. Detalle: ".$db->GetMsgError()."\"}";
        return;
      }
    }
    
    print "{success: true, message: 'Registro eliminado con éxito.'}";
  }
  
  public static function onGetPersona($access,$nacionalidad,$cedula){
    $db=SIGA::DBController();
     
    if(!($access=="r" || $access=="rw" || $access=="a")){
      print "{success: false, message: 'Error. El usuario no tiene permiso de lectura.'}";
      return;
    }
    if($nacionalidad=="") $nacionalidad=" ";
    
    $return["persona"]=$db->Execute("
      SELECT
        P.id as id_persona,
        split_part(P.denominacion,';',1) as primer_nombre,
        split_part(P.denominacion,';',2) as segundo_nombre,
        split_part(P.denominacion,';',3) as primer_apellido,
        split_part(P.denominacion,';',4) as segundo_apellido,
        P.correo,
        P.telefono
      FROM
        modulo_base.persona as P
      WHERE
        P.identificacion_tipo='$nacionalidad' and
        P.identificacion_numero='$cedula'
      ");
    
    if(!$return["persona"]){
      $xml=SIGA::xml("../persona/xml/?nacionalidad=$nacionalidad&cedula=$cedula");
      $personas = $xml->getElementsByTagName('persona');
      
      $return["persona"]["primer_nombre"]="";
      $return["persona"]["segundo_nombre"]="";
      $return["persona"]["primer_apellido"]="";
      $return["persona"]["segundo_apellido"]="";
      $return["persona"]["correo"]="";
      $return["persona"]["telefono"]="";
      
      foreach ($personas as $persona){
        $return["persona"]["primer_nombre"]=$persona->getAttribute('primer_nombre');
        $return["persona"]["segundo_nombre"]=$persona->getAttribute('segundo_nombre');
        $return["persona"]["primer_apellido"]=$persona->getAttribute('primer_apellido');
        $return["persona"]["segundo_apellido"]=$persona->getAttribute('segundo_apellido');
        $return["persona"]["correo"]=$persona->getAttribute('correo');
        $return["persona"]["telefono"]=$persona->getAttribute('telefono');
      }
    }
    else{
      $return["persona"]=$return["persona"][0];
    }
    
    $id_persona="";
    if(isset($return["persona"]["id_persona"]))
      $id_persona=$return["persona"]["id_persona"];

    
    
    $ultima_institucion_persona=$db->Execute("
        SELECT 
          i.id_institucion
        FROM
          modulo_asl.inscrito as i
        WHERE
          i.id_persona='$id_persona'
        ORDER BY
          i.fecha_inscripcion DESC
        LIMIT 1
        ");

    if(!$ultima_institucion_persona){
      $ultima_institucion_persona=$db->Execute("
        SELECT 
          p.id_institucion
        FROM
          modulo_asl.preinscrito as p
        WHERE
          p.nacionalidad='$nacionalidad' and
          p.cedula='$cedula'
        ORDER BY
          p.fecha DESC
        LIMIT 1
        ");
      }
    if($ultima_institucion_persona)    
      $return["id_institucion"]=$ultima_institucion_persona[0][0];
    else
      $return["id_institucion"]="1";//publico general
    
    $return["preinscrito"]=$db->Execute("
        SELECT 
          p.nombres_apellidos,
          p.telefono,
          p.correo,
          (select i.nombre from modulo_asl.institucion as i where i.id=p.id_institucion) as institucion
        FROM
          modulo_asl.preinscrito as p
        WHERE
          p.nacionalidad='$nacionalidad' and
          p.cedula='$cedula'
        ORDER BY
          p.fecha DESC
        LIMIT 1
        ");
    if($return["preinscrito"])
      $return["preinscrito"]=$return["preinscrito"][0];
    
    
    $return["cursos_realizados"]=$db->Execute("
      SELECT
        ca.id,
        c.denominacion as curso,
        c.duracion,
        ca.codigo,
        ca.fecha_inicio,
        ca.fecha_culminacion,
        concat(TO_CHAR(ca.fecha_inicio,'DD/MM/YYYY'),' - ',TO_CHAR(ca.fecha_culminacion,'DD/MM/YYYY')) as fecha,
        i.id_estado,
        e.denominacion as estado,
        i.id as id_inscrito,
        ins.nombre as institucion
      FROM
        modulo_asl.inscrito as i,
        modulo_asl.institucion as ins,
        modulo_asl.estado as e,
        modulo_asl.curso_aperturado as ca,
        modulo_asl.curso as c
      WHERE
        i.id_institucion=ins.id and
        i.id_estado=e.id and          
        i.id_curso_aperturado=ca.id and
        ca.id_curso=c.id and
        i.id_persona='$id_persona'
      ORDER BY
        ca.fecha_inicio, ca.fecha_culminacion, ca.codigo desc
        ");
    
    $return["cursos_preinscritos"]=$db->Execute("
      SELECT
        c.id,
        c.denominacion||' ('||c.duracion||' HORAS)' as curso,
        TO_CHAR(p.fecha,'DD/MM/YYYY HH24:MI:SS') as fecha
      FROM	  
        modulo_asl.curso as c,
        modulo_asl.preinscrito as p,
        modulo_asl.preinscrito_curso as pc
      WHERE
        pc.descartar=0 and
        p.nacionalidad='$nacionalidad' and
        p.cedula='$cedula' and
        p.id=pc.id_preinscrito and 
        c.id=pc.id_curso
      ORDER BY
        p.fecha");
    
    $return["llamadas"]=$db->Execute("
      SELECT
        TO_CHAR(lt.fecha,'DD/MM/YYYY HH24:MI:SS') as fecha,
        lt.telefono,
        lt.motivo,
        lt.resultado
      FROM	  
        modulo_base.llamada_telefonica as lt
      WHERE        
        lt.id_persona='$id_persona'
      ORDER BY
        lt.fecha");
    
    
    
    print json_encode($return);
    
  }

  
  public static function onAgregarPersona($access,
                                          $id_curso_aperturado,
                                          $nacionalidad,
                                          $cedula,
                                          $primer_nombre,
                                          $segundo_nombre,
                                          $primer_apellido,
                                          $segundo_apellido,
                                          $telefono,
                                          $correo,
                                          $id_institucion){
    $db=SIGA::DBController();
     
    
    //verificar si la persona existe
    $id_persona=$db->Execute("select id from modulo_base.persona where identificacion_tipo='$nacionalidad' and identificacion_numero='$cedula'");
    
    if(isset($id_persona[0][0])){//si existe, actualizar datos en modulo_base.persona
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
        return;
      }
      $id_persona=$id_persona[0][0];
      //Modificar registro
      $datos_persona=array("denominacion"=>"'$primer_nombre;$segundo_nombre;$primer_apellido;$segundo_apellido'",
                          "telefono"=>"'$telefono'",
                          "correo"=>"'$correo'");
      $result=$db->Update("modulo_base.persona",$datos_persona,"id='$id_persona'");
      if(!$result){      
        print "{success: false, message: \"Error al guardar en la tabla: modulo_base.persona. Detalle: ".$db->GetMsgError()."\"}";
        return;
      }
    }
    else{//si no existe, registrar a la persona
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_base.persona(tipo,identificacion_tipo,identificacion_numero,denominacion,telefono,correo)
                           VALUES('N','$nacionalidad','$cedula','$primer_nombre;$segundo_nombre;$primer_apellido;$segundo_apellido','$telefono','$correo') RETURNING id");
      
      //buscar el id de la movimiento_material recien ingresada
      if(!isset($result[0][0])){        
        print '{"success": false, "message": "Error al obtener el identificador de la persona ingresada."}';
        return;
      }
      $id_persona=$result[0][0];
    }
    
    //registrar persona en el curso
    //verificar si ya no existe en el curso
    $existe=$db->Execute("select count(*) from modulo_asl.inscrito where id_persona='$id_persona' and id_curso_aperturado='$id_curso_aperturado'");
    if($existe[0][0]>0){//actualizar datos
      if(!($access=="rw")){//solo el acceso 'rw' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
        return;
      }
      //Modificar registro
      $datos_inscrito=array("id_institucion"=>"'$id_institucion'");
      $result=$db->Update("modulo_asl.inscrito",$datos_inscrito,"id_curso_aperturado='$id_curso_aperturado' and id_persona='$id_persona'");
    }
    else{
      if(!($access=="rw" or $access=="a")){//solo el acceso 'rw' y 'a' es permitido
        print "{success: false, message: 'Error. El usuario no tiene permiso para guardar datos.'}";
        return;
      }
      //Insertar registro
      $datos_inscrito=array("id_curso_aperturado"=>"'$id_curso_aperturado'",
                            "id_persona"=>"'$id_persona'",
                            "fecha_inscripcion"=>"'".date("Y-m-d")."'",
                            "id_estado"=>"'2'",
                            "id_institucion"=>"'$id_institucion'");
      $result=$db->Insert("modulo_asl.inscrito",$datos_inscrito);
    }
    //Si hay error al modificar o insertar
    if(!$result){      
      print "{success: false, message: \"Error al guardar en la tabla: inscrito. Detalle: ".$db->GetMsgError()."\"}";
      return;
    }
    //eliminar preinscripcion para esta persona es este tipo de curso
    $result=$db->Update("modulo_asl.preinscrito_curso",
                        array("descartar"=>'1'),
                        "id_curso IN (select id_curso from modulo_asl.curso_aperturado where id='$id_curso_aperturado') and
                         id_preinscrito IN (select id from modulo_asl.preinscrito where nacionalidad='$nacionalidad' and cedula='$cedula')");

    print "{success: true, message: 'Datos guardados con exito.'}";
  }
  
  public static function onAprobar($access,$id_curso_aperturado,$id_inscrito,$calificacion){
    $db=SIGA::DBController();
    
    
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
      return;
    }
    
    $id_inscrito=json_decode($id_inscrito,true);
    for($i=0;$i<count($id_inscrito);$i++){
      $result=$db->Update("modulo_asl.inscrito",array("id_estado"=>"3", "calificacion_final"=>"'".$calificacion."'"),"id='".$id_inscrito[$i]."'");
      if(!$result){                    
        print "{success: false, message: \"Error al modificar datos en la tabla: modulo_asl.inscrito. Detalle: ".$db->GetMsgError()."\"}";
        return;
      }
    }
    print "{success: true, message: 'Registro guardado con éxito.'}";
  }
  
  public static function onReprobar($access,$id_curso_aperturado,$id_inscrito,$calificacion){
    $db=SIGA::DBController();
    
    
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
      return;
    }
    
    $id_inscrito=json_decode($id_inscrito,true);
    for($i=0;$i<count($id_inscrito);$i++){
      $result=$db->Update("modulo_asl.inscrito",array("id_estado"=>"4", "calificacion_final"=>"'".$calificacion."'"),"id='".$id_inscrito[$i]."'");
      if(!$result){                    
        print "{success: false, message: \"Error al modificar datos en la tabla: modulo_asl.inscrito. Detalle: ".$db->GetMsgError()."\"}";
        return;
      }
    }
    print "{success: true, message: 'Registro guardado con éxito.'}";
  }
  
  public static function onRestaurar($access,$id_curso_aperturado,$id_inscrito){
    $db=SIGA::DBController();
    
    
    if(!($access=="rw")){//solo el acceso 'rw' es permitido
      print "{success: false, message: 'Error. El usuario no tiene permiso para modificar datos.'}";
      return;
    }
    
    $id_inscrito=json_decode($id_inscrito,true);
    for($i=0;$i<count($id_inscrito);$i++){
      $result=$db->Update("modulo_asl.inscrito",array("id_estado"=>"2", "calificacion_final"=>"''"),"id='".$id_inscrito[$i]."'");
      if(!$result){                    
        print "{success: false, message: \"Error al modificar datos en la tabla: modulo_asl.inscrito. Detalle: ".$db->GetMsgError()."\"}";
        return;
      }
    }
    print "{success: true, message: 'Registro guardado con éxito.'}";
  }
  
}
  
?>
