<?php
class persona{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.persona WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }

  public static function onGet_PersonaNatural($id){
    $db=SIGA::DBController();
    $sql="SELECT
            P.identificacion_tipo,
            P.identificacion_numero,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
            split_part(P.denominacion,';',1) as primer_nombre,
            split_part(P.denominacion,';',2) as segundo_nombre,
            split_part(P.denominacion,';',3) as primer_apellido,
            split_part(P.denominacion,';',4) as segundo_apellido,
            replace(denominacion,';',' ') as denominacion,
            P.telefono,
            P.correo,
            P.direccion
          FROM modulo_base.persona as P WHERE P.id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }

  public static function onGet_PersonaCNE($identificacion_tipo,$identificacion_numero){
    if($identificacion_tipo=="") $identificacion_tipo=" ";

    $return=array();
    $return["id_persona"]="";
    $return["nacionalidad"]="$identificacion_tipo";
    $return["cedula"]="$identificacion_numero";
    $return["primer_nombre"]="";
    $return["segundo_nombre"]="";
    $return["primer_apellido"]="";
    $return["segundo_apellido"]="";
    $return["fecha_nacimiento"]="";
    $return["genero"]="";
    $return["correo"]="";
    $return["telefono"]="";

    $db=SIGA::DBController("base");
    $persona=$db->Execute("
      SELECT
        p.*
      FROM
        persona as p
      WHERE
        p.nacionalidad='$identificacion_tipo' and
        p.cedula='$identificacion_numero'
    ");

    if(isset($persona[0])){
      $return["nacionalidad"]=$persona[0]["nacionalidad"];
      $return["cedula"]=$persona[0]["cedula"];
      $return["primer_nombre"]=$persona[0]["primer_nombre"];
      $return["segundo_nombre"]=$persona[0]["segundo_nombre"];
      $return["primer_apellido"]=$persona[0]["primer_apellido"];
      $return["segundo_apellido"]=$persona[0]["segundo_apellido"];
      if(isset($persona[0]["fecha_nacimiento"]) && $persona[0]["fecha_nacimiento"])
        $return["fecha_nacimiento"]=$persona[0]["fecha_nacimiento"];
      if(isset($persona[0]["genero"]) && $persona[0]["genero"])
        $return["genero"]=$persona[0]["genero"];
    }

    return [$return];
  }

  public static function onGet_PersonaJuridica($id){
    $db=SIGA::DBController();
    $sql="SELECT
            P.identificacion_tipo,
            P.identificacion_numero,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
            P.denominacion,
            P.telefono,
            P.correo,
            P.direccion,
            PJ.cuenta_bancaria[1] cuenta_bancaria_principal,
            PJ.cuenta_bancaria[2] cuenta_bancaria_secundaria
          FROM modulo_base.persona as P
            LEFT JOIN modulo_base.persona_juridica PJ ON P.id=PJ.id_persona
          WHERE P.id='$id'
    ";
    $return=$db->Execute($sql);
    return $return;
  }

  public static function onSave_PersonaNatural($access,
                                               $id,
                                               $identificacion_tipo,
                                               $identificacion_numero,
                                               $primer_nombre,
                                               $segundo_nombre,
                                               $primer_apellido,
                                               $segundo_apellido,
                                               $telefono,
                                               $correo,
                                               $fecha_nacimiento=NULL,
                                               $genero=NULL){
    $db=SIGA::DBController();

    //buscar si existe el registro
    $existe=$db->Execute("select count(*)
                         from modulo_base.persona
                         where
                          tipo = 'N' and
                          identificacion_tipo='$identificacion_tipo' and
                          identificacion_numero='$identificacion_numero' and
                          text(id)<>'$id'");
    if($existe[0][0]>0)
      return array("success"=>false, "message"=>"Error. El número de cédula $identificacion_tipo-$identificacion_numero ya existe.");



    if($id!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $data=array("tipo"=>"'N'",
                  "identificacion_tipo"=>"'$identificacion_tipo'",
                  "identificacion_numero"=>"'$identificacion_numero'",
                  "denominacion"=>"'$primer_nombre;$segundo_nombre;$primer_apellido;$segundo_apellido'",
                  "telefono"=>"'$telefono'",
                  "correo"=>"'$correo'");
      $result=$db->Update("modulo_base.persona",$data,"id='$id'");

      if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.persona","messageDB"=>$db->GetMsgErrorClear());
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_base.persona(tipo,identificacion_tipo,identificacion_numero,denominacion,telefono,correo)
                            VALUES('N','$identificacion_tipo','$identificacion_numero','$primer_nombre;$segundo_nombre;$primer_apellido;$segundo_apellido','$telefono','$correo') RETURNING id");

      //Si hay error al modificar o insertar
      if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.persona","messageDB"=>$db->GetMsgErrorClear());

      if(!isset($result[0][0]))
        return array("success"=>false, "message"=>"Error al obtener el identificador de la persona.");
      $id=$result[0][0];
    }


    //actualizar en la tabla modulo_base.persona_natural
    if($fecha_nacimiento or $genero){
      $db->Delete("modulo_base.persona_natural","id_persona=$id");
      $db->Insert("modulo_base.persona_natural",array(
                                                      "id_persona"=>"$id",
                                                      "fecha_nacimiento"=>(!$fecha_nacimiento?"null":"'$fecha_nacimiento'"),
                                                      "genero"=>(!$genero?"null":"'$genero'")
                                                      ));
    }

    return array("success"=>true, "message"=>'Datos guardados con exito.', "id"=>"$id");
  }

  public static function onSave_PersonaJuridica($access,
                                                $id,
                                                $identificacion_tipo,
                                                $identificacion_numero,
                                                $denominacion,
                                                $telefono,
                                                $correo,
                                                $direccion,
                                                $cuenta_bancaria_principal = NULL,
                                                $cuenta_bancaria_secundaria = NULL){
    $db=SIGA::DBController();

    //buscar si existe el registro
    $existe=$db->Execute("select count(*)
                         from modulo_base.persona
                         where
                          tipo = 'J' and
                          identificacion_tipo='$identificacion_tipo' and
                          identificacion_numero='$identificacion_numero' and
                          text(id)<>'$id'");
    if($existe[0][0]>0)
      return array("success"=>false, "message"=>"Error. El número de rif $identificacion_tipo-$identificacion_numero ya existe.");

    if($id!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");

      $data=array("tipo"=>"'J'",
                  "identificacion_tipo"=>"'$identificacion_tipo'",
                  "identificacion_numero"=>"'$identificacion_numero'",
                  "denominacion"=>"'$denominacion'",
                  "telefono"=>"'$telefono'",
                  "correo"=>"'$correo'",
                  "direccion"=>"'$direccion'");

      //Modificar registro
      $result=$db->Update("modulo_base.persona",$data,"id='$id'");

      //Si hay error al modificar o insertar
      if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.persona","messageDB"=>$db->GetMsgErrorClear());
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      //$result=$db->Insert("modulo_base.persona",$data);
        //Insertar registro
      $result=$db->Execute("INSERT INTO modulo_base.persona(tipo,identificacion_tipo,identificacion_numero,denominacion,telefono,correo,direccion)
                            VALUES('J','$identificacion_tipo','$identificacion_numero','$denominacion','$telefono','$correo','$direccion') RETURNING id");

      //Si hay error al modificar o insertar
      if(!$result)
        return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.persona","messageDB"=>$db->GetMsgErrorClear());

      if(!isset($result[0][0]))
        return array("success"=>false, "message"=>"Error al obtener el identificador de la persona.");
      $id=$result[0][0];
    }
    

    $db->Delete("modulo_base.persona_juridica","id_persona=$id");
    if($cuenta_bancaria_principal || $cuenta_bancaria_secundaria){
      $db->Insert("modulo_base.persona_juridica",array(
                                                        "id_persona"=>"$id",
                                                        "cuenta_bancaria"=> "ARRAY['$cuenta_bancaria_principal','$cuenta_bancaria_secundaria']"
                                                        ));
    }



    return array("success"=>true, "message"=>'Datos guardados con exito.');
  }

  public static function onGet_Select($id){
    $db=SIGA::DBController();

    $sql="SELECT
            P.id,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero as identificacion,
            replace(P.denominacion,';',' ') as denominacion,
            (case when P.identificacion_tipo='' then 'S/N' else P.identificacion_tipo end) || '-' || P.identificacion_numero || ' ' || replace(P.denominacion,';',' ') as display,
            PT.id_cuenta_contable,
            PJ.cuenta_bancaria[1] cuenta_bancaria_principal,
            PJ.cuenta_bancaria[2] cuenta_bancaria_secundaria
          FROM
            modulo_base.persona as P
              LEFT JOIN modulo_base.persona_juridica PJ ON P.id=PJ.id_persona,
            modulo_base.persona_tipo as PT
          WHERE
            P.id='$id' AND
            P.tipo=PT.tipo";
    $return=$db->Execute($sql);
    return $return;
  }

  public static function onListSelect($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();

    $sql="SELECT
            id,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || lpad(text(identificacion_numero),9,'0') as identificacion,
            replace(denominacion,';',' ') as denominacion
          FROM
            modulo_base.persona
          WHERE
            (identificacion_tipo || '-' || identificacion_numero) ILIKE '%$text%' OR
            replace(denominacion,';',' ') ILIKE '%$text%'";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  /*public static function onList_Select($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();

    $sql="SELECT
            id,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) as identificacion_tipo,
            identificacion_numero,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || identificacion_numero as identificacion,
            replace(denominacion,';',' ') as denominacion
          FROM
            modulo_base.persona
          WHERE
            ((case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || identificacion_numero) ILIKE '%$text%' OR
            replace(denominacion,';',' ') ILIKE '%$text%'";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }*/

  public static function onList_Select($text,$start,$limit,$sort='',$tipo=NULL){
    $db=SIGA::DBController();

    $add="";
    if($tipo)
      $add="tipo ILIKE '$tipo' AND";

    $sql="SELECT
            id,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) as identificacion_tipo,
            identificacion_numero,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || lpad(text(identificacion_numero),9,'0') as identificacion,
            replace(denominacion,';',' ') as denominacion
          FROM
            modulo_base.persona
          WHERE
            $add
            (
              ((case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || identificacion_numero) ILIKE '%$text%' OR
              replace(denominacion,';',' ') ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onList_PersonaNatural($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();

    $sql="SELECT
            id,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) as identificacion_tipo,
            identificacion_numero,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || lpad(text(identificacion_numero),9,'0') as identificacion,
            replace(denominacion,';',' ') as denominacion
          FROM
            modulo_base.persona
          WHERE
            tipo ILIKE 'N' AND
            (
              ((case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || identificacion_numero) ILIKE '%$text%' OR
              replace(denominacion,';',' ') ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onList_PersonaJuridica($text,$start,$limit,$sort=''){
    $db=SIGA::DBController();

    $sql="SELECT
            id,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) as identificacion_tipo,
            identificacion_numero,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || identificacion_numero as identificacion,
            replace(denominacion,';',' ') as denominacion
          FROM
            modulo_base.persona
          WHERE
            tipo ILIKE 'J' AND
            (
              ((case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || identificacion_numero) ILIKE '%$text%' OR
              replace(denominacion,';',' ') ILIKE '%$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  //Lista a las personas pendientes con ordenes de pago
  public static function onList_OP_pendiente($text,$start,$limit,$sort,$tipo=NULL){
    $db=SIGA::DBController();

    $add="";
    if($tipo)
      $add="tipo ILIKE '$tipo' AND";

    $sql="SELECT
            id,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) as identificacion_tipo,
            identificacion_numero,
            (case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || lpad(text(identificacion_numero),9,'0') as identificacion,
            replace(denominacion,';',' ') as denominacion
          FROM
            modulo_base.persona
          WHERE
            $add
            (
              ((case when identificacion_tipo='' then 'S/N' else identificacion_tipo end) || '-' || identificacion_numero) ILIKE '%$text%' OR
              replace(denominacion,';',' ') ILIKE '%$text%'
            ) AND
            id IN
              (
                with consulta as (
                  SELECT
                    C.id_persona,
                    (select sum(DC.monto) from modulo_base.detalle_contable as DC where C.id=DC.id_comprobante and DC.operacion='H') as monto,
                    (
                      SELECT sum(CP2.monto_pagado)
                      FROM modulo_base.comprobante_previo_monto_pagado as CP2
                      WHERE C.id=CP2.id_comprobante_previo
                    ) as monto_pagado_acumulado
                  FROM
                    modulo_base.comprobante as C
                  WHERE
                    --EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
                    --EXTRACT(YEAR FROM C.fecha) in (".SIGA::data().",".(SIGA::data()-1).") AND
                    C.tipo='OP' AND
                    C.contabilizado AND
                    NOT (select count(*) from modulo_base.comprobante_previo as CP, modulo_base.comprobante as C2 where C.id=CP.id_comprobante_previo and CP.id_comprobante=C2.id and C2.tipo='CA')>0
                )
              select distinct
                id_persona
              from
                consulta
              WHERE
                monto<>monto_pagado_acumulado or
                monto_pagado_acumulado is null
              )";
    //SELECT id_comprobante as id_comprobante_posterior FROM modulo_base.comprobante_previo WHERE id_comprobante_previo='$id'
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onDelete($access,$id){
    $db=SIGA::DBController();

    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>true, "message"=>'Error. El usuario no tiene permiso para eliminar datos.');

    $result=$db->Delete("modulo_base.persona","id='$id'");
    if(!$result)
      return array("success"=>false, "message"=>"Error al eliminar en la tabla: modulo_base.persona","messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>'Registro eliminado con exito.');
  }
}

?>