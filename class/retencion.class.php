<?php
class retencion{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.retencion WHERE id='$id'";
    return $db->Execute($sql);
  }
  
  public static function onList($text,$start,$limit,$sort){
    $db=SIGA::DBController();    
    $sql="SELECT
            R.*,
            lpad(text(R.id),3,'0') as correlativo,
            RT.denominacion retencion_tipo
          FROM modulo_base.retencion as R
            LEFT JOIN modulo_base.retencion_tipo RT on RT.id=R.id_retencion_tipo
          WHERE
            R.activo AND
            (
              R.denominacion ILIKE '%$text%' OR
              R.formula ILIKE '%$text%'
            )
            ";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }

  public static function onSave(
    $access,
    $id,
    $id_retencion_tipo,
    $denominacion,
    $formula,
    $id_cuenta_contable
    ){
    $db=SIGA::DBController();

    $existe=$db->Execute("SELECT count(*) n FROM modulo_base.retencion WHERE activo AND text(id)<>'".$id."' AND denominacion ilike '".$denominacion."'");
    if(isset($existe[0]["n"]) and $existe[0]["n"]>0){
      return array("success"=>false, "message"=>"Ya existe una retención con la misma denominación.");
    }

    $data=array(
      "denominacion"             => "'$denominacion'",
      "formula"                  => "'$formula'",
      "id_retencion_tipo"        => "$id_retencion_tipo",
      "id_cuenta_contable"       => "'$id_cuenta_contable'"
    );

    if($id!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_base.retencion",
                          $data,
                          "id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_base.retencion",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.retencion", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Datos guardados con exito.");
  }

  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    $result=$db->Update("modulo_base.retencion",["activo"=>"false"],"id='$id'");
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.retencion", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Registro eliminado con éxito.");
  }
}  
?>