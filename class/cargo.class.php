<?php
class cargo{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_base.cargo WHERE id='$id'";
    return $db->Execute($sql);
  }

  public static function onList($text,$start,$limit,$sort){
    $db=SIGA::DBController();
    $sql="SELECT
            *,
            lpad(text(C.id),3,'0') as correlativo
          FROM modulo_base.cargo as C
          WHERE
            C.activo AND
            (
              C.denominacion ILIKE '%$text%' OR
              C.formula ILIKE '%$text%'
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
    $denominacion,
    $formula,
    $iva,
    $id_cuenta_presupuestaria
    ){
    $db=SIGA::DBController();

    $existe=$db->Execute("SELECT count(*) n FROM modulo_base.cargo WHERE activo AND text(id)<>'".$id."' AND denominacion ilike '".$denominacion."'");
    if(isset($existe[0]["n"]) and $existe[0]["n"]>0){
      return array("success"=>false, "message"=>"Ya existe un cargo / impuesto con la misma denominación.");
    }

    $data=array(
      "denominacion"             => "'$denominacion'",
      "formula"                  => "'$formula'",
      "iva"                      => "$iva",
      "id_cuenta_presupuestaria" => "'$id_cuenta_presupuestaria'"
    );

    if($id!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_base.cargo",
                          $data,
                          "id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_base.cargo",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.cargo", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Datos guardados con exito.");
  }

  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    $result=$db->Update("modulo_base.cargo",["activo"=>"false"],"id='$id'");
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.cargo", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Registro eliminado con éxito.");
  }
}
?>