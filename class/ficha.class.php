<?php
class ficha{
  public static function onList_Agregar($id_nomina,$id_periodo,$text,$start,$limit,$sort){
    $db=SIGA::DBController();

    $sql="SELECT
            F.id,
            F.id_persona,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
            replace(P.denominacion,';',' ') as denominacion
          FROM
            modulo_base.persona as P,
            modulo_nomina.ficha as F
          WHERE
            F.activo AND
            P.id=F.id_persona AND
            F.id NOT IN (select distinct id_ficha from modulo_nomina.ficha_concepto where id_nomina=$id_nomina and id_periodo=$id_periodo) AND
            (
              (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero ILIKE '%$text%' OR
              replace(P.denominacion,';',' ') ILIKE '%$text%'
            )
            ";

    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onList($text,$start,$limit,$sort){
    $db=SIGA::DBController();

    $sql="SELECT
            F.id,
            F.id_persona,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || lpad(text(identificacion_numero),8,'0') as identificacion,
            replace(P.denominacion,';',' ') as denominacion,
            case when activo then 'SI' else 'NO' end as activo,
            P.identificacion_tipo as nacionalidad,
            P.identificacion_numero as cedula
          FROM
            modulo_base.persona as P,
            modulo_nomina.ficha as F
          WHERE
            P.id=F.id_persona AND
            (
              (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || lpad(text(identificacion_numero),8,'0') ILIKE '%$text%' OR
              replace(P.denominacion,';',' ') ILIKE '%$text%'
            )
            ";

    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onGet($nacionalidad,$cedula,$id=NULL){
    $db=SIGA::DBController();

    $add="P.identificacion_tipo='$nacionalidad' AND P.identificacion_numero='$cedula'";
    if($id) $add="F.id=$id";

    $sql="SELECT
            F.id,
            F.fecha_ingreso,
            F.fecha_egreso,
            F.codigo,
            F.extension,
            F.activo,
            F.cuenta_nomina,
            F.id_escala_salarial,
            F.antiguedad_apn,
            F.profesionalizacion_porcentaje,
            P.identificacion_tipo as nacionalidad,
            P.identificacion_numero as cedula,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
            split_part(P.denominacion,';',1) as primer_nombre,
            split_part(P.denominacion,';',2) as segundo_nombre,
            split_part(P.denominacion,';',3) as primer_apellido,
            split_part(P.denominacion,';',4) as segundo_apellido,
            replace(P.denominacion,';',' ') as denominacion,
            split_part(P.denominacion,';',1) || ' ' || split_part(P.denominacion,';',3) as nombre_apellido,
            P.telefono,
            P.correo,
            P.direccion,
            PN.fecha_nacimiento,
            PN.genero
          FROM
            modulo_base.persona as P LEFT JOIN modulo_base.persona_natural as PN ON P.id=PN.id_persona,
            modulo_nomina.ficha as F
          WHERE
            P.id=F.id_persona AND
            $add";

    $return=$db->Execute($sql);
    if(!isset($return[0])){
      include_once("persona.class.php");
      $return=persona::onGet_PersonaCNE($nacionalidad,$cedula);
    }
    if(!isset($return[0])) return array();

    $return[0]["grupo_familiar"]=[];
    if(isset($return[0]["id"]) and $return[0]["id"]){
      //buscar grupo familiar
      $sql="SELECT * FROM modulo_nomina.grupo_familiar WHERE id_ficha='".$return[0]["id"]."' ORDER BY id";
      $return[0]["grupo_familiar"]=$db->Execute($sql);
    }

    $carpeta=$nacionalidad.$cedula;

    //$return[0]["archivos"]=self::onList_Archivo($carpeta);
    //$return[0]["foto"]=self::onGet_Foto($carpeta);
    $return[0]+=self::onList_Archivo($carpeta);

    if(isset($return[0]["fecha_ingreso"])){
      $fecha_ingreso=str_replace("}","",str_replace("{","",$return[0]["fecha_ingreso"]));
      $return[0]["fecha_ingreso"]=$fecha_ingreso=explode(",",$fecha_ingreso);
      $fecha_egreso="";
      if(isset($return[0]["fecha_egreso"]) and $return[0]["fecha_egreso"])
        $fecha_egreso=str_replace("}","",str_replace("{","",$return[0]["fecha_egreso"]));
      $return[0]["fecha_egreso"]=$fecha_egreso=explode(",",$fecha_egreso);

      $return[0]+=self::onGet_Antiguedad($return[0]["id"]);
    }
    else{
      $return[0]["fecha_ingreso"]="";
      $return[0]["fecha_egreso"]="";
    }

    if(!isset($return[0]["activo"])) $return[0]["activo"]="t";

    return $return;
  }

  public static function onGet_Antiguedad($id, $fecha_culminacion=NULL){
    $database_name=isset(SIGA::$database[SIGA::database()]["name"])?SIGA::$database[SIGA::database()]["name"]:"";
    //CASO ESPECIFICO PARA LA FUNDACITE SUCRE
    if($database_name && preg_grep("/siga_fundacite_sucre*/i",[$database_name])){
      //Calculo de antiguedad basado en dias. Este calculo es inexacto para los años de antiguedad.
      return self::onGet_AntiguedadLegacy($id, $fecha_culminacion);
    }

    $db=SIGA::DBController();
    $sql="SELECT fecha_ingreso, fecha_egreso FROM modulo_nomina.ficha WHERE id=$id";
    $result=$db->Execute($sql);
    
    if($fecha_culminacion===NULL) $fecha_culminacion=date("Y-m-d");      
    
    $return=array();
    $return["antiguedad_dia"]=0;
    $return["antiguedad_anio"]=0;
    $return["antiguedad_anio_dia"]=0;
    $return["antiguedad_simple"]="";
    //$return["antiguedad"]="";
    $return["antiguedad"]=["y"=>0,"m"=>0,"d"=>0, "days"=>0];

    if(isset($result[0]["fecha_ingreso"])){
      $fecha_ingreso=str_replace("}","",str_replace("{","","{$result[0]["fecha_ingreso"]}"));
      $fecha_ingreso=explode(",",$fecha_ingreso);
      $fecha_egreso="";
      if(isset($result[0]["fecha_egreso"]) and $result[0]["fecha_egreso"])
        $fecha_egreso=str_replace("}","",str_replace("{","","{$result[0]["fecha_egreso"]}"));
      $fecha_egreso=explode(",",$fecha_egreso);

      $dias=0;
      for($i=0;$i<count($fecha_ingreso);$i++){
        if(!isset($fecha_egreso[$i]) or $fecha_egreso[$i]=="" or $fecha_egreso[$i]>$fecha_culminacion) $fecha_egreso[$i]=$fecha_culminacion;

        $dt_inicio       = new DateTime($fecha_ingreso[$i]);
        $dt_culminacion  = new DateTime($fecha_egreso[$i]);
        $diff=$dt_inicio->diff($dt_culminacion,true);
        $return["antiguedad"]["y"]+=$diff->y;
        $return["antiguedad"]["m"]+=$diff->m;
        $return["antiguedad"]["d"]+=$diff->d+1;
        $return["antiguedad"]["days"]+=$diff->days+1;

        $dias+=floor((strtotime($fecha_egreso[$i])-strtotime($fecha_ingreso[$i]))/(60*60*24));
      }
      if(count($fecha_ingreso)>0){
        $return["fecha_ingreso"]=$fecha_ingreso[0];
        $return["fecha_ingreso_ultima"]=$fecha_ingreso[count($fecha_ingreso)-1];
      }

      $simple="";
      if($return["antiguedad"]["y"]>0){
        $simple=$return["antiguedad"]["y"]." año";
        if($return["antiguedad"]["y"]>1) $simple.="s";
      }
      if($return["antiguedad"]["m"]>0){
        if($simple) $simple.=", ";
        $simple.=$return["antiguedad"]["m"]." mes";
        if($return["antiguedad"]["m"]>1) $simple.="es";
      }
      if($simple) $simple.=" y ";
      $simple.=$return["antiguedad"]["d"]." día";
      if($return["antiguedad"]["d"]>1) $simple.="s";

      $simple=$return["antiguedad"]["days"]." días = ".$simple;

      $return["antiguedad_simple"]=$simple;
      $return["antiguedad_anio"]=$return["antiguedad"]["y"];


      $return["antiguedad_dia"]=$return["antiguedad"]["d"];
      $return["antiguedad_dias"]=$return["antiguedad"]["days"];
      $return["antiguedad_anio"]=$return["antiguedad"]["y"];
      $return["antiguedad_anio_dia"]=[$return["antiguedad_anio"],$return["antiguedad_dia"]];
      $return["antiguedad_simple"]=$simple;
      $return["antiguedad[]"]=$return["antiguedad"];
      $return["antiguedad"]=$simple;
    }


    return $return;
  }

  public static function onGet_AntiguedadLegacy($id, $fecha_culminacion=NULL){
    $db=SIGA::DBController();
    $sql="SELECT fecha_ingreso, fecha_egreso FROM modulo_nomina.ficha WHERE id=$id";
    $result=$db->Execute($sql);
    
    if($fecha_culminacion===NULL) $fecha_culminacion=date("Y-m-d");
    
    $return=array();
    $return["antiguedad_dia"]=0;
    $return["antiguedad_anio"]=0;
    $return["antiguedad_anio_dia"]=0;
    $return["antiguedad_simple"]="";
    $return["antiguedad"]="";
    
    if(isset($result[0]["fecha_ingreso"])){
      $fecha_ingreso=str_replace("}","",str_replace("{","","{$result[0]["fecha_ingreso"]}"));
      $fecha_ingreso=explode(",",$fecha_ingreso);
      $fecha_egreso=str_replace("}","",str_replace("{","","{$result[0]["fecha_egreso"]}"));
      $fecha_egreso=explode(",",$fecha_egreso);
      
      $dias=0;
      for($i=0;$i<count($fecha_ingreso);$i++){
        if(!isset($fecha_egreso[$i]) or $fecha_egreso[$i]=="" or $fecha_egreso[$i]>$fecha_culminacion) $fecha_egreso[$i]=$fecha_culminacion;
        $dias+=floor((strtotime($fecha_egreso[$i])-strtotime($fecha_ingreso[$i]))/(60*60*24));
      }
      $return["antiguedad_dia"]=$dias%365;
      $return["antiguedad_dias"]=$dias;
      $return["antiguedad_anio"]=floor($dias/365);
      $return["antiguedad_anio_dia"]=array(floor($dias/365),$dias%365);
      $return["antiguedad_simple"]=$dias>=365?(floor($dias/365)." año".(floor($dias/365)==1?"":"s")." y ".($dias%365)." día".($dias%365==1?"":"s")):"$dias Día".($dias==1?"":"s");
      $return["antiguedad[]"]=[
        "y"=>$return["antiguedad_anio"],
        "m"=>0,
        "d"=>$return["antiguedad_dia"], 
        "days"=>$dias
      ];
      $return["antiguedad"]=$dias>=365?("$dias Días = ".floor($dias/365)." años y ".($dias%365)." días."):"$dias Días";
      
    }
    return $return;
  }

  public static function onGet_Select($id){
    $db=SIGA::DBController();

    $sql="SELECT
            F.id,
            F.fecha_ingreso,
            F.fecha_egreso,
            F.codigo,
            F.extension,
            F.activo,
            P.identificacion_tipo as nacionalidad,
            P.identificacion_numero as cedula,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
            replace(P.denominacion,';',' ') as denominacion,
            replace(P.denominacion,';',' ') as nombres_apellidos,
            split_part(P.denominacion,';',1)||' '||split_part(P.denominacion,';',2) as nombre_apellido,
            split_part(P.denominacion,';',1) as primer_nombre,
            split_part(P.denominacion,';',2) as segundo_nombre,
            split_part(P.denominacion,';',3) as primer_apellido,
            split_part(P.denominacion,';',4) as segundo_apellido,

            P.telefono,
            P.correo,
            P.direccion,
            PN.fecha_nacimiento,
            PN.genero
          FROM
            modulo_base.persona as P LEFT JOIN modulo_base.persona_natural as PN ON P.id=PN.id_persona,
            modulo_nomina.ficha as F
          WHERE
            P.id=F.id_persona AND
            F.id=$id";

    $return=$db->Execute($sql);
    return $return;
  }

  public static function onAgregar($access,$id_nomina,$id_periodo,$id_ficha){
    $db=SIGA::DBController();

    //agregar el concepto SUELDO_BASICO en cero
    $id_sueldo_basico=$db->Execute("SELECT id FROM modulo_nomina.concepto WHERE identificador='SUELDO_BASICO' AND tipo='A' AND activo");
    $id_sueldo_basico=$id_sueldo_basico[0][0];

    $db->Insert("modulo_nomina.ficha_concepto", array("id_nomina"=>"$id_nomina","id_periodo"=>"$id_periodo","id_ficha"=>"$id_ficha","id_concepto"=>"$id_sueldo_basico","valor"=>"0"));
  }

  public static function onQuitar($access,$id_nomina,$id_periodo,$id_ficha){
    $db=SIGA::DBController();
    //$db->Delete("modulo_nomina.concepto_periodo", "id_periodo=$id_periodo AND id_concepto=$id_concepto");
  }

  public static function onSave($access,
                                $id,
                                $nacionalidad,
                                $cedula,
                                $primer_nombre,
                                $segundo_nombre,
                                $primer_apellido,
                                $segundo_apellido,
                                $fecha_nacimiento,
                                $genero,
                                $telefono,
                                $extension,
                                $correo,
                                $fecha_ingreso,
                                $fecha_egreso,
                                $cuenta_nomina,
                                $id_escala_salarial,
                                $antiguedad_apn,
                                $profesionalizacion_porcentaje,
                                $codigo,
                                $activo,
                                $grupo_familiar=NULL){

    if($id!="" and !($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
    else if($id and !($access=="rw" or $access=="a"))
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");

    if(!$nacionalidad)
      return array("success"=>false, "message"=>"Error. Seleccione la nacionalidad.");

    if(!$cedula)
      return array("success"=>false, "message"=>"Error. Ingrese el número de cédula.");




    $db=SIGA::DBController();

    $aux="";
    if($fecha_ingreso)
      for($i=0;$i<count($fecha_ingreso);$i++)
        if(str_clear($fecha_ingreso[$i]))
          $aux.="'".str_clear($fecha_ingreso[$i])."',";
    $fecha_ingreso=$aux?"ARRAY[".trim($aux,",")."]::date[]":"null";

    $aux="";
    if($fecha_egreso)
      for($i=0;$i<count($fecha_egreso);$i++)
        if(str_clear($fecha_egreso[$i]))
          $aux.="'".str_clear($fecha_egreso[$i])."',";
    $fecha_egreso=$aux?"ARRAY[".trim($aux,",")."]::date[]":"null";

    //$fecha_ingreso=json_encode($fecha_ingreso);
    //$fecha_egreso=json_encode($fecha_egreso);

    $id_persona=$db->Execute("
      SELECT
        id
      FROM
        modulo_base.persona as P
      WHERE
        P.tipo='N' and
        P.identificacion_tipo='$nacionalidad' and
        P.identificacion_numero='$cedula'
      ");

    $id_persona=isset($id_persona[0][0])?$id_persona[0][0]:"";

    include_once("persona.class.php");

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

    if($id!=""){//si es modificar un registro
      //Modificar registro
      $data=array("id_persona"=>"$id_persona",
                  "fecha_ingreso"=>"$fecha_ingreso",
                  "fecha_egreso"=>"$fecha_egreso",
                  "cuenta_nomina"=>"'$cuenta_nomina'",
                  "id_escala_salarial"=>"".(!$id_escala_salarial?"null":"'$id_escala_salarial'")."",
                  "antiguedad_apn"=>"".(!$antiguedad_apn?"0":"'$antiguedad_apn'")."",
                  "profesionalizacion_porcentaje"=>"".(!$profesionalizacion_porcentaje?"0":"'$profesionalizacion_porcentaje'")."",
                  "codigo"=>"".(!$codigo?"null":"'$codigo'")."",
                  "extension"=>"'$extension'",
                  "activo"=>"'$activo'");
      $result=$db->Update("modulo_nomina.ficha",$data,"id='$id'");

      if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_nomina.ficha","messageDB"=>$db->GetMsgErrorClear());
    }
    else{//si es nuevo
      //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_nomina.ficha(id_persona,fecha_ingreso,fecha_egreso,cuenta_nomina,id_escala_salarial,antiguedad_apn,profesionalizacion_porcentaje,codigo,activo)
                            VALUES('$id_persona',$fecha_ingreso,$fecha_egreso,'$cuenta_nomina',".(!$id_escala_salarial?"null":"'$id_escala_salarial'").",".(!$antiguedad_apn?"0":"$antiguedad_apn").",".(!$profesionalizacion_porcentaje?"0":"$profesionalizacion_porcentaje").",".(!$codigo?"null":"'$codigo'").",'$activo') RETURNING id");

      //Si hay error al modificar o insertar
      if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_nomina.ficha","messageDB"=>$db->GetMsgErrorClear());

      if(!isset($result[0][0]))
        return array("success"=>false, "message"=>"Error al obtener el identificador de la persona.");
      $id=$result[0][0];
    }

    //  print_r($grupo_familiar);
    if($grupo_familiar!==NULL){
      $tmp=[];
      //guardar los ids viejos para omitirlos en en borrado, se deben hacer update sobre estos
      for($i=0; $i<count($grupo_familiar); $i++){
        if($grupo_familiar[$i]["id"]){
          $tmp[]=$grupo_familiar[$i]["id"];
        }
      }
      //print_r($tmp);
      //borrado
      if(count($tmp)>0){
        $db->Delete("modulo_nomina.grupo_familiar","not id in ('".implode("','",$tmp)."') and id_ficha='$id'");
        //print "DELETE not id in ('".implode("','",$tmp)."') and id_ficha='$id'  ";
      }
      else{
        $db->Delete("modulo_nomina.grupo_familiar","id_ficha='$id'");
      }

      //hacer updates sobre id!='' e insert id=''
      for($i=0; $i<count($grupo_familiar); $i++){
        $data=[
          "id_ficha"           => "'$id'",
          "id_parentesco"      => "'".SIGA::clear($grupo_familiar[$i]["id_parentesco"])."'",
          "nacionalidad"       => "'".SIGA::clear($grupo_familiar[$i]["nacionalidad"])."'",
          "cedula"             => $grupo_familiar[$i]["cedula"]?"'".SIGA::clear($grupo_familiar[$i]["cedula"])."'":"NULL",
          "nombres_apellidos"  => "'".SIGA::clear($grupo_familiar[$i]["nombres_apellidos"])."'",
          "genero"             => "'".SIGA::clear($grupo_familiar[$i]["genero"])."'",
          "fecha_nacimiento"   => "'".SIGA::clear($grupo_familiar[$i]["fecha_nacimiento"])."'"
        ];
        if($grupo_familiar[$i]["id"]){//update
          $result=$db->Update("modulo_nomina.grupo_familiar",$data,"id='".SIGA::clear($grupo_familiar[$i]["id"])."'");
          if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_nomina.grupo_familiar","messageDB"=>$db->GetMsgErrorClear());
        }
        else{//insert
          $result=$db->Insert("modulo_nomina.grupo_familiar",$data);
          if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_nomina.grupo_familiar","messageDB"=>$db->GetMsgErrorClear());
        }
      }
    }


    return array("success"=>true, "message"=>'Datos guardados con exito.',"id"=>$id);
  }

  public static function onGet_Foto($carpeta){
    $carpeta_base=SIGA::databasePath()."/persona/";
    $archivo=$carpeta."/foto";
    $foto="";
    if(file_exists($carpeta_base.$archivo.".jpg")) $foto="$archivo.jpg";
    elseif(file_exists($carpeta_base.$archivo.".jpeg")) $foto="$archivo.jpeg";
    elseif(file_exists($carpeta_base.$archivo.".png")) $foto="$archivo.png";
    return $foto;
  }

  public static function onGet_ArchivoFoto($carpeta){
    $foto=self::onGet_Foto($carpeta);
    if(!$foto){
      $finfo = new finfo(FILEINFO_MIME);
      $type  = $finfo->file(SIGA::path()."/image/photo-default.png");
      header("Content-Type: $type");
      header("Content-Transfer-Encoding: Binary");
      header("Content-disposition: inline; filename='photo-default.png'");
      readfile(SIGA::path()."/image/photo-default.png");
      exit;
    }
    self::onGet_Archivo("r",$foto);
  }

  public static function onGet_Archivo($access, $archivo){
    $carpeta_base=SIGA::databasePath()."/persona/";
    if(!file_exists($carpeta_base.$archivo)) return;
    //verificar si la carpera base corresponde al usuario logueado, si es así, este puede consultar su carpeta
    if(!$access){
      $db=SIGA::DBController();
      $sql="select
              identificacion_tipo,
              identificacion_numero
            from
              modulo_base.usuario as u,
              modulo_base.persona as p
            where
              u.usuario like '".SIGA::user()."' and
              u.id_persona_responsable=p.id";
      $persona=$db->Execute($sql);
      $carpeta_usuario=$persona[0]["identificacion_tipo"].$persona[0]["identificacion_numero"]."/";

      //verificar que la carpeta base corresponda a la persona logeada
      if(substr_compare($archivo,$carpeta_usuario,0,strlen($carpeta_usuario))!=0)
        return;
    }

    $finfo = new finfo(FILEINFO_MIME);
    $type  = $finfo->file($carpeta_base.$archivo);
    header("Content-Type: $type");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: inline; filename=\"".basename($archivo)."\"");
    readfile($carpeta_base.$archivo);
  }

  private static function sortArchivo($a,$b){
    if($a["leaf"]==$b["leaf"])
      return strcmp($a["text"],$b["text"]);
    if($a["leaf"]==true) return 1;
    return -1;
  }

  public static function onList_Archivo($carpeta){
    $carpeta_base=SIGA::databasePath()."/persona/";
    $result=array();
    if(!file_exists($carpeta_base.$carpeta)) return $result;
    $cdir=scandir($carpeta_base.$carpeta);
    foreach($cdir as $key => $value){
      if(!in_array($value,array(".",".."))){
        if(is_dir($carpeta_base.$carpeta."/".$value))
          $result[]=array(
            //"iconCls"=>"perfil-data-nodo",
            "text"=>"$value",
            "leaf"=>false,
            "path"=>$carpeta."/".$value."/",
            "parent"=>$carpeta."/",
            "children"=> self::onList_Archivo($carpeta."/".$value."")["archivos"]
          );
        else
          $result[]=array(
            //"iconCls"=>"perfil-tipo-nodo",
            "text"=>"$value",
            "path"=>"$carpeta/",
            "link"=>"module/ficha/?action=onGet_Archivo&archivo=".$carpeta."/".$value,
            "leaf"=>true,
          );
      }
    }

    usort($result, array(__CLASS__,'sortArchivo'));

    return array("archivos"=>$result, "foto"=>self::onGet_Foto($carpeta));
  }

  public static function onUpload_Archivo($access, $path, $FILES){
    if($access!="rw")
      return array("success"=>false, "message"=>"El usuario no tiene acceso al módulo.");
    $carpeta_base=SIGA::databasePath()."/persona/";
    if(!file_exists($carpeta_base.$path))
    	mkdir($carpeta_base.$path,0755);

    for($i=0;$i<count($FILES['tmp_name']);$i++){
      move_uploaded_file($FILES['tmp_name'][$i], $carpeta_base.$path.$FILES['name'][$i]);
    }
    return array("success"=>true, "message"=>"");
  }

  public static function onDelete_Archivo($access, $archivo){
    if($access!="rw")
      return array("success"=>false, "message"=>"El usuario no tiene acceso al módulo.");
    $carpeta_base=SIGA::databasePath()."/persona/";

    if(is_dir($carpeta_base.$archivo))
      rmdir($carpeta_base.$archivo);
    else
      unlink($carpeta_base.$archivo);

    return array("success"=>true, "message"=>"");
  }

  public static function onRename_Archivo($access, $archivo, $archivo_nuevo){
    if($access!="rw")
      return array("success"=>false, "message"=>"El usuario no tiene acceso al módulo.");
    $carpeta_base=SIGA::databasePath()."/persona/";
    rename($carpeta_base.$archivo,$carpeta_base.$archivo_nuevo);
    return array("success"=>true, "message"=>"");
  }

  public static function onAdd_Carpeta($access, $carpeta){
    if($access!="rw")
      return array("success"=>false, "message"=>"El usuario no tiene acceso al módulo.");
    $carpeta_base=SIGA::databasePath()."/persona/";
    if(file_exists($carpeta_base.$carpeta))
      return array("success"=>false, "message"=>"Ya existe un archivo o carpeta con el mismo nombre.");
    mkdir($carpeta_base.$carpeta);
    return array("success"=>true, "message"=>"");
  }

  public static function onNominaPeriodo($id_ficha, $id_periodo){
    $db=SIGA::DBController();

    $sql="
      select distinct
        FC.id_nomina,
        N.codigo,
        N.nomina
      from
        modulo_nomina.ficha_concepto FC,
        modulo_nomina.nomina N
      where
        FC.id_periodo=$id_periodo and
        FC.id_ficha='$id_ficha' and
        N.id=FC.id_nomina
      ";

    $return=$db->Execute($sql);
    return $return;
  }

}
?>
