<?php
class cuenta_presupuestaria{  
  public static function onGet($id_cuenta_presupuestaria){
    $db=SIGA::DBController(); 
    $sql="SELECT
            CP.id_cuenta_presupuestaria,
            CP.denominacion,
            CP.padre,
            _formatear_cuenta_presupuestaria(CP.id_cuenta_presupuestaria) as cuenta_presupuestaria
          FROM modulo_base.cuenta_presupuestaria as CP
          WHERE CP.id_cuenta_presupuestaria='$id_cuenta_presupuestaria'";
    $return=$db->Execute($sql);
    return $return;
  }
  
  public static function onList($text,$start,$limit,$sort='',$filtro){
    $db=SIGA::DBController();
    
    $filtro=explode("|",$filtro);
    $add="";
    for($i=0;$i<count($filtro);$i++)
      $add.="id_cuenta_presupuestaria like '".$filtro[$i]."'".(($i<count($filtro)-1)?" OR ":"");
    $add="($add) AND";
    $sql="SELECT
            id_cuenta_presupuestaria,
            denominacion,
            padre,
            _formatear_cuenta_presupuestaria(id_cuenta_presupuestaria) as cuenta_presupuestaria
          FROM
            modulo_base.cuenta_presupuestaria
          WHERE            
            $add
            (
              denominacion ILIKE '%$text%' OR              
              id_cuenta_presupuestaria ILIKE '%$text%' OR              
              _formatear_cuenta_presupuestaria(id_cuenta_presupuestaria) ILIKE '$text%'
            )";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onList_AP($text,$start,$limit,$sort=''){
    $db=SIGA::DBController(); 
    $sql="SELECT DISTINCT
            DP.id_cuenta_presupuestaria,
            _formatear_cuenta_presupuestaria(DP.id_cuenta_presupuestaria) as cuenta_presupuestaria,
            CP.denominacion
          FROM
            modulo_base.detalle_presupuestario as DP,
            modulo_base.comprobante as C,
            modulo_base.cuenta_presupuestaria as CP
          WHERE
            C.id=DP.id_comprobante AND
            EXTRACT(YEAR FROM C.fecha)=".SIGA::data()." AND
            DP.operacion='AP' AND
            DP.id_cuenta_presupuestaria=CP.id_cuenta_presupuestaria";
    $return["result"]=$db->Execute($sql." ".sql_sort($sort)." LIMIT $limit OFFSET $start");
    $return["total"]=$db->Execute(sql_query_total($sql));
    $return["total"]=$return["total"][0][0];
    return $return;
  }
  
  public static function onSave($access,$id_cuenta_presupuestaria,$id_cuenta_presupuestaria_seleccion,$denominacion,$padre){
    $db=SIGA::DBController(); 
    
    $data=array("id_cuenta_presupuestaria"=>"'$id_cuenta_presupuestaria'", "denominacion"=>"'$denominacion'", "padre"=>"'$padre'");
    
    if($id_cuenta_presupuestaria_seleccion!=""){//si es modificar un registro
      if(!($access=="rw"))//solo el acceso 'rw' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para modificar datos.");
      //Modificar registro
      $result=$db->Update("modulo_base.cuenta_presupuestaria",
                          $data,
                          "id_cuenta_presupuestaria='$id_cuenta_presupuestaria_seleccion'");
    }
    else{//si es nuevo
      if(!($access=="rw" or $access=="a"))//solo el acceso 'rw' y 'a' es permitido
        return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para guardar datos.");
      //Insertar registro
      $result=$db->Insert("modulo_base.cuenta_presupuestaria",$data);
    }
    //Si hay error al modificar o insertar
    if(!$result)                   
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.cuenta_presupuestaria", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Datos guardados con exito.");
  }
  
  public static function onDelete($access,$id_cuenta_presupuestaria){
    $db=SIGA::DBController();
    if(!($access=="rw"))//solo el acceso 'rw' es permitido
      return array("success"=>false, "message"=>"Error. El usuario no tiene permiso para eliminar datos.");
    $result=$db->Delete("modulo_base.cuenta_presupuestaria","id_cuenta_presupuestaria='$id_cuenta_presupuestaria'");
    if(!$result)
      return array("success"=>false, "message"=>"Error al guardar en la tabla: modulo_base.cuenta_presupuestaria", "messageDB"=>$db->GetMsgErrorClear());
    return array("success"=>true, "message"=>"Registro eliminado con éxito.");
  }
  
  public static function onExist($id_cuenta_presupuestaria){
    $db=SIGA::DBController();    
    $sql="SELECT count(*) FROM modulo_base.cuenta_presupuestaria WHERE id_cuenta_presupuestaria LIKE '$id_cuenta_presupuestaria'";
    $return=$db->Execute($sql);
    return $return;
  }  
}  
?>