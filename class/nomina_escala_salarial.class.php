<?php
class nomina_escala_salarial{
  public static function onGet($id){
    $db=SIGA::DBController();
    $sql="SELECT * FROM modulo_nomina.escala_salarial WHERE id='$id'";
    $return=$db->Execute($sql);
    return $return;
  }

  public static function onList($text,$start,$limit,$sort='',$group=''){
    $db=SIGA::DBController();

    $sql="SELECT * FROM modulo_nomina.escala_salarial WHERE text(escala) ILIKE '%$text%'";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");//$return["result"]=$db->Execute($sql." LIMIT $start,$limit");
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
                                $escala,
                                $sueldo_basico){
    $db=SIGA::DBController();

    //validación de la información
    if(!$escala)
      return array("success"=>false,"message"=>"Error. La definición de la escala del sueldo se encuentra vacio.");

    if(!is_numeric($sueldo_basico))
      return array("success"=>false,"message"=>"Error. Monto ingresado para la escaba de sueldo debe ser numérico.");

    //verificar si existe la escala
    $existe=$db->Execute("SELECT count(*) FROM modulo_nomina.escala_salarial WHERE escala='".$escala."' AND text(id)<>'$id'");
    if($existe[0][0]>0)
      return array("success"=>false,"message"=>"Error. La escala '$escala' ya existe.");

    $data=array(
                "escala"=>"'".$escala."'",
                "sueldo_basico"=>"'$sueldo_basico'"
                );

    if($id!=""){//si el modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_nomina.escala_salarial",$data,"id='$id'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_nomina.escala_salarial",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)
      return array("success"=>false,"message"=>"Error al guardar en la tabla: 'modulo_nomina.escala_salarial'");

    return array("success"=>true,"message"=>"Datos guardados con exito.");
  }

  public static function onDelete($access,$id){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false,"message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    $result=$db->Delete("modulo_nomina.escala_salarial","id='$id'");
    if(!$result)
      return array("success"=>false,"message"=>"Error al eliminar en la tabla: 'modulo_nomina.escala_salarial'");
    return array("success"=>true,"message"=>"Registro eliminado con éxito.");
  }
}
?>