<?php
class nomina_cargo{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_nomina.cargo WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }

  public static function onList($text,$start,$limit,$sort='',$group=''){
    $db=SIGA::DBController();

    $sql="SELECT * FROM modulo_nomina.cargo WHERE activo AND (cargo ILIKE '%$text%' or denominacion ILIKE '%$text%')";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    if(strtoupper($limit)=="ALL"){
      $return["total"]=count($return["result"]);
    }
    else{
      $return["total"]=$db->Execute(sql_query_total($sql));
      $return["total"]=$return["total"][0]["total"];
    }
    return $return;
  }

  public static function onSave($access,
                                $id,
                                $cargo,
                                $denominacion,
                                $orden){
    $db=SIGA::DBController();

    //validación de la información
    if(!$cargo)
      return ["success"=>false,"message"=>"Error. La definición del cargo se encuentra vacio."];
    

    //verificar si existe la cargo
    $existe=$db->Execute("SELECT count(*) FROM modulo_nomina.cargo WHERE cargo='".$cargo."' AND text(id)<>'$id'");
    if($existe[0][0]>0)
      return ["success"=>false,"message"=>"Error. El cargo '$cargo' ya existe."];

    $data=[
      "cargo"=>"'".$cargo."'",
      "denominacion"=>"'$denominacion'",
      "orden"=>"'$orden'"
    ];

    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return ["success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos."];
      //Modificar registro
      $result=$db->Update("modulo_nomina.cargo",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return ["success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos."];
      //Insertar registro
      $result=$db->Insert("modulo_nomina.cargo",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)
      return ["success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.cargo'"];

    return ["success"=>true,"message"=>"Datos guardados con exito."];
  }

  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return ["success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos."];
    $result=$db->Update("modulo_nomina.cargo",["activo"=>"'f'"],"id='$id'");
    if(!$result)
      return ["success"=>false,"message"=>"Error al eliminar en la tabla: 'modulo_nomina.cargo'"];
    return ["success"=>true,"message"=>"Registro eliminado con éxito."];
  }
}
?>