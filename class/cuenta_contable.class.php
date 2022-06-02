<?php
class cuenta_contable{
  public static function onGet($id_cuenta_contable){
    $db=SIGA::DBController();  
    $sql="SELECT id_cuenta_contable, denominacion,  _formatear_cuenta_contable(id_cuenta_contable) as cuenta_contable FROM modulo_base.cuenta_contable WHERE id_cuenta_contable='$id_cuenta_contable'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort='',$filtro=''){
    $db=SIGA::DBController();     
    $sql="SELECT
            id_cuenta_contable,
            denominacion,
            _formatear_cuenta_contable(id_cuenta_contable) as cuenta_contable
          FROM
            modulo_base.cuenta_contable
          WHERE
            id_cuenta_contable like '$filtro' AND
            (
              denominacion ILIKE '%$text%' OR   
              id_cuenta_contable ILIKE '$text%' OR
              _formatear_cuenta_contable(id_cuenta_contable) ILIKE '$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,$id_cuenta_contable,$id_cuenta_contable_seleccion,$denominacion){
    $db=SIGA::DBController();  
    $data=array("id_cuenta_contable"=>"'$id_cuenta_contable'", "denominacion"=>"'$denominacion'");
    if($id_cuenta_contable_seleccion!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_base.cuenta_contable",
                          $data,
                          "id_cuenta_contable='$id_cuenta_contable_seleccion'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_base.cuenta_contable",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.cuenta_contable", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Datos guardados con exito.");
  }
  
  public static function onDelete($access,$id_cuenta_contable){
    $db=SIGA::DBController();  
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    $result=$db->Delete("modulo_base.cuenta_contable","id_cuenta_contable='$id_cuenta_contable'");
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.cuenta_contable", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Registro eliminado con éxito.");
  }
  
  public static function onExist($id_cuenta_contable){
    $db=SIGA::DBController();  
    $sql="SELECT count(*) FROM modulo_base.cuenta_contable WHERE id_cuenta_contable LIKE '$id_cuenta_contable'";
    $return=$db->Execute($sql);
    return $return;
  }  
}  
?>